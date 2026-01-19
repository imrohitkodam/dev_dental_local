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
$micro = array('hour', 'minute', 'second');
?>

<div class="flex-grow space-y-sm min-w-0" style="width: 100%" data-timer-wrapper data-timer="<?php echo $name; ?>">
		
	<div class="o-input-group">
		<input class="o-form-control" placeholder="" value="<?php echo $displayTitle; ?>" disabled type="text" data-timer-label <?php echo $attributes;?> >
		<button class="o-btn o-btn--default-o t-hidden" type="button" data-timer-update-button><?php echo JText::_('COM_PP_UPDATE_BUTTON'); ?></button>
		<button class="o-btn o-btn--default-o" type="button" data-timer-edit-button><?php echo JText::_('COM_PP_EDIT_BUTTON'); ?></button>
	</div>

	<div class="flex-grow space-y-sm min-w-0 editable t-hidden" data-timer-edit-wrapper>
		<div class="flex gap-xs">
		<?php foreach ($segments as $key => $options) { ?>
			<?php if (in_array($key, $micro)) { continue; } ?>
			
			<div class="min-w-0 flex flex-col gap-xs">
				<span class="text-center">
					<?php echo JText::_('COM_PAYPLANS_TIMER_' . $key . 'S'); ?>
				</span>
				<div class="">
					<div class="o-select-group">
						<select class="o-form-control"
								data-timer-select data-key="<?php echo $key; ?>">
							<?php foreach ($options as $option) { ?>
								<option value="<?php echo $option->value; ?>"<?php echo ($option->selected) ? ' selected="selected"' : ''; ?>><?php echo $option->title; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>

		<div class="flex gap-xs <?php echo (!PP::config()->get('microsubscription')) ? 't-hidden': ''; ?>">
		<?php foreach ($segments as $key => $options) { ?>
			<?php if (!in_array($key, $micro)) { continue; } ?>

			<div class="min-w-0 flex flex-col gap-xs <?php echo $key == 'second' ? 't-hidden' : '';?>">
				<span class="text-center">
					<?php echo JText::_('COM_PAYPLANS_TIMER_' . $key . 'S'); ?>
				</span>
				<div class="">
					<div class="o-select-group">
						<select class="o-form-control"
								data-timer-select data-key="<?php echo $key; ?>">
							<?php foreach ($options as $option) { ?>
								<option value="<?php echo $option->value; ?>"<?php echo ($option->selected) ? ' selected="selected"' : ''; ?>><?php echo $option->title; ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
		<?php } ?>
		</div>
		<div class="o-form-helper-text text-xs">
			<i class="fdi fa fa-exclamation-circle"></i> <?php echo JText::_("COM_PAYPLANS_PLAN_LIFE_TIME_EXPIRATION_MSG"); ?>
		</div>
	</div>

	<input type="hidden" name="<?php echo $name; ?>" value="<?php echo $value; ?>" data-timer-hidden/>
</div>
