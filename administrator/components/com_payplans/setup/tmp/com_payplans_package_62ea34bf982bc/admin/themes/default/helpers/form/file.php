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
<div class="o-form-custom-file <?php echo $allowInput ? '' : 't-hidden'; ?>" data-pp-cd-file-form>
	<div data-pp-cd-attachment-input>
		<input type="file" name="<?php echo $name;?>" class="o-form-control <?php echo $classes; ?>" <?php echo $attributes;?> data-pp-file-input />
		<label for="<?php echo $id;?>" class="o-form-label" data-btn-content="Browse"><?php echo $required ? '*' : ''; ?><?php echo $label;?></label>
	</div>

	<?php echo $this->html('attachment.item', '', [
		'saved' => false,
		'download' => false
	]); ?>
</div>