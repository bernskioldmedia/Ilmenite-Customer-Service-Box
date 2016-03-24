/**
 * Ilmenite Customer Service Box
 */
jQuery(function($) {

	var boxTrigger = $('.cs-box-trigger');
	var boxWrapper = $('.cs-box-wrapper');
	var methodItem = $('.cs-box-method-list-item');

	// Open/Close the Box
	$(boxTrigger).click(function(e) {
		e.preventDefault();

		$(boxWrapper).toggleClass('is-active');
	});

	// Open/close expanded content
	$(methodItem).each(function() {

		var _this = $(this);

		if($(_this).hasClass('has-content')) {
			$(_this).find('a').click(function(e) {
				e.preventDefault();

				$(_this).find('.cs-method-content').slideToggle();
			});
		}
	});

	// Process the phone number form
	$('form.cs-box-leave-phone').submit(function(e) {

		var _this = $(this);

		e.preventDefault();

		var formMessage = $('.cs-box-leave-phone').find('.cs-box-form-message');

		// Get input name value
		var name = $('.cs-box-leave-phone').find('#name').val();

		// Get input phone value
		var phone = $('.cs-box-leave-phone').find('#phone').val();

		// Get the nonce
		var nonce = $('.cs-box-leave-phone').find('#ilcsb_phone').val();

		var data = {
			'action' : 'ilcsb_phone_form',
			'name' : name,
			'phone' : phone,
			'nonce' : nonce
		};

		$.post(
			ilcsb.ajaxurl,
			data,
			function(response) {
				$(formMessage).hide();
				$(formMessage).html(response);
				$(formMessage).show();
			}
		);

	});

});