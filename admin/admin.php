<?php
/**
 * CRP Taxonomy Admin interface.
 *
 * This page is accessible via Settings > Contextual Related Posts >
 *
 * @package		CRP_Taxonomy
 * @author		Ajay D'Souza <me@ajaydsouza.com>
 * @license		GPL-2.0+
 * @link		https://webberzone.com
 * @copyright 	2014-2015 Ajay D'Souza
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
 * @param	array $crp_settings   CRP Settings.
 * @param	array $postvariable   $_POST array.
 * @return	array	Filtered CRP settings
 */
function crpt_save_options( $crp_settings, $postvariable ) {

	/* Save options for categories and tags */
	$crp_settings['crpt_tag'] = ( isset( $postvariable['crpt_tag'] ) ? true : false );
	$crp_settings['crpt_category'] = ( isset( $postvariable['crpt_category'] ) ? true : false );

	/* Fetch custom taxonomies */
	$args = array(
		'public'   => true,
		'_builtin' => false,
	);

	$output = 'names';
	$operator = 'and';
	$wp_taxonomies = get_taxonomies( $args, $output, $operator );

	/* Save options for custom taxonomies */
	$taxonomies = ( isset( $postvariable['crpt_taxes'] ) && is_array( $postvariable['crpt_taxes'] ) ) ? $postvariable['crpt_taxes'] : array();
	$taxonomies = array_intersect( $wp_taxonomies, $taxonomies );

	$crp_settings['crpt_taxes'] = implode( ',', $taxonomies );
	$crp_settings['crpt_match_all'] = isset( $postvariable['crpt_match_all'] ) ? true : false;
	$crp_settings['crpt_disable_contextual'] = ( isset( $postvariable['crpt_disable_contextual'] ) ? true : false );
	$crp_settings['crpt_disable_contextual_cpt'] = ( isset( $postvariable['crpt_disable_contextual_cpt'] ) ? true : false );

	return $crp_settings;
}
add_filter( 'crp_save_options', 'crpt_save_options', 10, 2 );



/**
 * Add options to CRP Settings > General Options.
 *
 * @since 1.0.0
 *
 * @param	array $crp_settings   CRP Settings.
 */
function crt_general_options( $crp_settings ) {

	$args = array(
		'public'   => true,
		'_builtin' => false,
	);

	$output = 'names';
	$operator = 'and';
	$wp_taxonomies = get_taxonomies( $args, $output, $operator );

	$taxonomies = isset( $crp_settings['crpt_taxes'] ) ? explode( ',', $crp_settings['crpt_taxes'] ) : array();
	$taxonomies = array_intersect( $taxonomies, $wp_taxonomies );

	if ( ! isset( $crp_settings['crpt_match_all'] ) ) {
		$crp_settings['crpt_match_all'] = 'OR';
	}
?>

	<tr><th scope="row"><?php esc_html_e( 'Fetch related posts only from', 'crp-taxonomy' ); ?>:</th>
		<td>
			<label><input type="checkbox" name="crpt_category" id="crpt_category" <?php checked( $crp_settings['crpt_category'], true ); ?> /> <?php esc_html_e( 'Same categories', 'crp-taxonomy' ); ?></label><br />
			<label><input type="checkbox" name="crpt_tag" id="crpt_tag" <?php checked( $crp_settings['crpt_tag'], true ); ?> /> <?php esc_html_e( 'Same tags', 'crp-taxonomy' ); ?></label><br />

			<?php if ( ! empty( $wp_taxonomies ) ) : foreach ( $wp_taxonomies as $taxonomy ) : ?>

				<label>
					<input type="checkbox" name="crpt_taxes[]" value="<?php echo esc_attr( $taxonomy ); ?>"
					<?php checked( in_array( $taxonomy, $taxonomies, true ), true ); ?> />
					<?php /* translators: taxonomy. */
						printf( esc_html__( 'Same %s', 'crp-taxonomy' ), esc_attr( $taxonomy ) );
					?>
				</label><br />

			<?php endforeach;
endif; ?>

			<p class="description"><?php esc_html_e( 'Limit the related posts only to the current categories, tags, and/or taxonomies.', 'crp-taxonomy' ); ?></p>
		</td>
	</tr>

	<tr><th scope="row"><?php esc_html_e( 'Taxonomy matching:', 'crp-taxonomy' ); ?></th>
		<td>
			<label><input type="checkbox" name="crpt_match_all" id="crpt_match_all" <?php checked( $crp_settings['crpt_match_all'], true ); ?> /> <?php esc_html_e( 'Match all taxonomy terms', 'crp-taxonomy' ); ?></label><br />
			<p class="description"><?php esc_html_e( 'If selected, will limit the related posts to ones that match all the above selected taxonomy terms of the current post instead of just one of them.', 'crp-taxonomy' ); ?></p>
			<p class="description highlight"><?php esc_html_e( 'Note: This can result in no related posts being found.', 'crp-taxonomy' ); ?></p>
		</td>
	</tr>

<?php
}
add_action( 'crp_admin_general_options_after', 'crt_general_options' );


/**
 * Add options to CRP Settings > List Tuning Options.
 *
 * @since 1.1.0
 *
 * @param	array $crp_settings   CRP Settings.
 */
function crt_tuning_options( $crp_settings ) {
?>

	<tr><th scope="row"><?php esc_html_e( 'Disable contextual matching', 'crp-taxonomy' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="crpt_disable_contextual" id="crpt_disable_contextual" <?php if ( $crp_settings['crpt_disable_contextual'] ) { echo 'checked="checked"'; } ?> />
			</label>

			<p class="description"><?php esc_html_e( 'Selecting this option will turn off contextual matching. This is only useful if you activate the above option: "Fetch related posts only from above". Otherwise, you will end up with the same set of related posts on all pages.', 'crp-taxonomy' ); ?></p>
		</td>
	</tr>
	<tr><th scope="row"><?php esc_html_e( 'Disable contextual matching ONLY on attachments and custom post types', 'crp-taxonomy' ); ?></th>
		<td>
			<label>
				<input type="checkbox" name="crpt_disable_contextual_cpt" id="crpt_disable_contextual_cpt" <?php if ( $crp_settings['crpt_disable_contextual_cpt'] ) { echo 'checked="checked"'; } ?> />
			</label>

			<p class="description"><?php esc_html_e( 'Applies only if the previous option is checked. Selecting this option will retain contextual matching for posts and pages but disable this on any custom post types.', 'crp-taxonomy' ); ?></p>
		</td>
	</tr>

<?php
}
add_action( 'crp_admin_tuning_options_before', 'crt_tuning_options' );

