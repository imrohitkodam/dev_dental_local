<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die('');
JHtml::_('behavior.tooltip');

$user = JFactory::getUser() ;
$userId		= $user->get('id');
$saveOrder	= $this->state->filter_order == 'tbl.ordering';
EdocmanHelper::chosen();
$config = EDocmanHelper::getConfig();
?>
<form action="index.php?option=com_edocman&view=categories" method="post" name="adminForm" id="adminForm">
    <fieldset id="filter-bar">
        <div class="filter-search pull-left">
            <label class="filter-search-lbl" for="filter_search"><?php echo JText::_('JSEARCH_FILTER_LABEL'); ?></label>
            <input type="text" name="filter_search" id="filter_search" value="<?php echo $this->state->filter_search;?>" class="search-query input-medium" onchange="document.adminForm.submit();" />
            <button onclick="this.form.submit();" class="btn"><?php echo JText::_( 'Go' ); ?></button>
        </div>
        <div class="filter-select pull-right">
            <?php
	            if (@$config->activate_multilingual_feature)
	            {
	            	echo $this->lists['filter_language'];
	            }
                echo $this->lists['filter_category_id'] ;
                echo $this->lists['filter_level'] ;
	            echo $this->lists['filter_state'] ;
	            echo $this->lists['filter_access'] ;
	            if (version_compare(JVERSION, '3.0', 'ge'))
	            {
		            echo $this->pagination->getLimitBox();
	            }
            ?>
        </div>
    </fieldset>
    <div class="clearfix"></div>
<div id="editcell">
	<table class="adminlist table table-striped">
	<thead>
		<tr>			
			<th width="20">
				<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
			</th>
			<th class="title" style="text-align: left;">
				<?php echo JHtml::_('grid.sort',  JText::_('EDOCMAN_TITLE'), 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>											
			<th class="title center">
				<?php echo JText::_('# Documents'); ?>
			</th>			
			<th width="10%" nowrap="nowrap">
				<?php echo JHtml::_('grid.sort',  JText::_('EDOCMAN_ORDER'), 'tbl.ordering', $this->state->filter_order_Dir, $this->state->filter_order); ?>
				<?php if ($saveOrder) :?>
						<?php echo JHtml::_('grid.order',  $this->items, 'filesave.png', 'saveorder'); ?>
				<?php endif; ?>
			</th>
			<th class="center">
				<?php echo JHtml::_('grid.sort',  JText::_('EDOCMAN_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
			</th>
			<th width="10%" class="center">
					<?php echo JHtml::_('grid.sort',  'JGRID_HEADING_ACCESS', 'access_level', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
		    </th>
			<th class="center">
				<?php echo JHtml::_('grid.sort',  JText::_('EDOCMAN_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
			</th>													
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="8">
				<?php echo $this->pagination->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	$k = 0;
	for ($i=0, $n=count( $this->items ); $i < $n; $i++)
	{
		$item = $this->items[$i];									
		$canEdit	= $user->authorise('core.edit',			'com_edocman.category.'.$item->id);
		$canCheckin	= $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
		$canEditOwn	= $user->authorise('core.edit.own',		'com_edocman.category.'.$item->id) && $item->created_user_id == $userId;
		$canChange	= $user->authorise('core.edit.state',	'com_edocman.category.'.$item->id) && $canCheckin;

		?>
		<tr class="<?php echo "row$k"; ?>">
			<td>
				<?php echo JHtml::_('grid.id', $i, $item->id); ?>
			</td>			
			<td>
				<?php if ($item->checked_out) : ?>
					<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin); ?>
				<?php endif; ?>
				<?php if ($canEdit || $canEditOwn) : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_edocman&task=category.edit&id='.$item->id);?>">
						<?php echo $item->treename ? $item->treename : $item->title; ?></a>
				<?php else : ?>
					<?php echo $item->treename ? $item->treename : $item->title; ?>
				<?php endif; ?>
				<p class="smallsub">
					<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
			</td>									
			<td class="center">
				<?php echo $item->total_documents; ?>
			</td>												
			<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($this->state->filter_order == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->parent_id == @$this->items[$i-1]->parent_id), 'orderup', 'JLIB_HTML_MOVE_UP', true); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->parent_id == @$this->items[$i+1]->parent_id), 'orderdown', 'JLIB_HTML_MOVE_DOWN', true); ?></span>
							<?php elseif ($this->state->filter_order == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->parent_id == @$this->items[$i-1]->parent_id), 'orderdown', 'JLIB_HTML_MOVE_UP', true); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->parent_id == @$this->items[$i+1]->parent_id), 'orderup', 'JLIB_HTML_MOVE_DOWN', true); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order input-mini" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>			
			<td class="center">
				<?php echo JHtml::_('jgrid.published', $item->published, $i, '', $canChange);?>
			</td>
			<td class="center">
				<?php echo $item->access_level ; ?>
			</td>
			<td class="center">
				<?php echo $item->id; ?>
			</td>
		</tr>
		<?php
		$k = 1 - $k;
	}
	?>
	</tbody>
	</table>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />	
	<?php echo JHtml::_( 'form.token' ); ?>
</form>