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

namespace RegularLabs\Component\Conditions\Administrator\Form\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use RegularLabs\Component\Conditions\Administrator\Helper\Helper;
use RegularLabs\Component\Conditions\Administrator\Helper\Summary;
use RegularLabs\Component\Conditions\Administrator\Model\ItemModel;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\ShowOn as RL_ShowOn;
use RegularLabs\Library\StringHelper as RL_String;
use RegularLabs\Library\User as RL_User;

class ConditionField extends RL_FormField
{
    private $canCreate;
    private $canEdit;
    private $enabled_types;
    private $extension;
    private $item_id;
    private $name_column;
    private $table;
    private $text_no_conditions;

    public function __construct($form = null)
    {
        parent::__construct($form);

        $this->canCreate = RL_User::authorise('core.create', 'com_conditions');
        $this->canEdit   = RL_User::authorise('core.edit', 'com_conditions');
    }

    public function getCondition(): ?object
    {
        return (new ItemModel)->getConditionByExtensionItem(
            $this->extension,
            $this->item_id,
            false,
            $this->enabled_types
        );
    }

    protected function getButtonUrl($view, $task = 'edit', $layout = 'modal')
    {
        return 'index.php?option=com_conditions'
            . '&view=' . $view
            . ($task ? '&task=' . $view . '.' . $task : '')
            . '&extension=' . $this->extension
            . '&item_id=' . $this->item_id
            . '&table=' . $this->table
            . '&name_column=' . $this->name_column
            . '&enabled_types=' . $this->enabled_types
            . '&message=' . $this->text_no_conditions
            . '&layout=' . $layout
            . '&tmpl=component';
    }

    protected function getInput()
    {
        $this->extension          = $this->get('extension', $this->app->input->getCmd('option', ''));
        $this->item_id            = $this->get('item_id', $this->app->input->getInt('id'));
        $this->table              = $this->get('table', $this->app->input->getCmd('table'));
        $this->name_column        = $this->get('name_column', $this->app->input->getCmd('name_column'));
        $this->text_no_conditions = $this->get('text_no_conditions', $this->app->input->get('message', ''));
        $this->enabled_types      = $this->get('enable', '');
        $this->enabled_types      = str_replace(' ', '', $this->enabled_types);

        // Fix incorrect table value
        if ($this->table == 'array' || $this->table == 'Array')
        {
            $this->table = 'modules';
        }

        if ( ! $this->extension)
        {
            return '';
        }

        if ( ! $this->item_id)
        {
            return $this->getHtmlForNoItemId();
        }

        RL_Document::script('regularlabs.regular');
        RL_Document::script('conditions.script');
        RL_Language::load($this->extension);

        $condition = $this->getCondition();

        if ($condition && $condition->published !== 1)
        {
            $condition = null;
        }

        $has_conditions = ! empty($condition);

        $html   = [];
        $html[] = '<input type="hidden" name="' . $this->getName('has_conditions') . '" id="' . $this->getId('', 'has_conditions') . '" value="' . (int) $has_conditions . '">';
        $html[] = '<input type="hidden" name="' . $this->getName('condition_id') . '" id="' . $this->getId('', 'condition_id') . '" value="' . ($condition->id ?? '') . '">';
        $html[] = '<input type="hidden" name="' . $this->getName('condition_alias') . '" id="' . $this->getId('', 'condition_alias') . '" value="' . RL_String::escape($condition->alias ?? '') . '">';
        $html[] = '<input type="hidden" name="' . $this->getName('condition_name') . '" id="' . $this->getId('', 'condition_name') . '" value="' . RL_String::escape($condition->name ?? '') . '">';
        $html[] = '<div id="rules_summary" class="position-relative">';
        $html[] = '<div id="rules_summary_message" class="alert alert-warning' . ($has_conditions ? ' hidden' : '') . '">'
            . '<span class="icon-info-circle text-info" aria-hidden="true"></span> '
            . JText::_('CON_MESSAGE_NO_CONDITION_SELECTED')
            . ($this->text_no_conditions ? '<br><br>' . JText::_($this->text_no_conditions) : '')
            . '</div>';
        $html[] = $this->getButtons();
        $html[] = '<div id="rules_summary_content" class="mt-4">';
        $html[] = Summary::render($condition, $this->extension, $this->text_no_conditions, $this->enable);
        $html[] = '</div >';
        $html[] = '</div >';

        return implode('', $html);
    }

    protected function getLabel()
    {
        return '';
    }

    private function addSaveButtons(array &$options): void
    {
        $modal     = 'this.closest(\'.modal-content\')';
        $iframe    = $modal . '.querySelector(\'.modal-body > iframe\')';
        $hide_self = 'this.classList.add(\'hidden\');';
        $apply     = $hide_self . $modal . ' && ' . $iframe . ' && ' . $iframe . '.contentWindow.Joomla.submitbutton(\'item.apply\')';
        $save      = $hide_self . $modal . ' && ' . $iframe . ' && ' . $iframe . '.contentWindow.Joomla.submitbutton(\'item.save\')';

        $options = [
            ...$options,
            'keyboard'         => false,
            'backdrop'         => 'static',
            'confirmText'      => JText::_('JAPPLY'),
            'confirmCallback'  => $apply,
            'confirmClass'     => 'btn btn-success hidden conditions-button',
            'confirmIcon'      => 'save',
            'confirm2Text'     => JText::_('JSAVE'),
            'confirm2Callback' => $save,
            'confirm2Class'    => 'btn btn-success hidden conditions-button',
            'confirm2Icon'     => 'save',
        ];
    }

