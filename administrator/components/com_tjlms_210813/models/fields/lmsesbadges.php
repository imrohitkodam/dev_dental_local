<?php
/**
 * @version    SVN: <svn_id>
 * @package    Tjlms
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('JPATH_BASE') or die;
JFormHelper::loadFieldClass('list');

/**
 * Supports an HTML select list of categories
 *
 * @since  1.0.0
 */
class JFormFieldLmsesbadges extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since	1.6
	 */
	protected $type = 'lmsesbadges';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array		An array of JHtml options.
	 *
	 * @since   11.4
	 */
	protected function getInput()
	{
		$db = JFactory::getDbo();
		$input = JFactory::getApplication()->input;
		$query = $db->getQuery(true);
		$query->select('a.*');
		$query->from('`#__social_badges` AS a');
		$query->where('extension="com_tjlms"');
		$db->setQuery($query);
		$esbadges = $db->loadObjectList();

		$options = array();
		$prop = " class='inputbox' ";
		$options[] = JHTML::_('select.option', '', JText::_('COM_TJLMS_SELECT'));

		foreach ($esbadges as $key => $obj)
		{
			$options[] = JHTML::_('select.option', $obj->id, $obj->title);
		}

		if (!$this->value)
		{
			$this->value = $options[0]->value;
		}

		$dropdown = JHTML::_('select.genericlist', $options, $this->name, $prop, 'value', 'text', $this->value);

		return $dropdown;
	}
}
