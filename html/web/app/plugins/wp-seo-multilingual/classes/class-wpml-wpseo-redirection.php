<?php

class WPML_WPSEO_Redirection {

	const OPTION = 'wpseo-premium-redirects-base';

	/**
	 * @return bool
	 */
	public function is_redirection() {
		$redirections = $this->get_all_redirections();
		if ( is_array( $redirections ) ) {

			// Use same logic as WPSEO_Redirect_Util::strip_base_url_path_from_url.
			$url = trim( $_SERVER['REQUEST_URI'], '/' );

			add_filter( 'wpml_skip_convert_url_string', '__return_true' );
			$base_url_path = ltrim( wp_parse_url( home_url(), PHP_URL_PATH ), '/' );
			remove_filter( 'wpml_skip_convert_url_string', '__return_true' );

			// The unfiltered URL got cached so we need to flush the group.
			self::clear_converter_cache_for_home_url();

			if ( stripos( trailingslashit( $url ), trailingslashit( $base_url_path ) ) === 0 ) {
				$url = substr( $url, strlen( $base_url_path ) );
			}

			foreach ( $redirections as $redirection ) {
				if ( $redirection['origin'] === $url || '/' . $redirection['origin'] === $url ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Clear convert_url cache group.
	 */
	private static function clear_converter_cache_for_home_url() {
		$wpml_cache = new WPML_WP_Cache( 'convert_url' );
		$wpml_cache->flush_group_cache();
	}

	/**
	 * @return mixed
	 */
	private function get_all_redirections() {
		return get_option( self::OPTION );
	}
}
