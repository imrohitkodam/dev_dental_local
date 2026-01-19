<?php
/*
* @package		MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
*/

// No Permission
defined('_JEXEC') or die ('Restricted access');

class com_MijosqlInstallerScript {
	
	public function postflight($type, $parent) {
?>
<img src="components/com_mijosql/assets/images/logo.png" alt="Joomla Database Manager" style="width:80px; height:80px; float: left; padding-right:15px;" />

<h2>MijoSQL Installation</h2>
<h2><a href="index.php?option=com_mijosql">Go to MijoSQL</a></h2>
<table class="adminlist table table-striped">
	<thead>
		<tr>
			<th class="title" colspan="2"><?php echo JText::_('Extension'); ?></th>
			<th width="30%"><?php echo JText::_('Status'); ?></th>
		</tr>
	</thead>
	<tbody>
		<tr class="row0">
			<td class="key" colspan="2"><?php echo 'MijoSQL '.JText::_('Component'); ?></td>
			<td><strong><?php echo JText::_('Installed'); ?></strong></td>
		</tr>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="3"></td>
		</tr>
	</tfoot>
</table>
	<?php
    }
}