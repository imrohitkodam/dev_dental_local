<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2025 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\Console\AdminTools\Command;

defined('_JEXEC') || die;

use Joomla\Application\ApplicationInterface;
use Joomla\CMS\MVC\Factory\MVCFactoryAwareTrait;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;

class CommandFactory implements CommandFactoryInterface, DatabaseAwareInterface
{
	use MVCFactoryAwareTrait;
	use DatabaseAwareTrait;

	private ApplicationInterface $application;

	public function getCLICommand(string $commandName): AbstractCommand
	{
		$classFQN = 'Akeeba\\Component\\AdminTools\\Administrator\\CliCommand\\' . ucfirst($commandName);

		if (!class_exists($classFQN))
		{
			throw new \RuntimeException(sprintf('Unknown Admin Tools CLI command class ‘%s’.', $commandName));
		}

		$classParents = class_parents($classFQN);

		if (!in_array(AbstractCommand::class, $classParents))
		{
			throw new \RuntimeException(sprintf('Invalid Admin Tools CLI command object ‘%s’.', $commandName));
		}

		$o = new $classFQN;

		if (method_exists($classFQN, 'setMVCFactory'))
		{
			$o->setMVCFactory($this->getMVCFactory());
		}

		if ($o instanceof DatabaseAwareInterface)
		{
			$o->setDatabase($this->getDatabase());
		}

		if (method_exists($o, 'getApplication'))
		{
			$o->setApplication($this->getApplication());
		}

		return $o;
	}

	private function getApplication(): ApplicationInterface
	{
		return $this->application;
	}

	public function setApplication(ApplicationInterface $application): void
	{
		$this->application = $application;
	}
}