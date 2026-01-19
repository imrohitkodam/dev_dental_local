<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Factory;
use Joomla\Event\DispatcherInterface;
use YOOtheme\Application\EventLoader;
use YOOtheme\Container;
use YOOtheme\EventDispatcher;

/**
 * @property EventDispatcher|DispatcherInterface $dispatcher
 */
class ActionLoader extends EventLoader
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        $joomla = Factory::getApplication();

        if (version_compare(JVERSION, '5.0', '<')) {
            $this->dispatcher = new Dispatcher($joomla);
        } else {
            $this->dispatcher = $joomla->getDispatcher();
        }
    }

    /**
     * Load action listeners.
     *
     * @param Container $container
     * @param array     $configs
     */
    public function __invoke(Container $container, array $configs)
    {
        if (!$container->has('dispatcher')) {
            $container->set('dispatcher', $this->dispatcher);
        }

        parent::__invoke($container, $configs);
    }
}
