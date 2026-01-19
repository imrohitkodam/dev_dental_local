<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

// no direct access
if (!(defined('_JEXEC') || defined('ABSPATH')))
{
    die('Restricted access');
};

jimport('joomla.version');

?>
<div class="sourcecoast">
    <form method="post" id="adminForm" name="adminForm">
        <div class="row-fluid">
            <div class="span12">
                <div class="jfbcControlIcons">
                    <div class="icon-wrapper">
                        <div class="icon">
                            <a href="index.php?option=com_jfbconnect&view=opengraph&task=objects">
                                <?php echo JHTML::_('image', 'administrator/components/com_jfbconnect/assets/images/icon-48-object-sc.png', NULL, NULL); ?>
                                <span><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTS'); ?></span>
                            </a>
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <div class="icon">
                            <a href="index.php?option=com_jfbconnect&view=opengraph&task=settings">
                                <?php echo JHTML::_('image', 'administrator/components/com_jfbconnect/assets/images/icon-48-config-sc.png', NULL, NULL); ?>
                                <span><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_CONFIGURATION'); ?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="span12">
                <?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OVERVIEW_DESCRIPTION'); ?>
                <p style="font-size:1.5em"><?php echo JText::sprintf('COM_JFBCONNECT_OPENGRAPH_CONFIGURATION_GUIDE_LINK', '<a
                    href="http://www.sourcecoast.com/jfbconnect/docs/facebook-open-graph-actions-for-joomla" target="_blank">', '</a>'); ?>
                </p>
            </div>
        </div>
        <div style="clear:both"/>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="controller" value="opengraph"/>
        <input type="hidden" name="task" value=""/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>