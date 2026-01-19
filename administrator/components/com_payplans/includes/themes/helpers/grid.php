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

class PPThemesHelperGrid extends PPThemesHelperAbstract
{
	/**
	 * Renders a check all checkbox
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function checkall()
	{
		$themes = PP::themes();
		$output = $themes->fd->html('table.checkAll');

		return $output;
	}

	/**
	 * Renders an empty block for table layout
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function emptyBlock($text, $columns, $center = false)
	{
		$theme = PP::themes();
		$theme->set('columns', $columns);
		$theme->set('text', $text);
		$theme->set('center', $center);

		$contents = $theme->output('admin/helpers/grid/empty.block');

		return $contents;
	}

	/**
	 * Renders a featured icon
	 *
	 * @since	4.1.2
	 * @access	public
	 */
	public static function featured($obj, $controllerName = '', $key = '', $tasks = [], $allowed = true, $tooltip = [], $classes = [])
	{

		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty($key) ? $key : 'default';

		$classes += [
			-1 => 'trash',
			0 => 'default',
			1 => 'featured'
		];

		$tasks += [
			-1 => 'featured',
			0 => 'featured',
			1 => 'unfeatured'
		];

		$tooltips = [
			-1 => 'COM_PP_GRID_TOOLTIP_TRASHED_ITEM',
			0 => 'COM_PP_GRID_TOOLTIP_FEATURE_ITEM',
			1 => 'COM_PP_GRID_TOOLTIP_UNFEATURE_ITEM'
		];

		$class = isset($classes[$obj->$key]) ? $classes[$obj->$key] : '';
		$task = isset($tasks[$obj->$key]) ? $controllerName . '.' . $tasks[$obj->$key] : '';
		$tooltip = isset($tooltips[$obj->$key]) ? JText::_($tooltips[$obj->$key]) : '';

		$theme = PP::themes();
		$theme->set('allowed', $allowed);
		$theme->set('tooltip', $tooltip);
		$theme->set('task', $task);
		$theme->set('class', $class);


		return $theme->output('admin/helpers/grid/featured');
	}

	/**
	 * Renders a checkbox for each row in a table
	 *
	 * @since	3.7.0
	 * @access	public
	 */
	public function id($number, $id, $allowed = true, $checkedOut = false, $name = 'cid')
	{
		$themes = PP::themes();
		$output = $themes->fd->html('table.id', $number, $id, $allowed, $checkedOut, $name);

		return $output;
	}

	/**
	 * Renders the ordering column for table output
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function ordering($total, $current, $showOrdering = '', $ordering = 0, $controllerName = '')
	{
		$theme = PP::themes();

		$theme->set('current', $current);
		$theme->set('total', $total);
		$theme->set('ordering', $ordering);
		$theme->set('showOrdering', $showOrdering);
		$theme->set('controller', $controllerName);

		$contents = $theme->output('admin/helpers/grid/ordering');

		return $contents;
	}

	/**
	 * Renders the order save button in a grid
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function order($rows, $controllerName = '')
	{
		$count = count($rows);

		if (!$rows || !$count) {
			return '';
		}

		$task = $controllerName.'.saveorder';

		$themes = PP::themes();
		$output = $themes->fd->html('table.saveOrder', $task);

		return $output;
	}

	/**
	 * Renders the pagination for tables
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function pagination(PPPagination $pagination, $columns)
	{
		$theme = PP::themes();
		$theme->set('columns', $columns);
		$theme->set('pagination', $pagination);

		$contents = $theme->output('admin/helpers/grid/pagination');

		return $contents;
	}

	/**
	 * Renders icon for publishing state
	 *
	 * @since	3.7.0
	 * @access	public
	 */
	public function published($obj, $controllerName = '', $key = '', $tasks = [], $tooltips = [], $classes = [], $allowed = true)
	{
		$fd = PP::fd();

		// If primary key is not provided, then we assume that we should use 'state' as the key property.
		$key = !empty($key) ? $key : 'state';

		// array_replace is only supported php>5.3
		// While array_replace goes by base, replacement
		// Using + changes the order where base always goes last

		$classes += [
			-1 => 'trash',
			0 => 'unpublish',
			1 => 'publish'
		];

		$tasks += [
			-1 => 'publish',
			0 => 'publish',
			1 => 'unpublish'
		];

	
		if (!$tooltips) {
			$tooltips = [
				-1 => 'COM_PP_GRID_TOOLTIP_TRASHED_ITEM',
				0 => 'COM_PP_GRID_TOOLTIP_PUBLISH',
				1 => 'COM_PP_GRID_TOOLTIP_UNPUBLISH'
			];
		}

		$class = isset($classes[$obj->$key]) ? $classes[$obj->$key] : '';
		$task = isset($tasks[$obj->$key]) ? $controllerName . '.' . $tasks[$obj->$key] : '';
		$tooltip = isset($tooltips[$obj->$key]) ? JText::_($tooltips[$obj->$key]) : '';

		$theme = PP::themes();
		$theme->set('allowed', $allowed);
		$theme->set('tooltip', $tooltip);
		$theme->set('task', $task);
		$theme->set('class', $class);

		return $fd->html('table.published', $class, $allowed, [
			'task' => $task,
			'tooltip' => $tooltip
		]);
	}

	/**
	 * Renders a sortable column for table heading
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function sort($column, $text, $currentOrdering, $direction = '')
	{
		$text = JText::_($text);
		$ordering = $currentOrdering;

		if (is_object($currentOrdering) && isset($currentOrdering->direction)) {
			$direction = $currentOrdering->direction;
		}

		if (is_object($currentOrdering) && isset($currentOrdering->ordering)) {
			$ordering = $currentOrdering->ordering;
		}

		// Ensure that the direction is always in lowercase because we will check for it in the theme file.
		$direction = PPJString::strtolower($direction);
		$ordering = PPJString::strtolower($ordering);
		$column = PPJString::strtolower($column);

		$themes = PP::themes();
		$ouput = $themes->fd->html('table.sort', $text, $column, $ordering, $direction);

		return $ouput;
	}
}
