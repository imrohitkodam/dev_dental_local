<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\CMS\Access\Access;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\UserGroupsHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\Component\Contact\Site\Helper\RouteHelper;
use Joomla\Component\Users\Administrator\Extension\UsersComponent;
use Joomla\Component\Users\Administrator\Model\UsersModel;
use Joomla\Database\DatabaseDriver;
use function YOOtheme\app;

class UserHelper
{
    /**
     * Gets the user's contact.
     *
     * @param int $id
     *
     * @return object|null
     */
    public static function getContact($id)
    {
        static $contacts = [];

        if (!isset($contacts[$id])) {
            /** @var DatabaseDriver $db */
            $db = app(DatabaseDriver::class);
            $query = sprintf(
                'SELECT id AS contactid, alias, catid
                FROM #__contact_details
                WHERE published = 1 AND user_id = %d',
                $id,
            );

            if (Multilanguage::isEnabled() === true) {
                $lang = Factory::getApplication()->getLanguage();
                $query .= sprintf(
                    ' AND (language IN (%s, %s) OR language IS NULL)',
                    $db->quote($lang->getTag()),
                    $db->quote('*'),
                );
            }

            $query .= ' ORDER BY id DESC LIMIT 1';

            $contacts[$id] = $db->setQuery($query)->loadObject() ?: false;
        }

        return $contacts[$id] ?: null;
    }

    /**
     * Query users.
     *
     * @param array $args
     *
     * @return array
     */
    public static function queryContacts(array $args = [])
    {
        $model = new ContactsModel(['ignore_request' => true]);
        $model->setState('params', ComponentHelper::getParams('com_contact'));
        $model->setState('filter.published', 1);

        $props = [
            'offset' => 'list.start',
            'limit' => 'list.limit',
            'order' => 'list.ordering',
            'order_direction' => 'list.direction',
            'catid' => 'filter.category_id',
            'tag' => 'filter.tags',
            'include_child_categories' => 'filter.include_child_categories',
            'include_child_tags' => 'filter.include_child_tags',
        ];

        foreach (array_intersect_key($props, $args) as $key => $prop) {
            $model->setState($prop, $args[$key]);
        }

        return $model->getItems();
    }

    /**
     * Gets the user's contact link.
     *
     * @param int $id
     *
     * @return string|null
     */
    public static function getContactLink($id)
    {
        if (!($contact = self::getContact($id))) {
            return null;
        }

        return Route::_(RouteHelper::getContactRoute($contact->contactid, (int) $contact->catid));
    }

    /**
     * Query users.
     *
     * @param array $args
     *
     * @return array
     */
    public static function query(array $args = [])
    {
        /** @var UsersModel $model */
        $model = static::getModel();
        $model->setState('params', ComponentHelper::getParams('com_users'));
        $model->setState('filter.active', true);
        $model->setState('filter.state', 0);

        $props = [
            'offset' => 'list.start',
            'limit' => 'list.limit',
            'order' => 'list.ordering',
            'order_direction' => 'list.direction',
            'groups' => 'filter.groups',
        ];

        if (empty($args['groups'])) {
            unset($args['groups']);
        }

        foreach (array_intersect_key($props, $args) as $key => $prop) {
            $model->setState($prop, $args[$key]);
        }

        return $model->getItems();
    }

    public static function getAuthorList()
    {
        /** @var DatabaseDriver $db */
        $db = app(DatabaseDriver::class);
        $query = sprintf(
            'SELECT DISTINCT(m.user_id) AS value, u.name AS text
            FROM #__usergroups AS ug1
            JOIN #__usergroups AS ug2 ON ug2.lft >= ug1.lft AND ug1.rgt >= ug2.rgt
            JOIN #__user_usergroup_map AS m ON ug2.id=m.group_id
            JOIN #__users AS u ON u.id=m.user_id
            WHERE ug1.id IN (%s)',
            join(
                ',',
                array_filter(
                    array_map(fn($group) => $group->id, UserGroupsHelper::getInstance()->getAll()),
                    fn($id) => Access::checkGroup($id, 'core.create', 'com_content') ||
                        Access::checkGroup($id, 'core.admin'),
                ),
            ),
        );

        return $db->setQuery($query)->loadObjectList();
    }

    protected static function getModel()
    {
        if (version_compare(JVERSION, '4.0', '<')) {
            BaseDatabaseModel::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_users/models');

            return BaseDatabaseModel::getInstance('users', 'UsersModel', [
                'ignore_request' => true,
            ]);
        }

        /** @var UsersComponent $component */
        $component = Factory::getApplication()->bootComponent('com_users');

        return $component
            ->getMVCFactory()
            ->createModel('users', 'administrator', ['ignore_request' => true]);
    }
}
