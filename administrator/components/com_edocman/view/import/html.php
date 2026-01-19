<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2018-2011 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();
class EDocmanViewImportHtml extends OSViewHtml
{

	function display()
	{
		$db                     = JFactory::getDbo();
		$jinput                 = JFactory::getApplication()->input;
		$config                 = EdocmanHelper::getConfig();
		$path                   = $config->documents_path;
		$path                   = str_replace("\\", '/', $path);
		$folders                = JFolder::folders($path, '.', true, true);
		$pathLength             = strlen($path);
		$options                = array();
		$options[]              = JHtml::_('select.option', '', JText::_('Root'));
		if (count($folders))
		{
			foreach ($folders as $folder)
			{
				$folder         = str_replace("\\", '/', $folder);
				$folder         = substr($folder, $pathLength + 1);
				$options[]      = JHtml::_('select.option', $folder, $folder);
			}
		}		
		$lists['folder'] = JHtml::_('select.genericlist', $options, 'folder', ' class="inputbox" ', 'value', 'text', $jinput->getString('folder'));		
		$sql                    = 'SELECT id, title, parent_id FROM #__edocman_categories WHERE published = 1';
		$db->setQuery($sql);
		$rows                   = $db->loadObjectList();
		$children = array();
		// first pass - collect children
		if (count($rows))
		{
			foreach ($rows as $v)
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
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
				'list.attr' => 'class="inputbox" ',
				'option.text' => 'text',
				'option.key' => 'value',
				'list.select' =>0));
		
		$lists['access']        = JHtml::_('access.level', 'access', 1, ' class="input-large" ', false);
        $lists['groups']        = JHtml::_('access.usergroup','groups[]',array(),'multiple class="input-large chosen inputbox"');
        $optionArr              = array();
        $optionArr[]            = JHtml::_('select.option',0,JText::_('EDOCMAN_PRESETS'));
        $optionArr[]            = JHtml::_('select.option',1,JText::_('EDOCMAN_GROUPS'));
        $lists['accesspicker']  = JHtml::_('select.genericlist',$optionArr,'accesspicker','','value','text');
		$allowedFileTypes = EdocmanHelper::getConfigValue('allowed_file_types');
		$this->allowedFileTypes = $allowedFileTypes;
		$this->lists = $lists;
		$this->config = $config;
		// We don't need toolbar in the modal window.
		if (version_compare(JVERSION, '3.0', 'ge')) {
			if ($this->getLayout() !== 'modal')
			{
				//EdocmanHelper::addSideBarmenus('import');
				//$this->sidebar = JHtmlSidebar::render();
				EDocmanHelperHtml::renderSubmenu('import');
			}
		}
		parent::display();
	}
}