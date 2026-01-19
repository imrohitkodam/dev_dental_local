<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EDocmanModelLanguage extends OSModel
{

    function getTotal($languageFile,$site,$search){
        jimport('joomla.filesystem.file');
        $search = @strtolower($search);
        $registry = new JRegistry();
        if($languageFile == "com_edocman"){
            if ($site == 1)
            {
                $languageFolder = JPATH_ROOT . '/administrator/language/';
            }
            else
            {
                $languageFolder = JPATH_ROOT . '/language/';
            }
        }else{
            $languageFolder = JPATH_ROOT . '/language/';
        }
        $path = $languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini';

        $registry->loadFile($path, 'INI');
        $enGbItems = $registry->toArray();
        if ($search)
        {
            $search = strtolower($search);
            foreach ($enGbItems as $key => $value)
            {
                if (strpos(strtolower($key), $search) === false && strpos(strtolower($value), $search) === false)
                {
                    unset($enGbItems[$key]);
                }
            }
        }
        return count($enGbItems);
    }

    /**
     * Get pagination object
     *
     * @return JPagination
     */
    function getPagination($item, $site,$search,$limitstart,$limit)
    {
        // Lets load the content if it doesn't already exist
        if (empty($pagination))
        {
            jimport('joomla.html.pagination');
            $pagination = new JPagination($this->getTotal($item,$site,$search), $limitstart, $limit);
        }

        return $pagination;
    }
	/**
	 * Get language items and store them in an array
	 */
	function getTrans($lang, $item, $site, $search ,$limitstart, $limit)
	{
		$registry  = new JRegistry();
		$languages = array();
        $path = JPATH_ROOT.'/language' ;
        if ($site) $path = JPATH_ROOT.'/administrator/language';
        if($item == "com_edocman"){
            if ($site == 1)
            {
                $languageFolder = JPATH_ROOT . '/administrator/language/';
            }
            else
            {
                $languageFolder = JPATH_ROOT . '/language/';
            }
        }else{
            $languageFolder = JPATH_ROOT . '/language/';
        }

        $path = $languageFolder . 'en-GB/en-GB.' . $item . '.ini';

        $registry->loadFile($path, 'INI');
        $enGbItems = $registry->toArray();

        if ($lang != 'en-GB')
        {
            $translatedRegistry = new JRegistry();
            $translatedPath = $languageFolder . $lang . '/' . $lang . '.' . $item . '.ini';
            if (JFile::exists($translatedPath))
            {
                $translatedRegistry->loadFile($translatedPath);
                $translatedLanguageItems = $translatedRegistry->toArray();
                //Remove unused language items
                $enGbKeys = array_keys($enGbItems);
                $changed = false;
                foreach ($translatedLanguageItems as $key => $value)
                {
                    if (!in_array($key, $enGbKeys))
                    {
                        unset($translatedLanguageItems[$key]);
                        $changed = true;
                    }
                }
                if ($changed)
                {
                    $translatedRegistry = new JRegistry();
                    $translatedRegistry->loadArray($translatedLanguageItems);
                }
            }
            else
            {
                $translatedLanguageItems = array();
            }
            $translatedLanguageKeys = array_keys($translatedLanguageItems);
            foreach ($enGbItems as $key => $value)
            {
                if (!in_array($key, $translatedLanguageKeys))
                {
                    $translatedRegistry->set($key, $value);
                    $changed = true;
                }
            }
            JFile::write($translatedPath, $translatedRegistry->toString('INI'));
        }

        if ($search)
        {
            $search = strtolower($search);
            foreach ($enGbItems as $key => $value)
            {
                if (strpos(strtolower($key), $search) === false && strpos(strtolower($value), $search) === false)
                {
                    unset($enGbItems[$key]);
                }
            }
        }
        //self::$_total = count($enGbItems);
        $data['en-GB'][$item] = array_slice($enGbItems, $limitstart,$limit);

        if ($lang != 'en-GB')
        {
            $path = $languageFolder . $lang . '/' . $lang . '.' . $item . '.ini';

            if (JFile::exists($path))
            {
                $registry->loadFile($path);
                $languageItems = $registry->toArray();
                //$data[$language][$languageFile] = array_slice($languageItems, $limitstart, $limit);
                $translatedItems = array();
                foreach ($data['en-GB'][$item] as $key => $value)
                {
                    $translatedItems[$key] = isset($languageItems[$key]) ? $languageItems[$key] : '';
                }
                $data[$lang][$item] = $translatedItems;
            }
            else
            {
                $data[$lang][$item] = array();
            }
        }

		return $data;
	}

	/**
	 * Get site languages
	 */
	function getSiteLanguages()
	{
		$path    = JPATH_ROOT . '/language';
		$folders = JFolder::folders($path);
		$rets    = array();
		foreach ($folders as $folder)
		{
			if ($folder != 'pdf_fonts')
			{
				$rets[] = $folder;
			}
		}

		return $rets;
	}

	/**
	 * Save translation data
	 *
	 * @param array $data
	 */
	function save($data)
	{
		$language				= $data['lang'];
		$languageFile			= 'com_edocman';
		$site					= $data['site'];
		
		if($languageFile == "com_edocman"){
			if ($site == 1)
			{
				$languageFolder = JPATH_ROOT . '/administrator/language/';
			}
			else
			{
				$languageFolder = JPATH_ROOT . '/language/';
			}
		}else{
			$languageFolder = JPATH_ROOT . '/language/';
		}
		$registry = new JRegistry();
		$filePath = $languageFolder . $language . '/' . $language . '.' . $languageFile . '.ini';
		if (JFile::exists($filePath))
		{
			$registry->loadFile($filePath, 'INI');
		}
		else
		{
			$registry->loadFile($languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini', 'INI');
		}
		//Get the current language file and store it to array
		$keys = $data['keys'];
		$items = $data['items'];
		$content = "";
		foreach ($items as $item)
		{
			$item = trim($item);
			$value = trim($data['item_'.$item]);
			$registry->set($keys[$item], $value);
		}
		if (isset($data['extra_keys']))
		{
			$keys = $data['extra_keys'];
			$values = $data['extra_values'];
			for ($i = 0, $n = count($keys); $i < $n; $i++)
			{
				$key = trim($keys[$i]);
				$value = trim($values[$i]);
				$registry->set($key, $value);
			}
		}
		
		if ($language != 'en-GB')
		{
			//We need to add new language items which are not existing in the current language
			$enRegistry = new JRegistry();
			$enRegistry->loadFile($languageFolder . 'en-GB/en-GB.' . $languageFile . '.ini', 'INI');
			$enLanguageItems = $enRegistry->toArray();
			$currentLanguageItems = $registry->toArray();
			foreach ($enLanguageItems as $key => $value)
			{
				$currentLanguageKeys = array_keys($currentLanguageItems);
				if (!in_array($key, $currentLanguageKeys))
				{					
					$registry->set($key, $value);
				}
			}
		}
		JFile::write($filePath, $registry->toString('INI'));

		return true;
	}
}