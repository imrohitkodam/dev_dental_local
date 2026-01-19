<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_ppinstaller
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

// no direct access
defined('_JEXEC') or die;

$redirectUrl 	= JRoute::_("index.php?option=com_ppinstaller"); ?>

<form action="<?php echo $redirectUrl; ?>" method="post" name="adminForm" id="adminForm" class="pp-adminForm" >
	
<input type="hidden" name="view" 			value="<?php echo $this->nextView; ?>" />
<input type="hidden" name="task" 			value="<?php echo $this->nextTask; ?>" />
		
<div class="row pp-precheck">
	
	
	<!-- nothing is going to visible during first installation -->
	<?php if($this->installedVersion): ?>

		<div class="clearfix"><h4><?php echo JText::sprintf('COM_PPINSTALLER_INSTALLED_VERSION', $this->installedVersion); ?></h4></div>

	<?php endif; ?>
	
		
	<!-- if upgrade is available then show it otherwise not -->		
	<?php if($this->installedVersion && (version_compare($this->goingToInstall,$this->latestRelease['version']) == 0)) : ?>
	<div class="clearfix">
		<h4>
			<?php echo JText::sprintf('COM_PPINSTALLER_LATEST_RELEASE', $this->latestRelease['version'],$this->latestRelease['title']); ?>
		</h4>
	</div>
	<?php endif; ?>
	
	<div class="clearfix">
		<h4 class="pp-linkable" onclick=" jQuery('#requirements').slideToggle('slow');  jQuery(this).children('i').toggle();">
			<?php echo JText::_('COM_PPINSTALLER_REQUIREMENTS'); ?>
			<i class="pull-right glyphicon glyphicon-chevron-down " <?php echo (!$this->errorExist)? '':'style="display: none;"'; ?>"></i>
		</h4>
		<div id="requirements" <?php echo (!$this->errorExist)? 'style="display: none;"':''; ?>>
			
			<?php foreach ($this->results as $result) : ?>
				<ul class="row">
					<div class="col-md-11">
						<h5><?php echo JText::_($result['msg']); ?> </h5>
						<h6 class='text-muted'>
							<?php echo (isset($result['recommended'])) ?JText::_($result['recommended']):'';?>
						</h6>
					</div>
					<div class="col-md-1">
						<?php 
								
							$suffix = ($result['status'] == PPINSTALLER_SUCCESS_LEVEL) 
										? 'success' 
										: (($result['status']== PPINSTALLER_WARNING_LEVEL) ? 'warning': 'danger');
							
							$class  = 'label label-'.$suffix;

							switch ($result['status']) {
								case PPINSTALLER_SUCCESS_LEVEL 	: $status=JText::_('COM_PPINSTALLER_SUCCESS_LEVEL'); 	break;
								case PPINSTALLER_WARNING_LEVEL 	: $status=JText::_('COM_PPINSTALLER_WARNING_LEVEL'); 	break;
								case PPINSTALLER_ERROR_LEVEL 	: $status=JText::_('COM_PPINSTALLER_ERROR_LEVEL'); 		break;
								case PPINSTALLER_CRITICAL_LEVEL : $status=JText::_('COM_PPINSTALLER_CRITICAL_LEVEL'); 	break;
								default: $status=JText::_('COM_PPINSTALLER_SUCCESS_LEVEL');
							}
						?>
						<span class="<?php echo $class; ?>" title="<?php echo $suffix; ?>"><?php echo $status; ?></span>
					</div>
				</ul>
			<?php endforeach; ?>
			
		</div>
	</div>

	<?php if($this->installedVersion): ?>

			<!--it is not advisable to show restore point user might test it unnecessarily-->
			<div style="display:none;">
			<!-- Restore points -->	
			<?php if(isset($this->restorePoints) && !empty($this->restorePoints)) : ?>
				<div class="clearfix">
					<h4  class="pp-linkable" onclick=" jQuery('#somethingElse').slideToggle('slow');  jQuery(this).children('i').toggle();">
						<?php echo JText::_('COM_PPINSTALLER_RESTORE_POINTS'); ?>
						<i class="pull-right glyphicon glyphicon-chevron-down"></i>
					</h4>
					<div id="somethingElse" style="display: none;">
						
						<p>
							<?php echo JText::_('COM_PPINSTALLER_RESTORE_POINTS_CHOOSE'); ?>
						</p>
						<?php foreach ($this->restorePoints as $version => $support): ?>
						<div class="radio">
						  <label>
						  	<?php if($support) : ?>
						  	<input type="radio" name="restore" value="<?php echo $version;?>"/>
						    <?php else: ?>
						    <span class="label label-warning"><?php echo JText::_('COM_PPINSTALLER_VERSION_NOT_SUPPORT'); ?></span>
						    <?php endif; ?>
						    <?php echo JText::sprintf('COM_PPINSTALLER_RESTORE_TO',$version); ?>
						    
						  </label>
						</div>
					
						<?php endforeach; ?>
					</div>
				</div>
			<?php endif; ?>
			</div>
			
			<!-- LTS releases -->
			<div class="clearfix">
				<h4  class="pp-linkable" onclick=" jQuery('#ltsReleases').slideToggle('slow');  jQuery(this).children('i').toggle();">
					<?php echo JText::_('COM_PPINSTALLER_LTS_RELEASES'); ?>
					<i class="pull-right glyphicon glyphicon-chevron-down"></i>
				</h4>
				<div>
				<table class="table table-bordered" id="ltsReleases" style="display:none;">
					<tr>
						<th><?php echo JText::_('COM_PPINSTALLER_SERIES'); ?></th>
						<th><?php echo JText::_('COM_PPINSTALLER_SERIES_VERSION'); ?></th>
						<th><?php echo JText::_('COM_PPINSTALLER_SUPPORT'); ?></th>
					</tr>
					
					<?php foreach ($this->ltsReleases as $series => $release) :?>
					<tr>
						<td><?php echo $series; ?></td>
						<td><?php echo $release['version']; ?></td>
						
						<?php if($release['supported'] == false): ?>
							<td><span class="label label-default"><?php echo JText::_('COM_PPINSTALLER_VERSION_NOT_SUPPORT'); ?></span></td>
						<?php else: ?>
							<td>
								<span class="label label-primary"><?php echo JText::_('COM_PPINSTALLER_VERSION_SUPPORT'); ?></span>
								<?php if(version_compare($this->installedVersion,$release['version']) < 0): ?>
										<span class="label label-warning"><?php echo JText::_('COM_PPINSTALLER_VERSION_AVAILABLE'); ?></span>
								<?php endif; ?>
							</td>
						<?php endif; ?>
					</tr>
					<?php endforeach; ?>
				</table>
				</div>
			</div>


	<?php endif; ?>

	<?php if($this->needPrecheck): ?>
	<div>
		<?php echo $this->precheckTerms; ?>
		<div class="checkbox">
			<label>
				<input type="checkbox" onclick="return ppInstaller.precheckAgree(this);" > 
				<?php echo JText::_('COM_PPINSTALLER_PRECHECK_AGREE_ON_TERMS'); ?> 
			</label>
		</div>
	</div>
	<?php endif; ?>
	
	<!-- what is going to be, if nothing then do not show this -->
	<br />
	<?php if($this->errorExist): ?>
		<div class="clearfix alert alert-danger">
			<?php echo JText::_('COM_PPINSTALLER_FULLFILL_SPECIFIED'); ?>
			<strong><span class="text-danger"><?php echo JText::_('COM_PPINSTALLER_REQUIREMENTS'); ?></span></strong>
		</div>
	<?php else: ?>
		<?php if ( isset($this->needForRevert) && $this->needForRevert): ?>
		<div class="form-inline clearfix well">
			<span class="pull-right col-md-3">								
				<button class="btn btn-warning btn-lg" 
						data-loading-text="verifying..."
						data-complete-text="install"
	                	title="Revert Database" 
	                	onclick="return ppInstaller.executeTask({});"
	                	id ="ppInstaller_submit_button">
						<?php echo JText::_('COM_PPINSTALLER_REVERT_DATABASE'); ?>
				</button>
			</span>
		</div>
		<?php elseif ($this->goingToInstall): ?>
		<div id="ppinstallerCredential" class="form-inline">
			
			<div class="alert alert-danger hide" id="ppinstallerCredentialError"></div>
			<div class=" clearfix well">
			<span class="col-md-8">
				<div class="form-group">
					<label class="sr-only" for="exampleInputEmail2"><?php echo JText::_('COM_PPINSTALLER_USERNAME'); ?></label>
					<input type="email" class="form-control" id="ppinstallerUsername" placeholder="<?php echo JText::_('COM_PPINSTALLER_EMAIL_TITLE'); ?>" required value="<?php echo $this->ppinstallerUsername; ?>">
				</div>
				<div class="form-group">
					<label class="sr-only" for="exampleInputPassword2"><?php echo JText::_('COM_PPINSTALLER_PASSWORD'); ?></label>
					<input type="password" class="form-control" id="ppinstallerPassword" placeholder="<?php echo JText::_('COM_PPINSTALLER_PASSWORD_TITLE'); ?>" required value="<?php echo $this->ppinstallerPassword; ?>">
				</div>
				<h5 class="text-muted"><?php echo JText::_('COM_PPINSTALLER_CREDENTIAL_MSG'); ?></h5>
			</span>
			
			<span class="col-md-4">
				
					<input type="hidden" name="goingToInstall" id="goingToInstall" value="<?php echo $this->goingToInstall; ?>"/>
					
					<!-- installation button -->
					<button <?php echo ($this->needPrecheck)? 'disabled="true"' : ''; ?>
							class="btn btn-primary btn-lg" 
							data-loading-text="verifying..."
							data-complete-text="Install Payplans <?php echo $this->goingToInstall; ?>"
		                	title="Install Payplans <?php echo $this->goingToInstall; ?>" 
		                	onclick="return ppInstaller.credentialCheck();"
		                	id ="ppInstaller_submit_button">
							<?php echo JText::sprintf('COM_PPINSTALLER_INSTALL_PAYPLANS',$this->goingToInstall); ?>
					</button>
			</span>
			</div>
		</div>
			
		<?php else: ?>
			<div class="clearfix  alert alert-success">
				<strong class="col-md-10"><?php echo JText::sprintf('COM_PPINSTALLER_USER_VERSION',$this->latestRelease['version']); ?></strong>
				<span class='col-md-2 pull-right'><a href="<?php echo JRoute::_('index.php?option=com_payplans'); ?>" class='btn btn-success' ><?php echo JText::_('COM_PPINSTALLER_DASHBOARD'); ?></a></span>
			</div>
		<?php endif; ?>
	<?php endif; ?>
			
</div>
	
</form>
