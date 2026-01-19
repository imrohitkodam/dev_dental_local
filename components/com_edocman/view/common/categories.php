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

if (count($categories))
{
	if ($categoryId)
	{
	?>
		<h2 class="edocman-heading"><i class="edicon edicon-folder-open"></i>&nbsp;<?php echo JText::_('EDOCMAN_SUB_CATEGORIES'); ?></h2>
	<?php
	}
	if((int) $number_categories > 0)
    {
        $numberColumns = $number_categories;
    }
	elseif (isset($config->number_subcategories))
	{
		$numberColumns = $config->number_subcategories ;
	}
	else
	{
		$numberColumns = 1 ;
	}
	$span = intval(12 / $numberColumns);
	if ($span != 12)
	{
		$spanClass = ' '.$bootstrapHelper->getClassMapping('span'.$span);
	}
	else
	{
		$spanClass = '';
	}
	$rowFluidClass = $bootstrapHelper->getClassMapping('row-fluid');
	?>
	<div id="edocman-categories">
		<div class="<?php echo $rowFluidClass; ?> clearfix">
		<?php
		$j = 0;
		for ($i = 0 , $n = count($categories) ; $i < $n ; $i++)
		{
			$category = $categories[$i];
			if (!$config->show_empty_cat && !$category->total_documents)
			{
				continue ;
			}
			if ($category->image && JFile::exists(JPATH_ROOT.'/media/com_edocman/category/thumbs/'.$category->image))
			{
				$imgUrl = JUri::base().'media/com_edocman/category/thumbs/'.$category->image;
			}
			else
			{
				if (!isset($config->show_default_category_thumbnail) || $config->show_default_category_thumbnail)
				{
					$imgUrl = JUri::base().'components/com_edocman/assets/images/icons/32x32/folder.png' ;
				}
				else
				{
					$imgUrl = '';
				}
			}
			//if ($numberColumns != 1 && ($i % $numberColumns == 0))
			//{
			?>
			<!--<div class="clearfix">-->
			<?php
			//}
			$j++;
			?>
			<div class="edocman-category<?php echo $spanClass; ?>">
				<div class="edocman-box-heading">
					<h3 class="edocman-category-title">
						<a href="<?php echo JRoute::_(EDocmanHelperRoute::getCategoryRoute($category->id, $Itemid)); ?>" class="edocman-category-title-link" style="display: inline-block;">
							<?php
							if($config->show_icon_beside_title)
							{
							?>
								<i class="edicon edicon-folder"></i>
							<?php } ?>
							<?php
								echo $category->title;
								if ($config->show_number_documents)
								{
								?>
									<small>( <?php echo $category->total_documents ;?> <?php echo $category->total_documents > 1 ? JText::_('EDOCMAN_DOCUMENTS') :  JText::_('EDOCMAN_DOCUMENT') ; ?> )</small>
								<?php
								}
							?>
						</a>
						<?php
							if ($config->enable_rss)
							{
							?>
								<span class="edocman-rss-icon">
									<a href="<?php echo JRoute::_('index.php?option=com_edocman&view=category&id='.$category->id.'&format=feed&type=rss'); ?>"><img src="<?php echo JUri::root().'/components/com_edocman/assets/images/rss.png' ?>" /></a>
								</span>
							<?php
							}
						?>
					</h3>
				</div>
				<?php
				if((int)$subscat == 1)
				{
                    if(($category->description || $imgUrl) && ($config->show_subcategory_description))
                    {
                    ?>
                        <div class="edocman-description clearfix">
                            <?php
                                if ($imgUrl)
                                {
                                ?>
                                    <img class="edocman-thumb-left" src="<?php echo $imgUrl; ?>" alt="<?php echo $category->title; ?>" />
                                <?php
                                }
                                if((int)$config->cnumber_words == 0){
                                    echo $category->description;
                                }else{
                                    $description = strip_tags($category->description);
                                    if((int)$config->number_cwords > 0){
                                        $descriptionArr = explode(" ",$description);
                                        if(count($descriptionArr) > (int)$config->cnumber_words){
                                            for($d = 0;$d < (int)$config->number_cwords - 1;$d++){
                                                echo $descriptionArr[$d]." ";
                                            }
                                            echo "..";
                                        }else{
                                            echo $description;
                                        }
                                    }else{
                                        echo $description;
                                    }
                                }
                            ?>
                        </div>
                    <?php
                    }
				}
				elseif($category->description || $imgUrl)
				{
				?>
					<div class="edocman-description clearfix">
						<?php
							if ($imgUrl)
							{
							?>
								<img class="edocman-thumb-left" src="<?php echo $imgUrl; ?>" alt="<?php echo $category->title; ?>" />
							<?php
							}
							$description = strip_tags($category->description);
							if((int)$config->number_cwords > 0){
								$descriptionArr = explode(" ",$description);
								if(count($descriptionArr) > (int)$config->cnumber_words){
									for($d = 0;$d < (int)$config->number_cwords - 1;$d++){
										echo $descriptionArr[$d]." ";
									}
									echo "..";
								}else{
									echo $description;
								}
							}else{
								echo $description;
							}
						?>
					</div>
				<?php
				}
				?>
			</div>
		<?php
			if($j == $numberColumns)
			{
				?>
				</div><div class="<?php echo $rowFluidClass; ?> clearfix">
				<?php
				$j = 0;
			}
		}
		?>
		</div>
	</div>
<?php
}