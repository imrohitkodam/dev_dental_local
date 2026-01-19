<?php
/**
 * @package    Plg_System_Dentaltjcertificate
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (C) 2009 - 2025 Techjoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Ensure the class file is loaded (for server compatibility - before use statement)
// Try multiple paths to ensure compatibility
$classFile = __DIR__ . '/../src/Extension/Dentaltjcertificate.php';
if (!file_exists($classFile))
{
    $classFile = JPATH_PLUGINS . '/system/dentaltjcertificate/src/Extension/Dentaltjcertificate.php';
}

if (file_exists($classFile) && !class_exists('Joomla\Plugin\System\Dentaltjcertificate\Extension\Dentaltjcertificate', false))
{
    require_once $classFile;
}

use Joomla\CMS\Extension\PluginInterface;
use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;
use Joomla\Plugin\System\Dentaltjcertificate\Extension\Dentaltjcertificate;

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->set(
            PluginInterface::class,
            function (Container $container) {
                $dispatcher = $container->get(DispatcherInterface::class);
                $pluginInfo = (array) PluginHelper::getPlugin('system', 'dentaltjcertificate');
                
                // Double check class exists before instantiating
                if (!class_exists('Joomla\Plugin\System\Dentaltjcertificate\Extension\Dentaltjcertificate'))
                {
                    $classFile = JPATH_PLUGINS . '/system/dentaltjcertificate/src/Extension/Dentaltjcertificate.php';
                    if (file_exists($classFile))
                    {
                        require_once $classFile;
                    }
                }
                
                $plugin = new Dentaltjcertificate($dispatcher, $pluginInfo);
                $plugin->setApplication(JFactory::getApplication());
                $plugin->setDispatcher($dispatcher);
                $plugin->registerListeners();

                return $plugin;
            }
        );
    }
};

