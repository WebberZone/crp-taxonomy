<?php
/**
 * CRP Taxonomy Admin interface.
 *
 * This page is accessible via Settings > Contextual Related Posts >
 *
 * @package   Contextual_Related_Posts
 * @author    Ajay D'Souza <me@ajaydsouza.com>
 * @license   GPL-2.0+
 * @link      http://ajaydsouza.com
 * @copyright 2009-2014 Ajay D'Souza
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Save the new options.
 *
 * @since 1.0.0
 *
 * @param	array	$crp_settings	CRP Settings
 * @param	array	$postvariable	$_POST array
 * @return	array	Filtered CRP settings
 */
function crpt_save_options( $crp_settings, $postvariable ) {

	$crp_settings['crpt_tag'] = ( isset( $postvariable['crpt_tag'] ) ? true : false );
	$crp_settings['crpt_category'] = ( isset( $postvariable['crpt_category'] ) ? true : false );

	return $crp_settings;
}
add_filter( 'crp_save_options', 'crpt_save_options', 10, 2 );



/**
 * Add options to CRP Settings > General Options.
 *
 * @since 1.0.0
 *
 * @param	array	$crp_settings	CRP Settings
 */
function crt_general_options( $crp_settings ) {

?>

	<tr><th scope="row"><?php _e( 'Fetch related posts only from:', 'crp-taxonomy' ); ?></th>
		<td>
			<label><input type="checkbox" name="crpt_category" id="crpt_category" <?php if ( $crp_settings['crpt_category'] ) echo 'checked="checked"' ?> /> <?php _e( 'Same categories', 'crp-taxonomy' ); ?></label><br />
			<label><input type="checkbox" name="crpt_tag" id="crpt_tag" <?php if ( $crp_settings['crpt_tag'] ) echo 'checked="checked"' ?> /> <?php _e( 'Same tags', 'crp-taxonomy' ); ?></label><br />
			<p class="description"><?php _e( "Limit the related posts only to the current categories and/or tags of the current posts. This should add a greater degree of relevance.", 'crp-taxonomy' ); ?></p>
		</td>
	</tr>

<?php
}
add_action( 'crp_admin_general_options_after', 'crt_general_options' );

?>