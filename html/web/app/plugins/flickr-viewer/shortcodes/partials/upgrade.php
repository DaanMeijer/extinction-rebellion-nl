<div>Sorry, this is only available in Google Photos Pro</div>
<?php
// $plugin = new CWS_Flickr_Gallery_Pro($plugin_name, $version, $isPro);
$plugin = new CWS_Flickr_Gallery_Pro();
$plugin_admin = new CWS_Flickr_Gallery_Pro_Admin( $plugin->get_plugin_name(), $plugin->get_version(), $plugin->get_isPro() );
echo $plugin_admin->cws_fgp_upgrade_content(); 