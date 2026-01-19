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

class PPThemesHelperForm extends PPThemesHelperAbstract
{
	/**
	 * Renders a hidden form inputs on generic forms
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function action($controller = '', $task = '', $view = '')
	{
		$theme = PP::themes();

		if ($task) {
			$task = $controller ? $controller . '.' . $task : $task;
		}

		$theme->set('controller', $controller);
		$theme->set('task', $task);
		$theme->set('view', $view);

		$output = $theme->output('admin/helpers/form/action');

		return $output;
	}

	/**
	 * Renders a hidden form inputs on generic forms
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function activeTab($active = '')
	{
		$theme = PP::themes();
		$theme->set('active', $active);

		$output = $theme->output('admin/helpers/form/activetab');

		return $output;
	}

	/**
	 * Renders an amount form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function amount($amount, $currency, $id = '', $attributes = '', $options = [])
	{
		$config = PP::config();
		$fractionDigitCount = $config->get('fractionDigitCount');
		$separator = $config->get('price_decimal_separator');
		$currencyBeforeAfter = $config->get('show_currency_at');

		$amount = number_format(round($amount, $fractionDigitCount), $fractionDigitCount, $separator, '');

		$theme = PP::themes();
		$theme->set('amount', $amount);
		$theme->set('currency', $currency);
		$theme->set('currencyBeforeAfter', $currencyBeforeAfter);
		$theme->set('id', $id);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/amount');

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function autocomplete($name, $selected = null, $id = '', $attributes = '', $options = [])
	{
		$theme = PP::themes();

		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		// Ensure that options are all objects
		if ($options) {
			foreach ($options as &$option) {
				$option = (object) $option;
			}
		}

		JHtml::_('formbehavior.chosen', '.pp-autocomplete', null);

		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('selected', $selected);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);

		$output = $theme->output('admin/helpers/form/autocomplete');

		return $output;
	}

	/**
	 * Renders a calendar input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function calendar($name, $value, $id = '', $attributes = '', $options = [])
	{
		if (is_array($attributes)) {
			$attributes	= implode(' ', $attributes);
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('id', $id);
		$theme->set('attributes', $attributes);

		return $theme->output('admin/helpers/form/calendar');
	}

	/**
	 * Renders a credit card input form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function card($names, $value = '', $id = '', $attributes = '', $options = [])
	{
		if (is_array($attributes)) {
			$attributes	= implode(' ', $attributes);
		}

		$inputNames = new stdClass;
		$inputNames->name = false;
		$inputNames->nameValue = '';
		$inputNames->card = 'card';
		$inputNames->cardValue = '';
		$inputNames->expireMonthYear = 'XX / YYYY';
		$inputNames->expireMonth = 'exp_month';
		$inputNames->expireMonthValue = '';
		$inputNames->expireYear = 'exp_year';
		$inputNames->expireYearValue = '';
		$inputNames->code = 'cvv';
		$inputNames->codeValue = '';

		if (isset($names['name'])) {
			$inputNames->name = $names['name'];
		}

		if (isset($names['card'])) {
			$inputNames->card = $names['card'];
		}

		if (isset($names['expire_month_year'])) {
			$inputNames->expireMonthYear = $names['expire_month_year'];
		}

		if (isset($names['expire_month'])) {
			$inputNames->expireMonth = $names['expire_month'];
		}

		if (isset($names['expire_year'])) {
			$inputNames->expireYear = $names['expire_year'];
		}

		if (isset($names['code'])) {
			$inputNames->code = $names['code'];
		}

		foreach ($inputNames as $key => $property) {
			if (isset($value[$property])) {
				$variable = $key . 'Value';

				if (isset($inputNames->$variable)) {
					$inputNames->$variable = $value[$property];
				}
			}
		}

		$theme = PP::themes();
		$uuid = uniqid();

		$theme->set('inputNames', $inputNames);
		$theme->set('uuid', $uuid);
		$theme->set('value', $value);
		$theme->set('id', $id);
		$theme->set('attributes', $attributes);

		return $theme->output('site/helpers/form/card');
	}

	/**
	 * Renders a list of currencies
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function currency($name, $value, $id = '', $attributes = '', $options = [])
	{
		$items = PP::getCurrency();
		$currencies = [];
		$attributes = $this->formatAttributes($attributes);

		if ($items) {
			foreach ($items as $item) {
				$currencies[$item->currency_id] = PPFormats::currency($item, [], 'fullname');
			}
		}

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('currencies', $currencies);

		$output = $theme->output('admin/helpers/form/currency');

		return $output;
	}

	/**
	 * Renders the registration type form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function dependency($name, $value, $id = '', $attributes = '', $options = [])
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		// Ensure that options are all objects
		if ($options) {
			foreach ($options as &$option) {
				$option = (object) $option;
			}
		}

		$uid = uniqid();

		$theme = PP::themes();
		$theme->set('options', $options);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('uid', $uid);

		$output = $theme->output('admin/helpers/form/dependency');

		return $output;
	}

	/**
	 * Generates a text input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function email($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/email');

		return $output;
	}

	/**
	 * Generates a WYSIWYG editor
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function editor($name, $value, $id = '', $attributes = '', $options = [], $dependents = [], $decode = true)
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$editor = PPCompat::getEditor();

		if ($decode) {
			$value = base64_decode($value);
		}

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('editor', $editor);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/editor');

		return $output;
	}

	/**
	 * Renders an expiration type form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function expiration($name, $value, $id = '', $attributes = '', $options = [])
	{
		$attributes = $this->formatAttributes($attributes);

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/expiration');

		return $output;
	}

	/**
	 * Formats a list of attributes
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function formatAttributes($data)
	{
		if (!$data) {
			return '';
		}

		// If attributes is already a string, we shouldn't need to format anything
		if (!is_array($data) && is_string($data)) {
			return $data;
		}

		$attributes = '';

		foreach ($data as $key => $value) {
			$attributes .= ' ' . $key . '="' . $value . '"';
		}

		return $attributes;
	}

	/**
	 * Renders a hidden input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function hidden($name, $value = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/hidden');

		return $output;
	}

	/**
	 * Renders a hidden input that is normally used for storing ids
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function ids($name, $ids = [])
	{
		if (!$ids) {
			return;
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('ids', $ids);

		$output = $theme->output('admin/helpers/form/ids');

		return $output;
	}

	/**
	 * Generates a password input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function password($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/password');

		return $output;
	}

	/**
	 * Renders a plans dropdown
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function plans($name, $value, $editable = true, $multiple = false, $attributes = '', $exclusion = [], $options = [])
	{
		static $allPlans = null;

		if (!is_array($attributes)) {
			$attributes = PP::makeArray($attributes);
		}
		
		if (isset($attributes['multiple']) && $attributes['multiple']) {
			$multiple = true;
		}

		$theme = isset($options['theme']) ? $options['theme'] : '';
		$width = isset($options['width']) ? $options['width'] : '';

		if (!$editable) {
			$attributes['disabled'] = true;
		}

		$attributes = $this->formatAttributes($attributes);

		$value = (array) $value;

		$selectedPlan = false;

		if (is_null($allPlans)) {
			$model = PP::model('Plan');
			$items = $model->loadRecords();

			if ($items) {
				foreach ($items as $item) {
					$plan = PP::plan($item);
					$allPlans[] = $plan;
				}
			}
		}

		// container
		$plans = $allPlans;

		// if exclusion is needed
		if ($exclusion && $allPlans) {
			// reset the container here. we will exclude manually here.
			$plans = [];

			foreach ($allPlans as $p) {
				if (! in_array($p->plan_id, $exclusion)) {
					$plans[] = $p;
				}
			}
		}

		$planSelections = [];

		if ($plans) {
			foreach ($plans as &$plan) {
				$plan->isSelected = false;

				if (in_array($plan->plan_id, $value)) {
					$plan->isSelected = true;
				}

				$planSelections[$plan->plan_id] = $plan;
			}
		}

		if ($multiple) {
			$name = $name . '[]';
		}

		$themes = PP::themes();
		$themes->set('width', $width);
		$themes->set('editable', $editable);
		$themes->set('planSelections', $planSelections);
		$themes->set('name', $name);
		$themes->set('value', $value);
		$themes->set('multiple', $multiple);
		$themes->set('attributes', $attributes);
		$themes->set('theme', $theme);

		$output = $themes->output('admin/helpers/form/plans');

		return $output;
	}

	/**
	 * Renders a plans dropdown
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function plansgroup($name, $value, $editable = true, $attributes = '')
	{
		static $plans = null;

		$multiple = false;

		if (isset($attributes['multiple']) && $attributes['multiple']) {
			$multiple = true;
		}

		$attributes = $this->formatAttributes($attributes);
		$selectedGroup = false;

		if (is_null($plans)) {
			$model = PP::model('Group');
			$groups = $model->loadRecords();

			if ($groups) {
				foreach ($groups as &$group) {
					$group = PP::group($group);

					$group->isSelected = false;

					if ($group->group_id == $value) {
						$group->isSelected = true;
						$slectedGroup = $group;
					}
				}
			}
		}

		if ($multiple) {
			$name = $name . '[]';
		}

		$theme = PP::themes();
		$theme->set('selectedGroup', $selectedGroup);
		$theme->set('editable', $editable);
		$theme->set('groups', $groups);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('multiple', $multiple);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/plansgroup');

		return $output;
	}

	/**
	 * Plan to alias mapping. Currently only being used by Fastspring
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function planAlias($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$model = PP::model('Plan');
		$plans = $model->getItems();

		$totalValues = is_array($value) ? count($value) : 0;

		$theme = PP::themes();
		$theme->set('plans', $plans);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('totalValues', $totalValues);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/plan.alias');

		return $output;
	}

	/**
	 * Generates a file input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function file($name, $value, $id = '', $attributes = [], $options = [])
	{
		$user = PP::normalize($options, 'user', null);
		$allowInput = PP::normalize($options, 'allowInput', false);
		$download = PP::normalize($options, 'download', false);
		$required = PP::normalize($options, 'required', false);
		$label = PP::normalize($options, 'label', '');
		$classes = PP::normalize($attributes, 'class', '');

		if (isset($attributes['class'])) {
			unset($attributes['class']);
		}

		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$theme = PP::themes();
		$theme->set('classes', $classes);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('user', $user);
		$theme->set('allowInput', $allowInput);
		$theme->set('download', $download);
		$theme->set('required', $required);
		$theme->set('label', $label);

		$output = $theme->output('admin/helpers/form/file');

		return $output;
	}

	/**
	 * Generates a dropdown list for file input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function filelist($name, $value, $id = '', $attributes = '', $folder = '', $pattern = '.php', $exclude = [], $stripExtension = true)
	{
		if (!$folder) {
			return;
		}

		// If the folder is a namespace, we need to resolve to the proper path
		$isNamespace = PP::isNamespace($folder);

		if ($isNamespace) {
			$namespace = $folder;

			$resolver = PP::resolver();
			$folder = $resolver->resolve($folder, '');
		}

		if (!$isNamespace) {
			$folder = JPATH_ROOT . '/' . $folder;
		}

		$options = [];

		$files = '';
		if (JFolder::exists($folder)) {
			$files = JFolder::files($folder, $pattern, true, true, $exclude);	
		}

		// Default empty option
		$option = new stdClass();
		$option->title = JText::_('COM_PP_SELECT_FILE');
		$option->value = '';

		$options[] = $option;

		if ($files) {
			foreach ($files as $file) {
				$option = new stdClass();
				$option->title = basename($file);
				$option->value = $stripExtension ? JFile::stripExt($option->title) : $option->title;

				$options[] = $option;
			}
		}

		$theme = PP::themes();
		$theme->set('options', $options);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/list');

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function discounts($name, $value, $id = '', $attributes = '')
	{
		$theme = PP::themes();

		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		// Get a list of coupon codes
		$model = PP::model('Discount');
		$codes = $model->getCouponCodes();

		$options = [];

		foreach ($codes as $code) {
			$options[$code->coupon_code] = JText::_($code->title);
		}

		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);

		$output = $theme->output('admin/helpers/form/discounts');

		return $output;
	}

	/**
	 * Generates a file input with image
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function imagefile($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$image = PP::config()->get($name, false);

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('image', $image);

		$output = $theme->output('admin/helpers/form/imagefile');

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function easyblogCategories($name, $selected, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::easyblog();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYBLOG_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$categories = $lib->getCategories();
		$options = [];

		foreach ($categories as $category) {
			$options[$category->id] = JText::_($category->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Render Lists of Easyblog Article
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function easyblogArticles($name, $selected, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::easyblog();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYBLOG_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$articles = $lib->getArticles();
		$options = [];

		foreach ($articles as $article) {
			$options[$article->id] = JText::_($article->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function easydiscussAcl($name, $selected, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::easydiscuss();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYDISCUSS_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$rules = $lib->getAclRules();
		$options = [];

		foreach ($rules as $rule) {
			$options[$rule->id] = ucfirst(JText::_($rule->description));
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function easydiscussBadges($name, $selected, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::easydiscuss();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYDISCUSS_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$badges = $lib->getBadges();
		$options = [];

		foreach ($badges as $badge) {
			$options[$badge->id] = JText::_($badge->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function easydiscussCategories($name, $selected, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::easydiscuss();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYDISCUSS_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$categories = $lib->getCategories();
		$options = [];

		foreach ($categories as $category) {
			$options[$category->id] = JText::_($category->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function kunenaCategories($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::kunena();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_KUNENA_BEFORE_USING_THIS_APPLICATION'), $attributes);

		}

		$categories = $lib->getCategories();
		$options = [];

		foreach ($categories as $category) {
			$options[$category->id] = JText::_($category->name);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders an autocomplete form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function mailchimplist($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		// Get current app id from the query
		$appId = $this->input->get('id', '');

		if (!$appId) {
			return $this->notavailable(JText::_('COM_PP_PLEASE_SAVE_MAILCHIMP_APP_FIRST'), $attributes);
		}

		$app = PP::app()->getAppInstance($appId);
		$params = $app->getAppParams();
		$apiKey = $params->get('mailchimpApiKey', '');
		$email = $params->get('mailchimpMerchantEmail', '');

		if (!$apiKey || !$email) {
			return $this->notavailable(JText::_('COM_PP_PLEASE_SET_MAILCHIMP_APIKEY_AND_EMAIL'), $attributes);
		}

		$lib = PP::mailchimp();
		$lists = $lib->getLists($apiKey, $email);

		$options = [];

		foreach ($lists as $list) {
			$options[$list->id] = JText::_($list->name);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders the popover html contents
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function label($label, $desc = '', $columns = 3, $help = true, $required = false)
	{
		if (!$desc) {
			$desc = $label . '_DESC';
			$desc = JText::_($desc);
		}

		$label = JText::_($label);

		// Generate a short unique id for each label
		$uniqueId = PPJString::substr(md5($label), 0, 16);

		$theme = PP::themes();
		$theme->set('uniqueId', $uniqueId);
		$theme->set('columns', $columns);
		$theme->set('help', $help);
		$theme->set('label', $label);
		$theme->set('desc', $desc);
		$theme->set('required', $required);

		return $theme->output('admin/helpers/form/label');
	}

	/**
	 * Renders the registration type form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function lists($name, $value, $id = '', $attributes = '', $options = [])
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		// Ensure that options are all objects
		if ($options) {
			foreach ($options as &$option) {
				$option = (object) $option;
			}
		}

		$theme = PP::themes();
		$theme->set('options', $options);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/list');

		return $output;
	}

	/**
	 * Renders checkbox
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function checkbox($name, $value, $id = '', $attributes = '', $options = [])
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$value = (array) $value;
		$theme = PP::themes();
		$theme->set('options', $options);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/checkbox');

		return $output;
	}

	/**
	 * Renders a hidden input for tokens
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function token()
	{
		$themes = PP::themes();
		$output = $themes->fd->html('form.token');

		return $output;
	}

	/**
	 * Generates a text input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function text($name, $value, $id = '', $attributes = '', $options = [], $readOnly = false)
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		if (is_object($options)) {
			$options = (array) $options;
		}

		$size = PP::normalize($options, 'size', '');
		$postfix = PP::normalize($options, 'postfix', '');
		$prefix = PP::normalize($options, 'prefix', '');
		$classes = PP::normalize($options, 'class', '');
		$placeholder = PP::normalize($options, 'placeholder', '');

		$theme = PP::themes();
		$theme->set('classes', $classes);
		$theme->set('placeholder', $placeholder);
		$theme->set('size', $size);
		$theme->set('postfix', $postfix);
		$theme->set('prefix', $prefix);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('readOnly', $readOnly);

		$output = $theme->output('admin/helpers/form/text');

		return $output;
	}

	/**
	 * Generates a telephone input
	 *
	 * @since	4.2.6
	 * @access	public
	 */
	public function telephone($name, $value, $id = '', $attributes = '', $options = [], $readOnly = false)
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		if (is_object($options)) {
			$options = (array) $options;
		}

