<?php
/**
 * Contextual Related Posts Taxonomy Extender
 *
 * CRP Taxomy Extender adds extra options to CRP settings that allow you to
 * restrict posts to the category or tag of the current post.
 *
 * @package		CRP_Taxonomy
 * @author		Ajay D'Souza <me@ajaydsouza.com>
 * @license		GPL-2.0+
 * @link		http://ajaydsouza.com
 * @copyright 	2014-2015 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: Contextual Related Posts Taxonomy Extender
 * Plugin URI: http://ajaydsouza.com/wordpress/plugins/crp-taxonomy/
 * Description: Adds new settings to Contextual Related Posts that allows you to restrict posts to the category or tag of the current post
 * Version: 1.1.0
 * Author: Ajay D'Souza
 * Author URI: http://ajaydsouza.com
 * Text Domain: crp-taxonomy
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/ajaydsouza/crp-taxonomy/
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Initialises text domain for l10n.
 *
 * @since 1.0.0
 */
function crpt_lang_init() {
	load_plugin_textdomain( 'crp-taxonomy' , false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'crpt_lang_init' );


/**
 * Filter JOIN clause of CRP query to add taxonomy tables.
 *
 * @since 1.0.0
 *
 * @param	mixed	$join
 * @return	string	Filtered CRP JOIN clause
 */
function crpt_crp_posts_join( $join ) {
	global $wpdb, $crp_settings;

	if ( $crp_settings['crpt_tag'] || $crp_settings['crpt_category'] ) {
		return $join . "
			INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
			INNER JOIN $wpdb->term_taxonomy ON ($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
		";
	} else {
		return $join;
	}
}
add_filter( 'crp_posts_join', 'crpt_crp_posts_join' );


/**
 * Filter WHERE clause of CRP query to limit posts only to category of current post.
 *
 * @since 1.0.0
 *
 * @param	mixed	$where
 * @return	string	Filtered CRP WHERE clause
 */
function crpt_crp_posts_where( $where ) {
	global $wpdb, $post, $crp_settings;
	$term_ids = $category_ids = $tag_ids = $taxonomies = array();

	if ( ! $crp_settings['crpt_tag'] && ! $crp_settings['crpt_category'] ) {
		return $where;
	}

	if ( $crp_settings['crpt_category'] ) {
		$taxonomies[] = 'category';
	}

	if ( $crp_settings['crpt_tag'] ) {
		$taxonomies[] = 'post_tag';
	}

	if ( $crp_settings['crpt_taxes'] ) {
		$taxonomies = array_merge( $taxonomies, explode( ",", $crp_settings['crpt_taxes'] ) );
	}

	$terms = wp_get_object_terms( $post->ID, $taxonomies );
	$term_ids = wp_list_pluck( $terms, 'term_id' );

	if ( empty( $term_ids ) ) {
		return $where;
	} else {
		return $where . "
			AND $wpdb->term_taxonomy.term_id IN (" . implode(',', array_unique( $term_ids ) ) . ")
		";
	}
}
add_filter( 'crp_posts_where', 'crpt_crp_posts_where' );


/**
 * Add options to CRP Settings array.
 *
 * @since 1.1.0
 *
 * @param	array	$crp_settings	CRP Settings
 * @return	array	Filtered array of CRP Settings
 */
function crpt_crp_posts_match( $match, $stuff, $postid ) {
	global $crp_settings;

	$post_type = get_post_type( $postid );

	if ( $crp_settings['crpt_disable_contextual'] ) {

		/* If post or page and we're not disabling custom post types */
		if ( ( 'post' == $post_type || 'page' == $post_type ) && ( $crp_settings['crpt_disable_contextual_cpt'] ) ) {
			return $match;
		}

		return ' ';

	}

	return $match;

}
add_filter( 'crp_posts_match', 'crpt_crp_posts_match', 10, 3 );


/**
 * Add options to CRP Settings array.
 *
 * @since 1.0.0
 *
 * @param	array	$crp_settings	CRP Settings
 * @return	array	Filtered array of CRP Settings
 */
function crpt_crp_default_options( $crp_settings ) {

	$more_options = array(
		'crpt_tag' => false,		// Restrict to current post's tags
		'crpt_category' => false,	// Restrict to current post's categories
		'crpt_taxes' => '',			// Restrict to custom taxonomies
		'crpt_disable_contextual' => false,		// Disable contextual matching on all posts
		'crpt_disable_contextual_cpt' => true,	// Disable contextual matching on custom post types only
	);
	return	array_merge( $more_options, $crp_settings );
}
add_filter( 'crp_default_options', 'crpt_crp_default_options' );


/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 1.0.0
 *
 * @param    boolean    $network_wide    True if WPMU superadmin uses
 *                                       "Network Activate" action, false if
 *                                       WPMU is disabled or plugin is
 *                                       activated on an individual blog.
 */
function crpt_activate( $network_wide ) {
    global $wpdb;

    if ( is_multisite() && $network_wide ) {

        // Get all blogs in the network and activate plugin on each one
        $blog_ids = $wpdb->get_col( "
        	SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0' AND deleted = '0'
		" );
        foreach ( $blog_ids as $blog_id ) {
        	switch_to_blog( $blog_id );
			crpt_single_activate();
        }

        // Switch back to the current blog
        restore_current_blog();

    } else {
        crp_single_activate();
    }
}
register_activation_hook( __FILE__, 'crpt_activate' );


/**
 * Fired for each blog when the plugin is activated.
 *
 * @since 1.0.0
 */
function crpt_single_activate() {

	// Loop through crp_read_options to ensure that our options are added across the network
	$crp_settings = crp_read_options();

}


/**
 * Fired when a new site is activated with a WPMU environment.
 *
 * @since 1.0.0
 *
 * @param    int    $blog_id    ID of the new blog.
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


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );

} // End admin.inc

?>