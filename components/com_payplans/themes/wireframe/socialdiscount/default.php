<?php
/**
* @package      PayPlans
* @copyright    Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>

<div class="pp-checkout-item">
	<div class="pp-checkout-item__title">
		<?php echo mb_strtoupper(JText::_("PLG_PP_SOCIALDISCOUNT_TITLE"));?>
	</div>

	<div class="pp-checkout-item__content">
		<table class="pp-checkout-table">
			<tbody>
				<tr>
					<td class="t-text--left">
						<div class="pp-checkout-table__desc"><?php echo JText::_("PLG_PP_SOCIALDISCOUNT_DESCRIPTION"); ?></div>
					</td>
				</tr>
				<tr>	
					<td class="t-text--left">
						<div class="pp-social-discount">
							<?php foreach ($output as $buttonHtml) { ?>
								<div class="pp-social-discount__item">
									<?php echo $buttonHtml; ?>
								</div>
							<?php } ?>
						</div>
					</td>
				</tr>
			</tbody>
		</table>

		<hr class="pp-hr t-lg-mt--no t-lg-mb--no">
	</div>
</div>