		$classes = PP::normalize($options, 'class', '');

		$theme = PP::themes();
		$theme->set('classes', $classes);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('readOnly', $readOnly);

		$output = $theme->output('admin/helpers/form/telephone');

		return $output;
	}

	/**
	 * Generates a textarea input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function textarea($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/textarea');

		return $output;
	}

	/**
	 * Generates an on/off switch
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function toggler($name, $value, $id = '', $attributes = '', $options = [], $dependents = [])
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$theme = PP::themes();
		$theme->set('dependents', $dependents);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/toggler');

		return $output;
	}

	/**
	 * Renders the status dropdown selection
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function status($name, $selected, $type, $id = '', $multiple = false, $attributes = '', $excludeOptions = [])
	{
		$options = [];
		$attributes = $this->formatAttributes($attributes);

		if ($type == 'subscription') {
			$options[PP_SUBSCRIPTION_ACTIVE] = JText::_('COM_PP_SUBSCRIPTION_ACTIVE');
			$options[PP_SUBSCRIPTION_HOLD] = JText::_('COM_PP_SUBSCRIPTION_HOLD');
			$options[PP_SUBSCRIPTION_EXPIRED] = JText::_('COM_PP_SUBSCRIPTION_EXPIRED');
			$options[PP_SUBSCRIPTION_NONE] = JText::_('COM_PP_SUBSCRIPTION_NONE');
		}

		if ($type == 'invoice') {
			$options[PP_INVOICE_CONFIRMED] = JText::_('COM_PP_INVOICE_CONFIRMED');
			$options[PP_INVOICE_PAID] = JText::_('COM_PP_INVOICE_PAID');
			$options[PP_INVOICE_REFUNDED] = JText::_('COM_PP_INVOICE_REFUNDED');
			$options[PP_INVOICE_WALLET_RECHARGE] = JText::_('COM_PP_INVOICE_WALLET_RECHARGE');
		}

		if ($type == 'both') {
			$options[PP_SUBSCRIPTION_ACTIVE] = JText::_('Subscriptions') . ' (' . JText::_('COM_PP_SUBSCRIPTION_ACTIVE') . ')';
			$options[PP_SUBSCRIPTION_HOLD] = JText::_('Subscriptions') . ' (' . JText::_('COM_PP_SUBSCRIPTION_HOLD') . ')';
			$options[PP_SUBSCRIPTION_EXPIRED] = JText::_('Subscriptions') . ' (' . JText::_('COM_PP_SUBSCRIPTION_EXPIRED') . ')';
			$options[PP_SUBSCRIPTION_NONE] = JText::_('Subscriptions') . ' (' . JText::_('COM_PP_SUBSCRIPTION_NONE') . ')';
			$options[PP_INVOICE_CONFIRMED] = JText::_('Invoice') . ' (' . JText::_('COM_PP_INVOICE_CONFIRMED') . ')';
			$options[PP_INVOICE_PAID] = JText::_('Invoice') . ' (' . JText::_('COM_PP_INVOICE_PAID') . ')';
			$options[PP_INVOICE_REFUNDED] = JText::_('Invoice') . ' (' . JText::_('COM_PP_INVOICE_REFUNDED') . ')';
		}

		if ($excludeOptions) {
			foreach ($excludeOptions as $excludeOption) {
				if (isset($options[$excludeOption])) {
					unset($options[$excludeOption]);
				}
			}
		}

		if (!is_array($selected)) {
			$selected = PP::makeArray($selected);
		}

		$theme = PP::themes();
		$theme->set('selected', $selected);
		$theme->set('name', $name);
		$theme->set('options', $options);
		$theme->set('attributes', $attributes);
		$theme->set('multiple', $multiple);
		$output = $theme->output('admin/helpers/form/status');

		return $output;
	}


	/**
	 * Renders a colour picker input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function colorpicker($name, $value = '', $revert = '')
	{
		// This must be calling from 'admin/forms/renderer/apps'
		if ($revert && substr($revert, 0) !== '#') {
			$revert = $value;
		}

		$themes = PP::themes();
		$output = $themes->fd->html('form.colorpicker', $name, $value, $revert);

		return $output;
	}

	/**
	 * Renders a dropdown of countries
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function country($name, $value = '', $id = '', $attributes = '', $options = [], $disabled = false)
	{
		$attributes = $this->formatAttributes($attributes);

		$multiple = false;
		$allowAll = false;

		$width = FH::normalize($options, 'width', '');
		$multiple = FH::normalize($options, 'multiple', false);
		$allowAll = FH::normalize($options, 'allowAll', false);

		$options = [];
		$model = PP::model('Country');
		$countries = $model->loadRecords([
			'published' => 1
		]);

		$floatLabel = false;

		if (isset($options['floatLabel']) && $options['floatLabel']) {
			$floatLabel = true;
		}

		// Get the default value if there is any
		if (!$value) {
			$defaultCountry = $model->getDefaultCountry();

			if ($defaultCountry) {
				$value = [$defaultCountry->country_id];
			}
		}

		if ($multiple) {
			$name = $name . '[]';
		}

		$countrySelections = [];

		if (!$multiple) {
			$countrySelections['0'] = 'COM_PP_SELECT_COUNTRY';
		}

		if (!$allowAll) {
			$countrySelections['-1'] = 'COM_PP_ALL_COUNTRIES';
		}

		foreach ($countries as $country) { 
			$countrySelections[$country->country_id] = $country->title;
		}

		$themes = PP::themes();
		$themes->set('floatLabel', $floatLabel);
		$themes->set('attributes', $attributes);
		$themes->set('name', $name);
		$themes->set('id', $id);
		$themes->set('value', $value);
		$themes->set('multiple', $multiple);
		$themes->set('allowAll', $allowAll);
		$themes->set('disabled', $disabled);
		$themes->set('width', $width);
		$themes->set('countrySelections', $countrySelections);
		$output = $themes->output('admin/helpers/form/country');

		return $output;
	}

	/**
	 * Renders the registration type form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function timer($name, $value, $id = '', $attributes = '', $options = [])
	{
		$dateSegments = [
			'year' => 10,
			'month' => 11,
			'day' => 30,
			'hour' => 23,
			'minute' => 59,
			'second' => 59
		];

		if (!$value) {
			$value = '000000000000';
		} else if (stripos($value, 'NaN') !== false) {
			// fix legacy value.
			$value = str_replace('NaN', '00', $value);
		}

		$values = str_split($value, 2);

		// split the values into correct segments
		list($year,$month,$day,$hour,$minute,$second) = $values;

		$segments = [];

		$displayTitle = '';

		foreach ($dateSegments as $key => $limit) {
			$options = [];

			for ($i = 0; $i <= $limit; $i++) {
				$obj = new stdClass();
				$obj->title = $i;
				// $obj->value = str_pad($i, 2, '0', STR_PAD_LEFT);
				$obj->value = $i;
				$obj->selected = false;

				$val = $$key;
				$val = (int) $val;

				if ($val == $i) {
					$obj->selected = true;
				}

				$options[] = $obj;
			}

			$segments[$key] = $options;
		}

		$displayTitle = PP::string()->formatTimer($value);

		$theme = PP::themes();
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('segments', $segments);
		$theme->set('attributes', $attributes);
		$theme->set('displayTitle', $displayTitle);

		$output = $theme->output('admin/helpers/form/timer');

		return $output;
	}

	/**
	 * Renders a textbox with the ability to browse users
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function user($name, $selectedUser = null, $id = '', $attributes = '', $options = [])
	{
		if (!$id) {
			$id = str_ireplace(array('.', ' ', '_'), '-', $name);
		}

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('selectedUser', $selectedUser);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/user');

		return $output;
	}

	/**
	 * Renders a textbox with the ability to browse users
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function usersubscriptions($name, $value, $id = '', $attributes = '', $userId = null)
	{
		$theme = PP::themes();

		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		if ($value) {
			$value = ltrim($value, ',');
			$value = rtrim($value, ',');

			$value = explode(',', $value);
		}

		// Ensure that options are all objects
		$user = PP::user($userId);
		$subscriptions = $user->getSubscriptions();
		$options = [];

		if ($subscriptions) {
			foreach ($subscriptions as $subscription) {
				$option = new stdClass();
				$option->title = $subscription->getId();
				$option->value = $subscription->getId();

				$options[] = $option;
			}
		}

		JHtml::_('formbehavior.chosen', '.pp-autocomplete', null);

		$attributes = 'multiple="true"';

		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('options', $options);

		$output = $theme->output('admin/helpers/form/usersubscriptions');

		return $output;
	}

	/**
	 * Generates a radio input
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function radio($name, $value, $checked, $label, $id = '', $attributes = [])
	{
		$attributes = $this->formatAttributes($attributes);
		$label = JText::_($label);

		$theme = PP::themes();
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('checked', $checked);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('label', $label);

		$output = $theme->output('admin/helpers/form/radio');

		return $output;
	}

	/**
	 * Generates a hidden input for return url
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function returnUrl($name = 'return', $value = '')
	{
		if (!$value) {
			$value = base64_encode(PP::getURI());
		}

		return $this->hidden($name, $value);
	}

	/**
	 * Renders the registration type form
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function registrationType($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$registration = PP::registration();

		$adapters = $registration->getAdapters();

		$options = [];

		foreach ($adapters as $adapter) {
			$options[$adapter] = JText::_('COM_PAYPLANS_REGISTRATION_TYPE_' . strtoupper($adapter));
		}

		$theme = PP::themes();
		$theme->set('options', $options);
		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/registration.type');

		return $output;
	}

	/**
	 * Renders the rewriter list
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function rewriter()
	{
		$theme = PP::themes();

		$output = $theme->output('admin/helpers/form/rewriter');

		return $output;
	}

	/**
	 * Renders a apps dropdown
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function apps($name, $apps = [], $editable = true, $attributes = '')
	{
		static $applist = null;

		if (is_null($applist)) {
			$applist = PP::model('app')->loadRecords();
		}

		$selections = [];
		$appIds = [];

		if ($apps) {
			foreach ($apps as $app) {
				$appIds[] = $app->app_id;
			}
		}

		if ($applist) {
			foreach ($applist as $app) {
				if ($app->published) {
					$obj = new stdClass();

					$obj->id = $app->app_id;
					$obj->title = $app->title;
					$obj->type = $app->type;
					$obj->selected = false;
					if (in_array($app->app_id, $appIds)) {
						$obj->selected = true;
					}

					$selections[] = $obj;
				}
			}
		} else if ($apps) {
			// just use the provided app as selections
			foreach ($apps as $app) {

				$obj = new stdClass();

				$obj->id = $app->app_id;
				$obj->title = $app->title;
				$obj->type = $app->type;
				$obj->selected = true;

				$selections[] = $obj;
			}
		}


		$theme = PP::themes();
		$theme->set('apps', $apps);
		$theme->set('name', $name);
		$theme->set('attributes', $attributes);
		$theme->set('editable', $editable);
		$theme->set('selections', $selections);

		$output = $theme->output('admin/helpers/form/apps');
		return $output;
	}

	/**
	 * Renders the groups dropdown
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function groups($name, $groups = [], $editable = true, $attributes = '', $multiple = true)
	{
		static $grouplist = null;

		if (is_null($grouplist)) {
			$grouplist = PP::model('group')->loadRecords();
		}

		$selections = [];
		$groupIds = [];

		if ($groups) {
			if (!is_array($groups)) {
				$groups = [$groups];
			}

			foreach ($groups as $group) {

				if (is_object($group)) {
					$groupIds[] = $group->group_id;

					continue;
				}

				$groupIds[] = $group;
			}
		}

		if ($grouplist) {
			foreach ($grouplist as $group) {
				if ($group->published) {
					$obj = new stdClass();

					$obj->id = $group->group_id;
					$obj->title = $group->title;
					$obj->selected = false;

					if (in_array($group->group_id, $groupIds)) {
						$obj->selected = true;
					}

					$selections[$obj->id] = $obj;
				}
			}
		} else if ($groups) {
			// just use the provided group as selections
			foreach ($groups as $group) {

				$obj = new stdClass();

				$obj->id = $group->group_id;
				$obj->title = $group->title;
				$obj->selected = true;

				$selections[$obj->id] = $obj;
			}
		}

		if ($multiple) {
			$name .= '[]'; 
		}

		$theme = PP::themes();
		$theme->set('multiple', $multiple);
		$theme->set('groups', $groups);
		$theme->set('name', $name);
		$theme->set('attributes', $attributes);
		$theme->set('editable', $editable);
		$theme->set('selections', $selections);

		$output = $theme->output('admin/helpers/form/groups');
		return $output;
	}

	/**
	 * Inserts a validation block
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function validate($message)
	{
		$theme = PP::themes();
		$theme->set('message', $message);

		$output = $theme->output('admin/helpers/form/validate');

		return $output;
	}

	/**
	 * Renders a form to browse user group
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function usergroups($name, $selected = array(), $id = null, $attributes = '', $options = [])
	{
		$model = PP::model('User');
		$groups = $model->getAllUserGroups();

		$multiple = isset($options['multiple']) ? $options['multiple'] : true;

		if (is_null($id)) {
			$id = self::normalizeId($name);
		}

		if (!is_array($selected)) {
			$selected = (array) $selected;
		}

		$readOnly = isset($options['readOnly']) ? $options['readOnly'] : false;

		if ($readOnly) {
			$readOnly = 'disabled="disabled"';
		}

		// Default width and height
		$minWidth = 350;
		$minHeight = 220;

		if (isset($options['minWidth'])) {
			$minWidth = $options['minWidth'];
		}

		if (isset($options['minHeight'])) {
			$minWidth = $options['minHeight'];
		}

		if ($multiple) {
			$name = $name . '[]';
		}

		$theme = PP::themes();
		$theme->set('minHeight', $minHeight);
		$theme->set('readOnly', $readOnly);
		$theme->set('id', $id);
		$theme->set('minWidth', $minWidth);
		$theme->set('name', $name);
		$theme->set('groups', $groups);
		$theme->set('selected', $selected);

		$output = $theme->output('admin/helpers/form/usergroups');

		return $output;
	}

	/**
	 * Renders the list of Joomla Articles
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function joomlaArticle($name, $selected = [], $id = null, $attributes = '', $options = [])
	{
		// Get all articles
		$db = PP::db();
		$query = 'SELECT `id`, `title` FROM ' . $db->qn('#__content');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (empty($result)) {
			return $this->notavailable('COM_PP_NO_JOOMLA_ARTICLES_MESSAGE');
		}

		$articles = [];

		foreach ($result as $article) {
			$articles[$article->id] = JText::_($article->title);
		}

		$multiple = FH::normalize($options, 'multiple', true);

		if ($multiple) {
			$name = $name . '[]';
		}

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $articles, ['attributes' => $attributes, 'multiple' => $multiple, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Render lists of joomla category
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function joomlaCategory($name, $selected = [], $id = null, $attributes = '', $options = [])
	{
		$db = PP::db();
		$query = 'SELECT `id`, `title` FROM ' . $db->qn('#__categories');
		$query .= ' WHERE `extension` = ' . $db->Quote('com_content');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if (empty($result)) {
			return $this->notavailable('COM_PP_NO_JOOMLA_CATEGORIES_MESSAGE');
		}

		$categories = [];

		foreach ($result as $category) {
			$categories[$category->id] = JText::_($category->title);
		}

		$multiple = FH::normalize($options, 'multiple', true);

		if ($multiple) {
			$name = $name . '[]';
		}

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $categories, ['attributes' => $attributes, 'multiple' => $multiple, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders modules selection
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function menus($name, $value = [], $id = null, $attributes = '')
	{
		$db = PP::db();

		require_once(realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php'));

		$menus = MenusHelper::getMenuLinks();

		JHtml::_('formbehavior.chosen', '.pp-autocomplete', null);

		if (!is_array($value)) {
			$value = [$value];
		}

		$theme = PP::themes();
		$theme->set('value', $value);
		$theme->set('menus', $menus);
		$theme->set('name', $name);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/menus');

		return $output;

	}


	/**
	 * Renders menus selection
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function menulist($name, $value = [], $id = null)
	{
		$db = PP::db();

		require_once(realpath(JPATH_ADMINISTRATOR . '/components/com_menus/helpers/menus.php'));

		$menus = MenusHelper::getMenuLinks();

		if (!is_array($value)) {
			$value = array($value);
		}

		$theme = PP::themes();
		$theme->set('selected', $value);
		$theme->set('menus', $menus);
		$theme->set('name', $name);

		$output = $theme->output('admin/helpers/form/menulist');

		return $output;

	}

	/**
	 * Renders article selection
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function articlelist($name, $value = [], $id = null, $attributes = "")
	{
		$db = PP::db();
		$query = 'SELECT `id`, `title` FROM ' . $db->qn('#__content');

		$db->setQuery($query);
		$articles = $db->loadObjectList();

		if (!is_array($value)) {
			$value = [$value];
		}

		$theme = PP::themes();
		$theme->set('selected', $value);
		$theme->set('articles', $articles);
		$theme->set('name', $name);
		$theme->set('attributes', $attributes);

		$output = $theme->output('admin/helpers/form/jarticlelist');

		return $output;

	}


	/**
	 * Renders modules selection
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function modules($name, $selected = [], $id = null, $attributes = '', $options = [])
	{
		$db = PP::db();

		$query = [];
		$query[] = 'SELECT m.`id`, m.`title` FROM ' . $db->qn('#__modules') . ' as m';
		$query[] = 'LEFT JOIN ' . $db->qn('#__extensions') . ' as e ON e.`element` = m.`module`';
		$query[] = 'AND e.`client_id` = m.`client_id`';
		$query[] = 'WHERE m.`published` = ' . $db->Quote(1);
		$query[] = 'AND e.`enabled` = ' . $db->Quote(1);

		// $now = PP::date();
		// $nullDate = $db->getNullDate();

		// $query[] = 'AND (m.`publish_up` = ' . $db->Quote($nullDate) . ' OR m.`publish_up` <= ' . $db->Quote($now) . ')';
		// $query[] = 'AND (m.`publish_down` = ' . $db->Quote($nullDate) . ' OR m.`publish_down` >= ' . $db->Quote($now) . ')';
		$query[] = 'AND m.`client_id` = ' . $db->Quote(0);
		$query[] = 'ORDER BY m.`title`';

		$db->setQuery($query);
		$modules = $db->loadObjectList('id');

		if (is_null($id)) {
			$id = self::normalizeId($name);
		}

		$theme = PP::themes();
		$theme->set('modules', $modules);
		$theme->set('name', $name);
		$theme->set('attributes', $attributes);
		$theme->set('selected', $selected);
		$theme->set('id', $id);

		$output = $theme->output('admin/helpers/form/modules');

		return $output;
	}

	/**
	 * Given a string, generate a valid id that can be used in forms
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public static function normalizeId($value)
	{
		$value = str_ireplace(array(' ', '.'), '-', $value);

		return $value;
	}

	/**
	* Render autocomplete form for Easysocial profile types
	*
	* @since	4.0.0
	* @access	public
	*/
	public function easysocialBadges($name, $selected, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if(!$id) {
			$id = $name;
		}

		$lib = PP::easysocial();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYSOCIAL_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		ES::language()->loadSite();

		$badges = $lib->getBadges();
		$options = [];

		foreach ($badges as $badge) {
			$options[$badge->id] = JText::_($badge->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	* Render list selection form for Easysocial groups
	*
	* @since	5.0
	* @access	public
	*/
	public function easysocialGroups($name, $selected = [], $id = null, $attributes = '', $options = [])
	{
		$formOptions = [
			'name' => $name,
			'selected' => $selected,
			'id' => $id,
			'attributes' => $attributes,
			'options' => $options
		];

		return $this->easysocialClusters('groups', $formOptions);
	}

	/**
	* Render list selection form for EasySocial Pages
	*
	* @since	5.0
	* @access	public
	*/
	public function easysocialPages($name, $selected = [], $id = null, $attributes = '', $options = [])
	{
		$formOptions = [
			'name' => $name,
			'selected' => $selected,
			'id' => $id,
			'attributes' => $attributes,
			'options' => $options
		];

		return $this->easysocialClusters('pages', $formOptions);
	}

	/**
	 * Method to render the list of easysocial's cluster selection based on the cluster type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function easysocialClusters($type, $formOptions = [])
	{
		$name = isset($formOptions['name']) ? $formOptions['name'] : false;
		$selected = isset($formOptions['selected']) ? $formOptions['selected'] : [];
		$id = isset($formOptions['id']) ? $formOptions['id'] : null;
		$attributes = isset($formOptions['attributes']) ? $formOptions['attributes'] : '';
		$options = isset($formOptions['options']) ? $formOptions['options'] : [];

		if (!$name) {
			return JText::_('Please specify the input name');
		}

		$lib = PP::easysocial();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYSOCIAL_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$multiple = isset($options['multiple']) ? $options['multiple'] : true;

		if (is_null($id)) {
			$id = self::normalizeId($name);
		}

		if (!is_array($selected)) {
			$selected = (array) $selected;
		}

		$readOnly = isset($options['readOnly']) ? $options['readOnly'] : false;

		if ($readOnly) {
			$attributes .= ' disabled="disabled" ';
		}

		if ($multiple) {
			$name = $name . '[]';
		}

		ES::language()->loadSite();

		$allowedType = ['groups', 'pages'];

		if (!in_array($type, $allowedType)) {
			return JText::_('Cluster type is not allowed');
		}

		$method = 'get' . ucfirst($type);
		$clusters = $lib->$method();
		$options = [];

		foreach ($clusters as $cluster) {
			$options[$cluster->id] = JText::_($cluster->title);
		}

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $selected, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	* Render autocomplete form for Easysocial profile types
	*
	* @since	4.2.0
	* @access	public
	*/
	public function easysocialProfileType($name, $value, $id = '', $attributes = '')
	{
		$attributes = $this->formatAttributes($attributes);

		if(!$id) {
			$id = $name;
		}

		$lib = PP::easysocial();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYSOCIAL_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$profileTypes = $lib->getProfileTypes();

		$options = [];

		foreach ($profileTypes as $profileType) {
			$options[$profileType->id] = JText::_($profileType->title);
		}

		$theme = PP::themes();
		$output = $theme->fd->html('form.dropdown', $name, $value, $options, ['attributes' => $attributes]);

		return $output;
	}

	/**
	 * Method to render the list of easysocial's cluster categories selection based on the cluster type
	 *
	 * @since	5.0
	 * @access	public
	 */
	public function easysocialClustersCategories($type, $formOptions = [])
	{
		$name = isset($formOptions['name']) ? $formOptions['name'] : false;
		$value = isset($formOptions['value']) ? $formOptions['value'] : [];
		$id = isset($formOptions['id']) ? $formOptions['id'] : null;
		$attributes = isset($formOptions['attributes']) ? $formOptions['attributes'] : '';

		$lib = PP::easysocial();
		$attributes = $this->formatAttributes($attributes);
		
		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYSOCIAL_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		$options = [];

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$categories = $lib->getClusterCategories($type);
		$options = [];

		foreach ($categories as $category) {
			$options[$category->id] = JText::_($category->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders the easysocial group's categories dropdown selection used in app params.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function easysocialGroupCategories($name, $value, $id = '', $attributes = '')
	{
		$formOptions = [
			'name' => $name,
			'value' => $value,
			'id' => $id,
			'attributes' => $attributes
		];

		return $this->easysocialClustersCategories('group', $formOptions);
	}

	/**
	 * Renders the easysocial page's categories dropdown selection used in app params.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function easysocialPageCategories($name, $value, $id = '', $attributes = '')
	{
		$formOptions = [
			'name' => $name,
			'value' => $value,
			'id' => $id,
			'attributes' => $attributes
		];

		return $this->easysocialClustersCategories('page', $formOptions);
	}

	/**
	 * Renders the easysocial event's categories dropdown selection used in app params.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function easysocialEventCategories($name, $value, $id = '', $attributes = '')
	{
		$formOptions = [
			'name' => $name,
			'value' => $value,
			'id' => $id,
			'attributes' => $attributes
		];

		return $this->easysocialClustersCategories('event', $formOptions);
	}

	/**
	 * Renders not available component
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function notavailable($text, $attributes = '')
	{
		$theme = PP::themes();

		$theme->set('attributes', $attributes);
		$theme->set('text', JText::_($text));
		$output = $theme->output('admin/helpers/form/not.available');

		return $output;
	}

	/**
	 * Renders the easysocial page's categories dropdown selection used in app params.
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function easysocialMarketplaceCategories($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::easysocial();
		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYSOCIAL_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$version = ES::getLocalVersion();

		if (version_compare($version, '4.0.0', '<')) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_EASYSOCIAL4_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		$options = [];

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$categories = $lib->getMarketplaceCategories();

		$options = [];

		foreach ($categories as $category) {
			$options[$category->id] = JText::_($category->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}


	/**
	 * Renders the subscription status dropdown selection used in app params.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function subscriptionstatus($name, $value, $id = '', $attributes = '')
	{
		$options = [];
		$attributes = $this->formatAttributes($attributes);

		$options[PP_SUBSCRIPTION_ACTIVE] = JText::_('COM_PP_SUBSCRIPTION_ACTIVE');
		$options[PP_SUBSCRIPTION_HOLD] = JText::_('COM_PP_SUBSCRIPTION_HOLD');
		$options[PP_SUBSCRIPTION_EXPIRED] = JText::_('COM_PP_SUBSCRIPTION_EXPIRED');
		// $options[PP_SUBSCRIPTION_NONE] = JText::_('COM_PP_SUBSCRIPTION_NONE');

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		// for now we hard code to allow multiple.
		$multiple = true;

		if ($multiple) {
			$name = $name . '[]';
		}

		$theme = PP::themes();
		$theme->set('selected', $value);
		$theme->set('name', $name);
		$theme->set('options', $options);
		$theme->set('attributes', $attributes);
		$theme->set('multiple', $multiple);
		$theme->set('id', $id);

		$output = $theme->output('admin/helpers/form/status');

		return $output;
	}

	/**
	 * Renders Acymailing list
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function acymailingLists($name, $value, $id = '', $attributes = '')
	{
		$theme = PP::themes();

		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::acymailing();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_ACYMAILING_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$lists = $lib->getLists();

		JHtml::_('formbehavior.chosen', '.pp-autocomplete', null);

		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('lists', $lists);

		$output = $theme->output('admin/helpers/form/acymailing.lists');

		return $output;
	}

	/**
	 * Renders an Vitruemart Shoppers group
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function shoppersGroups($name, $value, $id = '', $attributes = '')
	{
		$theme = PP::themes();

		$attributes = $this->formatAttributes($attributes);

		if (!$id) {
			$id = $name;
		}

		$lib = PP::virtuemart();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_VIRTUE_MART_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$groups = $lib->getGroups();

		JHtml::_('formbehavior.chosen', '.pp-autocomplete', null);

		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('groups', $groups);

		$output = $theme->output('admin/helpers/form/vitruemart.groups');

		return $output;
	}

	/**
	* Render autocomplete form for Easysocial profile types
	*
	* @since	4.0.0
	* @access	public
	*/
	public function jomsocialMultiprofile($name, $value, $id = '', $attributes = '')
	{
		$theme = PP::themes();

		$attributes = $this->formatAttributes($attributes);

		if(!$id) {
			$id = $name;
		}

		$lib = PP::jomsocial();

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PP_PLEASE_INSTALL_JOMSOCIAL_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		$profileTypes = $lib->getProfiles();
		$options = [];
		foreach ($profileTypes as $profileType) {
			$options[$profileType->id] = JText::_($profileType->name);
		}

		JHtml::_('formbehavior.chosen', '.pp-autocomplete', null);

		$theme->set('id', $id);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('attributes', $attributes);
		$theme->set('profileTypes', $options);

		$output = $theme->output('admin/helpers/form/jomsocial.multiprofile');

		return $output;
	}

	/**
	 * Renders offline payment methods
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function offlinePayment($name, $value = '', $id = '', $attributes = '', $options = [])
	{
		$attributes = $this->formatAttributes($attributes);

		$multiple = false;

		if (isset($options['multiple']) && $options['multiple']) {
			$multiple = true;
		}

		$options = [];

		// currently only 3 available payment method for offline
		$paymentMethods = [
			[
				'value' => 'Cash', 
				'title' => 'COM_PAYPLANS_CASH'
			],
			[
				'value' => 'Cheque', 
				'title' => 'COM_PP_CHEQUE'
			],
			[
				'value' => 'Wiretransfer', 
				'title' => 'COM_PP_WIRETRANSFER'
			]
		];

		$floatLabel = false;

		if (isset($options['floatLabel']) && $options['floatLabel']) {
			$floatLabel = true;
		}

		$theme = PP::themes();
		$theme->set('floatLabel', $floatLabel);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('value', $value);
		$theme->set('paymentMethods', $paymentMethods);
		$theme->set('multiple', $multiple);
		$output = $theme->output('admin/helpers/form/offlinepayment');

		return $output;
	}

	/**
	 * Renders a dropdown of language
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function language($name, $value = '', $id = '', $attributes = '', $options = [], $disabled = false)
	{
		$attributes = $this->formatAttributes($attributes);

		// Get Joomla language lists
		$languages = JLanguageHelper::createLanguageList('', JPATH_SITE, true, true);

		$floatLabel = false;

		if (isset($options['floatLabel']) && $options['floatLabel']) {
			$floatLabel = true;
		}

		$theme = PP::themes();
		$theme->set('floatLabel', $floatLabel);
		$theme->set('attributes', $attributes);
		$theme->set('name', $name);
		$theme->set('id', $id);
		$theme->set('value', $value);
		$theme->set('languages', $languages);
		$theme->set('multiple', false);
		$theme->set('allowAll', false);
		$theme->set('disabled', $disabled);
		$output = $theme->output('admin/helpers/form/language');

		return $output;
	}

	/**
	 * Renders Mailster groups
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function mailsterGroups($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::mailster();
		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_MAILSTER_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$groups = $lib->getGroups();

		$options = [];
		foreach ($groups as $group) {
			$options[$group->id] = JText::_($group->name);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders K2 Categories
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function k2Categories($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::k2();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_K2_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$categories = $lib->getCategories();

		$options = [];
		foreach ($categories as $category) {
			$options[$category->category_id] = JText::_($category->name);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;

	}

	/**
	 * Renders K2 Items
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function k2Items($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::k2();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_K2_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$items = $lib->getItems();

		$options = [];
		foreach ($items as $item) {
			$options[$item->item_id] = JText::_($item->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;

	}

	/**
	 * Renders K2 Items
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function k2Usergroups($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::k2();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_K2_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$groups = $lib->getK2UserGroups();

		$options = [];
		foreach ($groups as $group) {
			$options[$group->groups_id] = JText::_($group->name);
		}

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'theme' => 'fd']);

		return $output;

	}

	/**
	 * Renders Mosstes tree categories
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function mosetsCategories($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::mosets();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_MTREE_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$categories = $lib->getCategories();

		$options = [];
		foreach ($categories as $category) {
			$options[$category->category_id] = JText::_($category->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;

	}

	/**
	 * Renders Phoca categories
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function phocaCategories($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::phoca();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_PHOCA_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$categories = $lib->getCategories();

		$options = [];
		foreach ($categories as $category) {
			$options[$category->id] = JText::_($category->title);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders sobipro categories
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function sobiproCategories($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::sobipro();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_SOBIPRO_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$categories = $lib->getCategories();

		$options = [];
		foreach ($categories as $category) {
			$options[$category->cat_id] = JText::_($category->name);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders sobipro sections
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function sobiproSections($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::sobipro();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_SOBIPRO_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$sections = $lib->getSections();

		$options = [];
		foreach ($sections as $section) {
			$options[$section->sec_id] = JText::_($section->name);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}

	/**
	 * Renders Phoca categories
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function zooCategories($name, $value, $id = '', $attributes = '')
	{
		$lib = PP::zoo();

		$attributes = $this->formatAttributes($attributes);

		if (!$lib->exists()) {
			return $this->notavailable(JText::_('COM_PAYPLANS_PLEASE_INSTALL_ZOO_BEFORE_USING_THIS_APPLICATION'), $attributes);
		}

		if(!$id) {
			$id = $name;
		}

		if (!is_array($value)) {
			$value = PP::makeArray($value);
		}

		$categories = $lib->getCategories();

		$options = [];
		foreach ($categories as $category) {
			$options[$category->id] = JText::_($category->name);
		}

		$name = $name . '[]';

		$theme = PP::themes();
		$output = $theme->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);

		return $output;
	}
}