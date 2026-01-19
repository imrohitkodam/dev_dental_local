<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$text = Text::_('JTOOLBAR_CANCEL');
?>
<joomla-toolbar-button>
    <button
            onclick="location.href='index.php?option=com_advancedmodules&view=modules&client_id=<?php echo $displayData['client_id']; ?>'"
            class="btn btn-danger">
        <span class="icon-times" aria-hidden="true"></span> <?php echo $text; ?>
    </button>
</joomla-toolbar-button>
