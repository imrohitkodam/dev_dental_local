<?php
/**
 * @version    SVN: <svn_id>
 * @package    Plg_System_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access.
defined('_JEXEC') or die;

/**
 * Methods supporting a list of Tjlms action.
 *
 * @since  1.0.0
 */
class PlgSystemplg_Tjlms_Custom_Fields extends JPlugin
{
	/**
	 * Function onAfterRender
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	public function onAfterRender()
	{
		$app = JFactory::getApplication();

		if ($app->isSite() == false)
		{
			return;
		}

		// Get the body
		$body = $app->getBody();

/*
		// Get current menu
		$menu = JFactory::getApplication()->getMenu();
		if(!empty($menu->getActive()))
		{
			$parent = $menu->getItem($menu->getActive()->parent_id);
			$p_id = 0;

			// Check parent menu id
			if (!empty($parent->id))
			{
				$p_id = $parent->id;
			}

			// Check for current & parent menu items
			if ((in_array($p_id, $this->params->get('menulist')) || in_array($menu->getActive()->id, $this->params->get('menulist'))) && $this->params->get('use_menu'))
			{
				// Replace the tags with the actual custom fields value
				$body = $this->replaceTags($body);
				$app->setBody($body);
			}
			else
			{
				*/
				// Replace the tags with the actual custom fields value
				$body = $this->replaceTags($body);
				$app->setBody($body);
			//}
	//	}
	}

	/**
	 * Function replaceTags used  to replce the tags
	 *
	 * @param   object  $text  The context of the content being passed to the plugin.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function replaceTags($text)
	{
		/* string eg :
		 {com_tjlms_fields course_id|custom_field_name}
		 {com_tjlms_fields 46|com_tjlms_course_radio}
		 */

		// Regex for the string
		$regex = '/\{com_tjlms_fields\ ([^\}]+)\}/';

		// Compare the regex with the body text
		if (preg_match_all($regex, $text, $matches))
		{
			foreach ($matches[1] as $matchIndex => $match)
			{
				$id = explode('|', $match);
				$cid = $id[0];
				$title = $id[1];
				$tag = $matches[0][$matchIndex];
				$data = $this->getfieldvalue($cid, $title);

				// Replace the string
				$text = str_replace($tag, $data, $text);
			}
		}

		return $text;
	}

	/**
	 * Function getfieldvalue used  to replce the tags
	 *
	 * @param   int     $cid    The context of the content being passed to the plugin.
	 * @param   string  $title  The context of the content being passed to the plugin.
	 *
	 * @return  void
	 *
	 * @since  1.0.0
	 */
	protected function getfieldvalue($cid, $title)
	{
		// Get the value of custom field
		$db = JFactory::getDBO();
		$query = $db->getQuery('true');
		$query->select('a.*, b.label,b.type');
		$query->from('#__tjfields_fields_value as a');
		$query->leftjoin('#__tjfields_fields as b ON b.id = a.field_id');
		$query->where('a.content_id = ' . $cid);
		$query->where('a.client LIKE "com_tjlms.course"');
		$query->where('b.name LIKE "' . $title . '"');
		$db->setQuery($query);
		$result = $db->loadObject();

		if (!empty($result))
		{
			switch ($result->type)
			{
				case 'text' :
				case 'textarea' :
				case 'editor' :
					$string = $result->value;
				break;

				case 'file' :
					$fileValue = explode("/", $result->value);
					$file_name = end($fileValue);

					$imageType = array('gif', 'png', 'jpg', 'jpeg');
					$ext       = pathinfo($result->value, PATHINFO_EXTENSION);
					$path      = $result->value;

					if (in_array($ext, $imageType))
					{
						$string = '<img src="' . JUri::root() . $result->value . '" />';
					}
					else
					{
						$string = '<a href="' . JUri::root() . $path . '" target="_blank">' . $file_name . '</a>';
					}
				break;

				case 'user' :
					require_once JPATH_ADMINISTRATOR . '/components/com_easysocial/includes/easysocial.php';
					$my = ES::user($result->value);
					$string = $my->getName();
				break;

				case 'single_select':
				case 'radio' :
					$query = $db->getQuery('true');
					$query->select('a.*');
					$query->from('#__tjfields_options as a');
					$query->where('a.field_id = ' . $result->field_id);
					$query->where('a.value LIKE "' . $result->value . '"');
					$db->setQuery($query);
					$opt = $db->loadObject();
					$string = $opt->options;
				break;

				case 'multi_select':
					$valArray = json_decode($result->value);
					$valString = "'" . implode("', '", $valArray) . "'";

					$query = $db->getQuery('true');
					$query->select('a.*');
					$query->from('#__tjfields_options as a');
					$query->where('a.field_id = ' . $result->field_id);
					$query->where('a.value IN (' . $valString . ')');
					$db->setQuery($query);
					$valOpt = $db->loadObjectList();
					$count = count($valOpt);
					$i = 1;
					$opt_string = '';

					foreach ($valOpt as $opt)
					{
						$opt_string .= $opt->options;

						if ($i < $count)
						{
							$opt_string .= ', ';
						}

						$i++;
					}

					$string = $opt_string;
				break;

				default :
				$string = $result->value;
			}
		}

		return $string;
	}
}
