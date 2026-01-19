<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		PayPlans
* @subpackage	Email
* @contact 		support+payplans@readybytes.in
*/
if(defined('_JEXEC')===false) die(); ?>

<html xmlns="http://www.w3.org/1999/xhtml" >
<style>
		.fontlist
		{
		  	color: #323334;
		   font-family: Arial,Verdana,Tahoma,Sans Serif;
  			line-height: 25px;
  			word-spacing: 3px;
		}
			.row
		{
			padding:10px;
		}
		.col
		{
			padding:10px;
		}
		.links {
    		color: #2B6EAD;
    		text-decoration: none;
		}
		#shadow{
			box-shadow:0px 0px 11px -4px grey;
		}
		

</style>
		<table class="fontlist" id='shadow' align="center" bgcolor="#f8f8f8" border="0" cellpadding="0" cellspacing="0" width="650">
			<tbody>
			<tr>
				<td  bgcolor="f8f8f8">
					<br>
					<p style="margin-left:25px;">Dear [[USER_REALNAME]],<br></p><p style="padding:10px; margin-left:15px;">
					Thanks for choosing [[PLAN_TITLE]] at [[CONFIG_SITE_NAME]] !<br>We notice that your purchase is incomplete.<br><br>
					<b>Your details are as follows:</b>
					</p>
					<table  align="center">
						<tbody>
						<tr>
							<td class="col" align="right">
								Username:
							</td>						
							<td class="col">
								[[USER_USERNAME]]
							</td>
						</tr>
						<tr>
							<td style="padding:10px;" mce_style="padding:10px;" align="right">
								Plan Name:
							</td>						
							<td style="padding:10px;" mce_style="padding:10px;">
								[[PLAN_TITLE]]
							</td>
					</tbody></table>					
					<p style="padding:10px; margin-left:15px;">If you require assistance in completing the order please reply to this email, telephone +44 (0)20 8299 9742 or contact us at <a href="[[CONFIG_SITE_URL]]" class="links">[[CONFIG_SITE_URL]]</a><br></p>
					<p>Kind regards,</p>
					<p style="padding:10px; margin-left:15px;">
						<b>[[CONFIG_COMPANY_NAME]]</b><br>
						<br>
						<span style="font-size:12px;">
						[[CONFIG_COMPANY_ADDRESS]]						
						</span> 
					</p>
					
				</td>				
			</tr>
		</tbody></table>
</html>