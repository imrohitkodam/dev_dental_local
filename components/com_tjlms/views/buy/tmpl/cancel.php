<?php
echo $msg = JText::_( 'OPERATION_CANCELLED' );
$user =JFactory::getUser();
$input=JFactory::getApplication()->input;
$eventid=$input->get('eventid','','INT');
$linkcreateevent='index.php?option=com_community&view=events&event='.$eventid;
$itemid=jticketinghelper::getitemid($linkcreateevent);
$linkcreateevent=JRoute::_(JURI::base().'?option=com_community&view=events&task=viewevent'.'&Itemid='.$itemid);
echo "<div style='float:right'><a href='".$linkcreateevent."'>".JText::_('BACK')."</a></div>";


