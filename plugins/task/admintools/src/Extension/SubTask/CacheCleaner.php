<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2025 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\Task\AdminTools\Extension\SubTask;

defined('_JEXEC') or die;

use Akeeba\Component\AdminTools\Administrator\Mixin\RunPluginsTrait;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Cache\Administrator\Model\CacheModel;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Task\Task;
use Joomla\Event\Event;

/**
 * Clean up the Joomla cache
 *
 * @since  7.1.2
 */
trait CacheCleaner
{
	use RunPluginsTrait;

	/**
	 * Clean up the Joomla cache
	 *
	 * @param   ExecuteTaskEvent  $event  The scheduled task event we are handling
	 *
	 * @return  int
	 * @since   7.1.2
	 */
	private function cacheCleaner(ExecuteTaskEvent $event): int
	{
		$this->loadAdminToolsLanguage();

		// Get some basic information about the task at hand.
		/** @var Task $task */
		$task      = $event->getArgument('subject');
		$params    = $event->getArgument('params') ?: (new \stdClass());
		$cleanType = $params->cleantype ?? 'clean';

		/** @var MVCFactoryInterface $factory */
		$factory = $this->getApplication()->bootComponent('com_cache')->getMvcFactory();
		/** @var CacheModel $model */
		$model  = $factory->createModel('Cache', 'Administrator', ['ignore_request' => true]);
		$mCache = $model->getCache();

		switch ($cleanType)
		{
			// Delete all cache
			case 'clean':
				foreach ($mCache->getAll() as $cache)
				{
					$mCache->clean($cache->group);
				}

				$this->triggerPluginEvent('onAfterPurge', [], null, $this->getApplication());

				break;

			case 'expire':
				$model->purge();
				break;
		}

		return Status::OK;
	}

}