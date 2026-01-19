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

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use RegularLabs\Component\AdvancedModules\Administrator\Helper\ModulesHelper;
use RegularLabs\Library\Form\Field\MiniColorField;
use RegularLabs\Library\Form\Field\SimpleCategoryField;

$clientId = $this->state->get('client_id');

// Show only Module Positions of published Templates
$published              = 1;
$positions              = HTMLHelper::_('advancedmodules.positions', $clientId, $published);
$positions['']['items'] = [
    ModulesHelper::createOption('nochange', '- ' . Text::_('COM_MODULES_BATCH_POSITION_NOCHANGE') . ' -'),
    ModulesHelper::createOption('noposition', Text::_('COM_MODULES_BATCH_POSITION_NOPOSITION')),
];

// Build field
$attr = [
    'id'          => 'batch-position-id',
    'list.select' => 'nochange',
];

Text::script('JGLOBAL_SELECT_NO_RESULTS_MATCH');
Text::script('JGLOBAL_SELECT_PRESS_TO_SELECT');

$this->document->getWebAssetManager()
    ->usePreset('choicesjs')
    ->useScript('webcomponent.field-fancy-select')
    ->useScript('joomla.batch-copymove');

?>

<div class="p-3">
    <p><?php echo Text::_('COM_MODULES_BATCH_TIP'); ?></p>

    <?php if ($published >= 0) : ?>
        <div class="form-group">
            <div class="controls">
                <label id="batch-choose-action-lbl" for="batch-choose-action">
                    <?php echo Text::_('COM_MODULES_BATCH_POSITION_LABEL'); ?>
                </label>
                <div id="batch-choose-action">
                    <joomla-field-fancy-select allow-custom
                                               search-placeholder="<?php echo $this->escape(Text::_('COM_MODULES_TYPE_OR_SELECT_POSITION')); ?>">
                        <?php echo HTMLHelper::_('select.groupedlist', $positions, 'batch[position_id]', $attr); ?>
                    </joomla-field-fancy-select>
                    <div id="batch-copy-move" class="control-group radio">
                        <?php echo HTMLHelper::_('advancedmodules.batchOptions'); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div class="controls">
            <label id="batch-choose-action-lbl" for="batch-choose-action">
                <?php echo Text::_('RL_SET_CATEGORY'); ?>
            </label>
            <div>
                <?php
                $colorfield = new SimpleCategoryField;

                $element = new SimpleXMLElement(
                    '<field
                         id="batch-category"
                         name="batch[category]"
                         type="SimpleCategory"
                         default=""
                         table="advancedmodules"
                         show_keep_original="1"
                         none_value="---"
                         />'
                );

                $colorfield->setup($element, '-1');

                echo $colorfield->__get('input');
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="controls">
            <label id="batch-choose-action-lbl" for="batch-choose-action">
                <?php echo Text::_('RL_SET_COLOR'); ?>
            </label>
            <div>
                <?php
                $colorfield = new MiniColorField;

                $element = new SimpleXMLElement(
                    '<field
                         id="batch-color"
                         name="batch[color]"
                         type="MiniColor"
                         default=""
                         colors="' . ($this->config->main_colors ?? '') . '"
                         />'
                );

                $colorfield->setup($element, '');

                echo $colorfield->__get('input');
                ?>
            </div>
        </div>
    </div>

    <?php if ($clientId != 1) : ?>
        <div class="form-group">
            <div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.language', []); ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($clientId == 1 && ModuleHelper::isAdminMultilang()) : ?>
        <div class="form-group">
            <div class="controls">
                <?php echo LayoutHelper::render('joomla.html.batch.adminlanguage', []); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <div class="controls">
            <?php echo LayoutHelper::render('joomla.html.batch.access', []); ?>
        </div>
    </div>
</div>
