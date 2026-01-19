<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="eb-composer-fieldset" <?php echo $wrapperAttributes;?>>
	<div class="eb-composer-fieldset-header">
		<label for="<?php echo $name;?>"><?php echo $title; ?></label>
	</div>
	<div class="eb-composer-fieldset-content o-form-horizontal">
		<?php echo $this->html($type, $name, $value, $name, $attributes); ?>
	</div>

	<?php if ($help) { ?>
	<div class="eb-composer-fieldset-help t-text--muted t-lg-mt--md">
		<?php echo $help; ?>
	</div>
	<?php } ?>
</div>
