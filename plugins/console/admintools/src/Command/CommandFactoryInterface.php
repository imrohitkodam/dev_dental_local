<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2025 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Plugin\Console\AdminTools\Command;

defined('_JEXEC') || die;

use Joomla\Console\Command\AbstractCommand;

interface CommandFactoryInterface
{
	public function getCLICommand(string $commandName): AbstractCommand;
}