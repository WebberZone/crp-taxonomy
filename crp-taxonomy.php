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
 * Version: 1.6.0
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

// Only include files if we are below CRP_VERSION 3.0.0 as this functionality was added into v3.0.0.
if ( defined( 'CRP_VERSION' ) && version_compare( CRP_VERSION, '3.0.0', '<' ) ) {
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
} else {
	add_filter( 'admin_notices', 'crpt_disabled_notice' );

	/**
	 * Disable plugin notice for CRP v3.0.0.
	 *
	 * @since 1.6.0
	 */
	function crpt_disabled_notice() {
		crpt_migrate_settings();

		?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'Contextual Related Posts v3.0.0 or above has been detected. Related Posts by Categories and Tags is no longer needed as all its functionality has now been incorporated into Contextual Related Posts v3.0.0. You can deactivate and delete Related Posts by Categories and Tags. Remember to visit the Contextual Related Posts settings page and update your settings.', 'crp-taxonomy' ); ?></p>
			<p><?php esc_html_e( 'This notice will remain until the plugin is deactived.', 'crp-taxonomy' ); ?></p>
		</div>
		<?php
	}

	/**
	 * Upgrade CRP Taxonomy settings to CRP v3.0.0 and higher.
	 *
	 * @since 1.6.0
	 */
	function crpt_migrate_settings() {

		$options = get_option( 'crp_settings' );

		$settings = array(
			'crpt_same_taxes'             => 'same_taxes',
			'crpt_match_all'              => 'match_all',
			'crpt_no_of_taxes'            => 'no_of_common_terms',
			'crpt_disable_contextual'     => 'disable_contextual',
			'crpt_disable_contextual_cpt' => 'disable_contextual_cpt',
		);

		// Migrate setting. $key holds the old setting and $value holds the new.
		foreach ( $settings as $key => $value ) {
			if ( isset( $options[ $value ] ) ) {
				continue;
			}
			$options[ $value ] = isset( $options[ $key ] ) ? $options[ $key ] : crp_get_default_option( $value );
			unset( $options[ $key ] );
		}

		$did_update = update_option( 'crp_settings', $options );

		// If it updated, let's update the global variable.
		if ( $did_update ) {
			global $crp_settings;
			$crp_settings = $options;
		}
	}
}
