<?php

use WPML\WPSEO\Utils;

/**
 * Class WPML_WPSEO_Should_Create_Redirect
 *
 * Extra checks to decide when a redirect should be created.
 */
class WPML_WPSEO_Should_Create_Redirect implements IWPML_Action {

	/** @var array */
	private $filter_hooks;

	/** @var string */
	private $unfiltered_url;

	/**
	 * Class constructor.
	 */
	public function __construct() {
		$this->filter_hooks = wpml_collect( [ 'post_link', 'page_link', 'post_type_link' ] );
	}

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		Utils::add_filter( 'wpseo_premium_post_redirect_slug_change', [ $this, 'dont_convert_url' ], 10, 4 );
	}

	/**
	 * @param bool    $result
	 * @param integer $post_id
	 * @param WP_Post $post
	 * @param WP_Post $post_before
	 *
	 * @return bool
	 */
	public function dont_convert_url( $result, $post_id, $post, $post_before ) {
		// This applies to drafts only.
		$status = get_post_status( $post_before );
		if ( in_array( $status, [ 'draft', 'auto-draft' ], true ) ) {

			$this->filter_hooks->each(
				function( $filter_hook ) {
					add_filter( $filter_hook, [ $this, 'save_unfiltered_url' ], 0 );
					add_filter( $filter_hook, [ $this, 'restore_unfiltered_url' ], 20 );
				}
			);

		}

		return $result;
	}

	/**
	 * Keep the unfiltered URL to use later.
	 *
	 * @param string $url
	 * @return string
	 */
	public function save_unfiltered_url( $url ) {
		$this->unfiltered_url = $url;

		return $url;
	}

	/**
	 * Restore the unfiltered URL.
	 *
	 * @param string $url
	 * @return string
	 */
	public function restore_unfiltered_url( $url ) {
		$url                  = $this->unfiltered_url;
		$this->unfiltered_url = null;

		$this->filter_hooks->each(
			function( $filter_hook ) {
				remove_filter( $filter_hook, [ $this, 'save_unfiltered_url' ], 0 );
				remove_filter( $filter_hook, [ $this, 'restore_unfiltered_url' ], 20 );
			}
		);

		return $url;
	}

}
