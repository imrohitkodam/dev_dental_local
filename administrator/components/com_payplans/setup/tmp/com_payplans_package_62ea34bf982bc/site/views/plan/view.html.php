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

PP::import('site:/views/views');
PP::import('admin:/includes/limitsubscription/limitsubscription');

class PayplansViewPlan extends PayPlansSiteView
{
	public function display($tpl = null)
	{
		PP::setMeta();
		$returnUrl = '';

		$model = PP::model('Plan');

		$id = $this->input->get('plan_id', 0, 'int');
		$groupId = $this->input->get('group_id', 0, 'int');
		$from = $this->input->get('from', '', 'string');

		// If it is coming from the checkout and dashboard page, we should redirect them back to the plan listing
		// even though the display plans/groups menu item has set specific plan/group
		if (($id || $groupId) && ($from === 'checkout' || $from === 'dashboard')) {
			$id = 0;
			$groupId = 0;
		}

		// If a plan id is already provided, we need to redirect the user to the checkout page
		// If $from is 'popup' means that this is selected from the purchased popup so we just render back the plan listing and highlight the selected plan for the user.
		if ($id && $from !== 'popup') {
			$plan = PP::plan($id);
			$redirect = $plan->getSelectPermalink(false);

			PP::redirect($redirect);
			return;
		}

		// The plan that selected from the purchased popup
		$selectedFromPopup = null;

		if ($id && $from === 'popup') {
			$selectedFromPopup = $id;

			// Revert back after cache the selected plan id
			$id = null;
		}

		$options = [
			'published' => 1,
			'visible' => 1
		];

		// Default options
		$groups = [];
		$plans = [];
		$returnUrl = false;

		// To fix legacy issues with the columns per row settings
		$columns = $this->getTotalColumns();

		// If groups is enabled then use the groups layout
		$useGroups = $this->config->get('useGroupsForPlan', false);

		$this->page->title('COM_PP_PAGE_TITLE_PLAN');

		if (!$useGroups) {
			$plans = $model->loadRecords($options, ['limit'], '', 'ordering');
			$plans = $this->formatPlans($plans);

			// Retrieve plan and group badge styling
			$renderBadgeStyleCss = $this->renderBadgeStyleCss($plans, $groups);

			$this->set('returnUrl', $returnUrl);
			$this->set('columns', $columns);
			$this->set('groups', $groups);
			$this->set('plans', $plans);
			$this->set('renderBadgeStyleCss', $renderBadgeStyleCss);
			
			return parent::display('site/plan/default/default');
		}

		$groupModel = PP::model('Group');
		

		// if both are not set then need to show all groups and ungrouped plans
		if (!$id && $groupId <= 0) {
			$groupOptions = array_merge($options, ['parent' => 0]);
			$groups = $groupModel->loadRecords($groupOptions, ['limit'], '', 'ordering');
			$plans = $model->getUngrouppedPlans($options);
		}

		// When there is a group id in the query string, we should only retrieve plans under the group
		if ($groupId) {

			$plans = $model->getGrouppedPlans($options, $groupId);
			
			$groupOptions = array_merge($options, ['parent' => $groupId]);
			$groups = $groupModel->loadRecords($groupOptions, ['limit'], '', 'ordering');

			// Add Breadcrum for plan groups
			$group = PP::group($groupId);
			$returnUrl = PPR::_('index.php?option=com_payplans&view=plan&task=subscribe&group_id='. $group->getId());

			$parentGroups = array_reverse(self::getParentGroups($groupId));
			if ($parentGroups) {
				foreach ($parentGroups as $parentGroup) {
					
					$returnUrl = PPR::_('index.php?option=com_payplans&view=plan&task=subscribe&group_id=' . $parentGroup->getId());
					$this->setPathway($parentGroup->getTitle(), $returnUrl);	
				}
			} 

			$this->setPathway($group->getTitle(), $returnUrl);

			$active = $this->app->getMenu()->getActive();

			// Do not show back button when the menu item is associated with group plan. #683
			if ($active && $active->query['view'] === 'plan' && isset($active->query['group_id']) && $active->query['group_id']) {
				$returnUrl = false;
			}

			$this->page->title('COM_PP_PAGE_TITLE_PLAN_GROUP');
		}

		$groups = $this->formatGroups($groups);
		$plans = $this->formatPlans($plans);

		// Retrieve plan and group badge styling
		$renderBadgeStyleCss = $this->renderBadgeStyleCss($plans, $groups);

		$this->set('returnUrl', $returnUrl);
		$this->set('columns', $columns);
		$this->set('link', $returnUrl);
		$this->set('groups', $groups);
		$this->set('plans', $plans);
		$this->set('renderBadgeStyleCss', $renderBadgeStyleCss);
		$this->set('from', $from);
		$this->set('selectedFromPopup', $selectedFromPopup);

		return parent::display('site/plan/default/default');
	}

