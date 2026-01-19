<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'helper.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'translate.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'fields.php');

class JFormFieldfsjreportfield extends JFormField
{
	protected $type = 'fsjreportfield';
	static $counts;
	
	protected function getInput()
	{
		$this->data = json_decode($this->value, true);

		return $this->buildTable();
	}	

	function buildTable()
	{
		$output = array();

		$output[] = "<style>
			.field-table input {
				margin-bottom: 0;
			}
		</style>
		";

		$output[] = "<table class='table table-bordered table-condensed field-table'>";
		$output[] = "<tr>";
		$output[] = "<th></th>";
		$output[] = "<th>Field</th>";
		$output[] = "<th>Label</th>";
		$output[] = "<th>Row</th>";
		$output[] = "<th>Span</th>";
		$output[] = "<th>Format</th>";
		$output[] = "<th>Style</th>";
		$output[] = "<th>Blank</th>";
		$output[] = "<th nowrap>No Wrap</th>";
		$output[] = "</tr>";
		$output[] = "<tbody>";
		$output[] = $this->buildFields();
		$output[] = "</tbody>";

		$output[] = "</table>";

		$js = "
			jQuery(document).ready( function () {
				jQuery('.field-table tbody').sortable();
			});
		";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration( $js );
		FSJ_Page::JQueryUI(array('sortable'));

		return implode($output);
	}

	function buildFields()
	{
		$field = array();
		$field["title"] = $this->buildField("title", "Title", array('label' => 'Title', 'style' => 'font-weight:bold;font-size:110%;'));
		$field["time"] = $this->buildField("time", "Ticket Time Taken", array('label' => 'Time'));

		$field["user"] = $this->buildField("user", "Name (username)", array('label' => 'User'));
		$field["user_username"] = $this->buildField("user_username", "Users Username", array('label' => 'Username'));
		$field["user_name"] = $this->buildField("user_name", "Users Name", array('label' => 'Name'));
		$field["user_email"] = $this->buildField("user_email", "Users EMail", array('label' => 'EMail'));

		$field["handler"] = $this->buildField("handler", "Handler Name (username)", array('label' => 'Handler'));
		$field["handler_username"] = $this->buildField("handler_username", "Handler Username", array('label' => 'Handler Username'));
		$field["handler_name"] = $this->buildField("handler_name", "Handler Name", array('label' => 'Handler Name'));
		$field["handler_email"] = $this->buildField("handler_email", "Handler EMail", array('label' => 'Handler EMail'));

		$field["reference"] = $this->buildField("reference", "Ticket Reference", array('label' => 'Ref'));
		$field["opened"] = $this->buildField("opened", "Opened Date", array('label' => 'Opened', 'format' => 'date', 'dateformat' => 'M j, Y', 'blank' => ' --- '));
		$field["closed"] = $this->buildField("closed", "Closed Date", array('label' => 'Closed', 'format' => 'date', 'dateformat' => 'M j, Y', 'blank' => ' --- '));
		$field["lastupdate"] = $this->buildField("lastupdate", "Last Updated", array('label' => 'Last Updated', 'format' => 'date', 'dateformat' => 'M j, Y', 'blank' => ' --- '));

		$field["product"] = $this->buildField("product", "Product Name", array('label' => 'Product'));
		$field["department"] = $this->buildField("department", "Department Name", array('label' => 'Department'));
		$field["category"] = $this->buildField("category", "Category Name", array('label' => 'Category'));
		$field["priority"] = $this->buildField("priority", "Priority Name", array('label' => 'Priority'));
		$field["status"] = $this->buildField("status", "Status Name", array('label' => 'Status', 'style' => 'font-weight:bold;'));

		$cust_fields = FSSCF::GetAllCustomFields();

		foreach ($cust_fields as $cust_field)
		{
			$field["custom" . $cust_field['id']] = $this->buildField("custom" . $cust_field['id'], "CF: " . $cust_field['description'], array('label' => $cust_field['description'], 'format' => 'custfield'));
		}

		$output = array();

		if ($this->data && is_array($this->data))
		{
			foreach ($this->data as $key => $value) $output[] = $field[$key];
			foreach ($this->data as $key => $value) unset($field[$key]);
		}
		
		foreach ($field as $fieldhtml) $output[] = $fieldhtml;

		return implode($output);
	}

