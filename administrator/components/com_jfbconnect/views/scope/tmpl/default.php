<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2018 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v7.2.6
 * @build-date      2018/05/24
 */

// no direct access
if (!(defined('_JEXEC') || defined('ABSPATH')))
{
    die('Restricted access');
};

jimport('joomla.version');

?>
<div class="sourcecoast">
    <div class="row-fluid">
        <div class="span12">
            <div class="well">
                <legend><?php echo JText::_('COM_JFBCONNECT_SCOPE_REVIEW_TITLE'); ?></legend>
                <p><?php echo JText::_('COM_JFBCONNECT_SCOPE_REVIEW_DESCRIPTION') ?></p>
                <p><?php echo JText::_('COM_JFBCONNECT_SCOPE_REVIEW_FACEBOOK_DESCRIPTION') ?></p>
                <?php if ($this->autotuneSettingsLoaded) :
                    echo "<h3>" . JText::_('COM_JFBCONNECT_SCOPE_REVIEW_REQUESTED') . ":</h3>";
                    echo "<ul>";

                    if (count($this->scopes->profiles) > 0)
                    {
                        echo "<li>" . JText::_('COM_JFBCONNECT_MENU_PROFILES') . ":";
                        echo "<ul>";
                        foreach ($this->scopes->profiles as $scope => $details)
                        {
                            echo "<li><strong>" . $scope . "</strong> - " . $details->description . "</li>";
                        }
                        echo "</ul></li>";
                    }

                    if (count($this->scopes->channels) > 0)
                    {
                        echo "<li>" . JText::_('COM_JFBCONNECT_MENU_CHANNELS') . ":";
                        echo "<ul>";
                        foreach ($this->scopes->channels as $scope => $details)
                        {
                            echo "<li><strong>" . $scope . "</strong> - " . $details->description . "</li>";
                        }
                        echo "</ul></li>";
                    }

                    if (count($this->scopes->custom) > 0)
                    {
                        echo "<li>" . JText::_('COM_JFBCONNECT_SCOPE_REVIEW_CUSTOM_SCOPE') . ":";
                        echo "<ul>";
                        foreach ($this->scopes->custom as $scope => $details)
                        {
                            echo "<li><strong>" . $scope . "</strong> - " . $details->description . "</li>";
                        }
                        echo "</ul></li>";
                    }

                    echo "</ul>";
                    ?>

                <?php else: ?>
                    <p><strong><?php echo JText::_('COM_JFBCONNECT_SCOPE_REVIEW_NO_SCOPES_LOADED');?> </strong></p>
                <?php endif; ?>

                <p><?php echo JText::_('COM_JFBCONNECT_SCOPE_REVIEW_DOCUMENTATION_LINK'); ?></p>

            </div>
        </div>
    </div>
    <form method="post" id="adminForm" name="adminForm">
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="controller" value="scope"/>
        <input type="hidden" name="task" value=""/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>