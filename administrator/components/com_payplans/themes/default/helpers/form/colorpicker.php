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
<div data-colorpicker>
	<input type="text" name="<?php echo $name;?>" class="o-form-control minicolors hex minicolors-input" value="<?php echo $value; ?>" style="padding-left: 30px;" />

	<?php if ($revert && $value) { ?>
	<a href="javascript:void(0);" class="btn btn-pp-default-o" data-colorpicker-revert data-color="<?php echo $revert;?>">
		<i class="fdi fa fa-undo"></i>
	</a>
	<?php } ?>
</div>