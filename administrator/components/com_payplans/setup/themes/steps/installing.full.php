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
<form name="installation" method="post" data-installation-form>
	<div class="mb-1">
		<?php echo JText::_('COM_PP_INSTALLATION_INSTALLING_DESC');?>
	</div>

	<div class="pp-alert pp-alert--success d-none mt-4 mb-4" role="alert" data-installation-completed>
		<?php echo JText::_('COM_PP_INSTALLATION_INSTALLING_COMPLETED'); ?>
	</div>

	<div class="pp-container-overflow mt-4 mb-4 pr-3" style="height: 35vh;max-height: 35vh;" data-install-progress>
		<ol class="pp-install-logs mt-4" data-progress-logs>
			<li class="pp-install-logs__item" data-progress-extract>
				<div class="pp-install-logs__title">
					<?php echo JText::_('COM_PP_INSTALLATION_INSTALLING_EXTRACTING_FILES');?>
				</div>

				<?php include(__DIR__ . '/log.state.php'); ?>
			</li>

			<?php include(dirname(__FILE__) . '/installing.steps.php'); ?>
		</ol>
	</div>


	<input type="hidden" name="option" value="com_payplans" />
	<input type="hidden" name="active" value="<?php echo $active; ?>" />
	<input type="hidden" name="source" data-source />

	<?php if ($reinstall) { ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if ($update) { ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>

</form>

<script type="text/javascript">
$(document).ready(function(){

	<?php if ($reinstall) { ?>
		pp.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=com_payplans&ajax=1&reinstall=1";
	<?php } ?>

	<?php if ($update) { ?>
		pp.ajaxUrl = "<?php echo JURI::root();?>administrator/index.php?option=com_payplans&ajax=1&update=1";
	<?php } ?>

	// Immediately proceed with installation
	pp.installation.extract();
});

</script>

