<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class ToolbarSearch
{
	public $adapter = null;

	public function __construct()
	{
		$this->adapter = $this->getAdapter();

		if (is_a($this->adapter, 'ToolbarAdapterGlobal')) {
			$this->adapter = FDT::getAdapter(FDT::getMainComponent());
		}
	}

	/**
	 * Responsible to prepare the output.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function render()
	{
		if (!$this->adapter->showSearch()) {
			return;
		}

		// Do not render anything on mobile since it will conflict with the dialog.
		if (FH::responsive()->isMobile() || FH::responsive()->isTablet()) {
			return;
		}

		$themes = FDT::themes();
		return $themes->output('search/default', [
			'component' => $this->adapter->getComponent(),
			'task' => $this->adapter->getTask(),
			'itemid' => $this->adapter->getSearchRoute()
		]);
	}

	public function getAdapter()
	{
		// Retrieve the search behavior of the toolbar.
		$behavior = FDT::config()->get('defaultSearch', 'search-default');

		if ($behavior === 'search-default') {
			$component = JFactory::getApplication()->input->get('option', '');

			if ($component === 'com_ajax') {
				$component = JFactory::getApplication()->input->get('component', '');
			}

			return FDT::getAdapter($component);
		}

		return FDT::getAdapter(str_replace('search-', 'com_', $behavior));
	}

	public function categories($args = [])
	{
		if (!$this->getAdapter()->showCategoriesFilter()) {
			return;
		}

		$class = FH::normalize($args, 'class', '');

		$themes = FDT::themes();
		return $themes->output('search/categories', [
			'component' => $this->getAdapter()->getComponent(),
			'class' => $class
		]);
	}

	public function categoriesItems($id, $adapter)
	{
		$themes = FDT::themes();
		return $themes->output('search/ajax/categories.items', [
			'adapter' => $adapter,
			'categories' => $adapter->getChildCategories($id),
		]);
	}

	public function filter($args = [])
	{
		if (!$this->getAdapter()->showFilter()) {
			return;
		}

		$class = FH::normalize($args, 'class', '');

		$searchLib = ES::search();
		$filters = $searchLib->getFilters();

		$themes = FDT::themes();
		return $themes->output('search/filter', [
			'filters' => $filters,
			'class' => $class
		]);
	}

	public function input($args = [])
	{
		if (!$this->adapter->showSearch()) {
			return;
		}

		$class = FH::normalize($args, 'class', '');
		$placeholder = FH::normalize($args, 'placeholder', JText::_('MOD_SI_TOOLBAR_SEARCH_DEFAULT'));

		$themes = FDT::themes();
		return $themes->output('search/input', [
			'queryName' => $this->adapter->getQueryName(),
			'query' => $this->adapter->getSearchQuery(),
			'component' => $this->adapter->getComponent(),
			'header' => $this->adapter->getSuggestion() ? 'MOD_SI_TOOLBAR_SUGGESTED_KEYWORDS' : 'MOD_SI_TOOLBAR_SEARCH_RESULT',
			'class' => $class,
			'placeholder' => $placeholder,
		]);
	}
}