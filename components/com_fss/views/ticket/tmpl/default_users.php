<?php
/**
* @Copyright Freestyle Joomla (C) 2010
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*     
* This file is part of Freestyle Support Portal
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/
?>
<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php echo FSS_Helper::PageStylePopup(); ?>
<?php echo FSS_Helper::PageTitle("INCLUDE_USER"); ?>
<div><?php echo JText::_('INCLUDE_USER_HELP'); ?></div>
<form action="<?php echo JRoute::_( 'index.php?option=com_fss&view=ticket&tmpl=component&what=addccuser&ticketid=' . JRequest::getVar('ticketid') );?>" method="post" name="fssForm">
<div id="editcell">
	<table>
		<tr>
			<td width="100%">
				<?php echo JText::_("SEARCH"); ?>:
				<input type="text" name="search" id="search" value="<?php echo JView::escape($this->search);?>" onchange="document.fssForm.submit();"/><br>
				<button onclick="this.form.submit();"><?php echo JText::_("GO"); ?></button>
				<button onclick="document.getElementById('search').value='';this.form.submit();this.form.getElementById('faq_cat_id').value='0';"><?php echo JText::_("RESET"); ?></button>
			</td>
		</tr>
	</table>

    <table class="fss_table" cellpadding="0" cellspacing="0" width="100%">
    <thead>

        <tr>
			<th width="5">#</th>
            <th nowrap="nowrap" style="text-align:left;">
                <?php echo JHTML::_('grid.sort',   'User_ID', 'question', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
            </th>
			<th nowrap="nowrap" style="text-align:left;">
				<?php echo JHTML::_('grid.sort',   'User_Name', 'title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th nowrap="nowrap" style="text-align:left;">
				<?php echo JHTML::_('grid.sort',   'EMail', 'title', @$this->lists['order_Dir'], @$this->lists['order'] ); ?>
			</th>
			<th nowrap="nowrap" style="text-align:left;">
				<?php echo JText::_("PICK"); ?>
			</th>
		</tr>
    </thead>
    <?php
    if (count($this->users) == 0)
    {
    	echo "<tr><td colspan=6>" . JText::_('NO_USERS_FOUND') . "</td></tr>";	
    }
    $k = 0;
    foreach ($this->users as $user)
    {
        //$link = JRoute::_( 'index.php?option=com_fss&controller=faq&task=edit&cid[]='. $row->id );

        ?>
        <tr class="<?php echo "row$k"; ?>">
            <td>
                <?php echo $user->id; ?>
            </td>
            <td>
                <?php echo $user->username; ?>
            </td>
            <td>
                <?php echo $user->name; ?>
            </td>
            <td>
                <?php echo $user->email; ?>
			</td>
			<td>
                <a href="#" class='pick_user' id="user_<?php echo $user->id; ?>"><?php echo JText::_("PICK"); ?></a>
            </td>
		</tr>
        <?php
        $k = 1 - $k;
    }
    ?>
	<tfoot>
		<tr>
			<td colspan="9"><?php echo $this->pagination->getListFooter(); ?></td>
		</tr>
	</tfoot>

    </table>
</div>
<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
</form>

<?php include "components/com_fss/_powered.php" ?>
<?php echo FSS_Helper::PageStylePopupEnd(); ?>

<script>
$fjq(document).ready(function () {
	$fjq('.pick_user').click(function(ev) { 
		ev.preventDefault();
		window.parent.AddCCUser($fjq(this).attr('id').split('_')[1]);
	});
});
</script>