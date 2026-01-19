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
<div class="grid grid-cols-1 md:grid-cols-12 gap-md">
	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_DISCOUNTS_GENERAL'); ?>
				
			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'discounts_referral', 'COM_PP_CONFIG_DISCOUNTS_ENABLE_REFERRAL'); ?>

				<?php echo $this->fd->html('settings.toggle', 'enableDiscount', 'COM_PP_CONFIG_DISCOUNTS_ENABLE_DISCOUNTS'); ?>
	
				<?php echo $this->fd->html('settings.toggle', 'multipleDiscount', 'COM_PP_CONFIG_DISCOUNTS_ALLOW_COMBINING_DISCOUNTS'); ?>

				<?php echo $this->fd->html('settings.text', 'allowedMaxPercentDiscount', 'COM_PPC_ONFIG_DISCOUNTS_MAX_DISCOUNTS', '', [
					'postfix' => '%', 
					'size' => 5
				], '', 'text-center'); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_SOCIAL_DISCOUNTS_GENERAL'); ?>
				
			<div class="panel-body">
	
				<?php echo $this->fd->html('settings.toggle', 'discounts_twitter', 'PLG_PAYPLANSSOCIALDISCOUNT_ENABLE_TWITTER_FOLLOW', '', '', '', '', [
					'dependency' => '[data-pp-discounts-twitter]', 
					'dependencyValue' => 1]); ?>
				<?php echo $this->fd->html('settings.text', 'discounts_twitter_url', 'PLG_PAYPLANSSOCIALDISCOUNT_TWITTER_FOLLOW_PAGEURL', '', ['wrapperAttributes' => 'data-pp-discounts-twitter', 'visible' => $this->config->get('discounts_twitter', 0)]); ?>
				<?php echo $this->html('settings.discounts', 'discounts_twitter_code', 'PLG_PAYPLANSSOCIALDISCOUNT_TWITTER_FOLLOW_DISCOUNT', '', ['wrapperAttributes' => 'data-pp-discounts-twitter', 'visible' => $this->config->get('discounts_twitter', 0)]); ?>
			</div>
		</div>
	</div>
</div>