    private function getButton(
        string $name,
        string $text,
        string $link,
        string $icon,
        string $class = 'primary',
        array  $options = []
    ): object
    {
        $button          = (object) [];
        $button->name    = $this->id . '_' . $name;
        $button->text    = JText::_($text);
        $button->icon    = $icon;
        $button->class   = 'btn-' . $class . ' mb-1';
        $button->options = $options;

        if ($link)
        {
            $button->link = $link;
        }

        return $button;
    }

    private function getButtonCreate(): string
    {
        if ( ! $this->canCreate)
        {
            return '';
        }

        $options = [];
        $this->addSaveButtons($options);

        return $this->renderButtonAndModal(
            'create',
            'CON_BUTTON_CREATE',
            $this->getButtonUrl('item', 'modaledit', 'modal_edit'),
            'file-add',
            'success',
            $options
        );
    }

    private function getButtonDelete(): string
    {
        return $this->renderButtonAndModal(
            'delete',
            'CON_BUTTON_REMOVE',
            $this->getButtonUrl('item', 'remove_mapping'),
            'times',
            'danger',
            [
                'height'     => '400px',
                'width'      => '400px',
                'bodyHeight' => '400px',
                'modalWidth' => '400px',
            ]
        );
    }

    private function getButtonEdit(): string
    {
        if ( ! $this->canEdit)
        {
            return '';
        }

        $options = [];
        $this->addSaveButtons($options);

        return $this->renderButtonAndModal(
            'edit',
            'CON_BUTTON_EDIT',
            $this->getButtonUrl('item', 'modaledit'),
            'edit',
            'warning',
            $options
        );
    }

    private function getButtonSelect(): string
    {
        if ( ! Helper::thereAreConditions())
        {
            return '';
        }

        return $this->renderButton(
            'select',
            'CON_BUTTON_SELECT',
            $this->getButtonUrl('items', ''),
            'hand-pointer'
        );
    }

    private function getButtons(): string
    {
        $html = [
            RL_ShowOn::open('has_conditions:1', $this->formControl),
            $this->getButtonSelect(),
            $this->getButtonEdit(),
            $this->getButtonDelete(),
            RL_ShowOn::close(),

            RL_ShowOn::open('has_conditions:0', $this->formControl),
            $this->getButtonSelect(),
            $this->getButtonCreate(),
            RL_ShowOn::close(),

            $this->getModalSelect(),
        ];

        return implode('', $html);
    }

    private function getHtmlForNoItemId(): string
    {
        $item_name = Helper::getExtensionItemString($this->extension);

        return '<div class="alert alert-warning">'
            . '<span class="icon-warning"></span> '
            . JText::_('CON_MESSAGE_SAVE_ITEM_BEFORE_CREATING')
            . '<br><br>'
            . JText::sprintf('CON_MESSAGE_SAVE_AS_UNPUBLISHED', $item_name)
            . '<br><br>'
            . $this->getSaveButton(true)
            . $this->getSaveButton(false)
            . '</div>';
    }

    private function getModalButton(
        string $name,
        string $text,
        string $link,
        string $icon,
        string $class = 'primary',
        array  $options = []
    ): object
    {
        $button = $this->getButton($name, $text, $link, $icon, $class, $options);

        $button->modal   = true;
        $button->options = [
            'height'     => '400px',
            'width'      => '800px',
            'bodyHeight' => '70',
            'modalWidth' => '80',
            ...$button->options,
        ];

        return $button;
    }

    private function getModalSelect(): string
    {
        return $this->renderModal(
            'select',
            'CON_BUTTON_SELECT',
            $this->getButtonUrl('items', ''),
            'hand-pointer'
        );
    }

    private function getSaveButton(bool $published = true): string
    {
        $button = $this->getButton(
            $published ? 'save-published' : 'save-unpublished',
            $published ? 'CON_SAVE_AS_PUBLISHED' : 'CON_SAVE_AS_UNPUBLISHED',
            '',
            'save',
            $published ? 'warning' : 'success'
        );

        $button->onclick = '$("select#jform_state").val(' . ($published ? 1 : 0) . ');'
            . '$("select#jform_published").val(' . ($published ? 1 : 0) . ');'
            . '$("joomla-toolbar-button#toolbar-apply > button").click();';

        return $this->getRenderer('regularlabs.buttons.button')
            ->addIncludePath(JPATH_SITE . '/libraries/regularlabs/layouts')
            ->render($button);
    }

    private function renderButton(
        string $name,
        string $text,
        string $link,
        string $icon,
        string $class = 'primary',
        array  $options = []
    ): string
    {
        $button = $this->getModalButton($name, $text, $link, $icon, $class, $options);

        return $this->getRenderer('regularlabs.buttons.button')
            ->addIncludePath(JPATH_SITE . '/libraries/regularlabs/layouts')
            ->render($button);
    }

    private function renderButtonAndModal(
        string $name,
        string $text,
        string $link,
        string $icon,
        string $class = 'primary',
        array  $options = []
    ): string
    {
        return $this->renderButton($name, $text, $link, $icon, $class, $options)
            . $this->renderModal($name, $text, $link, $icon, $class, $options);
    }

    private function renderModal(
        string $name,
        string $text,
        string $link,
        string $icon,
        string $class = 'primary',
        array  $options = []
    ): string
    {
        $button = $this->getModalButton($name, $text, $link, $icon, $class, $options);

        return $this->getRenderer('regularlabs.buttons.modal')
            ->addIncludePath(JPATH_SITE . '/libraries/regularlabs/layouts')
            ->render($button);
    }
}
