<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  System.ruleengine
 *
 * @copyright   Copyright (C) 2025. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Cross-version compatibility aliases for Joomla 3-5
if (!class_exists('JFactory')) {
    if (class_exists('Joomla\CMS\Factory')) {
        class_alias('Joomla\CMS\Factory', 'JFactory');
    }
}
if (!class_exists('JUserHelper')) {
    if (class_exists('Joomla\CMS\User\UserHelper')) {
        class_alias('Joomla\CMS\User\UserHelper', 'JUserHelper');
    }
}
if (!class_exists('JDate')) {
    if (class_exists('Joomla\CMS\Date\Date')) {
        class_alias('Joomla\CMS\Date\Date', 'JDate');
    }
}
if (!class_exists('JLoader')) {
    if (class_exists('Joomla\CMS\Loader\Loader')) {
        class_alias('Joomla\CMS\Loader\Loader', 'JLoader');
    }
}
if (!class_exists('JComponentHelper')) {
    if (class_exists('Joomla\CMS\Component\ComponentHelper')) {
        class_alias('Joomla\CMS\Component\ComponentHelper', 'JComponentHelper');
    }
}

/**
 * Helper class for Rule Engine plugin
 */
class PlgSystemRuleengineHelper
{
    /** @var Registry */
    protected $params;

    public function __construct($params)
    {
        $this->params = $params;
    }

    /**
     * Write a log message for debugging the rule engine helper.
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
            $line = '[' . $timestamp . '] [helper] ' . $message . PHP_EOL;
            @file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            // Never interrupt main flow due to logging
        }
    }

    /**
     * Checks if the user meets all the conditions for a given rule.
     *
     * @param   object   $rule   The rule object from the plugin parameters.
     * @param   array    $user   The user data array from the onUserAfterSave event.
     * @param   boolean  $isNew  Whether the user is new.
     *
     * @return  boolean  True if all conditions are met, false otherwise.
     */
    public function checkConditions($rule, $user, $isNew)
    {
        $this->logMessage('checkConditions: userId=' . (isset($user['id']) ? (int)$user['id'] : 0) . ', isNew=' . ($isNew ? '1' : '0'));
        // Handle conditions structure - could be stdClass or array
        $conditions = [];
        if (is_object($rule->conditions)) {
            // Convert stdClass to array for easier processing
            foreach (get_object_vars($rule->conditions) as $key => $condition) {
                if (strpos($key, 'conditions') === 0 && is_object($condition)) {
                    $conditions[] = $condition;
                }
            }
        } elseif (is_array($rule->conditions)) {
            $conditions = $rule->conditions;
        }
        
        if (empty($conditions)) {
            $this->logMessage('checkConditions: no conditions; treat as passed');
            return true; // No conditions means always execute
        }
        
        // All conditions must be met (AND logic)
        foreach ($conditions as $condition) {
            if (!$this->checkSingleCondition($condition, $user, $isNew)) {
                $this->logMessage('checkConditions: a condition failed for userId=' . (isset($user['id']) ? (int)$user['id'] : 0));
                return false; // If any condition fails, the rule fails
            }
        }

        $this->logMessage('checkConditions: all conditions passed');
        return true; // All conditions passed
    }
    
