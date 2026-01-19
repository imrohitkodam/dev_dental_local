<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	Copyright (C) 2011-2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();
/**
 * View class for a list of EDocman.
 */
class EDocmanViewDocumentsHtml extends OSViewList
{
	/**
	 * Preare data for the view before displaying
	 */
	protected function prepareView()
	{
		parent::prepareView();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$user = JFactory::getUser();
		$userId = $user->id;
		$query->select('id, title, parent_id');
		$query->from('#__edocman_categories');
		$query->where("published=1 and ((user_ids = '' AND access IN (" . implode(',', $user->getAuthorisedViewLevels()) . ")) OR user_ids='$userId' OR user_ids LIKE '$userId,%' OR user_ids LIKE '%,$userId,%' OR user_ids LIKE '%,$userId' OR (created_user_id='".$userId."' AND created_user_id > 0))");
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$children = array();
		// first pass - collect children
		if (count($rows))
		{
			foreach ($rows as $v)
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('EDOCMAN_SELECT_CATEGORY'));
		if (count($list))
		{
			foreach ($list as $row)
			{
				$options[] = JHtml::_('select.option', $row->id, $row->treename);
			}
		}
		$this->lists['filter_category_id'] = JHtml::_('select.genericlist', $options, 'filter_category_id',
			array(
				'option.text.toHtml' => false,
				'list.attr' => 'class="inputbox" onchange="submit();"  ',
				'option.text' => 'text',
				'option.key' => 'value',
				'list.select' => (int) $this->state->filter_category_id));


		// Upload New Document Form
		$layout = $this->getLayout();
		$user = JFactory::getUser();
		if ($user->authorise('core.create', 'com_edocman') || count(EDocmanHelper::getAuthorisedCategories('core.create')))
		{
			$this->canUpload = true;
		}
		else
		{
			$this->canUpload = false;
		}
		if ($layout == 'modal' && $this->canUpload)
		{
			// Upload new document form options
			$this->lists['category_id'] = JHtml::_('select.genericlist', $options, 'category_id',
				array(
					'option.text.toHtml' => false,
					'list.attr'          => 'class="inputbox" required="required"',
					'option.text'        => 'text',
					'option.key'         => 'value',
					'list.select'        => $this->input->getInt('category_id', 0)));

			$documentLinkPlugin                    = JPluginHelper::getPlugin('editors-xtd', 'edocman');
			$params                                = new JRegistry($documentLinkPlugin->params);
			$options                               = array();
			$options[]                             = JHtml::_('select.option', 0, JText::_('EDOCMAN_CHOOSE_EXISTING_DOCUMENT'));
			$options[]                             = JHtml::_('select.option', 1, JText::_('EDOCMAN_UPLOAD_NEW_DOCUMENT'));
			$this->lists['choose_document_option'] = JHtml::_('select.radiolist', $options, 'choose_document_option', ' class="inputbox" onclick="changeOption(this.value)" ', 'value', 'text', $this->input->getInt('choose_document_option', $params->get('default_insert_option', 0)));

			$this->lists['published'] = JHtml::_('select.booleanlist', 'published', ' class="inputbox" ', $this->input->getInt('published', 1));

			//Access
			$this->lists['access'] = JHtml::_('access.level', 'access', $this->input->getInt('access', 1), true);
		}

		$this->config = EdocmanHelper::getConfig();
	}
}
