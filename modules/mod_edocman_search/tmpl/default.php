<?php
/**
 * @package     Edocman
 * @subpackage  Module Edocman Search
 *
 * @copyright   Copyright (C) 2010 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<?php // no direct access
$output = '<input name="filter_search" class="'.$input_style.'" id="search_edocman_box" maxlength="50"  type="text"  value="'.$text.'"  onblur="if(this.value==\'\') this.value=\''.$defaultText.'\';" onfocus="if(this.value==\''.$defaultText.'\') this.value=\'\';" />';
?>
<div class="edocmansearch<?php echo $moduleclass_sfx; ?>">
<form id="edocman_search" name="edocman_search" action="<?php echo JRoute::_('index.php?option=com_edocman&task=search&Itemid='.$itemId);  ?>" method="post">
	<table width="100%" class="search_table">
		<tr>
			<td>
				<?php echo $output; ?>
			</td>
		</tr>
		<?php
		if((int) $default_category_id == 0){
		?>
		<tr>
			<td>
				<?php echo modEDocmanSearchHelper::categoryList($categoryId,$input_style); ?>
			</td>
		</tr>
		<?php
		}else{
			?>
			<input type="hidden" name="filter_category_id" id="filter_category_id" value="<?php echo $default_category_id;?>" />
			<?php
		}
		
		if ($file_type){ // file type
			?>
			<tr>
				<td>
					<div class="row-fluid">
					<?php
					$i = 0;
					foreach($file_type_array as $type){
						$i++;
						$type = trim($type);
						if(in_array($type,$fileType)){
							$checked = "checked";
						}else{
							$checked = "";
						}
						?>
						<div class="span6">
							<input type="checkbox" name="fileType[]" value="<?php echo $type;?>" <?php echo $checked;?>>&nbsp;<?php echo $type; ?>
						</div>
						<?php
						if($i == 2){
							$i = 0;
							?>
							</div><div class="row-fluid">
							<?php
						}
					}
					?>
					</div>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td>
				<input type="button" class="btn btn-primary button search_button" value="<?php echo JText::_('EDOCMAN_SEARCH'); ?>" onclick="EDocmanSearchData();" />
			</td>
		</tr>
	</table>

	<script language="javascript">
		function EDocmanSearchData()
		{
			var form = document.edocman_search ;
			if (form.filter_search.value == '<?php echo $defaultText; ?>')
			{
				form.filter_search.value = '' ;
			}
			form.submit();
		}
	</script>
	<input type="hidden" name="layout" value="<?php echo $layout; ?>" />
	<input type="hidden" name="show_category" value="<?php echo $show_category;?>" />
</form>
</div>