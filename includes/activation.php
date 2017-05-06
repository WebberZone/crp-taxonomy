<?php
/**
 * Activation functions
 *
 * @package CRP_Taxonomy
 */

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
		$blog_ids = $wpdb->get_col( "
			SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0' AND deleted = '0'
		" );
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

	// Loop through crp_read_options to ensure that our options are added across the network.
	crp_read_options();

}


/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 1.0.0
 *
 * @param    int $blog_id    ID of the new blog.
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


