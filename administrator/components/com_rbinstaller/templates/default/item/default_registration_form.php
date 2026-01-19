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
	 <div class="modal-header">
	 	<h3 id="myModalLabel"><?php echo JText::_('COM_RBINSTALLER_REGISTRATION_FORM_WINDOW_TITLE');?></h3>
	</div>

	<div class="modal-body">
		<div class="row-fluid" id="rbinstaller-registration-form">
			<div class="span11 offset1">
				<form class="form-horizontal">
				
					<div class="row-fluid" id="rbinstaller-registration-error">
					<?php if(isset($registration_error)):?>
						<div class="alert alert-error">
							<?php echo $registration_error;?>
			    		</div>
					<?php endif;?>
					</div>
					
					<fieldset>		
						<!-- Text input-->
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_RBINSTALLER_REGISTRATION_FORM_WINDOW_EMAIL');?></label>
							<div class="controls">
						    	<input id="rbinstaller-registration-email" name="rbinstaller_registration_email" class="validate-email" required type="text" value="">			    	
							</div>
						</div>
						
						<!-- Text input-->
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_RBINSTALLER_REGISTRATION_FORM_WINDOW_PASSWORD');?></label>
							<div class="controls">
						    	<input id="rbinstaller-registration-password" name="rbinstaller_registration_password" class="required" required type="password">		    	
							</div>
						</div>
						
					</fieldset>
				</form>	
			</div>
		</div>
	</div>
	
	<div class="modal-footer">		
		<div class="pull-left">
			<a href="#" onclick="rb.ajax.go('index.php?option=com_rbinstaller&view=item&task=show_login_popup'); return false;">
				<?php echo JText::_('COM_RBINSTALLER_ALREADY_REGISTER');?>
			</a>
		</div>
		<button class="btn btn-info" id="rbinstaller-registration-register" onClick="rbinstaller.registration.register();"><?php echo JText::_('COM_RBINSTALLER_REGISTER');?></button>
	</div>	
<?php 