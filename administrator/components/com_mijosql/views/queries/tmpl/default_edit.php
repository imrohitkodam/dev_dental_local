<?php
/**
* @version		1.0.0
* @package		MijoSQL
* @subpackage	MijoSQL
* @copyright	2009-2012 Mijosoft LLC, www.mijosoft.com
* @license		GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @license		GNU/GPL based on AceSQL www.joomace.net
*/

// No Permission
defined('_JEXEC') or die('Restricted access');
?>

<form action="index.php?option=com_mijosql&amp;controller=queries&amp;task=save&cid[]=<?php echo $this->row->id; ?>" method="post" name="adminForm" id="adminForm">
    <fieldset class="adminform">
        <legend><?php echo JText::_('COM_MIJOSQL_QUERY_DETAILS'); ?></legend>
        <table class="admintable">
            <tr>
                <td width="20%" class="key">
                    <label for="name">
                        <?php echo JText::_('COM_MIJOSQL_TITLE'); ?>
                    </label>
                </td>
                <td width="80%">
                    <input class="inputbox" type="text" id="title" name="title" size="50" value="<?php echo $this->row->title; ?>" />
                </td>
            </tr>
            <tr>
                <td width="20%" class="key">
                    <label for="name">
                        <?php echo JText::_('COM_MIJOSQL_QUERY'); ?>
                    </label>
                </td>
                <td width="80%">
                    <textarea class="text_area" id="ja_query" name="ja_query" style="width:100%;height:70px;"><?php echo $this->row->query; ?></textarea>
                </td>
            </tr>
            </tr>
        </table>
    </fieldset>
    <input type="hidden" name="option" value="com_mijosql" />
    <input type="hidden" name="controller" value="queries" />
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
    <?php echo JHTML::_('form.token'); ?>
</form>