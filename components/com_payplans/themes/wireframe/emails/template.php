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
<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
<title>PayPlans</title>
<!--[if !mso]><!-- -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--<![endif]-->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
#outlook a { padding:0; }
body { margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%; }
table, td { border-collapse:collapse;mso-table-lspace:0pt;mso-table-rspace:0pt; }
img { border:0;height:auto;line-height:100%; outline:none;text-decoration:none;-ms-interpolation-mode:bicubic; }
p { display:block;margin:13px 0; }
</style>
<!--[if mso]>
<xml>
<o:OfficeDocumentSettings>
  <o:AllowPNG/>
  <o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->
<!--[if lte mso 11]>
<style type="text/css">
  .mj-outlook-group-fix { width:100% !important; }
</style>
<![endif]-->

<!--[if !mso]><!-->
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet" type="text/css">
<style type="text/css">
@import url(https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap);
</style>
<!--<![endif]-->

<style type="text/css">
@media only screen and (min-width:320px) {
.mj-column-per-100 { width:100% !important; max-width: 100%; }
}
</style>

<style type="text/css">
@media only screen and (max-width:320px) {
	table.mj-full-width-mobile { width: 100% !important; }
	td.mj-full-width-mobile { width: auto !important; }
}
</style>

</head>
<body style="background-color:#ffffff;">

	<?php if ($intro) { ?>
	<!-- Ignore for Outlook to duplicate the content -->
	<!--[if !mso ]><!-->
	<!-- Visually Hidden Preheader Text : BEGIN -->
	<div style="display:none;font-size:1px;color:#ffffff;line-height:1px;max-height:0px;max-width:0px;opacity:0;overflow:hidden;">
		<?php echo $intro;?>
	</div>
	<!-- Visually Hidden Preheader Text : END -->
	<!--<![endif]-->
	<?php } ?>

	<div style="background-color:#ffffff;">

		<!--[if mso | IE]>
		<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
		<![endif]-->
		<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
			<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;"><tbody><tr>
				<td style="direction:ltr;font-size:0px;padding:20px;text-align:center;">
					<!--[if mso | IE]>
					<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:440px;">
					<![endif]-->
					<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
						<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%"><tbody><tr>
							<td style="vertical-align:top;padding:0;">
								<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%"><tr>
									<td align="center" style="font-size:0px;padding:5px 0 5px 0;word-break:break-word;">
										<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="border-collapse:collapse;border-spacing:0px;"><tbody><tr>
											<td style="width:130px;">
												<a href="<?php echo JURI::root();?>" target="_blank" style="color: #4078C0; text-decoration: none;">
													<img height="auto" src="<?php echo PP::getCompanyLogo();?>" style="border:0;display:block;outline:none;text-decoration:none;height:auto;width:100%;font-size:13px;">
												</a>
											</td>
										</tr></tbody></table>
									</td>
								</tr></table>
							</td>
						</tr></tbody></table>
					</div>
					<!--[if mso | IE]>
					</td></tr></table>
					<![endif]-->
				</td>
			</tr></tbody></table>
		</div>
		<!--[if mso | IE]>
		</td></tr></table>
		<![endif]-->

		<?php echo $contents;?>

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
																<?php $siteUrl = rtrim(JURI::root(), '/'); ?>
																<p><?php echo JText::_('COM_PP_EMAIL_INQUIRIES_LINE');?>, <a href="<?php echo $siteUrl; ?>" class="links"><?php echo $siteUrl;?></a></p>
																<br />
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

		<!-- Border -->
		<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#e1e4ed;background-color:#e1e4ed;width:100%;">
			<tbody>
				<tr>
					<td>
						<!--[if mso | IE]>
						<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
						<![endif]-->

						<div style="margin:0px auto;max-width:480px;">
							<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="width:100%;"><tbody><tr><td style="direction:ltr;font-size:0px;padding:0;padding-bottom:0px;padding-top:1px;text-align:center;">
							<!--[if mso | IE]>
							<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr></tr></table>
							<![endif]-->
							</td></tr></tbody></table>
						</div>
						<!--[if mso | IE]>
						</td></tr></table>
						<![endif]-->
					</td>
				</tr>
			</tbody>
		</table>
		<!-- Border -->


		<!--[if mso | IE]>
		<table align="center" border="0" cellpadding="0" cellspacing="0" class="" style="width:480px;" width="480"><tr><td style="line-height:0px;font-size:0px;mso-line-height-rule:exactly;">
		<![endif]-->
		<div style="background:#ffffff;background-color:#ffffff;margin:0px auto;max-width:480px;">
			<table align="center" border="0" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;background-color:#ffffff;width:100%;">
				<tbody>
					<tr>
						<td style="direction:ltr;font-size:0px;padding:0;padding-top:10px;text-align:center;">
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
															<div style="font-family:'Roboto', Arial, sans-serif;font-size:12px;line-height:10px;text-align:left;color:#888888;">
																<p><?php echo JText::_('COM_PP_EMAIL_DISCLAIMER');?></p>
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
						<td style="direction:ltr;font-size:0px;padding:0;padding-top:10px;text-align:center;">
							<div class="mj-column-per-100 mj-outlook-group-fix" style="font-size:0px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;">
								<table border="0" cellpadding="0" cellspacing="0" role="presentation" width="100%">
									<tbody>
										<tr>
											<td style="background-color:#ffffff;vertical-align:top;padding:0;">
												<!--[if mso | IE]>
												<table role="presentation" border="0" cellpadding="0" cellspacing="0"><tr><td class="" style="vertical-align:top;width:480px;">
												<![endif]-->
												<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="" width="100%">
													<tr>
														<td align="left" style="font-size:0px;padding:0;word-break:break-word;">
															<div style="font-family:'Roboto', Arial, sans-serif;font-size:12px;line-height:10px;text-align:left;color:#888888;">
																<?php $config = PP::config();?>
																<p><b><?php echo $config->get('companyName')?></b>,</p>
																<p><?php echo $config->get('companyAddress')?>.</p>
															</div>
														</td>
													</tr>
												</table>
												<!--[if mso | IE]>
												</td></tr></table>
												<![endif]-->
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<!--[if mso | IE]>
		</td></tr></table>
		<![endif]-->
	</div>
</body>

</html>
