<?php
/**
 * Contextual Related Posts Taxonomy Tools
 *
 * CRP Taxomy Tools adds extra options to CRP settings that allow you to
 * restrict posts to the category or tag of the current post.
 *
 * @package		CRP_Taxonomy
 * @author		Ajay D'Souza <me@ajaydsouza.com>
 * @license		GPL-2.0+
 * @link		https://webberzone.com
 * @copyright 	2014-2015 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: Contextual Related Posts Taxonomy Tools
 * Plugin URI: https://webberzone.com/downloads/crp-taxonomy/
 * Description: Adds new settings to Contextual Related Posts that allows you to restrict posts to the category or tag of the current post
 * Version: 1.2.0
 * Author: Ajay D'Souza
 * Author URI: https://webberzone.com
 * Text Domain: crp-taxonomy
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 * GitHub Plugin URI: https://github.com/WebberZone/crp-taxonomy/
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

	if ( $crp_settings['crpt_tag'] || $crp_settings['crpt_category'] || $crp_settings['crpt_taxes'] ) {
		$sql = $join;
		$sql .= " INNER JOIN $wpdb->term_relationships AS crpt_tr ON ($wpdb->posts.ID = crpt_tr.object_id) ";
		$sql .= " INNER JOIN $wpdb->term_taxonomy AS crpt_tt ON (crpt_tr.term_taxonomy_id = crpt_tt.term_taxonomy_id) ";

		if ( $crp_settings['crpt_match_all'] ) {
			$sql .= " INNER JOIN $wpdb->terms AS crpt_t ON (crpt_tt.term_id = crpt_t.term_id) ";
		}

		return $sql;
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

	// Return if we have no tag / category or taxonomy to be matched
	if ( ! $crp_settings['crpt_tag'] && ! $crp_settings['crpt_category'] && ! $crp_settings['crpt_taxes'] ) {
		return $where;
	}

	// Temp variable used in crpt_crp_posts_having()
	$crp_settings['crpt_taxonomy_count'] = 0;

	if ( $crp_settings['crpt_category'] ) {
		$taxonomies[] = 'category';
		++$crp_settings['crpt_taxonomy_count'];
	}

	if ( $crp_settings['crpt_tag'] ) {
		$taxonomies[] = 'post_tag';
		++$crp_settings['crpt_taxonomy_count'];
	}

	if ( $crp_settings['crpt_taxes'] ) {
		$crpt_taxes = explode( ',', $crp_settings['crpt_taxes'] );
		$crp_settings['crpt_taxonomy_count'] += count( $crpt_taxes );
		$taxonomies = array_merge( $taxonomies, $crpt_taxes );
	}

	// Get the terms for the current post
	$terms = wp_get_object_terms( $post->ID, $taxonomies );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		$crp_settings['crpt_taxonomy_count'] = 0;
		return $where;
	} else {
		$sql = '';

		if ( $crp_settings['crpt_match_all'] ) {
			// Limit to posts matching all current taxonomy terms
			$term_strings            = array();
			$selected_taxes = $taxonomies;

			if ( count( $selected_taxes ) ) {
				// Find the matching selected taxonomies
				// Then add the term_id for that tax to the array
				foreach ( $terms as $term ) {
					if ( in_array( $term->taxonomy, $selected_taxes ) ) {
						$term_strings[] = $wpdb->prepare( '%s', $term->taxonomy . '/' . $term->term_id );
					}
				}
			}

			// This SQL will match all posts that have the specified term_id in a particular taxonomy by converting
			// the data into a string and matching against that.  Now this does return nearly the same results
			// as if crpt_match_all was false, however a HAVING statement added later reduces the dataset down
			// to only posts that match at least 1 term in each the taxonomy.
			// Credit for solution: http://wordpress.stackexchange.com/questions/8503/optimize-multiple-taxonomy-term-mysql-query
			$term_count = count( $term_strings );

			if ( $term_count ) {
				$sql .= " AND CONCAT(crpt_tt.taxonomy, '/', crpt_t.term_id) IN (";
				$sql .= implode( ',', $term_strings );
				$sql .= ')';
			} else {
				$crp_settings['crpt_taxonomy_count'] = 0;
			}
		} else {
			// Limit to posts matching any current taxonomy term
			$term_ids = array_unique( wp_list_pluck( $terms, 'term_id' ) );

			if ( count( $term_ids ) ) {
				$tax_ids = implode( ',', $term_ids );
				$sql .= " AND crpt_tt.term_id IN ($tax_ids)";
			}
		}

		return $where . ' ' . $sql;
	}
}
add_filter( 'crp_posts_where', 'crpt_crp_posts_where' );


/**
 * Filter GROUP BY clause of CRP query.
 *
 * @since	1.1.1
 *
 * @param  mixed  $groupby
 * @return string Filtered CRP GROUP BY clause
 */
function crpt_crp_posts_groupby( $groupby ) {
	global $wpdb, $crp_settings;

	if ( $crp_settings['crpt_match_all'] && ( $crp_settings['crpt_tag'] || $crp_settings['crpt_category'] || $crp_settings['crpt_taxes'] ) ) {
		$groupby .= " $wpdb->posts.ID";
	}

	return $groupby;
}
add_filter( 'crp_posts_groupby', 'crpt_crp_posts_groupby' );


/**
 * Filter HAVING clause of CRP query.
 *
 * @since	1.2.0
 *
 * @param  mixed  $having
 * @return string Filtered CRP HAVING clause
 */
function crpt_crp_posts_having( $having ) {
	global $wpdb, $crp_settings;

	if ( $crp_settings['crpt_match_all'] && isset( $crp_settings['crpt_taxonomy_count'] ) && $crp_settings['crpt_taxonomy_count'] ) {
		$having .= $wpdb->prepare( " COUNT(DISTINCT crpt_tt.taxonomy) = %d", $crp_settings['crpt_taxonomy_count'] );
	}

	return $having;
}
add_filter( 'crp_posts_having', 'crpt_crp_posts_having', 10, 1);


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
		'crpt_tag'                    => false,	// Restrict to current post's tags
		'crpt_category'               => false,	// Restrict to current post's categories
		'crpt_taxes'                  => '',		// Restrict to custom taxonomies
		'crpt_match_all'              => false,	// Require all or only one of the taxonomy terms to match
		'crpt_disable_contextual'     => false,	// Disable contextual matching on all posts
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
		crpt_single_activate();
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

