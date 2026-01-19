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
<div class="es-snackbar">
	<h1 class="es-snackbar__title"><?php echo JText::_($heading);?></h1>
</div>

<div class="es-cards es-cards--<?php echo $showSidebar ? '2' : '3';?>" >
	<?php if ($groups) { ?>
		<?php foreach ($groups as $group) { ?>
		<div class="es-cards__item">
			<div class="es-card <?php echo $group->isFeatured() ? ' is-featured' : '';?>">
				<div class="es-card__hd">
					<?php if ($group->canAccessActionMenu()) { ?>
						<div class="es-card__admin-action">
							<div class="pull-right dropdown_">
								<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
									<i class="fa fa-ellipsis-h"></i>
								</a>
								
								<ul class="dropdown-menu">
									<?php echo $this->html('group.adminActions', $group); ?>

									<?php if ($this->html('group.report', $group)) { ?>
									<li>
										<?php echo $this->html('group.report', $group); ?>
									</li>
									<?php } ?>
								</ul>
							</div>
						</div>
					<?php } ?>

					<?php echo $this->html('card.cover', $group); ?>
				</div>

				<div class="es-card__bd es-card--border">
					<?php echo $this->html('card.avatar', $group); ?>

					<?php echo $this->html('card.icon', 'featured', 'COM_EASYSOCIAL_GROUPS_FEATURED_GROUPS'); ?>

					<?php echo $this->html('card.title', $group->getTitle(), $group->getPermalink()); ?>

					<div class="es-card__meta t-lg-mb--sm">
						<ol class="g-list-inline g-list-inline--delimited">
							<li>
								<?php echo $this->html('group.type', $group); ?>
							</li>

							<li>
								<i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $group->getCategory()->getPermalink();?>"><?php echo $group->getCategory()->getTitle();?></a>
							</li>
						</ol>
					</div>

					<?php if ($this->config->get('groups.layout.description')) { ?>
					<div class="es-card__desc">
						<?php if ($group->description) { ?>
							<?php echo $this->html('string.truncate', $group->getDescription(), 200, '', false, false, false, true);?>
						<?php } else { ?>
							<?php echo JText::_('COM_EASYSOCIAL_GROUPS_NO_DESCRIPTION_YET'); ?>
						<?php }?>
					</div>
					<?php } ?>
				</div>

				<div class="es-card__ft es-card--border">
					<div class="es-card__meta">
						<ol class="g-list-inline g-list-inline--delimited">

							<?php if (!$this->isMobile()) { ?>
							<li>
								<i class="fa fa-user"></i>&nbsp; <a href="<?php echo $group->getCreator()->getPermalink();?>"><?php echo $group->getCreator()->getName();?></a>
							</li>
							<?php } ?>

							<li>
								<a href="<?php echo $group->getAppPermalink('members');?>" data-es-provide="tooltip"
									data-original-title="<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_MEMBERS', $group->getTotalMembers()), $group->getTotalMembers() ); ?>"
								>
									<i class="fa fa-users"></i>&nbsp; <span data-group-join-count-<?php echo $group->id; ?> ><?php echo $group->getTotalMembers();?></span>
								</a>
							</li>

							<li class="pull-right">
								<?php echo $this->html('group.action', $group); ?>
							</li>
						</ol>
					</div>
			   </div>
			</div>
		</div>
		<?php } ?>
	<?php } ?>
</div>

<?php if (isset($featured) && $featured) { ?>
<div>
	<a href="<?php echo ESR::groups(array('filter' => 'featured')); ?>" class="btn btn-es-default-o btn-block"><?php echo JText::_('COM_EASYSOCIAL_VIEW_ALL'); ?></a>
</div>

<hr class="es-hr">
<?php } ?>

<?php if ($pagination) { ?>
	<?php echo $pagination->getListFooter('site');?>
<?php } ?>