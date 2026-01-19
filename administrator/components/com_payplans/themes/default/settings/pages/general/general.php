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
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_GENERAL_FEATURES'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'accessLoginBlock', 'COM_PAYPLANS_CONFIG_ACCESS_BLOCK_NON_SUBSCRIBERS'); ?>
				<?php echo $this->fd->html('settings.toggle', 'microsubscription', 'COM_PAYPLANS_CONFIG_MICROSUBSCRIPTION'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_MENU_ACCESS_CONFIGURATION'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'show404error', 'COM_PAYPLANS_CONFIG_MENU_ACCESS_SHOW404ERROR'); ?>
				<?php echo $this->fd->html('settings.toggle', 'showOrhide', 'COM_PAYPLANS_CONFIG_MENU_ACCESS_SHOW_OR_HIDE_MENU'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->html('panel.heading', 'COM_PP_CONFIG_CUSTOM_DETAILS_SECTION'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.text', 'custom_details_file_limit', 'COM_PP_CONFIG_CUSTOM_DETAILS_FILE_FIELD_LIMIT', '', [
					'size' => 7, 
					'postfix' => 'COM_PP_FILES_POSTFIX'
				]); ?>
				<?php echo $this->fd->html('settings.textbox', 'custom_details_file_maxsize', 'COM_PP_CONFIG_CUSTOM_DETAILS_FILE_FIELD_MAXSIZE', '', [
					'size' => 7, 
					'postfix' => 'MB'
				]); ?>
			</div>
		</div>
	</div>

	<div class="col-span-1 md:col-span-6 w-auto">
		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PAYPLANS_CONFIG_LOCALIZATION'); ?>
			
			<div class="panel-body">
				<?php echo $this->html('settings.currency', 'currency', 'COM_PAYPLANS_CONFIG_CURRENCY_LABEL'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'show_currency_as', 'COM_PP_CONFIG_GENERAL_LOCALIZATION_CURRENCY_AS', [
							'fullname' => 'COM_PAYPLANS_CONFIG_SHOW_CURRENCY_AS_FULLNAME',
							'isocode' => 'COM_PAYPLANS_CONFIG_SHOW_CURRENCY_AS_ISOCODE',
							'symbol' => 'COM_PAYPLANS_CONFIG_SHOW_CURRENCY_AS_SYMBOL'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'show_currency_at', 'COM_PP_CONFIG_GENERAL_LOCALIZATION_CURRENCY_LOCATION', [
					'before' => 'COM_PAYPLANS_CONFIG_SHOW_CURRENCY_AS_BEFORE',
					'after' => 'COM_PAYPLANS_CONFIG_SHOW_CURRENCY_AT_AFTER'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'price_decimal_separator', 'COM_PP_CONFIG_GENERAL_LOCALIZATION_DECIMAL_SEPARATOR', [
					'.' => 'COM_PAYPLANS_CONFIG_AMOUNT_DECIMAL_SEPARATOR_DOT',
					',' => 'COM_PAYPLANS_CONFIG_AMOUNT_DECIMAL_SEPARATOR_COMMA'
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'fractionDigitCount', 'COM_PAYPLANS_CONFIG_FRACTION_DIGIT_COUNT', [
					0 => '0',
					1 => '1',
					2 => '2',
					3 => '3',
					4 => '4',
					5 => '5',
				]); ?>

				<?php echo $this->fd->html('settings.dropdown', 'date_format', 'COM_PAYPLANS_DATE_FORMAT_LABEL', [
					'%Y-%m-%d' => '%Y-%m-%d',
					'%m/%d/%Y' => '%m/%d/%Y',
					'%m-%d-%Y' => '%m-%d-%Y',
					'%d/%m/%Y' => '%d/%m/%Y',
					'%d-%m-%Y' => '%d-%m-%Y',
					'%d %B %y' => '%d %B %y',
					'%d %B %Y' => '%d %B %Y',
					'%B %d, %y' => '%B %d, %y',
					'%B %d, %Y' => '%B %d, %Y'
				]); ?>

				<?php echo $this->fd->html('settings.toggle', 'show_time', 'COM_PAYPLANS_CONFIG_SHOW_TIME'); ?>
			</div>
		</div>

		<div class="panel">
			<?php echo $this->fd->html('panel.heading', 'COM_PP_CONFIG_SALES_POPUP'); ?>

			<div class="panel-body">
				<?php echo $this->fd->html('settings.toggle', 'enable_purchased_popup', 'COM_PP_ENABLE_SALES_POPUP'); ?>

				<?php echo $this->fd->html('settings.toggle', 'sitewide_purchased_popup', 'COM_PP_CONFIG_SALES_POPUP_SITEWIDE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'purchased_popup_purchaser', 'COM_PP_CONFIG_SALES_POPUP_DISPLAY_PURCHASER_NAME'); ?>

				<?php echo $this->fd->html('settings.toggle', 'purchased_popup_purchaser_country', 'COM_PP_CONFIG_SALES_POPUP_DISPLAY_PURCHASER_COUNTRY'); ?>

				<?php echo $this->fd->html('settings.toggle', 'purchased_popup_lapsed', 'COM_PP_CONFIG_SALES_POPUP_DISPLAY_TIME_LAPSE'); ?>

				<?php echo $this->fd->html('settings.text', 'purchased_popup_lapsed_duration', 'COM_PP_CONFIG_SALES_POPUP_PURCHASED_TIME_LAPSE', '', [
					'postfix' => 'Hours',
					'size' => 7
				], '', 'text-center'); ?>

				<?php echo $this->fd->html('settings.text', 'purchased_popup_delay', 'COM_PP_CONFIG_SALES_POPUP_DELAY', '', [
					'postfix' => 'Seconds',
					'size' => 7
				], '', 'text-center'); ?>

				<?php echo $this->fd->html('settings.text', 'purchased_popup_duration', 'COM_PP_CONFIG_SALES_POPUP_DURATION', '', [
					'postfix' => 'Seconds',
					'size' => 7
				], '', 'text-center'); ?>

				<?php echo $this->fd->html('settings.text', 'purchased_popup_interval', 'COM_PP_CONFIG_SALES_POPUP_INTERVAL', '', [
					'postfix' => 'Seconds',
					'size' => 7
				], '', 'text-center'); ?>

				<?php echo $this->fd->html('settings.toggle', 'purchased_popup_mobile', 'COM_PP_CONFIG_SALES_POPUP_MOBILE'); ?>

				<?php echo $this->fd->html('settings.toggle', 'purchased_popup_loop', 'COM_PP_CONFIG_SALES_POPUP_LOOP'); ?>

				<?php echo $this->fd->html('settings.text', 'purchased_popup_total', 'COM_PP_CONFIG_SALES_POPUP_TOTAL', '', [
					'postfix' => 'Items',
					'size' => 7
				], '', 'text-center'); ?>

				<?php echo $this->fd->html('settings.dropdown', 'purchased_popup_position', 'COM_PP_CONFIG_SALES_POPUP_POSITION', [
					'top-left' => 'Top Left',
					'top-right' => 'Top Right',
					'bottom-left' => 'Bottom Left',
					'bottom-right' => 'Bottom Right'
				]); ?>
			</div>
		</div>
	</div>
</div>