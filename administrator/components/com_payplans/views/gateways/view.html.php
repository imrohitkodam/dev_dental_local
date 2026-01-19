<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PayplansViewGateways extends PayPlansAdminView
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('gateways');
	}

	public function display($tpl = null)
	{
		$this->heading('Payment Gateways');

		JToolbarHelper::addNew();
		JToolbarHelper::publish('app.publish');
		JToolbarHelper::unpublish('app.unpublish');
		JToolbarHelper::deleteList(JText::_('COM_PP_CONFIRM_DELETE_PAYMENT_METHOD'), 'gateways.delete');

		$model = PP::model('Gateways');
		$model->initStates();

		// Get only apps related to payments
		$apps = $model->getItems();
		$types = $model->getTypes();
		$pagination = $model->getPagination();

		$paymentTypes = [];

		if ($types) {
			foreach ($types as $type) {
				$paymentTypes[$type] = ucwords($type);
			}
		}

		// Get states used in this list
		$states = $this->getStates(['search', 'published', 'type', 'limit', 'ordering', 'direction'], $model);
		$saveOrder = $states->ordering == 'ordering' && $states->direction == 'asc';
		
		$this->set('paymentTypes', $paymentTypes);
		$this->set('pagination', $pagination);
		$this->set('apps', $apps);
		$this->set('states', $states);
		$this->set('saveOrder', $saveOrder);

		return parent::display('gateways/default/default');
	}

	/**
	 * Unique form to create payment methods
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function create()
	{
		$this->heading('Create Payment Method');

		$element = $this->input->get('element', '', 'default');

		if (!$element) {
			// Get a list of available payment gateways
			$model = PP::model('Gateways');
			$apps = $model->getApps();

			// Get a list of custom apps
			$appModel = PP::model('App');
			$customApps = $appModel->getCustomApps('payment');

			$this->set('customApps', $customApps);
			$this->set('view', 'gateways');
			$this->set('layout', 'create');
			$this->set('apps', $apps);

			return parent::display('app/create/default');
		}

		// Simulate editing app, since this is a new app
		JToolbarHelper::apply('gateways.apply');
		JToolbarHelper::save('gateways.save');
		JToolbarHelper::cancel('gateways.cancel');

		$model = PP::model('App');
		$appManifest = $model->getApp($element, 'gateway');
		$path = $model->getAppManifestPath($element);

		$this->heading($appManifest->name, $appManifest->description, false);

		if (!file_exists($path)) {
			$this->info->set(JText::sprintf('The plugin <b><u>%1$s</u></b> is not installed on the site. Please ensure that the plugin is installed and enabled on the site.', $element), 'error');
			return $this->app->redirect('index.php?option=com_payplans&view=gateways&layout=create');
		}

		$form = PP::form('apps');
		$form->load($path, new JRegistry());

		$app = PP::app();
		$activeTab = $this->input->get('activeTab', '', 'word');

		$this->set('controller', 'gateways');
		$this->set('element', $element);
		$this->set('form', $form);
		$this->set('activeTab', $activeTab);
		$this->set('app', $app);
		
		parent::display('app/form/default');
	}

	/**
	 * Renders the form to edit the payment method
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function form()
	{
		$id = $this->input->get('id', 0, 'int');

		$this->heading('Edit Payment Method');

		JToolbarHelper::apply('gateways.apply');
		JToolbarHelper::save('gateways.save');
		JToolbarHelper::cancel('gateways.cancel');

		$app = PP::app($id);
		$form = $app->getForm();

		$model = PP::model('App');
		$appManifest = $model->getApp($app->type, 'gateway');

		if ($appManifest && isset($appManifest->documentation)) {
			$this->addHelpButton($appManifest->documentation);
		}

		$activeTab = $this->input->get('activeTab', '', 'word');

		$this->set('controller', 'gateways');
		$this->set('form', $form);
		$this->set('activeTab', $activeTab);
		$this->set('app', $app);

		return parent::display('app/form/default');
	}

	/**
	 * Used internally for building the list of payments
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function buildList()
	{
		$path = JPATH_PLUGINS . '/payplans';

		$folders = JFolder::folders($path, '.', false, true);
		$types = array();
		$payments = array();
		$apps = array();

		foreach ($folders as $folder) {
			$pluginXml = $folder . '/' . basename($folder) . '.xml';
			$xml = $folder . '/' . basename($folder) . '/app/' . basename($folder) . '/' . basename($folder) . '.xml';

			if (!JFile::exists($pluginXml)) {
				continue;
			}

			if (!JFile::exists($xml)) {
				continue;
			}

			$pluginParser = simplexml_load_file($pluginXml);
			$parser = simplexml_load_file($xml);
			$type = (string) $parser->tags;

			$type = strtolower($type);

			$name = (string) $parser->name;

			if ($type === 'payment' || $type === 'payment gateway' || $type === 'payunity') {

				$app = new stdClass();
				$app->name = (string) $parser->name;
				$app->element = str_replace('pp-', '', strtolower((string) $parser->alias));
				$app->description = (string) $parser->description;
				$app->documentation = 'https://stackideas.com/docs/payplans/payment-gateways/' . $app->element;

				$files = $pluginParser->files->children();

				foreach ($files as $file) {
					foreach ($file->attributes() as $attr) {
						$app->element = (string) $attr;
					}
				}

				if ($app->element === 'tpg') {
					continue;
				}

				$apps[] = $app;
			}

			$types[] = $type;
		}
		
		echo json_encode($apps);
		exit;
		
		dump($apps, $types);
	}
}