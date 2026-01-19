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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Visitor;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\DB as RL_DB;
use RegularLabs\Library\User as RL_User;

class AccessLevel extends Condition
{
    use HasArraySelection;

    public function pass(): bool
    {
        $levels = RL_User::getAuthorisedViewLevels();

        $this->selection = $this->convertAccessLevelNamesToIds($this->selection);

        if ($this->params->match_all ?? false)
        {
            return $this->passMatchAll($levels);
        }

        return $this->passSimple($levels);
    }

    private function convertAccessLevelNamesToIds(array $selection): array
    {
        $names = [];

        foreach ($selection as $i => $level)
        {
            if (is_numeric($level))
            {
                continue;
            }

            unset($selection[$i]);

            $names[] = strtolower(str_replace(' ', '', $level));
        }

        if (empty($names))
        {
            return $selection;
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from('#__viewlevels')
            ->where(RL_DB::in('LOWER(REPLACE(' . $db->quoteName('title') . ', " ", ""))', $names, [], false));
        $db->setQuery($query);

        $level_ids = $db->loadColumn();

        return array_unique([...$selection, ...$level_ids]);
    }

    private function passMatchAll(array $groups): bool
    {
        return empty(array_diff($this->selection, $groups));
    }
}
