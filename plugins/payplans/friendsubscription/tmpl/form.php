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
<div class="pp-checkout-item">
	<div class="pp-checkout-item__title"><?php echo strtoupper(JText::_('COM_PP_APP_FRIEND_SUBSCRIPTION_TITLE'));?></div>

	<div class="pp-checkout-item__content">
		<table class="pp-checkout-table">
			<tbody>
				<tr>
					<td class="pp-checkout-table__desc">
						<?php echo JText::_('COM_PP_APP_FRIEND_SUBSCRIPTION_TITLE_DESC'); ?>
					</td>
				</tr>
				<tr>
					<td class="o-form-group" data-pp-friendsubscription-wrapper>
						<div class="o-input-group">
							<div class="" data-pp-form-user>
								<div class="o-input-group" >
									<input type="text" class="o-form-control" disabled="disabled" size="35" placeholder="<?php echo JText::_('Browse User');?>" data-pp-form-user-preview />
									
									<span class="o-input-group__append">
										<a href="javascript:void(0);" class="btn btn-pp-default-o" data-pp-form-user-browse>
											<i class="fa fa-user"></i> <?php echo JText::_('COM_PP_BROWSE_BUTTON'); ?>
										</a>
										<a href="javascript:void(0);" class="btn btn-pp-default-o" data-pp-form-user-clear>
											<i class="fa fa-times"></i>
										</a>
									</span>
								</div>
								<input type="hidden" id="app_friend_user_id" name="app_friend_user_id" value="" data-pp-friend-user-id />
								<input type="hidden" id="app_friend_userlist_option" name="app_friend_userlist_option" value="<?php echo $listOption;?>" data-pp-friend_userlist_option />

							</div>
						</div>

						<div class="t-text--danger" data-pp-friend-userid-message></div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>

	<hr class="pp-hr t-lg-mt--no t-lg-mb--no">
</div>

