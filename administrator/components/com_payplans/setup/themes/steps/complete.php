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
<div class="mb-5 text-center">
	<b>PayPlans</b> has been successfully installed on your site and you may start using it.
</div>

<div class="d-flex justify-content--c mb-3">
	<div class="pr-3">
		<a href="<?php echo PPR::_('index.php?option=com_payplans');?>" class="btn btn-outline-secondary" target="_blank">
			Launch Frontend
		</a>
	</div>
	<div class="pl-3">
		<a href="<?php echo JURI::root();?>administrator/index.php?option=com_payplans" class="btn btn-outline-secondary">
			Launch Backend
		</a>
	</div>
</div>