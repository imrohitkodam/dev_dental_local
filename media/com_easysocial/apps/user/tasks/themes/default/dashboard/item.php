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
<li class="taskItem all <?php echo $task->state == 2 ? ' is-resolved' : ' is-unresolved';?> task-<?php echo isset($task->cluster) && $task->cluster ? $task->cluster->getType() : 'user';?>" data-item data-id="<?php echo $task->id;?>">
	<div class="o-grid">
		<div class="o-grid__cell">
			<div class="o-checkbox t-lg-mt--no t-lg-mb--no">
				<input type="checkbox" id="task-<?php echo $task->id;?>" data-item-checkbox <?php echo $task->state == 2 ? 'checked="checked" ' : '';?>/>
				<label for="task-<?php echo $task->id;?>"><?php echo $task->get('title'); ?></label>
			</div>
		</div>

		<div class="o-grid__cell-auto-size task-stats">
			<span class="btn-group">
				<a href="javascript:void(0);" data-bs-toggle="dropdown" class="dropdown-toggle_ btn btn-es-default-o btn-xs">
					<i class="fa fa-caret-down"></i>
				</a>
				<ul class="dropdown-menu dropdown-menu-user messageDropDown">
					<li>
						<a href="javascript:void(0);" data-item-delete><?php echo JText::_( 'APP_USER_TASKS_DELETE_TASK' );?></a>
					</li>
				</ul>
			</span>

			<span class="o-label o-label--success-o">
				<?php echo JText::_('APP_USER_TASKS_RESOLVED'); ?>
			</span>
		</div>
	</div>

	<?php if (isset($task->cluster) && $task->cluster) { ?>
	<ul class="g-list-inline g-list-inline--dashed t-lg-ml--xl t-text--muted">

		<li>
			<i class="fa fa-<?php echo $task->cluster->getType() == 'event' ? 'calendar' : 'group';?>"></i>&nbsp; <?php echo $this->html('html.cluster', $task->cluster); ?>
		</li>		

		<?php if ($task->hasDueDate() && $task->due && $task->state != 2) { ?>
		<li>
			<?php echo JText::sprintf('APP_EVENT_TASKS_DUE_ON', ES::date($task->due)->format(JText::_('DATE_FORMAT_LC1'))); ?>
		</li>
		<?php } ?>

		<li class="t-text--muted">
			<?php echo ES::date($task->created)->toLapsed(); ?>
		</li>
	</ul>
	<?php } ?>
</li>