	function buildField($id, $title, $params)
	{
		$this->paramsBlanks($params);

		if (isset($this->data[$id]))
		{
			foreach ($this->data[$id] as $field => $value)
			{
				$params[$field] = $value;
			}
		}
		
		if (empty($params['row'])) $params['row'] = 0;
		if (empty($params['span'])) $params['span'] = 1;
		if (empty($params['format'])) $params['format'] = '';
		if (empty($params['blank'])) $params['blank'] = '';
		if (empty($params['nowrap'])) $params['nowrap'] = 0;
		if (empty($params['style'])) $params['style'] = 0;

		$output = array();
		$output[] = "<tr>";

		$output[] = "<td class='.handle'><i class='icon-menu'></i></td>";
		$output[] = "<td width='75%'>" . $title . "</td>";
		$output[] = "<td><input type='text' class='input-xlarge' name='field[{$id}][label]' value='" . htmlentities($params['label']) . "' /></td>";
		$output[] = "<td>" . $this->buildRowDropdown($id, null, $params['row']) . "</td>";
		$output[] = "<td>" . $this->buildSpanDropdown($id, null, $params['span']) . "</td>";
		$output[] = "<td>" . $this->buildFormatDropdown($id, null, $params['format']) . "</td>";
		$output[] = "<td><input class='input-xlarge' type='text' name='field[{$id}][style]' value='" . htmlentities($params['style']) . "' /></td>";
		$output[] = "<td><input type='text' name='field[{$id}][blank]' value='" . htmlentities($params['blank']) . "' /></td>";
		$output[] = "<td style='text-align: center'><input type='checkbox' name='field[{$id}][nowrap]' value='1' " . (($params['nowrap']) ? 'checked' : '') . " /></td>";

		return implode($output);
	}

	static $orderno = 1;

	function paramsBlanks($params)
	{
		if (empty($params['row'])) $params['row'] = 1;
		if (empty($params['span'])) $params['span'] = 1;
		if (empty($params['format'])) $params['format'] = '';
		if (empty($params['style'])) $params['style'] = '';
		if (empty($params['blank'])) $params['blank'] = '';
		if (empty($params['nowrap'])) $params['nowrap'] = 0;
	}

	function buildRowDropdown($id, $current, $default)
	{
		$value = $current ? $current : $default;

		$options = array();
		$options[] = JHTML::_('select.option', '', 'Hide', 'id', 'title');
		for ($i = 1 ; $i < 10 ; $i++)
		{
			$options[] = JHTML::_('select.option', $i, 'Row ' . $i, 'id', 'title');
		}

		return JHTML::_('select.genericlist',  $options, "field[{$id}][row]", 'style="width: 80px";', 'id', 'title', $value);
	}

	function buildSpanDropdown($id, $current, $default)
	{
		$value = $current ? $current : $default;

		$options = array();
		for ($i = 1 ; $i < 10 ; $i++)
		{
			$options[] = JHTML::_('select.option', $i, 'Span ' . $i, 'id', 'title');
		}

		return JHTML::_('select.genericlist',  $options, "field[{$id}][span]", 'style="width: 80px";', 'id', 'title', $value);
	}

	function buildFormatDropdown($id, $current, $default)
	{
		$value = $current ? $current : $default;

		$options = array();
		$options[] = JHTML::_('select.option', '', 'Auto', 'id', 'title');
		$options[] = JHTML::_('select.option', 'date', 'Date', 'id', 'title');
		$options[] = JHTML::_('select.option', 'custfield', 'Custom Field', 'id', 'title');
		
		return JHTML::_('select.genericlist',  $options, "field[{$id}][format]", 'style="width: 120px";', 'id', 'title', $value);
	}
	
	function doSave($field, &$data)
	{
		$params = JRequest::getVar('field');
		$data[$field] = json_encode($params);
	}
}
