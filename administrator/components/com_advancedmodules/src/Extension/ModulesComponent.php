<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\AdvancedModules\Administrator\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use Psr\Container\ContainerInterface;
use RegularLabs\Component\AdvancedModules\Administrator\Service\HTML\AdvancedModules;
use RegularLabs\Library\Input as RL_Input;

/**
 * Component class for com_advancedmodules
 */
class ModulesComponent extends MVCComponent implements BootableExtensionInterface
{
    use HTMLRegistryAwareTrait;

    /**
     * Booting the extension. This is the function to set up the environment of the extension like
     * registering new class loaders, etc.
     *
     * If required, some initial set up can be done from services of the container, eg.
     * registering HTML services.
     *
     * @param ContainerInterface $container The container
     *
     * @return  void
     */
    public function boot(ContainerInterface $container)
    {
        if (RL_Input::getCmd('option') === 'com_associations')
        {
            return;
        }

        $this->getRegistry()->register('advancedmodules', new AdvancedModules);
    }
}
