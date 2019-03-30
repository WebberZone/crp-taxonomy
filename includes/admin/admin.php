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
 * @since 2.6.0
 *
 * @param array $settings Contextual Related Posts General settings array.
 * @return array General settings array
 */
function crpt_settings_general( $settings ) {

	$new_settings = array(
		'crpt_same_taxes' => array(
			'id'      => 'crpt_same_taxes',
			'name'    => esc_html__( 'Fetch related posts only from', 'contextual-related-posts' ),
			'desc'    => esc_html__( 'Limit the related posts only to the current categories, tags, and/or taxonomies.', 'contextual-related-posts' ),
			'type'    => 'taxonomies',
			'options' => '',
		),
		'crpt_match_all'  => array(
			'id'      => 'crpt_match_all',
			'name'    => esc_html__( 'Match all taxonomy terms', 'contextual-related-posts' ),
			'desc'    => esc_html__( 'If selected, will limit the related posts to ones that match all the above selected taxonomy terms of the current post instead of just one of them. This can result in no related posts being found.', 'contextual-related-posts' ),
			'type'    => 'checkbox',
			'options' => false,
		),
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'crp_settings_general', 'crpt_settings_general' );


/**
 * Add options to the List tuning settings array of CRP.
 *
 * @since 2.6.0
 *
 * @param array $settings Contextual Related Posts List tuning settings array.
 * @return array List tuning settings array
 */
function crpt_settings_list( $settings ) {

	$new_settings = array(
		'crpt_disable_contextual'     => array(
			'id'      => 'crpt_disable_contextual',
			'name'    => esc_html__( 'Disable contextual matching', 'contextual-related-posts' ),
			'desc'    => esc_html__( 'Selecting this option will turn off contextual matching. This is only useful if you activate the above option: "Fetch related posts only from above". Otherwise, you will end up with the same set of related posts on all pages.', 'contextual-related-posts' ),
			'type'    => 'checkbox',
			'options' => false,
		),
		'crpt_disable_contextual_cpt' => array(
			'id'      => 'crpt_disable_contextual_cpt',
			'name'    => esc_html__( 'Disable contextual matching ONLY on attachments and custom post types', 'contextual-related-posts' ),
			'desc'    => esc_html__( 'Applies only if the previous option is checked. Selecting this option will retain contextual matching for posts and pages but disable this on any custom post types.', 'contextual-related-posts' ),
			'type'    => 'checkbox',
			'options' => true,
		),
	);

	return array_merge( $settings, $new_settings );
}
add_filter( 'crp_settings_list', 'crpt_settings_list' );
