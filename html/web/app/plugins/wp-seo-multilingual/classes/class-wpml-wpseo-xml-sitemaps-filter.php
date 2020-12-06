<?php

/**
 * WP SEO by Yoast sitemap filter class
 *
 * @version 1.0.2
 */
class WPML_WPSEO_XML_Sitemaps_Filter implements IWPML_Action {

	/**
	 * @var SitePress
	 */
	protected $sitepress;

	/**
	 * @var WPML_URL_Converter
	 */
	private $wpml_url_converter;

	/**
	 * @var WPML_Debug_BackTrace
	 */
	private $back_trace;

	/**
	 * @var WPSEO_Sitemap_Image_Parser
	 */
	private $image_parser;

	const FILTER_PREFIX = 'wpseo_sitemap_';
	const FILTER_SUFFIX = '_content';

	/**
	 * WPSEO_XML_Sitemaps_Filter constructor.
	 *
	 * @param SitePress                  $sitepress
	 * @param stdClass                   $wpml_url_converter
	 * @param WPSEO_Sitemap_Image_Parser $image_parser
	 * @param WPML_Debug_BackTrace       $back_trace
	 */
	public function __construct( $sitepress, $wpml_url_converter, WPSEO_Sitemap_Image_Parser $image_parser, WPML_Debug_BackTrace $back_trace = null ) {
		$this->sitepress          = $sitepress;
		$this->wpml_url_converter = $wpml_url_converter;
		$this->image_parser       = $image_parser;
		$this->back_trace         = $back_trace;
	}

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		global $wpml_query_filter;

		if ( $this->is_per_domain() ) {
			add_filter( 'wpml_get_home_url', [ $this, 'get_home_url_filter' ], 10, 4 );
			add_filter( 'wpseo_posts_join', [ $wpml_query_filter, 'filter_single_type_join' ], 10, 2 );
			add_filter( 'wpseo_posts_where', [ $wpml_query_filter, 'filter_single_type_where' ], 10, 2 );
			add_filter( 'wpseo_typecount_join', [ $wpml_query_filter, 'filter_single_type_join' ], 10, 2 );
			add_filter( 'wpseo_typecount_where', [ $wpml_query_filter, 'filter_single_type_where' ], 10, 2 );
		} else {
			// Add translated archives.
			add_filter( self::FILTER_PREFIX . 'post' . self::FILTER_SUFFIX, [ $this, 'add_languages_to_sitemap' ] );
			add_filter( self::FILTER_PREFIX . 'page' . self::FILTER_SUFFIX, [ $this, 'add_languages_to_sitemap' ] );
			add_filter( 'wpseo_sitemap_post_type_archive_link', [ $this, 'init_hooks_when_custom_post_types_are_available' ], PHP_INT_MAX, 2 );

			// Remove posts under hidden language.
			add_filter( 'wpseo_xml_sitemap_post_url', [ $this, 'exclude_hidden_language_posts' ], 10, 2 );

			// Remove sitemaps from secondary language folders.
			add_action( 'parse_query', [ $this, 'remove_sitemap_from_non_default_languages' ] );
		}

		if ( $this->is_per_directory() ) {
			add_filter( 'wpml_get_home_url', [ $this, 'maybe_return_original_url_in_get_home_url_filter' ], 10, 2 );
		}

