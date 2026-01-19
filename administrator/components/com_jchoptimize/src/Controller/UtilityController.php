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

namespace CodeAlfa\Component\JchOptimize\Administrator\Controller;

use _JchOptimizeVendor\V91\GuzzleHttp\Psr7\UploadedFile;
use CodeAlfa\Component\JchOptimize\Administrator\Extension\MVCFactoryDecoratorAwareInterface;
use CodeAlfa\Component\JchOptimize\Administrator\Extension\MVCFactoryDecoratorAwareTrait;
use CodeAlfa\Component\JchOptimize\Administrator\Model\ModeSwitcherModel;
use CodeAlfa\Component\JchOptimize\Administrator\Model\ReCacheModel;
use Exception;
use JchOptimize\Core\Admin\AdminTasks;
use JchOptimize\Core\Model\CacheMaintainer;
use JchOptimize\Core\Platform\PathsInterface;
use JchOptimize\Core\Platform\PluginInterface;
use JchOptimize\Core\Registry;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\Route;
use Joomla\Filesystem\File;
use Joomla\Input\Input;
use Joomla\Uri\UriInterface;

use function base64_decode;
use function defined;
use function ob_clean;

use const UPLOAD_ERR_CANT_WRITE;
use const UPLOAD_ERR_EXTENSION;
use const UPLOAD_ERR_FORM_SIZE;
use const UPLOAD_ERR_INI_SIZE;
use const UPLOAD_ERR_NO_FILE;
use const UPLOAD_ERR_NO_TMP_DIR;
use const UPLOAD_ERR_OK;
use const UPLOAD_ERR_PARTIAL;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die('Restricted Access');
// phpcs:enable PSR1.Files.SideEffects

class UtilityController extends BaseController implements MVCFactoryDecoratorAwareInterface
{
    use MVCFactoryDecoratorAwareTrait;

    private CacheMaintainer $cacheMaintainer;

    private Registry $params;

    private PluginInterface $pluginUtils;

    private PathsInterface $pathsUtils;

    private AdminTasks $tasks;

    public function __construct(
        $config = [],
        ?MVCFactoryInterface $factory = null,
        ?CMSApplicationInterface $app = null,
        ?Input $input = null
    ) {
        parent::__construct($config, $factory, $app, $input);

        $return = $this->input->get('return', '', 'base64');

        if ($return) {
            $redirectUrl = Route::_(base64_decode($return));
        } else {
            $redirectUrl = Route::_('index.php?option=com_jchoptimize', false);
        }

        $this->setRedirect($redirectUrl);
    }

    public function setCacheMaintainer(CacheMaintainer $cacheMaintainer): void
    {
        $this->cacheMaintainer = $cacheMaintainer;
    }

    public function setParams(Registry $params): void
    {
        $this->params = $params;
    }

    public function setPluginUtils(PluginInterface $pluginUtils): void
    {
        $this->pluginUtils = $pluginUtils;
    }

    public function setPathsUtils(PathsInterface $pathsUtils): void
    {
        $this->pathsUtils = $pathsUtils;
    }

    public function setTasks(AdminTasks $tasks): void
    {
        $this->tasks = $tasks;
    }

    public function browsercaching(): void
    {
        $success = null;

        $expires = $this->tasks->leverageBrowserCaching();

        if ($success === false) {
            $this->message = Text::_('COM_JCHOPTIMIZE_LEVERAGEBROWSERCACHE_FAILED');
            $this->messageType = 'error';
        } elseif ($expires === 'FILEDOESNTEXIST') {
            $this->message = Text::_('COM_JCHOPTIMIZE_LEVERAGEBROWSERCACHE_FILEDOESNTEXIST');
            $this->messageType = 'warning';
        } elseif ($expires === 'CODEUPDATEDSUCCESS') {
            $this->message = Text::_('COM_JCHOPTIMIZE_LEVERAGEBROWSERCACHE_CODEUPDATEDSUCCESS');
        } elseif ($expires === 'CODEUPDATEDFAIL') {
            $this->message = Text::_('COM_JCHOPTIMIZE_LEVERAGEBROWSERCACHE_CODEUPDATEDFAIL');
            $this->messageType = 'notice';
        } else {
            $this->message = Text::_('COM_JCHOPTIMIZE_LEVERAGEBROWSERCACHE_SUCCESS');
        }

        $this->redirect();
    }

