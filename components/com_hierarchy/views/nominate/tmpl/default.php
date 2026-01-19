<?php
/**
 * @version     1.0.0
 * @package     com_hierarchy
 * @copyright   Copyright (C) 2015. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @author      Parth Lawate <contact@techjoomla.com> - http://techjoomla.com
 */
// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
JHTML::_('behavior.modal', 'a.modal');

// Import CSS
JHtml::stylesheet(JUri::root(). 'components/com_hierarchy/assets/css/hierarchy.css' );

$app = JFactory::getApplication();
$search = $app->input->get('filter_search');
$user = JFactory::getUser();
$userId = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn = $this->state->get('list.direction');
$canCreate = $user->authorise('core.create', 'com_hierarchy');
$canEdit = $user->authorise('core.edit', 'com_hierarchy');
$canCheckin = $user->authorise('core.manage', 'com_hierarchy');
$canChange = $user->authorise('core.edit.state', 'com_hierarchy');
$canDelete = $user->authorise('core.delete', 'com_hierarchy');
?>
<script type="text/javascript">
	jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});

		jQuery('#sbox-btn-close').on('click', function () {
			window.document.location.reload();
		});
	});
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&view=nominate'); ?>" method="post" name="adminForm" id="adminForm">
	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
	<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER');?></label>
				<input type="text" class="nomineeSearch" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $search; ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
				<button class="btn hasTooltip" id="clear-search-button" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
			</div>
			<?php if (empty($this->items)) : ?>
				<div class="clearfix">&nbsp;</div>
				<div class="alert alert-no-items">
					<?php echo JText::_('COM_HIEARCHY_NO_MATCHING_RESULTS'); ?>
				</div>
			<?php
			else : ?>
	<table class="table table-striped" id = "hierarchyList" >
		<thead >
			<tr >
				<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_HIERARCHYS_USER_ID', 'u.id', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_HIERARCHYS_USER_NAME', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th class='left'>
					<?php echo JText::_('COM_HIERARCHY_ALREADY_NOMINATED_EVENTS'); ?>
				</th>
				<th class='left'>
					<?php echo JText::_('COM_HIERARCHY_NOMINATION_COL'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>

			<?php foreach ($this->items as $i => $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo $item->id; ?>
					</td>
					<td>
						<?php echo JFactory::getUser($item->id)->name; ?>
					</td>

					<td>
						<?php  if (isset($item->already_nominated_events)) echo $item->already_nominated_events; ?>
					</td>
					<td>


						<a class="btn btn-primary nomineeBtn modal" title="<?php echo JText::_('COM_HIERARCHY_NOMINATION_BUTTON'); ?>" href="<?php echo JRoute::_('index.php?option=com_hierarchy&view=nominates&tmpl=component&nomineeId='.(int) $item->id); ?> " rel="{handler: 'iframe', size: {x: 800, y: 500}}">
							<i class="icon-user icon-white"></i> <?php echo JText::_('COM_HIERARCHY_NOMINATION_BUTTON'); ?>
						</a>

					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
			<?php endif; ?>

	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
