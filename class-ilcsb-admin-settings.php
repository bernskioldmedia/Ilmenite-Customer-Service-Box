<?php

/**
 * Admin Settings Page
 */
class ILCSB_Admin_Settings {

	/**
	 * ILCSB_Admin_Settings Constructor
	 */
	public function __construct() {

	// Add admin menu
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		// Initalize Settings
		add_action( 'admin_init', array( $this, 'settings_init' ) );

	}

	public function add_admin_menu() {

		add_submenu_page(
			'themes.php',
			__( 'Customer Service Box', 'ilcsb' ),
			__( 'Customer Service Box', 'ilcsb' ),
			'manage_options',
			'ilcsb_settings',
			array( $this, 'settings_page' )
		);

	}

	public function settings_init() {

		register_setting( 'ilcsb_group', 'ilcsb_settings' );

		add_settings_section(
			'ilcsb_contact_methods',
			__( 'Contact Methods', 'ilcsb' ),
			array( $this, 'cb_section' ),
			'ilcsb_settings'
		);

		add_settings_field(
			'ilcsb_phone',
			__( 'Phone Number', 'ilcsb' ),
			array( $this, 'cb_input' ),
			'ilcsb_settings',
			'ilcsb_contact_methods',
			array(
				'label_for'     => 'ilcsb_phone',
				'description'   => __( 'Enter the phone number where your customers can call you.', 'ilcsb' ),
			)
		);

		add_settings_field(
			'ilcsb_phone_from',
			__( 'Phone Open From', 'ilcsb' ),
			array( $this, 'cb_input' ),
			'ilcsb_settings',
			'ilcsb_contact_methods',
			array(
				'label_for'     => 'ilcsb_phone_from',
				'description'   => __( 'From which hour is the phone open.', 'ilcsb' ),
			)
		);

		add_settings_field(
			'ilcsb_phone_to',
			__( 'Phone Open Until', 'ilcsb' ),
			array( $this, 'cb_input' ),
			'ilcsb_settings',
			'ilcsb_contact_methods',
			array(
				'label_for'     => 'ilcsb_phone_to',
				'description'   => __( 'Until which hour is the phone open.', 'ilcsb' ),
			)
		);

		add_settings_field(
			'ilcsb_email',
			__( 'E-mail Address', 'ilcsb' ),
			array( $this, 'cb_input' ),
			'ilcsb_settings',
			'ilcsb_contact_methods',
			array(
				'label_for'     => 'ilcsb_email',
				'description'   => __( 'Enter the email address where your customers can contact you.', 'ilcsb' ),
			)
		);

	}

	public function settings_page() {

		?>

		<form action="options.php" method="post">

			<h1><?php _e( 'Ilmenite Customer Service Box', 'ilcsb' ); ?></h1>

			<?php
			settings_fields( 'ilcsb_group' );
			do_settings_sections( 'ilcsb_settings' );
			submit_button();
			?>

		</form>

		<?php

	}

	public function cb_input( $args ) {

		$options = get_option( 'ilcsb_settings' );
		?>

			<input type="text" name="ilcsb_settings[<?php echo $args['label_for']; ?>]" value="<?php echo $options[ $args['label_for'] ]; ?>" id="<?php echo $args['label_for']; ?>" class="regular-text">

			<?php if ( $args['description'] ) : ?>
				<p class="description" id="<?php echo $args['label_for']; ?>-description">
					<?php echo $args['description']; ?>
				</p>
			<?php endif; ?>

		<?php
	}

	public function cb_section() {}

}