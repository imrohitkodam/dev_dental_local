<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };
jimport('joomla.html.sliders');
JHTML::_('behavior.tooltip');

?>

<div class="sourcecoast">
    <form method="post" id="adminForm" name="adminForm">

        <?php
        $this->tabsStart('myTab', 'meta_general');

        $this->tabStart('myTab', 'meta_general', JText::_('COM_JFBCONNECT_CONFIG_MENU_GENERAL'));
        $this->formDisplay('meta');
        $this->tabEnd();
        $this->tabsEnd();
        ?>


        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="controller" value="opengraph" />
        <input type="hidden" name="cid[]" value="0" />
        <input type="hidden" name="formtype" value="settings" />
        <input type="hidden" name="task" value="apply" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>