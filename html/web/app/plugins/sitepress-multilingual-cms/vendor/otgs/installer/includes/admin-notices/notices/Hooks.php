<?php


namespace OTGS\Installer\AdminNotices\Notices;


use function OTGS\Installer\FP\partial;

class Hooks {
	public static function addHooks( $class, \WP_Installer $installer ) {
		add_filter( 'otgs_installer_admin_notices_config', $class . '::config', 10, 1 );
		add_filter( 'otgs_installer_admin_notices_texts', $class . '::texts', 10, 1 );
		add_filter(
			'otgs_installer_admin_notices',
			partial( $class . '::getCurrentNotices', $installer )
		);
	}
}