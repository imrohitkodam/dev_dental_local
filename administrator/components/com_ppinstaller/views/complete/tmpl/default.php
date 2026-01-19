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

$redirectUrl = JRoute::_("index.php?option=com_ppinstaller");
define('ppglyphicon-0','activate glyphicon-refresh');
define('ppglyphicon-1','glyphicon-ok');
define('ppglyphicon--1','glyphicon-question-sign');


?>


<form action="<?php echo $redirectUrl; ?>" method="post" name="adminForm" id="adminForm" class="pp-adminForm" >
	
    <h3 class="ppinstaller-steps-error alert alert-danger hide clearfix" >
	</h3>
      
	<div class="ppinstaller-steps">
		<?php foreach ($this->completeTask as $task => $flag): ?>
		<h4>
			<span id="<?php echo $task; ?>" class="glyphicon glyphicon-ok <?php echo constant("ppglyphicon-{$flag}"); ?>"></span>
			<span><?php echo JText::_('COM_PPINSTALLER_'.$task); ?></span>
			<span class="label label-info"></span>
		</h4>
		<?php endforeach; ?>
	</div>

	<input type="hidden" name="view" 			value="<?php echo $this->nextView; ?>" />
	<input type="hidden" name="task" 			value="<?php echo $this->nextTask; ?>" />

</form>
