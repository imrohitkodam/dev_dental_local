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

class UserGroup extends Condition
{
    use HasArraySelection;

    static $user_group_children;

    public function pass(): bool
    {
        $user = RL_User::get();

        $groups = ! empty($user->groups)
            ? array_values($user->groups)
            : $user->getAuthorisedGroups();

        if ( ! $this->params->match_all && $this->params->include_children)
        {
            $this->setUserGroupChildrenIds();
        }

        $this->selection = $this->convertUsergroupNamesToIds($this->selection);

        if ($this->params->match_all ?? false)
        {
            return $this->passMatchAll($groups);
        }

        return $this->passSimple($groups);
    }

    private function convertUsergroupNamesToIds(array $selection): array
    {
        $names = [];

        foreach ($selection as $i => $group)
        {
            if (is_numeric($group))
            {
                continue;
            }

            unset($selection[$i]);

            $names[] = strtolower(str_replace(' ', '', $group));
        }

        if (empty($names))
        {
            return $selection;
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select($db->quoteName('id'))
            ->from('#__usergroups')
            ->where(RL_DB::in('LOWER(REPLACE(' . $db->quoteName('title') . ', " ", ""))', $names, [], false));
        $db->setQuery($query);

        $group_ids = $db->loadColumn();

        return array_unique([...$selection, ...$group_ids]);
    }

    private function getUserGroupChildrenIds(array $groups): array
    {
        $children = [];

        foreach ($groups as $group)
        {
            $group_children = $this->getUserGroupChildrenIdsByGroup($group);

            if (empty($group_children))
            {
                continue;
            }

            $children = [...$children, ...$group_children];

            $group_grand_children = $this->getUserGroupChildrenIds($group_children);

            if (empty($group_grand_children))
            {
                continue;
            }

            $children = [...$children, ...$group_grand_children];
        }

        $children = array_unique($children);

        return $children;
    }

    private function getUserGroupChildrenIdsByGroup(?int $group): array
    {
        $group = (int) $group;

        if ( ! is_null(self::$user_group_children))
        {
            return self::$user_group_children[$group] ?? [];
        }

        $db = RL_DB::get();

        $query = $db->getQuery(true)
            ->select(['id', 'parent_id'])
            ->from($db->quoteName('#__usergroups'));
        $db->setQuery($query);

        $groups = $db->loadAssocList('id', 'parent_id');

        foreach ($groups as $id => $parent)
        {
            if ( ! isset(self::$user_group_children[$parent]))
            {
                self::$user_group_children[$parent] = [];
            }

            self::$user_group_children[$parent][] = $id;
        }

        return self::$user_group_children[$group] ?? [];
    }

    private function passMatchAll(array $groups): bool
    {
        return empty(array_diff($this->selection, $groups));
    }

    private function setUserGroupChildrenIds(): void
    {
        $children = $this->getUserGroupChildrenIds($this->selection);

        if (($this->params->include_children ?? 0) === 2)
        {
            $this->selection = $children;

            return;
        }

        $this->selection = [...$this->selection, ...$children];
    }
}
