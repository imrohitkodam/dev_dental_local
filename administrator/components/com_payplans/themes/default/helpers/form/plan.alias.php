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
<div>
	<?php if ($totalValues > 0) { ?>
		<div class="flex-grow space-y-md" data-plan-listing>
		<?php foreach ($value as $key => $item) { ?>
		
			<div class="grid grid-cols-2 gap-xs" data-plan-item>
				<div class="">
					<div class="o-select-group">
						<select class="o-form-control" name="<?php echo $name;?>[<?php echo $key;?>][]">
							<option value=""><?php echo JText::_('COM_PAYPLANS_PLAN_SELECT');?></option>
							<?php foreach ($plans as $plan) { ?>
							<option value="<?php echo $plan->plan_id;?>" <?php echo $plan->plan_id == $value[$key][0] ? ' selected="selected"' : '';?>><?php echo $plan->title;?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="">
					<div class="o-input-group">
						<input class="o-form-control" type="text" name="<?php echo $name;?>[<?php echo $key;?>][]" value="<?php echo isset($value[$key][1]) ? $value[$key][1] : '';?>" />

						<?php if ($key > 0) { ?>
							<button type="button" data-fd-toggle="tooltip" data-fd-template="o-tooltip" data-fd-placement="right" class="o-btn o-btn--default-o" data-remove-row>
								<i class="fdi fa fa-times"></i>
							</button>
						<?php } else { ?>
							<button type="button" data-fd-toggle="tooltip" data-fd-template="o-tooltip" data-fd-placement="right" class="o-btn o-btn--default-o" data-insert-row>
								<i class="fdi fa fa-plus"></i>
							</button>
						<?php } ?>
					</div>
				</div>
			</div>
		
		<?php } ?>
		</div>
	<?php } else { ?>
		<div class="flex-grow space-y-md" data-plan-listing>
			<div class="grid grid-cols-2 gap-xs">
				<div class="">
					<div class="o-select-group">
						<select class="o-form-control" name="<?php echo $name;?>[0][]">
							<option value="" selected="selected"><?php echo JText::_('COM_PAYPLANS_PLAN_SELECT');?></option>
							<?php foreach ($plans as $plan) { ?>
							<option value="<?php echo $plan->plan_id;?>"><?php echo $plan->title;?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="">
					<div class="o-input-group">
						<input type="text" name="<?php echo $name;?>[0][]" class="o-form-control" value="" />

						<button type="button" data-fd-toggle="tooltip" data-fd-template="o-tooltip" data-fd-placement="right" class="o-btn o-btn--default-o" data-insert-row>
							<i class="fdi fa fa-plus"></i>
						</button>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</div>

<div class="t-hidden grid grid-cols-2 gap-xs" data-plan-template data-plan-item>
	<div class="">
		<div class="o-select-group">
			<select class="o-form-control" name="">
				<option value="" selected="selected"><?php echo JText::_('COM_PAYPLANS_PLAN_SELECT');?></option>
				<?php foreach ($plans as $plan) { ?>
				<option value="<?php echo $plan->plan_id;?>"><?php echo $plan->title;?></option>
				<?php } ?>
			</select>
			<label for="" class="o-select-group__drop"></label>
		</div>
	</div>
	<div class="">
		<div class="o-input-group">
			<input type="text" name="" class="o-form-control" />

			<button type="button" data-fd-toggle="tooltip" data-fd-template="o-tooltip" data-fd-placement="right" class="o-btn o-btn--danger-o" data-remove-row>
				<i class="fdi fa fa-times"></i>
			</button>
		</div>
	</div>
</div>