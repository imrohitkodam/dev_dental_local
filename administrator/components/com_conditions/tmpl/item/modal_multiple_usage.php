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
use RegularLabs\Component\Conditions\Administrator\Helper\Helper;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\User as RL_User;

$canCreate = RL_User::authorise('core.create', 'com_conditions');
$canEdit   = RL_User::authorise('core.edit', 'com_conditions');

$extension     = RL_Input::getCmd('extension');
$item_id       = RL_Input::getInt('item_id');
$table         = RL_Input::getCmd('table');
$name_column   = RL_Input::getCmd('name_column');
$enabled_types = RL_Input::getString('enabled_types');
$message       = RL_Input::get('message', '');

$current_item_name = $this->item->usage[$extension][$item_id]->item_name ?? null;

$url = 'index.php?option=com_conditions'
    . '&view=item'
    . '&id=' . $this->item->id
    . '&extension=' . $extension
    . '&item_id=' . $item_id
    . '&table=' . $table
    . '&name_column=' . $name_column
    . '&enabled_types=' . $enabled_types
    . '&message=' . $message
    . '&layout=modal_edit'
    . '&tmpl=component';
?>

<div class="alert alert-warning">
    <p><?php echo JText::_('CON_USED_ON'); ?></p>

    <?php if ( ! empty($this->item->name)) : ?>
        <h3><?php echo RL_String::escape($this->item->name); ?></h3>
    <?php endif; ?>

    <?php foreach ($this->item->usage as $extension_name => $extension_usage) : ?>
        <?php $extension_name = Helper::getExtensionName($extension_name); ?>
        <span class="badge badge-sm bg-secondary"><?php echo $extension_name; ?></span>
        <ul class="mb-1">
            <?php foreach ($extension_usage as $usage_item) : ?>
                <?php $is_current = ($usage_item->extension == $extension && $usage_item->item_id == $item_id); ?>
                <li>
                    <?php echo $is_current ? '<strong>' : ''; ?>
                    [<?php echo $usage_item->item_id; ?>] <?php echo $usage_item->item_name; ?>
                    <?php echo $is_current ? '</strong>' : ''; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endforeach; ?>
</div>

<div class="text-center">
    <?php if ($canCreate && $canEdit) : ?>
        <p><?php echo JText::_('CON_WHAT_TO_DO'); ?></p>
    <?php endif; ?>
    <p>
        <?php if ($canCreate) : ?>
            <a class="btn btn-primary" title="<?php echo JText::_('CON_BUTTON_COPY', true); ?>"
               href="<?php echo JRoute::_($url . '&task=item.copy'); ?>">
                <span class="icon-copy" aria-hidden="true"></span>
                <?php echo JText::sprintf('CON_BUTTON_CONFIRM_COPY', $current_item_name ? ': ' . $current_item_name : ''); ?>
            </a>
        <?php endif; ?>
        <?php if ($canEdit) : ?>
            <a class="btn btn-warning" title="<?php echo JText::_('CON_BUTTON_EDIT', true); ?>"
               href="<?php echo JRoute::_($url . '&task=item.edit'); ?>">
                <span class="icon-edit" aria-hidden="true"></span>
                <?php echo JText::sprintf('CON_BUTTON_CONFIRM_EDIT', $current_item_name ? ': ' . $current_item_name : ''); ?>
            </a>
        <?php endif; ?>
    </p>
</div>
