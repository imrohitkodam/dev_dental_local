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

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\Field\MiniColorField;
use RegularLabs\Library\Language as RL_Language;

RL_Document::useScript('keepalive');
RL_Document::useScript('form.validate');
RL_Document::script('regularlabs.admin-form');
RL_Document::script('regularlabs.admin-form-descriptions');

RL_Language::load('com_content', JPATH_ADMINISTRATOR);
?>

<form action="<?php echo Route::_('index.php?option=com_snippets&id=' . (int) $this->item->id); ?>" method="post"
      name="adminForm" id="item-form"
      aria-label="<?php echo Text::_('COM_SNIPPETS_FORM_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>"
      class="form-validate">
    <?php echo LayoutHelper::render('joomla.edit.title_alias', $this); ?>

    <fieldset class="mt-3">
        <?php echo HTMLHelper::_('uitab.startTabSet', 'main', ['active' => 'details']); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'main', 'details', Text::_('JDETAILS')); ?>
        <div class="row form-vertical">
            <div class="col-lg-9">
                <?php echo $this->form->renderFieldset('-content'); ?>
            </div>
            <div class="col-lg-3">
                <?php echo $this->form->renderFieldset('details'); ?>
                <?php if ($this->config->use_categories) : ?>
                    <?php echo $this->form->renderFieldset('category'); ?>
                <?php endif; ?>
                <?php if ($this->config->use_colors) : ?>
                    <div class="control-group">
                        <div class="control-label">
                            <label id="jform_extra_color-lbl" for="jform_extra_color" role="button" tabindex="0">
                                <?php echo JText::_('RL_COLOR'); ?>
                            </label>
                        </div>

                        <div class="controls">
                            <?php
                            $colorfield = new MiniColorField;

                            $color = $this->item->color ?? '';

                            $element = new SimpleXMLElement(
                                '<field
                                                name="jform[color]"
                                                type="MiniColor"
                                                default=""
                                                colors="' . ($this->config->main_colors ?? '') . '"
                                                table="snippets"
                                                item_id="' . $this->item->id . '"
                                                />'
                            );

                            $element->value = $color;

                            $colorfield->setup($element, $color);

                            echo $colorfield->__get('input');
                            ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'main', 'variables', Text::_('SNP_VARIABLES')); ?>
        <div class="row form-vertical">
            <?php echo $this->form->renderFieldset('variables'); ?>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.addTab', 'main', 'settings', Text::_('JOPTIONS')); ?>
        <div class="row form-vertical">
            <?php echo $this->form->renderFieldset('settings'); ?>
        </div>
        <?php echo HTMLHelper::_('uitab.endTab'); ?>

        <?php echo HTMLHelper::_('uitab.endTabSet'); ?>

        <input type="hidden" name="task" value="">
        <?php echo HTMLHelper::_('form.token'); ?>
    </fieldset>
</form>
