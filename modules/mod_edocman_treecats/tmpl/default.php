<?php
	defined('_JEXEC') or die;
	if (count($list)) {		
	?>
        <div class="k-tree k-js-category-tree" style="position: relative;">
            <ul class="jqtree_common jqtree-tree" role="tree">
                <?php
                foreach ($list as $row)
                {
					if(in_array($row->id, $parentArr))
					{
						$arrowClass	= "edicon-circle-down";
						$folderClass= "edicon-folder-open";
					}
					else
					{
						$arrowClass	= "edicon-circle-right";
						$folderClass= "edicon-folder";
					}

					$extraClass = "";
					if($category_id == $row->id){
						$extraClass = "jqtree-selected";
					}
                ?>
                    <li class="jqtree_common <?php echo $extraClass;?>" role="presentation">
                        <div class="jqtree-element jqtree_common" role="presentation">
							<?php
							if(count($row->subcats) > 0)
							{
								?>
								<a class="jqtree-toggler jqtree_common jqtree-toggler-left jqtree-closed" role="presentation" aria-hidden="true" id="aEdocmancategory<?php echo $row->id;?>">
									<span class="edicon <?php echo $arrowClass;?>" id="arrowEdocmancategory<?php echo $row->id;?>"></span>
								</a>
								<?php
							}
							else
							{
								?>
	                            <i class="jqtree-whitespace"></i>
								<?php
							}
							?>
                            <span class="jqtree_common jqtree-icon edicon <?php echo $folderClass;?>" id="folderEdocmancategory<?php echo $row->id;?>">
                            </span>
                            <span class="jqtree-title jqtree_common" role="treeitem" aria-level="<?php echo $row->level;?>" aria-selected="true" aria-expanded="true" tabindex="0">
								<?php
								if(count($row->subcats) > 0)
								{
									?>
									<a href="javascript:void(0);" id="a1Edocmancategory<?php echo $row->id;?>" title="<?php echo $row->title; ?>">
										<?php echo $row->title; ?>
									</a>
									<?php
								}
								else
								{
									?>
									<a href="<?php echo EDocmanHelperRoute::getCategoryRoute($row->id, $itemId); ?>" title="<?php echo $row->title; ?>"><?php echo $row->title; ?></a>
									<?php
								}
								?>
                            </span>
                        </div>
						<?php
						if(count($row->subcats) > 0){
							ModEdocmanTreeCatsHelper::showSubCats($row->id, $row->subcats, 1, $itemId, $parentArr, $category_id);
						}
						?>
                    </li>
					<script type="text/javascript">
					jQuery("#aEdocmancategory<?php echo $row->id;?>").click(function() {
						jQuery("#edocmancategory<?php echo $row->id;?>").toggle( "fast", function() {
							if(jQuery("#arrowEdocmancategory<?php echo $row->id;?>").hasClass('edicon-circle-right')){
								jQuery("#arrowEdocmancategory<?php echo $row->id;?>").removeClass('edicon-circle-right').addClass('edicon-circle-down');
							}else{
								jQuery("#arrowEdocmancategory<?php echo $row->id;?>").removeClass('edicon-circle-down').addClass('edicon-circle-right');
							}
							if(jQuery("#folderEdocmancategory<?php echo $row->id;?>").hasClass('edicon-folder')){
								jQuery("#folderEdocmancategory<?php echo $row->id;?>").removeClass('edicon-folder').addClass('edicon-folder-open');
							}else{
								jQuery("#folderEdocmancategory<?php echo $row->id;?>").removeClass('edicon-folder-open').addClass('edicon-folder');
							}
					    });
					});
					jQuery("#a1Edocmancategory<?php echo $row->id;?>").click(function() {
						jQuery("#edocmancategory<?php echo $row->id;?>").toggle( "fast", function() {
							if(jQuery("#arrowEdocmancategory<?php echo $row->id;?>").hasClass('edicon-circle-right')){
								jQuery("#arrowEdocmancategory<?php echo $row->id;?>").removeClass('edicon-circle-right').addClass('edicon-circle-down');
							}else{
								jQuery("#arrowEdocmancategory<?php echo $row->id;?>").removeClass('edicon-circle-down').addClass('edicon-circle-right');
							}
							if(jQuery("#folderEdocmancategory<?php echo $row->id;?>").hasClass('edicon-folder')){
								jQuery("#folderEdocmancategory<?php echo $row->id;?>").removeClass('edicon-folder').addClass('edicon-folder-open');
							}else{
								jQuery("#folderEdocmancategory<?php echo $row->id;?>").removeClass('edicon-folder-open').addClass('edicon-folder');
							}
					    });
					});
					</script>
                <?php
                }
                ?>
            </ul>
        </div>
<?php
	}
?>					
