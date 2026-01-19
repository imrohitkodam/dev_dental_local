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
<?php if ($params->get('display_overlay', true)) { ?>
<style type="text/css">
.pp-restricted-gradient {
	background: linear-gradient(rgba(<?php echo $overlayRgb;?>,0) 0%, rgba(<?php echo $overlayRgb;?>, 0.5) 50%, rgba(<?php echo $overlayRgb;?>, 1) 100%);
	height: 150px;
	margin-top: -160px;
	position: relative;
}
</style>
<?php } ?>

<div id="pp" class="<?php echo $this->isMobile() ? 'is-mobile' : 'is-desktop';?>">
	<?php if ($params->get('display_overlay', true)) { ?>
	<div class="pp-restricted-gradient"></div>
	<?php } ?>

	<div class="pp-restricted o-card">
		<div class="pp-restricted-pop-label">
			<div class="pp-restricted-pop-label__icon">
				<i class="fa fa-lock"></i>
			</div>
		</div>
		<div class="pp-restricted__body t-text--center">	
			<h3 class="pp-restricted__title"><?php echo JText::_('COM_PP_CONTENTACL_RESTRICTED_MEMBERS_TO_ACCESS');?></h3>
			<p class="pp-restricted__desc"><?php echo JText::_('COM_PP_CONTENTACL_RESTRICTED_MEMBERS_TO_ACCESS_DESC');?></p>
		</div>
		<div class="pp-restricted__body pp-restricted--bg-shade ">

			<?php if (isset($allPlanData)) { ?>
				<?php foreach ($allPlanData as $plan) { ?>
					<div class="o-card t-lg-mb--lg">
						<div class="o-card__body">
							<div class="o-grid o-grid--center">
								<div class="o-grid__cell o-grid__cell--flex-grow-1 t-lg-pr--lg t-xs-pr--no">
									<div class="o-card__title"><?php echo $plan->getTitle(); ?>
										<?php echo $this->html('html.amount', $plan->getPrice(), $plan->getCurrency()); ?>
									</div>
									<div class="o-card__desc t-lg-mb--no">
										<?php if ($plan->isRecurring()) { ?>
											<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR'); ?>
										<?php } else { ?>
											<?php echo JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR_FOR'); ?>
										<?php } ?>
										<?php echo $this->html('html.plantime', $plan->getExpiration()); ?>
									</div>
								</div>
								<div class="o-grid__cell o-grid__cell--right">
									<a href="<?php echo $plan->getSelectPermalink();?>" class="btn btn-pp-default-o t-xs-mt--lg"><?php echo JText::_('COM_PP_SUBSCRIBE');?></a>
								</div>
							</div>
						</div>
					</div>

				<?php } ?>
			<?php } else { ?>
				<div class="o-card t-lg-mb--lg">
					<div class="o-card__body">
						<div class="o-grid o-grid--center">
							<div class="o-grid__cell o-grid__cell--flex-grow-1 t-lg-pr--lg t-xs-pr--no">
								<div class="o-card__title"><?php echo JText::_('COM_PAYPLANS_CONTENTACL_SUBSCRIBE_PLAN');?></div>
							</div>
							<div class="o-grid__cell o-grid__cell--right">
								<a href="<?php echo $viewPlans ?> " class="btn btn-pp-default-o t-xs-mt--lg"><?php echo JText::_('COM_PP_CONTENTACL_SEE_ALL');?></a>
							</div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<?php if (!$user->id) { ?>
		<div class="pp-restricted__footer">
				<div class="o-grid o-grid--center o-grid--justify-center">
					<div class="o-grid__cell o-grid__cell--auto-size">
						<?php echo JText::_('COM_PP_RESTRICTED_LOGIN_IF_YOU_HAVE_MEMBERSHIP'); ?> 
					</div>
					<div class="o-grid__cell o-grid__cell--auto-size">
						<a href="<?php echo $loginUrl; ?>" class="btn btn-pp-primary t-xs-mt--lg t-xs-ml--no t-lg-ml--md"><?php echo JText::_('COM_PP_LOGIN_FOR_ACCESS'); ?></a>
					</div>
				</div>	
		</div>
		<?php } ?>
	</div>
</div>