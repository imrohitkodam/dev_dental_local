<?php
/**
 * @version    SVN: <svn_id>
 * @package    JLike
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die();

Foundry::import('admin:/includes/model');

/**
 * JlikemylikesModel for likes data
 *
 * @package     JLike
 * @subpackage  com_jlike
 * @since       1.1.8
 */
class JlikemylikesModel extends EasySocialModel
{
	/**
	 * Retrieves a list of likes by a particular user.
	 *
	 * @param   INT  $userId  The user's id
	 * @param   INT  $limit   No. of records to show
	 *
	 * @return	Array  A list of records.
	 *
	 * @since	1.0
	 */
	public function getItems($userId , $limit = 0)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('likecontent.*');
		$query->select($db->quoteName('likeannotations.annotation', 'annotation'));
		$query->select($db->quoteName('likelist.title', 'list_name'));
		$query->from($db->quoteName('#__jlike_content', 'likecontent'));

		$query->join('LEFT', $db->quoteName('#__jlike_likes', 'likes') . ' ON ('
		. $db->quoteName('likecontent.id') . ' = ' . $db->quoteName('likes.content_id') . ')' . ' AND likes.userid = ' . $userId
		);

		$query->join('LEFT', $db->quoteName('#__jlike_annotations', 'likeannotations')
		. ' ON (' . $db->quoteName('likeannotations.content_id') . ' = ' . $db->quoteName('likecontent.id') . ')'
		. ' AND likeannotations.user_id = likes.userid');

		$query->join('LEFT', $db->quoteName('#__jlike_likes_lists_xref', 'listxref')
		. ' ON (' . $db->quoteName('likeannotations.content_id') . ' = ' . $db->quoteName('listxref.content_id') . ')'
		);

		$query->join('LEFT', $db->quoteName('#__jlike_like_lists', 'likelist')
		. ' ON (' . $db->quoteName('listxref.list_id') . ' = ' . $db->quoteName('likelist.id') . ') AND '
		. $db->quoteName('likes.userid')
		. ' = ' . $db->quoteName('likelist.user_id')
		);

		$query->where('(' . $db->quoteName('likes.like') . ' = 1 OR' . $db->quoteName('likes.dislike') . '= 1 )');

		$query->group($db->quoteName('likecontent.id'));

		if ($limit)
		{
			$query .= ' limit ' . $limit;
		}

		$db->setQuery($query);
		$result = $db->loadObjectList();

		return $result;
	}
}
