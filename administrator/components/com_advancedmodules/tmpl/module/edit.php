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

use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper as JLayout;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\Field\MiniColorField;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;

RL_Document::style('regularlabs.admin-form');
RL_Document::script('regularlabs.admin-form');
RL_Document::script('regularlabs.admin-form-descriptions');
JHtml::_('behavior.combobox');

$hasContent          = isset($this->item->xml->customContent);
$hasContentFieldName = 'content';

// For a later improvement
if ($hasContent)
{
    $hasContentFieldName = 'content';
}

// Get Params Fieldsets
$this->fieldsets = $this->form->getFieldsets('params');
$this->useCoreUI = true;

RL_Language::load('com_modules', JPATH_ADMINISTRATOR);
RL_Language::load('com_conditions');

JText::script('JYES');
JText::script('JNO');
JText::script('JALL');
JText::script('JTRASHED');

$this->document->addScriptOptions('module-edit', [
    'itemId' => $this->item->id, 'state' => (int) $this->item->id == 0 ? 'Add' : 'Edit',
]);

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->getRegistry()->addExtensionRegistryFile('com_modules');
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useScript('com_modules.admin-module-edit');

// In case of modal
$isModal = RL_Input::getCmd('layout') === 'modal';
$layout  = $isModal ? 'modal' : 'edit';
$tmpl    = $isModal || RL_Input::getCmd('tmpl') === 'component' ? '&tmpl=component' : '';

$is_admin = $this->item->client_id == 1;
?>

