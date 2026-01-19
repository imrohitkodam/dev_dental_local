<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldfsjreportfilter extends JFormField
{
	protected $type = 'fsjreportfilter';
	static $counts;
	
	protected function getInput()
	{
		$this->data = json_decode($this->value, true);

		$output = array();

		$filters = array(
			'opened' => 'Open Date',
			'status' => 'Status',
			'product' => 'Product',
			'department' => 'Department',
			'handler' => 'Handler',
			'user' => 'User',
			'group' => 'Ticket Group');

		foreach ($filters as $key => $label)
		{
			$checked = "";
			if (isset($this->data[$key]))
				$checked = " checked";
			$output[] = '
			<div class="control-group" id="filter_' . $key . '">
				<div class="control-label">
					<label id="jfilter_' . $key . '-lbl" for="filter_' . $key . '" class="">
					'.$label.'</label>	
				</div>
				<div class="controls">';

			if ($key == "opened")
			{
				$options = array();
				$options[] = JHTML::_('select.option', '', 'None', 'id', 'title');
				$options[] = JHTML::_('select.option', 'opened', 'Opened', 'id', 'title');
				$options[] = JHTML::_('select.option', 'closed', 'Closed', 'id', 'title');
				$options[] = JHTML::_('select.option', 'lastupdate', 'Last Update', 'id', 'title');
				
				$output[] = JHTML::_('select.genericlist',  $options, "filter[" . $key . "]", 'style="width: 120px";', 'id', 'title', $this->data[$key]);
				
			} else {
				$output[] = '<input type="checkbox" name="filter[' . $key . ']" id="filter_' . $key . '" value="1" class="input-xxlarge" ' . $checked . '>';
			}

			$output[] = '	</div> 
			</div>		';
		}

		return implode($output);
	}
	
	function doSave($field, &$data)
	{
		$params = JRequest::getVar('filter');
		$data[$field] = json_encode($params);
	}		
}
