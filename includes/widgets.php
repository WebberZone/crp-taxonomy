<?php
/**
 * Widget functions
 *
 * @package CRP_Taxonomy
 */

/**
 * Add options to CRP widget.
 *
 * @since 1.3.0
 * @param array $instance Widget Instance.
 *
 * @return array Updated Widget Instance
 */
function crpt_widget_options_add( $instance ) {

	$args = array(
		'public'   => true,
		'_builtin' => false,
	);

	$wp_taxonomies = get_taxonomies( $args, 'names', 'and' );

	$taxonomies = isset( $instance['crpt_taxes'] ) ? explode( ',', $instance['crpt_taxes'] ) : array();
	$taxonomies = array_intersect( $taxonomies, $wp_taxonomies );

	$crpt_category = isset( $instance['crpt_category'] ) ? esc_attr( $instance['crpt_category'] ) : '';
	$crpt_tag = isset( $instance['crpt_tag'] ) ? esc_attr( $instance['crpt_tag'] ) : '';

	?>

	<p><?php esc_html_e( 'Fetch related posts only from', 'crp-taxonomy' ); ?>:<br />

			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'crpt_category' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'crpt_category' ) ); ?>" <?php checked( $crpt_category, true ); ?> /> <?php esc_html_e( 'Same categories', 'crp-taxonomy' ); ?></label><br />
			<label><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'crpt_tag' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'crpt_tag' ) ); ?>" <?php checked( $crpt_tag, true ); ?> /> <?php esc_html_e( 'Same tags', 'crp-taxonomy' ); ?></label><br />

			<?php if ( ! empty( $wp_taxonomies ) ) : foreach ( $wp_taxonomies as $taxonomy ) : ?>

				<label>
					<input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'crpt_taxes' ) ); ?>[]" value="<?php echo esc_attr( $taxonomy ); ?>"
					<?php checked( in_array( $taxonomy, $taxonomies, true ), true ); ?> />
					<?php /* translators: taxonomy. */
						printf( esc_html__( 'Same %s', 'crp-taxonomy' ), $taxonomy );
					?>
				</label><br />

			<?php endforeach;
endif; ?>

	</p>

	<?php
}
add_action( 'crp_widget_options_after', 'crpt_widget_options_add' );


/**
 * Update CRP Taxonomy options when CRP widget options are saved.
 *
 * @since 1.3.0
 * @param array $instance Widget Instance.
 *
 * @return array Update Widget Instance
 */
function crpt_widget_options_update( $instance ) {

	$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

	return $instance;
}
add_filter( 'crp_widget_options_update', 'crpt_widget_options_update' );


/**
 * Include the arguments to the front-end display of the widget.
 *
 * @since 1.3.0
 * @param array $arguments Arguments passed to get_crp.
 *
 * @return array Filtered arguments
 */
function crpt_widget_options( $arguments ) {

}
add_filter( 'crp_widget_options', 'crpt_widget_options' );

