<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="app-tasks" data-es-tasks>

	<div class="app-contents<?php echo !$tasks ? ' is-empty' : '';?>" data-app-contents>
		<p><?php echo JText::_('APP_USER_TASKS_DASHBOARD_INFO'); ?></p>

		<div class="o-grid t-lg-mb--xl">
			<div class="o-grid__cell">
				<?php
					$sortOptions = array();
					$sortOptions[] = $this->html('form.popdownOption', 'all', 'APP_USER_TASKS_FILTER_ALL', '', false, array('data-tasks-filter', 'data-filter="all"'), '');
					$sortOptions[] = $this->html('form.popdownOption', 'task-user', 'APP_USER_TASKS_FILTER_USER', '', false, array('data-tasks-filter', 'data-filter="task-user"'), '');
					$sortOptions[] = $this->html('form.popdownOption', 'task-group', 'APP_USER_TASKS_FILTER_GROUPS', '', false, array('data-tasks-filter', 'data-filter="task-group"'), '');
					$sortOptions[] = $this->html('form.popdownOption', 'task-event', 'APP_USER_TASKS_FILTER_EVENTS', '', false, array('data-tasks-filter', 'data-filter="task-event"'), '');
					$sortOptions[] = $this->html('form.popdownOption', 'is-resolved', 'APP_USER_TASKS_FILTER_RESOLVED', '', false, array('data-tasks-filter', 'data-filter="is-resolved"'), '');
					$sortOptions[] = $this->html('form.popdownOption', 'is-unresolved', 'APP_USER_TASKS_FILTER_UNRESOLVED', '', false, array('data-tasks-filter', 'data-filter="is-unresolved"'), '');
				?>
				<?php echo $this->html('form.popdown', 'radius', 'all', $sortOptions, 'left'); ?>
			</div>
			<div class="o-grid__cell-auto-size">
				<a class="btn btn-es-primary btn-sm" href="javascript:void(0);" data-create><?php echo JText::_('APP_USER_TASKS_NEW_TASK_BUTTON'); ?></a>
			</div>
		</div>

		<div class="app-contents-data">
			<div class="form-item t-hidden" data-form>
				<div class="o-form-group">
					<div class="o-input-group">
						<input type="text" class="o-form-control" value="" placeholder="<?php echo JText::_('APP_USER_TASKS_PLACEHOLDER', true);?>" data-form-title />

						<span class="o-input-group__btn">
							<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-form-save>
								<i class="fa fa-check"></i>
							</a>

							<a href="javascript:void(0);" class="btn btn-es-danger-o btn-sm" data-form-cancel>
								<i class="fa fa-remove"></i>
							</a>
						</span>
					</div>
				</div>
			</div>

			<ul class="g-list-unstyled tasks-list mt-20 ml-0" data-lists>
				<?php if ($tasks) { ?>
					<?php foreach ($tasks as $task) { ?>
						<?php echo $this->loadTemplate('themes:/apps/user/tasks/dashboard/item', array('task' => $task)); ?>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>

		<?php echo $this->html('html.emptyBlock', 'APP_USER_TASKS_NO_TASKS_YET', 'fa-checkbox'); ?>
	</div>

</div>
