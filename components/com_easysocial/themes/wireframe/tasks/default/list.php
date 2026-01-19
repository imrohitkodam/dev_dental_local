<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if ($milestones) { ?>
<ul class="milestone-list g-list-unstyled">
    <?php foreach ($milestones as $milestone) { ?>
    <li class="milestone-item <?php echo $milestone->isDue() ? 'is-due' : ''; ?> <?php echo $milestone->isCompleted() ? ' is-completed ' : ''; ?>" data-tasks-milestone-item data-id="<?php echo $milestone->id; ?>">
        <div class="milestone-title">
            <h4>
                <a href="<?php echo ESR::apps(array('layout' => 'canvas', 'customView' => 'item', 'uid' => $cluster->getAlias(), 'type' => $cluster->getType(), 'id' => $app->getAlias(), 'milestoneId' => $milestone->id), false); ?>">
                    <?php echo $milestone->get('title'); ?>
                </a>

                <span class="o-label o-label--danger-o due"><?php echo JText::_('APP_EVENT_TASKS_OVERDUE'); ?></span>
                <span class="o-label o-label--success-o completed"><?php echo JText::_('APP_EVENT_TASKS_COMPLETED'); ?></span>

                <?php if ($cluster->isAdmin()) { ?>
                <div class="btn-group t-lg-pull-right">
                    <button data-es-provide="tooltip" data-bs-toggle="dropdown" class="btn btn-es-default-o btn-xs dropdown-toggle_" type="button">
                        <i class="fa fa-caret-down"></i>
                    </button>

                    <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                            <a href="<?php echo ESR::apps(array('layout' => 'canvas', 'customView' => 'form', 'uid' => $cluster->getAlias(), 'type' => $cluster->getType(), 'id' => $app->getAlias(), 'milestoneId' => $milestone->id), false); ?>"><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_EDIT'); ?></a>
                        </li>
                        <li class="divider"></li>

                        <li class="mark-uncomplete">
                            <a href="javascript:void(0);" data-milestone-task="unresolve"><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_MARK_INCOMPLETE'); ?></a>
                        </li>
                        <li class="mark-completed">
                            <a href="javascript:void(0);" data-milestone-task="resolve"><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_MARK_COMPLETE'); ?></a>
                        </li>

                        <li class="divider"></li>
                        <li>
                            <a href="javascript:void(0);" data-milestone-delete><?php echo JText::_('APP_EVENT_TASKS_MILESTONE_DELETE'); ?></a>
                        </li>
                    </ul>
                </div>
                <?php } ?>
            </h4>
        </div>
        
        <div class="milestone-meta">
            <ul class="g-list-inline g-list-inline--space-right t-text--muted">
                <li>
                    <i class="fa fa-user"></i>&nbsp; <?php echo $milestone->getOwner()->getName();?>
                </li>

                <li>
                    <i class="fa fa-calendar"></i>&nbsp; <?php echo $milestone->getCreatedDate()->format(JText::_('DATE_FORMAT_LC1')); ?>
                </li>

                <li>
                    <i class="fa fa-tasks"></i>&nbsp; <?php echo JText::sprintf(ES::string()->computeNoun('APP_EVENT_TASKS_TOTAL_TASKS', $milestone->getTotalTasks()), $milestone->getTotalTasks()); ?>
                </li>

                <?php if ($milestone->hasDueDate()) { ?>
                <li>
                    <i class="fa fa-calendar"></i>&nbsp; <?php echo JText::sprintf('APP_EVENT_TASKS_META_DUE_ON', ES::date($milestone->due)->format(JText::_('DATE_FORMAT_LC1'))); ?></i>
                </li>
                <?php } ?>

                <?php if ($milestone->hasAssignee()) { ?>
                <li>
                    <i class="fa fa-user"></i>&nbsp; <?php echo JText::sprintf('APP_EVENT_TASKS_MILESTONE_IS_RESPONSIBLE', $this->html('html.user', $milestone->getAssignee()->id, true)); ?></a>
                </li>
                <?php } ?>
            </ul>
        </div>

        <div class="milestone-desc t-lg-mt--md">
            <?php echo $this->html('string.truncate', $milestone->getContent(), 300); ?>
        </div>
    </li>
    <?php } ?>
</ul>
<?php } ?>