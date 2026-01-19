PayPlans.require()
.script('site/card')
.done(function($) {
	var wrapper = $('[data-card-<?php echo $uuid;?>]');

	// Initialize card js
	wrapper.CardJs();
	<?php if ($inputNames->expireMonthYear == 'MM / YY') { ?>
		wrapper.data('cardjs').constructor.EXPIRY_MASK = 'XX / XX';
	<?php } ?>

	var cardInput = wrapper.find('input[name=<?php echo $inputNames->card;?>]');
	var codeInput = wrapper.find('input[name=<?php echo $inputNames->code;?>]');
	var expMonth = wrapper.find('input[name=<?php echo $inputNames->expireMonth;?>]');
	var expYear = wrapper.find('input[name=<?php echo $inputNames->expireYear;?>]');

	<?php if ($inputNames->name) { ?>
	<?php } ?>

	wrapper.CardJs('refresh');
	
	// cardInput.trigger('paste');
	// codeInput.trigger('paste');
});