<div class="row-fluid form-horizontal-desktop">
	<div class="span6">
		<div class="control-group" style="display:none">
			<div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('id'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('title'); ?></div>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('alias'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('alias'); ?></div>
		</div>

		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('created_by'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('created_by'); ?></div>
		</div>

		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('catid'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('catid'); ?></div>
		</div>
		<?php if ($this->show_tags == 1): ?>
			<div class="control-group">
				<div class="control-label"><?php echo $this->form->getLabel('tags'); ?></div>
				<div class="controls"><?php echo $this->form->getInput('tags'); ?></div>
			</div>
		<?php endif; ?>
		<div class="control-group">
			<?php
			$canState = false;
			$canState = $canState = JFactory::getUser()->authorise('core.edit.state','com_jticketing');

			if(!$canState): ?>
				<div class="control-label">
					<?php echo $this->form->getLabel('state'); ?>
				</div>
				<?php
				$state_string = JText::_('COM_JTICKETING_UNPUBLISH');
				$state_value = 0;

				if ($this->item->state == 1):
					$state_string = JText::_('COM_JTICKETING_PUBLISH');
					$state_value = 1;
				endif;
				?>
				<div class="controls"><?php echo $state_string; ?></div>
				<input type="hidden" name="jform[state]" value="<?php echo $state_value; ?>" />
				<?php
			else: ?>
				<div class="control-label">
					<?php echo $this->form->getLabel('state'); ?>
				</div>
				<div class="controls"><?php echo $this->form->getInput('state'); ?></div>
				<?php
			endif; ?>
		</div>
		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('allow_view_attendee'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('allow_view_attendee'); ?></div>
		</div>

		<div class="control-group">
			<div class="control-label"><?php echo $this->form->getLabel('access'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('access'); ?></div>
		</div>
	</div>

	<div class="span6">
		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('startdate'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('startdate'); ?>
			</div>
		</div>

		<div class="control-group">
			<div class="control-label">
				<?php echo $this->form->getLabel('enddate'); ?>
			</div>
			<div class="controls">
				<?php
					echo $this->form->getInput('enddate');
				 ?>
			</div>
		</div>
		<?php

			$OnlineEvents = $this->params->get('enable_online_events');

			if ($OnlineEvents == 1)
			{	?>
				<div class="control-group">
					<div class="control-label"><?php echo $this->form->getLabel('online_events'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('online_events'); ?></div>
				</div>
			<?php
			}

			if($this->item->venue == 0)
			{
		?>
			<div class="control-group id="note_id">
				<div class="controls">
					<?php echo JText::sprintf('COM_VENUE_LOCATION_NOTE');?>
				</div>
			</div>
		<?php
			}
		?>
		<div class="control-group" id="venue_id">

			<div class="control-label"><?php echo $this->form->getLabel('venue'); ?></div>
			<div class="controls"><?php echo $this->form->getInput('venue'); ?>
			<div id="ajax_loader"></div>

			</div>
		</div>

		<?php
			if ($OnlineEvents == 1)
			{	?>
				<div class="control-group" id="venuechoice_id">
					<div class="control-label"><?php echo $this->form->getLabel('venuechoice'); ?></div>
					<div class="controls"><?php echo $this->form->getInput('venuechoice'); ?></div>
					<input type="hidden" name="event_url" class="event_url" id="event_url" value=""/>
					<input type="hidden" name="event_sco_id" class="event_sco_id" id="event_sco_id" value=""/>
				</div>
				<div class="control-group" id="existingEvent">
				<?php

				$exitingField = $this->form->getField('existing_event');

				if ($this->event->getId())
				{
					$exitingField->__set('value', $this->event->getOnlineEventId());
					$exitingField->addOption($this->event->getTitle(), ['value' => $this->event->getOnlineEventId(), 'selected' => 'selected']);
				}

				?>
					<div class="control-label"><?php echo $this->form->getLabel('existing_event'); ?></div>
					<div class="controls"><?php echo $exitingField->__get('input'); ?></div>
				</div>
		<?php
			}?>
		<div class="control-group" id="event-location">

			<div class="control-label"><?php echo $this->form->getLabel('location'); ?></div>
			<div class="controls">
				<?php echo $this->form->getInput('location');
					echo $this->form->renderField('longitude');
					echo $this->form->renderField('latitude');
				?>

			</div>

		</div>

		<?php
		if ($this->tncForCreateEvent == 1)
		{
			?>
			<div class="control-group">
				<div class="checkbox d-flex ml-15">
					<?php

					$checked = '';

					if (!empty($this->item->privacy_terms_condition))
					{
						$checked = 'checked';
					}

					$link = JRoute::_(JUri::root() . "index.php?option=com_content&view=article&id=" . $this->eventArticle . "&tmpl=component");
						?>
					<label for="accept_privacy_term">
						<input class="mb-10" type="checkbox" name="accept_privacy_term" id="accept_privacy_term" size="30" <?php echo $checked ?> />
						<a rel="{handler: 'iframe', size: {x: 600, y: 600}}" href="<?php
						echo $link;
							?>" class="modal relative d-block "> <?php echo JText::_('COM_JTICKETING_TERMS_CONDITION_EVENT');?>
						</a>
					</label>
				</div>
			</div>
		<?php
		}
		?>
	</div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?sensor=false&amp;libraries=places&key=<?php echo $this->googleMapApiKey;?>" type="text/javascript"></script>
<script type="text/javascript">

	// Google Map autosuggest  for location
	function initialize()
	{
		input = document.getElementById('jform_location');
		var autocomplete = new google.maps.places.Autocomplete(input);
	}

	google.maps.event.addDomListener(window, 'load', initialize);
</script>
