<?php

/**
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (C) 2007 - 2017 Extly, CB. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        http://www.extly.com http://support.extly.com
 */
// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Utility class for creating HTML Grids
 *
 * @package     Extly.Library
 * @subpackage  lib_extly - Extly Framework
 * @since       3.0
 */
abstract class EHtmlGrid
{
	const IMG_ICON_YES = '<i style="color: green;" class="xticon xticon-check"></i>';

	const IMG_ICON_NO = '<i style="color: red;" class="xticon xticon-circle-o"></i>';

	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value     Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i         The index
	 * @param   bool     $withLink  Param
	 *
	 * @return  string
	 */
	public static function published($value, $i, $withLink = true)
	{
		return self::publishedWithIcons($value, $i, $withLink);
	}

	/**
	 * Method to create a clickable icon to change the state of an item
	 *
	 * @param   mixed    $value     Either the scalar value or an object (for backward compatibility, deprecated)
	 * @param   integer  $i         The index
	 * @param   bool     $withLink  Param
	 *
	 * @return  string
	 */
	public static function publishedWithIcons($value, $i, $withLink = false)
	{
		if (is_object($value))
		{
			$value = $value->published;
		}

		$img = $value ? self::IMG_ICON_YES : self::IMG_ICON_NO;

		if (!$withLink)
		{
			return $img;
		}

		$task = $value ? 'unpublish' : 'publish';
		$alt = $value ? JText::_('JPUBLISHED') : JText::_('JUNPUBLISHED');
		$action = $value ? JText::_('JLIB_HTML_UNPUBLISH_ITEM') : JText::_('JLIB_HTML_PUBLISH_ITEM');
		$href = '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $action . '">' . $img . '</a>';

		return $href;
	}

	/**
	 * Method to create a icon
	 *
	 * @param   mixed   $locked     Param
	 * @param   string  $img1       Param
	 * @param   string  $img0       Param
	 * @param   bool    $optimized  Param
	 *
	 * @return  string
	 */
	public static function lockedWithIcons($locked, $img1 = '<i class="xticon xticon-lock"></i>', $img0 = '<i class="xticon xticon-unlock"></i>', $optimized = true)
	{
		if (($optimized) && (!$locked))
		{
			return null;
		}

		$img = $locked ? $img1 : $img0;
		$alt = $locked ? JText::_('JLOCKED') : JText::_('JUNLOCKED');

		// Return JHtml::_('image', $img, $alt, null, true);

		return $img;
	}

	/**
	 * ajaxOrderingInit
	 *
	 * @param   string  $option       Param
	 * @param   string  $orderDir     Param
	 * @param   string  $listTableId  Param
	 * @param   string  $formId       Param
	 *
	 * @return  string
	 */
	public static function ajaxOrderingInit($option, $orderDir, $listTableId = 'itemsList', $formId = 'adminForm')
	{
		$saveOrderingUrl = 'index.php?option=' . $option . '&task=saveorder';
		JHtml::_('sortablelist.sortable', $listTableId, $formId, strtolower($orderDir), $saveOrderingUrl);
	}

	/**
	 * ajaxOrderingColumn
	 *
	 * @param   bool  $editstate  Param
	 * @param   int   $ordering   Param
	 *
	 * @return  string
	 */
	public static function ajaxOrderingColumn($editstate, $ordering)
	{
		$output = array();
		$output[] = '<td class="order nowrap center hidden-phone">';

		if ($editstate)
		{
			$output[] = '<span class="sortable-handler iactive" >';
		}
		else
		{
			$disabledLabel = JText::_('JORDERINGDISABLED');
			$disableClassName = 'inactive tip-top';

			$output[] = '<span class="sortable-handler ';
			$output[] = $disableClassName;
			$output[] = '" title="';
			$output[] = $disabledLabel;
			$output[] = '" rel="tooltip">';
		}

		$output[] = '<i class="icon-menu"></i></span>';
		$output[] = '<input type="text" name="order[]" value="';
		$output[] = $ordering;
		$output[] = '" style="display:none;" />';
		$output[] = '</span>';
		$output[] = '</td>';

		return implode('', $output);
	}

	/**
	 * basicOrderingColumn
	 *
	 * @param   string  &$pagination  Param
	 * @param   string  $i            Param
	 * @param   string  $count        Param
	 * @param   string  $ordering     Param
	 *
	 * @return  string
	 */
	public static function basicOrderingColumn(&$pagination, $i, $count, $ordering)
	{
		$j3 = (EXTLY_J3);

		$output = array();
		$output[] = '<td class="order span2" align="center">';
		$output[] = '<span>';

		$orderup = $pagination->orderUpIcon($i, true, 'orderup', 'Move Up', $ordering);

		if (($j3) && ($orderup == '&#160;'))
		{
			$output[] = '<span><a class="disabled btn btn-micro"  href="#"><i class="icon-empty"></i></a></span>';
		}
		else
		{
			$output[] = $orderup;
		}

		$output[] = '</span>';
		$output[] = '<span>';

		$orderdown = $pagination->orderDownIcon($i, $count, true, 'orderdown', 'Move Down', $ordering);

		if (($j3) && ($orderdown == '&#160;'))
		{
			$output[] = '<span><a class="disabled btn btn-micro"  href="#"><i class="icon-empty"></i></a></span>';
		}
		else
		{
			$output[] = $orderdown;
		}

		$output[] = '</span>';

		$disabled = ($ordering !== null) ? '' : 'disabled="disabled"';
		$output[] = ' <input type="text" name="order[]" size="5" value="';
		$output[] = $ordering;
		$output[] = '" ' . $disabled;
		$output[] = ' class="text_area input-ordering" style="text-align: center; width:auto;" />';
		$output[] = '</td>';

		return implode('', $output);
	}
}
