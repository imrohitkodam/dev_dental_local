<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="es" class="mod-es mod-es-sidebar-discuss <?php echo $moduleLib->getSuffix();?>">
	<div class="es-sidebar" data-sidebar>
		<?php if ($canCreateDiscussion) { ?>
			<a class="btn btn-es-primary btn-block t-lg-mb--xl" href="<?php echo $composeLink;?>"><?php echo JText::_('APP_EASYDISCUSS_CREATE_DISCUSSION'); ?></a>
		<?php } ?>
	</div>

	<div class="es-side-widget">
		<?php echo $this->html('widget.title', 'COM_ES_FILTERS'); ?>

		<div class="es-side-widget__bd">
			<ul class="o-tabs o-tabs--stacked feed-items">
				
				<li class="o-tabs__item has-notice<?php echo $filter == 'userposts' ? 'active' : '';?>" data-discuss-filter="userposts">
					<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_ALL');?></a>
					<div class="o-tabs__bubble"><?php echo $counters['all'];?></div>
				</li>

				<?php if ($config->get('main_qna') && $config->get('layout_enablefilter_unanswered')) { ?>
				<li class="o-tabs__item has-notice<?php echo $filter == 'unanswered' ? 'active' : '';?>" data-discuss-filter="unanswered">
					<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_UNANSWERED');?></a>
					<div class="o-tabs__bubble"><?php echo $counters['unanswer'];?></div>
				</li>
				<?php } ?>

				<?php if ($config->get('main_qna') && $config->get('layout_enablefilter_resolved')) { ?>
				<li class="o-tabs__item has-notice<?php echo $filter == 'resolved' ? 'active' : '';?>" data-discuss-filter="resolved">
					<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_RESOLVED');?></a>
					<div class="o-tabs__bubble"><?php echo $counters['resolved'];?></div>
				</li>
				<?php } ?>
				
				<?php if ($config->get('main_qna') && $config->get('layout_enablefilter_unresolved')) { ?>
				<li class="o-tabs__item has-notice<?php echo $filter == 'unresolved' ? 'active' : '';?>" data-discuss-filter="unresolved">
					<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_UNRESOLVED');?></a>
					<div class="o-tabs__bubble"><?php echo $counters['unresolved'];?></div>
				</li>
				<?php } ?>
				
				<li class="o-tabs__item has-notice<?php echo $filter == 'userreplies' ? 'active' : '';?>" data-discuss-filter="userreplies">
					<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_REPLIES');?></a>
					<div class="o-tabs__bubble"><?php echo $counters['replies'];?></div>
				</li>

				<?php if ($config->get('main_qna') && (ED::ismoderator() || (ED::isMine($user->id)))) { ?>
				<li class="o-tabs__item has-notice<?php echo $filter == 'pending' ? ' active' : '';?>" data-discuss-filter="pending">
					<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('APP_EASYDISCUSS_DISCUSSIONS_FILTER_PENDING');?></a>
					<div class="o-tabs__bubble"><?php echo $counters['pending'];?></div>
				</li>
				<?php } ?>			
			</ul>
		</div>
	</div>
</div>