<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2025 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\Task\AdminTools\Extension\SubTask;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Model\JupdateModel;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;

/**
 * Automatically import Admin Tools configuration settings from a URL
 *
 * @since      7.1.2
 */
trait Jupdate
{
	/**
	 * Reset Joomla! Update
	 *
	 * @param   ExecuteTaskEvent  $event  The scheduled task event we are handling
	 *
	 * @return  int
	 * @since   7.1.2
	 */
	private function jupdate(ExecuteTaskEvent $event): int
	{
		$this->loadAdminToolsLanguage();

		/** @var MVCFactoryInterface $factory */
		$factory = $this->getApplication()->bootComponent('com_admintools')->getMVCFactory();
		/** @var JupdateModel $jupdateModel */
		$jupdateModel = $factory->createModel('Jupdate', 'Administrator', ['ignore_request' => true]);

		$jupdateModel->resetJoomlaUpdate();

		return Status::OK;
	}
}