	/**
	 * Generate badges styling for each of the plan and group.
	 *
	 * @since	4.0.11
	 * @access	public
	 */
	public function getBadgeStyleCss($items = '', $planType = 'plan')
	{
		if (!$items) {
			return false;
		}

		$badgeStyleCss = '';

		$isPlan = $planType == 'plan' ? true : false;
		$suffix = $isPlan ? 'plan-id-' : 'group-id-';

		foreach ($items as $item) {

			$itemSuffix = $suffix . $item->getId();

			if ($item->hasBadge()) {

				if ($item->getBadgeTitleColor()) {
					$badgeStyleCss .= "#pp .pp-plan-pop-label__txt." . $itemSuffix . "{color: " . $item->getBadgeTitleColor() . " !important;}";
				}

				if ($item->getBadgeBackgroundColor()) {
					$badgeStyleCss .= "#pp .pp-plan-pop-label." . $itemSuffix . "{background: " . $item->getBadgeBackgroundColor() . " !important;}";
					$badgeStyleCss .= "#pp .pp-plan-pop-label." . $itemSuffix . "::before{border-top-color: " . $item->getBadgeBackgroundColor() . " !important;}";
				}
			}
		}

		return $badgeStyleCss;
	}	

	/**
	 * Generate badges styling for each of the plan and group.
	 *
	 * @since	4.0.11
	 * @access	public
	 */
	public function renderBadgeStyleCss($plans = [], $groups = [])
	{
		$renderBadgePlanStyleCss = '';
		$renderBadgeGroupStyleCss = '';
		$badgeStyleCss = '';

		if ($plans) {
			$renderBadgePlanStyleCss = $this->getBadgeStyleCss($plans, 'plan');	
		}
	
		if ($groups) {
			$renderBadgeGroupStyleCss = $this->getBadgeStyleCss($groups, 'group');
		}

		$badgeStyleCss = $renderBadgePlanStyleCss . $renderBadgeGroupStyleCss;

		if ($badgeStyleCss) {
			$badgeStyleCss = '<style type="text/css">' . $badgeStyleCss . '</style>';
		}

		return $badgeStyleCss;		
	}	

