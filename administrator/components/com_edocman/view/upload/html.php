<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

/**
 * View to edit
 */
class EdocmanViewUploadHtml extends OSViewHtml
{
	public function display()
	{
		$layout                 = $this->getLayout();
		if ($layout == 'edit')
		{
			$this->_displayEditDocumentsForm();
			return;
		}
		$db                     = JFactory::getDbo();
		$query                  = $db->getQuery(true);
		$query->select('id, title, parent_id');
		$query->from('#__edocman_categories');
		$query->where('published=1');
		$db->setQuery($query);
		$rows                   = $db->loadObjectList();
		$children               = array();
		// first pass - collect children
		if (count($rows))
		{
			foreach ($rows as $v)
			{
				$pt             = $v->parent_id;
				$list           = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt]  = $list;
			}
		}
		$list                   = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999);
		$options                = array();
		$options[]              = JHtml::_('select.option', 0, JText::_('EDOCMAN_SELECT_CATEGORY'));
		if (count($list))
		{
			foreach ($list as $row)
			{
				$options[]      = JHtml::_('select.option', $row->id, $row->treename);
			}
		}
		$lists['category_id']   = JHtml::_('select.genericlist', $options, 'category_id',
			array(
				'option.text.toHtml' => false, 
				'list.attr' => 'class="inputbox"', 
				'option.text' => 'text', 
				'option.key' => 'value', 
				'list.select' => 0));
		$lists['published']     = JHtml::_('select.booleanlist', 'published', ' class="inputbox" ', 1);
		$optionArr              = array();
		$optionArr[]            = JHtml::_('select.option',0,JText::_('EDOCMAN_PRESETS'));
        $optionArr[]            = JHtml::_('select.option',1,JText::_('EDOCMAN_GROUPS'));
        $lists['accesspicker']  = JHtml::_('select.genericlist',$optionArr,'accesspicker','','value','text');
		//Access
		$lists['access']        = JHtml::_('access.level', 'access', 1, true);
		$lists['groups']        = JHtml::_('access.usergroup','groups[]',array(),'multiple class="input-large chosen inputbox"');
		$config                 = EDocmanHelper::getConfig();
		$maxFilesize            = $config->max_file_size ? (int) $config->max_file_size : 2;
		$maxFilesizeType        = $config->max_filesize_type ? (int) $config->max_filesize_type : 3;
		if ($maxFilesizeType == 1)
		{
			$maxFilesize        = ceil($maxFilesize / (1024 * 1024));
		}
		elseif ($maxFilesizeType == 2)
		{
			$maxFilesize        = ceil($maxFilesize / 1024);
		}
		if (!$maxFilesize)
		{
			$maxFilesize        = 2;
		}
		$allowedFiletypes       = $config->allowed_file_types;
		if (!$allowedFiletypes)
		{
			$allowedFiletypes   = 'doc, docx, ppt, pptx, pdf, zip, rar, png, zipx';
		}
		$allowedFiletypes       = explode(',', $allowedFiletypes);
		$allowedFiletypes       = array_map('trim', $allowedFiletypes);
		$this->lists            = $lists;
		$this->maxFilesize      = $maxFilesize;
		$this->allowedFiletypes = implode(',', $allowedFiletypes);
		$this->config           = $config;
		EDocmanHelperJquery::upload();
		
		//Clear session data
		$session = JFactory::getSession();
		$session->clear('files');
		$session->clear('originalFiles');
        $session->clear('filesize');
		EDocmanHelperHtml::renderSubmenu('upload');
		parent::display();
	}		
	/**
	 * Display form which allows editing documents before storing into database
	 */
	protected function _displayEditDocumentsForm()
	{
		$session                = JFactory::getSession();
		$this->state            = $this->model->getState();
		$this->files            = (array) $session->get('files', array());
		$this->originalFiles    = (array) $session->get('originalFiles', array());
        $this->filesizes        = (array) $session->get('filesize', array());
        $this->fileid           = (array) $session->get('fileid', array());
		EDocmanHelperHtml::renderSubmenu('upload');
		parent::display();
	}
}
