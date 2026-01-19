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

class PayplansControllerDiscounts extends PayPlansController
{
	protected	$_defaultOrderingDirection = 'ASC';

	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('discounts');

		$this->registerTask('save', 'save');
		$this->registerTask('saveNew', 'save');
		$this->registerTask('apply', 'save');

		$this->registerTask('close', 'cancel');

		$this->registerTask('publish', 'togglePublish');
		$this->registerTask('unpublish', 'togglePublish');
	}

	/**
	 * Deletes discounts
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function delete()
	{
		$ids = $this->input->get('cid', 0, 'int');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$discount = PP::discount((int) $id);
			$title = $discount->getTitle();
			$discount->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_DISCOUNTS_DELETED', 'discounts', [
				'discountTitle' => $title
			]);
		}

		$this->info->set('COM_PP_SELECTED_DISCOUNTS_DELETED_SUCCESS', 'success');

		return $this->redirectToView('discounts');
	}

	/**
	 * Cancel process
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function cancel()
	{
		return $this->app->redirect('index.php?option=com_payplans&view=discounts');
	}

	/**
	 * Saves the discount
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function save()
	{
		$id = $this->input->get('id', 0, 'int');
		$data = $this->input->post->getArray();

		$coreDiscount = $this->input->get('core_discount', true, 'bool');
		$couponType = $this->input->get('coupon_type', true, '');

		if (!$data['title']) {
			$this->info->set('COM_PP_DISCOUNTS_EMPTY_TITLE_ERROR', 'danger');

			return $this->redirectToView('discounts', 'form', $id ? 'id=' . $id : '');
		}

		// coupon code validation 
		if (!in_array($couponType, ['autodiscount_onrenewal', 'autodiscount_onupgrade', 'autodiscount_oninvoicecreation', 'discount_for_time_extend']) && !$data['coupon_code']) {
			$this->info->set('COM_PP_DISCOUNTS_EMPTY_COUPON_CODE_ERROR', 'danger');

			return $this->redirectToView('discounts', 'form', $id ? 'id=' . $id : '');
		}

		// before we bind the data, we need to handle the dates. # 816
		if (isset($data['start_date']) && $data['start_date']) {
			$data['start_date'] = PP::convertToGMTDate($data['start_date']);
		}

		if (isset($data['end_date']) && $data['end_date']) {
			$data['end_date'] = PP::convertToGMTDate($data['end_date']);
		}

		$discount = PP::discount($id);
		$discount->bind($data);

		if (!$coreDiscount && isset($data['plans'])) {
			$discount->plans = json_encode($data['plans']);
		}

		if ($coreDiscount) {
			$discount->plans = json_encode([]);
		}

		$discount->save();

		$actionString = 'COM_PP_ACTIONLOGS_DISCOUNTS_CREATED';
		$message = 'COM_PP_DISCOUNT_CREATED_SUCCESS';

		if ($id) {
			$actionString = 'COM_PP_ACTIONLOGS_DISCOUNTS_UPDATED';
			$message = 'COM_PP_DISCOUNT_UPDATED_SUCCESS';
		}

		$actionlog = PP::actionlog();
		$actionlog->log($actionString, 'discounts', [
			'discountTitle' => $discount->getTitle(),
			'discountLink' => 'index.php?option=com_payplans&view=discounts&layout=form&id=' . $discount->getId()
		]);

		$this->info->set($message, 'success');

		$task = $this->getTask();

		if ($task === 'apply') {
			return $this->redirectToView('discounts', 'form', 'id=' . $discount->getId());
		}

		if ($task === 'saveNew') {
			return $this->redirectToView('discounts', 'form');
		}

		return $this->redirectToView('discounts');
	}

	/**
	 * Toggles publishing state
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function togglePublish()
	{
		$ids = $this->input->get('cid', [], 'array');
		$task = $this->getTask();

		$published = $task === 'unpublish' ? false : true;
		$actionString = $task === 'publish' ? 'COM_PP_ACTIONLOGS_DISCOUNTS_PUBLISHED' : 'COM_PP_ACTIONLOGS_DISCOUNTS_UNPUBLISHED';
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$id = (int) $id;

			$discount = PP::discount($id);
			$discount->setPublished($published);
			$discount->save();

			$actionlog->log($actionString, 'discounts', [
				'discountTitle' => $discount->getTitle(),
				'discountLink' => 'index.php?option=com_payplans&view=discounts&layout=form&id=' . $discount->getId()
			]);
		}

		$message = 'COM_PP_DISCOUNT_PUBLISHED_SUCCESSFULLY';

		if ($task === 'unpublish') {
			$message = 'COM_PP_DISCOUNT_UNPUBLISHED_SUCCESSFULLY';
		}

		$this->info->set($message, 'success');
		return $this->redirectToView('discounts');
	}


	/**
	 * Generates bulk discount codes
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function generate()
	{
		$data = $this->input->post->getArray();
		$prefix = $this->input->get('generator_prefix', '', 'default');
		$total = $this->input->get('generator_total', 10, 'int');

		$model = PP::model('Discount');
		$codes = $model->generate($prefix, $total, $data);

		$output = fopen('php://output', 'w');

		// Output each row now
		foreach ($codes as $row) {
			fputcsv($output, $row);
		}

		$date = JFactory::getDate();
		$fileName = 'coupon_generator' . $date->format('m_d_Y') . '.csv';

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_DISCOUNTS_GENERATED_COUPONS', 'discounts', [
			'total' => $total,
			'prefix' => $prefix,
			'fileName' => $fileName	
		]);

		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=' . $fileName);

		fclose($output);
		exit;
	}
}
