<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

?>
<form id="mainform" action="<?php echo JRoute::_("index.php?option=com_fsj_fssadd&view=canned&layout=form&tmpl=component&ticket=" . JRequest::getVar('ticket') . "&message=" . JRequest::getVar('message'), false); ?>" method="post" class="form-horizontal form-condensed">
	<input name="canned" type="hidden" value="<?php echo $this->canned->id; ?>" />
	<input name="insert" type="hidden" id='insert' value="<?php echo JRequest::getVar('insert'); ?>" />

	<script>
		<?php echo $this->canned->javascript; ?>
	</script>
	
	<style>
		<?php echo $this->canned->css; ?>
	</style>
	
	<?php echo FSS_Helper::PageStylePopup(true); ?>
	<?php echo FSS_Helper::PageTitlePopup("CANNED_PRESET_REPLIES", $this->canned->title); ?>

		<ul class="nav nav-tabs">
			<?php if ($this->form_data): ?>
				<li><a href="#form_tab_preview" data-toggle="tab"><?php echo JText::_('CANNED_PREVIEW'); ?></a></li>
			<?php endif; ?>

			<?php echo $this->form->getTabLabels(); ?>
		</ul>

		<div class="tab-content">
			<?php if ($this->form_data): ?>
				<div class="tab-pane" id="form_tab_preview">
					<div class="subject_preview well well-mini">
						<?php echo $this->subject; ?>
					</div>
					<div class="message_preview well well-mini">
						<?php echo FSS_Helper::ParseBBCode($this->preview); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php echo $this->form->getTabContent(); ?>
		</div>

	</div>

	<div class="modal-footer fss_main">
		<?php if ($this->form_data): ?>
			<?php if (JRequest::getVar('insert') != ""): ?>
				<a href="#" class="btn btn-success" onclick="fsj_Canned_Insert();return false;"><?php echo JText::_('CANNED_INSERT_MESSAGE'); ?></a>
			<?php elseif (JRequest::getVar('message') == "user" || JRequest::getVar('message') == "admin"): ?>
				<a href="#" class="btn btn-success" onclick="jQuery('#inlinereply').submit();return false;"><?php echo JText::_('CANNED_ADD_MESSAGE'); ?></a>
			<?php else: ?>
				<a href="#" class="btn btn-success" onclick="alert('post to message');"><?php echo JText::_('CANNED_ADD_MESSAGE'); ?></a>
			<?php endif; ?>
		<?php endif; ?>

		<a href="#" class="btn btn-default" onclick="jQuery('#mainform').submit();return false;"><?php echo JText::_('CANNED_SUBMIT_FORM'); ?></a>
		<a href="#" class="btn btn-default close_popup simplemodal-close" data-dismiss="modal"><?php echo JText::_('CANNED_CLOSE'); ?></a>
		<a href="<?php echo JRoute::_('index.php?option=com_fsj_fssadd&view=canned&tmpl=component&message=' . JRequest::getVar('user') . '&ticket=' . JRequest::getVar('ticket')); ?>" class="btn btn-default"><?php echo JText::_('CANNED_BACK'); ?></a>
	</div>

</form>

<?php if (JRequest::getVar('insert') != ""): ?>
	
		<input type=hidden class="input-xlarge" name="subject" id="subject" size="35" value="<?php echo FSS_Helper::escape(isset($this->subject) ? $this->subject : ''); ?>" required="">
		<textarea type=hidden name='body' id='body' style="display: none;"><?php echo htmlspecialchars(isset($this->preview) ? $this->preview : ''); ?></textarea>
		<input name="source" type="hidden" id='source' value="fr-<?php echo $this->canned->alias; ?>" />
		<input name="status" type="hidden" id='status' value="<?php echo $this->canned->newstatus; ?>" />
		
<?php elseif (JRequest::getVar('message') == "user"): ?>
	
	<form id='inlinereply' class="hide" style="display: none;" target="_parent" 
		action="<?php echo FSSRoute::_( 'index.php?option=com_fss&view=ticket&task=reply.fullpost' ); ?>" 
		method="post" enctype="multipart/form-data" class="form-horizontal form-condensed">
		<input type=hidden name='source' value='fr-<?php echo $this->canned->alias; ?>'>
		<input type=hidden name='reply_status' value='<?php echo $this->canned->newstatus > 0 ? $this->canned->newstatus : ''; ?>'>
		<input type=hidden class="input-xlarge" name="subject" size="35" value="<?php echo FSS_Helper::escape($this->subject); ?>" required="">
		<textarea type=hidden name='body'><?php echo htmlspecialchars($this->preview); ?></textarea>
		<input type="hidden" name="ticketid" value="<?php echo (int)$this->ticket->id; ?>" />
	</form>
	
<?php elseif (JRequest::getVar('message') == 'admin'): ?>
	
	<form id='inlinereply' class="hide" style="display: none;" target="_parent" 
		action="<?php echo FSSRoute::_( 'index.php?option=com_fss&view=admin_support&layout=reply&ticketid=' . $this->ticket->id, false ); ?>" 
		method="post" enctype="multipart/form-data" class="form-horizontal form-condensed">
		<input type=hidden name='what' value='savereply'>
		<input type=hidden name='reply_type' value='reply'>
		<input type=hidden name='source' value='fr-<?php echo $this->canned->alias; ?>'>
		<input type=hidden name='reply_status' value='<?php echo $this->canned->newstatus > 0 ? $this->canned->newstatus : $this->ticket->ticket_status_id; ?>'>
		<input type=hidden class="input-xlarge" name="subject" id="subject" size="35" value="<?php echo FSS_Helper::escape($this->subject); ?>" required="">
		<textarea type=hidden name='body'><?php echo htmlspecialchars($this->preview); ?></textarea>
		<input type="hidden" name="ticketid" value="<?php echo (int)$this->ticket->id; ?>" />
	</form>
	
<?php endif; ?>

<script>
jQuery(document).ready(function () {
	jQuery('ul.nav-tabs li:first-child a').tab("show");
});

function fsj_Canned_Insert()
{
	window.parent.insertSubject(jQuery('#subject').val());
	window.parent.insertSource(jQuery('#source').val());
	window.parent.insertStatus(jQuery('#status').val());
	window.parent.insertCannedText(jQuery('#body').val(), jQuery('#insert').val());
	window.parent.fss_modal_hide();
}
</script>

<div style='display: none' id="ticket_vars">
	<?php foreach ($this->ticket_vars as $key => $var): ?>
		<div id="ticket_var_<?php echo $key; ?>"><?php echo $var; ?></div>
	<?php endforeach; ?>
</div>