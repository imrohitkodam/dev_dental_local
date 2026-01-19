<?php
/**
 * @package         Snippets
 * @version         9.3.8
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
use Joomla\CMS\Session\Session;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\DownloadKey as RL_DownloadKey;
use RegularLabs\Library\Form\Field\MiniColorField;
use RegularLabs\Library\License as RL_License;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\Version as RL_Version;

RL_Document::style('regularlabs.admin-form');

$listOrder = RL_String::escape($this->state->get('list.ordering'));
$listDirn  = RL_String::escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'a.ordering');

$user       = JFactory::getUser();
$canCreate  = $user->authorise('core.create', 'com_snippets');
$canEdit    = $user->authorise('core.edit', 'com_snippets');
$canChange  = $user->authorise('core.edit.state', 'com_snippets');
$canCheckin = $user->authorise('core.manage', 'com_checkin');
$saveOrder  = $listOrder == 'a.ordering' && ! empty($this->items);

if ($saveOrder)
{
    $saveOrderingUrl = 'index.php?option=com_snippets&task=items.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    JHtml::_('draggablelist.draggable');
}

$showColors     = $this->config->use_colors;
$showCategories = ($this->hasCategories && $this->config->use_categories);

$cols = 7;
$cols += ($showColors ? 1 : 0);
$cols += ($showCategories ? 1 : 0);


if ($this->config->show_update_notification)
{
    // Version check
    echo RL_Version::getMessage('SNIPPETS');
}
?>
    <form action="<?php echo JRoute::_('index.php?option=com_snippets&view=items'); ?>"
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
                    <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                        <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                    </th>
                    <?php if ($showColors) : ?>
                        <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                            <?php echo JHtml::_('searchtools.sort', '', 'a.color', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>
                    <th scope="col" class="w-1 text-nowrap text-center">
                        <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="">
                        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="d-none d-md-table-cell">
                        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
                    </th>
                    <th scope="col" class="w-10">
                        <?php echo JHtml::_('searchtools.sort', 'JFIELD_ALIAS_LABEL', 'a.alias', $listDirn, $listOrder); ?>
                    </th>
                    <?php if ($showCategories) : ?>
                        <th scope="col" class="w-3 text-nowrap d-none d-md-table-cell">
                            <?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'a.category', $listDirn, $listOrder); ?>
                        </th>
                    <?php endif; ?>
                    <th scope="col" class="w-1 text-nowrap text-center d-none d-md-table-cell">
                        <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                    </th>
                </tr>
            </thead>
            <tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>

                <?php if (empty($this->items)): ?>
                    <tr>
                        <td colspan="<?php echo $cols; ?>">
                            <?php echo JText::_('RL_NO_ITEMS_FOUND'); ?>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($this->items as $i => $item) : ?>
                        <?php
                        $canCheckinItem = ($canCheckin || $item->checked_out == 0 || $item->checked_out == $user->get('id'));
                        $canChangeItem  = ($canChange && $canCheckinItem);

                        $description = explode('---', $item->description);
                        ?>
                        <tr class="row<?php echo $i % 2; ?>"
                            data-draggable-group="<?php echo JFilterOutput::stringURLSafe($item->category) ?: 'no-group'; ?>">
                            <td class="text-center d-none d-md-table-cell">
                                <?php echo JHtml::_('grid.id', $i, $item->id); ?>
                            </td>
                            <td class="order text-nowrap text-center d-none d-md-table-cell d-none d-md-table-cell">
                                <?php
                                $iconClass = '';

                                if ( ! $canChange)
                                {
                                    $iconClass = ' inactive';
                                }
                                elseif ( ! $saveOrder)
                                {
                                    $iconClass = ' inactive" title="' . JText::_('JORDERINGDISABLED');
                                }
                                ?>
                                <span class="sortable-handler<?php echo $iconClass ?>">
                                    <span class="icon-ellipsis-v"></span>
                                </span>
                                <?php if ($canChange && $saveOrder) : ?>
                                    <input type="text"
                                           class="hidden"
                                           name="order[]"
                                           size="5"
                                           value="<?php echo $item->ordering; ?>">
                                <?php endif; ?>
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
                                            table="snippets"
                                            item_id="' . $item->id . '"
                                            />'
                                    );

                                    $element->value = $color;

                                    $colorfield->setup($element, $color);

                                    echo $colorfield->__get('input');
                                    ?>
                                </td>
                            <?php endif; ?>
                            <td class="text-center">
                                <?php echo JHtml::_('jgrid.published', $item->published, $i, 'items.', $canChangeItem); ?>
                            </td>
                            <td>
                                <?php if ($item->checked_out) : ?>
                                    <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
                                <?php endif; ?>
                                <?php if ($canEdit) : ?>
                                    <a href="<?php echo JRoute::_('index.php?option=com_snippets&task=item.edit&id=' . $item->id); ?>">
                                        <?php echo RL_String::escape($item->name); ?></a>
                                <?php else : ?>
                                    <?php echo RL_String::escape($item->name); ?>
                                <?php endif; ?>
                            </td>
                            <td class="d-none d-md-table-cell">
                                <span><?php echo nl2br(RL_String::escape(trim($description[0]))); ?></span>
                                <?php if ( ! empty($description[1])) : ?>
                                    <div role="tooltip"><?php echo nl2br(RL_String::escape(trim($description[1]))); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php echo RL_String::escape($item->alias); ?>
                            </td>
                            <?php if ($showCategories) : ?>
                                <td class="d-none d-md-table-cell">
                                    <?php echo $item->category ? '<span class="label label-default">' . $item->category . '</span>' : ''; ?>
                                </td>
                            <?php endif; ?>
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

        <?php if ($canCreate) : ?>
            <?php echo JHtml::_(
                'bootstrap.renderModal',
                'importModal',
                [
                    'title'  => JText::_('RL_IMPORT_ITEMS'),
                    'footer' => $this->loadTemplate('import_footer'),
                ],
                $this->loadTemplate('import_body')
            ); ?>
        <?php endif; ?>

        <input type="hidden" name="task" value="">
        <input type="hidden" name="boxchecked" value="0">
        <?php echo JHtml::_('form.token'); ?>
    </form>
<?php

// PRO Check
echo RL_License::getMessage('SNIPPETS');

// Copyright
echo RL_Version::getFooter('SNIPPETS');
