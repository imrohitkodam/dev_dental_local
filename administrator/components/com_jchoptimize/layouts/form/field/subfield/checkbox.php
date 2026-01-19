<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 *  @package   jchoptimize/core
 *  @author    Samuel Marshall <samuel@jch-optimize.net>
 *  @copyright Copyright (c) 2025 Samuel Marshall / JCH Optimize
 *  @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

defined('_JEXEC') or die('Restricted access');

extract($displayData);

/**
 * @var string $subFieldClass
 * @var string $fieldName
 * @var int $i
 * @var string $option
 * @var array $v
 * @var string $class;
 */

?>

        <span class="<?= $subFieldClass ?>">
            <input type="checkbox" class="group<?= $i ?> <?= $class ?>" name="jform[<?= $fieldName ?>][<?= $i ?>][<?= $option; ?>]"
                <?= isset($v[$option]) ? 'checked' : '' ?>/>
        </span>
