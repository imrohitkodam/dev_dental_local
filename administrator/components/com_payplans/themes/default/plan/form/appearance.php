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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_PLAN_EDIT_BADGE_PARAMETER'); ?>
			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_HIGHLIGHT', 'planHighlighter'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'planHighlighter', $plan->getPlanHighlighter(), 'planHighlighter', array()); ?>
					</div>
				</div>
				
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_APPLY_BADGE', 'planbadgeVisibleHighlighter'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'badgeVisible', $plan->getBadgeVisible(), 'planbadgeVisibleHighlighter', array()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_POSITION', 'badgePosition'); ?>
					<div class="flex-grow">
						<?php echo $this->html('form.lists', 'badgePosition', $plan->getBadgePosition(), 'badgePosition', '', $badgePositions); ?>

					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_BADGE_TITLE', 'badgeTitle'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'badgeTitle', $plan->getBadgeTitle(), 'badgeTitle'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_BADGE_TEXT_COLOR', 'badgeTitleColor'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.colorpicker', 'badgeTitleColor', $plan->getBadgeTitleColor(), '#ffffff'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_BADGE_BACKGROUND_COLOR', 'badgebackgroundcolor'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.colorpicker', 'badgebackgroundcolor', $plan->getBadgebackgroundcolor(), '#3498db'); ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</div>