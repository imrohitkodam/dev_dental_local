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
<div class="es-search-group__item o-grid">
	<div class="o-grid__cell">
		<div class="o-flag__image o-flag--top">
			<?php echo $this->html('avatar.user', $item); ?>
		</div>

		<div class="o-flag__body">
			<a href="<?php echo $item->getPermalink();?>"><?php echo $item->getName();?></a>

			<ul class="g-list-inline g-list-inline--dashed t-text--muted">
				<?php if ($this->config->get('friends.enabled')) { ?>
				<li class="item-friend">
					<a class="muted" href="<?php echo FRoute::friends( array( 'userid' => $item->getAlias() ) );?>"> <?php echo FD::get( 'Language', 'COM_EASYSOCIAL_FRIENDS' )->pluralize( $item->getTotalFriends() , true ); ?></a>
				</li>
				<?php } ?>

				<?php if (isset($displayOptions['showGender']) && $displayOptions['showGender']) { ?>
				<li class="item-friend">
					<?php $gender = $item->getFieldValue($displayOptions['GenderCode']); ?>
					<?php if ($gender) { ?>
					<?php echo $gender->toDisplay('listing', true); ?>
					<?php } ?>
				</li>
				<?php } ?>

				<li class="item-friend">
					<?php
						$tooltips = JText::sprintf('COM_EASYSOCIAL_USER_LISTING_LAST_LOGGED_IN_TOOLSTIPS', FD::date($item->lastvisitDate)->toLapsed());
						$showText = FD::date($item->lastvisitDate)->toLapsed();

						if ($item->lastvisitDate == '' || $item->lastvisitDate == '0000-00-00 00:00:00') {
							$tooltips = JText::_('COM_EASYSOCIAL_USER_LISTING_NEVER_LOGGED_IN');
							$showText = JText::_('COM_EASYSOCIAL_USER_LISTING_NEVER_LOGGED_IN');
						}
					?>
					<span class="item-meta" title="<?php echo $tooltips; ?>">
						<i class="fa fa-sign-in"></i>
						<?php echo $showText; ?>
					</span>
				</li>

				<li class="item-friend">
					<span class="item-meta" title="<?php echo JText::sprintf('COM_EASYSOCIAL_USER_LISTING_MEMBER_SINCE_TOOLSTIPS', FD::date($item->registerDate)->toFormat('d M Y')); ?>">
						<i class="fa fa-file-text-o"></i>
						<?php echo FD::date($item->registerDate)->toFormat('d M Y'); ?>
					</span>
				</li>

				<?php if (isset($displayOptions['showDistance']) && $displayOptions['showDistance']) { ?>
				<?php $address = $item->getFieldValue($displayOptions['AddressCode']); ?>
					<?php if ($address) { ?>
					<?php $displays = array('display' => 'distance', 'lat' => $displayOptions['AddressLat'], 'lon' => $displayOptions['AddressLon']); ?>
					<li class="item-friend"><?php echo $address->toDisplay($displays, true); ?></li>
					<?php } ?>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="o-grid__cell o-grid__cell--auto-size">
		<?php if ($item->hasCommunityAccess()) { ?>
			<?php echo $this->html('user.friends', $item); ?>

			<?php echo $this->html('user.subscribe', $item); ?>
		<?php } ?>
	</div>
</div>
