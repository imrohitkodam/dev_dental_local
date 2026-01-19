<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\User as RL_User;

$canDelete = RL_User::authorise('core.delete', 'com_conditions');

$extension   = RL_Input::getCmd('extension');
$item_id     = RL_Input::getInt('item_id');
$table       = RL_Input::getCmd('table');
$name_column = RL_Input::getCmd('name_column');

$current_item_name = $this->item->usage[$extension][$item_id]->item_name ?? null;

$url = 'index.php?option=com_conditions'
    . '&view=item'
    . '&id=' . $this->item->id
    . '&extension=' . $extension
    . '&item_id=' . $item_id
    . '&table=' . $table
    . '&name_column=' . $name_column
    . '&task=item.remove_mapping'
    . '&tmpl=component'
?>
<div class="text-center">
    <p>
        <?php echo JText::_('CON_ONLY_ITEM_USING_CONDITION'); ?>
        <?php if ($canDelete) : ?>
            <br>
            <?php echo JText::_('CON_WHAT_TO_DO'); ?>
        <?php endif; ?>
    </p>
    <p>
        <a class="btn btn-primary" title="<?php echo JText::_('CON_BUTTON_REMOVE', true); ?>"
           href="<?php echo JRoute::_($url . '&remove=1'); ?>">
            <span class="icon-times" aria-hidden="true"></span>
            <?php echo JText::sprintf('CON_BUTTON_CONFIRM_REMOVE_BUT_KEEP', $current_item_name ? ': ' . $current_item_name : ''); ?>
        </a>
        <?php if ($canDelete) : ?>
            <a class="btn btn-danger" title="<?php echo JText::_('CON_BUTTON_REMOVE', true); ?>"
               href="<?php echo JRoute::_($url . '&remove=all'); ?>"
               onclick="return confirm('<?php echo JText::_('RL_ARE_YOU_SURE', true); ?>')">
                <span class="icon-trash" aria-hidden="true"></span>
                <?php echo JText::_('CON_BUTTON_CONFIRM_REMOVE_COMPLETELY'); ?>
            </a>
        <?php endif; ?>
    </p>
</div>
