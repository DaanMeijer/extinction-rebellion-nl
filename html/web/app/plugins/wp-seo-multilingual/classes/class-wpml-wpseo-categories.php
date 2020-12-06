<?php

class WPML_WPSEO_Categories implements IWPML_Action {

	/**
	 * Add hooks.
	 */
	public function add_hooks() {
		if ( $this->is_stripping_category_base() ) {
			add_filter( 'category_rewrite_rules', array( $this, 'append_categories_hook' ), 1, 1 );
			add_filter( 'category_rewrite_rules', array( $this, 'turn_off_get_terms_filter' ), PHP_INT_MAX, 1 );
		}
	}

	/**
	 * Are we stripping the category base?
	 *
	 * @return bool
	 */
	private function is_stripping_category_base() {
		$option = (array) get_option( 'wpseo_titles' );

		return array_key_exists( 'stripcategorybase', $option ) && $option['stripcategorybase'];
	}

	/**
	 * Turn on filter.
	 *
	 * @param array $rules
	 * @return array
	 */
	public function append_categories_hook( $rules ) {
		add_filter( 'get_terms', array( $this, 'append_categories_translations' ), 10, 2 );

		return $rules;
	}

	/**
	 * Turn off filter.
	 *
	 * @param array $rules
	 * @return array
	 */
	public function turn_off_get_terms_filter( $rules ) {
		remove_filter( 'get_terms', array( $this, 'append_categories_translations' ) );

		return $rules;
	}

	/**
	 * We need categories in all languages for 'stripcategorybase' to work.
	 *
	 * @param array $categories
	 * @param array $taxonomy
	 * @return array
	 */
	public function append_categories_translations( $categories, $taxonomy ) {
		if ( ! in_array( 'category', $taxonomy, true ) || ! $this->is_array_of_wp_term( $categories ) ) {
			return $categories;
		}

		global $wpdb;

		$sql = "
			SELECT t.term_id FROM {$wpdb->terms} t
			INNER JOIN {$wpdb->term_taxonomy} tt ON tt.term_id = t.term_id
			WHERE tt.taxonomy = 'category'
		";

		return array_filter( array_map( array( $this, 'map_to_term' ), $wpdb->get_col( $sql ) ) ); // phpcs:ignore WordPress.WP.PreparedSQL.NotPrepared
	}

	/**
	 * @param array $terms
	 *
	 * @return bool
	 */
	private function is_array_of_wp_term( array $terms ) {
		return current( $terms ) instanceof WP_Term;
	}

	/**
	 * @param int $term_id
	 *
	 * @return false|WP_Term
	 */
	protected function map_to_term( $term_id ) {
		return get_term_by( 'term_id', $term_id, 'category' );
	}
}
