<?php

namespace OTGS\Installer\AdminNotices\Notices;

class Texts {

	protected static $repo;
	protected static $product;
	protected static $apiHost;
	protected static $communicationDetailsLink;
	protected static $supportLink;

	public static function notRegistered() {
		// translators: %s Product name
		$headingHTML = self::getHeadingHTML( __( 'You are using an unregistered version of %s and are not receiving compatibility and security updates', 'installer' ) );
		// translators: %s Product name
		$bodyHTML = self::getBodyHTML( __( '%s plugin must be registered in order to receive stability and security updates. Without these updates, the plugin may become incompatible with new versions of WordPress, which include security patches.', 'installer' ) ) .
		            self::inButtonAreaHTML( self::getNotRegisteredButtons() ) .
		            self::getDismissHTML();

		return self::insideDiv( 'register', $headingHTML . $bodyHTML );
	}

	public static function expired() {
		// translators: %s Product name
		$headingHTML = self::getHeadingHTML( __( 'You are using an expired %s account.', 'installer' ) );
		// translators: %s Product name
		$bodyHTML = self::getBodyHTML( __( "Your site is using an expired %s account, which means you won't receive updates. This can lead to stability and security issues.", 'installer' ) ) .
		            self::inButtonAreaHTML( self::getExpiredButtons() ) .
		            self::getDismissHTML();

		return self::insideDiv( 'expire', $headingHTML . $bodyHTML );
	}

	public static function refunded() {
		// translators: %s Product name
		$headingHTML = self::getHeadingHTML( __( 'Remember to remove %s from this website', 'installer' ) );
		// translators: %s Product name
		$body = self::getBodyHTML( __( 'This site is using the %s plugin, which has not been paid for. After receiving a refund, you should remove this plugin from your sites. Using unregistered plugins means you are not receiving stability and security updates and will ultimately lead to problems running the site.', 'installer' ) ) .
		        self::inButtonAreaHTML( self::getRefundedButtons() );

		return self::insideDiv( 'refund', $headingHTML . $body );
	}

	public static function connectionIssues() {
		// translators: %1$s Product name %2$s host name (ex. wpml.org)
		$headingHTML = self::getConnectionIssueHeadingHTML( __( '%1$s plugin cannot connect to %2$s', 'installer' ) );

		// translators: %1$s Product name %2$s host name (ex. wpml.org)
		$body = self::getConnectionIssueBodyHTML( __( '%1$s needs to connect to its server to check for new releases and security updates. Something in the network or security settings is preventing this. Please allow outgoing communication to %2$s to remove this notice.', 'installer' ) ) .
		        self::inLinksAreaHTML(
			        __( 'Need help?', 'installer' ),
			        // translators: %1$s is `communication error details` %2$s is ex. wpml.org technical support
			        __( 'See the %1$s and let us know in %2$s.', 'installer' ),
			        self::getCommunicationDetailsLinkHTML( __( 'communication error details', 'installer' )),
			        // translators: %s is host name (ex. wpml.org)
			        self::getSupportLinkHTML(  __( '%s technical support', 'installer' ))
		        );

		return self::insideDiv( 'connection-issues', $headingHTML . $body );
	}

	/**
	 * @param string $type The type is used as a suffix of the `otgs-installer-notice-` CSS class.
	 * @param string $html An unescaped HTML string but with escaped data (e.g. attributes, URLs, or strings in the HTML produced from any input).
	 *
	 * @return string
	 */
	protected static function insideDiv( $type, $html ) {
		$classes = [
			'notice',
			'otgs-installer-notice',
			'otgs-installer-notice-' . esc_attr( static::$repo ),
			'otgs-installer-notice-' . esc_attr( $type ),
		];

		if ( $type !== 'refund' && $type !== 'connection-issues') {
			$classes[] = 'is-dismissible';
		}

		return '<div class="' . implode( ' ', $classes ) . '">' .
		       '<div class="otgs-installer-notice-content">' .
		       $html .
		       '</div>' .
		       '</div>';
	}

	/**
	 * @return string
	 */
	protected static function getNotRegisteredButtons() {
		$registerUrl = \WP_Installer::menu_url();
		$register    = __( 'Register', 'installer' );
		$stagingSite = __( 'This is a development / staging site', 'installer' );

		return self::getPrimaryButtonHTML( $registerUrl, $register ) .
		       self::getStagingButtonHTML( $stagingSite );
	}

	/**
	 * @return string
	 */
	protected static function getExpiredButtons() {
		$checkOrderStatusUrl = \WP_Installer::menu_url() . '&validate_repository=' . static::$repo;
		$accountButton       = __( 'Extend your subscription', 'installer' );
		$checkButton         = __( 'Check my order status', 'installer' );
		$statusText          = __( 'Got renewal already?', 'installer' );
		$productUrl          = \WP_Installer::instance()->get_product_data( static::$repo, 'url' );

		return self::getPrimaryButtonHTML( $productUrl . '/account', $accountButton ) .
		       self::getStatusHTML( $statusText ) .
		       self::getRefreshButtonHTML( $checkOrderStatusUrl, $checkButton );
	}

