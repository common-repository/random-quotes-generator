<?php

/**
 * Plugin Name: Random Quotes Generator
 * Plugin URI: https://github.com/GrigoreMihai/quotes-block
 * Description: This is a plugin adding a custom block for displaying random quotes on the page
 * Version: 1.0.0
 * Author: Grigore Mihai
 * Text Domain:       random-quotes-generator
 * Domain Path:       /languages
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * WordPress Available:  yes
 * Requires License:    no
 */


/**
 *  Calls the api and renders the dynamic content of the card
 *
 */
function random_quotes_generator_dynamic_render_callback( $block_attributes, $content ) {
	$response = wp_remote_get( 'https://api.quotable.io/random' );
	$quote = false;

	if ( is_array( $response ) && ! is_wp_error( $response ) ) {
		$quote = json_decode( $response['body'], true );
	}

	return sprintf(
		'<div>
		 <blockquote class="random-quotes-blockquote"><p>“%1$s”
		 </p><cite>%2$s</cite></blockquote>
		</div>',
		$quote['content'] ? $quote['content'] : __( 'The meaning I picked, the one that changed my life: Overcome fear, behold wonder.', 'random-quotes' ),
		$quote['author'] ? $quote['author'] : 'Richard Bach'
	);
}
/**
 * Load all translations for our plugin from the MO file.
 */
add_action( 'init', 'random_quotes_generator_load_textdomain' );

function random_quotes_generator_load_textdomain() {
	load_plugin_textdomain( 'random-quotes-generator', false, basename( __DIR__ ) . '/languages' );
}


/**
 * Registers all block assets so that they can be enqueued through Gutenberg in
 * the corresponding context.
 *
 */
function random_quotes_generator_register_block() {

	// automatically load dependencies and version
	$asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
	wp_register_script(
		'random-quotes-generator',
		plugins_url( 'build/index.js', __FILE__ ),
		$asset_file['dependencies'],
		$asset_file['version']
	);

	wp_register_style(
		'random-quotes-generator',
		plugins_url( 'style.css', __FILE__ ),
		array( ),
		filemtime( plugin_dir_path( __FILE__ ) . 'style.css' )
	);

	register_block_type( 'random-quotes-generator/random-quotes', array(
		'style' => 'random-quotes-generator',
		'editor_script' => 'random-quotes-generator',
		'render_callback' => 'random_quotes_generator_dynamic_render_callback'
	) );
	if ( function_exists( 'wp_set_script_translations' ) ) {
		wp_set_script_translations( 'random-quotes-generator', 'random-quotes-generator' );
	}

}
add_action( 'init', 'random_quotes_generator_register_block' );
