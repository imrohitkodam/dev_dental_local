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
?>
<div class="o-form-group o-form-group--ifta is-filled" data-pp-cd-file-wrapper>
	<?php echo $this->html('attachment.list', $files, $field->name, array('saved' => true, 'action' => true, 'type' => 'customdetails', 'objId' => $obj->getId(), 'group' => $group)); ?>

	<?php if ($allowInput) { ?>
		<?php echo $this->html('floatLabel.file', $field->title, $type . '[' . $field->name . '][]', '', '', $field->attributes, $field->options); ?>
	<?php } ?>
</div>