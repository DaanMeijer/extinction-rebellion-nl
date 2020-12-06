<?php

namespace WPML\WPSEO\Presentation;

use Yoast\WP\SEO\Presentations\Indexable_Presentation;

class Hooks implements \IWPML_Frontend_Action {

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		add_filter( 'wpseo_title', [ $this, 'translateTitle' ], 10, 2 );
		add_filter( 'wpseo_metadesc', [ $this, 'translateDescription' ], 10, 2 );

		add_filter( 'wpseo_opengraph_title', [ $this, 'translateTitle' ], 10, 2 );
		add_filter( 'wpseo_opengraph_desc', [ $this, 'translateDescription' ], 10, 2 );

		add_filter( 'wpseo_breadcrumb_indexables', [ $this, 'translateBreadcrumbs' ], 10, 2 );

		add_filter( 'wpseo_frontend_presentation', [ $this, 'translatePermalinks' ] );
	}

	/**
	 * Translates a title.
	 *
	 * @param string                 $title        The title in the default language.
	 * @param Indexable_Presentation $presentation The presentation class.
	 *
	 * @return string
	 */
	public function translateTitle( $title, $presentation ) {
		return $this->translate( 'title', $title, $presentation );
	}

	/**
	 * Translates a description.
	 *
	 * @param string                 $description  The description in the default language.
	 * @param Indexable_Presentation $presentation The presentation class.
	 *
	 * @return string
	 */
	public function translateDescription( $description, $presentation ) {
		$type = $this->isHomePageObjectType( $presentation ) ? 'desc' : 'metadesc';

		return $this->translate( $type, $description, $presentation );
	}

	/**
	 * Translates a breadcrumb title.
	 *
	 * @param string                 $title        The title in the default language.
	 * @param Indexable_Presentation $presentation The presentation class.
	 *
	 * @return string
	 */
	public function translateBreadcrumbTitle( $title, $presentation ) {
		return $this->translate( 'bctitle', $title, $presentation );
	}

	/**
	 * Get the translations from the options table, which will include the translated admin-texts.
	 *
	 * @param string                 $type         The object type of the Indesable, used as a prefix for the option name.
	 * @param string                 $text         The text in the default language.
	 * @param Indexable_Presentation $presentation The presentation class.
	 *
	 * @return string
	 */
	private function translate( $type, $text, $presentation ) {
		$optionName = $this->getOptionName( $presentation );
		$optionKey  = $this->getOptionKey( $type, $presentation );

		if ( $optionName && $optionKey ) {
			$option = get_option( $optionName );
			if ( isset( $option[ $optionKey ] ) ) {
				$text = wpseo_replace_vars( $option[ $optionKey ], $presentation );
			}
		}

		return $text;
	}

	/**
	 * Returns the option name for the object being translated.
	 *
	 * @param Indexable_Presentation $presentation The presentation class.
	 *
	 * @return string
	 */
	private function getOptionName( $presentation ) {
		return $this->isHomePageObjectType( $presentation ) ? 'wpseo_social' : 'wpseo_titles';
	}

	/**
	 * Returns the option key for the object being translated.
	 *
	 * @param string                 $type         How to prefix the option name.
	 * @param Indexable_Presentation $presentation The presentation class.
	 *
	 * @return string
	 */
	private function getOptionKey( $type, $presentation ) {
		if (
			'post' === $presentation->model->object_type
			&& $presentation instanceof \Yoast\WP\SEO\Presentations\Indexable_Post_Type_Presentation
		) {
			return false;
		}

		return wpml_collect(
			[
				'post-type-archive' => $type . '-ptarchive-' . $presentation->model->object_sub_type,
				'system-page'       => $type . '-' . $presentation->model->object_sub_type . '-wpseo',
				'post'              => $type . '-ptarchive-' . $presentation->model->object_sub_type,
				'home-page'         => 'og_frontpage_' . $type,
			]
		)->get( $presentation->model->object_type, false );
	}

	/**
	 * Returns true if we are we handling the home page.
	 *
	 * @param Indexable_Presentation $presentation The presentation class.
	 *
	 * @return bool
	 */
	private function isHomePageObjectType( $presentation ) {
		return 'home-page' === $presentation->model->object_type;
	}

	/**
	 * Translate titles and links for home and archives.
	 *
	 * @param Indexable[]       $indexables An array of Indexable objects representing the breacrumbs.
	 * @param Meta_Tags_Context $context    The context class provided by WPSEO.
	 */
	public function translateBreadcrumbs( $indexables, $context ) {
		foreach ( $indexables as &$indexable ) {
			if ( 'post-type-archive' === $indexable->object_type ) {
				$indexable->breadcrumb_title = $this->translateBreadcrumbTitle( $indexable->breadcrumb_title, $context->presentation );
			}
			if ( 'term' === $indexable->object_type ) {
				$term                        = apply_filters( 'wpml_object_id', $indexable->object_id, $indexable->object_sub_type, true );
				$indexable->permalink        = get_term_link( $term );
				$indexable->breadcrumb_title = get_term( $term )->name;
			} else {
				$indexable->permalink = apply_filters( 'wpml_permalink', $indexable->permalink );
			}
		}

		return $indexables;
	}

	/**
	 * Translate permalinks.
	 *
	 * @param Indexable_Presention $presentation The indexable presentation.
	 */
	public function translatePermalinks( $presentation ) {
		$presenttion = clone $presentation;

		if ( 'post' === $presentation->model->object_type ) {
			$presentation->model->permalink = get_permalink( $presentation->model->object_id );
		} elseif ( 'term' === $presentation->model->object_type ) {
			$presentation->model->permalink = get_term_link( $presentation->model->object_id );
		}

		return $presentation;
	}
}
