<?php

/**
 * @version        1.0.0
 * @package        Joomla
 * @subpackage     Doc Indexer
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2010 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
class IndexerAdapter
{
	/**
	 * Get an indexer class
	 *
	 * @param string $type
	 */
	public static function getInstance($type)
	{
		static $instances;
		if (!isset($instances[$type]))
		{
			require_once dirname(__FILE__) . '/adapters/' . $type . '.php';
			$className        = ucfirst($type) . '2Text';
			$instances[$type] = new $className();
		}

		return $instances[$type];
	}

	/**
	 * Get text from selected documents
	 *
	 */
	public static function getText($fileName)
	{
		jimport('joomla.filesystem.file');
		$type = JString::strtolower(JFile::getExt($fileName));
		$doc  = IndexerAdapter::getInstance($type);
		return $doc->getText($fileName);
	}
}

?>