<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2025 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\Task\AdminTools\Extension\SubTask;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Model\CleantempdirectoryModel;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Factory\MVCFactoryServiceInterface;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Task\Task;

/**
 * Clean up the temporary directory
 *
 * @since  7.1.2
 */
trait TempDirCleaner
{
	/**
	 * Clean up the temporary directory
	 *
	 * @param   ExecuteTaskEvent  $event  The scheduled task event we are handling
	 *
	 * @return  int
	 * @since   7.1.2
	 */
	private function tempDirCleaner(ExecuteTaskEvent $event): int
	{
		$this->loadAdminToolsLanguage();

		// Get some basic information about the task at hand.
		/** @var Task $task */
		$task   = $event->getArgument('subject');
		$params = $event->getArgument('params') ?: (new \stdClass());

		if (!ComponentHelper::isEnabled('com_admintools'))
		{
			return Status::OK;
		}

		// Make sure Admin Tools is installed and enabled.
		$component = ComponentHelper::isEnabled('com_admintools')
			? $this->getApplication()->bootComponent('com_admintools')
			: null;

		if (!($component instanceof MVCFactoryServiceInterface))
		{
			throw new \RuntimeException('The Admin Tools component is not installed or has been disabled.');
		}

		$factory = $component->getMVCFactory();

		/** @var CleantempdirectoryModel $model */
		$model = $factory->createModel('Cleantempdirectory', 'Administrator', ['ignore_request' => true]);

		$schedulerParams = ComponentHelper::getParams('com_scheduler');
		$timeLimit       = intdiv(intval($schedulerParams->get('timeout', 300) ?: 300), 2);
		$phpTimeLimit    = function_exists('ini_get') ? ini_get('max_execution_time') : 0;
		$timeLimit       = ($phpTimeLimit > 0) ? min($timeLimit, $phpTimeLimit) : $timeLimit;
		$startTime       = microtime(true);
		$status          = $model->process();

		while (!$status && microtime(true) - $startTime < $timeLimit)
		{
			$status = $model->process();
		}

		return $status ? Status::OK : Status::WILL_RESUME;
	}
}