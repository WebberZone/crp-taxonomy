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
 * Version: 1.3.0
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
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'CRPT_PLUGIN_DIR' ) ) {
	define( 'CRPT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'CRPT_PLUGIN_URL' ) ) {
	define( 'CRPT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 2.3.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'CRPT_PLUGIN_FILE' ) ) {
	define( 'CRPT_PLUGIN_FILE', __FILE__ );
}


/**
 * Add options to CRP Settings array.
 *
 * @since 1.0.0
 *
 * @param	array $crp_settings   CRP Settings.
 * @return	array	Filtered array of CRP Settings
 */
function crpt_crp_default_options( $crp_settings ) {

	$more_options = array(
		'crpt_tag'                    => false,	// Restrict to current post's tags.
		'crpt_category'               => false,	// Restrict to current post's categories.
		'crpt_taxes'                  => '',		// Restrict to custom taxonomies.
		'crpt_match_all'              => false,	// Require all or only one of the taxonomy terms to match.
		'crpt_disable_contextual'     => false,	// Disable contextual matching on all posts.
		'crpt_disable_contextual_cpt' => true,	// Disable contextual matching on custom post types only.
	);
	return	array_merge( $more_options, $crp_settings );
}
add_filter( 'crp_default_options', 'crpt_crp_default_options' );


/*
 ----------------------------------------------------------------------------*
 * CRP modules & includes
 *----------------------------------------------------------------------------
 */

require_once( CRPT_PLUGIN_DIR . 'includes/activation.php' );
require_once( CRPT_PLUGIN_DIR . 'includes/filters.php' );
require_once( CRPT_PLUGIN_DIR . 'includes/l10n.php' );

/*
 ----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
*/

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once( CRPT_PLUGIN_DIR . 'admin/admin.php' );

} // End if().

