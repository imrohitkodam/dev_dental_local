<?php
/**
 * @version        1.11.1
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
?>
<div id="edocman-categories-page" class="edocman-container">
	<?php
		if (!$this->categoryId)
		{
			if ($this->params->get('page_heading'))
			{
				$heading = $this->params->get('page_heading');
			}
			else
			{
				$heading = JText::_('EDOCMAN_CATEGORIES');
			}
		?>
			<h1 class="edocman-page-heading"><i class="edicon edicon-folder-open"></i>&nbsp;<?php echo $heading; ?></h1>
		<?php
		}
		else
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
				<h1 class="edocman-page-heading"><?php echo $this->category->title;?><i class="icon-rss"></i></h1>
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
						<div class="edocman-description">
							<?php echo $this->category->description; ?>
						</div>
					    <?php
					}
				?>
			</div>
		<?php
		}
		$categories_layout = $this->params->get('categories_layout','default');
		if($categories_layout == 'default')
        {
            $categories_layout = 'categories.php';
        }
        else
        {
            $categories_layout = 'categories_table.php';
        }
		echo EDocmanHelperHtml::loadCommonLayout('common/'.$categories_layout, array('categories' => $this->items, 'categoryId' => $this->categoryId, 'config' => $this->config, 'bootstrapHelper' => $this->bootstrapHelper, 'Itemid' => $this->Itemid, 'subscat' => 1 , 'number_categories' => $this->config->number_categories_per_row));
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