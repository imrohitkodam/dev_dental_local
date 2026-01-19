<?php
/**
 * @package         Conditional Content
 * @version         5.5.7
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Parameters as RL_Parameters;

$rule_types = [
    'menu' => [
        'menu__menu_item',
        'menu__home_page',
    ],

    'date'    => [
        'date__date',
    ],


    'visitor' => [
        'visitor__access_level',
        'visitor__user_group',
        'visitor__language',
        'agent__device',
    ],

];

$disabled_rule_types = RL_Array::toArray(RL_Parameters::getComponent('conditions')->disabled_rule_types);

foreach ($rule_types as $group => $types)
{
    foreach ($types as $i => $type)
    {
        if (in_array($type, $disabled_rule_types))
        {
            unset($rule_types[$group][$i]);
        }
    }
}

foreach ($rule_types as $group => $types)
{
    if (empty($types))
    {
        unset($rule_types[$group]);
    }
}
?>

<form action="index.php" id="adminForm" name="conditionalcontentForm" method="post"
      class="rl-form labels-sm">
    <div class="container-fluid container-main">
        <div class="row">
            <div class="fixed-top d-lg-none">
                <button type="button" class="btn btn-success mb-4 w-100"
                        onclick="RegularLabs.ConditionalContentPopup.insertText();window.parent.Joomla.Modal.getCurrent().close();">
                    <span class="icon-file-import" aria-hidden="true"></span>
                    <?php echo JText::_('RL_INSERT'); ?>
                </button>
            </div>

            <div class="pt-5 d-lg-none"></div>

            <div class="col-lg-6 border-end">
                <input type="hidden" name="type" id="type" value="url">
                <?php echo JHtml::_('uitab.startTabSet', 'main', ['active' => 'tab-content']); ?>

                <?php echo JHtml::_('uitab.addTab', 'main', 'tab-content', JText::_('COC_CONTENT')); ?>
                <div class="form-vertical">
                    <?php echo $this->form->renderFieldset($this->params->use_editors ? 'content' : 'content_no_editor'); ?>
                </div>
                <?php echo JHtml::_('uitab.endTab'); ?>

                <?php echo JHtml::_('uitab.addTab', 'main', 'tab-alternative', JText::_('COC_ALTERNATIVE_CONTENT')); ?>
                <div class="form-vertical">
                    <?php echo $this->form->renderFieldset($this->params->use_editors ? 'alternative' : 'alternative_no_editor'); ?>
                </div>
                <?php echo JHtml::_('uitab.endTab'); ?>

                <?php echo JHtml::_('uitab.addTab', 'main', 'tab-conditions', JText::_('COC_CONDITIONS')); ?>
                <?php echo $this->form->renderFieldset('conditions'); ?>

                <div id="inline_rules">
                    <?php

                    echo $this->form->renderFieldset('inline__a');

                    foreach ($rule_types as $group => $types)
                    {
                        echo $this->form->renderFieldset('group__' . $group . '__a');

                        foreach ($types as $type)
                        {
                            echo $this->form->renderFieldset($type);
                        }

                        echo $this->form->renderFieldset('group__' . $group . '__b');
                    }

                    echo $this->form->renderFieldset('inline__b');
                    ?>
                </div>
                <?php echo JHtml::_('uitab.endTab'); ?>

                <?php echo JHtml::_('uitab.endTabSet'); ?>
            </div>
            <div class="col-lg-6">
                <div class="position-sticky" style="top:1.25rem;">
                    <button type="button" class="btn btn-success mb-4 w-100 hidden d-lg-block"
                            onclick="RegularLabs.ConditionalContentPopup.insertText();window.parent.Joomla.Modal.getCurrent().close();">
                        <span class="icon-file-import" aria-hidden="true"></span>
                        <?php echo JText::_('RL_INSERT'); ?>
                    </button>
                    <fieldset class="options-form mt-2 position-relative">
                        <legend class="mb-1"><?php echo JText::_('JGLOBAL_PREVIEW'); ?></legend>
                        <span id="preview_spinner" class="rl-spinner hidden"></span>
                        <div id="preview_code" class="hidden"></div>
                    </fieldset>
                    <?php echo $this->form->renderFieldset('messages'); ?>
                </div>
            </div>
        </div>
    </div>
</form>
