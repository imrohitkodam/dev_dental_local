/*------------------------------------------------------------------------
# mod_edocmancalendar - News Calendar
# ------------------------------------------------------------------------
# author    Dang Thuc Dam
# Copyright (C) 2018 www.joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomlahill.com
# Technical Support:  Forum - https://www.joomdonation.com/forum
-------------------------------------------------------------------------*/

function updateNewsCalendar(curmonth,curyear,mid) 
{	
	var currentURL = window.location;
	var live_site = currentURL.protocol+'//'+currentURL.host+sfolder;
	
	var loading = document.getElementById('monthyear_'+mid);
	
	loading.innerHTML='<img src="'+live_site+'/modules/mod_edocmancalendar/assets/loading.gif" border="0" align="absmiddle" />';
	
	var ajax = new XMLHttpRequest;
   	ajax.onreadystatechange=function()
  	{
		if (ajax.readyState==4 && ajax.status==200)
		{
			document.getElementById("newscalendar"+mid).innerHTML = ajax.responseText;
		}
  	}	
	ajax.open("GET",live_site+"/modules/mod_edocmancalendar/assets/ajax.php?month="+curmonth+"&year="+curyear+"&mid="+mid,true);
	ajax.send();
}