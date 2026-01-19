<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JToolbarHelper::title(JText::_('EDOCMAN_REMOVE_ORPHAN_DOCUMENTS'),'delete');
JToolbarHelper::cancel();
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=documents'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
    <div class="span12" style="border:1px solid #397DB7;background: #5AA0E1;padding:10px;color:white;text-align:center;">
        <strong>Notice</strong>: Do you want to remove Orphan documents. <strong>Orphan Documents</strong> are documents that aren't belong to any Categories or Categories of them have been removed before.
        <div class="clearfix" style="height:15px;"></div>
        <a href="index.php?option=com_edocman&task=document.removeorphan" class="btn"><i class="icon-delete"></i> Yes, I am agree</a>
        &nbsp;
        <a href="index.php?option=com_edocman" class="btn btn-danger"><i class="icon-cancel"></i>No, I do not agree</a>
    </div>
</div>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>