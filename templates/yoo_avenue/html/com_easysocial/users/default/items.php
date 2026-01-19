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
<div class="es-users-list <?php echo !$users ? ' is-empty' : '';?>" data-es-users-result>
	<?php foreach ($users as $user) { ?>
		<?php echo $this->render('module', 'es-users-between-user'); ?>

		<div class="es-users-item" data-item data-id="<?php echo $user->id;?>">

			<div class="o-grid">
				<div class="o-grid__cell t-lg-pr--lg t-xs-pr--no t-xs-pb--md">
					<div class="o-flag">
						<div class="o-flag__image o-flag--top">
							<?php echo $this->html('avatar.user', $user); ?>
						</div>

						<div class="o-flag__body">
							<h1 class="es-users-item__title">
								<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
							</h1>
							<div class="es-user-meta">

								<?php if (!$this->isMobile() && !JFactory::getUser()->guest) { ?>
								<ol class="g-list-inline g-list-inline--delimited es-user-item-meta">
									<li data-breadcrumb=".">
										<span class="t-text--muted"><?php echo JText::sprintf('COM_EASYSOCIAL_MEMBERS_SINCE', $user->getRegistrationDate()->format('d M Y')); ?></span>
									</li>
									<li data-breadcrumb=".">
										<span class="t-text--muted">
											<?php if ($user->getLastVisitDate() == '0000-00-00 00:00:00') { ?>
												<?php echo JText::_('COM_EASYSOCIAL_USER_NEVER_LOGGED_IN'); ?>
											<?php } else { ?>
												<?php echo JText::sprintf('COM_EASYSOCIAL_LAST_LOGGED_IN', $user->getLastVisitDate('lapsed')); ?>
											<?php } ?>
										</span>
									</li>
								</ol>
								<?php } ?>

								<ol class="g-list-inline g-list-inline--delimited es-user-item-meta">

									<?php if ($this->config->get('friends.enabled') && !JFactory::getUser()->guest) { ?>
									<li>
										<a href="<?php echo ESR::friends(array('userid' => $user->getAlias()));?>" class="t-text--muted">
											<?php if ($user->getTotalFriends()) { ?>
												<?php echo $user->getTotalFriends();?> <?php echo JText::_(ES::string()->computeNoun('COM_EASYSOCIAL_FRIENDS', $user->getTotalFriends())); ?>
											<?php } else { ?>
												<?php echo JText::_('COM_EASYSOCIAL_NO_FRIENDS_YET'); ?>
											<?php } ?>
										</a>
									</li>
									<?php } ?>

									<?php if ($this->config->get('followers.enabled')  && !JFactory::getUser()->guest) { ?>
									<li data-breadcrumb="·">
										<a href="<?php echo ESR::followers(array('userid' => $user->getAlias()));?>" class="t-text--muted">

											<?php if ($user->getTotalFollowers()) { ?>
												<?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_FOLLOWERS', $user->getTotalFollowers()), $user->getTotalFollowers()); ?>
											<?php } else { ?>
												<?php echo JText::_('COM_EASYSOCIAL_NO_FOLLOWERS_YET'); ?>
											<?php } ?>
										</a>
									</li>
									<?php } ?>

									<?php if ($this->config->get('badges.enabled') && !JFactory::getUser()->guest) { ?>
									<li data-breadcrumb="·">
										<a href="<?php echo ESR::badges(array('userid' => $user->getAlias(), 'layout' => 'achievements'));?>" class="t-text--muted">
											<?php if ($user->getTotalbadges()){ ?>
												<?php echo $user->getTotalbadges();?> <?php echo JText::_(ES::string()->computeNoun('COM_EASYSOCIAL_BADGES', $user->getTotalbadges())); ?>
											<?php } else { ?>
												<?php echo JText::_('COM_EASYSOCIAL_NO_BADGES_YET'); ?>
											<?php } ?>
										</a>
									</li>
									<?php } ?>

									<?php if ($this->config->get('users.layout.gender', true) && $user->getFieldValue('GENDER') && !JFactory::getUser()->guest) { ?>
									<li data-breadcrumb="·">
										<?php echo $user->getFieldValue('GENDER')->toDisplay('listing', true); ?>
									</li>
									<?php } ?>

									<?php if (isset($displayOptions['showDistance']) && $displayOptions['showDistance']) { ?>
										<?php if ($user->getFieldValue($displayOptions['AddressCode'])) { ?>
										<li data-breadcrumb="·">
											<?php echo $user->getFieldValue($displayOptions['AddressCode'])->toDisplay(array('display' => 'distance', 'lat' => $displayOptions['AddressLat'], 'lon' => $displayOptions['AddressLon']), true); ?>
										</li>
										<?php } ?>
									<?php } ?>
								</ol>
							</div>
						</div>
					</div>
				</div>


				<?php if (ES::reports()->canReport() || $filter == 'list' || $filter == 'all') { ?>
				<div class="o-grid__cell o-grid__cell--auto-size">
					<div role="toolbar" class="btn-toolbar">

						<?php echo $this->html('user.friends', $user); ?>

						<?php echo $this->html('user.subscribe', $user); ?>

						<?php echo $this->html('user.conversation', $user); ?>

						<?php echo $this->html('user.actions', $user); ?>
					</div>
				</div>
				<?php } ?>
			</div>
		</div>

	<?php } ?>

	<?php echo $this->html('html.emptyBlock', 'COM_EASYSOCIAL_USERS_NO_USERS_HERE', 'fa-users'); ?>

	<?php if ($pagination) { ?>
	<div class="es-pagination-footer" data-es-users-pagination>
		<?php echo $pagination->getListFooter('site');?>
	</div>
	<?php } ?>
</div>
