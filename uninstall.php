<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package		CRP_Taxonomy
 * @author		Ajay D'Souza <me@ajaydsouza.com>
 * @license		GPL-2.0+
 * @link		https://webberzone.com
 * @copyright	2014 Ajay D'Souza
 */

if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}


global $wpdb, $crp_settings;

$option_name = 'ald_crp_settings';

if ( !is_multisite() ) {

	unset( $crp_settings['crpt_tag'] );
	unset( $crp_settings['crpt_category'] );
	update_option( $option_name, $crp_settings );

} else {

    // Get all blogs in the network and activate plugin on each one
    $blog_ids = $wpdb->get_col( "
    	SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0' AND deleted = '0'
	" );
    foreach ( $blog_ids as $blog_id ) {
    	switch_to_blog( $blog_id );

		unset( $crp_settings['crpt_tag'] );
		unset( $crp_settings['crpt_category'] );
		update_option( $option_name, $crp_settings );

	}

    // Switch back to the current blog
    restore_current_blog();

}
?>