    public function cleancache(): void
    {
        $deleted = $this->cacheMaintainer->cleanCache();

        if (!$deleted) {
            $this->message = Text::_('COM_JCHOPTIMIZE_CACHECLEAN_FAILED');
            $this->messageType = 'error';
        } else {
            $this->message = Text::_('COM_JCHOPTIMIZE_CACHECLEAN_SUCCESS');
        }

        $this->redirect();
    }

    public function togglepagecache(): void
    {
        $this->message = Text::_('COM_JCHOPTIMIZE_TOGGLE_PAGE_CACHE_FAILURE');
        $this->messageType = 'error';

        /** @var ModeSwitcherModel $modeSwitcher */
        $modeSwitcher = $this->getModel('ModeSwitcher');
        $result = $modeSwitcher->togglePageCacheState();


        if ($result) {
            $this->message = Text::sprintf('COM_JCHOPTIMIZE_TOGGLE_PAGE_CACHE_SUCCESS', 'enabled');
            $this->messageType = 'success';
        }

        $this->redirect();
    }

    public function keycache(): void
    {
        $this->tasks->generateNewCacheKey();

        $this->message = Text::_('COM_JCHOPTIMIZE_CACHE_KEY_GENERATED');

        $this->redirect();
    }

    public function orderplugins(): void
    {
        $saved = $this->getModel('OrderPlugins')->orderPlugins();

        if ($saved === false) {
            $this->message = Text::_('JLIB_APPLICATION_ERROR_REORDER_FAILED');
            $this->messageType = 'error';
        } else {
            $this->message = Text::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED');
        }

        $this->redirect();
    }

    public function restoreimages(): void
    {
        $mResult = $this->tasks->restoreBackupImages();

        if ($mResult === 'SOMEIMAGESDIDNTRESTORE') {
            $this->message = Text::_('COM_JCHOPTIMIZE_SOMERESTOREIMAGE_FAILED');
            $this->messageType = 'warning';
        } elseif ($mResult === 'BACKUPPATHDOESNTEXIST') {
            $this->message = Text::_('COM_JCHOPTIMIZE_BACKUPPATH_DOESNT_EXIST');
            $this->messageType = 'warning';
        } else {
            $this->message = Text::_('COM_JCHOPTIMIZE_RESTOREIMAGE_SUCCESS');
        }

        $this->setRedirect(Route::_('index.php?option=com_jchoptimize&view=OptimizeImages', false));
        $this->redirect();
    }

    public function deletebackups(): void
    {
        $mResult = $this->tasks->deleteBackupImages();

        if ($mResult === false) {
            $this->message = Text::_('COM_JCHOPTIMIZE_DELETEBACKUPS_FAILED');
            $this->messageType = 'error';
        } elseif ($mResult === 'BACKUPPATHDOESNTEXIST') {
            $this->message = Text::_('COM_JCHOPTIMIZE_BACKUPPATH_DOESNT_EXIST');
            $this->messageType = 'warning';
        } else {
            $this->message = Text::_('COM_JCHOPTIMIZE_DELETEBACKUPS_SUCCESS');
        }

        $this->setRedirect(Route::_('index.php?option=com_jchoptimize&view=OptimizeImages', false));
        $this->redirect();
    }

