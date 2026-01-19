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

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Filter\OutputFilter as JFilterOutput;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper as JLayout;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\Field\MiniColorField;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\User as RL_User;
use RegularLabs\Library\Version as RL_Version;

$canAccess = RL_User::authorise('core.admin', 'com_conditions');

if ( ! $canAccess)
{
    throw new NotAllowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$canCreate  = RL_User::authorise('core.create', 'com_conditions');
$canEdit    = RL_User::authorise('core.edit', 'com_conditions');
$canChange  = RL_User::authorise('core.edit.state', 'com_conditions');
$canCheckin = RL_User::authorise('core.manage', 'com_checkin');

RL_Document::style('regularlabs.admin-form');

$listOrder = RL_String::escape($this->state->get('list.ordering'));
$listDirn  = RL_String::escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'a.name');

$filter_state = $this->state->get('filter.state') ?: 1;

$showColors     = $this->config->use_colors;
$showCategories = ($this->hasCategories && $this->config->use_categories);
?>
    <form action="<?php echo JRoute::_('index.php?option=com_conditions&view=items'); ?>"
          method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
        <?php
        // Search tools bar
        echo JLayout::render('joomla.searchtools.default', ['view' => $this]);
        ?>
        <table class="table table-striped" id="itemList">
            <thead>
                <tr>
                    <td scope="col" class="w-1 text-center">
                        <?php echo JHtml::_('grid.checkall'); ?>
                    </td>
                    <?php if ($showColors) : ?>
                        <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.color', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>
                    <?php if ($filter_state != 1): ?>
                        <th scope="col" class="w-1 text-nowrap text-center">
                            <?php echo JText::_('JSTATUS'); ?>
                        </th>
                    <?php endif; ?>
                    <th scope="col" class="">
                        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="d-none d-md-table-cell">
                        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
                    </th>
                    <?php if ($showCategories) : ?>
                        <th scope="col" class="w-10 d-none d-md-table-cell">
                            <?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'a.category', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>
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
                        <td colspan="5">
                            <?php echo JText::_('RL_NO_ITEMS_FOUND'); ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <?php
                        $canCheckinItem = ($canCheckin || $item->checked_out == 0 || $item->checked_out == RL_User::getId());
                        $canChangeItem  = ($canChange && $canCheckinItem);

                        $description = explode('---', $item->description);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>"
                            data-draggable-group="<?php echo JFilterOutput::stringURLSafe($item->category) ?: 'no-group'; ?>">
                            <td class="text-center d-none d-md-table-cell">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <?php if ($showColors) : ?>
                                <td class="center inlist">
                                    <?php
                                    $colorfield = new MiniColorField;

                                    $color = $item->color ?? '';

                                    $element = new SimpleXMLElement(
                                        '<field
                                            id="color_' . $i . '"
                                            name="color_' . $i . '"
                                            type="MiniColor"
                                            default=""
                                            colors="' . ($this->config->main_colors ?? '') . '"
                                            table="conditions"
                                            item_id="' . $item->id . '"
                                            />'
                                    );

                                    $element->value = $color;

                                    $colorfield->setup($element, $color);

                                    echo $colorfield->__get('input');
                                    ?>
                                </td>
                            <?php endif; ?>
                            <?php if ($filter_state != 1): ?>
                                <td class="text-center">
                                    <span class="tbody-icon">
                                        <?php echo JLayout::render('joomla.icon.iconclass', ['icon' => $item->published == 1 ? 'icon-check' : 'icon-trash']); ?>
                                    </span>
                                </td>
                            <?php endif; ?>
                            <td>
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_conditions&task=item.edit&id=' . $item->id); ?>">
                                        <?php echo RL_String::escape($item->name); ?></a>
                                <?php else : ?>
                                    <span title="<?php echo JText::sprintf('JFIELD_ALIAS_LABEL', RL_String::escape($item->alias)); ?>">
                                        <?php echo RL_String::escape($item->name); ?>
                                    </span>
                                <?php endif; ?>

                                <div class="small break-word">
                                    <?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', RL_String::escape($item->alias)); ?>
                                </div>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span><?php echo nl2br(RL_String::escape(trim($description[0]))); ?></span>
                                <?php if ( ! empty($description[1])) : ?>
                                    <div role="tooltip"><?php echo nl2br(RL_String::escape(trim($description[1]))); ?></div>
                                <?php endif; ?>
                            </td>
                            <?php if ($showCategories) : ?>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $item->category ? '<span class="badge rl-bg-teal">' . $item->category . '</span>' : ''; ?>
                                </td>
                            <?php endif; ?>
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

        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo JHtml::_('form.token'); ?>
    </form>
<?php

// Copyright
echo RL_Version::getFooter('CONDITIONS', true, false);
