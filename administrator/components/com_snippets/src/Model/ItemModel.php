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

namespace RegularLabs\Component\Snippets\Administrator\Model;

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\MVC\Model\AdminModel as JAdminModel;
use Joomla\CMS\Object\CMSObject as JCMSObject;
use Joomla\Utilities\ArrayHelper as JArray;
use RegularLabs\Library\Alias as RL_Alias;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\StringHelper as RL_String;

defined('_JEXEC') or die;

class ItemModel extends JAdminModel
{
    /**
     * @var        string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';

    /**
     * @param int $id
     *
     * @return  boolean  True on success.
     */
    public function duplicate($id)
    {
        $item = $this->getItem($id);

        //        unset($item->_errors);
        unset($item->typeAlias);

        $item->id        = 0;
        $item->published = 0;
        $this->incrementName($item->name, $item->alias, $item->id);

        $item = $this->validate(null, (array) $item);

        return parent::save($item);
    }

    /**
     * @param array   $data     Data for the form.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A Form object on success, false on failure
     */
    public function getForm($data = [], $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm('com_snippets.item', 'item', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form))
        {
            return false;
        }

        // Modify the form based on access controls.
        if ($this->canEditState((object) $data) != true)
        {
            // Disable fields for display.
            $form->setFieldAttribute('published', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is a record you can edit.
            $form->setFieldAttribute('published', 'filter', 'unset');
        }

        return $form;
    }

    /**
     * @return  mixed    Object on success, false on failure.
     */
    public function getItem($pk = null)
    {
        // Initialise variables.
        $pk    = ( ! empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
        $table = $this->getTable();

        if ($pk > 0)
        {
            // Attempt to load the row.
            $return = $table->load($pk);

            // Check for a table object error.
            if ($return === false && $table->getError())
            {
                $this->setError($table->getError());

                return false;
            }
        }

        $properties = $table->getProperties(1);
        $item       = JArray::toObject($properties, JCMSObject::class);

        $params = RL_Parameters::getObjectFromData(
            $item->params,
            JPATH_ADMINISTRATOR . '/components/com_snippets/forms/item.xml'
        );

        foreach ($params as $key => $val)
        {
            if (isset($item->{$key}))
            {
                continue;
            }

            $item->{$key} = $val;
        }

        unset($item->params);

        return $item;
    }

    /**
     * @param array $data The form data.
     *
     * @return  boolean  True on success.
     */
    public function save($data)
    {
        $task = RL_Input::getCmd('task');

        if ($task == 'save2copy')
        {
            $data['published'] = 0;
        }

        $this->incrementName($data['name'], $data['alias'], (int) $data['id']);

        return parent::save($data);
    }

    /**
     * Method to validate form data.
     */
    public function validate($form, $data, $group = null)
    {
        // Check for valid name
        if (empty($data['name']))
        {
            $this->setError(JText::_('SNP_THE_ITEM_MUST_HAVE_A_NAME'));

            return false;
        }

        $params = [];

        $db = $this->getDatabase();
        $db->setQuery('SHOW COLUMNS FROM #__snippets');
        $dbkeys = $db->loadColumn();

        $newdata = [];

        foreach ($data as $key => $val)
        {
            if (in_array($key, $dbkeys))
            {
                $newdata[$key] = $val;
                continue;
            }

            $params[$key] = $val;
        }

        $newdata['params'] = json_encode($params);

        return $newdata;
    }

    /**
     * @param object $record A record object.
     *
     * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
     */
    protected function canDelete($record)
    {
        if ($record->published != -2)
        {
            return false;
        }

        return parent::canDelete($record);
    }

    /**
     * @return  mixed  The data for the form.
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState('com_snippets.edit.item.data', []);

        if (empty($data))
        {
            $data = $this->getItem();
        }

        $this->preprocessData('com_snippets.item', $data);

        return $data;
    }

    private function aliasExists(string $alias, int $id = 0)
    {
        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select('id')
            ->from('#__snippets')
            ->where($db->quoteName('alias') . ' = ' . $db->quote($alias))
            ->where($db->quoteName('published') . ' != -2')
            ->setLimit(1);

        if ($id)
        {
            $query->where($db->quoteName('id') . ' != ' . (int) $id);
        }

        $db->setQuery($query);

        return (bool) $db->loadResult();
    }

    private function incrementName(string &$name, string &$alias, int $id = 0)
    {
        $alias = $alias ?: RL_Alias::get($name);

        while ($this->aliasExists($alias, $id))
        {
            $name  = RL_String::increment($name);
            $alias = RL_String::increment($alias, 'dash');
        }
    }
}
