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
					<p style="margin-left:25px;">Dear [[USER_REALNAME]],<br></p>
					<p style="padding:10px; margin-left:15px;">
					Thank you for enrolling in this membership package. Your courses are now available from your learning hub. Log in at any time to access them from your desktop, laptop, tablet or phone.<br><br>
					We very much hope that you will find them engaging and a valuable aid to your career development. We have an extensive help section and active community, but should you need assistance contact us via our support section or telephone +44 (0)20 8299 9742. We will look forward to reading your evaluations, but should you have any requests or suggestions do let us know.<br></p>
					<p style="padding:10px; margin-left:15px;">Kind regards,<br><br>
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
