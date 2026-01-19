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

namespace RegularLabs\Plugin\EditorButton\ConditionalContent\Form\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Object\CMSObject as JCMSObject;
use RegularLabs\Component\Conditions\Administrator\Helper\Helper;
use RegularLabs\Component\Conditions\Administrator\Model\ItemModel;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Form\FormField as RL_FormField;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\ShowOn as RL_ShowOn;

class ConditionField extends RL_FormField
{
    private $enabled_types;
    private $canCreate;
    private $canEdit;
    private $user;

    public function __construct($form = null)
    {
        parent::__construct($form);

        RL_Language::load('com_conditions', JPATH_ADMINISTRATOR);

        $this->user      = JFactory::getApplication()->getIdentity() ?: JFactory::getUser();
        $this->canCreate = $this->user->authorise('core.create', 'com_conditions');
        $this->canEdit   = $this->user->authorise('core.edit', 'com_conditions');
    }

    public function getCondition()
    {
        return (new ItemModel)->getConditionByExtensionItem(
            $this->extension,
            $this->item_id,
            false,
            $this->enabled_types
        );
    }

    protected function getButtonUrl($view, $task = '', $layout = 'modal')
    {
        return 'index.php?option=com_conditions'
            . '&view=' . $view
            . ($task ? '&task=' . $view . '.' . $task : '')
            . '&extension=conditionalcontent'
            . '&enabled_types=' . $this->enabled_types
            . '&layout=' . $layout
            . '&tmpl=component';
    }

    protected function getInput()
    {
        $this->enabled_types = $this->get('enable', '');
        $this->enabled_types = str_replace(' ', '', $this->enabled_types);

        RL_Document::script('regularlabs.regular');
        RL_Document::script('conditions.script');

        $html   = [];
        $html[] = '<input type="hidden" name="' . $this->getName('has_conditions') . '" id="' . $this->getId('', 'has_conditions') . '" value="0">';
        $html[] = '<input type="hidden" name="' . $this->getName('condition_id') . '" id="' . $this->getId('', 'condition_id') . '" value="">';
        $html[] = '<input type="hidden" name="' . $this->getName('condition_alias') . '" id="' . $this->getId('', 'condition_alias') . '" value="">';
        $html[] = '<input type="hidden" name="' . $this->getName('condition_name') . '" id="' . $this->getId('', 'condition_name') . '" value="">';
        $html[] = '<input type="hidden" name="' . $this->getName('use_inline') . '" id="' . $this->getId('', 'use_inline') . '" value="1">';
        $html[] = '<div id="rules_summary" class="position-relative">';
        $html[] = '<div id="rules_summary_message" class="alert alert-warning hidden">'
            . '</div>';
        $html[] = $this->getButtons();
        $html[] = '<div id="rules_summary_content" class="mt-4">';
        $html[] = '</div >';
        $html[] = '</div >';

        return implode('', $html);
    }

    protected function getLabel()
    {
        return '';
    }

    private function addSaveButtons(&$options)
    {
        $modal     = 'this.closest(\'.modal-content\')';
        $iframe    = $modal . '.querySelector(\'.modal-body > iframe\')';
        $hide_self = 'this.classList.add(\'hidden\');';
        $save      = $hide_self . $modal . ' && ' . $iframe . ' && ' . $iframe . '.contentWindow.Joomla.submitbutton(\'item.save\')';

        $options = [
            ...$options,
            'keyboard'         => false,
            'backdrop'         => 'static',
            'confirm2Text'     => JText::_('JSAVE'),
            'confirm2Callback' => $save,
            'confirm2Class'    => 'btn btn-success hidden conditions-button',
            'confirm2Icon'     => 'save',
        ];
    }

    private function getButton($name, $text, $link, $icon, $class = 'primary', $options = [])
    {
        $button          = new JCMSObject;
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

    private function getButtonCreate()
    {
        if ( ! $this->canCreate)
        {
            return '';
        }

        if (RL_Document::isClient('site'))
        {
            return '<div class="fst-italic">'
                . JText::_('CON_CREATE_IN_ADMIN')
                . '</div>';
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

    private function getButtonInline()
    {
        $button          = $this->getButton('inline',
            'COC_BUTTON_INLINE',
            '',
            'code',
            'warning'
        );
        $button->onclick = 'RegularLabs.ConditionalContentPopup.setInline();';

        return $this->getRenderer('regularlabs.buttons.button')
            ->addIncludePath(JPATH_SITE . '/libraries/regularlabs/layouts')
            ->render($button);
    }

    private function getButtonSelect()
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

    private function getButtons()
    {
        $html = [
            $this->getButtonSelect(),
            $this->getButtonCreate(),
            RL_ShowOn::open('use_inline:0[OR]has_conditions:1')
            . $this->getButtonInline()
            . RL_ShowOn::close(),

            $this->getModalSelect(),
        ];

        return implode('<br>', $html);
    }

    private function getModalButton($name, $text, $link, $icon, $class = 'primary', $options = [])
    {
        $button = $this->getButton($name, $text, $link, $icon, $class, $options);

        $button->modal   = true;
        $button->options = [
            'height'     => '400px',
            'width'      => '800px',
            'bodyHeight' => '70',
            'modalWidth' => '80',
            ... $button->options,
        ];

        return $button;
    }

    private function getModalSelect()
    {
        return $this->renderModal(
            'select',
            'CON_BUTTON_SELECT',
            $this->getButtonUrl('items', ''),
            'hand-pointer'
        );
    }

    private function renderButton($name, $text, $link, $icon, $class = 'primary', $options = [])
    {
        $button = $this->getModalButton($name, $text, $link, $icon, $class, $options);

        return $this->getRenderer('regularlabs.buttons.button')
            ->addIncludePath(JPATH_SITE . '/libraries/regularlabs/layouts')
            ->render($button);
    }

    private function renderButtonAndModal(
        $name,
        $text,
        $link,
        $icon,
        $class = 'primary',
        $options = []
    )
    {
        return $this->renderButton($name, $text, $link, $icon, $class, $options)
            . $this->renderModal($name, $text, $link, $icon, $class, $options);
    }

    private function renderModal($name, $text, $link, $icon, $class = 'primary', $options = [])
    {
        $button = $this->getModalButton($name, $text, $link, $icon, $class, $options);

        return $this->getRenderer('regularlabs.buttons.modal')
            ->addIncludePath(JPATH_SITE . '/libraries/regularlabs/layouts')
            ->render($button);
    }
}
