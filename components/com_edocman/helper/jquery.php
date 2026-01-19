<?php
/**
 * @version		   1.7.5
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2011 - 2016 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;

abstract class EDocmanHelperJquery
{		
	/**
	 * Method to load the plupload into the document head
	 *
	 * If debugging mode is on an uncompressed version of plupload is included for easier debugging.
	 *
	 * @return  void
	 */
	public static function upload()
	{
		static $loaded = false;
		if ($loaded)
		{
			return;
		}
		EDocmanHelper::loadJQuery();
		JHtml::_('script', JUri::root().'components/com_edocman/assets/js/noconflict.js', false, false);
		JHtml::_('stylesheet', JUri::root().'components/com_edocman/assets/js/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css', false, false);
		JHtml::_('script', JUri::root().'components/com_edocman/assets/js/plupload/plupload.full.min.js', false, false);
		JHtml::_('script', JUri::root().'components/com_edocman/assets/js/plupload/jquery.plupload.queue/jquery.plupload.queue.min.js', false, false);

		$activeLanguageTag = JFactory::getLanguage()->getTag();
		$allowedLanguageTags = array('ar-AA', 'bs-BA', 'ca-ES', 'cs-CZ', 'da-DK', 'de-DE', 'el-GR', 'en-AU', 'en-GB',
			'en-US', 'es-ES', 'et-EE', 'fa-IR', 'fi-FI', 'fr-FR', 'he-IL', 'hr-HR', 'hu-HU', 'it-IT', 'ja-JP', 'ko-KR',
			'lv-LV', 'nl-NL', 'pl-PL', 'pt-BR', 'ro-RO', 'ru-RU', 'sk-SK', 'sr-RS', 'sr-YU', 'sv-SE', 'th-TH', 'tr-TR',
			'uk-UA', 'zh-CN', 'zh-TW'
		);
		$showedLanguage = in_array($activeLanguageTag, $allowedLanguageTags) ? $activeLanguageTag : 'en-GB';

		JHtml::_('script', JUri::root().'components/com_edocman/assets/js/plupload/i18n/' . $showedLanguage . '.js', false, false);

		$loaded = true;
	}
	
	
}