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

class JFormFieldChannelAutopostAccesstype extends JFormFieldList
{
    public $type = 'ChannelAutopostObjects';

    protected function getOptions()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true)
            ->select($db->quoteName('a.id', 'value') . ', ' . $db->quoteName('a.title', 'text'))
            ->from($db->quoteName('#__viewlevels', 'a'))
            ->where($db->quoteName('id') . '<>' . $db->quote(" 6")) //Super Users
            ->where($db->quoteName('id') . '<>' . $db->quote(" 3")) //Special
            ->group($db->quoteName(array('a.id', 'a.title', 'a.ordering')))
            ->order($db->quoteName('a.ordering') . ' ASC')
            ->order($db->quoteName('title') . ' ASC');

        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();

        return $options;
    }
}