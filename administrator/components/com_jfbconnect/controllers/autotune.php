<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// Check to ensure this file is included in Joomla!
if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class JFBConnectControllerAutoTune extends JFBConnectController
{
    public $steps;

    public function __construct()
    {
        parent::__construct();
        $document = JFactory::getDocument();
        $viewType = $document->getType();
        $viewName = 'autotune';
        $this->view = $this->getView($viewName, $viewType);

        $this->steps = [
            1 => [
                'name' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_DEFAULT'),
                'task' => 'display'
            ],
            2 => [
                'name' =>  JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_BASICINFO'),
                'task' => 'basicinfo'
            ],
            3 => [
                'name' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_NETWORKSETTINGS'),
                'task' => 'networksettings'
            ],
            4 => [
                'name' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_FBAPP'),
                'task' => 'fbapp'
            ],
            5 => [
                'name' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_SITECONFIG'),
                'task' => 'siteconfig'
            ],
            6 => [
                'name' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_ERROR'),
                'task' => 'errors'
            ],
            7 => [
                'name' => JText::_('COM_JFBCONNECT_AUTOTUNE_SIDEBAR_STEP_FINISH'),
                'task' => 'finish'
            ]
        ];
        $this->view->assignRef('autotuneSteps', $this->steps);
    }

    function display($cachable = false, $urlparams = false)
    {
        $task = JRequest::getCmd('task', 'display');

        if ($this->getCurrentStep($task) > 2)
        {
            $downloadId = JFBCFactory::config()->getSetting('sc_download_id');
            if (!$downloadId)
            {
                JFBCFactory::log(JText::_('COM_JFBCONNECT_MSG_ERROR_BASIC_INFO'), 'error');

                $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=basicinfo');
                $this->redirect();
            }
        }

        $viewLayout = $task;
        $autotuneModel = $this->getModel('autotune');
        $this->view->setModel($autotuneModel, true);

        $configModel = $this->getModel('config');
        $this->view->setModel($configModel, false);

        switch ($task)
        {
            case 'basicinfo':
                $this->getBasicInfo();
                break;
            case 'fbapp':
                $viewLayout = 'fbapp';
                // First, check that a FB App ID and Key are set. If not, skip this step.
                $appId = JFBCFactory::config()->getSetting('facebook_app_id');
                $secretKey = JFBCFactory::config()->getSetting('facebook_secret_key');
                if (!$appId || !$secretKey)
                {
                    JFBCFactory::log(JText::_('COM_JFBCONNECT_MSG_ERROR_NOT_CONFIGURED'), 'error');
                    $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=siteconfig');
                    $this->redirect();
                }

                // Next, check that the app is valid
                if (!$autotuneModel->validateApp(JFBCFactory::config()->getSetting('facebook_app_id'), JFBCFactory::config()->getSetting('facebook_secret_key')))
                {
                    JFBCFactory::log(JText::_('COM_JFBCONNECT_MSG_CHECK_KEYS'), 'error');
                    $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=basicinfo');
                    $this->redirect();
                }

                // Finally, check if we should redirect to the new app page
                if ($autotuneModel->isNewApp())
                {
                    $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbappnew');
                    $this->redirect();
                }

                $fields = $autotuneModel->getFieldDescriptors(false);
//                if ($fields == null) // Check if we should force load the data
//                    $autotuneModel->getFieldDescriptors(true);
                $appConfig = $autotuneModel->getAppConfig(false);
//                if ($appConfig == null)
//                    $autotuneModel->getFieldDescriptors(true);

                JToolBarHelper::custom('fbappRefresh', 'refresh', 'refresh', 'Refresh', false, false);
                JToolBarHelper::divider();
                break;
            case 'fbappnew':
                break;
            case 'siteconfig':
                $this->setupSiteConfig();
                break;
            case 'errors':
                $this->setupCheckErrors();
                break;
            case 'finish':
                break;
            case 'default':
            default:
                $this->setupIntroPage();
                break;
        }

        $this->setupToolbarButtons($this->getCurrentStep($task));

        JToolBarHelper::divider();
        JToolBarHelper::cancel('exitAutoTune', 'Exit AutoTune');
        $this->view->setLayout($viewLayout);
        $this->view->display();
    }

    public function setupToolbarButtons($currentStep)
    {
        if (defined('SC16')):
            $backIcon = 'back';
            $forwardIcon = 'forward';
        endif; //SC16
        if (defined('SC30')):
            $backIcon = 'arrow-left';
            $forwardIcon = 'arrow-right';
        endif; //SC30

        if ($currentStep > 1)
            JToolBarHelper::custom($this->steps[$currentStep-1]['task'], $backIcon, $backIcon, $this->steps[$currentStep-1]['name'], false);
        if ($currentStep < count($this->steps))
            JToolBarHelper::custom($this->steps[$currentStep+1]['task'], $forwardIcon, $forwardIcon, $this->steps[$currentStep+1]['name'], false);
    }

    private function getCurrentStep($task)
    {
        // find our current step
        for ($i = 1; $i <= count($this->steps); $i++)
        {
            if ($task == $this->steps[$i]['task'])
                return $i;
        }
        return 1;
    }

    public function exitAutoTune()
    {
        $this->setRedirect('index.php?option=com_jfbconnect');
    }

    public function fbappRefresh()
    {
        $autotuneModel = $this->getModel('autotune');
        $autotuneModel->getFieldDescriptors(true);
        $autotuneModel->getAppConfig(true);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    private function setupSiteConfig()
    {
        $autotuneModel = $this->getModel('autotune');
        $JFBCSystemEnabled = $autotuneModel->isPluginEnabled('jfbcsystem');
        $JFBCAuthenticationEnabled = $autotuneModel->isPluginEnabled('jfbconnectauth');
        $JFBCContentEnabled = $autotuneModel->isPluginEnabled('jfbccontent');
        $JFBCUserEnabled = $autotuneModel->isPluginEnabled('jfbconnectuser');

        $errors = $autotuneModel->getJoomlaErrors();

        $this->view->assignRef('JFBCSystemEnabled', $JFBCSystemEnabled);
        $this->view->assignRef('JFBCAuthenticationEnabled', $JFBCAuthenticationEnabled);
        $this->view->assignRef('JFBCContentEnabled', $JFBCContentEnabled);
        $this->view->assignRef('JFBCUserEnabled', $JFBCUserEnabled);
        $this->view->assignRef('joomlaErrors', $errors);
    }

    private function setupIntroPage()
    {
        $phpVersion = phpversion();
        $errorsFound = false;
        if (version_compare($phpVersion, '5.0.0') >= 0)
            $phpVersion .= '<td><img src="components/com_jfbconnect/assets/images/icon-16-allow.png" /></td>';
        else
        {
            $phpVersion .= '<td><img src="components/com_jfbconnect/assets/images/icon-16-deny.png" /></td>';
            $errorsFound = true;
        }
        $this->view->assignRef('phpVersion', $phpVersion);

        // cURL check
        $disableFunctions = ini_get('disable_functions');
        if (in_array('curl', get_loaded_extensions()) && strpos($disableFunctions, 'curl_exec') === false)
            $curlCheck = 'Enabled <td><img src="components/com_jfbconnect/assets/images/icon-16-allow.png" /></td>';
        else
        {
            $curlCheck = '<strong>Disabled</strong> <td><img src="components/com_jfbconnect/assets/images/icon-16-deny.png" /></td>';
            $errorsFound = true;
        }

        if ($errorsFound)
        {
            JFBCFactory::log(JText::_('COM_JFBCONNECT_MSG_ERROR_SERVER_CONFIG'), 'error');
        }
        $this->view->assignRef('curlCheck', $curlCheck);
        $this->view->assignRef('errorsFound', $errorsFound);
    }

    public function getBasicInfo()
    {
        $configModel = $this->getModel('config');
        $subscriberId = $configModel->getSetting('sc_download_id');

        $this->view->assignRef('subscriberId', $subscriberId);
    }

    public function saveBasicInfo()
    {
        $subscriberId = JRequest::getString('subscriberId');
        foreach (JFBCFactory::getAllProviders() as $provider)
        {
            $name = $provider->systemName;
            $appId = JRequest::getString($name . '_app_id');
            $secretKey = JRequest::getString($name . '_secret_key');
            JFBCFactory::config()->update($name . '_app_id', $appId);
            JFBCFactory::config()->update($name . '_secret_key', $secretKey);
        }

        JFBCFactory::config()->update('sc_download_id', $subscriberId);

        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=networksettings');
    }

    private function setupCheckErrors()
    {
        if (defined('JFBCDEV'))
        {
            $domain = 'http://localhost/autotune/start.php';
            $baseUrl = 'http://www.sourcecoast.com';
        }
        else
        {
            $domain = 'https://autotune.sourcecoast.com/start.php';
            $baseUrl = JURI::root();
        }
        $baseUrl = base64_encode(urlencode($baseUrl));

        $autotuneModel = $this->getModel('autotune');
        $configModel = $this->getModel('config');
        $subscriptionId = $configModel->getSetting('sc_download_id');
        $query = '?baseUrl=' . $baseUrl . '&subscriptionId=' . $subscriptionId . '&task=jfbconnect.errorStart&format=html&' . $autotuneModel->getVersionURLQuery();

        $iframeUrl = $domain . $query;
        $this->view->assignRef('iframeUrl', $iframeUrl);
    }

    public function saveAppConfig()
    {
        $autotuneModel = $this->getModel('autotune');
        $settings = $autotuneModel->getAppValuesToSave(false);
        $autotuneModel->updateFBApplication($settings);

        // Always set the migrations
        $migrations = $autotuneModel->getAppMigrationsToSave();
        $autotuneModel->updateFBApplication($migrations);

        // Update the database with the new app info
        $autotuneModel->getAppConfig(true);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    public function saveAppRecommendations()
    {
        $autotuneModel = $this->getModel('autotune');
        $settings = $autotuneModel->getAppValuesToSave(true);
        $autotuneModel->updateFBApplication($settings);

        // Always set the migrations
        $migrations = $autotuneModel->getAppMigrationsToSave();
        $autotuneModel->updateFBApplication($migrations);

        // Update the database with the new app info
        $autotuneModel->getAppConfig(true);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=fbapp');
    }

    public function publishPlugin()
    {
        $name = JRequest::getString('pluginName');
        $status = JRequest::getInt('pluginStatus');
        $autotuneModel = $this->getModel('autotune');
        $autotuneModel->publishPlugin($name, $status);
        $this->setRedirect('index.php?option=com_jfbconnect&view=autotune&task=siteconfig');
    }

    /*
     * Ajax call to update each social network's constants for use as necessary
     */
    public function fetchNetworkSettings()
    {
        $autotuneModel = $this->getModel('autotune');
        $response = $autotuneModel->fetchNetworkSettings();
        echo json_encode($response);
        exit;
    }
}