<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EDocmanViewLanguageHtml extends OSViewHtml
{

	function display()
	{
		$jinput = JFactory::getApplication()->input;
		JFactory::getDocument()->addStyleSheet(JUri::base(true) . '/components/com_edocman/assets/css/style.css');
		$lang = $jinput->getString('lang', '');
		if (!$lang)
		{
			$lang = 'en-GB';
		}
		$item = 'com_edocman';
        $limitstart = $jinput->getInt('limitstart',0);
        $limit      = $jinput->getInt('limit',100);
        $search     = $jinput->getVar('search','');
        $site       = $jinput->getInt('site',0);

		$trans = $this->model->getTrans($lang, $item, $site, $search, $limitstart, $limit);
        $pagNav = $this->model->getPagination($item,$site, $search, $limitstart, $limit);

		$languages = $this->model->getSiteLanguages();
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('Select Language'));
		foreach ($languages as $language)
		{
			$options[] = JHtml::_('select.option', $language, $language);
		}
		$lists['langs'] = JHtml::_('select.genericlist', $options, 'lang', ' class="input-medium"  onchange="submit();" ', 'value', 'text', $lang);

        $options = array() ;
        $options[] = JHTML::_('select.option', 0, JText::_('Front-End Side')) ;
        $options[] = JHTML::_('select.option', 1, JText::_('Back-End Side')) ;
        $lists['site'] = JHTML::_('select.genericlist', $options, 'site', ' class="input-medium"  onchange="this.form.submit();" ', 'value', 'text', $site) ;

		EDocmanHelperHtml::renderSubmenu('language');

		$this->trans = $trans;
		$this->lists = $lists;
		$this->lang = $lang;
		$this->item = $item;
        $this->pagNav = $pagNav;
        $this->search = $search;
		parent::display();
	}
}