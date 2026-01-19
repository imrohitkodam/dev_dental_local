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

namespace RegularLabs\Component\Conditions\Administrator\Model;

use Exception;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel as JAdminModel;
use Joomla\Utilities\ArrayHelper as JArray;
use RegularLabs\Component\Conditions\Administrator\Helper\Cache;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Parameters as RL_Parameters;

defined('_JEXEC') or die;

class GroupModel extends JAdminModel
{
    protected $name = 'group';
    /**
     * @var        string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';
    /* @var RuleModel $group_model */
    private $rule_model;

    /**
     * @param array                $config      An array of configuration options (name, state, dbo, table_path, ignore_request).
     * @param MVCFactoryInterface  $factory     The factory.
     * @param FormFactoryInterface $formFactory The form factory.
     *
     * @throws  Exception
     */
    public function __construct(
        $config = [],
        ?MVCFactoryInterface $factory = null,
        ?FormFactoryInterface $formFactory = null
    )
    {
        parent::__construct($config, $factory, $formFactory);

        $this->config = RL_Parameters::getComponent('conditions');

        $this->rule_model = JFactory::getApplication()->bootComponent('com_conditions')
            ->getMVCFactory()->createModel('Rule', 'Administrator', ['ignore_request' => true]);
    }

    /**
     * @param array  &$pks An array of record primary keys.
     *
     * @return  boolean  True if successful, false if an error occurs.
     */
    public function delete(&$pks)
    {
        $group_ids = JArray::toInteger((array) $pks);

        $this->rule_model->deleteByGroupIds($group_ids);

        if ( ! parent::delete($pks))
        {
            return false;
        }

        $db    = $this->getDatabase();
        $query = $db->replacePrefix('ALTER TABLE ' . RL_DB::quoteName('#__conditions_groups') . ' AUTO_INCREMENT = 1');
        $db->setQuery($query);
        $db->execute();

        return true;
    }

    public function deleteByConditionId($condition_id)
    {
        $group_ids = $this->getGroupIdsByConditionId($condition_id);

        $this->delete($group_ids);
    }

    public function getForm($data = [], $loadData = true)
    {
        return false;
    }

    public function getRules($group_id)
    {
        $cache = (new Cache)->useFiles();

        if ($cache->exists())
        {
            return $cache->get();
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('*')
            ->from('#__conditions_rules as r')
            ->where('r.group_id = ' . (int) $group_id);

        $db->setQuery($query);

        $rules = $db->loadObjectList();

        foreach ($rules as &$rule)
        {
            if ( ! isset($rule->params))
            {
                continue;
            }

            $rule->params = (object) json_decode($rule->params);
        }

        return $cache->set($rules);
    }

    /**
     * @param array $data The form data.
     *
     * @return  boolean  True on success.
     */
    public function save($data)
    {
        $this->setState($this->getName() . '.id', null);

        $data = (array) $data;

        if (empty($data['rules']))
        {
            return true;
        }

        $rules = $data['rules'];

        $data['id'] = 0;

        unset($data['rules']);

        if ( ! parent::save($data))
        {
            return false;
        }

        $data['id'] = (int) $this->getState($this->getName() . '.id');

        foreach ($rules as $rule)
        {
            $rule->group_id = $data['id'];

            if ( ! $this->rule_model->save($rule))
            {
                return false;
            }
        }

        (new Cache())->resetAll();

        return parent::save($data);
    }

    private function getGroupIdsByConditionId(?int $condition_id): array
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select('g.id')
            ->from('#__conditions_groups as g')
            ->where('g.condition_id = ' . (int) $condition_id);

        $db->setQuery($query);

        return $db->loadColumn();
    }
}
