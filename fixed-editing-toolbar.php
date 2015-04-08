<?php
/*
 * Plugin Name: Fixed Editor Toolbar
 * Description: Fixed toolbar that stays with as you scroll while editing a post/page.
 * Version: 0.3
 * Author: Brett Wysocki
 * Author URI: http://brettwysocki.com
 */

if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

//define( 'FIXED_TOOLBAR__PLUGIN_URL', plugin_dir_url( __FILE__ ) );
//define( 'FIXED_TOOLBAR__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once( plugin_dir_path( __FILE__ ) . 'fixed-editing-meta-box.php' );

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
		//wp_enqueue_script( 'fixed-toolbar-js', FIXED_TOOLBAR__PLUGIN_URL . 'js/fixed-toolbar.js', array( 'jquery' ) );
		wp_enqueue_script( 'fixed-toolbar-js', plugin_dir_url( __FILE__ ) . 'js/fixed-toolbar.js', array( 'jquery' ) );

	/* Switched to a Flexbox-based style */
		//wp_enqueue_style( 'fixed-toolbar-flex-css', FIXED_TOOLBAR__PLUGIN_URL . 'css/fixed-toolbar-flex.css' );
		wp_enqueue_style( 'fixed-toolbar-flex-css', plugin_dir_url( __FILE__ ) . 'css/fixed-toolbar-flex.css' );

	/* Keeping the old one around, in case I need it */
		//wp_enqueue_style( 'fixed-toolbar-css', FIXED_TOOLBAR__PLUGIN_URL . 'css/fixed-toolbar.css' );

	}
	add_action( 'admin_enqueue_scripts', 'enqueue_scripts' );

	function fixed_toolbar_admin_body_class( $classes ) {
		global $wpdb, $post;
		$classes .= 'fixed-toolbar ';
		if ( get_option('fixed_toolbar_position') == '' ) {
			update_option('fixed_toolbar_position', 'top');
		}
	    //$classes .= 'ft-top';
	    //$classes .= 'ft-bottom';
	    //$classes .= 'ft-left';
	    //$classes .= 'ft-right';
	    $classes .= 'ft-' . get_option('fixed_toolbar_position');
		return $classes;
	}

	function remove_submitdiv_meta_box() {
		if ( $post_type = get_current_post_type() ) {
			remove_meta_box( 'submitdiv', $post_type, 'side' );
			add_meta_box( 'fixed-submitdiv', __( 'Publish' ), 'fixed_submit_meta_box', $post_type, 'advanced', 'core' );
			add_filter( 'admin_body_class', 'fixed_toolbar_admin_body_class' );
		}
	}
	add_action( 'do_meta_boxes', 'remove_submitdiv_meta_box' );

	function fet_create_menu() {

		add_options_page(
			'Fixed Editing Toolbar Options', 
			'Fixed Editing Toolbar', 
			'manage_options', 
			'fixed-editing-toolbar', 
			'fet_settings_page'
		);

		add_action( 'admin_init', 'register_mysettings' );
	}
	add_action('admin_menu', 'fet_create_menu');

	function register_mysettings() {
		register_setting( 'fixed-toolbar-settings-group', 'fixed_toolbar_position' );
	}

	function fet_settings_page() {
	?>
	<div class="wrap">
	<h2>Fixed Editing Toolbar</h2>

	<form method="post" action="">
	    <?php settings_fields( 'fixed-toolbar-settings-group' ); ?>
	    <?php do_settings_sections( 'fixed-toolbar-settings-group' ); ?>
	    <?php $positions = array( 'top', 'right', 'bottom', 'left' );
	    if ( get_option('fixed_toolbar_position') == '' ) {
	    	update_option('fixed_toolbar_position', 'top');
	    } else if ( !empty( $_POST['fixed_toolbar_position'] ) && in_array( $_POST['fixed_toolbar_position'], $positions ) ) {
	    	update_option('fixed_toolbar_position', $_POST['fixed_toolbar_position']);
	    }
	    ?>
	    <table class="form-table">
	    	<tr>
	    		<td colspan="<?php echo count($positions); ?>"><strong>Fixed Toolbar Position</strong></td>
	    	</tr>
			<tr valign="top" class="choices">
				<?php foreach ( $positions as $pos ) {
					echo '<td width="25%" class="' . $pos . '">' .
							'<label for="fixed-toolbar-' . $pos . '">' .
								'<div class="icon"><span></span></div>' .
								'<p>' . ucwords($pos) . '<br />' .
								'<input id="fixed-toolbar-' . $pos . '" type="radio" name="fixed_toolbar_position" value="' . $pos . '"' . ( get_option('fixed_toolbar_position') == $pos ? ' checked="checked"' : '' ) . ' /></p>' .
							'</label></td>';
				} ?>
			</tr>
			<tr valign="top" class="choices">
				<?php foreach ( $positions as $pos ) {
					//echo '<td><input id="fixed-toolbar-' . $pos . '" type="radio" name="fixed_toolbar_position" value="' . $pos . '"' . ( get_option('fixed_toolbar_position') == $pos ? ' checked="checked"' : '' ) . ' /></td>';
				} ?>
			</tr>
	    </table>
	    
	    <?php submit_button(); ?>

	</form>
	</div>
	<?php }

} ?>