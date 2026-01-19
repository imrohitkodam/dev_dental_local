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

$params = JComponentHelper::getParams('com_hierarchy');
$reschedule_perfix = $params->get('reschedule_perfix', '');
$groupid = $params->get('groupid', '','INT');
$training = HierarchyFrontendHelper::getTrainingAdminExit();
//print_r($training);
$path = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';
if (!class_exists('jticketingmainhelper'))
{
	JLoader::register('jticketingmainhelper', $path);
	JLoader::load('jticketingmainhelper');
}

$user = JFactory::getUser();

if (empty($training))
{
	echo '<p><span class="alert alert-warning  middle">'.JText::_("COM_HIERARCHY_RESCHEDULES_AUTHORIZE_GROUP").'</span></p>';
}
else
{
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
		});
	</script>
	<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&view=reschedules'); ?>" method="post" name="adminForm" id="adminForm">

		<div id="filter-bar" class="btn-toolbar">
				<div class="filter-search btn-group pull-left">
					<label for="filter_search" class="element-invisible"><?php echo JText::_('JSEARCH_FILTER');?></label>
					<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" title="<?php echo JText::_('JSEARCH_FILTER'); ?>" />
				</div>
				<div class="btn-group pull-left">
					<button class="btn hasTooltip" type="submit" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i class="icon-search"></i></button>
					<button class="btn hasTooltip" id="clear-search-button" type="button" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"><i class="icon-remove"></i></button>
				</div>
				
				<div class="contact pull-right"><a  href="<?php echo JRoute::_('index.php?option=com_hierarchy&view=reschedules&layout=form');?>"><h4><?php echo JText::_('COM_HIERARCHY_RESCHEDULE_WORKFLOW');?></h4></a></div>
		</div>
		<table class="table table-striped" id = "rescheduleList" >
			<thead >
				<tr >
					<?php if (isset($this->items[0]->state)): ?>
			<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
			</th>
		<?php endif; ?>

					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_TICKET_ID', 'a.ticket_id', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_PARTICIPANT_NAME', 'a.name', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_BATCH_NAME', 'a.title', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_BATCH_DATE', 'a.modified_on', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_BATCH_LOCATION', 'a.location', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_USER', 'a.user', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_BH', 'a.bh', $listDirn, $listOrder); ?>
					</th>
					
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_HR', 'a.hr', $listDirn, $listOrder); ?>
					</th>
					<th class='left'>
					<?php echo JText::_('COM_HIERARCHY_RESCHEDULES_INITATION_DATE'); ?>
					</th>
					
					<th class='left'>
					<?php echo JHtml::_('grid.sort',  'COM_HIERARCHY_RESCHEDULES_COMPELATION_DATE', 'a.modified_on', $listDirn, $listOrder); ?>
					</th>
					


		<?php if (isset($this->items[0]->id)): ?>
			<th width="1%" class="nowrap center hidden-phone">
				<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
			</th>
		<?php endif; ?>

						

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
		<?php foreach ($this->items as $i => $item) :
		
		 ?>
			<?php $canEdit = $user->authorise('core.edit', 'com_hierarchy'); ?>

							<?php if (!$canEdit && $user->authorise('core.edit.own', 'com_hierarchy')): ?>
						<?php $canEdit = JFactory::getUser()->id == $item->created_by; ?>
					<?php endif; ?>

			<tr class="row<?php echo $i % 2; ?>">

			   
								<td>
					<?php if (isset($item->checked_out) && $item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'reschedules.', $canCheckin); ?>
					<?php endif; ?>
					
					<?php echo $this->escape($item->ticket_id); 
					
					$ticketid = explode('-',$item->ticket_id);
					 $ticketid = $ticketid[1];
					$obj = new jticketingmainhelper();
					$t_data = $obj->getorderinfo($ticketid);
					
					?>
					</td>
					<td>

						<?php echo $t_data['order_info'][0]->firstname.' '.$t_data['order_info'][0]->lastname; ?>
					</td>
					<td>

						<?php echo $item->title; ?>
					</td>
					<td>

						<?php echo 
						JFactory::getDate($t_data['eventinfo']->startdate)->Format('d-m-Y') . JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_DATE_TO') . JFactory::getDate($t_data['eventinfo']->enddate)->Format('d-m-Y'); ?>
					</td>
					<td>

						<?php echo $item->location; ?>
					</td>
					<td>

					<?php 
					if ($item->user == 1)
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_CONFIRMED');
					}
					else
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_NOT_CONFIRMED');
					} ?>				</td>
					<td>

					<?php 	if ($item->bh == 2)
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_APPROVED');
					}
					else if ($item->bh == 1)
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_DECLINED');
					}
					else
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_NOT_RESPONED');
					} ?>
					</td>
					<td>

						<?php if ($item->hr == 2)
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_APPROVED');
					}
					else if ($item->hr == 1)
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_DECLINED');
					}
					else
					{
						echo JText::_('COM_HIERARCHY_RESCHEDULES_WORKFLOW_NOT_RESPONED');
					} ?>
					</td>
					
					<td class="center hidden-phone">
						<?php echo JFactory::getDate($item->created_on)->Format('d-m-Y') ; ?>
					</td>
					
					<td class="center hidden-phone">
						<?php echo JFactory::getDate($item->modified_on)->Format('d-m-Y') ; ?>
					</td>
				<?php if (isset($this->items[0]->id)): ?>
					<td class="center hidden-phone">
						<?php echo (int)$item->id; ?>
					</td>
				<?php endif; ?>

	   
					
			</tr>
		<?php endforeach; ?>
		</tbody>
		</table>

	   

		<input type="hidden" name="task" value=""/>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo JHtml::_('form.token'); ?>
	</form>

	<script type="text/javascript">

		jQuery(document).ready(function () {
			jQuery('.delete-button').click(deleteItem);
		});

		function deleteItem() {
			var item_id = jQuery(this).attr('data-item-id');
			if (confirm("<?php echo JText::_('COM_HIERARCHY_DELETE_MESSAGE'); ?>")) {
				window.location.href = '<?php echo JRoute::_('index.php?option=com_hierarchy&task=rescheduleform.remove&id=', false, 2) ?>' + item_id;
			}
		}
	</script>
<?php
}
?>
