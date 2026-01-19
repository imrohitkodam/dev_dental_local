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
$provider = $this->filter_provider;
?>
<div class="tab-pane <?php echo $provider ? '' : 'active';?>" id="social_content_comment">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_SETTING_LABEL"); ?></div>
        <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_ARTICLE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <?php $socialCommentArticleView = $model->getSetting('social_comment_article_view'); ?>
            <select name="social_comment_article_view">
                <option value="1" <?php echo ($socialCommentArticleView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
                <option value="2" <?php echo ($socialCommentArticleView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
                <option value="3" <?php echo ($socialCommentArticleView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialCommentArticleView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialCommentArticleView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_comment_article_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_article_comment_max_num" value="<?php echo $model->getSetting('social_article_comment_max_num') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_article_comment_width" value="<?php echo $model->getSetting('social_article_comment_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_comment_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_article_comment_color_schemeL" name="social_article_comment_color_scheme"
                           value="light" <?php echo $model->getSetting('social_article_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_article_comment_color_schemeD" name="social_article_comment_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_article_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_article_comment_order_by" class="radio btn-group">
                    <input type="radio" id="social_article_comment_order_byS" name="social_article_comment_order_by"
                           value="social" <?php echo $model->getSetting('social_article_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                    <input type="radio" id="social_article_comment_order_byR" name="social_article_comment_order_by"
                           value="reverse_time" <?php echo $model->getSetting('social_article_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                    <input type="radio" id="social_article_comment_order_byT" name="social_article_comment_order_by"
                           value="time" <?php echo $model->getSetting('social_article_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_article_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_LABEL'); ?></div>
            <div class="config_option">
            <textarea name="social_article_comment_intro_text" rows="3"
                      cols="30"><?php echo $model->getSetting('social_article_comment_intro_text') ?></textarea><br />
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
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_FRONTPAGE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <?php $socialCommentFrontPageView = $model->getSetting('social_comment_frontpage_view'); ?>
            <select name="social_comment_frontpage_view">
                <option value="1" <?php echo ($socialCommentFrontPageView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
                <option value="2" <?php echo ($socialCommentFrontPageView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
                <option value="3" <?php echo ($socialCommentFrontPageView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialCommentFrontPageView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialCommentFrontPageView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_SHOW_CATEGORY_LABEL"); ?>:
        </div>
        <div class="config_option">
            <?php $socialCommentCategoryView = $model->getSetting('social_comment_category_view'); ?>
            <select name="social_comment_category_view">
                <option value="1" <?php echo ($socialCommentCategoryView == '1') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_TOP_LABEL"); ?></option>
                <option value="2" <?php echo ($socialCommentCategoryView == '2') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL"); ?></option>
                <option value="3" <?php echo ($socialCommentCategoryView == '3') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_BOTH_LABEL"); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialCommentCategoryView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialCommentCategoryView == '0') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_NONE_LABEL"); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_comment_blog_settings">

        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_blog_comment_max_num" value="<?php echo $model->getSetting('social_blog_comment_max_num') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_WIDTH_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_blog_comment_width" value="<?php echo $model->getSetting('social_blog_comment_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC2"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_comment_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_blog_comment_color_schemeL" name="social_blog_comment_color_scheme"
                           value="light" <?php echo $model->getSetting('social_blog_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_blog_comment_color_schemeD" name="social_blog_comment_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_blog_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_blog_comment_order_by" class="radio btn-group">
                    <input type="radio" id="social_blog_comment_order_byS" name="social_blog_comment_order_by"
                           value="social" <?php echo $model->getSetting('social_blog_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                    <input type="radio" id="social_blog_comment_order_byR" name="social_blog_comment_order_by"
                           value="reverse_time" <?php echo $model->getSetting('social_blog_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                    <input type="radio" id="social_blog_comment_order_byT" name="social_blog_comment_order_by"
                           value="time" <?php echo $model->getSetting('social_blog_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_blog_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_LABEL'); ?></div>
            <div class="config_option">
            <textarea name="social_blog_comment_intro_text" rows="3"
                      cols="30"><?php echo $model->getSetting('social_blog_comment_intro_text') ?></textarea><br />
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
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_CATEGORY_SETTING_DESC"); ?>">
            <?php $catType = $model->getSetting('social_comment_cat_include_type'); ?>
            <fieldset id="social_comment_cat_include_type" class="radio btn-group">
                <input type="radio" id="social_comment_cat_include_type0" name="social_comment_cat_include_type"
                       value="0" <?php echo($catType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', 'none')" />
                <label for="social_comment_cat_include_type0"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ALL_LABEL"); ?></label>
                <input type="radio" id="social_comment_cat_include_type1" name="social_comment_cat_include_type"
                       value="1" <?php echo($catType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', '')" />
                <label for="social_comment_cat_include_type1"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?></label>
                <input type="radio" id="social_comment_cat_include_type2" name="social_comment_cat_include_type"
                       value="2" <?php echo($catType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('comment_cat_ids', '')" />
                <label for="social_comment_cat_include_type2"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row" id="comment_cat_ids" style="display:<?php echo($catType == "0" ? 'none' : ''); ?>">
        <?php
        $catids = $model->getSetting('social_comment_cat_ids');
        $categories = unserialize($catids);

        $db = JFactory::getDBO();
        $query = "SELECT id, title FROM #__categories WHERE extension='com_content'";
        $db->setQuery($query);
        $cats = $db->loadAssocList();
        $attribs = 'multiple="multiple"';
        echo '<td>' . JHTML::_('select.genericlist', $cats, 'social_comment_cat_ids[]', $attribs, 'id', 'title', $categories, 'social_comment_cat_ids') . '</td>';
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
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_INCLUDE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_comment_article_include_ids" value="<?php echo $model->getSetting('social_comment_article_include_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ARTICLE_EXCLUDE_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL"); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_comment_article_exclude_ids" value="<?php echo $model->getSetting('social_comment_article_exclude_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
</div>