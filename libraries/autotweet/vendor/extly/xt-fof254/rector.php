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

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Php71\Rector\FuncCall\RemoveExtraParametersRector;

return RectorConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
    ])
    ->withPhpSets(php74: true)
    ->withDowngradeSets(php74: true)
    ->withPreparedSets(
        codeQuality: true,
        codingStyle: true,
        // earlyReturn: true,
        instanceOf: true,
        naming: true,
        symfonyCodeQuality: true,
    )->withSkip([
        RemoveExtraParametersRector::class,
    ])
    ->withTypeCoverageLevel(0);
