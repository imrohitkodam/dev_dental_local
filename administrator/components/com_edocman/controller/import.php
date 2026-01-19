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
defined('_JEXEC') or die();

class EDocmanControllerImport extends EDocmanController
{
	/**
	 * Import documents from a specific folder
	 *
	 * @return bool|void
	 */
	public function save()
	{
		$model = $this->getModel('import');
		$data  = $this->input->post->getData();
		$model->store($data);
		$url = 'index.php?option=com_edocman&view=documents';
		$msg = JText::_('Documents imported');
		$this->setRedirect($url, $msg);
	}

	/**
	 * Cancel import action, redirect user to documents management screen
	 *
	 * @return bool|void
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_edocman&view=documents');
	}
}