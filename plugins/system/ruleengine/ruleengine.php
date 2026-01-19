<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.ruleengine
 *
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Cross-version compatibility aliases
if (!class_exists('JPlugin')) {
    if (class_exists('Joomla\CMS\Plugin\CMSPlugin')) {
        class_alias('Joomla\CMS\Plugin\CMSPlugin', 'JPlugin');
    }
}

/**
 * Rule Engine Plugin
 * Compatible with Joomla 3.x, 4.x, and 5.x
 */
class PlgSystemRuleengine extends JPlugin
{
    /**
     * Autoload language strings
     *
     * @var bool
     */
    protected $autoloadLanguage = true;

    /**
     * The helper class for this plugin
     *
     * @var PlgSystemRuleengineHelper
     */
    protected $helper;

    /**
     * Constructor
     *
     * @param   object  &$subject  The object to observe
     * @param   array   $config    An optional associative array of configuration settings.
     */
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);

        try {
            // Include the helper file
            require_once __DIR__ . '/helper.php';
            $this->helper = new PlgSystemRuleengineHelper($this->params);
        } catch (Exception $e) {
            // If helper creation fails, disable the plugin functionality
            $this->helper = null;
        }
    }

    /**
     * Write a log message for debugging the rule engine plugin.
     * Controlled by plugin param `enable_logging` (default: on).
     */
    private function logMessage($message)
    {
        try {
            $shouldLog = 1;
            if (is_object($this->params) && method_exists($this->params, 'get')) {
                $shouldLog = (int) $this->params->get('enable_logging', 1);
            }
            if (!$shouldLog) {
                return;
            }

            $logFile = JPATH_ROOT . '/administrator/logs/ruleengine.log';
            $timestamp = date('Y-m-d H:i:s');
            $line = '[' . $timestamp . '] [plugin] ' . $message . PHP_EOL;
            @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            // Never interrupt main flow due to logging
        }
    }

    /**
     * Called after user data is stored in the database.
     *
     * @param   array    $user     Holds the new user data.
     * @param   boolean  $isnew    True if a new user is stored.
     * @param   boolean  $success  True if storing the user data was successful.
     * @param   string   $msg      Message.
     *
     * @return  void
     */
    public function onUserAfterSave($user, $isnew, $success, $msg)
    {
        if (!$success) {
            return;
        }

        if (!$this->helper) {
            return;
        }

        $this->logMessage('onUserAfterSave: userId=' . (isset($user['id']) ? (int)$user['id'] : 0) . ', isNew=' . ($isnew ? '1' : '0'));

        // Get the rules from the plugin parameters
        $rules = $this->params->get('rules');

        if (empty($rules)) {
            $this->logMessage('onUserAfterSave: no rules configured');
            return; // No rules configured
        }

        // Normalize rules to array of objects
        if (is_object($rules)) {
            $rules = (array) $rules;
        }

        // Execute configured actions for each rule (checking conditions first)
        foreach ($rules as $ruleKey => $rule) {
            if (!is_object($rule)) {
                continue;
            }

            // Check if all conditions for this rule are met
            $conditionsMet = $this->helper->checkConditions($rule, $user, $isnew);

            if ($conditionsMet) {
                // If conditions are met, execute the defined action
                $this->helper->executeAction($rule, $user);
                $this->logMessage('onUserAfterSave: rule[' . $ruleKey . '] conditions met; executed action=' . (isset($rule->action_type) ? $rule->action_type : 'unknown'));
            } else {
                $this->logMessage('onUserAfterSave: rule[' . $ruleKey . '] conditions NOT met');
            }
        }
    }
}


