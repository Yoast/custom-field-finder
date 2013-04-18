<?php
/*
 * Plugin Name:	Custom Field Finder
 * Plugin URI: http://wordpress.org/extend/plugins/custom-field-finder/
 * Description: Allows you to easily find the custom fields (including hidden custom fields) and their values for a post, page or custom post type post.
 * Version: 0.1
 * Author: Joost de Valk
 * Author URI: http://yoast.com/
 * License: GPL v3
 */

class CustomFieldFinder {

	/**
	 * @var string The hook used for the plugins page.
	 */
	var $hook = 'cff';

	/**
	 * @var string Name used on plugins page and in menu
	 */
	var $name = 'Custom Field Finder';

	/**
	 * Class constructor.
	 */
	function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );
	}

	/**
	 * Register the plugins page, which resides under Tools.
	 */
	function register_page() {
		add_submenu_page( 'tools.php', $this->name, $this->name, 'manage_options', $this->hook, array( $this, 'plugin_page' ) );
	}

	/**
	 * Output for the plugin page.
	 */
	function plugin_page() {
		echo '<div class="wrap">';
		echo '<div id="icon-tools" class="icon32"><br></div>';
		echo '<h2>' . $this->name . '</h2>';

		echo '<p>Enter a post, page or custom post type ID below and press find custom fields to see the custom fields attached to that post.</p>';
		echo '<form method="get" action="' . admin_url( 'tools.php?page=' . $this->hook ) . '">';
		echo '<input type="hidden" name="page" value="' . $this->hook . '"/>';
		echo '<label for="post_id">Post ID:</label> <input type="text" name="post_id" id="post_id" value="' . ( ( isset( $_GET['post_id'] ) ) ? intval( $_GET['post_id'] ) : '' ) . '"/><br/><br/>';
		echo '<input type="submit" class="button-primary" value="Find custom fields"/>';
		echo '</form>';
		echo '</div>';

		if ( isset( $_GET['post_id'] ) ) {
			echo '<br/><br/>';
			$post = get_post( intval( $_GET['post_id'] ) );
			if ( is_null( $post ) ) {
				echo 'Post ' . intval( $_GET['post_id'] ) . ' not found.';
			} else {
				echo '<h2>Custom fields for post <em>"<a target="_blank" href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>"</em></h2>';
				$customs = get_post_custom( $post->ID );
				if ( count( $customs ) > 0 ) {
					ksort( $customs );
					echo '<p>Note that custom fields whose key starts with _ will normally be invisible in the custom fields interface.</p>';
					echo '<style>#cffoutput { max-width: 600px; } #cffoutput pre { margin: 0 } #cffoutput th, #cffoutput td { text-align: left; vertical-align: text-top; margin: 0; padding: 2px 10px; } #cffoutput tr:nth-child(2n) { background-color: #eee; }</style>';
					echo '<table id="cffoutput" cellspacing="0" cellpadding="0">';
					echo '<thead><tr><th width="40%">Key</th><th width="60%">Value(s)</th></tr></thead>';
					echo '<tbody>';
					foreach ( $customs as $key => $val ) {
						echo '<tr>';
						echo '<td>' . esc_html( $key ) . '</td><td>';
						if ( count( $val ) === 1 ) {
							$val = maybe_unserialize( $val[0] );
							if ( !is_array( $val ) )
								echo esc_html( $val );
							else
								echo '<pre>' . esc_html( print_r( $val, 1 ) ) . '</pre>';
						} else {
							foreach ( $val as $v ) {
								echo esc_html( $v ) . '<br/>';
							}
						}
						echo '</td></tr>';
						echo '</tr>';
					}
					echo '</tbody></table>';
				} else {
					echo '<p>No custom fields found for post ' . $post->ID . '.</p>';
				}
			}
		}
	}

}

$yoast_cff = new CustomFieldFinder();
