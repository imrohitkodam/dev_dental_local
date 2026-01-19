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
<form method="post" action="<?php echo JRoute::_('index.php');?>" data-fd-toolbar-logout-form>
	<input type="hidden" value="com_users"  name="option">
	<input type="hidden" value="user.logout" name="task">
	<input type="hidden" name="<?php echo FH::token();?>" value="1" />
	<input type="hidden" value="<?php echo $return; ?>" name="return" />
</form>