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

/**
 * Helper for mod_payplans_plan
 *
 * @since  1.5
 */
class ModPayplansPlanHelper
{

	/**
	 * Get plans and groups
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getPlans($planIds = [])
	{
		$model = PP::model('Plan');
		$config = PP::config();

		$options = [
			'published' => 1, 
			'visible' => 1
		];

		// Default options
		$groups = [];
		$plans = [];

		// If groups is enabled then use the groups layout
		$useGroups = $config->get('useGroupsForPlan', false);

		// if admin select to display certain plans in this module, we will ignore the group layout.
		// #1150
		if ($planIds) {
			$useGroups = false;
		}

		if (!$useGroups) {

			if ($planIds) {
				$conditions = [];
				$conditions[] = ['IN', '(' . implode(',', $planIds) . ')'];
				$options['id'] = $conditions;
			}

			$plans = $model->loadRecords($options, ['limit'], '', 'ordering');
			$plans = self::formatPlans($plans);

		} else {
			$groupModel = PP::model('Group');
				
			$groupOptions = array_merge($options, ['parent' => 0]);
			$groups = $groupModel->loadRecords($groupOptions, ['limit'], '', 'ordering');
			$plans = $model->getUngrouppedPlans($options);

			$groups = self::formatGroups($groups);
			$plans = self::formatPlans($plans);
		}

		return array($plans, $groups);

	}

	/**
	 * Get plan columns
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function getPlancolumns()
	{
		$config = PP::config();

		$columns = $config->get('row_plan_counter');
		$parts = explode(',', $columns);

		if (count($parts) == 1) {
			return (int) $columns;
		}

		// We only take the first one
		$columns = (int) $parts[0];

		return $columns;

	}

	/**
	 * Formats groups to it's proper object
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function formatGroups($groups)
	{
		if (!$groups) {
			return;
		}

		foreach ($groups as &$group) {
			$group = PP::group($group);
		}

		$groups = PP::parentChild()->filterGroups($groups);
		$config = PP::config();

		$user = PP::user();
		$displaySubscribedPlans = $config->get('displayExistingSubscribedPlans');

		// unset plan if user already subscribed and display existing subscribed plan to no
		if (!$displaySubscribedPlans && $user->id) {
			$userPlans = $user->getPlans();

			foreach ($groups as $group) {
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
	public static function formatPlans($plans)
	{
		$appModel = PP::model('App');

		// Backward compatibility
		$modifiers = $appModel->getAppInstances([
			'type' => 'planmodifier',
			'published' => PP_STATE_PUBLISHED
		]);

		// Get the Plan Price Variations
		$variations = $appModel->getAppInstances([
			'type' => 'planpricevariation',
			'published' => PP_STATE_PUBLISHED
		]);

		$variations = array_merge($variations, $modifiers);

		// Get all the advanced pricing instances
		$model = PP::model('Advancedpricing');
		$advPricings = $model->getItems();

		if ($plans) {
			foreach ($plans as &$plan) {
				$plan = PP::plan($plan);
				$plan->separator = $plan->isRecurring() !== false ? JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR') : JText::_('COM_PAYPLANS_PLAN_PRICE_TIME_SEPERATOR_FOR');

				$planPriceVariations = [];

				foreach ($variations as $variation) {
					$app = PP::app($variation->app_id);

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

						$planPriceVariations[] = $app;
					}
				}

				$plan->priceVariations = $planPriceVariations;

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
			}
		}

		$plans = PP::parentChild()->filterPlans($plans);

		$user = PP::user();
		$config = PP::config();
		$displaySubscribedPlans = $config->get('displayExistingSubscribedPlans');

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
	 * Generate badges styling for each of the plan and group.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public static function renderBadgeStyleCss($plans = [], $groups = [])
	{
		$renderBadgePlanStyleCss = '';
		$renderBadgeGroupStyleCss = '';
		$badgeStyleCss = '';

		if ($plans) {
			$renderBadgePlanStyleCss = self::getBadgeStyleCss($plans, 'plan');	
		}
	
		if ($groups) {
			$renderBadgeGroupStyleCss = self::getBadgeStyleCss($groups, 'group');
		}

		$badgeStyleCss = $renderBadgePlanStyleCss . $renderBadgeGroupStyleCss;

		if ($badgeStyleCss) {
			$badgeStyleCss = '<style type="text/css">' . $badgeStyleCss . '</style>';
		}

		return $badgeStyleCss;		
	}

	/**
	 * Generate badges styling for each of the plan and group.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public static function getBadgeStyleCss($items = '', $planType = 'plan')
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
}
