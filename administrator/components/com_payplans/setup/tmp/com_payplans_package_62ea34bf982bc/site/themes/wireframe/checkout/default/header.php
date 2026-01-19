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
<div class="text-center">
	<?php if ($this->config->get('checkout_display_logo')) { ?>
		<?php echo $this->output('site/checkout/default/logo'); ?>
	<?php } ?>
</div>

<hr class="flex h-[1px] border-none bg-gray-300">