<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;
$user	= JFactory::getUser();
$userId	= $user->get('id');
$config = EDocmanHelper::getConfig();
$canOrder	= $user->authorise('core.edit.state', 'com_edocman');
if (version_compare(JVERSION, '3.0', 'ge')) {
	//EdocmanHelper::addSideBarmenus('licenses');
	$sidebar = JHtmlSidebar::render();
}
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=licenses'); ?>" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="span12">
		<fieldset id="filter-bar">
			<div class="filter-search pull-left">
				<label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('Search'); ?>" class="search-query" />
				<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="document.id('filter_search').value='';this.form.submit();" class="btn"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>
			<div class="filter-select pull-right">            
					<select name="filter_published" class="inputbox" onchange="this.form.submit()">
						<option value=""><?php echo JText::_('JOPTION_SELECT_PUBLISHED');?></option>
						<?php echo JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('archived'=> false, 'trash' => false, 'all' => false)), "value", "text", $this->state->get('filter.state'), true);?>
					</select>               
			</div>
		</fieldset>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped">
			<thead>
				<tr>
					<th width="1%">
						<input type="checkbox" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<th class="title" style="text-align: left;">
						<?php echo JHtml::_('grid.sort',  JText::_('EDOCMAN_TITLE'), 'tbl.title', $this->state->filter_order, $this->state->filter_order_Dir); ?>
					</th>											    					    			
					<th class="title center">
						<?php echo JHtml::_('grid.sort',  JText::_('EDOCMAN_PUBLISHED'), 'tbl.published', $this->state->filter_order, $this->state->filter_order_Dir); ?>
					</th>    			
					<th class="title center">
						<?php echo JHtml::_('grid.sort',  JText::_('EDOCMAN_ID'), 'tbl.id', $this->state->filter_order, $this->state->filter_order_Dir); ?>
					</th>											                
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="4">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php foreach ($this->items as $i => $item) :					
				$canEdit	= $user->authorise('core.edit',			'com_edocman');			
				$canChange	= $user->authorise('core.edit.state',	'com_edocman');
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>        
					<td>
						<?php 
							if ($canEdit) {
							?>
								<a href="<?php echo JRoute::_('index.php?option=com_edocman&task=license.edit&id='.$item->id);?>">
									<?php echo $item->title; ?>
								</a>
							<?php    
							} 
							else 
							{
								echo $item->title ;
							}
						?>
					</td>        
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, '', $canChange);?>
							<a class="btn btn-micro active hasTooltip" title="<?php echo JText::_('EDOCMAN_EDIT');?>" href="index.php?option=com_edocman&task=license.edit&cid[]=<?php echo $item->id;?>" data-original-title="<?php echo JText::_('EDOCMAN_EDIT');?>">
								<span class="icon-edit"></span>
							</a>
							<?php
							if($config->use_default_license ==1){
								if($item->default_license == 1){
									?>
									<a class="btn btn-micro active hasTooltip" title="<?php echo JText::_('EDOCMAN_DEFAULT_LICENSE');?>" href="index.php?option=com_edocman&task=license.active_default&state=0&id=<?php echo $item->id;?>" data-original-title="<?php echo JText::_('EDOCMAN_DEFAULT_LICENSE');?>">
										<i class="icon-star" style="color:orange;"></i>
									</a>
									<?php
								}else{
									?>
									<a class="btn btn-micro active hasTooltip" title="<?php echo JText::_('EDOCMAN_CLICK_HERE_TO_ACTIVE_DEFAULT_LICENSE');?>" href="index.php?option=com_edocman&task=license.active_default&state=1&id=<?php echo $item->id;?>" data-original-title="<?php echo JText::_('EDOCMAN_CLICK_HERE_TO_ACTIVE_DEFAULT_LICENSE');?>">
										<i class="icon-star" style="color:#000;"></i>
									</a>
									<?php
								}
							}
							?>
						</div>
					</td>                               
					<td class="center">
						<?php echo $item->id; ?>
					</td>               
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>
</form>