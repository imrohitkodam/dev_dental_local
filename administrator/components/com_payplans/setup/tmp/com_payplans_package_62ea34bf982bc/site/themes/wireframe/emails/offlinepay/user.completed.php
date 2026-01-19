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
<?php echo $this->renderHeader('COM_PP_OFFLINE_PAY_USER_HEADING', 'COM_PP_OFFLINE_PAY_USER_HEADING_SUBTITLE'); ?>

<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->

<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
	<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
		<tbody>
			<tr>
				<td style="direction:ltr;font-size:0px;padding:0;padding-top:40px;text-align:center;">
					<!--[if mso | IE]>
					<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:480px;">
					<![endif]-->

					<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
							<tbody>
								<tr>
									<td style="background-color:#ffffff;vertical-align:top;padding:0;">
										<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
											<tr>
												<td align="left" style="font-size:0px;padding:0;word-break:break-word;">
													<div style="font-family:'Roboto', Arial, sans-serif;font-size:16px;line-height:30px;text-align:left;color:#444444;">
														<p>
															<?php echo nl2br(JText::sprintf('COM_PP_OFFLINE_PAY_USER_CONTENTS', $this->isPreview() ? 'Customer Name' : $buyer->getName())); ?>
														</p>

														<p>
															<?php echo JText::_('COM_PP_BANK_DETAILS') . " :"; ?>
															<br />
														</p>
														
														<div style="background: #f8f8f8;padding: 20px;">
															<table align="center" style="padding: 20px;">
																<tbody>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_PAYMENT_BANK_NAME');?>:</b>
																	</td>						
																	<td>
																		<?php if ($this->isPreview()) { ?>
																			Bank Name
																		<?php } else { ?>
																			<?php echo $bankName;?>
																		<?php } ?>
																	</td>
																</tr>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_PAYMENT_BANK_ACCOUNT_NAME');?>:</b>
																	</td>
																	<td>
																		<?php if ($this->isPreview()) { ?>
																			Account Name
																		<?php } else { ?>
																			<?php echo $accountName;?>
																		<?php } ?>
																	</td>
																</tr>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_PAYMENT_BANK_ACCOUNT_NUMBER');?>:</b>
																	</td>
																	<td>
																		<?php if ($this->isPreview()) { ?>
																			123456789
																		<?php } else { ?>
																			<?php echo $bankAccount;?>
																		<?php } ?>
																	</td>
																</tr>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_PAYMENT_BANK_INVOICE_REFERENCE_NUMBER');?>:</b>
																	</td>
																	<td>
																		<?php if ($this->isPreview()) { ?>
																			INV0001
																		<?php } else { ?>
																			<?php echo $invoice->getKey();?>
																		<?php } ?>
																	</td>
																</tr>
																</tbody>
															</table>
														</div>
													</div>
												</td>
											</tr>
										</table>
									</td>
								</tr>
							</tbody>
						</table>
					</div>

					<!--[if mso | IE]>
					</td></tr></table>
					<![endif]-->
				</td>
			</tr>
		</tbody>
	</table>
</div>


<!--[if mso | IE]>
</td></tr></table>
<![endif]-->