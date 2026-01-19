<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\AdvancedModules\Administrator\Controller;

defined('_JEXEC') or die;

use Exception;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Response\JsonResponse;
use Joomla\Input\Input;
use Joomla\Utilities\ArrayHelper;
use RegularLabs\Library\Language as RL_Language;

/**
 * Modules list controller class.
 */
class ModulesController extends AdminController
{
    /**
     * @var    string  The prefix to use with controller messages.
     */
    protected $text_prefix = 'COM_MODULES';

    public function __construct(
        $config = [],
        MVCFactoryInterface $factory = null,
        ?CMSApplication $app = null,
        ?Input $input = null
    )
    {
        RL_Language::load('com_modules', JPATH_ADMINISTRATOR);

        parent::__construct($config, $factory, $app, $input);
    }

    /**
     * Method to clone an existing module.
     *
     * @return  void
     */
    public function duplicate()
    {
        // Check for request forgeries
        $this->checkToken();

        $pks = $this->input->post->get('cid', [], 'array');
        $pks = ArrayHelper::toInteger($pks);

        try
        {
            if (empty($pks))
            {
                throw new Exception(Text::_('COM_MODULES_ERROR_NO_MODULES_SELECTED'));
            }

            $model = $this->getModel();
            $model->duplicate($pks);
            $this->setMessage(Text::plural('COM_MODULES_N_MODULES_DUPLICATED', count($pks)));
        }
        catch (Exception $e)
        {
            $this->app->enqueueMessage($e->getMessage(), 'warning');
        }

        $this->setRedirect('index.php?option=com_advancedmodules&view=modules');
    }

    /**
     * Method to get a model object, loading it if required.
     *
     * @param string $name   The model name. Optional.
     * @param string $prefix The class prefix. Optional.
     * @param array  $config Configuration array for model. Optional.
     *
     * @return  object  The model.
     */
    public function getModel($name = 'Module', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    /**
     * Method to get the number of frontend modules
     *
     * @return  void
     */
    public function getQuickiconContent()
    {
        $model = $this->getModel('Modules');

        $model->setState('filter.state', 1);
        $model->setState('filter.client_id', 0);

        $amount = (int) $model->getTotal();

        $result = [];

        $result['amount'] = $amount;
        $result['sronly'] = Text::plural('COM_MODULES_N_QUICKICON_SRONLY', $amount);
        $result['name']   = Text::plural('COM_MODULES_N_QUICKICON', $amount);

        echo new JsonResponse($result);
    }

    /**
     * Gets the URL arguments to append to a list redirect.
     *
     * @return  string  The arguments to append to the redirect URL.
     */
    protected function getRedirectToListAppend()
    {
        $append = parent::getRedirectToListAppend();
        $append .= '&client_id=' . $this->input->getInt('client_id');

        return $append;
    }
}
