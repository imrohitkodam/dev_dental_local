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
<?php echo $this->html('responsive.toggle'); ?>
<div class="es-container" data-es-users data-es-container>
	<div class="es-sidebar" data-sidebar>
		<?php echo $this->render('module', 'es-users-sidebar-top', 'site/dashboard/sidebar.module.wrapper'); ?>

		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_USERS'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<li class="o-tabs__item <?php echo !$filter || $filter == 'all' ? 'active' : '';?>" data-filter-item data-type="users" data-id="all">
						<a href="<?php echo ESR::users();?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_USERS_FILTER_USERS_ALL_USERS');?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>

					<li class="o-tabs__item <?php echo $filter == 'photos' ? 'active' : '';?>" data-filter-item data-type="users" data-id="photos">
						<a href="<?php echo ESR::users(array('filter' => 'photos'));?>" class="o-tabs__link" title="<?php echo JText::_('COM_EASYSOCIAL_PAGE_TITLE_USERS_WITH_PHOTOS', true);?>">
							<?php echo JText::_('COM_EASYSOCIAL_USERS_FILTER_USERS_WITH_PHOTOS');?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php if (!JFactory::getUser()->guest) { ?>
					<li class="o-tabs__item <?php echo $filter == 'online' ? 'active' : '';?>" data-filter-item data-type="users" data-id="online">
						<a href="<?php echo ESR::users(array('filter' => 'online'));?>" class="o-tabs__link" title="<?php echo JText::_( 'COM_EASYSOCIAL_PAGE_TITLE_USERS_ONLINE_USERS' , true );?>">
							<?php echo JText::_('COM_EASYSOCIAL_USERS_FILTER_ONLINE_USERS');?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>

		<?php if ($searchFilters) { ?>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_USERS_BROWSE_BY_FILTERS'); ?>
		
			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<?php foreach ($searchFilters as $filter) { ?>
					<li class="o-tabs__item filter-item<?php echo $filter == 'search' && $fid == $filter->id ? ' active' : '';?>" data-filter-item data-type="search" data-id="<?php echo $filter->id;?>">
						<a href="<?php echo ESR::users(array('filter' => 'search', 'id' => $filter->getAlias()));?>" class="o-tabs__link" title="<?php echo $filter->get('title'); ?>">
							<?php echo $this->html('string.escape', $filter->get('title')); ?>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>

		<?php if ($profiles && !JFactory::getUser()->guest) { ?>
		<div class="es-side-widget">
			<?php echo $this->html('widget.title', 'COM_EASYSOCIAL_USERS_BROWSE_BY_PROFILES'); ?>

			<div class="es-side-widget__bd">
				<ul class="o-tabs o-tabs--stacked">
					<?php foreach ($profiles as $profile) { ?>
					<li class="o-tabs__item has-notice filter-item<?php echo $filter == 'profiletype' && $activeProfile->id == $profile->id ? ' active' : '';?>" data-filter-item data-type="profiles" data-id="<?php echo $profile->id;?>">
						<a href="<?php echo ESR::users(array('filter' => 'profiletype', 'id' => $profile->getAlias()));?>" class="o-tabs__link">
							<?php echo $profile->get('title'); ?>
							<div class="o-tabs__bubble"><?php echo $profile->totalUsers;?></div>
						</a>
						<div class="o-loader o-loader--sm"></div>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
		<?php } ?>
		
		<?php echo $this->render('module', 'es-users-sidebar-bottom', 'site/dashboard/sidebar.module.wrapper'); ?>

	</div>

	<div class="es-content">
		<?php echo $this->render('module', 'es-users-before-contents'); ?>

		<div data-contents>
			<?php echo $this->includeTemplate('site/users/default/wrapper'); ?>
			<?php echo $this->html('html.loading'); ?>
		</div>

		<?php echo $this->render('module', 'es-users-after-contents'); ?>
	</div>


</div>
