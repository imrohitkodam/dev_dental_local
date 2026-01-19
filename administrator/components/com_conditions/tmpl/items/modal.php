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

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Filter\OutputFilter as JFilterOutput;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper as JLayout;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\User as RL_User;

$extension     = RL_Input::getCmd('extension');
$item_id       = RL_Input::getInt('item_id');
$table         = RL_Input::getCmd('table');
$name_column   = RL_Input::getCmd('name_column');
$enabled_types = RL_Input::getString('enabled_types');
$message       = RL_Input::get('message', '');

if (empty($extension))
{
    JFactory::getApplication()->enqueueMessage(
        JText::_('Direct access forbidden.'),
        'error'
    );

    return;
}

RL_Document::style('regularlabs.admin-form');

$listOrder = RL_String::escape($this->state->get('list.ordering'));
$listDirn  = RL_String::escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'a.name');

$canCreate  = RL_User::authorise('core.create', 'com_conditions');
$canEdit    = RL_User::authorise('core.edit', 'com_conditions');
$canChange  = RL_User::authorise('core.edit.state', 'com_conditions');
$canCheckin = RL_User::authorise('core.manage', 'com_checkin');

$form_url = 'index.php?option=com_conditions&view=items'
    . '&layout=modal&tmpl=component'
    . '&extension=' . $extension
    . '&item_id=' . $item_id
    . '&table=' . $table
    . '&enabled_types=' . $enabled_types
    . '&message=' . $message
    . '&name_column=' . $name_column;
$link_url = 'index.php?option=com_conditions&view=item'
    . '&task=item.map'
    . '&extension=' . $extension
    . '&item_id=' . $item_id
    . '&table=' . $table
    . '&enabled_types=' . $enabled_types
    . '&message=' . $message
    . '&name_column=' . $name_column;

$this->filterForm && $this->filterForm->removeField('state', 'filter');

?>
<form action="<?php echo JRoute::_($form_url); ?>"
      method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <?php
    // Search tools bar
    echo JLayout::render('joomla.searchtools.default', ['view' => $this]);
    ?>
    <table class="table table-striped" id="itemList">
        <thead>
            <tr>
                <th scope="col" class="">
                    <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
                </th>
                <th scope="col" class="d-none d-md-table-cell">
                    <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
                </th>
                <th scope="col" class="w-1 text-nowrap text-center d-none d-md-table-cell">
                    <?php echo JText::_('CON_USAGE'); ?>
                </th>
                <th scope="col" class="w-1 text-nowrap text-center d-none d-md-table-cell">
                    <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($this->items)): ?>
                <tr>
                    <td colspan="4">
                        <?php echo JText::_('RL_NO_ITEMS_FOUND'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($this->items as $i => $item) : ?>
                    <?php
                    $canCheckinItem = ($canCheckin || $item->checked_out == 0 || $item->checked_out == RL_User::getId());
                    $canChangeItem  = ($canChange && $canCheckinItem);
                    $isPublished    = $item->published === 1;

                    $description = explode('---', $item->description);
                    ?>
                    <tr class="row<?php echo $i % 2; ?><?php echo $isPublished ? '' : ' muted'; ?>"
                        data-draggable-group="<?php echo JFilterOutput::stringURLSafe($item->category) ?: 'no-group'; ?>">
                        <td>
                            <?php if ($isPublished) : ?>
                                <a class="btn btn-primary"
                                   title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', RL_String::escape($item->alias)); ?>"
                                   href="<?php echo JRoute::_($link_url . '&id=' . $item->id); ?>">
                                    <?php echo RL_String::escape($item->name); ?>
                                </a>
                            <?php else: ?>
                                <?php echo RL_String::escape($item->name); ?>
                            <?php endif; ?>

                            <div class="small break-word">
                                <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', RL_String::escape($item->alias)); ?>
                            </div>
                            <?php if ($item->category) : ?>
                                <div class="small">
                                    <?php echo JText::_('JCATEGORY') . ': '; ?>
                                    <span class="badge bg-info rl-badge">
                                        <?php echo RL_String::escape($item->category); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td class="d-none d-md-table-cell">
                            <span><?php echo nl2br(RL_String::escape(trim($description[0]))); ?></span>
                            <?php if ( ! empty($description[1])) : ?>
                                <div role="tooltip"><?php echo nl2br(RL_String::escape(trim($description[1]))); ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="text-center d-none d-md-table-cell">
                            <span class="badge bg-secondary<?php echo ! $item->nr_of_uses ? ' opacity-50' : ''; ?>">
                                <?php echo $item->nr_of_uses; ?>
                            </span>
                        </td>
                        <td class="text-center d-none d-md-table-cell">
                            <?php echo (int) $item->id; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php // load the pagination. ?>
    <?php echo $this->pagination->getListFooter(); ?>

    <?php echo JHtml::_('form.token'); ?>
</form>
