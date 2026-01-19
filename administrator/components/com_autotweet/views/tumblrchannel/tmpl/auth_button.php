<?php
/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - A powerful social content platform to manage multiple social networks.
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2020 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        https://www.extly.com
 */

defined('_JEXEC') or die;

?>
<p id="authorizeTumblr" class="text-center">
	<a id="authorizeButton" href="<?php

	echo $authUrl;
	?>" class="btn btn-info <?php

	echo $authUrlButtonStyle;

	?>"><?php

	echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTHBUTTON_TITLE');

	?></a>
</p>
<p>
<?php

	echo $message;

?>
</p>
<p><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TUMBLR_AFTER_CLICK'); ?></p>