	/**
	 * @return string
	 */
	private static function getRefundedButtons() {
		$checkOrderStatusUrl = \WP_Installer::menu_url() . '&validate_repository=' . static::$repo;
		$checkButton         = __( 'Check my order status', 'installer' );
		$status              = __( 'Bought again?', 'installer' );

		return self::getStatusHTML( $status ) .
		       self::getPrimaryButtonHTML( $checkOrderStatusUrl, $checkButton );
	}

	/**
	 * @return string
	 */
	protected static function getDismissHTML() {
		return '<span class="installer-dismiss-nag notice-dismiss" ' . self::getDismissedAttributes( Account::NOT_REGISTERED ) . '>'
		       . '<span class="screen-reader-text">' . esc_html__( 'Dismiss', 'installer' ) . '</span></span>';
	}

	/**
	 * @param string $text The method takes care of escaping the string.
	 *
	 * @return string
	 */
	private static function getDismissedAttributes( $text ) {
		return 'data-repository="' . esc_attr( static::$repo ) . '" data-notice="' . esc_attr( $text ) . '"';
	}

	/**
	 * @param string $url  The method takes care of escaping the string.
	 * @param string $text The method takes care of escaping the string.
	 *
	 * @return string
	 */
	protected static function getPrimaryButtonHTML( $url, $text ) {
		return '<a class="otgs-installer-notice-status-item otgs-installer-notice-status-item-btn" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
	}

	/**
	 * @param string $url  The method takes care of escaping the string.
	 * @param string $text The method takes care of escaping the string.
	 *
	 * @return string
	 */
	protected static function getRefreshButtonHTML( $url, $text ) {
		return '<a class="otgs-installer-notice-status-item otgs-installer-notice-status-item-link otgs-installer-notice-status-item-link-refresh" href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
	}

	/**
	 * @param string $text The method takes care of escaping the string.
	 *
	 * @return string
	 */
	protected static function getStatusHTML( $text ) {
		return '<p class="otgs-installer-notice-status-item">' . esc_html( $text ) . '</p>';
	}

	/**
	 * @param string $html An unescaped HTML string but with escaped data (e.g. attributes, URLs, or strings in the HTML produced from any input).
	 *
	 * @return string
	 */
	private static function inButtonAreaHTML( $html ) {
		return '<div class="otgs-installer-notice-status">' . $html . '</div>';

	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	private static function inLinksAreaHTML( $title, $text, $communicationDetails, $supportLink ) {
		return '<div class="otgs-installer-notice-status">
					<p class="otgs-installer-notice-status-item">' . esc_html( $title ) . '</p>
					<p class="otgs-installer-notice-status-item">' . sprintf( esc_html( $text ), $communicationDetails, $supportLink ) . '</p>
				</div>';
	}

	/**
	 * @param string $text  The method takes care of escaping the string.
	 *                      If the string contains a placeholder, it will be replaced with the value of `static::$product`.
	 *
	 * @return string
	 */
	protected static function getHeadingHTML( $text ) {
		return '<h2>' . esc_html( sprintf( $text, static::$product ) ) . '</h2>';
	}

	/**
	 * @param string $text
	 *
	 * @return string
	 */
	protected static function getConnectionIssueHeadingHTML( $text ) {
		return '<h2>' . esc_html( sprintf( $text, static::$product, static::$apiHost ) ) . '</h2>';
	}

	/**
	 * @param string $text  The method takes care of escaping the string.
	 *                      If the string contains a placeholder, it will be replaced with the value of `static::$product`.
	 *
	 * @return string
	 */
	protected static function getBodyHTML( $text ) {
		return '<p>' . esc_html( sprintf( $text, static::$product ) ) . '</p>';
	}

	/**
	 * @param string $text  The method takes care of escaping the string.
	 *                      If the string contains a placeholder, it will be replaced with the value of `static::$product`.
	 *
	 * @return string
	 */
	protected static function getConnectionIssueBodyHTML( $text ) {
		return '<p>' . esc_html( sprintf( $text, static::$product, static::$apiHost ) ) . '</p>';
	}

	/**
	 * @param string $text The method takes care of escaping the string.
	 *
	 * @return string
	 */
	private static function getStagingButtonHTML( $text ) {
		return '<a class="otgs-installer-notice-status-item otgs-installer-notice-status-item-link installer-dismiss-nag" ' . self::getDismissedAttributes( Account::NOT_REGISTERED ) . '>' . esc_html( $text ) . '</a>';
	}

	private static function getCommunicationDetailsLinkHTML( $text ) {
		return '<a href="' . esc_url( static::$communicationDetailsLink ) . '">' . esc_html( $text ) . '</a>';
	}

	private static function getSupportLinkHTML( $text ) {
		return '<a href="' . esc_url( static::$supportLink ) . '">' . esc_html( sprintf( $text, static::$product ) ) . '</a>';
	}
}
