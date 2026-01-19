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

use InvalidArgumentException;
use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\ArrayHelper as RL_Array;
use RegularLabs\Library\Extension as RL_Extension;
use RegularLabs\Library\Form\Field\SubformField as RL_SubformField;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;
use RuntimeException;

class ConditionRulesField extends RL_SubformField
{
    /**
     * @var    string
     */
    protected $layout = 'regularlabs.form.field.subform.repeatable';

    /**
     * Loads the form instance for the subform by given name and control.
     *
     * @param string $name    The name of the form.
     * @param string $control The control name of the form.
     *
     * @return  JForm  The form instance.
     *
     * @throws  InvalidArgumentException if no form provided.
     * @throws  RuntimeException if the form could not be loaded.
     */
    public function loadSubFormByName($name, $control)
    {
        $tmpl = JForm::getInstance($name, $this->formsource, ['control' => $control]);

        $disabled_types = $this->getDisabledTypes();
        $enabled_types  = $this->getEnabledTypes();

        // If enabled_types is empty, there is no specific limitation
        if (empty($disabled_types) && empty($enabled_types))
        {
            return $tmpl;
        }

        $remove        = false;
        $removals      = [];
        $previous_type = '';

        $fields = $tmpl->getXml()->fieldset->field;

        foreach ($fields as $field)
        {
            $name = (string) $field->attributes()->name;

            if ($this->isDisabledStartShowonField($field, $disabled_types, $enabled_types))
            {
                $parts = explode('__', $name);

                $type = $parts[1] . '__' . $parts[2];

                $disabled_types[] = $type;
                $previous_type    = $type;

                $remove = true;
            }

            if ($remove)
            {
                $removals[] = $name;
            }

            if ($previous_type && $name === '@showon__' . $previous_type . '__b')
            {
                $remove = false;
            }
        }

        foreach ($removals as $removal)
        {
            $tmpl->removeField($removal);
        }

        $type_field = $tmpl->getField('type');

        [
            $removals, $convert_to_free,
        ] = $this->getRemovals($type_field->element->group, $disabled_types, $enabled_types);

        $this->removeDisabledOptions($removals);
        $this->disableProOptionsForFree($convert_to_free);
        $this->removeEmptyGroups($type_field->element->group);

        return $tmpl;
    }

    private function disableProOptionsForFree(array $options): void
    {
        foreach ($options as $option)
        {
            if ( ! isset($option->attributes()->disabled))
            {
                $option->addAttribute('disabled', 'disabled');
            }

            $option[0] = JText::_($option[0]) . ' (' . strip_tags(JText::_('RL_ONLY_AVAILABLE_IN_PRO')) . ')';
        }
    }

    private function getDisabledTypes(): array
    {
        $disabled_types = RL_Array::toArray(RL_Parameters::getComponent('conditions')->disabled_rule_types ?: []);

        $extensions = ['hikashop', 'flexicontent', 'k2', 'zoo'];

        foreach ($extensions as $extension)
        {
            if ( ! RL_Extension::isEnabled($extension))
            {
                $disabled_types[] = $extension;
            }
        }

        return $disabled_types;
    }

    private function getEnabledTypes(): array
    {
        return RL_Array::toArray(RL_Input::getString('enabled_types'));
    }

    private function getRemovals(object $groups, array $disabled_types, array $enabled_types): array
    {
        $removals        = [];
        $convert_to_free = [];

        foreach ($groups as $group)
        {
            foreach ($group->option as $option)
            {
                $type = (string) $option->attributes()->value;

                if (in_array($type, $disabled_types))
                {
                    $removals[] = $option;
                    continue;
                }

                if ( ! empty($enabled_types)
                    && ! in_array($type, $enabled_types)
                )
                {
                    $convert_to_free[] = $option;
                }
            }
        }

        return [$removals, $convert_to_free];
    }

    private function isDisabledStartShowonField(
        object $field,
        array  $disabled_types,
        array  $enabled_types
    ): bool
    {
        $name      = (string) $field->attributes()->name;
        $is_showon = str_starts_with($name, '@showon');

        if ( ! $is_showon)
        {
            return false;
        }

        $parts = explode('__', $name);

        if (count($parts) !== 4 || $parts[3] !== 'a')
        {
            return false;
        }

        $group = $parts[1];
        $type  = $group . '__' . $parts[2];

        if (
            in_array($group, $disabled_types)
            || in_array($type, $disabled_types)
        )
        {
            return true;
        }

        if ( ! empty($enabled_types)
            && ! in_array($group, $enabled_types)
            && ! in_array($type, $enabled_types)
        )
        {
            return true;
        }

        return false;
    }

    private function removeDisabledOptions(array $options): void
    {
        foreach ($options as $option)
        {
            $this->removeXmlElement($option);
        }
    }

    private function removeEmptyGroups(object $groups): void
    {
        foreach ($groups as $group)
        {
            if (empty($group->option))
            {
                $this->removeXmlElement($group);
            }
        }
    }

    private function removeXmlElement(object $element): void
    {
        $dom = dom_import_simplexml($element);
        $dom->parentNode->removeChild($dom);
    }
}