    /**
     * Checks a single condition.
     *
     * @param   object   $condition The condition object.
     * @param   array    $user      The user data array.
     * @param   boolean  $isNew     Whether the user is new.
     *
     * @return  boolean  True if the condition is met, false otherwise.
     */
    private function checkSingleCondition($condition, $user, $isNew)
    {
        // Use JFactory for cross-version compatibility
        $userObject = JFactory::getUser($user['id']);

        switch ($condition->condition_type) {
            case 'user_group':
                $groupId = (int) $condition->condition_user_group_id;
                
                // Get user's current groups
                if (property_exists($userObject, 'groups') && is_array($userObject->groups)) {
                    $currentGroups = $userObject->groups;
                } else {
                    $currentGroups = JUserHelper::getUserGroups($userObject->id);
                }
                
                $result = in_array($groupId, $currentGroups);
                $this->logMessage('checkSingleCondition[user_group]: userId=' . (int)$userObject->id . ', groupId=' . $groupId . ', inGroup=' . ($result ? '1' : '0'));
                return $result;

            case 'reg_date_between':
                $regDate = new JDate($userObject->registerDate);
                $startDate = new JDate($condition->condition_reg_date_start);
                $endDate = new JDate($condition->condition_reg_date_end);
                // Set time to end of day for correct comparison
                $endDate->modify('+1 day -1 second');
                $ok = ($regDate >= $startDate && $regDate <= $endDate);
                $this->logMessage('checkSingleCondition[reg_date_between]: userId=' . (int)$userObject->id . ', regDate=' . $regDate->toSql() . ', start=' . $startDate->toSql() . ', end=' . $endDate->toSql() . ', match=' . ($ok ? '1' : '0'));
                return $ok;

            case 'com_fields':
                if (!JComponentHelper::isEnabled('com_fields')) {
                    $this->logMessage('checkSingleCondition[com_fields]: com_fields disabled');
                    return false;
                }

                $fieldName = $condition->condition_field_name;
                $expectedValue = $condition->condition_field_value;

                if (empty($fieldName)) {
                    $this->logMessage('checkSingleCondition[com_fields]: empty field name');
                    return false;
                }

                // First, check if we have the new custom field values in the user data
                if (isset($user['com_fields']) && is_array($user['com_fields']) && array_key_exists($fieldName, $user['com_fields'])) {
                    $fieldValue = $user['com_fields'][$fieldName];
                    $this->logMessage('checkSingleCondition[com_fields]: user data has field=' . $fieldName . ', expected=' . (string)$expectedValue . ', actual=' . (is_array($fieldValue) ? json_encode($fieldValue) : (string)$fieldValue));

                    // Handle array values (like checkboxes, multiple select)
                    if (is_array($fieldValue)) {
                        // For array values, check if any value matches the expected value
                        foreach ($fieldValue as $value) {
                            if (strtolower(trim($value)) === strtolower(trim($expectedValue))) {
                                $this->logMessage('checkSingleCondition[com_fields]: array value matched');
                                return true;
                            }
                        }
                        $this->logMessage('checkSingleCondition[com_fields]: array value did not match');
                        return false;
                    } else {
                        // Direct comparison for single values (case-insensitive for better matching)
                        $match = (strtolower(trim($fieldValue)) === strtolower(trim($expectedValue)));
                        $this->logMessage('checkSingleCondition[com_fields]: single value match=' . ($match ? '1' : '0'));
                        return $match;
                    }
                }

                // If new values not available, fall back to database query (for backward compatibility)
                try {
                    $db = JFactory::getDbo();
                    $query = $db->getQuery(true);
                    
                    // First get the field ID from the fields table
                    $query->select('f.id')
                          ->from('#__fields AS f')
                          ->where('f.name = ' . $db->quote($fieldName))
                          ->where('f.context = ' . $db->quote('com_users.user'))
                          ->where('f.state = 1');
                    
                    $db->setQuery($query);
                    $fieldId = $db->loadResult();
                    
                    if ($fieldId) {
                        // Now get the field value for this user
                        $query = $db->getQuery(true);
                        $query->select('fv.value')
                              ->from('#__fields_values AS fv')
                              ->where('fv.field_id = ' . (int)$fieldId)
                              ->where('fv.item_id = ' . (int)$userObject->id);
                        
                        $db->setQuery($query);
                        $fieldValue = $db->loadResult();
                        
                        if ($fieldValue !== null) {
                            // Direct comparison (case-insensitive for better matching)
                            $match = (strtolower(trim($fieldValue)) === strtolower(trim($expectedValue)));
                            $this->logMessage('checkSingleCondition[com_fields][db]: field=' . $fieldName . ', expected=' . (string)$expectedValue . ', actual=' . (string)$fieldValue . ', match=' . ($match ? '1' : '0'));
                            return $match;
                        }
                        $this->logMessage('checkSingleCondition[com_fields][db]: no value for userId=' . (int)$userObject->id . ', fieldId=' . (int)$fieldId);
                    }
                    
                } catch (Exception $e) {
                    $this->logMessage('checkSingleCondition[com_fields][db]: exception ' . $e->getMessage());
                    return false;
                }
                return false; // Field not found
        }

        return false; // Unknown condition type
    }

