<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Application\CMSApplication;
use Joomla\Event\Event;
use YOOtheme\EventDispatcher;

class Dispatcher extends EventDispatcher
{
    /**
     * @var CMSApplication
     */
    protected $joomla;

    public static $actions = [
        'onAfterCleanModuleList' => ['modules', 'subject'],
        'onBeforeCompileHead' => ['subject', 'document'],
        'onContentBeforeSave' => ['context', 'subject'],
        'onContentPrepare' => ['context', 'subject', 'params', 'page'],
        'onContentPrepareData' => ['context', 'data', 'subject'],
        'onContentPrepareForm' => ['subject', 'data'],
    ];

    /**
     * Constructor.
     *
     * @param CMSApplication $joomla
     */
    public function __construct($joomla)
    {
        parent::__construct();

        $this->joomla = $joomla;
    }

    /**
     * Adds an event listener.
     *
     * @param string   $event
     * @param callable $listener
     * @param int      $priority
     */
    public function addListener($event, $listener, $priority = 0)
    {
        if (version_compare(JVERSION, '4.0', '>=')) {
            return $this->joomla
                ->getDispatcher()
                ->addListener(
                    $event,
                    fn($event) => $listener($this->prepareArguments($event)),
                    $priority,
                );
        }

        if (empty($this->listeners[$event])) {
            if ($event === 'onAfterCleanModuleList') {
                $handler = fn(&$modules) => $this->dispatch(
                    $event,
                    new Event($event, ['modules' => &$modules]),
                );
            } else {
                $handler = function (...$arguments) use ($event) {
                    return $this->dispatch(
                        $event,
                        $this->prepareArguments(new Event($event, $arguments)),
                    );
                };
            }

            $this->joomla->registerEvent($event, $handler);
        }

        parent::addListener($event, $listener, $priority);
    }

    protected function prepareArguments($event)
    {
        foreach (static::$actions[$event->getName()] ?? [] as $i => $key) {
            if (!isset($event[$key])) {
                $event[$key] = $event[$i];
            }
        }

        return $event;
    }
}
