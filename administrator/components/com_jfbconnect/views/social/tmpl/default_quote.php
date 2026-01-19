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
<div class="tab-pane" id="social_content_quote">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_QUOTE_ENABLE_DESCRIPTION'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_QUOTE_ENABLE_LABEL'); ?></div>
        <div class="config_option">
            <fieldset id="social_quote_enabled" class="radio btn-group">
                <input type="radio" id="social_quote_enabled1" name="social_quote_enabled"
                       value="1" <?php echo $model->getSetting('social_quote_enabled') ? 'checked="checked"' : ""; ?> />
                <label for="social_quote_enabled1"><?php echo JText::_("JYES"); ?></label>
                <input type="radio" id="social_quote_enabled0" name="social_quote_enabled"
                       value="0" <?php echo $model->getSetting('social_quote_enabled') ? '""' : 'checked="checked"'; ?> />
                <label for="social_quote_enabled0"><?php echo JText::_("JNO"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip" title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_QUOTE_URL_DESCRIPTION'); ?>">
            <?php echo JText::_('COM_JFBCONNECT_SOCIAL_QUOTE_URL_LABEL'); ?>
        </div>
        <div class="config_option">
            <input type="text" name="social_quote_url" value="<?php echo $model->getSetting('social_quote_url') ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
</div>
