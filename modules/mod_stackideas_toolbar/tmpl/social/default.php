<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="sso space-y-sm border-t border-solid border-gray-200 pt-sm">
	<?php foreach ($buttons as $type => $button) { ?>
		<?php echo $this->fd->html('button.' . $type, $button->title, [
			'authorizedUrl' => $button->authorizedUrl,
			'block' => true,
			'attributes' => $button->attributes,
		]); ?>
	<?php } ?>
</div>
