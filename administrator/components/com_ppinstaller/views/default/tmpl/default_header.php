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

?>
<script>
//Work realted when ppinstaller start
//and it need be called again and again at success callback
//because on page refresh it called autmatically but need to call when ajax request
(function($){
	$(document).ready(function(){
		<?php if(!$this->isUpgradable) :?>
			$('#ppinstaller-wait').show();
			ppInstaller.executeTask({'view':'requirements'});
		<?php else :?>
			$('#ppinstaller-upgrade-modal').modal({ keyboard: false, backdrop: 'static' });
			$('#ppinstaller-upgrade-modal').modal('show');
		<?php endif; ?>
		
	});
})(jQuery);
</script>

<?php 
if($this->isUpgradable) :
	echo $this->loadTemplate('upgrade');
endif;

?>
<head>
<title><?php echo JText::_('COM_PPINSTALLER'); ?></title>
</head>

<div class="well clearfix">
	<a href="http://www.readybytes.net/payplans.html" target="_blank" style="text-decoration: none;">
		<img class="pull-left col-xs-offset-3" src="<?php echo JURI::base(); ?>components/com_ppinstaller/assets/images/payplans-logo.png" />
		<span class="pull-left" style="color:#666666; font-size:30px; margin:10px 0 0 5px; "><?php echo JText::_('COM_PPINSTALLER'); ?></span>
	</a>
	<span class="pull-right col-md-1">
		<a id="exitinstaller" class="btn btn-default" href="<?php echo $this->exitUrl; ?>"><?php echo JText::_('COM_PPINSTALLER_EXIT'); ?></a>
	</span>
	<span class="pull-right col-md-offset-1">
		<a class="btn btn-primary" href="<?php echo $this->instructionUrl; ?>"><?php echo JText::_('COM_PPINSTALLER_INSTRUCTIONS'); ?></a>
	</span>
</div>

<div class="payplans container">

	<?php if(isset($this->error)):?>
	<?php echo $this->error; ?>
	<?php else:?>
	<!-- installation steps -->
	<div class="row">
		
		<div class="info-rounds">
			<ul class="clearfix">
				<li class="col-xs-3 requirements">
					<span class="circle"><b>0</b></span><br>
					<span class="nav-text"><?php echo JText::_('COM_PPINSTALLER_PRE_CHECKS'); ?></span>
				</li>
				<li class="col-xs-3 migrate">
					<span class="circle"><b>1</b></span><br>
					<span class="nav-text"><?php echo JText::_('COM_PPINSTALLER_BACKUP_MIGRATION'); ?></span>
				</li>
				<li class="col-xs-3 install">
					<span class="circle"><b>2</b></span><br>
					<span class="nav-text"><?php echo JText::_('COM_PPINSTALLER_INSTALLATION'); ?></span>
				</li>
				<li class="col-xs-3 complete">
					<span class="circle"><b>3</b></span><br>
					<span class="nav-text"><?php echo JText::_('COM_PPINSTALLER_COMPLETE'); ?></span>
				</li>
			</ul>
		</div>
		
	</div>
	
	<div class="clearfix col-xs-10 col-xs-offset-1" id="replacableTpl">
		<h2 id="ppinstaller-wait" style="display: none;" class="text-center text-info"><i class="glyphicon glyphicon-screenshot ppinstaller-spin"></i></h2>		
		<?php echo $this->internalTpl;?>
	</div>		
	<?php endif; ?>
</div>


<div class="text-center" style="margin-top : 150px;" >
		<p>
		<a href="http://www.readybytes.net" target="_blank">
			<img src="<?php echo JURI::base(); ?>components/com_ppinstaller/assets/images/readybytes-logo.png">
		</a>
		</p>
		<h6><?php echo JText::_('COM_PPINSTALLER_READYBYTES'); ?></h6>
</div>

<?php 