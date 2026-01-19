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
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.multiselect');

$input     = JFactory::getApplication()->input;
$location  = $input->get('location');
$field     = $input->getCmd('field');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

//~ echo "<pre>";
//~ print_r($this->assignNominee);
//~ echo "</pre>";
?>
<script>
	jQuery(document).ready(function () {
		jQuery('#clear-search-button').on('click', function () {
			jQuery('#filter_search').val('');
			jQuery('#adminForm').submit();
		});

		// Autocomplete address for PickupAddress
		//~ jQuery("#location").keypress(function() {
			//~ var location=jQuery("#location").val();
//~ 
			//~ jQuery.ajax(
			//~ {
				//~ url:"index.php?option=com_hierarchy&task=nominate.searchLocationEvent',
				//~ data:{location:location},
				//~ type:"POST",
				//~ datatype : "json",
				//~ success:function(resp)
				//~ {
					//~ // console.log(resp);
				//~ }
			//~ });
		//~ });

	});

</script>
<div id="assign_event">

	<form action="<?php echo JRoute::_('index.php?option=com_hierarchy&view=nominates&tmpl=component');?>" method="post" name="adminForm" id="adminForm">

	<!--form  method="post" name="adminForm" id="adminForm"-->
		
			<!--input type="hidden" name="option" value="com_hierarchy" />
			<input type="hidden" name="task" value="nominate.searchLocationEvent" />
			<input type="hidden" name="view" value="nominate" />
			<input type="hidden" name="layout" value="assign_event" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" /-->
		<fieldset class="filter">
			<div id="filter-bar" class="btn-toolbar">
				<div class="btn-group pull-right">
					<!--label for="location" class=""><?php echo JText::_('COM_HIERARCHY_NOMINATION_LOCATION');?></label-->
					<input type="text" name="location" id="location" placeholder="<?php echo JText::_('COM_HIERARCHY_NOMINATION_LOCATION'); ?>" value="<?php echo $location;?>" title="<?php echo JText::_('COM_HIERARCHY_NOMINATION_LOCATION'); ?>" />
				</div>
			</div>
		</fieldset>

		<table class="table table-striped table-condensed">
			<thead>
				<tr>
					<th class="left">
						<?php echo JHtml::_('grid.sort', 'COM_HIERARCHY_NOMINATION_CATEGORY', 'u.name', $listDirn, $listOrder); ?>
					</th>
					<th class="nowrap" width="25%">
						<?php echo JText::_('COM_HIERARCHY_NOMINATION_CATEGORY_EVENT'); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="15">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
				$i = 0;

				foreach ($this->assignNominee as $item) : ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td>
						<?php echo $item->catName; ?>
					</td>
					<td align="center">
						<div id="events_<?php echo $item->catId;?>"></div>
						<?php
						$ajaxData = '
							jQuery.ajax(
								{
									url:"index.php?option=com_hierarchy&task=nominate.getEvents&catId='.$item->catId.'",
									data:{locationcc:1},
									type:"POST",
									datatype : "json",
									success:function(resp)
									{
										console.log(resp);
										jQuery("#events_' . $item->catId . '").html(resp);
									}
								});
						';
						// $ajaxData = 'callEventList(' . $item->catId . ');';

						$doc =JFactory::getDocument();
						$doc->addScriptDeclaration( $ajaxData );
						?>
					</td>
				</tr>
			<?php endforeach; ?>
			<tr>
				<td colspan="2" class="center">
					<button type="button" class="btn btn-success" id="nominate-submit">
						<i class="icon-user icon-white"></i>
						<?php echo JText::_('COM_HIERARCHY_NOMINATION_BUTTON'); ?>
					</button>
				</td>
			</tr>
			</tbody>
		</table>
		<div>
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="field" value="<?php echo $this->escape($field); ?>" />
			<input type="hidden" name="boxchecked" value="0" />
			<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
			<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
			<?php echo JHtml::_('form.token'); ?>
		</div>
	</form>

</div>
<script>
	//~ function callEventList(catId)
	//~ {
		//~ jQuery.ajax(
		//~ {
			//~ url:"index.php?option=com_hierarchy&task=nominate.getEvents&catId=" + catId,
			//~ data:{locationcc:1},
			//~ type:"POST",
			//~ datatype : "json",
			//~ success:function(resp)
			//~ {
				//~ console.log(resp);
				//~ jQuery("#events_"+catId).html(resp);
			//~ }
		//~ });
	//~ }
</script>
