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
		<a href="<?php echo $this->startUrl; ?>" class="btn btn-success "><?php echo JText::_('COM_PPINSTALLER_START'); ?></a>	
	</span>
</div>

<div class="payplans container">
<?php echo $this->instructions; ?>
<br />
<div>
	<span class="pull-right">
		<a href="<?php echo $this->startUrl; ?>" class="btn btn-success btn-lg"><?php echo JText::_('COM_PPINSTALLER_START'); ?></a>
	</span>
</div>
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
