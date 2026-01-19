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
<div class="pp-checkout-item">
	<div class="pp-checkout-item__title"><?php echo strtoupper(JText::_('COM_PP_TERMS_AND_CONDITIONS'));?></div>

	<div class="pp-checkout-item__content">
		<?php echo $appOutput;?>
	</div>
</div>