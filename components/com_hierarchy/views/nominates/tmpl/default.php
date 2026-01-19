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

$path = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';

if (!class_exists('jticketingmainhelper'))
{
	JLoader::register('jticketingmainhelper', $path);
	JLoader::load('jticketingmainhelper');
}

$jticketingmainhelper = new jticketingmainhelper;
$jinput        = JFactory::getApplication()->input;
// Import CSS
JHtml::stylesheet(JUri::root(). 'components/com_hierarchy/assets/css/hierarchy.css' );

$input     = JFactory::getApplication()->input;
$location  = $input->get('location');
$nomineeId  = $input->get('nomineeId');
$popupClose  = $input->get('popupClose');
$db     = JFactory::getDBO();

$groups = JAccess::getGroupsByUser($nomineeId);
$groupid_list      = '(' . implode(',', $groups) . ')';
$query  = $db->getQuery(true);
$query->select('title');
$query->from('#__usergroups');
$query->where('id IN ' .$groupid_list);
$db->setQuery($query);
$groups = '';
$rows   = $db->loadObjectlist();
foreach($rows AS $row)
{
$groups[] = $row->title;
}

$grouplist   = implode(", ",$groups);

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
			jQuery('#location').val('');
			jQuery('#adminForm').submit();
		});

		jQuery('#nominate-submit').on('click', function () {
			var flag=0;
			jQuery('.selected_events').each(function()
			{
				if (jQuery(this).val())
				{
					flag=1;
				}
			});

		if (flag != 1)
		{
			alert("<?php echo JText::_('COM_HIERARCHY_SELECT_BATCH_ERROR');?>");
			return false;
		}
			jQuery('#task').val('nominates.applyEvent');
			jQuery('#adminForm').submit();
		});

		/*var popupClose = '<?php echo $popupClose;?>';
		if (popupClose == 1)
		{
			window.parent.SqueezeBox.close();
		}*/
	});

	function enableNominateBtn(value)
	{
		if (value)
		{
			jQuery('#nominate-submit').removeAttr('disabled');
		}
		else
		{
			// jQuery('#nominate-submit').attr('disabled','disabled');
		}
	}
</script>
<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&view=nominates&tmpl=component&nomineeId='.(int) $nomineeId); ?> '); ?>" method="post" name="adminForm" id="adminForm">

<?php if ($popupClose == 1) { ?>

<?php ?>

<?php } else { ?>

	<?php echo JLayoutHelper::render('default_filter', array('view' => $this), dirname(__FILE__)); ?>
		<div class="">
			<div class="alert alert-info"><?php	echo JText::_('COM_HIERARCHY_HIERARCHYS_CONDITION');?>
			</div>
		</div>
		<table class="table table-striped" border="0" id = "hierarchyList" >


		<?php if (empty($this->items)) : ?>
			<thead >
				<tr>
					<th class='left'>
						<div class="alert alert-no-items">
							<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS');?>
						</div>
					</th>
				</tr>
			</thead>
		<?php else : ?>

			<thead >
				<tr class="">
					<th class='left'>


					</th>

					<th class='left'>
						<?php // echo JFactory::getUser($nomineeId)->name; ?>
					</th>
				</tr>
				<tr >
					<th class='left'>
						<?php echo JText::_('COM_HIERARCHY_NOMINATION_CATEGORY');?>
					</th>

					<th class='left'>
						<?php echo JText::_('COM_HIERARCHY_NOMINATION_CHOOSE_BATCH');?>
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
					<?php $eventArray = array();?>
					<?php $km = 0; ?>
					<?php $kp = 0; ?>
						<?php foreach ($this->items as $i => $item) : ?>
						<?php // $showEvent = 0;
						// $showEvent = $jticketingmainhelper->showbuybutton($item->ticketEventId); ?>
						<?php // if ($showEvent)
						// { ?>
							<tr class="row<?php echo $i % 2; ?>">
								<td>
									<?php echo $item->catName; ?>
									<input type="hidden" id="catId" name="catId[]" value="<?php echo $item->catId;?>"/>
								</td>

								<td>
									<div id="events_<?php echo $item->catId;?>"><?php echo $item->options;?></div>
								</td>
							</tr>

						<?php

						// } else { $km++; } ?>
					<?php // $kp++;
					endforeach; ?>
					<?php /*if ($km == $kp) : ?>
					<thead >
						<tr>
							<th class='left' colspan="2">
								<div class="alert alert-no-items">
									<?php echo JText::_('JGLOBAL_NO_MATCHING_RESULTS');?>
								</div>
							</th>
						</tr>
					</thead>
					<?php endif;*/ ?>
					<tr>
						<td colspan="2" class="center">
							<button type="button" class="btn btn-success" id="nominate-submit">
								<i class="icon-user icon-white"></i>
								<?php echo JText::_('COM_HIERARCHY_NOMINATION_BUTTON'); ?>
							</button>
						</td>
					</tr>
				</tbody>
			<?php endif; ?>
		</table>
<?php } ?>
	<input type="hidden" id="nomineeId" name="nomineeId" value="<?php echo $nomineeId;?>"/>
	<input type="hidden" id="task" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
