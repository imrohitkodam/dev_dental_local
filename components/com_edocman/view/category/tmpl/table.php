<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage  EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$user = JFactory::getUser();
?>
<form method="post" name="adminForm" id="adminForm" action="<?php echo JRoute::_('index.php?option=com_edocman&view=category&id='.$this->category->id.'&layout=table&Itemid='.$this->Itemid); ?>">
	<div id="edocman-category-page-table" class="edocman-container">
		<?php
			$imgUrl = '';
			if ($this->category)
			{
				if ($this->category->image && JFile::exists(JPATH_ROOT.'/media/com_edocman/category/thumbs/'.$this->category->image))
				{
					$imgUrl = JUri::base().'media/com_edocman/category/thumbs/'.$this->category->image;
				}
				else
				{
					//if (!isset($this->config->show_default_category_thumbnail) || $this->config->show_default_category_thumbnail)
					//{
						//$imgUrl = JUri::base().'components/com_edocman/assets/images/icons/32x32/folder.png' ;
					//}
					//else
					//{
						//$imgUrl = '';
					//}
				}
			?>
				<div id="edocman-category">
					<h1 class="edocman-page-heading">
					<?php
						if($imgUrl == ''){
							?>
							<i class="edicon edicon-folder-open"></i>
							<?php
						}
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
						<img class="edocman-thumb-left" src="<?php echo $imgUrl; ?>" alt="<?php echo $this->category->title; ?>" />
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
			if (count($this->categories) && $this->combine_categories == 0)
			{
				echo EDocmanHelperHtml::loadCommonLayout('common/categories_table.php', array('categories' => $this->categories, 'categoryId' => $this->category->id, 'config' => $this->config, 'bootstrapHelper' => $this->bootstrapHelper, 'Itemid' => $this->Itemid , 'subscat' => $this->show_subcat));
			}
			if ($this->config->show_sort_options && count($this->items))
			{
				echo EDocmanHelperHtml::loadCommonLayout('common/category_header.php', array('lists' => $this->lists, 'showLayoutswitcher' => false, 'bootstrapHelper' => $this->bootstrapHelper));
			}
			if (count($this->items))
			{
				echo EDocmanHelperHtml::loadCommonLayout('common/documents_table.php', array('items' => $this->items, 'config' => $this->config, 'Itemid' => $this->Itemid, 'category' => $this->category, 'categoryId' => (int) $this->categoryId, 'bootstrapHelper' => $this->bootstrapHelper));
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
	</div>
</form>