		add_filter( 'wpseo_enable_xml_sitemap_transient_caching', [ $this, 'transient_cache_filter' ], 10, 0 );
		add_filter( 'wpseo_build_sitemap_post_type', [ $this, 'wpseo_build_sitemap_post_type_filter' ] );
		add_action( 'wpseo_xmlsitemaps_config', [ $this, 'list_domains' ] );
		add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', [ $this, 'exclude_translations_of_static_pages' ], 10, 3 );
	}

	/**
	 * Delay adding this hook until the custom post type is available.
	 *
	 * @param string $link      The link to the archive.
	 * @param string $post_type The post type slug.
	 * @return string
	 */
	public function init_hooks_when_custom_post_types_are_available( $link, $post_type ) {
		if ( ! empty( $link ) && is_string( $post_type ) ) {
			add_filter( self::FILTER_PREFIX . $post_type . self::FILTER_SUFFIX, [ $this, 'add_languages_to_sitemap' ] );
		}

		return $link;
	}

	/**
	 * Add home page urls for languages to sitemap.
	 * Do this only if configuration language per domain option is not used.
	 */
	public function add_languages_to_sitemap() {

		$output = '';
		$type   = $this->get_sitemap_type();

		if ( ! $this->sitepress->is_translated_post_type( $type ) ) {
			return $output;
		}

		// Check if page_for_posts is set and bail out early.
		if ( 'post' === $type && ! get_option( 'page_for_posts' ) ) {
			return $output;
		}

		// Loop through language adding urls.
		$default_lang = $this->sitepress->get_default_language();
		$active_langs = $this->sitepress->get_active_languages();
		unset( $active_langs[ $default_lang ] );
		$lastmod = null;
		$images  = [];

		foreach ( $active_langs as $lang_code => $lang_data ) {
			switch ( $type ) {
				case 'page':
					$post_id = $this->get_post_id_for_option( 'page_on_front', $lang_code );
					$url     = $this->get_translated_home_url( $lang_code );
					$lastmod = $this->get_page_lastmod( $post_id );
					$images  = $this->get_images( $post_id );
					break;
				case 'post':
					$post_id = $this->get_post_id_for_option( 'page_for_posts', $lang_code );
					$url     = get_permalink( $post_id );
					$lastmod = $this->get_page_lastmod( $post_id );
					break;
				default:
					$this->sitepress->switch_lang( $lang_code );
					$url = get_post_type_archive_link( $type );
					$this->sitepress->switch_lang();
			}

			$output .= $this->sitemap_url_filter( $url, $lastmod, $images );
		}

		return $output;
	}

	/**
	 * Update home_url for language per-domain configuration to return correct URL in sitemap.
	 *
	 * @param string $home_url
	 * @param string $url
	 * @param string $path
	 * @param string $orig_scheme
	 *
	 * @return bool|mixed|string
	 */
	public function get_home_url_filter( $home_url, $url, $path, $orig_scheme ) {
		if ( 'relative' !== $orig_scheme ) {
			$home_url = $this->wpml_url_converter->convert_url( $home_url, $this->sitepress->get_current_language() );
		}
		return $home_url;
	}

	/**
	 * List sitemaps in other domains.
	 * Only used when language URL format is 'one domain per language'.
	 */
	public function list_domains() {
		$ls_languages = $this->sitepress->get_ls_languages();
		if ( $ls_languages && $this->is_per_domain() ) {

			echo '<h3>' . esc_html__( 'WPML', 'sitepress' ) . '</h3>';
			echo esc_html__( 'Sitemaps for each language can be accessed below. You need to submit all these sitemaps to Google.', 'sitepress' );
			echo '<table class="wpml-sitemap-translations" style="margin-left: 1em; margin-top: 1em;">';

			foreach ( $ls_languages as $lang ) {
				$url = $lang['url'] . 'sitemap_index.xml';
				echo '<tr>';
				echo '<td>';
				echo '<a ';
				echo 'href="' . esc_url( $url ) . '" ';
				echo 'target="_blank" ';
				echo 'class="button-secondary" ';
				printf(
					"style=\"background-image:url('%s'); background-repeat: no-repeat; background-position: 2px center; background-size: 16px; padding-left: 20px; width: 100%%;\"",
					esc_url( $lang['country_flag_url'] )
				);
				echo '>';
				echo esc_html( $lang['translated_name'] );
				echo '</a>';
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';
		}
	}

	/**
	 * @return bool
	 */
	public function is_per_domain() {
		return WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN === (int) $this->sitepress->get_setting( 'language_negotiation_type' );
	}

	/**
	 * @return bool
	 */
	private function is_per_directory() {
		return WPML_LANGUAGE_NEGOTIATION_TYPE_DIRECTORY === (int) $this->sitepress->get_setting( 'language_negotiation_type' );
	}

	/**
	 * Disable transient cache.
	 */
	public function transient_cache_filter() {
		return false;
	}

	/**
	 * Deactivate auto-adjust-ids while building the sitemap.
	 *
	 * @param string $type
	 * @return string
	 */
	public function wpseo_build_sitemap_post_type_filter( $type ) {
		global $sitepress_settings;
		// Before building the sitemap and as we are on front-end make sure links aren't translated.
		// The setting should not be updated in DB.
		$sitepress_settings['auto_adjust_ids'] = 0;

		if ( ! $this->is_per_domain() ) {
			remove_filter( 'terms_clauses', [ $this->sitepress, 'terms_clauses' ] );
		}

		remove_filter( 'category_link', [ $this->sitepress, 'category_link_adjust_id' ], 1 );

		return $type;
	}

	/**
	 * Exclude posts under hidden language.
	 *
	 * @param  string   $url  Post URL.
	 * @param  stdClass $post Object with some post information.
	 *
	 * @return string
	 */
	public function exclude_hidden_language_posts( $url, $post ) {
		// Check that at least ID is set in post object.
		if ( ! isset( $post->ID ) ) {
			return $url;
		}

		// Get list of hidden languages.
		$hidden_languages = $this->sitepress->get_setting( 'hidden_languages', [] );

		// If there are no hidden languages return original URL.
		if ( empty( $hidden_languages ) ) {
			return $url;
		}

		// Get language information for post.
		$language_info = $this->sitepress->post_translations()->get_element_lang_code( $post->ID );

		// If language code is one of the hidden languages return null to skip the post.
		if ( in_array( $language_info, $hidden_languages, true ) ) {
			return null;
		}

		return $url;
	}

	/**
	 * Convert URL to sitemap entry format.
	 *
	 * @param string $url     URl to prepare for sitemap.
	 * @param string $lastmod The last modification date in ISO8601 format.
	 * @param array  $images  Images included in this entry.
	 * @return string
	 */
	public function sitemap_url_filter( $url, $lastmod = null, $images = [] ) {
		if ( ! $url ) {
			return '';
		}

		$url = htmlspecialchars( $url, ENT_COMPAT, get_bloginfo( 'charset' ) );

		$output  = "\t<url>\n";
		$output .= "\t\t<loc>" . $url . "</loc>\n";
		$output .= '';
		$output .= "\t\t<changefreq>daily</changefreq>\n";
		if ( ! empty( $lastmod ) ) {
			$output .= "\t\t<lastmod>" . $lastmod . "</lastmod>\n";
		}
		foreach ( $images as $image ) {
			$output .= "\t\t<image:image>\n";
			$output .= "\t\t\t<image:loc>" . $image['src'] . "</image:loc>\n";
			$output .= "\t\t</image:image>\n";
		}
		$output .= "\t\t<priority>1.0</priority>\n";
		$output .= "\t</url>\n";

		return $output;
	}

	/**
	 * @param array $excluded_post_ids
	 *
	 * @return array
	 */
	public function exclude_translations_of_static_pages( $excluded_post_ids ) {
		$static_pages = [ 'page_on_front', 'page_for_posts' ];
		foreach ( $static_pages as $static_page ) {
			$page_id = (int) get_option( $static_page );
			if ( $page_id ) {
				$translations = (array) $this->sitepress->post_translations()->get_element_translations( $page_id );
				unset( $translations[ $this->sitepress->get_default_language() ] );
				$excluded_post_ids = array_merge( $excluded_post_ids, array_values( $translations ) );
			}
		}

		return $excluded_post_ids;
	}

	/**
	 * @param string $home_url
	 * @param string $original_url
	 *
	 * @return string
	 */
	public function maybe_return_original_url_in_get_home_url_filter( $home_url, $original_url ) {
		$places = [
			[ 'WPSEO_Watcher', 'format_redirect_url' ],
			[ 'WPSEO_Post_Watcher', 'get_target_url' ],
			[ 'WPSEO_Post_Type_Sitemap_Provider', 'get_home_url' ],
			[ 'WPSEO_Post_Type_Sitemap_Provider', 'get_classifier' ],
			[ 'WPSEO_Sitemaps_Router', 'get_base_url' ],
			[ 'WPSEO_Sitemaps_Renderer', '__construct' ],
			[ 'WPSEO_Redirect_Accessible_Validation', 'parse_target' ],
			[ 'WPSEO_Redirect_Subdirectory_Validation', 'get_subdirectory' ],
		];

		foreach ( $places as $place ) {
			if ( $this->get_back_trace()->is_class_function_in_call_stack( $place[0], $place[1] ) ) {
				return $original_url;
			}
		}

		return $home_url;
	}

	/**
	 * @return WPML_Debug_BackTrace
	 */
	private function get_back_trace() {
		if ( null === $this->back_trace ) {
			$this->back_trace = new WPML_Debug_BackTrace( phpversion() );
		}

		return $this->back_trace;
	}

	/**
	 * Figure out which sitemap we are working with by looking at the current filter.
	 */
	private function get_sitemap_type() {
		return substr( substr( current_filter(), strlen( self::FILTER_PREFIX ) ), 0, -strlen( self::FILTER_SUFFIX ) );
	}

	/**
	 * Get the translated post_id for a certain optionb.
	 *
	 * @param string $option The option we need.
	 * @param string $lang   The language we need.
	 * @return int
	 */
	private function get_post_id_for_option( $option, $lang ) {
		$post_id = get_option( $option );
		$post_id = $this->sitepress->get_object_id( $post_id, 'page', true, $lang );

		return $post_id;
	}

	/**
	 * @param string $lang_code
	 *
	 * @return bool|mixed|string
	 */
	private function get_translated_home_url( $lang_code ) {
		return $this->wpml_url_converter->convert_url( home_url(), $lang_code );
	}

	/**
	 * Get the last modification of a page in a certain language.
	 * Returns null if it doesn't exist.
	 *
	 * @param int $page_id
	 * @return string
	 */
	private function get_page_lastmod( $page_id ) {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			return get_the_modified_time( 'c', $page_id );
		}

		return null;
	}

	/**
	 * Get a list of images attached to this page.
	 *
	 * @param int $page_id
	 * @return array
	 */
	private function get_images( $page_id ) {
		$images = [];

		if ( apply_filters( 'wpseo_xml_sitemap_include_images', true ) ) {
			$images = $this->image_parser->get_images( get_post( $page_id ) );
		}

		return $images;
	}

	/**
	 * Removes the sitemap query_var on non-default languages.
	 * This will only run when the language URL format is not per domain.
	 *
	 * @param WP_Query $wp_query Passed WP query object.
	 */
	public function remove_sitemap_from_non_default_languages( &$wp_query ) {
		if (
				$wp_query->get( 'sitemap' )
				&& $this->sitepress->get_current_language() !== $this->sitepress->get_default_language()
			) {
			unset( $wp_query->query_vars['sitemap'] );
			$wp_query->set_404();
			status_header( 404 );
		}
	}

}
