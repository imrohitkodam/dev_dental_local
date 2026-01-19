<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace CodeAlfa\Component\JchOptimize\Administrator\Joomla\Log;

use _JchOptimizeVendor\V91\Psr\Log\AbstractLogger;
use _JchOptimizeVendor\V91\Psr\Log\LogLevel;
use InvalidArgumentException;
use Joomla\CMS\Log\Log;

use function array_key_exists;
use function defined;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die('Restricted Access');
// phpcs:enable PSR1.Files.SideEffects

class JoomlaLogger extends AbstractLogger
{
    protected array $psrToJoomlaPriorityMap = [
        LogLevel::EMERGENCY => Log::EMERGENCY,
        LogLevel::ALERT     => Log::ALERT,
        LogLevel::CRITICAL  => Log::CRITICAL,
        LogLevel::ERROR     => Log::ERROR,
        LogLevel::WARNING   => Log::WARNING,
        LogLevel::NOTICE    => Log::NOTICE,
        LogLevel::INFO      => Log::INFO,
        LogLevel::DEBUG     => Log::DEBUG,
    ];

    protected string $category = 'com_jchoptimize';

    public function __construct()
    {
        Log::addLogger(
            [
                'text_file' => 'com_jchoptimize.logs.php'
            ],
            Log::ALL,
            [$this->category]
        );
    }

    public function log($level, $message, array $context = []): void
    {
        if (!array_key_exists($level, $this->psrToJoomlaPriorityMap)) {
            throw new InvalidArgumentException('An invalid log product has been given.');
        }

        $priority = $this->psrToJoomlaPriorityMap[$level];
        $date = @$context['date'] ?? null;

        Log::add((string) $message, $priority, $this->category, $date, $context);
    }
}
