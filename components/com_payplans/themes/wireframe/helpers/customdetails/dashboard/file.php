<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$label = isset($field->attributes['required']) ? '*' . $field->title : $field->title;
?>
<div data-pp-cd-file-wrapper>
	<div class="flex px-md py-2xl flex-col md:flex-row gap-sm" data-pp-form-group data-name="<?php echo $field->name; ?>">
		<div class="md:w-[320px] flex-shrink-0">
			<?php echo $this->fd->html('form.label', $label, '', '', '', false, ['columns' => 6]); ?>
		</div>

		<div class="flex-grow space-y-xs">
			<div class="flex gap-xs">
				<div class="flex-grow">
					<div class="o-form-group space-y-xs is-filled" data-pp-cd-file-attachment-wrapper>
						<?php echo $this->html('attachment.list', $files, $field->name, [
							'saved' => true, 
							'action' => true, 
							'type' => 'customdetails', 
							'objId' => $obj->getId(), 
							'group' => $group
							]); ?>

							<?php if ($allowInput) { ?>
								<?php echo $this->html('form.file', $name, '', '', $field->attributes, ['allowInput' => $allowInput]); ?>
							<?php } ?>
					</div>

					<div class="o-form-helper-text text-xs text-danger t-hidden" data-error-message>
						<i class="fdi fa fa-exclamation-circle"></i> <?php echo JText::_('COM_PP_FIELD_REQUIRED_MESSAGE'); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>