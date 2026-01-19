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
 */

?>

<span class="<?= $subFieldClass ?>">
    <select name="jform[<?= $fieldName ?>][<?= $i ?>][<?= $option ?>]">
        <option value="West" <?= $v[$option] == 'West' ? 'selected' : '' ?> >Left</option>
        <option value="Center" <?= $v[$option] == 'Center' ? 'selected' : '' ?> >Center</option>
        <option value="East" <?= $v[$option] == 'East' ? 'selected' : '' ?>>Right</option>
    </select>
</span>
