<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

defined('_JEXEC') || exit;

/**
 * AutotweetModelAutomators.
 *
 * @since       1.0
 */
class AutotweetModelAutomators extends XTF0FModel
{
    /**
     * lastRunCheck.
     *
     * @param string $plugin   Param
     * @param int    $interval Param
     * @param JDate  $next     Param
     *
     * @return bool
     */
    public function lastRunCheck($plugin, $interval = 0, $next = null)
    {
        $jDate = $this->lastRun($plugin);

        $now = \Joomla\CMS\Factory::getDate();
        $diff = $now->toUnix() - $jDate->toUnix();

        $result = ($diff > $interval);

        if ($result) {
            if ($next) {
                $this->_updateLastRun($plugin, $next);
            } else {
                $this->_updateLastRun($plugin, $now);
            }
        }

        return $result;
    }

    /**
     * lastRunCheckFreqMhdmd.
     *
     * @param string $key        Param
     * @param string $freq_mhdmd Param
     *
     * @return bool
     */
    public function lastRunCheckFreqMhdmd($key, $freq_mhdmd)
    {
        $lastexec = $this->lastRun($key);

        if ($lastexec->toUnix() < \Joomla\CMS\Factory::getDate()->toUnix()) {
            $lastexec = 'now';
        }

        $next = TextUtil::nextScheduledDate($freq_mhdmd, $lastexec);

        $instance = AutotweetLogger::getInstance();
        $instance->log(\Joomla\CMS\Log\Log::INFO, sprintf('lastRunCheckFreqMhdmd: %s (%s, 0, %s)', $lastexec, $key, $next));

        return $this->lastRunCheck($key, 0, $next);
    }

    /**
     * lastRun.
     *
     * @param string $plugin Param
     *
     * @return JDate
     */
    public function lastRun($plugin)
    {
        $automat = $this->getTable();
        $automat->load(
            [
                'plugin' => $plugin,
            ]
        );

        if (($automat->id) && ($automat->plugin === $plugin)) {
            $this->getItem($automat->id);
            return \Joomla\CMS\Factory::getDate($automat->lastexec);
        }

        $this->reset();

        return \Joomla\CMS\Factory::getDate('1999-11-30 00:00:00');
    }

    /**
     * updateLastRun.
     *
     * @param string $plugin Param
     * @param JDate  $now    Param
     */
    private function _updateLastRun($plugin, $now)
    {
        if (!$now) {
            $now = \Joomla\CMS\Factory::getDate();
        }

        $this->save(
            [
                'id' => $this->id,
                'plugin' => $plugin,
                'lastexec' => $now->toSql(),
            ]
        );
    }
}
