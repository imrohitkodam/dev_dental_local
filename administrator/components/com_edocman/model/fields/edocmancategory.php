<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 * @package     Joomla.Platform
 * @subpackage  Form
 * @since       11.1
 */
class JFormFieldEdocmanCategory extends JFormField
{
	/**
	 * @var    string    The form field type.
	 * @since  11.1
	 */
	public $type = 'EdocmanCategory';

	/**
	 * Method to get the field options.
	 *
	 * @return  array    The field option objects.
	 */
	protected function getInput()
	{
		// Initialise variables.
		require_once JPATH_ROOT . '/components/com_edocman/helper/helper.php';
		$app    = JFactory::getApplication();
		$db     = JFactory::getDbo();
		$user   = JFactory::getUser();

		$userId = $user->get('id');
		$config = EDocmanHelper::getConfig();
		$query  = $db->getQuery(true);
		$query->select('id, title, parent_id')
			->from('#__edocman_categories AS tbl')
			->where('published = 1')
			->order('ordering');
		if ($app->isSite())
		{
			if (!$user->authorise('core.admin'))
			{
                $usergroup          = $user->groups;
                $usergroupArr       = array();
                $usergroupSql       = "";
                if(count($usergroup) > 0){
                    foreach ($usergroup as $group){
                        $usergroupArr[] = " (groups='$group' OR groups LIKE '$group,%' OR groups LIKE '%,$group,%' OR groups LIKE '%,$group') AND `data_type` = 0";
                    }
                    $usergroupSql = implode(" OR ",$usergroupArr);
                    $usergroupSql = " tbl.id in (Select item_id from #__edocman_levels where $usergroupSql) ";
                    $usergroupSql = " OR (tbl.user_ids = '' AND tbl.accesspicker = '1' AND $usergroupSql ) ";
                }

				$query->where("((tbl.user_ids = '' AND tbl.access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) $usergroupSql OR tbl.user_ids='$userId' OR tbl.user_ids LIKE '$userId,%' OR tbl.user_ids LIKE '%,$userId,%' OR tbl.user_ids LIKE '%,$userId' OR (tbl.created_user_id=$userId AND tbl.created_user_id > 0))");
			}
			if ($config->activate_multilingual_feature && $app->getLanguageFilter())
			{
				$query->where('tbl.language IN (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ', "")');
			}
			//$query->where('published = 1');
		}
		$parentId = 0;
		$catId    = 0;
		if ($app->isSite())
		{
			$catId = $app->input->getInt('catid', 0);
			if ($catId)
			{
				$query->where('id IN (' . implode(',', EdocmanHelper::getChildrenCategories($catId)) . ')');
				$sql = 'SELECT parent_id FROM #__edocman_categories WHERE id=' . $catId;
				$db->setQuery($sql);
				$parentId = (int) $db->loadResult();
			}

		}
		$db->setQuery($query);
		$rows     = $db->loadObjectList();
		$children = array();
		// first pass - collect children
		if (count($rows))
		{
			foreach ($rows as $v)
			{
				$pt   = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list      = JHtml::_('menu.treerecurse', $parentId, '', array(), $children, 9999);
		$options   = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EDOCMAN_SELECT_CATEGORY'));
		if (count($list))
		{
			foreach ($list as $row)
			{
				$options[] = JHtml::_('select.option', $row->id, $row->treename);
			}
		}

		if ($action = (string) $this->element['action'])
		{
			// Get the current user object.
			$user = JFactory::getUser();

			foreach ($options as $i => $option)
			{
				if ($i == 0)
				{
					continue;
				}
				// To take save or create in a category you need to have create rights for that category
				// unless the item is already in that category.
				// Unset the option if the user isn't authorised for it. In this field assets are always categories.
				if ($user->authorise('core.create', 'com_edocman.category.' . $option->value) != true)
				{
					unset($options[$i]);
				}
			}

		}
		if ($this->element['readonly'])
		{
			$disabled = ' disabled="true" ';
		}
		else
		{
			$disabled = '';
		}

		if ($this->element['multiple'] == 'true')
		{
			$multiple = ' multiple="multiple "';
		}
		else
		{
			$multiple = '';
		}

		if ($this->element['class'])
		{
			$class = 'class="'.$this->element['class'].'" ';
		}
		else
		{
			$class = 'class="inputbox" ';
		}


		if ($disabled)
		{
			return JHtml::_('select.genericlist', $options, $this->name, array(
				'option.text.toHtml' => false,
				'list.attr'          => $class. $disabled . $multiple,
				'option.text'        => 'text',
				'option.key'         => 'value',
				'list.select'        => $this->value
			)) . '<input type="hidden" name="' . $this->name . '" value="' . $catId . '" />';
		}
		else
		{
			return JHtml::_('select.genericlist', $options, $this->name, array(
				'option.text.toHtml' => false,
				'list.attr'          => $class . $multiple,
				'option.text'        => 'text',
				'option.key'         => 'value',
				'list.select'        => $this->value
			));
		}
	}
}