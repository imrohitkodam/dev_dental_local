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

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\Session\Session;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\DownloadKey as RL_DownloadKey;
use RegularLabs\Library\Form\Field\MiniColorField;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\License as RL_License;
use RegularLabs\Library\Version as RL_Version;
use RegularLabs\Library\User as RL_User;

RL_Language::load('com_modules', JPATH_ADMINISTRATOR);
RL_Language::load('com_conditions');

RL_Document::style('regularlabs.admin-form');
JHtml::_('behavior.multiselect');

$clientId  = (int) $this->state->get('client_id', 0);
$user      = RL_User::get();
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = ($listOrder == 'a.ordering');

if ($saveOrder && ! empty($this->items))
{
    $saveOrderingUrl = 'index.php?option=com_advancedmodules&task=modules.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    JHtml::_('draggablelist.draggable');
}

$showColors     = ($clientId == 0 && $this->config->use_colors);
$showCategories = ($this->hasCategories && $this->config->use_categories);
$isAdmin        = $clientId == 1;


if ($this->config->show_update_notification)
{
    // Version check
    echo RL_Version::getMessage('ADVANCEDMODULEMANAGER');
}
?>
    <form action="<?php echo Route::_('index.php?option=com_advancedmodules&view=modules&client_id=' . $clientId); ?>"
          method="post" name="adminForm" id="adminForm">
        <div id="j-main-container" class="j-main-container">
            <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
            <?php if ($this->total > 0) : ?>
                <table class="table" id="moduleList">
                    <caption class="visually-hidden">
                        <?php echo Text::_('COM_MODULES_TABLE_CAPTION'); ?>,
                        <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                        <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                    </caption>
                    <thead>
                        <tr>
                            <td class="w-1 text-center">
                                <?php echo JHtml::_('grid.checkall'); ?>
                            </td>
                            <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                <?php echo JHtml::_('searchtools.sort', '', 'a.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                            </th>
                            <?php if ($showColors) : ?>
                                <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                    <?php echo JHtml::_('searchtools.sort', '', 'amm.color', $listDirn, $listOrder); ?>
                                </th>
                            <?php endif; ?>
                            <th scope="col" class="w-1 text-center">
                                <?php echo JHtml::_('searchtools.sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="title">
                                <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
                            </th>
                            <?php if ($this->config->show_note == 'column') : ?>
                                <th class="title">
                                    <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_DESCRIPTION', 'a.note', $listDirn, $listOrder); ?>
                                </th>
                            <?php endif; ?>
                            <?php if ($showCategories) : ?>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'amm.category', $listDirn, $listOrder); ?>
                                </th>
                            <?php endif; ?>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo JHtml::_('searchtools.sort', 'COM_MODULES_HEADING_POSITION', 'a.position', $listDirn, $listOrder); ?>
                            </th>
                            <th scope="col" class="w-10 d-none d-md-table-cell">
                                <?php echo JHtml::_('searchtools.sort', 'COM_MODULES_HEADING_MODULE', 'name', $listDirn, $listOrder); ?>
                            </th>
                            <?php if ($isAdmin) : ?>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ACCESS', 'ag.title', $listDirn, $listOrder); ?>
                                </th>
                            <?php endif; ?>
                            <?php if ($isAdmin && ModuleHelper::isAdminMultilang()) : ?>
                                <th scope="col" class="w-10 d-none d-md-table-cell">
                                    <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
                                </th>
                            <?php endif; ?>
                            <th scope="col" class="w-5 d-none d-md-table-cell">
                                <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody <?php if ($saveOrder) : ?> class="js-draggable" data-url="<?php echo $saveOrderingUrl; ?>" data-direction="<?php echo strtolower($listDirn); ?>" data-nested="false"<?php endif; ?>>
                        <?php foreach ($this->items as $i => $item) :
                            $ordering = ($listOrder == 'a.ordering');
                            $canCreate = $user->authorise('core.create', 'com_modules');
                            $canEdit = $user->authorise('core.edit', 'com_modules.module.' . $item->id);
                            $canCheckin = $user->authorise('core.manage', 'com_checkin') || $item->checked_out == $user->id || is_null($item->checked_out);
                            $canChange = $user->authorise('core.edit.state', 'com_modules.module.' . $item->id) && $canCheckin;
                            ?>
                            <tr class="row<?php echo $i % 2; ?>"
                                data-draggable-group="<?php echo $item->position ?: 'none'; ?>">
                                <td class="text-center">
                                    <?php echo JHtml::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
                                </td>
                                <td class="text-center d-none d-md-table-cell">
                                    <?php
                                    $iconClass = '';

                                    if ( ! $canChange)
                                    {
                                        $iconClass = ' inactive';
                                    }
                                    elseif ( ! $saveOrder)
                                    {
                                        $iconClass = ' inactive" title="' . Text::_('JORDERINGDISABLED');
                                    }
                                    ?>
                                    <span class="sortable-handler<?php echo $iconClass; ?>">
                                        <span class="icon-ellipsis-v"></span>
                                    </span>
                                    <?php if ($canChange && $saveOrder) : ?>
                                        <input type="text" name="order[]" size="5"
                                               value="<?php echo $item->ordering; ?>"
                                               class="width-20 text-area-order hidden">
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
                                                table="advancedmodules"
                                                item_id="' . $item->id . '"
                                                id_column="module_id"
                                                />'
                                        );

                                        $element->value = $color;

                                        $colorfield->setup($element, $color);

                                        echo $colorfield->__get('input');
                                        ?>
                                    </td>
                                <?php endif; ?>
                                <td class="text-center">
                                    <?php // Check if extension is enabled
                                    ?>
                                    <?php if ($item->enabled > 0) : ?>
                                        <?php echo JHtml::_('jgrid.published', $item->published, $i, 'modules.', $canChange); ?>
                                    <?php else : ?>
                                        <?php // Extension is not enabled, show a message that indicates this. ?>
                                        <span class="tbody-icon"
                                              title="<?php echo Text::sprintf('COM_MODULES_MSG_MANAGE_EXTENSION_DISABLED', $this->escape($item->name)); ?>">
                                            <span class="icon-minus-circle" aria-hidden="true"></span>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <th scope="row" class="has-context">
                                    <div<?php echo $this->config->show_note == 'tooltip' && ! empty($item->note) ? ' aria-labelledby="title-tooltip-' . $item->id . '"' : ''; ?>>
                                        <?php if ($item->checked_out) : ?>
                                            <?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'modules.', $canCheckin); ?>
                                        <?php endif; ?>
                                        <?php if ($canEdit) : ?>
                                            <a href="<?php echo Route::_('index.php?option=com_advancedmodules&task=module.edit&id=' . (int) $item->id); ?>"
                                               title="<?php echo Text::_('JACTION_EDIT'); ?> <?php echo $this->escape($item->title); ?>">
                                                <?php echo $this->escape($item->title); ?></a>
                                        <?php else : ?>
                                            <?php echo $this->escape($item->title); ?>
                                        <?php endif; ?>
                                        <?php if ($this->config->show_note == 'tooltip' && ! empty($item->note)) : ?>
                                            <span class="icon-info-circle text-muted fs-6 ms-1 align-text-top"></span>
                                        <?php endif; ?>
                                        <?php if ($this->config->show_note == 'name' && ! empty($item->note)) : ?>
                                            <div class="small">
                                                <?php echo Text::sprintf('JGLOBAL_LIST_NOTE', $this->escape($item->note)); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($this->config->show_note == 'tooltip' && ! empty($item->note)) : ?>
                                        <div role="tooltip" id="title-tooltip-<?php echo $item->id; ?>">
                                            <?php echo $this->escape($item->note); ?>
                                        </div>
                                    <?php endif; ?>
                                </th>
                                <?php if ($this->config->show_note == 'column') : ?>
                                    <td class="has-context">
                                        <?php echo $this->escape($item->note); ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($showCategories) : ?>
                                    <td class="small d-none d-md-table-cell">
                                        <?php echo $item->category ? '<span class="badge rl-bg-teal">' . $item->category . '</span>' : ''; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="d-none d-md-table-cell">
                                    <?php if ($item->position) : ?>
                                        <span class="badge bg-info">
                                            <?php echo $item->position; ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="badge bg-secondary">
                                            <?php echo Text::_('JNONE'); ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="small d-none d-md-table-cell">
                                    <?php echo $item->name; ?>
                                </td>
                                <?php if ($isAdmin) : ?>
                                    <td class="small d-none d-md-table-cell">
                                        <?php echo $this->escape($item->access_level); ?>
                                    </td>
                                <?php endif; ?>
                                <?php if ($isAdmin && ModuleHelper::isAdminMultilang()) : ?>
                                    <td class="small d-none d-md-table-cell">
                                        <?php if ($item->language == ''): ?>
                                            <?php echo Text::_('JUNDEFINED'); ?>
                                        <?php elseif ($item->language == '*'): ?>
                                            <?php echo Text::alt('JALL', 'language'); ?>
                                        <?php else: ?>
                                            <?php echo $this->escape($item->language); ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                                <td class="d-none d-md-table-cell">
                                    <?php echo (int) $item->id; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <?php // load the pagination. ?>
                <?php echo $this->pagination->getListFooter(); ?>

            <?php endif; ?>

            <?php // Load the batch processing form. ?>
            <?php if (
                $user->authorise('core.create', 'com_modules')
                && $user->authorise('core.edit', 'com_modules')
                && $user->authorise('core.edit.state', 'com_modules')
            ) : ?>
                <?php echo JHtml::_(
                    'bootstrap.renderModal',
                    'collapseModal',
                    [
                        'title'  => Text::_('COM_MODULES_BATCH_OPTIONS'),
                        'footer' => $this->loadTemplate('batch_footer'),
                    ],
                    $this->loadTemplate('batch_body')
                ); ?>
            <?php endif; ?>
            <input type="hidden" name="task" value="">
            <input type="hidden" name="boxchecked" value="0">
            <?php echo JHtml::_('form.token'); ?>
        </div>
    </form>

<?php if ($this->config->show_switch) : ?>
    <div class="form-control-plaintext text-right">
        <a href="<?php echo JRoute::_('index.php?option=com_modules&force=1'); ?>">
            <?php echo JText::_('AMM_SWITCH_TO_CORE'); ?>
        </a>
    </div>
<?php endif; ?>

<?php

// PRO Check
echo RL_License::getMessage('ADVANCEDMODULEMANAGER');

// Copyright
echo RL_Version::getFooter('ADVANCEDMODULEMANAGER');