	/**
	 * Formats groups to it's proper object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function formatGroups($groups)
	{ 
		if (!$groups) {
			return;
		}

		foreach ($groups as &$group) {
			$group = PP::group($group);
		}

		$groups = PP::parentChild()->filterGroups($groups);

		$user = PP::user();
		$displaySubscribedPlans = $this->config->get('displayExistingSubscribedPlans');

		// unset plan if user already subscribed and display existing subscribed plan to no
		if (!$displaySubscribedPlans && $user->id) {
			$userPlans = $user->getPlans();

			foreach ($groups as &$group) {
				$groupPlans = $group->getPlans();

				// get its child groups
				$groupModel = PP::model('group');
				$childGroups = $groupModel->loadRecords([
					'parent' => $group->getId()
				]);

				// if has any child group then do nothing
				if (count($childGroups) > 0) {
					continue;
				}

				//otherwise check for its child plans
				$childPlans = $group->getPlans();

				if (empty($childPlans)) {
					continue;
				}

				foreach ($userPlans as $plan) {
					unset($childPlans[$plan->getId()]);
				}
			}
		}

		return $groups;
	}

	/**
	 * Formats plans to it's proper object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function formatPlans($plans)
	{
		$appModel = PP::model('App');

		// Get modifiers that applied to all plans
		$planPriceVariations = $appModel->getAppInstances([
			'type' => 'planpricevariation', 
			'published' => PP_STATE_PUBLISHED
		]);

		// Get basix tax that applied to plans
		$basictaxes = [];
		if ($this->config->get('layout_plan_include_tax', false)) {

			$basictaxes = $appModel->getAppInstances([
				'type' => 'basictax', 
				'published' => PP_STATE_PUBLISHED
			]);
		}

		// TODO:: get from settings
		$countryModel = PP::model('Country');
		$defaultCountry = $countryModel->getDefaultCountry();

		// Get all the advanced pricing instances
		$model = PP::model('Advancedpricing');
		$advPricings = $model->getItems();

		if ($plans) {
			foreach ($plans as &$plan) {
				$plan = PP::plan($plan);

				// Check if user really have access to this plan
				$hasAccess = true;
				$hasAccess = $plan->canSubscribe();

				if (!$hasAccess) {
					unset($plans[$plan->getId()]);
					continue;
				}

				$plan->separator = $plan->isRecurring() !== false ? JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR') : JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR_FOR');

				$PriceVariations = [];

				foreach ($planPriceVariations as $planPriceVariation) {
					$app = PP::app($planPriceVariation->app_id);
					if ($app->getCoreParams()->get('applyAll') == '1' || $appModel->isPlanRelated($app->getId(), $plan->getId())) {
						
						$tmpOption = unserialize($app->getAppParam('time_price'));
						$options = [];

						if ($tmpOption) {
							foreach ($tmpOption['title'] as $key => $value) {
								$obj = new stdClass;
								$obj->title = $value;
								$obj->price = $tmpOption['price'][$key];
								$obj->time = $tmpOption['time'][$key];

								$options[] = $obj;
							}
						}

						$app->options = $options;

						$PriceVariations[] = $app;
					}
				}

				$plan->pricevariations = $PriceVariations;

				// Process the advanced pricing
				$plan->advancedpricing = false;

				foreach ($advPricings as $adv) {
					// If advancepricing rule disabled then do nothing
					if (!$adv->published) {
						continue;
					}

					// Check if this plan is assigned in advanced pricing
					if (in_array($plan->getId(), $adv->assignedPlans)) {
						$plan->advancedpricing = $adv;
					}
				}

				// process basic tax
				$plan->basictax = 0;

				if ($basictaxes && !$plan->isFree()) {

					foreach ($basictaxes as $btax) {

						$appTax = PP::app($btax->app_id);

						if ($appTax->getCoreParams()->get('applyAll') == '1' || $appModel->isPlanRelated($appTax->getId(), $plan->getId())) {

							$taxRate = $appTax->getAppParam('tax_rate');
							$taxCountries = $appTax->getAppParams()->get('tax_country');

							if ($taxRate) {

								$taxRate = floatval($taxRate);
								$planPrice = floatval($plan->getPrice());

								$taxPrice = ($planPrice * $taxRate) / 100;

								if (count($taxCountries) == 1 && $taxCountries[0] == -1) {
									// all country
									$plan->basictax = $taxPrice;
									break;
								}

								if (count($taxCountries) > 1) {
									if ($defaultCountry && in_array($defaultCountry->country_id, $taxCountries)) {
										$plan->basictax = $taxPrice;
										break;
									}
								}
							}
						}
					}
				}

				// check for Fixed Expiration Plan && if recurring plan and forever/Lifetime plan skip the process
				$plan->fixedExpirationDate = '';
				if ($plan->isFixedExpirationDate() && !($plan->isRecurring() || $plan->isForever())) {

					$expirationDate = $plan->getExpirationOnDate();
					$startDate = $plan->getSubscriptionFromExpirationDate();
					$endDate = $plan->getSubscriptionEndExpirationDate();
					$currentDate = PP::date();
					
					$isApplicableFixedExpiration = false;

					//when range is not set then change date anyway
					if(empty($startDate) && empty($endDate)){
						$isApplicableFixedExpiration = true;
					}
		
					// when range is set then check subscription date whether lies in that range 
					if(!empty($startDate) && !empty($endDate) && ($currentDate->toUnix() >= $startDate->toUnix()) && ($currentDate->toUnix() <= $endDate->toUnix())){
						$isApplicableFixedExpiration = true;
					}                                             
					
					//when only start date is set
					if(!empty($startDate) && empty($endDate) && $currentDate->toUnix() >= $startDate->toUnix() ){
						$isApplicableFixedExpiration = true;
					}
					
					//when only end date is set
					if(empty($startDate) && !empty($endDate) && $currentDate->toUnix() <= $endDate->toUnix() && empty($startDate)){
						$isApplicableFixedExpiration = true;
					}

					// Set Fixed Expiration Date 
					if ($isApplicableFixedExpiration) {
						$plan->fixedExpirationDate = $expirationDate;
					}
				}
			}
		}

		$plans = PP::parentChild()->filterPlans($plans);
		$plans = PPlimitsubscription::filterPlans($plans);

		$user = PP::user();
		$displaySubscribedPlans = $this->config->get('displayExistingSubscribedPlans');

		// unset plan if user already subscribed and display existing subscribed plan to no
		if (!$displaySubscribedPlans && $user->id) {
			$userPlans = $user->getPlans();
			foreach ($userPlans as $plan) {
				unset($plans[$plan->getId()]);
			}
		}

		return $plans;
	}

	/**
	 * Renders the login form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function login()
	{
		$model = PP::model('Plan');

		$planId = $model->getState('id');
		$this->set('plan', PayplansPlan::getInstance($planId));

		return parent::display('site/plan/default/login');
	}

	/**
	 * Triggers?
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function trigger()
	{
		$this->setTpl('partial_position');
		return true;
	}

	/**
	 * Get total number of columns per row
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getTotalColumns()
	{
		$columns = $this->config->get('row_plan_counter');
		$parts = explode(',', $columns);

		if (count($parts) == 1) {
			return (int) $columns;
		}

		// We only take the first one
		$columns = (int) $parts[0];

		return $columns;
	}

	/**
	 * Get parent groups
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public static function getParentGroups($groupId, &$data = [])
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT * FROM ' . $db->qn('#__payplans_group');
		$query[] = 'WHERE ' . $db->qn('group_id') . '=' . $db->Quote($groupId);

		$query = implode(' ', $query);

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (!$result || (isset($result[0]->parent) && !$result[0]->parent)) {
			return $data;
		}

		$data[] = PP::group($result[0]->parent);

		return self::getParentGroups($result[0]->parent, $data);
	}
}
