<?php
/*
Plugin Name: TCBD Auto Refresher
Plugin URI: http://demos.tcoderbd.com/wordpress_plugins/
Description: This plugin will enable auto refresh your page in your Wordpress theme.
Author: Md Touhidul Sadeek
Version: 2.0
Author URI: http://tcoderbd.com
*/

/*  Copyright 2015 tCoderBD (email: info@tcoderbd.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

// Add settings page link in before activate/deactivate links.
function tcbd_auto_refresher_plugin_action_links( $actions, $plugin_file ){
	
	static $plugin;

	if ( !isset($plugin) ){
		$plugin = plugin_basename(__FILE__);
	}
		
	if ($plugin == $plugin_file) {
		
		if ( is_ssl() ) {
			$settings_link = '<a href="'.admin_url( 'plugins.php?page=tcbd_auto_refresh_settings', 'https' ).'">Settings</a>';
		}else{
			$settings_link = '<a href="'.admin_url( 'plugins.php?page=tcbd_auto_refresh_settings', 'http' ).'">Settings</a>';
		}
		
		$settings = array($settings_link);
		
		$actions = array_merge($settings, $actions);
			
	}
	
	return $actions;
	
}
add_filter( 'plugin_action_links', 'tcbd_auto_refresher_plugin_action_links', 10, 5 );


// Include Settings page
include( plugin_dir_path(__FILE__).'/settings.php' );


function tcbd_auto_refresh_active(){
	
	$tcbd_time = get_option('tcbd_auto_refresh_time');
	$tcbd_custom = get_option('tcbd_auto_refresh_custom');
	
	if (get_option( 'tcbd_auto_screen' ) == 'disabled') {

	} else if (
		get_option( 'tcbd_auto_screen' ) == 'all'
		or get_option( 'tcbd_auto_screen' ) == 'homepage' and is_home()
		or get_option( 'tcbd_auto_screen' ) == 'frontpage' and is_front_page()
		or get_option( 'tcbd_auto_screen' ) == 'posts' and is_single()
		or get_option( 'tcbd_auto_screen' ) == 'pages' and is_page()
		or get_option( 'tcbd_auto_screen' ) == 'cats' and is_category()
		or get_option( 'tcbd_auto_screen' ) == 'tags' and is_tag()
		or get_option( 'tcbd_auto_screen' ) == 'attachment' and is_attachment()
		or get_option( 'tcbd_auto_screen' ) == '404error' and is_404()
	) {
		echo '<meta http-equiv="refresh" content="'.$tcbd_time.'" >';
	} else if( get_option('tcbd_auto_refresh_custom') == 'custom' ) {
		echo '<meta http-equiv="refresh" content="'.$tcbd_time.';URL='.esc_url($tcbd_custom).'" >';
	}
}
add_action('wp_head', 'tcbd_auto_refresh_active');

function tcbd_auto_refresh_scripts() {
	?>
	<script>
		document.addEventListener('DOMContentLoaded', (event) => {
			var copyText = document.getElementById("tcbd_input_copy");
			var status = document.getElementById("tcbd_input_copy_status");
			copyText.addEventListener('focus', function() {
				copyText.select();
				copyText.setSelectionRange(0, 99999);
				navigator.clipboard.writeText(copyText.value).then(function() {
					status.textContent = "Copied!";
					setTimeout(() => {
						status.textContent = "";
					}, 1500);
				});
			});
		});
	</script>
	<?php
}
add_action('admin_head', 'tcbd_auto_refresh_scripts');

function tcbd_auto_refresh_shortcode( $atts ) {
	extract( shortcode_atts( array(
		'time' => '',
		'url' => ''
	), $atts ) );
	return '<meta http-equiv="refresh" content="'.$time.';URL='.esc_url($url).'" >';
}	
add_shortcode('tcbd_auto_refresh', 'tcbd_auto_refresh_shortcode');	