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
<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#F2F4FA;background-color:#F2F4FA;width:100%;">
	<tbody>
		<tr>
			<td>
				<!--[if mso | IE]>
				<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
				<![endif]-->

				<div style="margin:0px auto;max-width:480px;">
					<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;">
						<tbody>
							<tr>
								<td style="direction:ltr;font-size:0px;padding:0;text-align:center;">
									<!--[if mso | IE]>
									<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:480px;">
									<![endif]-->

									<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
										<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
											<tbody>
												<tr>
													<td style="vertical-align:top;padding:0;padding-top:40px;padding-bottom:40px;">
														<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
															<tr>
																<td align="center" style="font-size:0px;padding:0;word-break:break-word;">
																	<div style="font-family:'Roboto', Arial, sans-serif;font-size:36px;font-weight:bold;line-height:48px;text-align:center;color:#444444;"><?php echo JText::_('COM_PP_EMAIL_SUBSCRIPTION_POST_EXPIRED'); ?></div>
																</td>
															</tr>
															<tr>
																<td align="center" style="font-size:0px;padding:0;padding-bottom:20px;word-break:break-word;">
																	<div style="font-family:'Roboto', Arial, sans-serif;font-size:21px;font-weight:100;line-height:30px;text-align:center;color:#444444;"><?php echo JText::_('COM_PP_EMAIL_SUBSCRIPTION_POST_EXPIRED_DESC'); ?></div>
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
			</td>
		</tr>
	</tbody>
</table>

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

														<p><?php echo JText::_('COM_PP_EMAIL_HELLO_WITH_USERNAME'); ?></p>
														<p><?php echo JText::_('COM_PP_EMAIL_SUBSCRIPTION_POST_EXPIRED_CONTENT'); ?></p>

														<div style="background: #f8f8f8;padding: 20px;">
															<table align="center" style="padding: 20px;">
																<tbody>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_EMAIL_USERNAME'); ?>:</b>
																	</td>
																	<td>
																		[[USER_USERNAME]]
																	</td>
																</tr>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_EMAIL_PLAN_NAME'); ?>:</b>
																	</td>
																	<td>
																		[[PLAN_TITLE]]
																	</td>
																</tr>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_EMAIL_ACTIVATION_DATE'); ?>:</b>
																	</td>
																	<td>
																		[[SUBSCRIPTION_SUBSCRIPTION_DATE]]
																	</td>
																</tr>
																<tr>
																	<td align="right" style="padding: 10px;">
																		<b><?php echo JText::_('COM_PP_EMAIL_EXPIRATION_DATE'); ?>:</b>
																	</td>
																	<td>
																		[[SUBSCRIPTION_EXPIRATION_DATE]]
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

<!--[if mso | IE]>
<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
<![endif]-->

<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
	<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
		<tbody>
			<tr>
				<td style="direction:ltr;font-size:0px;padding:0;padding-bottom:40px;padding-top:8px;text-align:center;">
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
												<td align="center" vertical-align="middle" style="font-size:0px;padding:0 0 0;word-break:break-word;">
													<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:separate;width:100%;line-height:100%;">
														<tr>
															<td align="center" bgcolor="#353543" role="presentation" style="border:none;border-radius:3px;cursor:auto;mso-padding-alt:10px 25px;background:#353543;" valign="middle">
																<a href="[[CONFIG_PLAN_RENEW_URL]]" style="display: inline-block; background: #353543; color: white; font-family: Arial, sans-serif; font-size: 16px; font-weight: bold; line-height: 120%; margin: 0; text-transform: none; padding: 10px 25px; mso-padding-alt: 0px; border-radius: 3px; text-decoration: none;" target="_blank">
																	<?php echo JText::_('COM_PP_EMAIL_RENEW_SUBSCRIPTION'); ?> &#8594;
																</a>
															</td>
														</tr>
													</table>
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
