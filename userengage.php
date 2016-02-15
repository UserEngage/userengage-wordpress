<?php

/*
Plugin Name: UserEngage Plugin
Description: UserEngage Plugin for Wordpress.
Version: 1.0
Author: UserEngage
Author URI: https://www.userengage.io
License: GPLv2 or later
*/
/*	Copyright 2014 UserEngage

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


function ue_admin_style() {
	wp_register_style( 'custom_wp_admin_css', plugin_dir_url( __FILE__ ) . 'assets/css/style.css', false, '1.0.0' );
	wp_enqueue_style( 'custom_wp_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'ue_admin_style' );


function ue_widget ($meta) {

	global $user_login , $user_email;
	get_currentuserinfo();

	$output = "
	<script>
		window.civchat = {
			apiKey: \"$meta\",
			name: \"$user_login\",
			email: \"$user_email\"
	};
	</script>";

	echo $output;
}

function ue_widget_js() {
	wp_enqueue_script( 'UserEngage', 'https://widget.userengage.io/widget.js', array(), null, true );
}

add_action( 'wp_enqueue_scripts', 'ue_widget_js' );

if ( !class_exists( 'UserEngageScripts' ) ) {


	class UserEngageScripts {

		function UserEngageScripts() {
			add_action( 'admin_init', array( &$this, 'ue_admin_init' ) );
			add_action( 'admin_menu', array( &$this, 'ue_admin_menu' ) );
			add_action( 'wp_head', array( &$this, 'ue_wp_head' ) );
		}

		function ue_admin_init() {
			register_setting( 'ue-apiKey', 'ue__apiKey', 'trim' );
		}

		function ue_admin_menu() {
			add_menu_page(
			'UserEngage.io',
			'UserEngage.io',
			'manage_options',
			__FILE__, array( &$this, 'ue__panel' ),
			'dashicons-admin-userengage'
			);
		}

		function ue_wp_head() {
			$meta = get_option( 'ue__apiKey', '' );
				if ( $meta != '' ) {
					ue_widget($meta);
				}
		}

		function ue__panel() { ?>
			<div class="wrap">
			<?php screen_icon(); ?>
				<h2>UserEngage.io Plugin - Options</h2>
				<hr />
				<div class="ue__wrap">
				<div class="ue__brand"></div>
					<form name="dofollow" action="options.php" method="post">
						<?php settings_fields( 'ue-apiKey' ); ?>
						<h3 for="ue__apiKey">Your Api Key:</h3>
						<input type="text" id="apiKey" name="ue__apiKey" value="<?php echo esc_html( get_option( 'ue__apiKey' ) ); ?>" required><br>
						Please enter your application key which has been sent to your email address.<br>
						The api key is a 64 letter and number key.
						<p class="submit">
							<input class="button button-primary" type="submit" name="Submit" value="Submit Api Key" />
						</p>
					</form>
				</div>
			</div>
		<?php
		}
	}

$userengage_scripts = new UserEngageScripts();

}


