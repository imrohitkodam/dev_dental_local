<?php
/**
 * @version        1.10.0
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2011-2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die ;
$default_style = $this->config->default_style;
?>
<script type="text/javascript">
    var cookieVar = 'default_layout';
    <?php
    if((int) $default_style == 0)
    {
        ?>
        var defaultCookieLayout = 'list';
        <?php
    }else{
        ?>
        var defaultCookieLayout = 'grid';
        <?php
    }
    ?>
</script>
<h1 class="dm_title"><?php echo JText::_('EDOCMAN_SEARCH_RESULT'); ?></h1>
<form method="post" name="edocman_form" id="edocman_form" action="index.php">	
<?php
	if (count($this->items))
	{
		echo EDocmanHelperHtml::loadCommonLayout('common/documents_default.php', array('items' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'category' => $this->category ,'categoryId' => $this->categoryId,  'bootstrapHelper' => $this->bootstrapHelper ,'show_category' => $this->show_category));
		if ($this->pagination->total > $this->pagination->limit)
		{
		?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php
		}
	}
	else
	{
	?>
		<p><?php echo JText::_('EDOCMAN_NO_DOCS_FOUND'); ?></p>
	<?php
	}
?>
<input type="hidden" name="view" value="search" />	
<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
<input type="hidden" name="option" value="com_edocman" />
<input type="hidden" name="cid[]" value="0" id="document_id" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="document_id" id="document_id" value="" />
<?php echo JHtml::_('form.token'); ?>
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
