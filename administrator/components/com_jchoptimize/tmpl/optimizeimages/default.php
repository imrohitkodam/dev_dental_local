<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2020 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\CMS\WebAsset\WebAssetManager;

defined('_JEXEC') or die('Restricted Access');

$page = JRoute::_(
    'index.php?option=com_jchoptimize&view=OptimizeImage&task=optimizeimage',
    false,
    JRoute::TLS_IGNORE,
    true
);

$aAutoOptimize = [
    'link' => '',
    'icon' => 'fa fa-crop',
    'name' => Text::_('COM_JCHOPTIMIZE_OPTIMIZE_IMAGES'),
    'script' => 'onclick="jchOptimizeImageApi.optimizeImages(\''
        . $page . '&mode=byUrls\', \'auto\'); return false;"',
    'id' => 'auto-optimize-images',
    'class' => [],
    'proonly' => true
];

$aManualOptimize = [
    'link' => '',
    'icon' => 'fa fa-crop-alt',
    'name' => Text::_('COM_JCHOPTIMIZE_OPTIMIZE_IMAGES'),
    'script' => 'onclick="jchOptimizeImageApi.optimizeImages(\''
        . $page . '&mode=byFolders\', \'manual\'); return false;"',
    'id' => 'manual-optimize-images',
    'class' => [],
    'proonly' => true

];

/** @var WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useStyle('com_jchoptimize.core.dashicons');

?>
<div class="container-fluid">
    <div class="row g-3">
        <div class="col-12 col-md-8">
            <div id="manual-optimize-block" class="bg-white p-4">
                <div id="optimize-images-container" class="">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-12 col-md-4">
                                <div id="file-tree-container" class=""></div>
                            </div>
                            <div class="col-12 col-md-8">
                                <div id="files-container" class=""></div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="fs-6">
                        <span class="fa fa-folder-open"></span>
                        <?= Text::_('COM_JCHOPTIMIZE_OPTIMIZE_IMAGES_BY_FOLDER'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <nav class="jch-dash-icons">
                        <ul class="nav flex-wrap">
                            <?= HTMLHelper::_('dashicons.button', $aManualOptimize); ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="fs-6">
                        <span class="fa fa-external-link-square-alt"></span>
                        <?= Text::_('COM_JCHOPTIMIZE_OPTIMIZE_IMAGES_BY_URLS'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <nav class="jch-dash-icons">
                        <ul class="nav flex-wrap">
                            <?= HTMLHelper::_('dashicons.button', $aAutoOptimize); ?>
                        </ul>
                    </nav>
                </div>

            </div>
            <div class="card mb-3">
                <div class="card-header">
                    <h2 class="fs-6">
                        <span class="fa fa-tools"></span>
                        <?= Text::_('COM_JCHOPTIMIZE_API2_UTILITY_SETTING'); ?>
                    </h2>
                </div>
                <div class="card-body">
                    <nav class="jch-dash-icons">
                        <ul class="nav flex-wrap">
                            <?= HTMLHelper::_('dashicons.buttons', $this->icons->compileUtilityIcons(
                                $this->icons->getApi2utilityArray()
                            )); ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<div id="optimize-images-modal-container" class="modal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Optimizing Images</h5>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>