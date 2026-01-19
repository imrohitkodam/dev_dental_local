<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
class ModEdocmanTreeCatsHelper{
    static function getCategories($parentId, $usergroupSql, & $returnCategories ){
        $db			= JFactory::getDbo();
        $user		= JFactory::getUser();
		$userId		= $user->id;
        $query		= $db->getQuery(true);
        $query->select('id, title, parent_id, level')
            ->from('#__edocman_categories AS tbl')
            ->where('published = 1');
        if (!$user->authorise('core.admin'))
        {
            $query->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
        }
        if($parentId > 0)
        {
            $query->where("tbl.parent_id = ".$parentId);
        }
        else
        {
            $query->where("tbl.parent_id = 0");
        }
        $query->order('ordering');
        $db->setQuery($query);
        $rows = $db->loadObjectList();
        foreach ($rows as $row){
            $count                                  = count($returnCategories);
            $returnCategories[$count]->id           = $row->id;
            $returnCategories[$count]->title        = $row->title;
            $returnCategories[$count]->parent_id    = $row->parent_id;
			$returnCategories[$count]->level		= $row->level;
			$sub_cats								= self::findSubCats($row->id);
			if(count($sub_cats) > 0){
				$returnCategories[$count]->subcats	= self::repeatSubCats($sub_cats);
			}else{
				$returnCategories[$count]->subcats	= array();
			}
        }
		return $returnCategories;
    }

	static function repeatSubCats($sub_cats){
        $returnCategories = array();
		if(count($sub_cats) > 0){
			$count = 0;
			foreach ($sub_cats as $row){
				$returnCategories[$count]->id           = $row->id;
				$returnCategories[$count]->title        = $row->title;
				$returnCategories[$count]->parent_id    = $row->parent_id;
				$returnCategories[$count]->level		= $row->level;
				$sub_cats1								= self::findSubCats($row->id);
				if(count($sub_cats1) > 0){
					$returnCategories[$count]->subcats	= self::repeatSubCats($sub_cats1);
				}else{
					$returnCategories[$count]->subcats	= array();
				}
				$count++;
			}
		}
		return $returnCategories;
	}

	static function findSubCats($parent_id){
		$db			= JFactory::getDbo();
        $user		= JFactory::getUser();
		$userId		= $user->id;
        $query		= $db->getQuery(true);
        $query->select('id, title, parent_id, level')
            ->from('#__edocman_categories AS tbl')
            ->where('published = 1');
        if (!$user->authorise('core.admin'))
        {
            $query->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
        }
        if($parent_id > 0)
        {
            $query->where("tbl.parent_id = ".(int)$parent_id);
        }
        $query->order('ordering');
        $db->setQuery($query);
		return $db->loadObjectList();
	}


