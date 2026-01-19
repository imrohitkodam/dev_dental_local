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
					Please note that your [[PLAN_TITLE]] subscription will expire on [[SUBSCRIPTION_EXPIRATION_DATE]].  Please remember to complete your course(s) by this date.
					We hope that you will wish to renew, so please use coupon [xyz] and receive an [X%] discount before your plan expires.<br></p>
					
					<p style="padding:10px; margin-left:15px;">
						<span>Kind regards,</span><br>
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
