<?php
/**
 * @package     Edocman
 * @subpackage  Module Edocman Search Categories
 *
 * @copyright   Copyright (C) 2010 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
$app = JFactory::getApplication();
$Itemid = $app->getMenu()->getActive()->id;
?>
<div class="edocmansearch<?php echo $moduleclass_sfx; ?>">
    <form id="edocman_filter_category" name="edocman_filter_category" action="<?php echo JRoute::_('index.php?option=com_edocman&task=search&Itemid='.$itemId);  ?>" method="post">
        <div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?> filter_categories">
            <div class="<?php echo $bootstrapHelper->getClassMapping('span12')?>" id="level0">
                <?php echo $listCat; ?>
            </div>
        </div>
        <div id="check"></div>
        <input type="submit" class="btn btn-primary button search_button" value="Search">
        <input type="hidden" name="filter_category_id" class="filter_category_id" value="<?php echo $session->get('currentCategory'); ?>">
        <script language="javascript">
            function getListCategories(catId,level){
                if(catId == "" && level != 0){
                    level -= 1;
                    catId = jQuery("#categoriesId"+level).val();
                }
                jQuery(".filter_category_id").val(catId);
                var maxlevel = <?php echo $maxlevel ?>;
                var data  ={
                            'catid':catId,
                            'level':level,
                            'maxlevel':<?php echo $maxlevel ?>
                            };
                var url   = "index.php?option=com_ajax&module=edocman_filtercategories&method=getChildCategories&format=html&Itemid=<?php echo $Itemid; ?>";
                jQuery.ajax({
                    type:"POST",
                    url:url,
                    data:data,
                    success: function(html){
                        jQuery('#level'+level).nextAll('div').remove();
                        if(html != ""){
                            jQuery('.filter_categories').append(html);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                       // alert(textStatus);
                    }
                });
            }
            jQuery(document).ready(function($){
                var data = {'maxlevel':'<?php echo $maxlevel ?>'};
                var url2   = "index.php?option=com_ajax&module=edocman_filtercategories&method=setDropdown&format=html&Itemid=<?php echo $Itemid; ?>";
                $.ajax({
                    type:"POST",
                    url:url2,
                    data:data,
                    success: function(html){
                        if(html != ""){
                            $(".filter_categories").empty();
                            $('.filter_categories').append(html);
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                       //alert(textStatus);
                    }
                });
            });
        </script>
    </form>
</div>