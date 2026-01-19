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

JHtml::_('behavior.tooltip');
JHtml::_('script','system/multiselect.js',false,true);
$listOrder	= $this->state->filter_order;
$listDirn	= $this->state->filter_order_Dir;
$config     = EDocmanHelper::getConfig();
?>

<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=downloadlogs'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span12">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<input placeholder="<?php echo JText::_('JSEARCH_FILTER');?>" type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->filter_search); ?>" title="<?php echo JText::_('Search'); ?>" class="search-query" />
				</DIV>
				<div class="btn-group pull-left">
					<button type="submit" class="btn"><?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?></button>
					<button type="button" class="btn" onclick="document.id('filter_search').value='';this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
				</div>
			</div>
			<table class="table table-striped">
				<thead>
					<tr>				
						<th class="title" style="text-align: left;">
							<?php echo JHtml::_('grid.sort',  JText::_('Document'), 'b.title', $listDirn, $listOrder); ?>
						</th>
						<?php
							if ($config->collect_downloader_information)
							{
							?>
								<th width="12%" style="text-align: left;">
									<?php echo JHtml::_('grid.sort',  JText::_('Name'), 'tbl.name', $listDirn, $listOrder); ?>
								</th>
								<th width="15%" style="text-align: left;">
									<?php echo JHtml::_('grid.sort',  JText::_('Email'), 'tbl.email', $listDirn, $listOrder); ?>
								</th>
							<?php
							}
							else
							{
							?>
								<th width="12%" style="text-align: left;">
									<?php echo JHtml::_('grid.sort',  JText::_('Username'), 'c.username', $listDirn, $listOrder); ?>
								</th>
								<th width="15%" style="text-align: left;">
									<?php echo JHtml::_('grid.sort',  JText::_('Email'), 'c.email', $listDirn, $listOrder); ?>
								</th>
							<?php
							}
						?>
						<th width="12%">
							<?php echo JHtml::_('grid.sort',  JText::_('Download Time'), 'tbl.download_time', $listDirn, $listOrder); ?>
						</th>    			
						<th width="10%" style="text-align: center;">
							<?php echo JHtml::_('grid.sort',  JText::_('User IP'), 'tbl.user_ip', $listDirn, $listOrder); ?>
						</th>
						<th width="8%" style="text-align: center;">
							<?php echo JHtml::_('grid.sort',  JText::_('Browser'), 'tbl.browser', $listDirn, $listOrder); ?>
						</th>
						<th width="8%" style="text-align: center;">
							<?php echo JHtml::_('grid.sort',  JText::_('OS'), 'tbl.os', $listDirn, $listOrder); ?>
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
						<td>
							<?php echo $item->title ; ?>
						</td>
						<?php
							if ($config->collect_downloader_information && !$item->user_id)
							{
							?>
								<td>
									<?php echo $item->name ; ?>
								</td>
								<td>
									<?php echo $item->email ; ?>
								</td>
							<?php
							}
							else
							{
							?>
								<td>
									<?php echo $item->downloader_username ; ?>
								</td>
								<td>
									<?php echo $item->downloader_email ; ?>
								</td>
							<?php
							}
						?>
						<td class="center">
							<?php echo JHtml::_('date', $item->download_time, 'm-d-Y H:i:s'); ?>
						</td>                               
						<td class="center">
							<?php echo $item->user_ip ; ?>
						</td>               
						<td class="center">
							<?php echo $item->browser; ; ?>
						</td>
						<td class="center">
							<?php echo $item->os; ; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</div>
</form>