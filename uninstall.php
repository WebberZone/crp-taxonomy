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

if ( is_multisite() ) {

	// Get all blogs in the network and activate plugin on each one.
	$blogids = $wpdb->get_col( //phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		"
		SELECT blog_id FROM $wpdb->blogs
		WHERE archived = '0' AND spam = '0' AND deleted = '0'
	"
	);

	foreach ( $blogids as $blogid ) {
		switch_to_blog( $blogid );
		crpt_delete_data();
		restore_current_blog();
	}
} else {
	crpt_delete_data();
}


/**
 * Delete settings on uninstall.
 */
function crpt_delete_data() {

	$new_settings = get_option( 'crp_settings' );
	$old_settings = get_option( 'ald_crp_settings' );

	$options = array(
		'crpt_tag',
		'crpt_category',
		'crpt_taxes',
		'crpt_same_taxes',
		'crpt_match_all',
		'crpt_no_of_taxes',
		'crpt_disable_contextual',
		'crpt_disable_contextual_cpt',
	);

	foreach ( $options as $option ) {
		unset( $new_settings[ $option ] );
		unset( $old_settings[ $option ] );
	}

	if ( ! empty( $old_settings ) ) {
		update_option( 'ald_crp_settings', $old_settings );
	}
	if ( ! empty( $new_settings ) ) {
		update_option( 'crp_settings', $new_settings );
	}

}
