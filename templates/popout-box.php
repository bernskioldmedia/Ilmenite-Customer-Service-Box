<?php
/**
 * Popout Box HTML Code
 */
?>
<div class="cs-box-wrapper">
	<a href="#" class="cs-box-trigger">
		<?php _e( 'Contact Us', 'ilcsb' ); ?>
	</a>
	<div class="cs-box">

		<div class="cs-box-title">
			<?php _e( 'How can we help you?', 'ilcsb' ); ?>
		</div>

		<p class="cs-box-intro"><?php _e( 'Choose one of the following ways to get in touch with us.', 'ilcsb' ); ?></p>

		<?php
		$contact_methods = Ilmenite_CSBox()->get_contact_methods();

		if ( $contact_methods ) : ?>

			<ul class="cs-box-method-list">

				<?php foreach ( $contact_methods as $type => $val ) : ?>

					<li class="cs-box-method-list-item cs-method-<?php echo $type; ?> <?php echo ( $val['content'] ? 'has-content' : '' ); ?>">
						<a href="<?php echo $val['link']; ?>">

							<?php if ( $val['icon'] ) : ?>
								<span class="icon"></span>
							<?php endif; ?>

							<span class="value"><?php echo $val['value']; ?></span>

							<?php if ( $val['status'] ) : ?>

								<?php
								if ( 'phone_in' == $type ) {
									$status = Ilmenite_CSBox()->is_phone_open();

									if ( $status ) {
										$status_text = 'open';
									} else {
										$status_text = 'closed';
									}
								} else {
									$status_text = 'open';
								}
								?>

								<span class="status status-<?php echo $status_text; ?>"></span>
							<?php endif; ?>

						</a>

						<?php if ( $val['content'] ) : ?>
							<div class="cs-method-content">
								<?php echo $val['content']; ?>
							</div>
						<?php endif; ?>

					</li>

				<?php endforeach; ?>

			</ul>

		<?php endif; ?>

	</div>
</div>