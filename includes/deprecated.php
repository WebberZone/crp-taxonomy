<?php
/**
 * Deprecated functions
 *
 * @package CRP_Taxonomy
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Add options to CRP Settings array.
 *
 * @since 1.0.0
 * @deprecated 1.4.0
 *
 * @param  array $crp_settings   CRP Settings.
 * @return array Filtered array of CRP Settings
 */
function crpt_crp_default_options( $crp_settings ) {

	_deprecated_function( __FUNCTION__, '1.4.0' );

	$more_options = array(
		'crpt_tag'                    => false, // Restrict to current post's tags.
		'crpt_category'               => false, // Restrict to current post's categories.
		'crpt_taxes'                  => '',    // Restrict to custom taxonomies.
		'crpt_match_all'              => false, // Require all or only one of the taxonomy terms to match.
		'crpt_disable_contextual'     => false, // Disable contextual matching on all posts.
		'crpt_disable_contextual_cpt' => true,  // Disable contextual matching on custom post types only.
	);
	return array_merge( $more_options, $crp_settings );
}
add_filter( 'crp_default_options', 'crpt_crp_default_options' );


