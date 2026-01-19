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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_GROUP_FORM_GROUP_DETAILS'); ?>

			<div class="panel-body">
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_GROUP_TITLE', 'title'); ?>


					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'title', $group->getTitle(), 'title', array()); ?>
					</div>
				</div>
				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_PARENT', 'parent'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.lists', 'parent', $group->getParent(), 'parent', '', $parentSelection); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_GROUP_TEASER_TEXT', 'params[teasertext]'); ?>

					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[teasertext]', $params->get('teasertext', ''), 'params[teasertext]', array()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_GROUP_CHILD_PLANS', 'plans'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.plans', 'plans', $group->getPlans(), true, true, 'data-export-plans', [], ['theme' => 'fd']); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_PUBLISHED', 'published'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'published', $group->published, 'published', array()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_HIGHLIGHT_GROUP', 'params[planHighlighter]'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'params[planHighlighter]', $params->get('planHighlighter'), 'params[planHighlighter]', array()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_GROUP_VISIBLE', 'visible'); ?>

					<div class="flex-grow">
						<?php echo $this->html('form.toggler', 'visible', $group->getVisible(), 'visible'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_GROUP_FORM_GROUP_DESCRIPTION', 'description'); ?>

					<div class="flex-grow">
						<?php if ($renderEditor) { ?>
							<?php echo $this->html('form.editor', 'description', $group->getDescription(), 'description', ["data-pp-legacy-editor" => ''], [], [], false); ?>
						<?php } else { ?>
							<?php echo $this->fd->html('form.textarea', 'description', $group->getDescription(true), 'description', [], false); ?>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- right floater -->
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_PLAN_EDIT_BADGE_PARAMETER'); ?>
			<div class="panel-body">

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_APPLY_BADGE', 'params[badgeVisible]'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.toggler', 'params[badgeVisible]', $params->get('badgeVisible'), 'params[badgeVisible]', array()); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_POSITION', 'params[badgePosition]'); ?>
					<div class="flex-grow">
						<?php echo $this->html('form.lists', 'params[badgePosition]', $params->get('badgePosition'), 'params[badgePosition]', '', $badgePositions); ?>

					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_BADGE_TITLE', 'params[badgeTitle]'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.text', 'params[badgeTitle]', $params->get('badgeTitle', ''), 'params[badgeTitle]'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_BADGE_TEXT_COLOR', 'params[badgeTitleColor]'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.colorpicker', 'params[badgeTitleColor]', $params->get('badgeTitleColor', '#ffffff'), '#ffffff'); ?>
					</div>
				</div>

				<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md">
					<?php echo $this->fd->html('form.label', 'COM_PP_PLAN_EDIT_PLAN_BADGE_BADGE_BACKGROUND_COLOR', 'params[badgebackgroundcolor]'); ?>
					<div class="flex-grow">
						<?php echo $this->fd->html('form.colorpicker', 'params[badgebackgroundcolor]', $params->get('badgebackgroundcolor', '#707070'), '#707070'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
