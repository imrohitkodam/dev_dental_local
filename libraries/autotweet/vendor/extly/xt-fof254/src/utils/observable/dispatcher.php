<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('XTF0F_INCLUDED') || exit;

/**
 * Class to handle dispatching of events.
 *
 * This is the Observable part of the Observer design pattern
 * for the event architecture.
 *
 * This class is based on JEventDispatcher as found in Joomla! 3.2.0
 */
class XTF0FUtilsObservableDispatcher extends XTF0FUtilsObject
{
    /**
     * An array of Observer objects to notify
     *
     * @var array
     */
    protected $_observers = [];

    /**
     * The state of the observable object
     */
    protected $_state = null;

    /**
     * A multi dimensional array of [function][] = key for observers
     *
     * @var array
     */
    protected $_methods = [];

    /**
     * Stores the singleton instance of the dispatcher.
     *
     * @var XTF0FUtilsObservableDispatcher
     */
    protected static $instance = null;

    /**
     * Returns the global Event Dispatcher object, only creating it
     * if it doesn't already exist.
     *
     * @return XTF0FUtilsObservableDispatcher the EventDispatcher object
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Get the state of the XTF0FUtilsObservableDispatcher object
     *
     * @return mixed the state of the object
     */
    public function getState()
    {
        return $this->_state;
    }

    /**
     * Registers an event handler to the event dispatcher
     *
     * @param string $event   Name of the event to register handler for
     * @param string $handler Name of the event handler
     *
     * @return void
     *
     * @throws InvalidArgumentException
     */
    public function register($event, $handler)
    {
        // Are we dealing with a class or callback type handler?
        if (is_callable($handler)) {
            // Ok, function type event handler... let's attach it.
            $method = ['event' => $event, 'handler' => $handler];
            $this->attach($method);
        } elseif (class_exists($handler)) {
            // Ok, class type event handler... let's instantiate and attach it.
            $this->attach(new $handler($this));
        } else {
            throw new InvalidArgumentException('Invalid event handler.');
        }
    }

    /**
     * Triggers an event by dispatching arguments to all observers that handle
     * the event and returning their return values.
     *
     * @param string $event the event to trigger
     * @param array  $args  an array of arguments
     *
     * @return array an array of results from each function call
     */
    public function trigger($event, $args = [])
    {
        $result = [];

        /*
         * If no arguments were passed, we still need to pass an empty array to
         * the call_user_func_array function.
         */
        $args = (array) $args;

        $event = strtolower($event);

        // Check if any plugins are attached to the event.
        if (!isset($this->_methods[$event]) || empty($this->_methods[$event])) {
            // No Plugins Associated To Event!
            return $result;
        }

        // Loop through all plugins having a method matching our event
        foreach ($this->_methods[$event] as $key) {
            // Check if the plugin is present.
            if (!isset($this->_observers[$key])) {
                continue;
            }

            // Fire the event for an object based observer.
            if (is_object($this->_observers[$key])) {
                $args['event'] = $event;
                $value = $this->_observers[$key]->update($args);
            }
            // Fire the event for a function based observer.
            elseif (is_array($this->_observers[$key])) {
                $value = call_user_func_array($this->_observers[$key]['handler'], $args);
            }

            if (isset($value)) {
                $result[] = $value;
            }
        }

        return $result;
    }

    /**
     * Attach an observer object
     *
     * @param object $observer An observer object to attach
     *
     * @return void
     */
    public function attach($observer)
    {
        if (is_array($observer)) {
            if (!isset($observer['handler']) || !isset($observer['event']) || !is_callable($observer['handler'])) {
                return;
            }

            // Make sure we haven't already attached this array as an observer
            foreach ($this->_observers as $_observer) {
                if (is_array($_observer) && $_observer['event'] == $observer['event'] && $_observer['handler'] == $observer['handler']) {
                    return;
                }
            }

            $this->_observers[] = $observer;
            end($this->_observers);
            $methods = [$observer['event']];
        } else {
            if (!($observer instanceof XTF0FUtilsObservableEvent)) {
                return;
            }

            // Make sure we haven't already attached this object as an observer
            $class = get_class($observer);

            foreach ($this->_observers as $_observer) {
                if ($_observer instanceof $class) {
                    return;
                }
            }

            $this->_observers[] = $observer;

            // Required in PHP 7 since foreach() doesn't advance the internal array counter, see http://php.net/manual/en/migration70.incompatible.php
            end($this->_observers);

            $methods = [];

            foreach (get_class_methods($observer) as $obs_method) {
                // Magic methods are not allowed
                if (0 === strpos('__', $obs_method)) {
                    continue;
                }

                $methods[] = $obs_method;
            }

            // $methods = get_class_methods($observer);
        }

        $key = key($this->_observers);

        foreach ($methods as $method) {
            $method = strtolower($method);

            if (!isset($this->_methods[$method])) {
                $this->_methods[$method] = [];
            }

            $this->_methods[$method][] = $key;
        }
    }

    /**
     * Detach an observer object
     *
     * @param object $observer an observer object to detach
     *
     * @return bool true if the observer object was detached
     */
    public function detach($observer)
    {
        $retval = false;

        $key = array_search($observer, $this->_observers, true);

        if (false !== $key) {
            unset($this->_observers[$key]);
            $retval = true;

            foreach ($this->_methods as &$_method) {
                $k = array_search($key, $_method, true);

                if (false !== $k) {
                    unset($_method[$k]);
                }
            }
        }

        return $retval;
    }
}
