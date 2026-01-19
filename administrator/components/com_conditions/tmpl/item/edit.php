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
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper as JLayout;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Component\Conditions\Administrator\Helper\Helper;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\User as RL_User;

$canAccess = RL_User::authorise('core.admin', 'com_conditions');
$canEdit   = RL_User::authorise('core.edit', 'com_conditions');

if ( ! $canAccess || ! $canEdit)
{
    throw new NotAllowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

RL_Document::useScript('keepalive');
RL_Document::useScript('form.validate');
RL_Document::usePreset('choicesjs');
RL_Document::useScript('webcomponent.field-fancy-select');
RL_Document::script('regularlabs.regular');
RL_Document::script('regularlabs.admin-form');
RL_Document::script('regularlabs.admin-form-descriptions');
RL_Document::script('regularlabs.treeselect');
RL_Document::script('conditions.script');

$script = "document.addEventListener('DOMContentLoaded', function(){RegularLabs.Conditions.init()});";
RL_Document::scriptDeclaration($script, 'Conditions', true, 'after');

RL_Language::load('com_content', JPATH_ADMINISTRATOR);
?>

<form action="<?php echo JRoute::_('index.php?option=com_conditions&id=' . (int) $this->item->id); ?>"
      method="post" name="adminForm" id="conditionsForm"
      aria-label="<?php echo JText::_('COM_CONDITIONS_FORM_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>"
      class="form-validate rl-form">
    <?php echo JLayout::render('joomla.edit.title_alias', $this); ?>

    <fieldset class="mt-3">
        <?php echo JHtml::_('uitab.startTabSet', 'main', ['active' => 'details']); ?>

        <?php echo JHtml::_('uitab.addTab', 'main', 'details', JText::_('CON_RULES')); ?>
        <div class="hide-on-update-summary position-relative">
            <div class="rl-spinner rl-spinner-lg"></div>
        </div>
        <div class="row show-on-update-summary hidden">
            <div id="conditionsFormFields" class="col-lg-8">
                <?php echo $this->form->renderFieldset('rules'); ?>
            </div>
            <div class="col-lg-4">
                <h3><?php echo JText::_('CON_SUMMARY'); ?></h3>
                <div id="rules_summary" class="position-relative">
                    <div id="rules_summary_content" class="hidden"></div>
                </div>
            </div>
        </div>
        <?php echo JHtml::_('uitab.endTab'); ?>

        <?php
        $title = JText::_('CON_USAGE')
            . ' <span class="badge bg-secondary">' . $this->item->nr_of_uses . '</span>';
        ?>
        <?php echo JHtml::_('uitab.addTab', 'main', 'variables', $title); ?>
        <?php if (empty($this->item->usage)): ?>
            <div class="alert alert-warning">
                <?php echo JText::_('CON_NOT_USED'); ?>
            </div>
        <?php else: ?>
            <p><?php echo JText::_('CON_USED_ON'); ?></p>
            <?php foreach ($this->item->usage as $extension_name => $extension_usage) : ?>
                <?php $extension_name = Helper::getExtensionName($extension_name); ?>
                <h3><?php echo $extension_name; ?></h3>
                <ul>
                    <?php foreach ($extension_usage as $usage_items) : ?>
                        <li>
                            <?php if ($usage_items->url): ?>
                                <a href="<?php echo $usage_items->url; ?>" target="_blank">
                                    <?php echo $usage_items->item_name; ?>
                                </a>
                            <?php else: ?>
                                <?php echo $usage_items->item_name; ?>
                            <?php endif; ?>
                            <small>[<?php echo JText::_('JGLOBAL_FIELD_ID_LABEL'); ?>
                                : <?php echo $usage_items->item_id; ?>]</small>
                            <?php if ($usage_items->published !== 1): ?>
                                <span class="badge bg-danger"><?php echo Helper::getPublishStateString($usage_items->published); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endforeach; ?>
        <?php endif; ?>
        <?php echo JHtml::_('uitab.endTab'); ?>

        <?php echo JHtml::_('uitab.addTab', 'main', 'variables', JText::_('JDETAILS')); ?>
        <?php echo $this->form->renderFieldset('details'); ?>
        <?php echo JHtml::_('uitab.endTab'); ?>

        <?php echo JHtml::_('uitab.endTabSet'); ?>

        <input type="hidden" name="task" value="">
        <?php echo JHtml::_('form.token'); ?>
    </fieldset>
</form>
