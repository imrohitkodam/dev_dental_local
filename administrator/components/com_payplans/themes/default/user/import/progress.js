PayPlans.ready(function($) {

	start();

	function start() {

		// activate loading icon
		$('[data-user-progress-loading]').removeClass('hide');

		// Update the buttons message
		$('[data-user-progress-loading]').html(' <i class="fdi fas fa-spinner fa-pulse"></i>');

		PayPlans.ajax('admin/views/user/importUserSubscription', {
			plan_id: '<?php echo $plan->getId(); ?>',
			totalRecords: '<?php echo $totalRecords; ?>',
			importSubscriptionStatus: '<?php echo $importSubscriptionStatus; ?>',
			importSubscriptionStartDate: '<?php echo $importSubscriptionStartDate; ?>',
			importSubscriptionExpirationDate: '<?php echo $importSubscriptionExpirationDate; ?>',
			importSubscriptionNote: '<?php echo $importSubscriptionNote; ?>',
			limit: 5
		},
		{
			append: function(selector, message) {
				$(selector).append(message);
			}

		}).done(function(hasMore, progressPercentage) {

			// Hide the no progress message
			$('[data-user-progress-empty]').addClass('hide');

			percentage = progressPercentage + '%';

			// Update progress bar
			$('[data-user-progress-percentage]').html(percentage);
			$('[data-user-progress-bar]').css('width', percentage);


			// If there's still items to render, run a recursive loop until it doesn't have any more items;
			if (hasMore == true) {
				start();
				return;
			}

			// remove loading icon.
			$('[data-user-progress-loading]').addClass('hide');

			// update the progress bar status to complete
			$('[data-user-progress-bar-status]').html('<?php echo JText::_('COM_PP_USER_IMPORT_COMPLETED', true);?>');

			// Activate the back button after complete the import user process
			$('[data-user-progress-back]').removeClass('hide');
			
			if (hasMore == 'noitem') {
				$('[data-user-progress-status]').html('<?php echo JText::_('COM_PP_USER_IMPORT_NO_ITEM', true);?>');
			}
		});
	}
});
