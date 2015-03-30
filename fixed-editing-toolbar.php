<?php
/*
 * Plugin Name: Fixed Editor Toolbar
 * Description: Fixed toolbar that stays with as you scroll while editing a post/page.
 * Version: 0.1
 * Author: Brett Wysocki
 * Author URI: http://brettwysocki.com
 */

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'FIXED_TOOLBAR__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'FIXED_TOOLBAR__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( FIXED_TOOLBAR__PLUGIN_DIR . 'fixed-editing-meta-box.php' );

function get_current_post_type() {
	global $post, $typenow, $current_screen;
	$current_screen = get_current_screen();
	if ( isset($_REQUEST['post']) && isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' )
		return get_post_type( $_REQUEST['post'] );
	elseif ($post && $post->post_type) 						// Check to see if a post object exists
		return $post->post_type;       
	elseif ($typenow) 										// Check if the current type is set
		return $typenow;
	elseif ($current_screen && $current_screen->post_type) 	// Check to see if the current screen is set
		return $current_screen->post_type;
	elseif (isset($_REQUEST['post_type'])) 					// Finally make a last ditch effort to check the URL query for type
		return sanitize_key($_REQUEST['post_type']);
	return null;
 }

if ( function_exists('add_action') ) {

function enqueue_scripts() {
	wp_enqueue_script( 'fixed-toolbar-js', FIXED_TOOLBAR__PLUGIN_URL . 'js/fixed-toolbar.js', array( 'jquery' ) );

/* Switched to a Flexbox-based style */
	wp_enqueue_style( 'fixed-toolbar-flex-css', FIXED_TOOLBAR__PLUGIN_URL . 'css/fixed-toolbar-flex.css' );

/* Keeping the old one around, in case I need it */
	//wp_enqueue_style( 'fixed-toolbar-css', FIXED_TOOLBAR__PLUGIN_URL . 'css/fixed-toolbar.css' );

}
add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

	function remove_submitdiv_meta_box() {
		if ( $post_type = get_current_post_type() ) {
			remove_meta_box( 'submitdiv', $post_type, 'side' );
			add_meta_box( 'fixed-submitdiv', __( 'Publish' ), 'fixed_submit_meta_box', $post_type, 'advanced', 'core' );
		}
	}
	add_action( 'do_meta_boxes', 'remove_submitdiv_meta_box' );
}

?>