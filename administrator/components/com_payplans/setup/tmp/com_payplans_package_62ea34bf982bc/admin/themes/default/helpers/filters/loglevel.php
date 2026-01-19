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
<div class="app-filter-bar__cell app-filter-bar__cell--divider-left"
<?php if ($minWidth) { ?>
	style="min-width: <?php echo $minWidth;?>px !important;"
<?php } ?>
>
	<div class="app-filter-bar__filter-wrap">
		<div class="app-filter-select-group">
			<select name="<?php echo $name;?>" class="o-form-control" data-fd-select2="<?php echo $this->fd->getName();?>" data-theme="backend" data-fd-table-filter="<?php echo $this->fd->getName();?>"
				data-appearance="<?php echo $this->fd->getAppearance();?>"
			>
				<?php foreach ($options as $value => $title) { ?>
					<option value="<?php echo $value;?>"<?php echo $selected == (string) $value ? ' selected="selected"' : '';?>><?php echo JText::_($title); ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
</div>