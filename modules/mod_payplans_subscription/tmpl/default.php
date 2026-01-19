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
<?php if ($subscriptions) { ?>
	<div id="fd">
		<div class="o-card bg-white">
			<div class="o-card__body">
				<div class="space-y-md">
					<?php foreach ($subscriptions as $subscription) { ?>
						<?php $subscription = PP::subscription($subscription); ?>

						<div class="flex items-center hover:bg-gray-100 px-xs py-md rounded-md">
							<div class="flex-grow min-w-0 space-y-xs">
								<div class="o-card__title">
									<a href="<?php echo $subscription->getPermalink();?>">
										<span class="plan" ><?php echo $subscription->getTitle(); ?></span>
									</a>
								</div>
								<div class="o-card__desc text-gray-500">
									<?php if ($subscription->isActive() && !$subscription->getSubscriptionDate() || ($subscription->getExpirationType() === 'forever') ) { ?>
										<?php echo JText::_('MOD_PAYPLANS_SUBSCRIPTION_EXPIRATION_DATE_LIFETIME'); ?>
									<?php } else { ?>
										<?php $expirationDate = ""; ?>
										<?php if ($subscription->getExpirationDate()) { ?>
											<?php $expirationDate = $subscription->getExpirationDate(true)->toDisplay(PP::getDateFormat()); ?>
										<?php } ?>
										<?php echo JText::_('MOD_PAYPLANS_SUBSCRIPTION_EXPIRATION_DATE_' . PPJString::strtoupper($subscription->getStatusName())). " " .$expirationDate; ?>
									<?php } ?>
								</div>
							</div>
							<div class="flex-shrink-0">
								<?php if ($subscription->isRenewable()) { ?>
									<?php echo PP::themes()->fd->html('button.link', PPR::_('index.php?option=com_payplans&view=order&layout=processRenew&subscription_key=' . $subscription->getKey() . '&tmpl=component'), 'COM_PP_APP_RENEW_BUTTON', 'default', 'sm'); ?>
								<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
<?php } ?>