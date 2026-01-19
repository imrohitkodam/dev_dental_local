<?php

/**
* @copyright	Copyright (C) 2009 - 2012 Ready Bytes Software Labs Pvt. Ltd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* @package 		RBInstaller
* @subpackage	Back-end
* @contact		team@readybytes.in
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

?>

<div class="modal fade" id="ppinstaller-upgrade-modal" tabindex="-1" role="dialog" aria-labelledby="Payplans installer upgrade" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="myModalLabel">
        	<?php echo JText::_('COM_PPINSTALLER_UPGRADE_WINDOW_TITLE');?>
			<a class="close"  type="button" href="index.php?option=com_payplans">x</a>
        </h4>
      </div>
      <div class="modal-body">
      	<div id="ppinstaller-upgrade-init">
	      	<p    class="text-center">
				<?php echo JText::_('COM_PPINSTALLER_UPGRADE_MSG');?>
			</p>
			<p class="text-center">
				<a onclick="return ppInstaller.upgrade.request();" href="#" class="btn btn-primary" title="Upgrade Payplans Installer"><?php echo JText::_('COM_PPINSTALLER_UPGRADE_WINDOW_TITLE');?></a>
			</p>
		</div>
		<div id="ppinstaller-upgrade-loading" class="text-center" style="display: none;">
			<h3><i class="glyphicon glyphicon-screenshot  ppinstaller-spin"></i></h3>
		</div>
		<div id="ppinstaller-upgrade-success" class="text-center text-success" style="display: none;">
			<?php echo JText::_('COM_PPINSTALLER_UPGRADE_SUCCESS_MSG');?>
		</div>      
		<div id="ppinstaller-upgrade-error" class="text-center text-danger" style="display: none;">
		</div>		
      </div>
    </div>
  </div>
</div>

<?php 
