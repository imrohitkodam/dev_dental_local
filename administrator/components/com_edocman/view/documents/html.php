<?php
/**
 * @version        1.11.2
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EDocmanViewDocumentsHtml extends OSViewList
{
	/**
	 * Prepare data for the view before displaying
	 */
	protected function prepareView()
	{
		parent::prepareView();
		jimport('joomla.filesystem.folder');
		require_once JPATH_ROOT . '/components/com_edocman/helper/file.class.php';
        $orphan_state = $this->input->getInt('filter_orphan_state',0);
        $this->state->filter_orphan_state = $orphan_state;
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, title, parent_id');
		$query->from('#__edocman_categories');
		$query->where('published=1');
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

		$list      = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999);
		$options   = array();
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
				'list.attr'          => 'class="inputbox" onchange="submit();"  ',
				'option.text'        => 'text',
				'option.key'         => 'value',
				'list.select'        => (int) $this->state->filter_category_id));

        $this->lists['moving_category_id'] = JHtml::_('select.genericlist', $options, 'moving_category_id',
            array(
                'option.text.toHtml' => false,
                'list.attr'          => 'class="input-xxlarge" ',
                'option.text'        => 'text',
                'option.key'         => 'value',
                'list.select'        => (int) $this->state->moving_category_id));

		$this->lists['moving_category_id1'] = JHtml::_('select.genericlist', $options, 'moving_category_id1',
            array(
                'option.text.toHtml' => false,
                'list.attr'          => 'class="input-xxlarge" ',
                'option.text'        => 'text',
                'option.key'         => 'value',
                'list.select'        => (int) $this->state->moving_category_id1));

		// Upload New Document Form
		$layout = $this->getLayout();
		if ($layout == 'modal')
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

        $optionArr = array();
        $optionArr[] = JHtml::_('select.option','','--Select Orphan state--');
        $optionArr[] = JHtml::_('select.option','1','Show Only Orphan documents');
        $this->lists['filter_orphan_state'] = JHtml::_('select.genericlist',$optionArr,'filter_orphan_state','class="input-large" onChange="javascript:document.adminForm.submit();"','value','text',$this->input->getInt('filter_orphan_state', ''));

		$this->config = EdocmanHelper::getConfig();

		$query = $db->getQuery(true);
		$query->select('count(extension_id)');
		$query->from('#__extensions');
		$query->where('`element` like "indexer" and `folder` like "edocman" and enabled=1');
		$db->setQuery($query);
		$count = $db->loadResult();
		if(($count > 0) && (JFolder::exists(JPATH_ROOT.'/plugins/edocman/indexer')))
		{
			$this->indexer = 1;
		}
		else
		{
			$this->indexer = 0;
		}

		$query->clear();
		$query->select('distinct a.id as value, a.name as text')->from('#__users as a')->join('inner','#__edocman_documents as b on a.id = b.created_user_id');
		$db->setQuery($query);
		$creator                = $db->loadObjectList();
		$optionArr              = array();
		$optionArr[]            = JHtml::_('select.option','',JText::_('EDOCMAN_FILTER_USERS'));
		$optionArr              = array_merge($optionArr,$creator);
		$this->lists['creator'] = JHtml::_('select.genericlist',$optionArr,'creator','class="input-large" onChange="Joomla.submitform();"','value','text', $this->input->getInt('creator',0));

		// We don't need toolbar in the modal window.
		if (version_compare(JVERSION, '3.0', 'ge')) {
			if ($this->getLayout() !== 'modal')
			{
				////EdocmanHelper::addSideBarmenus('documents');
				//$this->sidebar = JHtmlSidebar::render();
			}
		}
	}

    protected function addToolbar(){
        $layout = $this->getLayout();;
        if($layout != "remove_orphan"){
            parent::addToolbar();
        }
    }
}
