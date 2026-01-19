<?php
/**
 * @version    SVN: <svn_id>
 * @package    Plg_System_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
jimport('techjoomla.common');

/**
 * view of courses
 *
 * @since  1.0
 */
class TjlmsViewOrders extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  void
	 */
	public function display($tpl = null)
	{
		$canDo = TjlmsHelper::getActions();

		if (!$canDo->get('view.orders'))
		{
			JError::raiseError(500, JText::_('JERROR_ALERTNOAUTHOR'));

			return false;
		}

		$this->techjoomlacommon = new TechjoomlaCommon;
		$this->state = $this->get('State');
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		$input = JFactory::getApplication()->input;
		$this->comtjlmsHelper = new comtjlmsHelper;
		$user = JFactory::getUser();

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		TjlmsHelper::addSubmenu('orders');

		$this->addToolbar();

		$order_id = $input->get('orderid', '', 'STRING');
		$oid = $this->comtjlmsHelper->getIDFromOrderID($order_id);

		$order = $this->comtjlmsHelper->getorderInfo($oid);

		$this->order_authorized = 1;

		if ($user->id)
		{
			if (!empty($order))
			{
				$this->order_authorized = 1;
				$this->orderinfo = $order['order_info'];
				$this->orderitems = $order['items'];
				$this->orderview = 1;
			}
			else
			{
					$this->noOrderDetails = 1;
			}
		}

		$this->ComtjlmsHelper = new ComtjlmsHelper;

		// Get component params
		$this->lmsparams = $this->ComtjlmsHelper->getcomponetsParams('com_tjlms');

		$myorderslink = 'index.php?option=com_tjlms&view=orders&layout=my';
		$this->myorderslink = $this->comtjlmsHelper->getitemid($myorderslink);

		if (JVERSION >= '3.0')
		{
			$this->sidebar = JHtmlSidebar::render();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  Toolbar instance
	 *
	 * @since	1.0.0
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT . '/helpers/tjlms.php';

		$state = $this->get('State');

		if (JVERSION >= '3.0')
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_ORDERS'), 'list');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_TJLMS_TITLE_ORDERS'), 'orders.png');
		}

		// Show trash and delete for components that uses the state field
		JToolBarHelper::deleteList(JText::_('COM_TJLMS_SURE_DELETE'), 'orders.delete', 'JTOOLBAR_DELETE');

		// Set sidebar action - New in 3.0
		if (JVERSION >= '3.0')
		{
			JHtmlSidebar::setAction('index.php?option=com_tjlms&view=orders');
		}

		$this->extra_sidebar = '';
	}
}
