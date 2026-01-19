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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Flexicontent;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

class Type extends Flexicontent
{
    use HasArraySelection;

    public function pass(): bool
    {
        if ($this->request->option !== 'com_flexicontent')
        {
            return false;
        }

        if ( ! in_array($this->request->view, ['item', 'items']))
        {
            return false;
        }

        $query = $this->db->getQuery(true)
            ->select('x.type_id')
            ->from('#__flexicontent_items_ext AS x')
            ->where('x.item_id = ' . (int) $this->request->id);
        $this->db->setQuery($query);
        $type = $this->db->loadResult();

        $types = $this->makeArray($type);

        return $this->passSimple($types);
    }
}
