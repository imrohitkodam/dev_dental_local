<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_ppinstaller
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		3.1
 */

// no direct access
defined('_JEXEC') or die;?>

<div class="row">

<h3 class="col-xs-12 pull-left"><?php echo JText::_('COM_PPINSTALLER_REVERT_COMPLETE');  ?></h3>
<h5 class="col-xs-12 pull-left"><?php echo JText::_('COM_PPINSTALLER_REVERT_COMPLETE_SUGGESTION');  ?></h5>

<div class="clearfix">&nbsp;</div>
<hr>
<div class="col-xs-9 ppinstaller-steps-error">
	<button data-target="#myModal" data-toggle="modal" class="btn btn-lg btn-warning"><?php echo JText::_('COM_PPINSTALLER_ASK_FOR_HELP');  ?></button>
</div>

<div class="col-xs-3">
	<a class="btn btn-lg btn-primary pull-left" href="<?php echo JRoute::_('index.php?option=com_ppinstaller'); ?>">
		<?php echo JText::_('COM_PPINSTALLER_RUN_AGAIN'); ?>
		<span class="glyphicon  glyphicon glyphicon-repeat"></span>
	</a>
</div>

</div>
