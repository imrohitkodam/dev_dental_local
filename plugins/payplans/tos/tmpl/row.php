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
<div class="o-form-group">
	<div class="o-checkbox">
		<input id="tos-checkbox-<?php echo $appId;?>" name="tos-<?php echo $appId;?>" type="checkbox" data-tos-checkbox data-tos-checkbox-<?php echo $appId; ?> />
		<label for="tos-checkbox-<?php echo $appId;?>">
			<a href="javascript:void(0);" data-tos-link data-id="<?php echo $appId?>"><?php echo JText::_($title);?></a>
		</label>
	</div>

	<div class="t-text--danger" data-pp-tos-message></div>
</div>
