<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Administrator\Condition\Agent;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;
use RegularLabs\Library\MobileDetect;
use RegularLabs\Library\RegEx;

abstract class Agent extends Condition
{
    use HasArraySelection;

    private $agent;
    private $device;
    private $is_mobile = false;

    public function isDesktop(): bool
    {
        return $this->getDevice() == 'desktop';
    }

    public function isMobile(): bool
    {
        return $this->getDevice() == 'mobile';
    }

    public function isPhone(): bool
    {
        return $this->isMobile();
    }

    public function isTablet(): bool
    {
        return $this->getDevice() == 'tablet';
    }

    public function passBrowser(string $browser = ''): bool
    {
        if ( ! $browser)
        {
            return false;
        }

        if ($browser == 'mobile')
        {
            return $this->isMobile();
        }

        // also check for _ instead of .
        $browser = RegEx::replace('\\\.([^\]])', '[\._]\1', $browser);
        $browser = str_replace('\.]', '\._]', $browser);

        return RegEx::match($browser, $this->getAgent(), $match, 'i');
    }

    private function getAgent(): string
    {
        if ( ! is_null($this->agent))
        {
            return $this->agent;
        }

        $detect = new MobileDetect;
        $agent  = $detect->getUserAgent() ?? '';

        switch (true)
        {
            case (stripos($agent, 'Trident') !== false):
                // Add MSIE to IE11 and others missing it
                $agent = RegEx::replace('(Trident/[0-9\.]+;.*rv[: ]([0-9\.]+))', '\1 MSIE \2', $agent);
                break;

            case (stripos($agent, 'Chrome') !== false):
                // Remove Safari from Chrome
                $agent = RegEx::replace('(Chrome/.*)Safari/[0-9\.]*', '\1', $agent);
                // Add MSIE to IE Edge and remove Chrome from IE Edge
                $agent = RegEx::replace('Chrome/.*(Edge/[0-9])', 'MSIE \1', $agent);
                break;

            case (stripos($agent, 'Opera') !== false):
                $agent = RegEx::replace('(Opera/.*)Version/', '\1Opera/', $agent);
                break;

            default:
                break;
        }

        $this->agent = $agent;

        return $this->agent;
    }

    private function getDevice(): string
    {
        if ( ! is_null($this->device))
        {
            return $this->device;
        }

        $detect = new MobileDetect;

        $this->is_mobile = $detect->isMobile();

        switch (true)
        {
            case($detect->isTablet()):
                $this->device = 'tablet';
                break;

            case ($detect->isMobile()):
                $this->device = 'mobile';
                break;

            default:
                $this->device = 'desktop';
        }

        return $this->device;
    }
}
