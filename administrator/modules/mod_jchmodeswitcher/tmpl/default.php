<?php

use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

defined('_JEXEC') or die('Restricted Access');

/**
 * @var string $mode
 * @var string $url
 * @var string $pageCacheStatus
 * @var string $pageCachePluginTitle
 * @var string $task
 * @var string $statusClass
 * @var CMSApplication $app
 * @var Input $input
 */
$uri = Uri::getInstance();

// Load the Bootstrap Dropdown
HTMLHelper::_('bootstrap.dropdown', '.dropdown-toggle');

if ($input->getBool('hidemainmenu')) {
    return;
}

$options = [
    'version' => JCH_VERSION
];

$document = $app->getDocument();
$document->addStyleSheet(Uri::root(true) . '/media/mod_jchmodeswitcher/css/modeswitcher.css', $options);
$document->addScript(Uri::root(true) . '/media/com_jchoptimize/js/platform-joomla.js', $options);
$script = <<<JS

window.addEventListener('DOMContentLoaded', event => {
    const modeSwitcherButton = document.getElementById('jch-modeswitcher-toggle')
    if (modeSwitcherButton !== null){
        modeSwitcherButton.addEventListener('show.bs.dropdown', event => {
            jchPlatform.getCacheInfo();
        });
    }
});
JS;
$document->addScriptDeclaration($script);
?>
<div class="header-item-content dropdown header-profile jch-modeswitcher">
    <button id="jch-modeswitcher-toggle" class="dropdown-toggle d-flex align-items-center ps-0 py-0"
            data-bs-toggle="dropdown" type="button"
            title="<?php
            echo Text::_('MOD_JCHMODESWITCHER'); ?>">
        <div class="header-item-icon">
                <span id="mode-switcher-indicator"
                      class="fa-dot-circle fas d-flex notification-icon <?= $statusClass; ?>"
                      aria-hidden="true"></span>
        </div>
        <div class="header-item-text">
            <?= Text::_('MOD_JCHMODESWITCHER_TITLE'); ?>
        </div>
        <span class="icon-angle-down" aria-hidden="true"></span>
    </button>
    <div class="dropdown-menu dropdown-menu-end">
        <?php
        $route = 'index.php?option=com_jchoptimize&view=ModeSwitcher&task=' . $task . '&return=' . base64_encode(
            $uri
        ); ?>
        <a class="dropdown-item" href="<?= Route::_($route); ?>">
            <span class="icon-cog icon-fw" aria-hidden="true"></span>
            <?= Text::sprintf('MOD_JCHMODESWITCHER_MODE', $mode); ?>
        </a>
        <?php
        $route = 'index.php?option=com_jchoptimize&view=Utility&task=togglepagecache&return=' . base64_encode(
            $uri
        ); ?>
        <a class="dropdown-item" href="<?= Route::_($route); ?>">
            <span class="icon-archive icon-fw" aria-hidden="true"></span>
            <span id="page-cache-status">
                    <?= Text::sprintf('MOD_JCHMODESWITCHER_PAGECACHE_STATUS', $pageCacheStatus); ?>
                </span>
        </a>

        <div class="dropdown-header">
            <span class="icon-info-circle icon-fw" aria-hidden="true"></span>
            <?= Text::_('MOD_JCHMODESWITCHER_CACHE_INFO'); ?>
            <div class="ms-5"><em>
                    <span><?= Text::_('MOD_JCHMODESWITCHER_FILES'); ?></span> &nbsp;
                    <span class="numFiles-container"><img
                                src="<?= Uri::root(true) . '/media/com_jchoptimize/core/images/loader.gif'; ?>"</span>
                </em>
            </div>
            <div class="ms-5"><em>
                    <span><?= Text::_('MOD_JCHMODESWITCHER_SIZE') ?></span> &nbsp;
                    <span class="fileSize-container"><img
                                src="<?= Uri::root(true) . '/media/com_jchoptimize/core/images/loader.gif'; ?>"</span>
                </em>
            </div>
        </div>
        <div class="dropdown-header pt-0">
            <em><small>[<?= $pageCachePluginTitle ?>]</small></em>
        </div>
        <?php
        $route = 'index.php?option=com_jchoptimize&view=Utility&task=cleancache&return=' . base64_encode(
            $uri
        ); ?>
        <a class="dropdown-item" href="<?= Route::_($route); ?>">
            <span class="fa-trash-alt fas icon-fw" aria-hidden="true"></span>
            <?= Text::_('MOD_JCHMODESWITCHER_DELETE_CACHE'); ?>
        </a>
    </div>
</div>