	static function showSubCats($cat_id, $categories, $level, $itemId, $parentArr, $category_id){
		if(in_array($cat_id, $parentArr))
		{
			$style		= "display:block;";
			$arrowClass	= "edicon-circle-down";
			$folderClass= "edicon-folder-open";
		}
		else
		{
			$style		= "display:none;";
			$arrowClass	= "edicon-circle-right";
			$folderClass= "edicon-folder";
		}

		?>
		<ul class="jqtree_common " role="group" style="<?php echo $style; ?>" id="edocmancategory<?php echo $cat_id; ?>">
			<?php
			$level++;
			foreach($categories as $category){
				$extraClass = "";
				if($category_id == $category->id){
					$extraClass = "jqtree-selected";
				}
				?>
				<li class="jqtree_common <?php echo $extraClass;?>" role="presentation">
					<div class="jqtree-element jqtree_common" role="presentation">
						<?php
						if($level > 1){
							for($i=0;$i < $level - 1;$i++){
								?>
									<i class="jqtree-whitespace"></i>
								<?php
							}
						}
						?>
						<?php
						if(count($category->subcats) > 0)
						{
							?>
							<a class="jqtree-toggler jqtree_common jqtree-toggler-left jqtree-closed" role="presentation" aria-hidden="true" id="aEdocmancategory<?php echo $category->id;?>">
								<span class="edicon <?php echo $arrowClass;?>" id="arrowEdocmancategory<?php echo $category->id;?>"></span>
							</a>
							<?php
						}
						else
						{
							?>
							<i class="jqtree-whitespace">|-</i>
							<?php
						}
						?>
						<span class="jqtree_common jqtree-icon edicon <?php echo $folderClass;?>" id="folderEdocmancategory<?php echo $category->id;?>">
						</span>
						<span class="jqtree-title jqtree_common" role="treeitem" aria-level="<?php echo $level;?>" aria-selected="true" aria-expanded="true" tabindex="0">
							<?php
							if(count($category->subcats) > 0)
							{
								?>
								<a href="javascript:void(0);" id="a1Edocmancategory<?php echo $category->id;?>" title="<?php echo $category->title; ?>">
									<?php echo $category->title; ?>
								</a>
								<?php
							}
							else
							{
								?>
								<a href="<?php echo EDocmanHelperRoute::getCategoryRoute($category->id, $itemId); ?>" title="<?php echo $category->title; ?>"><?php echo $category->title; ?></a>
								<?php
							}
							?>
						</span>
					</div>
					<?php
					if(count($category->subcats) > 0){
						self::showSubCats($category->id, $category->subcats, $level, $itemId, $parentArr, $category_id);
					}
					?>
					<script type="text/javascript">
					jQuery("#aEdocmancategory<?php echo $category->id;?>").click(function() {
						jQuery("#edocmancategory<?php echo $category->id;?>").toggle( "fast", function() {
							if(jQuery("#arrowEdocmancategory<?php echo $category->id;?>").hasClass('edicon-circle-right')){
								jQuery("#arrowEdocmancategory<?php echo $category->id;?>").removeClass('edicon-circle-right').addClass('edicon-circle-down');
							}else{
								jQuery("#arrowEdocmancategory<?php echo $category->id;?>").removeClass('edicon-circle-down').addClass('edicon-circle-right');
							}
							if(jQuery("#folderEdocmancategory<?php echo $category->id;?>").hasClass('edicon-folder')){
								jQuery("#folderEdocmancategory<?php echo $category->id;?>").removeClass('edicon-folder').addClass('edicon-folder-open');
							}else{
								jQuery("#folderEdocmancategory<?php echo $category->id;?>").removeClass('edicon-folder-open').addClass('edicon-folder');
							}
					    });
					});
					jQuery("#a1Edocmancategory<?php echo $category->id;?>").click(function() {
						jQuery("#edocmancategory<?php echo $category->id;?>").toggle( "fast", function() {
							if(jQuery("#arrowEdocmancategory<?php echo $category->id;?>").hasClass('edicon-circle-right')){
								jQuery("#arrowEdocmancategory<?php echo $category->id;?>").removeClass('edicon-circle-right').addClass('edicon-circle-down');
							}else{
								jQuery("#arrowEdocmancategory<?php echo $category->id;?>").removeClass('edicon-circle-down').addClass('edicon-circle-right');
							}
							if(jQuery("#folderEdocmancategory<?php echo $category->id;?>").hasClass('edicon-folder')){
								jQuery("#folderEdocmancategory<?php echo $category->id;?>").removeClass('edicon-folder').addClass('edicon-folder-open');
							}else{
								jQuery("#folderEdocmancategory<?php echo $category->id;?>").removeClass('edicon-folder-open').addClass('edicon-folder');
							}
					    });
					});
					</script>
				</li>
				<?php
			}
			?>
		</ul>
		<?php
	}


	static function getParentCategory($child_id, & $parentArr)
	{
		$db			= JFactory::getDbo();
		$user		= JFactory::getUser();
		$userId		= $user->id;
		$query		= $db->getQuery(true);
		$query->select('parent_id')
            ->from('#__edocman_categories AS tbl')
            ->where('published = 1')
			->where('id = '.$child_id);
        if (!$user->authorise('core.admin'))
        {
            $query->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
        }
        $db->setQuery($query);
		$parent_id = $db->loadResult();
		if($parent_id > 0)
		{
			$parentArr[count($parentArr)] = $parent_id;
			self::getParentCategory($parent_id, $parentArr);
		}
	}
}
?>