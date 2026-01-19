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

class PPThemesHelperFilter extends PPThemesHelperAbstract
{
	/**
	 * Renders the username form on table listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function username($value = '', $name = 'username')
	{
		$theme = PP::themes();

		$theme->set('value', $value);
		$theme->set('name', $name);

		$contents = $theme->output('admin/helpers/filters/username');

		return $contents;
	}

	/**
	 * Renders the invoice id on table listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function invoice($value = '', $name = 'invoice_id')
	{
		$theme = PP::themes();

		$theme->set('value', $value);
		$theme->set('name', $name);

		$contents = $theme->output('admin/helpers/filters/invoice');

		return $contents;
	}

	/**
	 * Renders the group list on table listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function group($name = 'group', $selected = '', $extras = [], $attr = [])
	{
		$options = [];

		$model = PP::model('Group');
		$groups = $model->getGroups();

		if (isset($attr['none'])) {
			$options[''] = JText::_($attr['none']);
		}

		foreach ($groups as $group) {
			$options[$group->group_id] = JText::_($group->title);
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('options', $options);

		$contents = $theme->output('admin/helpers/filters/group');

		return $contents;
	}

	/**
	 * Renders the plan list on table listings
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function plans($name = 'plan', $selected = '', $options = [])
	{
		$model = PP::model('Plan');
		$plans = $model->loadRecords([], ['where', 'limit']);

		$withoutOption = FH::normalize($options, 'without', false);

		$items = [
			'' => JText::_('COM_PP_SELECT_PLAN')
		];

		if ($withoutOption) {
			$items['-1'] = JText::_('COM_PP_WITHOUT_PLAN');
		}

		foreach ($plans as $plan) {
			$items[$plan->plan_id] = JText::_($plan->title);
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('items', $items);

		$contents = $theme->output('admin/helpers/filters/plan');

		return $contents;
	}

	/**
	 * Renders a status listing on grid layouts
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function status($name, $selected, $entity, $exclude = '', $attr = [])
	{
		$statuses = PP::getStatuses($entity);

		if ($exclude) {

			// @TODO: Refactor the exclusions
			//dump($exclude);
			$exclude = explode(",", $exclude);

			// It will remove any particular status+ any entity related fields.
			// underscore(_) was there in order to handle entity related data like PAYMENT_ and not remove any other string containg payment word
			// but as we have added ^ symbol, means that remove from starting, so, so no need to add underscore.
			foreach ($statuses as $key => $val) {
				foreach ($exclude as $exc) {
					if (preg_match("/^{$exc}/i", $key))
						unset($statuses[$key]);
				}
			}
		}

		$items = ['-1' => JText::_('COM_PP_SELECT_STATUS')];

		foreach ($statuses as $key => $value) {
			$items[$value] = JText::_('COM_PP_' . strtoupper($key));
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('items', $items);

		$contents = $theme->output('admin/helpers/filters/status');

		return $contents;
	}

	/**
	 * Renders the payplans app's on table listings
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function apps($name = 'app_id', $selected = '',  $group = '', $attr = [], $ignoreType = [], $opts = [])
	{
		$model = PP::model('app');
		$apps = $model->getItemsWithoutState([
			'published' => '', 
			'group' => $group, 
			'distinct' => true
		]);

		$options['0'] = JText::_('COM_PP_SELECT_APP_TYPE');

		if (isset($attr['none']) && !empty($attr['none'])) {
			$options['0'] = $attr['none'];
		}

		foreach ($apps as $app) {
			$title = PPFormats::app($app);

			if (isset($opts['typeAsTitle']) && $opts['typeAsTitle']) {
				$title = ucfirst($app->type);
			}

			$options[$app->type] = $title;
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('options', $options);

		$contents = $theme->output('admin/helpers/filters/apps');

		return $contents;
	}

	/**
	 * Renders the filter for payment methods
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function gateways($name = 'app_id', $selected = '',  $types = '', $attr = [], $ignoreType = [])
	{
		$model = PP::model('Gateways');
		$apps = $model->getItemsWithoutState(array('published' => 1));

		$options['0'] = JText::_('COM_PAYPLANS_SELECT_APPS');

		if (isset($attr['none']) && !empty($attr['none'])) {
			$options['0'] = $attr['none'];
		}
		
		foreach ($apps as $app) {
			$options[$app->app_id] = PPFormats::app($app);
		}
		
		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('options', $options);

		$contents = $theme->output('admin/helpers/filters/apps');

		return $contents;
	}

	/**
	 * Renders the usertype's on table listings
	 * @since	4.0
	 * @access	public
	 */
	public function usertype($name = 'usertype', $selected = '', $attr=null, $ignore=[])
	{
		$options = [];
		
		$groups = XiHelperJoomla::getUsertype();

		if (isset($attr['none'])) {
			$option = new stdClass();
			$option->title = JText::_('COM_PAYPLANS_SELECT_USERTYPE');
			$option->value = 0;
			
			$options[] = $option;		
		}
		
		foreach ($groups as $value) {
			$option = new stdClass();
			$option->title = $value;
			$option->value = $value;
			
			$options[] = $option;
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('options', $options);

		$contents = $theme->output('admin/helpers/filters/usertype');

		return $contents;
	}


	/**
	 * Renders the payplans log levels on table listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function loglevel($name = 'loglevel', $view = 'log', $selected = '', $prefix = 'filter_payplans', $attr = "")
	{ 
		$options = [];
		$levels = PP::logger()->getLevels();

		$none = \FH::normalize($attr, 'none', null);
		$minWidth = \FH::normalize($options, 'minWidth', 140);
		$identicalMatch = \FH::normalize($options, 'identicalMatch', true); 

		if ($none) {
			$options['all'] = $none;
		}

		foreach ($levels as $key => $value) {
			$options[$key] = $value;
		}

		$themes = PP::themes();
		$themes->set('name', $name);
		$themes->set('selected', $selected);
		$themes->set('options', $options);
		$themes->set('minWidth', $minWidth);
		$themes->set('identicalMatch', $identicalMatch);

		// Due to the debug value is zero and FD 'filter.lists' helper is using === comparison, we need to use back PP's 'admin/helpers/filters/loglevel' so that it won't kept showing 'Debug' filter
		$contents = $themes->output('admin/helpers/filters/loglevel');

		return $contents;
	}

	/**
	 * Renders the payplans log class on table listings
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function logclass($name = 'logclass', $view = 'log', $selected = '', $prefix = 'filter_payplans', $attr = "")
	{ 
		$options = [];
		$classes = PP::log()->getClassLog();

		$none = \FH::normalize($attr, 'none', null);

		if ($none) {
			$options['0'] = $none;
		}

		foreach ($classes as $value) {
			$options[$value] = $value;
		}

		$themes = PP::themes();
		$contents = $themes->fd->html('filter.lists', $name, $options, $selected, ['minWidth' => 280]);

		return $contents;
	}
}