<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die;

class EDocmanModelDownloadlog extends OSModel
{
	/**
	 * Empty the statistic table
	 *
	 * @return bool
	 */
	public function delete()
	{
		$db = $this->getDbo();
		$db->truncateTable('#__edocman_statistics');

		return true;
	}
}