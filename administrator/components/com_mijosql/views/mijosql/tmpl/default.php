<?php
/**
* @version		1.0.0
* @package		MijoSQL
* @subpackage	MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceSQL www.joomace.net
*
* Based on EasySQL Component
* @copyright (C) 2008 - 2011 Serebro All rights reserved
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL
* @link http://www.lurm.net
*/

//No Permision
defined('_JEXEC') or die('Restricted access');

?>

<script language="javascript" type="text/javascript">
<!--
function changeQuery(thiz) {
	limit = 'LIMIT ' + document.getElementById('ja_lim_p').value;
	sel = document.getElementById('ja_sel_table_p').value;
	
	if (sel != 'SELECT * FROM ') {
		limit = '';
	}
	
	table = '';
   
	if (sel == 'SELECT * FROM table_name PROCEDURE ANALYSE()') {
		table = document.getElementById('ja_tbl_p').value;
		document.getElementById('ja_qry_p').value = 'SELECT * FROM '+table+' PROCEDURE ANALYSE()';
		return;
	}
   
	if (sel == 'SELECT * FROM ' ||
		sel == 'SHOW KEYS FROM ' ||
		sel == 'SHOW FIELDS FROM ' ||
		sel == 'REPAIR TABLE ' ||
		sel == 'OPTIMIZE TABLE ' ||
		sel == 'CHECK TABLE ' ||
		sel == 'SHOW FULL COLUMNS FROM ' ||
		sel == 'SHOW INDEX FROM ' ||
		sel == 'SHOW TABLE STATUS ' ||
		sel == 'SHOW CREATE TABLE ' ||
		sel == 'ANALYZE TABLE ') {
		table = document.getElementById('ja_tbl_p').value+' '+limit;
	}
	
	document.getElementById('ja_qry_p').value = sel + table;
}
//-->
</script>

<form id="adminForm" name="adminForm" action="index.php?option=com_mijosql" method="post">
	<table width="100%" border="0" cellsppacing="0" cellpadding="5">
		<tr>
			<td>
				<?php echo JText::_('COM_MIJOSQL_COMMAND').': '; ?>
				<select id="ja_sel_table_p" class="inputbox" style="width:250px;" onchange="changeQuery(this);">
					<optgroup label="SQL commands">
						<option value="SELECT * FROM ">SELECT *</option>
						<option value="SHOW DATABASES ">SHOW DATABASES~</option>
						<option value="SHOW TABLES ">SHOW TABLES~</option>
						<option value="SHOW FULL COLUMNS FROM ">SHOW COLUMNS</option>
						<option value="SHOW INDEX FROM ">SHOW INDEX</option>
						<option value="SHOW TABLE STATUS ">SHOW TABLE STATUS~</option>
						<option value="SHOW STATUS ">SHOW STATUS~</option>
						<option value="SHOW VARIABLES ">SHOW VARIABLES</option>
						<option value="SHOW LOGS ">SHOW LOGS (BDB - Berkeley DB)</option>
						<option value="SHOW FULL PROCESSLIST ">SHOW PROCESSLIST</option>
						<option value="SHOW GRANTS FOR ">SHOW GRANTS FOR username</option>
						<option value="SHOW CREATE TABLE ">SHOW CREATE TABLE</option>
						<option value="SHOW MASTER STATUS ">SHOW MASTER STATUS</option>
						<option value="SHOW MASTER LOGS ">SHOW MASTER LOGS</option>
						<option value="SHOW SLAVE STATUS ">SHOW SLAVE STATUS</option>
						<option value="SHOW KEYS FROM ">SHOW KEYS</option>
						<option value="SHOW FIELDS FROM ">SHOW FIELDS</option>
						<option value="REPAIR TABLE ">REPAIR TABLE</option>
						<option value="OPTIMIZE TABLE ">OPTIMIZE TABLE</option>
						<option value="CHECK TABLE ">CHECK TABLE</option>
						<option value="SELECT * FROM table_name PROCEDURE ANALYSE() ">SELECT * FROM ... PROCEDURE ANALYSE()~</option>
						<option value="ANALYZE TABLE ">ANALYZE TABLE</option>
					</optgroup>
					<optgroup label="Non SQL commands">
						<option value='REPLACE PREFIX `<?php echo $this->prefix ?>` TO `newprefix_`'>REPLACE PREFIX <?php echo $this->prefix ?> TO</option>
					</optgroup>
				</select>
				&nbsp; &nbsp;
				<?php echo JText::_('COM_MIJOSQL_TABLE').': ';?>
				<select class="text_area" id="ja_tbl_p" name="ja_tbl_p" onchange="changeQuery(this);">
					<?php echo $this->tables;?>
				</select>
				&nbsp; &nbsp;
				<?php echo JText::_('COM_MIJOSQL_RECORDS').': ';?>
				<input class="text_area" type="text" size="3" id="ja_lim_p" name="ja_lim_p" value="<?php echo JRequest::getInt('ja_lim_p', 10, 'post'); ?>" style="width:30px;" onchange="changeQuery(this);">
		   </td>
		</tr>
		<tr>
			<td>
			   <textarea class="text_area" id="ja_qry_p" name="ja_qry_p" style="width:100%;height:70px;"><?php echo MijosqlHelper::getVar('qry'); ?></textarea>
			</td>
		</tr>
	</table>
	<?php echo $this->data; ?>
	
	<input type="hidden" name="option" value="com_mijosql" />
	<input type="hidden" name="controller" value="mijosql" />
	<input type="hidden" name="task" value="">
	
	<?php echo JHTML::_('form.token'); ?>
</form>