    /**
     * Executes the action defined in a rule.
     *
     * @param   object  $rule  The rule object from the plugin parameters.
     * @param   array   $user  The user data array from the onUserAfterSave event.
     *
     * @return  void
     */
    public function executeAction($rule, $user)
    {
        $userId = $user['id'];

        switch ($rule->action_type) {
            case 'assign_group':
                $groupId = (int) $rule->action_group_id;
                if ($groupId > 0) {
                    try {
                        $userObject = JFactory::getUser($userId);
                        // Get user's current groups
                        if (property_exists($userObject, 'groups') && is_array($userObject->groups)) {
                            $currentGroups = $userObject->groups;
                        } else {
                            $currentGroups = JUserHelper::getUserGroups($userId);
                        }

                        // Only add if not already present
                        if (!in_array($groupId, $currentGroups)) {
                            JUserHelper::addUserToGroup($userId, $groupId);
                            $this->logMessage('executeAction[assign_group]: added userId=' . $userId . ' to groupId=' . $groupId);
                        } else {
                            $this->logMessage('executeAction[assign_group]: userId=' . $userId . ' already in groupId=' . $groupId);
                        }
                    } catch (Exception $e) {
                        // Ignore errors to avoid breaking user save
                        $this->logMessage('executeAction[assign_group]: exception ' . $e->getMessage());
                    }
                }
                break;

            case 'assign_course':
                // Integrate with com_tjlms to enroll/assign user to a course
                $courseId = (int) $rule->action_course_id;
                $dueDays = isset($rule->action_course_due_days) ? (int) $rule->action_course_due_days : null;

                if ($courseId > 0) {
                    try {
                        // Load Tjlms enrolment model
                        JLoader::register('TjlmsModelEnrolment', JPATH_SITE . '/components/com_tjlms/models/enrolment.php');
                        if (class_exists('TjlmsModelEnrolment')) {
                            $enrolModel = new TjlmsModelEnrolment(array('ignore_request' => true));

                            // Check if already enrolled/assigned
                            $alreadyEnrolled = null;
                            if (method_exists($enrolModel, 'checkUserEnrollment')) {
                                $alreadyEnrolled = (int) $enrolModel->checkUserEnrollment($courseId, $userId);
                            } elseif (method_exists($enrolModel, 'checkIfUserEnrolled')) {
                                $alreadyEnrolled = (int) $enrolModel->checkIfUserEnrolled($userId, $courseId);
                            }

                            if (!empty($alreadyEnrolled)) {
                                $this->logMessage('executeAction[assign_course]: userId=' . $userId . ' already enrolled for courseId=' . $courseId);
                                break; // Skip duplicate
                            }

                            $data = array(
                                'user_id'   => (int) $userId,
                                'course_id' => (int) $courseId,
                                'state'     => 1,
                            );

                            if ($dueDays !== null) {
                                $dueDate = new JDate('now');
                                $dueDate->modify('+' . (int) $dueDays . ' days');
                                $data['due_date'] = $dueDate->toSql();
                                $enrolModel->userAssignment($data);
                                $this->logMessage('executeAction[assign_course]: assigned userId=' . $userId . ' to courseId=' . $courseId . ' with dueDays=' . (int)$dueDays);
                            } else {
                                $enrolModel->userEnrollment($data);
                                $this->logMessage('executeAction[assign_course]: enrolled userId=' . $userId . ' to courseId=' . $courseId);
                            }
                        }
                    } catch (Exception $e) {
                        // Swallow errors to avoid breaking user save
                        $this->logMessage('executeAction[assign_course]: exception ' . $e->getMessage());
                    }
                }
                break;
        }
    }
}


