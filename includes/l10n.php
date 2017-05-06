<?php
/**
 * Language functions
 *
 * @package CRP_Taxonomy
 */

/**
 * Initialises text domain for l10n.
 *
 * @since 1.0.0
 */
function crpt_lang_init() {
	load_plugin_textdomain( 'crp-taxonomy', false, dirname( plugin_basename( CRPT_PLUGIN_FILE ) ) . '/languages/' );
}
add_action( 'init', 'crpt_lang_init' );


