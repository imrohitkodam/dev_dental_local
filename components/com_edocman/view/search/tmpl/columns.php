<?php
/**
 * @version		   1.9.8
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Dang Thuc Dam
 * @copyright	   Copyright (C) 2011 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */ 
defined('_JEXEC') or die ;
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
if(!JFolder::exists(JPATH_ROOT.'/media/com_edocman/document/medium')){
    JFolder::create(JPATH_ROOT.'/media/com_edocman/document/medium');
    JFile::copy(JPATH_ROOT.'/media/com_edocman/index.html',JPATH_ROOT.'/media/com_edocman/document/medium/index.html');
}
$user           = JFactory::getUser();
$userId         = $user->get('id');
?>
<form method="post" name="edocman_form" id="edocman_form" action="index.php">	
	<h1 class="dm_title"><?php echo JText::_('EDOCMAN_SEARCH_RESULT'); ?></h1>	
	<!-- Documents List -->
	<?php if(count($this->items)) {
        echo EDocmanHelperHtml::loadCommonLayout('common/documents_columns.php', array('items' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'category' => $this->category ,'categoryId' => $this->categoryId,  'bootstrapHelper' => $this->bootstrapHelper ,'show_category' => $this->show_category));
        if ($this->pagination->total > $this->pagination->limit)
        {
        ?>
            <div class="pagination">
                <?php echo $this->pagination->getPagesLinks(); ?>
            </div>
        <?php
        }
	?>
	<?php } else { ?>
        <p><?php echo JText::_('EDOCMAN_NO_DOCS_FOUND'); ?></p>
	<?php } ?>
	<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
	<input type="hidden" name="view" value="category" />	
	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
	<input type="hidden" name="option" value="com_edocman" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="0" id="document_id" />
	<input type="hidden" name="direction" value="<?php echo $this->direction; ?>" />
	<?php echo JHtml::_('form.token'); ?>
	<script language="javascript">
		function changeDirection(newDirection) {
			var form = document.edocman_form ;
			form.direction.value = newDirection ;
			form.submit();
		}	
	</script>
</form>
<script type="text/javascript">
	function changeDirection(newDirection) {
		var form = document.edocman_form ;
		form.direction.value = newDirection ;
		form.submit();
	}
	function deleteConfirm(id) {
		var msg = "<?php echo JText::_('EDOCMAN_DELETE_CONFIRM'); ?>";
		if (confirm(msg)) {
			var form = document.edocman_form ;
			form.task.value = 'documents.delete';
			document.getElementById('document_id').value = id;
			form.submit();
		}
	}

	function publishConfirm(id, published) {
		var msg, task ;
		if (published) {
			msg = "<?php echo JText::_("EDOCMAN_PUBLISH_CONFIRM"); ?>";
			task = "documents.publish" ;
		} else {
			msg = "<?php echo JText::_("EDOCMAN_UNPUBLISH_CONFIRM"); ?>";
			task = "documents.unpublish" ;
		}
		if (confirm(msg)) {
			var form = document.edocman_form ;
			form.task.value = task ;
			document.getElementById('document_id').value = id;
			form.submit();
		}
	}
</script>