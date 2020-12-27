<?php
/**
 * CRP Taxonomy Admin interface.
 *
 * This page is accessible via Settings > Contextual Related Posts >
 *
 * @package     CRP_Taxonomy
 * @author      Ajay D'Souza
 * @license     GPL-2.0+
 * @link        https://webberzone.com
 * @copyright   2014-2019 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Add options to the General settings array of CRP.
 *
 * @since 1.4.0
 *
 * @param array $settings Contextual Related Posts General settings array.
 * @return array General settings array
 */
function crpt_settings_general( $settings ) {

	$new_settings = array(
		'crpt_same_taxes'  => array(
			'id'      => 'crpt_same_taxes',
			'name'    => esc_html__( 'Only from same', 'crp-taxonomy' ),
			'desc'    => esc_html__( 'Limit the related posts only to the categories, tags, and/or taxonomies of the current post.', 'crp-taxonomy' ),
			'type'    => 'taxonomies',
			'options' => '',
		),
		'crpt_match_all'   => array(
			'id'      => 'crpt_match_all',
			'name'    => esc_html__( 'Match all taxonomies', 'crp-taxonomy' ),
			'desc'    => esc_html__( 'If enabled, then it will only select posts that match all the above selected taxonomies. This can result in no related posts being found.', 'crp-taxonomy' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'crpt_no_of_taxes' => array(
			'id'      => 'crpt_no_of_taxes',
			'name'    => esc_html__( 'Number of common terms', 'crp-taxonomy' ),
			'desc'    => esc_html__( 'Enter the minimum number of common terms that have to be matched before a post is considered related.', 'crp-taxonomy' ),
			'type'    => 'number',
			'options' => '1',
			'min'     => '1',
		),
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'crp_settings_general', 'crpt_settings_general' );


/**
 * Add options to the List tuning settings array of CRP.
 *
 * @since 1.4.0
 *
 * @param array $settings Contextual Related Posts List tuning settings array.
 * @return array List tuning settings array
 */
function crpt_settings_list( $settings ) {

	$new_settings = array(
		'crpt_disable_contextual'     => array(
			'id'      => 'crpt_disable_contextual',
			'name'    => esc_html__( 'Disable contextual matching', 'crp-taxonomy' ),
			'desc'    => esc_html__( 'Selecting this option will turn off contextual matching. This is only useful if you activate the option: "Fetch related posts only from above" from the General tab. Otherwise, you will end up with the same set of related posts on all pages with no relevance.', 'crp-taxonomy' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'crpt_disable_contextual_cpt' => array(
			'id'      => 'crpt_disable_contextual_cpt',
			'name'    => esc_html__( 'Disable contextual matching ONLY on attachments and custom post types', 'crp-taxonomy' ),
			'desc'    => esc_html__( 'Applies only if the previous option is checked. Selecting this option will retain contextual matching for posts and pages but disable this on any custom post types.', 'crp-taxonomy' ),
			'type'    => 'checkbox',
			'options' => true,
		),
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'crp_settings_list', 'crpt_settings_list' );


/**
 * Add options to the Output settings array of CRP.
 *
 * @since 1.5.0
 *
 * @param array $settings Contextual Related Posts List tuning settings array.
 * @return array Output settings array
 */
function crpt_settings_output( $settings ) {

	$new_settings = array(
		'crpt_output_header'   => array(
			'id'   => 'crpt_output_header',
			'name' => '<h3>' . esc_html__( 'CRP Taxonomy settings', 'crp-taxonomy' ) . '</h3>',
			'desc' => '',
			'type' => 'header',
		),
		'exclude_on_cat_slugs' => array(
			'id'               => 'exclude_on_cat_slugs',
			'name'             => esc_html__( 'Exclude on Categories', 'crp-taxonomy' ),
			'desc'             => esc_html__( 'Comma separated list of category slugs. The field above has an autocomplete so simply start typing in the starting letters and it will prompt you with options. Does not support custom taxonomies.', 'crp-taxonomy' ),
			'type'             => 'csv',
			'options'          => '',
			'size'             => 'large',
			'field_class'      => 'category_autocomplete',
			'field_attributes' => array(
				'data-wp-taxonomy' => 'category',
			),
		),
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'crp_settings_output', 'crpt_settings_output' );


/**
 * Display notices in the admin screen if Contextual Related Posts v2.6.0 is not installed.
 *
 * @since 1.4.0
 */
function crpt_admin_notices() {

	if ( ! function_exists( 'crp_get_settings' ) ) {
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Related Posts by Categories and Tags requires Contextual Related Posts v2.6.0. Please install it or deactivate this plugin.', 'crp-taxonomy' ); ?></p>
		</div>
		<?php
	}
}
add_filter( 'admin_notices', 'crpt_admin_notices' );


/**
 * Modify settings when they are being saved.
 *
 * @since 1.5.0
 *
 * @param  array $settings Settings array.
 * @return string  $settings  Sanitized settings array.
 */
function crpt_change_settings_on_save( $settings ) {

	// Sanitize exclude_on_cat_slugs to save a new entry of exclude_on_categories.
	if ( isset( $settings['exclude_on_cat_slugs'] ) ) {

		$exclude_on_cat_slugs = array_unique( explode( ',', $settings['exclude_on_cat_slugs'] ) );

		foreach ( $exclude_on_cat_slugs as $cat_name ) {
			$cat = get_term_by( 'name', $cat_name, 'category' );

			if ( isset( $cat->term_taxonomy_id ) ) {
				$exclude_on_categories[]       = $cat->term_taxonomy_id;
				$exclude_on_categories_slugs[] = $cat->name;
			}
		}
		$settings['exclude_on_categories'] = isset( $exclude_on_categories ) ? join( ',', $exclude_on_categories ) : '';
		$settings['exclude_on_cat_slugs']  = isset( $exclude_on_categories_slugs ) ? join( ',', $exclude_on_categories_slugs ) : '';

	}

	return $settings;
}
add_filter( 'crp_settings_sanitize', 'crpt_change_settings_on_save' );
