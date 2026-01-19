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
<div class="tab-pane" id="social_content_like">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_VIEW_SETTING"); ?></div>
        <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_DESC2'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <?php $socialLikeArticleView = $model->getSetting('social_like_article_view'); ?>
            <select name="social_like_article_view">
                <option value="1" <?php echo ($socialLikeArticleView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
                <option value="2" <?php echo ($socialLikeArticleView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
                <option value="3" <?php echo ($socialLikeArticleView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialLikeArticleView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialLikeArticleView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_like_article_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_LABEL"); ?>:
            </div>
            <div class="config_option">
                <select name="social_article_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_article_like_layout_style') == 'standard' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_STANDARD_LABEL"); ?></option>
                    <option value="box_count" <?php echo $model->getSetting('social_article_like_layout_style') == 'box_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOX_COUNT_LABEL"); ?></option>
                    <option value="button_count" <?php echo $model->getSetting('social_article_like_layout_style') == 'button_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_COUNT_LABEL"); ?></option>
                    <option value="button" <?php echo $model->getSetting('social_article_like_layout_style') == 'button' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_LABEL"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_show_facebook" class="radio btn-group">
                    <input type="radio" id="social_article_like_show_facebook1" name="social_article_like_show_facebook"
                           value="1" <?php echo $model->getSetting('social_article_like_show_facebook') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_facebook1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_article_like_show_facebook0" name="social_article_like_show_facebook"
                           value="0" <?php echo $model->getSetting('social_article_like_show_facebook') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_facebook0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_show_faces" class="radio btn-group">
                    <input type="radio" id="social_article_like_show_faces1" name="social_article_like_show_faces"
                           value="1" <?php echo $model->getSetting('social_article_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_faces1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_article_like_show_faces0" name="social_article_like_show_faces"
                           value="0" <?php echo $model->getSetting('social_article_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_faces0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SHARE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SEND_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_show_send_button" class="radio btn-group">
                    <input type="radio" id="social_article_like_show_send_button1" name="social_article_like_show_send_button"
                           value="1" <?php echo $model->getSetting('social_article_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_send_button1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_article_like_show_send_button0" name="social_article_like_show_send_button"
                           value="0" <?php echo $model->getSetting('social_article_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_send_button0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_show_linkedin" class="radio btn-group">
                    <input type="radio" id="social_article_like_show_linkedin1" name="social_article_like_show_linkedin"
                           value="1" <?php echo $model->getSetting('social_article_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_linkedin1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_article_like_show_linkedin0" name="social_article_like_show_linkedin"
                           value="0" <?php echo $model->getSetting('social_article_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_linkedin0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_show_twitter" class="radio btn-group">
                    <input type="radio" id="social_article_like_show_twitter1" name="social_article_like_show_twitter"
                           value="1" <?php echo $model->getSetting('social_article_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_twitter1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_article_like_show_twitter0" name="social_article_like_show_twitter"
                           value="0" <?php echo $model->getSetting('social_article_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_twitter0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_show_pinterest" class="radio btn-group">
                    <input type="radio" id="social_article_like_show_pinterest1" name="social_article_like_show_pinterest"
                           value="1" <?php echo $model->getSetting('social_article_like_show_pinterest') ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_show_pinterest1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_article_like_show_pinterest0" name="social_article_like_show_pinterest"
                           value="0" <?php echo $model->getSetting('social_article_like_show_pinterest') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_article_like_show_pinterest0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_size" class="radio btn-group">
                    <input type="radio" id="social_article_like_size_small" name="social_article_like_size"
                           value="small" <?php echo $model->getSetting('social_article_like_size') == 'small' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_size_small"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_SMALL_LABEL"); ?></label>
                    <input type="radio" id="social_article_like_size_large" name="social_article_like_size"
                           value="large" <?php echo $model->getSetting('social_article_like_size') == 'large' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_size_large"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_LARGE_LABEL"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_verb_to_display" class="radio btn-group">
                    <input type="radio" id="social_article_like_verb_to_displayLike" name="social_article_like_verb_to_display"
                           value="like" <?php echo $model->getSetting('social_article_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_verb_to_displayLike"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LIKE_LABEL"); ?></label>
                    <input type="radio" id="social_article_like_verb_to_displayRec" name="social_article_like_verb_to_display"
                           value="recommend" <?php echo $model->getSetting('social_article_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_verb_to_displayRec"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_RECOMMEND_LABEL"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC3"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_like_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_article_like_color_schemeL" name="social_article_like_color_scheme"
                           value="light" <?php echo $model->getSetting('social_article_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_article_like_color_schemeD" name="social_article_like_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_article_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_like_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_LABEL"); ?>:
            </div>
            <div class="config_option">
                <select name="social_article_like_font">
                    <option value="" <?php echo $model->getSetting('social_article_like_font') == 'arial' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT1"); ?></option>
                    <option value="lucida grande" <?php echo $model->getSetting('social_article_like_font') == 'lucida grande' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT2"); ?></option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_article_like_font') == 'segoe ui' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT3"); ?></option>
                    <option value="tahoma" <?php echo $model->getSetting('social_article_like_font') == 'tahoma' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT4"); ?></option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_article_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT5"); ?></option>
                    <option value="verdana" <?php echo $model->getSetting('social_article_like_font') == 'verdana' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT6"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC3"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_article_like_width" value="<?php echo $model->getSetting('social_article_like_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_LABEL'); ?></div>
            <div class="config_option">
            <textarea name="social_article_like_intro_text" rows="3"
                      cols="30"><?php echo $model->getSetting('social_article_like_intro_text') ?></textarea><br />
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BLOG_SETTING_LABEL"); ?></div>
        <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_DESC2'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <?php $socialLikeFrontPageView = $model->getSetting('social_like_frontpage_view'); ?>
            <select name="social_like_frontpage_view">
                <option value="1" <?php echo ($socialLikeFrontPageView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
                <option value="2" <?php echo ($socialLikeFrontPageView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
                <option value="3" <?php echo ($socialLikeFrontPageView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialLikeFrontPageView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialLikeFrontPageView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_DESC2'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_LABEL"); ?>:
        </div>
        <div class="config_option">
            <?php $socialLikeCategoryView = $model->getSetting('social_like_category_view'); ?>
            <select name="social_like_category_view">
                <option value="1" <?php echo ($socialLikeCategoryView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
                <option value="2" <?php echo ($socialLikeCategoryView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
                <option value="3" <?php echo ($socialLikeCategoryView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialLikeCategoryView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialLikeCategoryView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_like_blog_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LAYOUT_STYLE_LABEL"); ?>:
            </div>
            <div class="config_option">
                <select name="social_blog_like_layout_style">
                    <option value="standard" <?php echo $model->getSetting('social_blog_like_layout_style') == 'standard' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_STANDARD_LABEL"); ?></option>
                    <option value="box_count" <?php echo $model->getSetting('social_blog_like_layout_style') == 'box_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOX_COUNT_LABEL"); ?></option>
                    <option value="button_count" <?php echo $model->getSetting('social_blog_like_layout_style') == 'button_count' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_COUNT_LABEL"); ?></option>
                    <option value="button" <?php echo $model->getSetting('social_blog_like_layout_style') == 'button' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BUTTON_LABEL"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACEBOOK_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_facebook" class="radio btn-group">
                    <input type="radio" id="social_blog_like_show_facebook1" name="social_blog_like_show_facebook"
                           value="1" <?php echo $model->getSetting('social_blog_like_show_facebook') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_facebook1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_blog_like_show_facebook0" name="social_blog_like_show_facebook"
                           value="0" <?php echo $model->getSetting('social_blog_like_show_facebook') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_facebook0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FACES_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_faces" class="radio btn-group">
                    <input type="radio" id="social_blog_like_show_faces1" name="social_blog_like_show_faces"
                           value="1" <?php echo $model->getSetting('social_blog_like_show_faces') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_faces1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_blog_like_show_faces0" name="social_blog_like_show_faces"
                           value="0" <?php echo $model->getSetting('social_blog_like_show_faces') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_faces0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SHARE_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_SEND_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_send_button" class="radio btn-group">
                    <input type="radio" id="social_blog_like_show_send_button1" name="social_blog_like_show_send_button"
                           value="1" <?php echo $model->getSetting('social_blog_like_show_send_button') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_send_button1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_blog_like_show_send_button0" name="social_blog_like_show_send_button"
                           value="0" <?php echo $model->getSetting('social_blog_like_show_send_button') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_send_button0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_LINKEDIN_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_linkedin" class="radio btn-group">
                    <input type="radio" id="social_blog_like_show_linkedin1" name="social_blog_like_show_linkedin"
                           value="1" <?php echo $model->getSetting('social_blog_like_show_linkedin') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_linkedin1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_blog_like_show_linkedin0" name="social_blog_like_show_linkedin"
                           value="0" <?php echo $model->getSetting('social_blog_like_show_linkedin') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_linkedin0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_TWITTER_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_twitter" class="radio btn-group">
                    <input type="radio" id="social_blog_like_show_twitter1" name="social_blog_like_show_twitter"
                           value="1" <?php echo $model->getSetting('social_blog_like_show_twitter') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_twitter1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_blog_like_show_twitter0" name="social_blog_like_show_twitter"
                           value="0" <?php echo $model->getSetting('social_blog_like_show_twitter') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_twitter0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_PINTEREST_LABEL"); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_show_pinterest" class="radio btn-group">
                    <input type="radio" id="social_blog_like_show_pinterest1" name="social_blog_like_show_pinterest"
                           value="1" <?php echo $model->getSetting('social_blog_like_show_pinterest') ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_show_pinterest1"><?php echo JText::_("JYES"); ?></label>
                    <input type="radio" id="social_blog_like_show_pinterest0" name="social_blog_like_show_pinterest"
                           value="0" <?php echo $model->getSetting('social_blog_like_show_pinterest') ? '""' : 'checked="checked"'; ?> />
                    <label for="social_blog_like_show_pinterest0"><?php echo JText::_("JNO"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_size" class="radio btn-group">
                    <input type="radio" id="social_blog_like_size_small" name="social_blog_like_size"
                           value="small" <?php echo $model->getSetting('social_blog_like_size') == 'small' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_size_small"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_SMALL_LABEL"); ?></label>
                    <input type="radio" id="social_blog_like_size_large" name="social_blog_like_size"
                           value="large" <?php echo $model->getSetting('social_blog_like_size') == 'large' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_size_large"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SIZE_LARGE_LABEL"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>

        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_VERB_DISPLAY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_verb_to_display" class="radio btn-group">
                    <input type="radio" id="social_blog_like_verb_to_displayLike" name="social_blog_like_verb_to_display"
                           value="like" <?php echo $model->getSetting('social_blog_like_verb_to_display') == 'like' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_verb_to_displayLike"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_LIKE_LABEL"); ?></label>
                    <input type="radio" id="social_blog_like_verb_to_displayRec" name="social_blog_like_verb_to_display"
                           value="recommend" <?php echo $model->getSetting('social_blog_like_verb_to_display') == 'recommend' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_verb_to_displayRec"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_RECOMMEND_LABEL"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_like_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_blog_like_color_schemeL" name="social_blog_like_color_scheme"
                           value="light" <?php echo $model->getSetting('social_blog_like_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_blog_like_color_schemeD" name="social_blog_like_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_blog_like_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_like_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT_LABEL"); ?>:
            </div>
            <div class="config_option">
                <select name="social_blog_like_font">
                    <option value="arial" <?php echo $model->getSetting('social_blog_like_font') == 'arial' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT1"); ?></option>
                    <option value="lucida grande" <?php echo $model->getSetting('social_blog_like_font') == 'lucida grande' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT2"); ?></option>
                    <option value="segoe ui" <?php echo $model->getSetting('social_blog_like_font') == 'segoe ui' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT3"); ?></option>
                    <option value="tahoma" <?php echo $model->getSetting('social_blog_like_font') == 'tahoma' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT4"); ?></option>
                    <option value="trebuchet ms" <?php echo $model->getSetting('social_blog_like_font') == 'trebuchet ms' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT5"); ?></option>
                    <option value="verdana" <?php echo $model->getSetting('social_blog_like_font') == 'verdana' ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_FONT6"); ?></option>
                </select>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC4"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_blog_like_width" value="<?php echo $model->getSetting('social_blog_like_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_LABEL'); ?></div>
            <div class="config_option">
            <textarea name="social_blog_like_intro_text" rows="3"
                      cols="30"><?php echo $model->getSetting('social_blog_like_intro_text') ?></textarea><br />
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <div class="config_row">
        <div class="config_setting_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_CATEGORY_SETTING"); ?></div>
        <div style="clear:both"></div>
    </div>

    <div class="config_row">
        <div class="config_setting_option hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_CATEGORY_SETTING_DESC2'); ?>">
            <?php $catType = $model->getSetting('social_like_cat_include_type'); ?>
            <fieldset id="social_like_cat_include_type" class="radio btn-group">
                <input type="radio" id="social_like_cat_include_type0" name="social_like_cat_include_type"
                       value="0" <?php echo($catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', 'none')" />
                <label for="social_like_cat_include_type0"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ALL_LABEL"); ?></label>
                <input type="radio" id="social_like_cat_include_type1" name="social_like_cat_include_type"
                       value="1" <?php echo($catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', '')" />
                <label for="social_like_cat_include_type1"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?></label>
                <input type="radio" id="social_like_cat_include_type2" name="social_like_cat_include_type"
                       value="2" <?php echo($catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('like_cat_ids', '')" />
                <label for="social_like_cat_include_type2"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row" id="like_cat_ids" style="display:<?php echo($catType == "0" ? 'none' : ''); ?>">
        <?php
        $catids = $model->getSetting('social_like_cat_ids');
        $categories = unserialize($catids);

        $db = JFactory::getDBO();
        $query = "SELECT id, title FROM #__categories WHERE extension='com_content'";
        $db->setQuery($query);
        $cats = $db->loadAssocList();

        $attribs = 'multiple="multiple"';
        echo '<td>' . JHTML::_('select.genericlist', $cats, 'social_like_cat_ids[]', $attribs, 'id', 'title', $categories, 'social_like_cat_ids') . '</td>';
        ?>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_SETTING"); ?></div>
        <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
        <div style="clear:both"></div>
    </div>

    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_ARTICLE_INCLUDE_DESC'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_like_article_include_ids" value="<?php echo $model->getSetting('social_like_article_include_ids'); ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_ARTICLE_EXCLUDE_DESC'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_like_article_exclude_ids" value="<?php echo $model->getSetting('social_like_article_exclude_ids'); ?>" size="20">
        </div>
        <div style="clear:both"></div>
    </div>
</div>