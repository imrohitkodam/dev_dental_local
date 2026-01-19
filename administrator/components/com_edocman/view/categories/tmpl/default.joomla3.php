<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$config = EDocmanHelper::getConfig();
$user	= JFactory::getUser();
$userId	= $user->get('id');
$listOrder	= $this->state->filter_order;
$listDirn	= $this->state->filter_order_Dir;
$saveOrder	= $listOrder == 'tbl.ordering';
$ordering = ($this->state->filter_order == 'tbl.ordering');

if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_edocman&task=category.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'categoryList', 'adminForm', strtolower($listDirn), $saveOrderingUrl, false, true);
}

$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering'
);
JHtml::_('searchtools.form', '#adminForm', $customOptions);
if (count($this->items))
{
	foreach ($this->items as $item)
	{
		$this->ordering[$item->parent_id][] = $item->id;
	}
}
?>
<form action="index.php?option=com_edocman&view=categories" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<div class="span12">
			<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('EDOCMAN_FILTER_SEARCH_CATEGORIES_DESC');?></label>
					<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EDOCMAN_SEARCH_CATEGORIES_DESC'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
					<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
				</div>
				<div class="btn-group pull-right hidden-phone">
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
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<table class="adminlist table table-striped" id="categoryList">
				<thead>
					<tr>
						<th width="1%" class="nowrap center hidden-phone">
							<?php echo JHtml::_('searchtools.sort', '', 'tbl.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
						</th>
						<th width="20" class="center">
							<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
						</th>
						<th width="5">
						</th>
						<th class="title" style="text-align: left;">
							<?php echo JHtml::_('searchtools.sort',  JText::_('EDOCMAN_TITLE'), 'tbl.title', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
						</th>											
						<th class="title center">
							<?php echo JText::_('# Documents'); ?>
						</th>
						<th class="center">
							<?php echo JHtml::_('searchtools.sort',  JText::_('EDOCMAN_PUBLISHED'), 'tbl.published', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
						</th>
						<th width="10%" class="center">
                            <?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'access_level', $this->state->filter_order_Dir, $this->state->filter_order ); ?>
						</th>
						<th class="center">
							<?php echo JHtml::_('searchtools.sort',  JText::_('EDOCMAN_ID'), 'tbl.id', $this->state->filter_order_Dir, $this->state->filter_order); ?>
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
				$db = $this->db;
				$query = $db->getQuery(true);
				for ($i=0, $n=count( $this->items ); $i < $n; $i++)
				{
					$item = $this->items[$i];
					$orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
					$canEdit	= $user->authorise('core.edit',			'com_edocman.category.'.$item->id);
					$canCheckin	= $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
					$canEditOwn	= $user->authorise('core.edit.own',		'com_edocman.category.'.$item->id) && $item->created_user_id == $userId;
					$canChange	= $user->authorise('core.edit.state',	'com_edocman.category.'.$item->id) && $canCheckin;

					$query->clear();
					$query->select('count(id)')->from('#__edocman_categories')->where('parent_id = "'.$item->id.'"');
					$db->setQuery($query);
					$count = $db->loadResult();
					if($count > 0)
					{
						$icon = "edicon edicon-folder-open";
					}
					else
					{
						$icon = "edicon edicon-folder";
					}
				
					// Get the parents of item for sorting
					if ($item->level > 1)
					{
						$parentsStr = "";
						$_currentParentId = $item->parent_id;
						$parentsStr = " " . $_currentParentId;
						for ($i2 = 0; $i2 < $item->level; $i2++)
						{
							foreach ($this->ordering as $k => $v)
							{
								$v = implode("-", $v);
								$v = "-" . $v . "-";
								if (strpos($v, "-" . $_currentParentId . "-") !== false)
								{
									$parentsStr .= " " . $k;
									$_currentParentId = $k;
									break;
								}
							}
						}
					}
					else
					{
						$parentsStr = "";
					}
					?>
					<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $item->parent_id; ?>" item-id="<?php echo $item->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $item->level ?>">
						<td class="order nowrap center hidden-phone">
							<?php
							$iconClass = '';
							if (!$canChange)
							{
								$iconClass = ' inactive';
							}
							elseif (!$saveOrder)
							{
								$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
							}
							?>
							<span class="sortable-handler<?php echo $iconClass ?>">
								<span class="icon-menu"></span>
							</span>
							<?php if ($canChange && $saveOrder) : ?>
								<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" />
							<?php endif; ?>
						</td>
						<td class="center">
							<?php echo JHtml::_('grid.id', $i, $item->id); ?>
						</td>
						<td style="width:5px;padding-left:0px;padding-right:0px;padding-top:12px;color:#5871a9;">
							<i class="<?php echo $icon; ?>"></i>
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
							<p class="smallsub" style="font-size:11px;color:grey;">
								<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
						</td>									
						<td class="center">
							<?php echo $item->total_documents; ?>
						</td>
						<td class="center">
							<div class="btn-group">
								<?php echo JHtml::_('jgrid.published', $item->published, $i, '', $canChange);?>
								<a class="btn btn-micro active hasTooltip" title="<?php echo JText::_('EDOCMAN_EDIT');?>" href="index.php?option=com_edocman&task=category.edit&cid[]=<?php echo $item->id;?>" data-original-title="<?php echo JText::_('EDOCMAN_EDIT');?>">
									<span class="icon-edit"></span>
								</a>
							</div>
						</td>
						<td class="center">
							<?php
                            if($item->accesspicker == 0) {
                                echo $item->access_level;
                            }else{
                                echo EDocmanHelper::getAccessGroup(0, $item->id);
                            }
                            ?>
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
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->state->filter_order; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->state->filter_order_Dir; ?>" />
	<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>