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
<div class="es-users-item" data-item data-id="<?php echo $user->id;?>">
	<div class="o-grid">
		<div class="o-grid__cell">
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<?php echo $this->html('avatar.user', $user); ?>
				</div>

				<div class="o-flag__body">
					<a href="<?php echo $user->getPermalink();?>" class="es-user-name"><?php echo $user->getName();?></a>
					<div class="es-user-meta">

						<ol class="g-list-inline g-list-inline--delimited es-user-item-meta">
							<li>
								<a href="<?php echo ESR::friends(array('userid' => $user->getAlias()));?>" class="t-text--muted">
									<?php if ($user->getTotalFriends()) { ?>
										<?php echo $user->getTotalFriends();?> <?php echo JText::_(ES::string()->computeNoun('COM_EASYSOCIAL_FRIENDS', $user->getTotalFriends())); ?>
									<?php } else { ?>
										<?php echo JText::_('COM_EASYSOCIAL_NO_FRIENDS_YET'); ?>
									<?php } ?>
								</a>
							</li>

							<?php if ($this->config->get('followers.enabled')) { ?>
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

							<?php if ($this->config->get('badges.enabled')) { ?>
							<li data-breadcrumb="·">
								<a href="<?php echo ESR::badges(array('userid' => $user->getAlias(), 'layout' => 'achievements'));?>" class="t-text--muted">
									<?php if( $user->getTotalbadges() ){ ?>
										<?php echo $user->getTotalbadges();?> <?php echo JText::_(ES::string()->computeNoun('COM_EASYSOCIAL_BADGES', $user->getTotalbadges())); ?>
									<?php } else { ?>
										<?php echo JText::_('COM_EASYSOCIAL_NO_BADGES_YET'); ?>
									<?php } ?>
								</a>
							</li>
							<?php } ?>

						</ol>
					</div>
				</div>
			</div>        
		</div>

		<div class="o-grid__cell o-grid__cell--auto-size">
			<div role="toolbar" class="btn-toolbar t-lg-mt--sm">
				<?php if ($filter == 'list') { ?>
					<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-remove-from-list><?php echo JText::_('COM_EASYSOCIAL_FRIENDS_REMOVE_FROM_LIST');?></a>
				<?php } ?>

				<?php echo $this->html('user.friends', $user); ?>

				<?php echo $this->html('user.conversation', $user); ?>

				<?php echo $this->html('user.actions', $user); ?>
			</div>
		</div>
	</div>
</div>
