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
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\MVC\Model\AdminModel as JAdminModel;
use RegularLabs\Component\Conditions\Administrator\Helper\Cache;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Parameters as RL_Parameters;

defined('_JEXEC') or die;

class RuleModel extends JAdminModel
{
    protected $name = 'rule';
    /**
     * @var        string    The prefix to use with controller messages.
     */
    protected $text_prefix = 'RL';

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
    }

    public function deleteByGroupIds($group_ids)
    {
        if (empty($group_ids))
        {
            return;
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true)
            ->delete('#__conditions_rules')
            ->where(RL_DB::in(RL_DB::quoteName('group_id'), $group_ids));
        $db->setQuery($query);
        $db->execute();

        $query = $db->replacePrefix('ALTER TABLE ' . RL_DB::quoteName('#__conditions_rules') . ' AUTO_INCREMENT = 1');
        $db->setQuery($query);
        $db->execute();
    }

    public function getForm($data = [], $loadData = true)
    {
        return false;
    }

    /**
     * @param array $data The form data.
     *
     * @return  boolean  True on success.
     */
    public function save($data)
    {
        $this->setState($this->getName() . '.id', null);

        $data           = (array) $data;
        $data['id']     = 0;
        $data['params'] = json_encode($data['params']);

        (new Cache())->resetAll();

        return parent::save($data);
    }

    private function getRulesByGroupId(?int $group_id): array
    {
        $cache = new Cache;

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
}
