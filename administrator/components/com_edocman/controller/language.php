<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access
defined('_JEXEC') or die();

class EDocmanControllerLanguage extends OSController
{
	/**
	 * Method to save language translation
	 */
	public function save()
	{
		$model				= $this->getModel('language');
		$data				= $this->input->getData();
		$model->save($data);
		$lang				= $data['lang'];
		$item				= $data['item'];
		$limitstart			= $data['limitstart'];
		$limit				= $data['limit'];
		$search				= $data['search'];
		$site				= $data['site'];
		$url				= 'index.php?option=com_edocman&view=language&lang=' . $lang . '&item=' . $item .'&limitstart='.$limitstart.'&limit=100&site='.$site.'&search='.$search;
		$msg				= JText::_('Traslation saved');
		$this->setRedirect($url, $msg);
	}

	/**
	 * Cancel editing language items, redirect user back to default view of the component
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_edocman&view=' . $this->defaultView);
	}
}
