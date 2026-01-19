<?php
/**
* @version     1.5
* @package     com_jticketing
* @copyright   Copyright (C) 2014. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
* @author      Techjoomla <extensions@techjoomla.com> - http://techjoomla.com
*/
// no direct access
defined('_JEXEC') or die;
?>

<div class="form-group">
	<div class=" col-lg-2 col-md-2 col-sm-3 col-xs-12  control-label"><?php echo $this->form->getLabel('startdate'); ?></div>
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
		<div class="form-inline">
		<?php
			//echo $this->form->getInput('startdate');

			for($i = 1; $i <= 12; $i++)
			{
				$hours[] = JHtml::_('select.option', $i, $i);
			}

			$minutes   = array();
			$minutes[] = JHtml::_('select.option', 0, '00');
			$minutes[] = JHtml::_('select.option', 15, '15');
			$minutes[] = JHtml::_('select.option', 30, '30');
			$minutes[] = JHtml::_('select.option', 45, '45');

			$amPmSelect   = array();
			$amPmSelect[] = JHtml::_('select.option', 'AM', 'am' );
			$amPmSelect[] = JHtml::_('select.option', 'PM', 'pm' );

			if(!isset($this->item->startdate) or $this->item->startdate=='0000-00-00 00:00:00')
			{
				$selectedmin = JFactory::getDate()->Format('i');
				$startAmPm   = JFactory::getDate()->Format('H')>= 12 ? 'PM' : 'AM';
				$final_start_event_date = JFactory::getDate()->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
				$selectedStartHour = JFactory::getDate()->Format('H');

			}
			else
			{
				$startAmPm   = JFactory::getDate($this->item->startdate)->Format('H');
				$startAmPm   = JHtml::date($this->item->startdate,JText::_('H'), true);
				$startAmPm  = $startAmPm >= 12 ? 'PM' : 'AM';
				$selectedmin = JFactory::getDate($this->item->startdate)->Format('i');
				$selectedmin = JHtml::date($this->item->startdate,JText::_('i'), true);
				$selectedStartHour = JFactory::getDate($this->item->startdate)->Format('H');
				$selectedStartHour = JHtml::date($this->item->startdate,JText::_('H'), true);

				if($selectedStartHour > 12)
				{
					$selectedStartHour = $selectedStartHour - 12;
				}

				$final_start_event_date = JFactory::getDate($this->item->startdate)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
				$final_start_event_date = JHtml::date($this->item->startdate,JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'), true);
			}

			if($selectedStartHour=='00' or $selectedStartHour=='0')
			{
				$selectedStartHour = 12;
			}
			?>
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-10">
				<?php
				//static calendar($value, $name, $id, $format= '%Y-%m-%d', $attribs=null)
				echo $calender = JHtml::_('calendar', $final_start_event_date, 'jform[startdate]', 'jform_startdate', JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER'), array('class'=>''));

				?>
					<p class="text-info"><?php	echo "<i>" . JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER_DESC') . "</i>";	?></p>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12  ">
					<?php
					echo $startHourSelect = JHtml::_('select.genericlist', $hours, 'event_start_time_hour', array('class'=>'required col-lg-3 col-md-3 col-sm-3 col-xs-3 chzn-done'), 'value', 'text', $selectedStartHour, false );

					echo $startMinSelect = JHtml::_('select.genericlist', $minutes, 'event_start_time_min', array('class'=>'required col-lg-3 col-md-3 col-sm-4 col-xs-4 chzn-done '), 'value', 'text', $selectedmin, false );

					echo $startAmPmSelect = JHtml::_('select.genericlist', $amPmSelect, 'event_start_time_ampm', array('class'=>' col-lg-3 col-md-3 col-sm-4 col-xs-4 required chzn-done'), 'value', 'text', $startAmPm, false);
				?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<div class=" col-lg-2 col-md-2 col-sm-3 col-xs-12  control-label"><?php echo $this->form->getLabel('enddate'); ?></div>
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
		<div class="form-inline">
		<?php
			//echo $this->form->getInput('enddate');
			$selectedStartHour = $selectedmin = $startAmPm = $end_date_event = '';
			// Set date to current date.
			$end_date_event = JFactory::getDate()->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));

			if(!isset($this->item->enddate))
			{
				$selectedmin = JFactory::getDate()->Format('i');
				$startAmPm   = JFactory::getDate()->Format('H') >= 12 ? 'PM' : 'AM';
				$final_end_event_date = JFactory::getDate()->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
				$selectedStartHour = JFactory::getDate()->Format('H');
			}
			else
			{
				$startAmPm = JFactory::getDate($this->item->enddate)->Format('H');
				$startAmPm   = JHtml::date($this->item->enddate,JText::_('H'), true);
				$startAmPm  = $startAmPm >= 12 ? 'PM' : 'AM';
				$selectedmin = JFactory::getDate($this->item->enddate)->Format('i');
				$selectedmin = JHtml::date($this->item->enddate,JText::_('i'), true);
				$selectedStartHour = JFactory::getDate($this->item->enddate)->Format('H');
				$selectedStartHour = JHtml::date($this->item->enddate,JText::_('H'), true);

				if($selectedStartHour>12)
				{
					$selectedStartHour = $selectedStartHour - 12;
				}

				$final_end_event_date = JFactory::getDate($this->item->enddate)->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
				$final_end_event_date = JHtml::date($this->item->enddate,JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'), true);
			}

			if($selectedStartHour=='00' OR $selectedStartHour=='0')
			{
				$selectedStartHour = 12;
			}

			?>
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-12 col-xs-10">
					<?php
					echo $calendar = JHtml::_('calendar', $final_end_event_date, 'jform[enddate]', 'jform_enddate', JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER'), array('class'=>''));

					?>
					<p class="text-info"><?php	echo "<i>" . JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER_DESC') . "</i>";	?></p>
				</div>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12  ">
					<?php
					echo $endHourSelect = JHtml::_('select.genericlist', $hours, 'event_end_time_hour', array('class'=>'required col-lg-3 col-md-3 col-sm-3 col-xs-3 chzn-done'), 'value', 'text', $selectedStartHour, false );

					echo $endMinSelect = JHtml::_('select.genericlist',  $minutes , 'event_end_time_min', array('class'=>'required col-lg-3 col-md-3 col-sm-4 col-xs-4 chzn-done'), 'value', 'text',$selectedmin , false );

					echo $endAmPmSelect = JHtml::_('select.genericlist', $amPmSelect, 'event_end_time_ampm', array('class'=>'required input col-lg-3 col-md-3 col-sm-4 col-xs-4 chzn-done'), 'value', 'text', $startAmPm, false );
				?>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<div class=" col-lg-2 col-md-2 col-sm-3 col-xs-12  control-label"><?php echo $this->form->getLabel('booking_start_date'); ?></div>
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
		<div class="form-inline">
		<?php
			//echo $this->form->getInput('booking_start_date');
			if(!isset($this->item->booking_start_date) or $this->item->booking_start_date=='0000-00-00 00:00:00')
			{
				$booking_start_date = JFactory::getDate()->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
			}
			else
			{
				$booking_start_date = JHtml::date($this->item->booking_start_date, JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'), true);
			}
		?>
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
				<?php
				echo $calendar = JHtml::_('calendar', $booking_start_date, 'jform[booking_start_date]','jform_booking_start_date', JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER'), array('class'=>''));

				?>
					<p class="text-info"><?php	echo "<i>" . JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER_DESC') . "</i>";	?></p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<div class=" col-lg-2 col-md-2 col-sm-3 col-xs-12  control-label"><?php echo $this->form->getLabel('booking_end_date'); ?></div>
	<div class="col-lg-10 col-md-10 col-sm-9 col-xs-12">
		<div class="form-inline">
		<?php
			//echo $this->form->getInput('booking_end_date');

			if(!isset($this->item->booking_end_date) or $this->item->booking_end_date=='0000-00-00 00:00:00')
			{
				$booking_end_date = JFactory::getDate()->Format(JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'));
			}
			else
			{
				$booking_end_date = JHtml::date($this->item->booking_end_date, JText::_('COM_JTICKETING_DATE_FORMAT_SHOW_SHORT'), true);
			}
		?>
			<div class="row">
				<div class="col-lg-4 col-md-4 col-sm-4 col-xs-10">
				<?php
				echo $calendar = JHtml::_('calendar', $booking_end_date, 'jform[booking_end_date]','jform_booking_end_date', JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER'), array('class'=>''));
				?>
					<p class="text-info"><?php	echo "<i>" . JText::_('COM_JTICKETING_DATE_FORMAT_CALENDER_DESC') . "</i>";	?></p>
				</div>
			</div>
		</div>
	</div>
</div>
