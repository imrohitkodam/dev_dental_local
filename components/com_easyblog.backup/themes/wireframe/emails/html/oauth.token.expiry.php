<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<tr>
    <td style="background: #55585d; color: #fff; line-height: 1; font-size: 18px; font-weight: bold; text-align: center; padding: 30px;">
        <?php echo JText::sprintf('COM_EASYBLOG_EMAIL_FACEBOOK_TOKEN_MESSAGE_TITLE', $type); ?>
    </td>
</tr>

<tr>
    <td style="background: #fff; padding: 40px 100px; text-align: center">
        <?php echo JText::sprintf('COM_EASYBLOG_EMAIL_FACEBOOK_TOKEN_MESSAGE_CONTENT', $site, $type);?>
    </td>
</tr>
