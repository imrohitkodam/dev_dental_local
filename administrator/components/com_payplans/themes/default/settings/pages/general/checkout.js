PayPlans.ready(function($) {

	$('select[name=registrationType]').on('change', function() {
		var selected = $(this).val();

		$('[data-es-social]').toggleClass('t-hidden', selected != 'easysocial');
		$('[data-pp-auto]').toggleClass('t-hidden', selected != 'auto');
		$('[data-jom-social]').toggleClass('t-hidden', selected != 'jomsocial');

		var verification = $('select[name=account_verification]').val();
		var showAutologin = verification != 'auto' || selected != 'auto';

		$('[data-pp-autologin]').toggleClass('t-hidden', showAutologin);
	});

	$('select[name=account_verification]').on('change', function() {
		var selected = $(this).val();
		var autologin = false;

		if (selected != 'auto' && selected != 'active_subscription') {
			autologin = true;
		}

		var showVerificationMsg = selected == 'user' || selected == 'admin' ? false : true;

		var verificationWrapper = $('[data-pp-accountverification] [data-fd-alert-message]');
		var verificationMsg = verificationWrapper.html();

		if (selected == 'user') {
			var option1 = verificationMsg.replace('<b>administrator</b>', '<b>self</b>');
			verificationWrapper.html(option1);
		}

		if (selected == 'admin') {
			var option2 = verificationMsg.replace('<b>self</b>', '<b>administrator</b>');
			verificationWrapper.html(option2);			
		}

		$('[data-pp-autologin]').toggleClass('t-hidden', autologin);
		$('[data-pp-accountverification]').toggleClass('t-hidden', showVerificationMsg);
	});

	$('select[name=default_recaptcha_language]').on('change', function() {
		var selected = $(this).val();

		$('[data-recaptcha_language]').toggleClass('t-hidden', selected == 'auto');
	});
});