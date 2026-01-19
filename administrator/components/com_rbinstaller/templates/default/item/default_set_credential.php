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
	 	<h3 id="myModalLabel"><?php echo JText::_('COM_RBINSTALLER_SET_CREDENTIAL_WINDOW_TITLE');?></h3>
	</div>

	<div class="modal-body">
		<div class="row-fluid" id="rbinstaller-credential-form">
			<div class="span11 offset1">
				<form class="form-horizontal rb-validate-form">
				
					<div class="row-fluid" id="rbinstaller-credential-error">
					<?php if(isset($verification_error)):?>
						<div class="alert alert-error">
							<?php echo $verification_error;?>
			    		</div>
					<?php endif;?>
					</div>
					
					<fieldset>		
						<!-- Text input-->
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_RBINSTALLER_SET_CREDENTIAL_WINDOW_EMAIL');?></label>
							<div class="controls">
						    	<input id="rbinstaller-credential-email" name="rbinstaller_credential_email" class="validate-email" required type="text" value="<?php echo $credential['email'];?>">			    	
							</div>
						</div>
						
						<!-- Text input-->
						<div class="control-group">
							<label class="control-label"><?php echo JText::_('COM_RBINSTALLER_SET_CREDENTIAL_WINDOW_PASSWORD');?></label>
							<div class="controls">
						    	<input id="rbinstaller-credential-password" name="rbinstaller_credential_password" required type="password">		    	
							</div>
						</div>
						
					</fieldset>
				</form>				
			</div>
		</div>
	</div>
	
	<div class="modal-footer">
		<div class="pull-left">
			<a href="#" onclick="return rbinstaller.registration.form();"><?php echo JText::_('COM_RBINSTALLER_REGISTER');?></a>
		</div>
		
		<button class="btn btn-info" id="rbinstaller-credential-verify"><?php echo JText::_('COM_RBINSTALLER_VERIFY');?></button>		
	</div>	

<?php 