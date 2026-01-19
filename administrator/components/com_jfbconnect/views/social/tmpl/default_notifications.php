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
<div class="tab-pane" id="social_notifications">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_COMMENTS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_COMMENTS_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_notification_comment_enabled" class="radio btn-group">
                <input type="radio" id="social_notification_comment_enabled1" name="social_notification_comment_enabled"
                       value="1" <?php echo $model->getSetting('social_notification_comment_enabled') ? 'checked="checked"' : ""; ?> />
                <label for="social_notification_comment_enabled1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_notification_comment_enabled0" name="social_notification_comment_enabled"
                       value="0" <?php echo $model->getSetting('social_notification_comment_enabled') ? '""' : 'checked="checked"'; ?> />
                <label for="social_notification_comment_enabled0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_LIKES_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_NEW_LIKES_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_notification_like_enabled" class="radio btn-group">
                <input type="radio" id="social_notification_like_enabled1" name="social_notification_like_enabled"
                       value="1" <?php echo $model->getSetting('social_notification_like_enabled') ? 'checked="checked"' : ""; ?> />
                <label for="social_notification_like_enabled1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_notification_like_enabled0" name="social_notification_like_enabled"
                       value="0" <?php echo $model->getSetting('social_notification_like_enabled') ? '""' : 'checked="checked"'; ?> />
                <label for="social_notification_like_enabled0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_ADDRESS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_ADDRESS_LABEL'); ?></div>
        <div class="config_option">
            <textarea name="social_notification_email_address" rows="3"
                      cols="30"><?php echo $model->getSetting('social_notification_email_address') ?></textarea><br />
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_GOOGLE_ANALYTICS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NOTIFICATIONS_GOOGLE_ANALYTICS_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_notification_google_analytics" class="radio btn-group">
                <input type="radio" id="social_notification_google_analytics1" name="social_notification_google_analytics"
                       value="1" <?php echo $model->getSetting('social_notification_google_analytics') ? 'checked="checked"' : ""; ?> />
                <label for="social_notification_google_analytics1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_notification_google_analytics0" name="social_notification_google_analytics"
                       value="0" <?php echo $model->getSetting('social_notification_google_analytics') ? '""' : 'checked="checked"'; ?> />
                <label for="social_notification_google_analytics0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
</div>