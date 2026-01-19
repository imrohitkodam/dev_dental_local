<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\CMS\Factory;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Http\Request;
use YOOtheme\Http\Response;

class SourceController
{
    /**
     * @throws \Exception
     */
    public static function articles(
        Request $request,
        Response $response,
        DatabaseDriver $db,
        User $user
    ): Response {
        $ids = implode(',', array_map('intval', (array) $request->getQueryParam('ids')));
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $titles = [];

        if (!empty($ids)) {
            $query = "SELECT id, title
                FROM #__content
                WHERE id IN ({$ids})
                AND access IN ({$groups})";

            $titles = $db->setQuery($query)->loadAssocList('id', 'title');
        }

        return $response->withJson((object) $titles);
    }

    /**
     * @throws \Exception
     */
    public static function users(Request $request, Response $response, User $user): Response
    {
        $titles = [];

        if ($user->authorise('core.manage', 'com_users')) {
            foreach ((array) $request->getQueryParam('ids') as $id) {
                $titles[$id] = Factory::getUser($id)->name;
            }
        }

        return $response->withJson((object) $titles);
    }
}
