<?php

/**
* @copyright	Copyright (C) 2009 - 2015 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Back-end
* @contact		support@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>
<div id="rbinstaller-upgrade-modal" class="modal hide fade">
	 <div class="modal-header">
	 	<h3 id="myModalLabel"><?php echo JText::_('COM_RBINSTALLER_UPGRADE_WINDOW_TITLE');?></h3>
	</div>

	<div class="modal-body">
		<p class="text-center">
			<?php echo JText::_('COM_RBINSTALLER_UPGRADE_MSG');?>
		</p>
		<p class="text-center">
			<a onclick="return rbinstaller.upgrade.request();" href="#" class="btn btn-primary" title="Upgrade RbInstaller"><?php echo JText::_('COM_RBINSTALLER_UPGRADE_WINDOW_TITLE');?></a>
		</p>
	</div>
	
	<div class="modal-footer">
		&nbsp;		
	</div>
</div>
<?php 