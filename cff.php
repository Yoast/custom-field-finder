<?php
/*
 * Plugin Name:	Custom field finder
 * Plugin URI: http://wordpress.org/extend/plugins/custom-field-finder/
 * Description: Allows you to easily find the custom fields (including hidden custom fields) and their values for a post, page or custom post type post.
 * Version: 0.3
 * Author: Team Yoast
 * Author URI: http://yoast.com/
 * Text Domain: custom-field-finder
 * License: GPL v3
 */

class Yoast_Custom_Field_Finder {

	/**
	 * @var string The hook used for the plugins page.
	 */
	var $hook = 'cff';

	/**
	 * Class constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_page' ) );
	}

	/**
	 * Register the plugins page, which resides under Tools.
	 */
	public function register_page() {
		$name = __( 'Custom field finder', 'custom-field-finder' );

		add_submenu_page( 'tools.php', $name, $name, 'manage_options', $this->hook, [ $this, 'plugin_page' ] );
	}

	/**
	 * Output for the plugin page.
	 */
	public function plugin_page() {
		$post_id = filter_input( INPUT_POST, 'post_id', FILTER_SANITIZE_NUMBER_INT );

		echo '<div class="wrap">';
		echo '<h2>' . esc_html__( 'Custom field finder', 'custom-field-finder' ) . '</h2>';

		echo '<p>', esc_html__( 'Enter a post, page or custom post type ID below and press find custom fields to see the custom fields attached to that post.', 'custom-field-finder' ), '</p>';
		echo '<form method="post">';
		wp_nonce_field( 'custom-field-finder' );
		echo '<label for="post_id">', esc_html__( 'Post ID', 'custom-field-finder' ), ':</label> <input type="text" name="post_id" id="post_id" value="' . $post_id . '"/><br/><br/>';
		echo '<input type="submit" class="button-primary" value="', esc_html__( 'Find custom fields', 'custom-field-finder' ), '"/>';
		echo '</form>';
		echo '</div>';

		if ( $post_id > 0 && wp_verify_nonce( $_POST['_wpnonce'], 'custom-field-finder' ) ) {
			echo '<br/><br/>';
			$post = get_post( $post_id );
			if ( is_null( $post ) ) {
				echo sprintf( esc_html__( 'Post %d not found.', 'custom-field-finder' ), $post_id );
			} else {
				echo '<h2>', esc_html__( 'Custom fields for post', 'custom-field-finder' ), ' <em>"<a target="_blank" href="' . get_permalink( $post->ID ) . '">' . $post->post_title . '</a>"</em></h2>';
				$customs = get_post_custom( $post->ID );
				if ( count( $customs ) > 0 ) {
					ksort( $customs );
					echo '<p>', __( 'Note that custom fields whose key starts with _ will normally be invisible in the custom fields interface.', 'custom-field-finder' ), '</p>';
					echo '<style>#cffoutput { max-width: 600px; }, #cffoutput .key { width: 40%; }, #cffoutput .value { width: 60%; }, #cffoutput pre { margin: 0 } #cffoutput th, #cffoutput td { text-align: left; vertical-align: text-top; margin: 0; padding: 2px 10px; } #cffoutput tr:nth-child(2n) { background-color: #eee; }</style>';
					echo '<table id="cffoutput">';
					echo '<thead><tr><th class="key">', esc_html__( 'Key', 'custom-field-finder' ), '</th><th class="value">', esc_html__( 'Value(s)', 'custom-field-finder' ), '</th></tr></thead>';
					echo '<tbody>';
					foreach ( $customs as $key => $val ) {
						echo '<tr>';
						echo '<td>', esc_html( $key ), '</td><td>';
						if ( count( $val ) === 1 ) {
							$val = maybe_unserialize( $val[0] );
							if ( ! is_array( $val ) ) {
								echo esc_html( $val );
							}
							else {
								echo '<pre>', esc_html( print_r( $val, 1 ) ), '</pre>';
							}
						}
						else {
							foreach ( $val as $v ) {
								echo esc_html( $v ), '<br/>';
							}
						}
						echo '</td></tr>';
						echo '</tr>';
					}
					echo '</tbody></table>';
				} else {
					echo '<p>', sprintf( esc_html__( 'No custom fields found for post %d.', 'custom-field-finder' ), $post->ID ), '</p>';
				}
			}
		}
	}

}

$yoast_cff = new Yoast_Custom_Field_Finder();
