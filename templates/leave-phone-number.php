<?php
/**
 * Leave Phone Number Form
 */
?>
<form action="" method="post" class="cs-box-form cs-box-leave-phone">

	<div class="cs-box-form-message"></div>

	<div class="cs-box-form-fields">

		<label for="name">
			<?php _e( 'Your Name', 'ilmenite-csbox' ); ?>
			<input type="text" name="name" id="name">
		</label>

		<label for="phone">
			<?php _e( 'Phone Number', 'ilmenite-csbox' ); ?>
			<input type="text" name="phone" id="phone">
		</label>

		<input type="submit" class="button" value="<?php _e( 'Send', 'ilmenite-csbox' ); ?>">

		<?php wp_nonce_field( 'send_phone', 'ilcsb_phone' ); ?>

	</div>

</form>
