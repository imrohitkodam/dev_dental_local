<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2018 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH')))
{
    die('Restricted access');
};
jimport('joomla.html.sliders');
JHTML::_('behavior.tooltip');

?>
<style type="text/css">
    #autotune-network-settings-status {
        font-weight: bold;
        font-size: 1.1em;
    }
</style>
<div class="sourcecoast">
    <div class="row-fluid">
        <?php include('step_sidebar.php'); ?>
        <div class="span9 autotune">
            <h3><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_NETWORKSETTINGS_HEADING'); ?></h3>
            <p><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_NETWORKSETTINGS_DESC'); ?><p>
                <input type="button" value="Fetch Latest Network Settings" class="btn btn-primary" onclick="jfbcAdmin.autotune.fetchNetworkSettings()"/>
            </p>
            <p>
                <span id="autotune-network-settings-status"></span>
            </p>

            <table class="table table-striped">
                <tr>
                    <th><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_NETWORKSETTINGS_LAST_UPDATE'); ?></th>
                    <th><?php echo JText::_('COM_JFBCONNECT_AUTOTUNE_NETWORKSETTINGS_NETWORK'); ?></th>
                </tr>
                <?php for ($i = 0; $i < count(JFBCFactory::getAllProviders()); $i++)
                {
                    $p = JFBCFactory::getAllProviders()[$i];
                    ?>
                    <tr>
                        <td class="span3 networksettings-updatedate">
                            <?php
                                $date = JFBCFactory::config()->getUpdatedDate($p->systemName . '_autotune_network_settings');
                                if ($date == null)
                                    $date = "Not Fetched";
                                else
                                { // It's a date
                                    $date = new JDate($date);
                                    $date = $date->format(JText::_('DATE_FORMAT_LC4'));
                                }
                                echo $date;
                            ?>
                            </td>
                        <td class="span9"><?php echo $p->name ?></td>
                    </tr>
                <?php }?>

            </table>

        </div>
        <form method="post" id="adminForm" name="adminForm">
            <input type="hidden" name="option" value="com_jfbconnect" />
            <input type="hidden" name="view" value="autotune" />
            <input type="hidden" name="task" value="startAutoTune" />
        </form>
    </div>
</div>