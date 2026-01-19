<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); }
$model = $this->model;
?>
<div class="tab-pane" id="social_messenger">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_ENABLED_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_ENABLED_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_messenger_enabled" class="radio btn-group">
                <input type="radio" id="social_messenger_enabled1" name="social_messenger_enabled"
                       value="1" <?php echo $model->getSetting('social_messenger_enabled') ? 'checked="checked"' : ""; ?> />
                <label for="social_messenger_enabled1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_messenger_enabled0" name="social_messenger_enabled"
                       value="0" <?php echo $model->getSetting('social_messenger_enabled') ? '""' : 'checked="checked"'; ?> />
                <label for="social_messenger_enabled0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_PAGEID_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_PAGEID_LABEL'); ?></div>
        <div class="config_option">
            <input type="text" name="social_messenger_page_id" value="<?php echo $model->getSetting('social_messenger_page_id') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_THEME_COLOR_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_THEME_COLOR_LABEL'); ?></div>
        <div class="config_option">
            <input type="text" name="social_messenger_theme_color" value="<?php echo $model->getSetting('social_messenger_theme_color') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_LOGGEDIN_GREETING_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_LOGGEDIN_GREETING_LABEL'); ?></div>
        <div class="config_option">
            <input type="text" name="social_messenger_logged_in_greeting" value="<?php echo $model->getSetting('social_messenger_logged_in_greeting') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_LOGGEDOUT_GREETING_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_LOGGEDOUT_GREETING_LABEL'); ?></div>
        <div class="config_option">
            <input type="text" name="social_messenger_logged_out_greeting" value="<?php echo $model->getSetting('social_messenger_logged_out_greeting') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_GREETING_DIALOGDISPLAY_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_GREETING_DIALOGDISPLAY_LABEL'); ?></div>
        <div class="config_option">
            <select name="social_messenger_greeting_dialog_display">
                <option value="show"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_GREETING_DIALOGDISPLAY_OPTION_SHOW');?></option>
                <option value="hide"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_GREETING_DIALOGDISPLAY_OPTION_HIDE');?></option>
                <option value="fade"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_GREETING_DIALOGDISPLAY_OPTION_FADE');?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_GREETING_DIALOGDELAY_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MESSENGER_GREETING_DIALOGDELAY_LABEL'); ?></div>
        <div class="config_option">
            <input type="text" name="social_messenger_greeting_dialog_delay" value="<?php echo $model->getSetting('social_messenger_greeting_dialog_delay') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
</div>