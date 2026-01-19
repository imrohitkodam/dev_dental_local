<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2011-2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
$user	= JFactory::getUser();
$userId	= $user->get('id');
$function	= $this->state->function;
$listOrder	= $this->state->filter_order;
$listDirn	= $this->state->filter_order_Dir;
if ($this->canUpload)
{
?>
	<div class="row-fluid">
		<form class="form form-horizontal">
			<div class="control-group">
				<label class="control-label" for="choose_document_option">
					<?php echo JText::_('EDOCMAN_INSERT_DOCUMENT_OPTION'); ?>
				</label>
				<?php
				if (version_compare(JVERSION, '3.0', 'lt'))
				{
					?>
					<div class="controls">
						<?php echo $this->lists['choose_document_option']; ?>
					</div>
				<?php
				}
				else
				{
					echo $this->lists['choose_document_option'];
				}
				?>
			</div>
		</form>
	</div>
<?php
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=documents&layout=modal&tmpl=component&function='.$function.'&'.JSession::getFormToken().'=1'); ?>" method="post" name="adminForm" id="adminForm" onsubmit="storeInsertOption();">
	<fieldset id="filter-bar">
		<div class="btn-wrapper input-append">
			<input type="text" name="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER_LABEL'); ?>" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" class="input-medium" />
			<button class="btn" type="submit"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button class="btn hasTooltip js-stools-btn-clear" type="button" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt pull-right">
			<?php echo $this->lists['filter_category_id']; ?>
			<?php
			if ($this->config->activate_multilingual_feature) {
				?>
				<select name="filter_language" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>
					<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
				</select>
			<?php
			}
			?>
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), "value", "text", $this->state->filter_published, true);?>
			</select>
		</div>
	</fieldset>
	<div class="clearfix"></div>
	<table class="adminlist table table-striped table-bordered  table-condensed">
		<thead>
			<tr>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>      
				<th style="text-align: left;">
					<?php echo JText::_('EDOCMAN_TITLE'); ?>					
				</th>			
				<th width="15%" style="text-align: left;">
					<?php echo JText::_('EDOCMAN_CATEGORY'); ?>
				</th>	        				                               
				<th width="5%">
					<?php echo JText::_('EDOCMAN_HITS'); ?>					
				</th>
				<th width="5%">
					<?php echo JText::_('EDOCMAN_DOWNLOADS'); ?>					
				</th>         
				<th width="10%">
					<?php echo JText::_('JGRID_HEADING_ACCESS'); ?>					
				</th>                       
                <th width="1%" class="nowrap">
                	<?php echo JText::_('JGRID_HEADING_ID'); ?>                    
                </th>                
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				</td>    
				<td>					
    				<a class="pointer" onclick="if (window.parent) window.parent.<?php echo $this->escape($function);?>('<?php echo $item->id; ?>', '<?php echo $this->escape(addslashes($item->title)); ?>');">    				
    						<?php echo $item->title; ?></a>    			   				
    				<p class="smallsub">					
				</td> 					
				<td>
					<?php echo $item->category_title ; ?>
				</td>			          			              
			    <td class="center">
			    	<?php echo (int)$item->hits ; ?>
			    </td>
			    <td class="center">
			    	<?php echo (int)$item->downloads ; ?>
			    </td>
			    <td class="center">
				    <?php echo $item->access_level ; ?>
				</td>                        
				<td class="center">
					<?php echo (int) $item->id; ?>
				</td>                
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
		<input type="hidden" name="choose_document_option" value="0" />

		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>

<?php
	if ($this->canUpload)
	{
	?>
		<div class="row-fluid">
			<form  action="<?php echo JRoute::_('index.php?option=com_edocman&task=document.saveDocument'); ?>" method="post" name="upload-document-form" id="item-form" class="form form-horizontal upload-new-document" enctype="multipart/form-data">
				<div class="control-group">
					<label class="control-label" for="title">
						<?php echo JText::_('JGLOBAL_TITLE'); ?>
					</label>
					<div class="controls">
						<input type="text" required="required" size="40" class="input-xlarge" value="" id="title" name="title" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="category_id">
						<?php echo JText::_('EDOCMAN_CATEGORY'); ?>
					</label>
					<div class="controls">
						<?php echo $this->lists['category_id']; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="access">
						<?php echo JText::_('JFIELD_ACCESS_LABEL'); ?>
					</label>
					<div class="controls">
						<?php echo $this->lists['access']; ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="published">
						<?php echo JText::_('JSTATUS'); ?>
					</label>
					<?php
					if (version_compare(JVERSION, '3.0', 'lt'))
					{
						?>
						<div class="controls">
							<?php echo $this->lists['published']; ?>
						</div>
					<?php
					}
					else
					{
						echo $this->lists['published'];
					}
					?>
				</div>
				<div class="control-group">
					<label class="control-label" for="filename">
						<?php echo JText::_('EDOCMAN_FILE'); ?>
					</label>
					<div class="controls">
						<input type="file" size="60" class="inputbox" value="" id="filename" name="filename" />
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="description">
						<?php echo JText::_('JGLOBAL_DESCRIPTION'); ?>
					</label>
					<div class="controls">
						<textarea id="description" name="description" class="input-xlarge" cols="60" rows="5"><?php echo $this->input->get('description', '', 'none'); ?></textarea>
					</div>
				</div>
				<div class="control-group">
					<button type="button" class="btn btn-small" onclick="saveDocument();"><span class="icon-save icon-white"></span><?php echo JText::_('EDOCMAN_UPLOAD'); ?></button>
				</div>
				<?php echo JHtml::_('form.token'); ?>
			</form>
		</div>

		<script type="text/javascript">
			(function($){
				$(document).ready(function(){
					changeOption = (function(value){
						if(value == 1)
						{
							$('#adminForm').hide();
							$('#item-form').show();
						}
						else
						{
							$('#item-form').hide();
							$('#adminForm').show();
						}
					})

					saveDocument = (function(){
						if($( "#title" ).val() == '')
						{
							$( "#title" ).css("border","1px solid #f11");
							$( "#title" ).focus();
						}
						else if($("#category_id").val() == 0)
						{
							$("#category_id").css("border","1px solid #f11");
							$("#category_id").val().focus();
						}
						else
						{
							$('#item-form').submit();
						}
					})

					storeInsertOption = (function(){
						document.adminForm.choose_document_option.value = $('input:radio[name=choose_document_option]:checked').val();
						return true;

					})

					var selectedOption = $('input:radio[name=choose_document_option]:checked').val();
					changeOption(selectedOption);

				})
			})(jQuery)
		</script>
	<?php
	}
?>