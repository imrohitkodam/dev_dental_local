<?php
/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package		Payplans
* @subpackage	Payplans Quick Icon
* @contact		support+payplans@readybytes.in
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

//If Payplans not Enabled or Not Installed then nothing to do.
if(!defined('PAYPLANS_LOADED')){
	return true;
}
	$dashBoard  = XiText::_('MOD_PAYPLANS_QUICK_ICON_DASHBOARD');
	$config		= XiText::_('MOD_PAYPLANS_QUICK_ICON_CONFIG');

	//Create an array of quick icons detail
	$record = array();   //'Dashboard','Appmanager'
	$record = array($dashBoard	   =>	array('route' => XiRoute::_route('index.php?option=com_payplans'),
										  	  'icon'  => PAYPLANS_PATH_TEMPLATE_ADMIN.'/default/_media/images/icons/16/payplans.png',
	 									  	  'alt'   => XiText::_('MOD_PAYPLANS_QUICK_ICON_DASHBOARD_ALT_TEXT')
										),
					$config		   =>	array('route' => XiRoute::_route('index.php?option=com_payplans&view=config'),
									    	  'icon'  => PAYPLANS_PATH_TEMPLATE_ADMIN.'/default/_media/images/icons/16/ppconfig.png' ,
	 									      'alt'	  => XiText::_('MOD_PAYPLANS_QUICK_ICON_CONFIG_ALT_TEXT')
										)
					); ?>
<div class="sidebar-nav quick-icons">
	<div class="j-links-groups">
		<h2 class="nav-header"><?php echo XiText::_("MOD_PAYPLANS_QUICK_ICON");?></h2>
		<ul class="j-links-group nav nav-list">
			<?php foreach ($record as $key => $value):?>
					<li>
						<a href="<?php echo JFilterOutput::ampReplace($value['route']); ?>">
						    <?php echo XiHtml::image(XiHelperTemplate::mediaURI($value['icon'], false), $value['alt']) ;?>
							<?php echo $key; ?>
						</a>
					</li>
			<?php endforeach;?>
		</ul>
	</div>
</div>
<?php 