<form
        action="<?php echo JRoute::_('index.php?option=com_advancedmodules&layout=' . $layout . $tmpl . '&client_id=' . $this->form->getValue('client_id') . '&id=' . (int) $this->item->id); ?>"
        method="post" name="adminForm" id="module-form"
        aria-label="<?php echo JText::_('COM_MODULES_FORM_TITLE_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>"
        class="form-validate">

    <?php echo JLayout::render('joomla.edit.title_alias', $this); ?>

    <div class="main-card">
        <?php echo JHtml::_('uitab.startTabSet', 'myTab', [
            'active' => 'general', 'recall' => true, 'breakpoint' => 768,
        ]); ?>

        <?php echo JHtml::_('uitab.addTab', 'myTab', 'general', JText::_('COM_MODULES_MODULE')); ?>

        <div class="row">
            <div class="col-lg-9">
                <?php if ($this->item->xml) : ?>
                    <?php if ($this->item->xml->description) : ?>
                        <h2>
                            <?php
                            if ($this->item->xml)
                            {
                                $text = (string) $this->item->xml->name;
                                echo $text ? JText::_($text) : $this->item->module;
                            }
                            else
                            {
                                echo JText::_('COM_MODULES_ERR_XML');
                            }
                            ?>
                        </h2>
                        <div class="info-labels">
                            <span class="badge bg-secondary">
                                <?php echo $is_admin ? JText::_('JADMINISTRATOR') : JText::_('JSITE'); ?>
                            </span>
                        </div>
                        <div>
                            <?php
                            $this->fieldset    = 'description';
                            $short_description = JText::_($this->item->xml->description);
                            $long_description  = JLayout::render('joomla.edit.fieldset', $this);

                            if ( ! $long_description)
                            {
                                $truncated = JHtml::_('string.truncate', $short_description, 550, true, false);

                                if (strlen($truncated) > 500)
                                {
                                    $long_description  = $short_description;
                                    $short_description = JHtml::_('string.truncate', $truncated, 250);

                                    if ($short_description == $long_description)
                                    {
                                        $long_description = '';
                                    }
                                }
                            }
                            ?>
                            <p><?php echo $short_description; ?></p>
                            <?php if ($long_description) : ?>
                                <p class="readmore">
                                    <a href="#" onclick="document.querySelector('#tab-description').click();">
                                        <?php echo JText::_('JGLOBAL_SHOW_FULL_DESCRIPTION'); ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php else : ?>
                    <div class="alert alert-danger">
                        <span class="icon-exclamation-triangle" aria-hidden="true"></span><span
                                class="visually-hidden"><?php echo JText::_('ERROR'); ?></span>
                        <?php echo JText::_('COM_MODULES_ERR_XML'); ?>
                    </div>
                <?php endif; ?>
                <?php
                if ($hasContent)
                {
                    echo $this->form->getInput($hasContentFieldName);
                }

                $this->fieldset = 'basic';
                $html           = JLayout::render('joomla.edit.fieldset', $this);
                echo $html ? '<hr>' . $html : '';
                ?>
            </div>
            <div class="col-lg-3">
                <?php
                // Set main fields.
                $this->fields = [
                    'showtitle',
                    'position',
                    'published',
                    'publish_up',
                    'publish_down',
                    'access',
                    'ordering',
                    'note',
                ];

                if ( ! $is_admin)
                {
                    $this->fields = array_diff($this->fields, [
                        'publish_up',
                        'publish_down',
                        'access',
                    ]);
                }
                ?>
                <?php if ($is_admin) : ?>
                    <?php echo JLayout::render('joomla.edit.admin_modules', $this); ?>
                <?php else : ?>
                    <?php echo JLayout::render('joomla.edit.global', $this); ?>
                    <div class="form-vertical">
                        <?php if ($this->config->use_categories) : ?>
                            <?php echo $this->form->renderFieldset('category'); ?>
                        <?php endif; ?>
                        <?php if ($this->config->use_colors) : ?>
                            <div class="control-group">
                                <div class="control-label">
                                    <label id="jform_extra_color-lbl"
                                           for="jform_extra_color"
                                           role="button"
                                           tabindex="0">
                                        <?php echo JText::_('RL_COLOR'); ?>
                                    </label>
                                    <div id="jform_extra_color-desc" class="">
                                        <small class="form-text">
                                            <?php echo JText::_('AMM_COLOR_DESC'); ?>
                                        </small>
                                    </div>
                                </div>

                                <div class="controls">
                                    <?php
                                    $colorfield = new MiniColorField;

                                    $color = $this->item->extra['color'] ?? '';

                                    $element = new SimpleXMLElement(
                                        '<field
                                                name="jform[extra][color]"
                                                type="MiniColor"
                                                default=""
                                                colors="' . ($this->config->main_colors ?? '') . '"
                                                table="advancedmodules"
                                                item_id="' . $this->item->id . '"
                                                id_column="module_id"
                                                />'
                                    );

                                    $element->value = $color;

                                    $colorfield->setup($element, $color);

                                    echo $colorfield->__get('input');
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($this->config->use_pre_post_html) : ?>
                            <?php echo $this->form->renderFieldset('pre_post_html'); ?>
                        <?php endif; ?>
                        <?php if ($this->config->use_hideempty) : ?>
                            <?php echo $this->form->renderFieldset('hideempty'); ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php echo JHtml::_('uitab.endTab'); ?>

        <?php if (isset($long_description) && $long_description != '') : ?>
            <?php echo JHtml::_('uitab.addTab', 'myTab', 'description', JText::_('JGLOBAL_FIELDSET_DESCRIPTION')); ?>
            <div class="card">
                <div class="card-body">
                    <?php echo $long_description; ?>
                </div>
            </div>
            <?php echo JHtml::_('uitab.endTab'); ?>
        <?php endif; ?>

        <?php if ( ! $is_admin) : ?>
            <?php echo JHtml::_('uitab.addTab', 'myTab', 'assignment', JText::_('RL_CONDITIONS')); ?>
            <?php echo $this->form->renderFieldset('conditions'); ?>
            <?php echo JHtml::_('uitab.endTab'); ?>
        <?php endif; ?>

        <?php echo JHtml::_('uitab.addTab', 'myTab', 'notes', JText::_('AMM_NOTES')); ?>
        <?php echo $this->form->renderFieldset('notes'); ?>
        <?php echo JHtml::_('uitab.endTab'); ?>

        <?php
        $this->fieldsets        = [];
        $this->ignore_fieldsets = [
            'basic',
            'description',
            'category',
            'pre_post_html',
            'hideempty',
            'extra',
            'notes',
            'color',
            'hidden',
            'conditions',
        ];
        RL_Input::set('option', 'com_modules');
        echo JLayout::render('joomla.edit.params', $this);
        ?>

        <?php if ($this->canDo->get('core.admin')) : ?>
            <?php echo JHtml::_('uitab.addTab', 'myTab', 'permissions', JText::_('COM_MODULES_FIELDSET_RULES')); ?>
            <fieldset id="fieldset-permissions" class="options-form">
                <legend><?php echo JText::_('COM_MODULES_FIELDSET_RULES'); ?></legend>
                <div>
                    <?php echo $this->form->renderField('rules'); ?>
                </div>
            </fieldset>
            <?php echo JHtml::_('uitab.endTab'); ?>
        <?php endif; ?>

        <?php echo JHtml::_('uitab.endTabSet'); ?>

        <input type="hidden" name="task" value="">
        <input type="hidden" name="return" value="<?php echo RL_Input::getBase64('return'); ?>">
        <?php echo JHtml::_('form.token'); ?>
        <?php echo $this->form->getInput('module'); ?>
        <?php echo $this->form->getInput('client_id'); ?>
        <?php echo $this->form->getInput('language'); ?>
        <?php if ( ! $is_admin): ?>
            <?php echo $this->form->getInput('access'); ?>
            <?php echo $this->form->renderFieldset('hidden'); ?>
        <?php endif; ?>
    </div>
</form>

<?php if ($this->config->show_switch) : ?>
    <div class="form-control-plaintext text-right">
        <a href="<?php echo JRoute::_('index.php?option=com_modules&force=1&task=module.edit&id=' . (int) $this->item->id); ?>">
            <?php echo JText::_('AMM_SWITCH_TO_CORE'); ?>
        </a>
    </div>
<?php endif; ?>
