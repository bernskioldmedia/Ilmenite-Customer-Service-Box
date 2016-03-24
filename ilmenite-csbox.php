<?php
/*
 * Plugin Name: Ilmenite Customer Service Box
 * Plugin URI:  http://www.ilmenite.io
 * Description: Adds a floating customer service box to the side of the website.
 * Version:     1.0
 * Author:      Bernskiold Media
 * Author URI:  http://www.bernskioldmedia.com
 * Text Domain: ilcsb
 * Domain Path: /languages/
 *
 * **************************************************************************
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program. If not, see <http://www.gnu.org/licenses/>.
 *
 * **************************************************************************
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

class Ilmenite_CSBox {

	/**
	 * Plugin URL
	 *
	 * @var string
	 */
	public $plugin_url = '';

	/**
	 * Plugin Directory Path
	 *
	 * @var string
	 */
	public $plugin_dir = '';

	/**
	 * Plugin Version Number
	 *
	 * @var string
	 */
	public $plugin_version = '';


	/**
	 * @var The single instance of the class
	 */
	protected static $_instance = null;

	public static function instance() {

	    if ( is_null( self::$_instance ) ) {
	    	self::$_instance = new self();
	    }

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.2
	 */
	private function __clone() {}

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.2
	 */
	private function __wakeup() {}

	/**
	 * Constructor
	 */
	public function __construct() {

		// Set Plugin Version
		$this->plugin_version = '1.0';

		// Set plugin Directory
		$this->plugin_dir = untrailingslashit( plugin_dir_path( __FILE__ ) );

		// Set Plugin URL
		$this->plugin_url = untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) );

		// Load Translations
		add_action( 'plugins_loaded', array( $this, 'languages' ) );

		// Load Scripts & Styles
		add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'styles' ) );

		// Add output to wp_footer
		add_action( 'wp_footer', array( $this, 'html_output' ) );

		// Register form AJAX
		add_action( 'wp_ajax_ilcsb_phone_form', array( $this, 'process_phone_number_form' ) );
		add_action( 'wp_ajax_nopriv_ilcsb_phone_form', array( $this, 'process_phone_number_form' ) );

		// Load Settings
		require_once( 'class-ilcsb-admin-settings.php' );
		$this->settings = new ILCSB_Admin_Settings();

			// Run Activation Hook
		register_activation_hook( __FILE__, array( $this, 'plugin_activation' ) );

	}

	/**
	 * Load Translations
	 */
	public function languages() {

		load_plugin_textdomain( 'ilcsb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	 * Scripts
	 */
	public function scripts() {

		wp_register_script( 'ilcsb', $this->get_plugin_assets_uri() . 'js/csbox.js', array( 'jquery' ), $this->get_plugin_version(), true );

		wp_localize_script( 'ilcsb', 'ilcsb', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
		) );

		wp_enqueue_script( 'ilcsb' );

	}

	/**
	 * Styles
	 */
	public function styles() {

		wp_register_style( 'ilcsb', $this->get_plugin_assets_uri() . 'css/csbox.css', false, $this->get_plugin_version(), 'all' );

		wp_enqueue_style( 'ilcsb' );

	}

	/**
	 * Box HTML Output
	 */
	public function html_output() {

		include( $this->get_plugin_dir() . '/templates/popout-box.php' );

	}

	/**
	 * Get Contact Methods
	 */
	public function get_contact_methods() {

		// Get the options
		$options = get_option( 'ilcsb_settings' );

		$contact_methods = array();

		$contact_methods['email'] = array(
			'value'     => $options['ilcsb_email'],
			'icon'      => true,
			'link'      => 'mailto:' . $options['ilcsb_email'],
			'status'    => true,
			'content'   => false,
		);

		$contact_methods['phone_out'] = array(
			'value'     => __( 'Let us call you', 'ilcs' ),
			'icon'      => true,
			'link'      => '#',
			'status'    => true,
			'content'   => $this->get_phone_number_form(),
		);

		$contact_methods['phone_in'] = array(
			'value'     => $options['ilcsb_phone'],
			'icon'      => true,
			'link'      => 'tel://' . $options['ilcsb_phone'],
			'status'    => true,
			'content'   => false,
		);

		return $contact_methods;

	}

	/**
	 * Phone Call Hours
	 */
	public function is_phone_open() {

		// Get the options
		$options = get_option( 'ilcsb_settings' );

		// Set the phone "open" hours.
		$open_hours = array(
			'from'  => new DateTime( $options['ilcsb_phone_from'] ),
			'to'    => new DateTime( $options['ilcsb_phone_to'] ),
		);

		// Get the current time
		$current_time = new DateTime();

		if ( $current_time >= $open_hours['from'] && $current_time <= $open_hours['to'] ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Leave Phone Number Form
	 */
	public function get_phone_number_form() {

		ob_start();
		include( $this->get_plugin_dir() . '/templates/leave-phone-number.php' );
		$output = ob_get_clean();

		return $output;
	}

	/**
	 * Process Phone Number Form
	 * using Admin AJAX
	 */
	public function process_phone_number_form() {

		// Verify nonce
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'send_phone' ) )
			wp_die( __( 'The nonce did not verify.', 'ilcsb' ) );

		// Get name
		$name = sanitize_text_field( $_REQUEST['name'] );

		// Get phone number
		$phone = sanitize_text_field( $_REQUEST['phone'] );

		if ( ! empty( $phone ) ) {

			// Email To Address
			$to_address = 'info@bernskioldmedia.com';

			// Email Subject
			$email_subject = __( 'Request For Phone Call', 'ilcsb' );

			// Email Message
			$email_message = sprintf(
				__( 'The following person has requested that you call them back.\n\nName: %1$s\nPhone: %2$s', 'ilcsb' ),
				$name,
				$phone
			);

			// Send the email
			wp_mail(
				$to_address,
				$email_subject,
				$email_message
			);

			echo __( 'We will get in touch with you as soon as we can.', 'ilcsb' );

		} else {
			echo __( 'You need to enter your phone number.', 'ilcsb' );
		}

		wp_die();

	}

	/**
	 * Activation Trigger
	 *
	 * This code is run automatically when the WordPress
	 * plugin is activated.
	 */
	public function plugin_activation() {

		// Initialize all the CPTs and flush permalinks
		flush_rewrite_rules();

	}

	/**
	 * Get the Plugin's Directory Path
	 *
	 * @return string
	 */
	public function get_plugin_dir() {
		return $this->plugin_dir;
	}

	/**
	 * Get the Plugin's Directory URL
	 *
	 * @return string
	 */
	public function get_plugin_url() {
		return $this->plugin_url;
	}

	/**
	 * Get the Plugin's Version
	 *
	 * @return string
	 */
	public function get_plugin_version() {
		return $this->plugin_version;
	}

	/**
	 * Get the Plugin's Asset Directory URL
	 *
	 * @return string
	 */
	public function get_plugin_assets_uri() {
		return $this->plugin_url . '/assets/';
	}

}

function Ilmenite_CSBox() {
    return Ilmenite_CSBox::instance();
}

// Initialize the class instance only once
Ilmenite_CSBox();