<?php

class FSS_Report_Generate_TicketList extends FSS_Report_Generate {
	var $title = 'Ticket List Report';

	function addLinks($attr)
	{
		if ($attr->name == 'title') $attr->link = 'index.php?option=com_fss&amp;view=admin_support&amp;layout=ticket&amp;ticketid={id}';
		if ($attr->name == 'user') $attr->link = 'index.php?option=com_fss&amp;view=admin_support&amp;searchtype=advanced&amp;what=search&amp;status=&amp;username={ticketusername}';
		if ($attr->name == 'handler') $attr->link = 'index.php?option=com_fss&amp;view=admin_support&amp;searchtype=advanced&amp;what=search&amp;status=&amp;handler={admin_id}';
		if ($attr->name == 'product') $attr->link = 'index.php?option=com_fss&amp;view=admin_support&amp;searchtype=advanced&amp;what=search&amp;status=&amp;product={prod_id}';
		if ($attr->name == 'department') $attr->link = 'index.php?option=com_fss&amp;view=admin_support&amp;searchtype=advanced&amp;what=search&amp;status=&amp;department={ticket_dept_id}';
		if ($attr->name == 'category') $attr->link = 'index.php?option=com_fss&amp;view=admin_support&amp;searchtype=advanced&amp;what=search&amp;status=&amp;category={ticket_cat_id}';
		if ($attr->name == 'status') $attr->link = 'index.php?option=com_fss&amp;view=admin_support&amp;tickets={ticket_status_id}';
	}
}