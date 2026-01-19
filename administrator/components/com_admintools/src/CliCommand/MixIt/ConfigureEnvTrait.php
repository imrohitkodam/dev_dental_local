<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2025 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Component\AdminTools\Administrator\CliCommand\MixIt;

defined('_JEXEC') || die;


/**
 * Configures Joomla environment before running any command
 *
 * @deprecated  8.0 To be removed when Joomla 5 and earlier support is dropped
 */
trait ConfigureEnvTrait
{
	/**
	 * Configure the environment with the data required (ie define component constants).
	 *
	 * This is only required on Joomla 4 and 5 because some parts of the core code (e.g. FormBehaviorTrait) depend on
	 * these constants prior to Joomla 6.0. When support for these legacy versions of Joomla is dropped circa 2028 or
	 * 2029, we can remove this trait.
	 *
	 * @deprecated  8.0 To be removed when Joomla 5 and earlier support is dropped.
	 *
	 * @return  void
	 */
	private function configureEnv()
	{
		if (!defined('JPATH_COMPONENT'))
		{
			define('JPATH_COMPONENT', JPATH_ADMINISTRATOR . '/components/com_admintools');
		}

		// These constants are deprecated, but let's add them anyway
		if (!defined('JPATH_COMPONENT_SITE'))
		{
			define('JPATH_COMPONENT_SITE', JPATH_ROOT . '/components/com_admintools');
		}

		if (!defined('JPATH_COMPONENT_ADMINISTRATOR'))
		{
			define('JPATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_admintools');
		}
	}
}
