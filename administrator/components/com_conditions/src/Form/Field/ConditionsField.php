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

namespace RegularLabs\Component\Conditions\Site\Form\Field;

defined('_JEXEC') or die;

use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\Form\FormField as RL_FormField;

class ConditionsField extends RL_FormField
{
    static      $options;
    public bool $is_select_list = true;

    public function getNamesByIds(array $values, array $attributes): array
    {
        $query = $this->db->getQuery(true)
            ->select('condition.name')
            ->from('#__conditions AS condition')
            ->where(RL_DB::is('condition.id', $values))
            ->order('condition.name ASC');

        $this->db->setQuery($query);

        return $this->db->loadColumn();
    }

    protected function getOptions()
    {
        if ( ! is_null(self::$options))
        {
            return self::$options;
        }

        $query = $this->db->getQuery(true)
            ->select('condition.id as value, condition.name as text')
            ->from('#__conditions AS condition')
            ->order('a.name ASC');
        $this->db->setQuery($query);

        self::$options = $this->db->loadObjectList();

        return self::$options;
    }
}
