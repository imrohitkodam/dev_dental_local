PayPlans.ready(function($) {
	<?php if ($from === 'popup' && $selectedFromPopup) { ?>
		const item = $('[data-plans-item][data-plan-id="' + <?php echo $selectedFromPopup; ?> + '"]').find('[data-plans-item-card]');

		// Paused around 1 second first in order to scroll nicely
		setTimeout(function(){
			// Highlight the selected plan for a while to acknowledge the user
			item.addClass('is-growing');

			// Scroll back to the position of the item to show it to the user
			item[0].scrollIntoView({
				behavior: 'smooth',
				block: 'center'
			});
		},1000);

		setTimeout(function(){
			item.removeClass('is-growing');
		},6000);
	<?php } ?>
});