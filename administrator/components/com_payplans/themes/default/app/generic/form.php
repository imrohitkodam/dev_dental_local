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
<div class="panel">
	<?php echo $this->fd->html('panel.heading', 'COM_PP_APP_GENERAL'); ?>

	<div class="panel-body">
		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
			<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_TITLE', 'title'); ?>

			<div class="flex-grow">
				<?php echo $this->fd->html('form.text', 'title', $app->getTitle()); ?>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
			<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_PUBLISH_STATE', 'published'); ?>

			<div class="flex-grow">
				<?php echo $this->fd->html('form.toggler', 'published', $app->getPublished()); ?>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
			<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_APPLY_ON_ALL_PLANS', 'core_params[applyAll]'); ?>

			<div class="flex-grow">
				<?php echo $this->fd->html('form.toggler', 'core_params[applyAll]', $app->getId() ? $app->getApplyAll() : true, 'core_params[applyAll]', '', [
					'dependency' => '[data-app-selected-plans]', 
					'dependencyValue' => 0
				]); ?>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo $app->getApplyAll() || !$app->getId() ? 't-hidden' : ''; ?>" data-app-selected-plans>
			<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_APPLY_ON_SELECTED_PLANS', 'appplans'); ?>

			<div class="flex-grow">
				<?php echo $this->html('form.plans', 'appplans', $app->getPlans(), true, true, 'data-plans-input', [], ['theme' => 'fd']); ?>
			</div>
		</div>

		<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md">
			<?php echo $this->fd->html('form.label', 'COM_PP_APP_GENERAL_DESCRIPTION', 'description'); ?>

			<div class="flex-grow">
				<?php echo $this->fd->html('form.textarea', 'description', $app->getDescription(), '', ['rows' => 5]); ?>
			</div>
		</div>
	</div>

</div>