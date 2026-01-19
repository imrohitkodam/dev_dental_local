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

class PayPlansViewPlanPriceVariations extends PayPlansAdminView
{
	public function __construct()
	{
		parent::__construct();
		
		$this->checkAccess('plans');
	}

	public function display($tpl = null)
	{
		$this->heading('Plan Price Variations');
		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/plans/plan-price-variations');

		JToolbarHelper::addNew();
		JToolbarHelper::publish('planpricevariations.publish');
		JToolbarHelper::unpublish('planpricevariations.unpublish');
		JToolbarHelper::deleteList('COM_PP_DELETE_SELECTED_ITEMS', 'planpricevariations.delete');

		$model = PP::model('App');
		$model->initStates();

		$planPriceVariations = $model->getAppInstances([
			'type' => 'planpricevariation'
		]);

		$pagination = $model->getPagination();

		$this->set('pagination', $pagination);
		$this->set('planPriceVariations', $planPriceVariations);

		parent::display('planpricevariations/default/default');
	}

	/**
	 * Renders the modifier form
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function form()
	{
		$this->heading('New Plan Price Variation');
		$this->addHelpButton('https://stackideas.com/docs/payplans/administrators/plans/plan-price-variations');

		JToolbarHelper::apply('planpricevariations.apply');
		JToolbarHelper::save('planpricevariations.save');
		JToolbarHelper::save2new('planpricevariations.saveNew');
		JToolbarHelper::cancel('planpricevariations.cancel');

		$id = $this->input->get('id', 0, 'int');
		$activeTab = $this->input->get('active', 'details', 'word');

		// Load the app instance
		$app = PP::app($id);
		$options = [];

		if ($app->getId()) {
			$this->heading('Editing Plan Price Variation');

			// Get the app params
			$appParams = $app->getAppParams();
			$timePrice = unserialize($appParams->get('time_price'));

			if ($timePrice) {
				foreach ($timePrice['title'] as $key => $value) {
					$obj = new stdClass;
					$obj->title = $value;
					$obj->price = $timePrice['price'][$key];
					$obj->time = $timePrice['time'][$key];

					$options[] = $obj;
				}
			}
		}

		if (empty($options)) {
			$obj = new stdClass;
			$obj->title = '';
			$obj->price = '';
			$obj->time = '';

			$options[] = $obj;
		}

		$this->set('activeTab', $activeTab);
		$this->set('app', $app);
		$this->set('options', $options);

		parent::display('planpricevariations/form/default');
	}

}
