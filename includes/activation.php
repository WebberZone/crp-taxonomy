<?php
/**
 * Activation functions
 *
 * @package CRP_Taxonomy
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 1.0.0
 *
 * @param    boolean $network_wide    True if WPMU superadmin uses
 *                                    "Network Activate" action, false if
 *                                    WPMU is disabled or plugin is
 *                                    activated on an individual blog.
 */
function crpt_activate( $network_wide ) {
	global $wpdb;

	if ( is_multisite() && $network_wide ) {

		// Get all blogs in the network and activate plugin on each one.
		$blog_ids = $wpdb->get_col( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
			"
			SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0' AND deleted = '0'
		"
		);
		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			crpt_single_activate();
		}

		// Switch back to the current blog.
		restore_current_blog();

	} else {
		crpt_single_activate();
	}
}
register_activation_hook( CRPT_PLUGIN_FILE, 'crpt_activate' );


/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 1.0.0
 */
function crpt_single_activate() {

	// Only run this if Contextual Related Posts v2.6.0 and higher is not installed.
	if ( ! function_exists( 'crp_get_settings' ) ) {
		// Loop through crp_get_settings to ensure that our options are added across the network.
		crp_get_settings();
	}

}


/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 1.0.0
 *
 * @param int $blog_id ID of the new blog.
 */
function crpt_activate_new_site( $blog_id ) {

	if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
		return;
	}

	switch_to_blog( $blog_id );
	crp_single_activate();
	restore_current_blog();

}
add_action( 'wpmu_new_blog', 'crpt_activate_new_site' );


/**
 * Upgrade pre v1.4.0 settings.
 *
 * @since 1.4.0
 * @return boolean True if updated, false if nothing to do.
 */
function crpt_upgrade_settings() {
	global $crp_settings;

	if ( isset( $crp_settings['crpt_same_taxes'] ) || ! function_exists( 'crp_get_settings' ) ) {
		return false;
	}

	$crp_settings = crp_get_settings();

	$taxonomies = array();

	if ( isset( $crp_settings['crpt_category'] ) && $crp_settings['crpt_category'] ) {
		$taxonomies[] = 'category';
		unset( $crp_settings['crpt_category'] );
	}

	if ( isset( $crp_settings['crpt_tag'] ) && $crp_settings['crpt_tag'] ) {
		$taxonomies[] = 'post_tag';
		unset( $crp_settings['crpt_tag'] );
	}

	if ( isset( $crp_settings['crpt_taxes'] ) && $crp_settings['crpt_taxes'] ) {
		$crpt_taxes = explode( ',', $crp_settings['crpt_taxes'] );
		$taxonomies = array_merge( $taxonomies, $crpt_taxes );
		unset( $crp_settings['crpt_taxes'] );
	}

	$crp_settings['crpt_same_taxes'] = implode( ',', $taxonomies );

	update_option( 'crp_settings', $crp_settings );
}
add_action( 'admin_init', 'crpt_upgrade_settings', 999 );
