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

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper as JLayout;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;

defined('_JEXEC') or die;

RL_Document::style('regularlabs.admin-form');
RL_Language::load('com_snippets');

$params = RL_Parameters::getComponent('snippets');

$request = RL_Input::getBase64('request');

if ($request)
{
    $request = json_decode(base64_decode($request));
}

$editor_name = RL_Input::getString('editor', 'text');

// Remove any dangerous character to prevent cross site scripting
$editor_name = RL_RegEx::replace('[\'\";\s]', '', $editor_name);

RL_Document::script('snippets.popup');

$ordering = $this->state->get('list.fullordering', '');

if (empty($ordering))
{
    $ordering = $this->state->get('list.ordering', 'a.ordering')
        . ' ' . $this->state->get('list.direction', 'ASC');
}

[$listOrder, $listDirn] = explode(' ', $ordering);

$listOrder = RL_String::escape($listOrder);
$listDirn  = RL_String::escape($listDirn);

$has_descriptions = false;
$has_categories   = false;

foreach ($this->items as $i => $item)
{
    if ($item->published != 1)
    {
        unset($this->items[$i]);
        continue;
    }


    if ($item->description)
    {
        $has_descriptions = true;
    }

    if ($item->category)
    {
        $has_categories = true;
    }
}

$cols = 3;
$cols += ($has_descriptions ? 1 : 0);
$cols += ($has_categories ? 1 : 0);

?>

<form action="index.php" id="adminForm" name="adminForm" method="post" class="rl-form labels-sm">
    <?php
    // Search tools bar
    echo JLayout::render('joomla.searchtools.default', ['view' => $this]);
    ?>
    <table class="table table-striped" id="itemList">
        <thead>
            <tr>
                <th scope="col" class="w-1 text-nowrap text-center d-none d-md-table-cell">
                    <?php echo JHtml::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
                </th>
                <th scope="col" class="w-20">
                    <?php echo JHtml::_('searchtools.sort', 'JFIELD_ALIAS_LABEL', 'a.alias', $listDirn, $listOrder); ?>
                </th>
                <th scope="col" class="w-20">
                    <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_TITLE', 'a.name', $listDirn, $listOrder); ?>
                </th>
                <?php if ($has_descriptions) : ?>
                    <th scope="col" class="d-none d-md-table-cell">
                        <?php echo JHtml::_('searchtools.sort', 'JGLOBAL_DESCRIPTION', 'a.description', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>
                <?php if ($has_categories) : ?>
                    <th scope="col" class="w-3 text-nowrap d-none d-md-table-cell">
                        <?php echo JHtml::_('searchtools.sort', 'JCATEGORY', 'a.category', $listDirn, $listOrder); ?>
                    </th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($this->items)): ?>
                <tr>
                    <td colspan="<?php echo $cols; ?>">
                        <?php echo JText::_('RL_NO_ITEMS_FOUND'); ?>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($this->items as $i => $item) : ?>
                    <?php
                    $vars      = '[]';
                    $fill_vars = false;
                    ?>
                    <tr class="row<?php echo $i % 2; ?>">
                        <td class="text-center text-nowrap d-none d-md-table-cell">
                            <button
                                    onclick="RegularLabs.SnippetsPopup.insertById('<?php echo $editor_name; ?>', <?php echo (int) $item->id; ?>, <?php echo $vars; ?>, <?php echo $fill_vars; ?>);"
                                    type="button" class="btn btn-secondary btn-sm text-left">
                                <span class="fa fa-file-code me-1" aria-hidden="true"></span>
                                <?php echo (int) $item->id; ?>
                            </button>
                        </td>
                        <td class="text-nowrap">
                            <button
                                    onclick="RegularLabs.SnippetsPopup.insertByAlias('<?php echo $editor_name; ?>', '<?php echo RL_String::escape($item->alias); ?>', <?php echo $vars; ?>, <?php echo $fill_vars; ?>);"
                                    type="button" class="btn btn-secondary btn-sm text-left">
                                <span class="fa fa-file-code me-1" aria-hidden="true"></span>
                                <?php echo RL_String::escape($item->alias); ?>
                            </button>
                        </td>
                        <td>
                            <button
                                    onclick="RegularLabs.SnippetsPopup.insertByTitle('<?php echo $editor_name; ?>', '<?php echo RL_String::escape($item->name); ?>', <?php echo $vars; ?>, <?php echo $fill_vars; ?>);"
                                    type="button" class="btn btn-secondary btn-sm text-left">
                                <span class="fa fa-file-code me-1" aria-hidden="true"></span>
                                <?php echo RL_String::escape($item->name); ?>
                            </button>
                        </td>
                        <?php if ($has_descriptions) : ?>
                            <?php
                            $description = explode('---', $item->description);
                            ?>
                            <td class="d-none d-md-table-cell">
                                <span><?php echo nl2br(RL_String::escape(trim($description[0]))); ?></span>
                                <?php if ( ! empty($description[1])) : ?>
                                    <div role="tooltip"><?php echo nl2br(RL_String::escape(trim($description[1]))); ?></div>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <?php if ($has_categories) : ?>
                            <td class="d-none d-md-table-cell">
                                <?php
                                $category      = $item->category;
                                $category_icon = '';

                                if (str_contains($category, '::'))
                                {
                                    [$category, $category_icon] = explode('::', $category, 2);
                                    $category_icon = '<span class="icon-' . $category_icon . '"></span>';
                                }

                                echo $category ? '<span class="badge rl-bg-teal">' . $category_icon . $category . '</span>' : '';
                                ?>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php echo $this->pagination->getListFooter(); ?>

    <input type="hidden" name="rl_qp" value="1">
    <input type="hidden" name="tmpl" value="component">
    <input type="hidden" name="class" value="Plugin.EditorButton.Snippets.Popup">
    <input type="hidden" name="editor" value="<?php echo $editor_name; ?>">
    <input type="hidden" name="request" value="<?php echo RL_Input::getBase64('request'); ?>">
    <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>">
    <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>">
</form>
