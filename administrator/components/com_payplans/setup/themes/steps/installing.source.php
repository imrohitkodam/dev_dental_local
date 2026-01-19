<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php?option=com_payplans" method="post" name="installation" data-form>

	<div class="pp-container-overflow mb-4 pr-3" style="height: 5vh">
		<ol class="pp-install-logs" data-logs>
			<?php if (PP_INSTALLER == 'launcher') { ?>
			<li class="pp-install-logs__item is-loading" data-log-checklicense>
				<div class="pp-install-logs__title">
					Checking for a valid license...
				</div>

				<?php include(__DIR__ . '/log.state.php'); ?>

				<input type="hidden" name="method" value="launcher" />
				<input type="hidden" value="<?php echo PP_KEY;?>" name="apikey" class="hidden" data-api-key />
			</li>
			<?php } ?>

			<?php if (PP_INSTALLER == 'full' || PP_BETA) { ?>
				<input type="hidden" name="method" value="full" />
			<?php } ?>
		</ol>
	</div>

	<div class="pp-alert pp-alert--danger d-none" role="alert" data-source-errors data-api-errors>
		<p data-error-message style="margin-bottom: 15px;"><?php echo JText::_('COM_PP_INSTALLATION_METHOD_API_KEY_INVALID'); ?></p>
		<br />
		<center>
			<a href="https://stackideas.com/forums" class="btn btn-danger" target="_blank"><?php echo JText::_('COM_PP_INSTALLATION_CONTACT_SUPPORT');?></a>
		</center>
	</div>

	<div class="form-inline d-none" data-licenses>
		<div>
			<h2><?php echo JText::_('Please Select A License');?></h2>
			<p><?php echo JText::_('Multiple licenses detected from your account. Please select a license to associate with your installation.');?></p>
			
			<div data-licenses-placeholder></div>
		</div>
	</div>

	<input type="hidden" name="option" value="com_payplans" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="update" value="<?php echo $update;?>" />
</form>

<script type="text/javascript">
$(document).ready(function() {
	var form = $('[data-form]');

	<?php if (PP_INSTALLER == 'full') { ?>
		form.submit();
	<?php } ?>

	<?php if (PP_INSTALLER == 'launcher') { ?>
	var log = $('[data-log-checklicense]');

	// Hide submit button
	var submitButton = $('[data-installation-submit]');
	
	submitButton.addClass('d-none');

	// Validate api key
	$.ajax({
		type: 'POST',
		url: '<?php echo JURI::root();?>administrator/index.php?option=com_payplans&ajax=1&controller=license&task=verify',
		data: {
			"key": $('[data-api-key]').val()
		}
	}).done(function(result) {

		// Hide the loading
		log.removeClass('is-loading');

		// User is not allowed to install
		if (result.state == 400) {
			log.addClass('is-error');

			$('[data-api-errors]').removeClass('d-none');
			$('[data-error-message]').html(result.message);
			
			return false;
		}

		// Valid licenses
		if (result.state == 200) {
			log.addClass('is-complete');

			var licenses = $('[data-licenses]');
			var licensePlaceholder = $('[data-licenses-placeholder]');

			// If there are multiple licenses, we need to request them to submit
			if (result.licenses.length > 1) {
				submitButton.removeClass('d-none');

				licenses.removeClass('d-none');

				var output = $('<div>').html(result.html);
				output.find('select')
					.css('font-size', '14px')
					.css('padding', '6px')
					.css('width', '100%');

				licensePlaceholder.append(output);

				// Change the behavior of form submission
				submitButton.on('click', function() {
					form.submit();
				});
				return;
			}

			// If the user only has 1 license, just submit this immediately.
			licensePlaceholder.append(result.html);
			form.submit();
		}
	});
	<?php } ?>
});
</script>