    public function recache(string|UriInterface|null $redirectUrl = null): void
    {
        if (JCH_PRO === '1') {
            $reCacheModel = $this->getModel('ReCache');
            if ($reCacheModel instanceof ReCacheModel) {
                try {
                    if ($redirectUrl === null) {
                        $redirectUrl = Route::_('index.php?option=com_jchoptimize', false, 0, true);
                    }
                    $reCacheModel->reCache($this->app, $redirectUrl);
                } catch (Exception $e) {
                }
            }
        }

        $this->redirect();
    }


    public function importsettings()
    {
        /** @psalm-var array{tmp_name:string, size:int, error:int, name:string|null, type:string|null}|null $file */
        $file = $this->input->files->get('file');

        if (empty($file)) {
            $this->message = Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_NO_FILE');
            $this->messageType = 'error';

            return;
        }

        $uploadErrorMap = [
            UPLOAD_ERR_OK => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_OK'),
            UPLOAD_ERR_INI_SIZE => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_INI_SIZE'),
            UPLOAD_ERR_FORM_SIZE => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_FORM_SIZE'),
            UPLOAD_ERR_PARTIAL => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_PARTIAL'),
            UPLOAD_ERR_NO_FILE => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_NO_FILE'),
            UPLOAD_ERR_NO_TMP_DIR => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_NO_TMP_DIR'),
            UPLOAD_ERR_CANT_WRITE => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_CANT_WRITE'),
            UPLOAD_ERR_EXTENSION => Text::_('COM_JCHOPTIMIZE_UPLOAD_ERR_EXTENSION')
        ];

        try {
            $uploadedFile = new UploadedFile(
                $file['tmp_name'],
                $file['size'],
                $file['error'],
                $file['name'],
                $file['type']
            );

            if ($uploadedFile->getError() !== UPLOAD_ERR_OK) {
                throw new Exception(Text::_($uploadErrorMap[$uploadedFile->getError()]));
            }
        } catch (Exception $e) {
            $this->message = Text::sprintf('COM_JCHOPTIMIZE_UPLOADED_FILE_ERROR', $e->getMessage());
            $this->messageType = 'error';

            return;
        }

        try {
            $this->getModel('BulkSettings')->importSettings($uploadedFile);
        } catch (Exception $e) {
            $this->message = Text::sprintf('COM_JCHOPTIMIZE_IMPORT_SETTINGS_ERROR', $e->getMessage());
            $this->messageType = 'error';

            return;
        }

        $this->message = Text::_('COM_JCHOPTIMIZE_SUCCESSFULLY_IMPORTED_SETTINGS');

        $this->redirect();
    }

    public function exportsettings(): void
    {
        $file = $this->getModel('BulkSettings')->exportSettings();

        if (file_exists($file) && $this->app instanceof CMSApplication) {
            $this->app->setHeader('Content-Description', 'FileTransfer');
            $this->app->setHeader('Content-Type', 'application/json');
            $this->app->setHeader('Content-Disposition', 'attachment; filename="' . basename($file) . '"');
            $this->app->setHeader('Expires', '0');
            $this->app->setHeader('Cache-Control', 'must-revalidate');
            $this->app->setHeader('Pragma', 'public');
            $this->app->setHeader('Content-Length', (string)filesize($file));
            $this->app->sendHeaders();

            ob_clean();
            flush();
            readfile($file);

            File::delete($file);

            $this->app->close();
        }
    }

    public function setdefaultsettings(): void
    {
        try {
            $this->getModel('BulkSettings')->setDefaultSettings();
        } catch (Exception $e) {
            $this->message = Text::_('COM_JCHOPTIMIZE_RESTORE_DEFAULT_SETTINGS_FAILED');
            $this->messageType = 'error';

            return;
        }

        $this->message = Text::_('COM_JCHOPTIMIZE_DEFAULT_SETTINGS_RESTORED');

        $this->redirect();
    }

    private function default(): void
    {
        $this->setRedirect(Route::_('index.php?option=com_jchoptimize', false));
        $this->redirect();
    }
}
