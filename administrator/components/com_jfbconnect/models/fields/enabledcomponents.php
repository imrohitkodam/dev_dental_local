<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldEnabledComponents extends JFormFieldList
{
    public $type = 'EnabledComponents';

    protected function getInput()
    {
        $skipComponents = JFBCFactory::config()->getSetting('social_graph_skip_components', array());
        //$blocked = unserialize($skipComponents); //we do the unserialize in the config model
        $this->value = $skipComponents;
        return parent::getInput();
    }

    protected function getOptions()
    {
        $options = array();
        $options[] = JHtml::_('select.option', "--", JText::_('COM_JFBCONNECT_OPENGRAPH_SETTINGS_SKIP_COMPONENTS_NONE'));

        $components = $this->getComponents();
        $options = array_merge($options, $components);
        return $options;
    }

    //Com_users/helpers/debug.php
    protected function getComponents()
    {
        // Initialise variable.
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select('name AS text, element AS value')
            ->from('#__extensions')
            ->where('enabled >= 1')
            ->where('type =' . $db->quote('component'));

        $items = $db->setQuery($query)->loadObjectList();

        if (count($items))
        {
            $lang = JFactory::getLanguage();

            foreach ($items as &$item)
            {
                // Load language
                $extension = $item->value;
                $source = JPATH_ADMINISTRATOR . '/components/' . $extension;
                $lang->load("$extension.sys", JPATH_ADMINISTRATOR, null, false, true)
                || $lang->load("$extension.sys", $source, null, false, true);

                // Translate component name
                $item->text = strip_tags(JText::_($item->text)) . ' ('.$item->value.')';
            }

            // Sort by component name
            JArrayHelper::sortObjects($items, 'text', 1, true, true);
        }

        return $items;
    }
}
