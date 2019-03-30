<?php
/**
 * Fired when the plugin is uninstalled
 *
 * @package     CRP_Taxonomy
 * @author      Ajay D'Souza
 * @license     GPL-2.0+
 * @link        https://webberzone.com
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}


global $wpdb, $crp_settings;

$option_name = 'ald_crp_settings';

if ( ! is_multisite() ) {

	unset( $crp_settings['crpt_tag'] );
	unset( $crp_settings['crpt_category'] );
	update_option( $option_name, $crp_settings );

} else {

	// Get all blogs in the network and activate plugin on each one.
	$blogids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		"
        SELECT blogid FROM $wpdb->blogs
        WHERE archived = '0' AND spam = '0' AND deleted = '0'
	"
	);
	foreach ( $blogids as $blogid ) {
		switch_to_blog( $blogid );

		unset( $crp_settings['crpt_tag'] );
		unset( $crp_settings['crpt_category'] );
		update_option( $option_name, $crp_settings );

	}

	// Switch back to the current blog.
	restore_current_blog();

}

