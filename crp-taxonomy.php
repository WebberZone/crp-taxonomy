<?php
/**
 * Related Posts by Categories and Tags
 *
 * CRP Taxomy Tools is an addon for Contextual Related Posts.
 * It adds extra options to CRP settings that allow you to
 * restrict posts to the category or tag of the current post.
 *
 * @package     CRP_Taxonomy
 * @author      Ajay D'Souza
 * @license     GPL-2.0+
 * @link        https://webberzone.com
 * @copyright   2014-2020 Ajay D'Souza
 *
 * @wordpress-plugin
 * Plugin Name: Related Posts by Categories and Tags
 * Plugin URI: https://webberzone.com/downloads/crp-taxonomy/
 * Description: Restrict the related posts to the same category, tag or custom taxonomy. Requires Contextual Related Posts v2.6.0 or higher.
 * Version: 1.5.0
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
 * @since 1.0.0
 *
 * @var string Plugin Root File
 */
if ( ! defined( 'CRPT_PLUGIN_FILE' ) ) {
	define( 'CRPT_PLUGIN_FILE', __FILE__ );
}


/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 1.0.0
 *
 * @var string Plugin folder path
 */
if ( ! defined( 'CRPT_PLUGIN_DIR' ) ) {
	define( 'CRPT_PLUGIN_DIR', plugin_dir_path( CRPT_PLUGIN_FILE ) );
}

/**
 * Holds the filesystem directory path (with trailing slash) for Contextual Related Posts.
 *
 * @since 1.0.0
 *
 * @var string Plugin folder URL
 */
if ( ! defined( 'CRPT_PLUGIN_URL' ) ) {
	define( 'CRPT_PLUGIN_URL', plugin_dir_url( CRPT_PLUGIN_FILE ) );
}

/*
 *---------------------------------------------------------------------------*
 * CRP modules & includes
 *----------------------------------------------------------------------------
 */

require_once CRPT_PLUGIN_DIR . 'includes/activation.php';
require_once CRPT_PLUGIN_DIR . 'includes/filters.php';
require_once CRPT_PLUGIN_DIR . 'includes/l10n.php';
require_once CRPT_PLUGIN_DIR . 'includes/deprecated.php';

/*
 *---------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------
*/

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

	require_once CRPT_PLUGIN_DIR . 'includes/admin/admin.php';
	require_once CRPT_PLUGIN_DIR . 'includes/admin/deprecated.php';

}

