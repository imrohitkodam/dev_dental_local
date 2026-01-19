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
<div class="flex flex-col md:flex-row hover:bg-gray-100 px-xs py-md rounded-md <?php echo !$visible ? 't-hidden' : '';?>" <?php echo $wrapperAttributes;?>>
	<div class="inline-flex align-middle md:mb-0 md:pr-md md:w-5/12 w-full flex-shrink-0">&nbsp;</div>

	<div class="flex-grow">
		<?php echo $this->html('form.rewriter'); ?>
	</div>
</div>