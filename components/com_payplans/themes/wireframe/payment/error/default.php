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
<div class="pp-checkout-container">
	<?php echo $this->output('site/checkout/default/header', ['step' => 'payment', 'title' => 'COM_PAYPLANS_PAYMENT_ERROR']); ?>

	<div class="pp-checkout-wrapper">
		<div class="pp-checkout-wrapper__sub-content">
			<div class="pp-checkout-menu">
				<div class="t-lg-mb--lg">
					<div class="pp-result-container">
						<div class="pp-result">

							<div class="pp-result__title">
								<?php echo JText::_('COM_PAYPLANS_PAYMENT_ERROR'); ?>
							</div>

							<div class="pp-result__desc">
								<?php echo JText::_('COM_PAYPLANS_PAYMENT_ERROR_MSG');?>
							</div>

							<div class="pp-result__action">
								<a href="<?php echo PPR::_('index.php?option=com_payplans&view=plan'); ?>" class="t-lg-mr--lg">
									&larr; <?php echo JText::_('COM_PAYPLANS_PAYMENT_ERROR_SUBSCRIBE_AGAIN'); ?>
								</a>
							</div>

							<div class="pp-result__note t-lg-mt--xl">
								<a href="javascript:void(0);" data-pp-contact>
									<?php echo JText::_('COM_PP_HELP_COMPLETE_PAYMENT'); ?>
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<?php 