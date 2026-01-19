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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Visitor;

defined('_JEXEC') or die;

use RegularLabs\Component\Conditions\Administrator\Condition\Condition;

class Ip extends Condition
{
    public function pass(): bool
    {
        if (is_array($this->selection))
        {
            $this->selection = implode(',', $this->selection);
        }

        $this->selection = $this->getIPsFromString($this->selection);

        return $this->checkIPList();
    }

    private function checkIP(?string $range): bool
    {
        if (empty($range))
        {
            return false;
        }

        if (str_contains($range, '-'))
        {
            // Selection is an IP range
            return $this->checkIPRange($range);
        }

        // Selection is a single IP (part)
        return $this->checkIPPart($range);
    }

    private function checkIPList(): bool
    {
        foreach ($this->selection as $range)
        {
            // Check next range if this one doesn't match
            if ( ! $this->checkIP($range))
            {
                continue;
            }

            // Match found, so return true!
            return true;
        }

        // No matches found, so return false
        return false;
    }

    private function checkIPPart(string $range): bool
    {
        $ip = $this->getIP();

        // Return if no IP address can be found (shouldn't happen, but who knows)
        if (empty($ip))
        {
            return false;
        }

        $ip_parts    = explode('.', $ip);
        $range_parts = explode('.', trim($range));

        // Trim the IP to the part length of the range
        $ip = implode('.', array_slice($ip_parts, 0, count($range_parts)));

        // Return false if ip does not match the range
        if ($range != $ip)
        {
            return false;
        }

        return true;
    }

    private function checkIPRange(string $range): bool
    {
        $ip = $this->getIP();

        // Return if no IP address can be found (shouldn't happen, but who knows)
        if (empty($ip))
        {
            return false;
        }

        // check if IP is between or equal to the from and to IP range
        [$min, $max] = explode('-', trim($range), 2);

        // Return false if IP is smaller than the range start
        if ($ip < trim($min))
        {
            return false;
        }

        $max = $this->fillMaxRange($max, $min);

        // Return false if IP is larger than the range end
        if ($ip > trim($max))
        {
            return false;
        }

        return true;
    }

    /* Fill the max range by prefixing it with the missing parts from the min range
     * So 101.102.103.104-201.202 becomes:
     * max: 101.102.201.202
     */

    private function fillMaxRange(string $max, string $min): string
    {
        $max_parts = explode('.', $max);

        if (count($max_parts) == 4)
        {
            return $max;
        }

        $min_parts = explode('.', $min);

        $prefix = array_slice($min_parts, 0, count($min_parts) - count($max_parts));

        return implode('.', $prefix) . '.' . implode('.', $max_parts);
    }

    private function getIP(): string
    {
        if ( ! empty($_SERVER['HTTP_CF_CONNECTING_IP']) && $this->isValidIp($_SERVER['HTTP_CF_CONNECTING_IP']))
        {
            return $_SERVER['HTTP_CF_CONNECTING_IP'];
        }

        if ( ! empty($_SERVER['HTTP_TRUE_CLIENT_IP']) && $this->isValidIp($_SERVER['HTTP_TRUE_CLIENT_IP']))
        {
            return $_SERVER['HTTP_TRUE_CLIENT_IP'];
        }

        if ( ! empty($_SERVER['HTTP_X_FORWARDED_FOR']) && $this->isValidIp($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        if ( ! empty($_SERVER['HTTP_X_REAL_IP']) && $this->isValidIp($_SERVER['HTTP_X_REAL_IP']))
        {
            return $_SERVER['HTTP_X_REAL_IP'];
        }

        if ( ! empty($_SERVER['HTTP_CLIENT_IP']) && $this->isValidIp($_SERVER['HTTP_CLIENT_IP']))
        {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    private function getIPsFromString(?string $string): array
    {
        if (is_array($string))
        {
            $this->selection = implode('\n', $this->selection);
        }

        $string = str_replace("\r", "\n", $string);
        $string = preg_replace('#\s*(\#|\/\/).*?(\n|$)#', "\n", $string);
        $string = str_replace(',', "\n", $string);
        $string = preg_replace('#\n\n+#', "\n", $string);
        $string = trim($string);

        return explode("\n", $string);
    }

    private function isValidIp(string $string): bool
    {
        return preg_match('#^([0-9]{1,3}\.){3}[0-9]{1,3}$#', $string);
    }
}
