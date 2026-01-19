<?php
/**
 * @version        1.9.8
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Dang Thuc Dam
 * @copyright	   Copyright (C) 2011-2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$user = JFactory::getUser();
?>
<script type="text/javascript">
	var cookieVar = 'columns_layout';
	var defaultCookieLayout = 'grid';
</script>
<div id="edocman-category-page-columns" class="edocman-container">
	<form method="post" name="edocman_form" id="edocman_form" action="<?php echo JRoute::_('index.php?option=com_edocman&view=category&layout=columns&id='.$this->category->id.'&Itemid='.$this->Itemid); ?>">
		<?php
		if ($this->category)
		{
			if ($this->category->image && JFile::exists(JPATH_ROOT.'/media/com_edocman/category/thumbs/'.$this->category->image))
			{
				$imgUrl = JUri::base().'media/com_edocman/category/thumbs/'.$this->category->image;
			}
			else
			{
				if (!isset($this->config->show_default_category_thumbnail) || $this->config->show_default_category_thumbnail)
				{
					$imgUrl = JUri::base().'components/com_edocman/assets/images/icons/32x32/folder.png' ;
				}
				else
				{
					$imgUrl = '';
				}
			}
			?>
			<div id="edocman-category">
				<h1 class="edocman-page-heading">
				<?php
					echo $this->category->title;
					if($this->config->enable_rss)
					{
					?>
						<span class="edocman-rss-icon"><a href="<?php echo JRoute::_('index.php?option=com_edocman&view=category&id='.$this->category->id.'&format=feed&type=rss'); ?>"><img src="<?php echo JUri::root().'/components/com_edocman/assets/images/rss.png' ?>" /></a></span>
					<?php	
					}
					if ($user->authorise('core.create', 'com_edocman.category.'.$this->category->id)) 
					{
					?>
						<span style="float: right;"><a href="<?php echo JRoute::_('index.php?option=com_edocman&view=document&layout=edit&catid=' . $this->category->id . '&Itemid=' . $this->Itemid); ?>" class="edocman_upload_link btn btn-primary"><i class="edicon edicon-upload"></i>&nbsp;<?php echo JText::_('EDOCMAN_UPLOAD'); ?></a></span>
					<?php                        
					}	 
				?>      
				</h1>
				<?php
				if ($imgUrl)
				{
				?>
					<img class="edocman-thumb-left img-polaroid" src="<?php echo $imgUrl; ?>" alt="<?php echo $this->category->title; ?>" />
				<?php
				}
				if($this->category->description != '')
				{
				?>
					<div class="edocman-description"><?php echo $this->category->description;?></div>
				<?php
				}
				?>
			</div>
			<div class="clearfix"></div>
		<?php
		}
		else
		{
			if ($this->params->get('show_page_heading', 0))
			{
			?>
				<h1 class="edocman-page-heading"><?php echo $this->params->get('page_heading'); ?></h1>
			<?php
			}
		}
		if (count($this->categories))
		{
			echo EDocmanHelperHtml::loadCommonLayout('common/categories.php', array('categories' => $this->categories, 'categoryId' => $this->category->id, 'config' => $this->config, 'bootstrapHelper' => $this->bootstrapHelper, 'Itemid' => $this->Itemid, 'subscat' => $this->show_subcat));
		}
		if ($this->config->show_sort_options && count($this->items))
		{
			echo EDocmanHelperHtml::loadCommonLayout('common/category_header.php', array('lists' => $this->lists, 'showLayoutswitcher' => false, 'bootstrapHelper' => $this->bootstrapHelper));
		}
		if (count($this->items))
		{
			echo EDocmanHelperHtml::loadCommonLayout('common/documents_columns.php', array('items' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'category' => $this->category, 'bootstrapHelper' => $this->bootstrapHelper));
		}
		if ($this->pagination->total > $this->pagination->limit)
		{
		?>
			<div class="pagination">
				<?php echo $this->pagination->getPagesLinks(); ?>
			</div>
		<?php
		}
		?>
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
		<input type="hidden" name="direction" value="<?php echo $this->state->filter_order_Dir; ?>" />
		<input type="hidden" name="cid[]" value="0" id="document_id" />
		<input type="hidden" name="category_id" value="<?php echo $this->category->id ; ?>" />
		<input type="hidden" name="Itemid" value="<?php echo $this->Itemid ; ?>" />
		<input type="hidden" name="option" value="com_edocman" />
        <input type="hidden" name="task" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>