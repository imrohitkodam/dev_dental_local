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
<?php if ($active != 'complete') { ?>
<script type="text/javascript">
$(document).ready( function(){

	var previous = $('[data-installation-nav-prev]'),
		active = $('[data-installation-form-nav-active]'),
		nav = $('[data-installation-form-nav]'),
		retry = $('[data-installation-retry]'),
		cancel = $('[data-installation-nav-cancel]'),
		loading = $('[data-installation-loading]');

	previous.on('click', function() {
		active.val(<?php echo $active;?> - 2);

		nav.submit();
	});

	cancel.on('click', function() {
		window.location = '<?php echo JURI::base();?>index.php?option=com_payplans&cancelSetup=true';
	});

	retry.on('click', function() {
		var step = $(this).data('retry-step');

		$(this).addClass('d-none');

		loading.removeClass('d-none');

		window['pp']['installation'][step]();
	});
});
</script>

<form action="index.php?option=com_payplans" method="post" data-installation-form-nav class="d-none">
	<input type="hidden" name="active" value="" data-installation-form-nav-active />
	<input type="hidden" name="option" value="com_payplans" />
	<?php if ($reinstall) { ?>
	<input type="hidden" name="reinstall" value="1" />
	<?php } ?>

	<?php if ($update) { ?>
	<input type="hidden" name="update" value="1" />
	<?php } ?>
</form>


<a href="javascript:void(0);" class="btn btn-outline-secondary" <?php echo $active > 2 ? ' data-installation-nav-prev' : ' data-installation-nav-cancel';?>>
	<span>
		&#8592; &nbsp;
	</span>

	<?php if ($active > 2) { ?>
		<?php echo JText::_('COM_PP_INSTALLATION_PREVIOUS'); ?>
	<?php } else { ?>
		<?php echo JText::_('COM_PP_INSTALLATION_EXIT'); ?>
	<?php } ?>
</a>

<a href="javascript:void(0);" class="btn btn-primary ml-auto px-4"  data-installation-submit>
	<?php echo JText::_('Next'); ?>
	<span>
		&#8594; &nbsp;
	</span>
</a>

<a href="javascript:void(0);" class="btn btn-primary btn-loading loading d-none disabled ml-auto px-4"  data-installation-loading>
	<?php echo JText::_('COM_PP_INSTALLATION_LOADING'); ?>
	<span>
		<b class="ui loader"></b>
	</span>
</a>

<a href="javascript:void(0);" class="btn btn-primary ml-auto px-4 d-none" data-installation-install-addons>
	<?php echo JText::_('Next'); ?>

	<span>
		&#8594; &nbsp;
	</span>
</a>

<a href="javascript:void(0);" class="btn btn-primary d-none" data-installation-retry>
	<?php echo JText::_('COM_PP_INSTALLATION_RETRY'); ?>

	<span>
		&#8594; &nbsp;
	</span>
</a>
<?php } ?>
