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
<div class="tab-pane" id="social_content_k2_comment">
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ITEM_VIEW_SETTING"); ?></div>
        <div class="config_option header"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_OPTIONS"); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_ITEM_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_ITEM_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentItemView = $model->getSetting('social_k2_comment_item_view'); ?>
            <select name="social_k2_comment_item_view">
                <option value="1" <?php echo ($socialK2CommentItemView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentItemView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentItemView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialK2CommentItemView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialK2CommentItemView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_k2_comment_item_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COMMENTS_DESC3'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL"); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_item_comment_max_num" value="<?php echo $model->getSetting('social_k2_item_comment_max_num') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_DESC5'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_LABEL'); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_item_comment_width" value="<?php echo $model->getSetting('social_k2_item_comment_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC4'); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL"); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_comment_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_k2_item_comment_color_schemeL" name="social_k2_item_comment_color_scheme"
                           value="light" <?php echo $model->getSetting('social_k2_item_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_k2_item_comment_color_schemeD" name="social_k2_item_comment_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_k2_item_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_item_comment_order_by" class="radio btn-group">
                    <input type="radio" id="social_k2_item_comment_order_byS" name="social_k2_item_comment_order_by"
                           value="social" <?php echo $model->getSetting('social_k2_item_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                    <input type="radio" id="social_k2_item_comment_order_byR" name="social_k2_item_comment_order_by"
                           value="reverse_time" <?php echo $model->getSetting('social_k2_item_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                    <input type="radio" id="social_k2_item_comment_order_byT" name="social_k2_item_comment_order_by"
                           value="time" <?php echo $model->getSetting('social_k2_item_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_item_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_LABEL'); ?></div>
            <div class="config_option">
                <textarea name="social_k2_item_comment_intro_text" rows="3"
                          cols="30"><?php echo $model->getSetting('social_k2_item_comment_intro_text') ?></textarea><br />
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BLOG_SETTING_LABEL'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_CATEGORY_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_CATEGORY_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentCategoryView = $model->getSetting('social_k2_comment_category_view'); ?>
            <select name="social_k2_comment_category_view">
                <option value="1" <?php echo ($socialK2CommentCategoryView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentCategoryView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentCategoryView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialK2CommentCategoryView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialK2CommentCategoryView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_TAG_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_TAG_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentTagView = $model->getSetting('social_k2_comment_tag_view'); ?>
            <select name="social_k2_comment_tag_view">
                <option value="1" <?php echo ($socialK2CommentTagView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentTagView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentTagView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialK2CommentTagView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialK2CommentTagView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_USERPAGE_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_USERPAGE_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentUserpageView = $model->getSetting('social_k2_comment_userpage_view'); ?>
            <select name="social_k2_comment_userpage_view">
                <option value="1" <?php echo ($socialK2CommentUserpageView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentUserpageView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentUserpageView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialK2CommentUserpageView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialK2CommentUserpageView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_LATEST_VIEW_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SHOW_K2_LATEST_VIEW_LABEL'); ?>
            :
        </div>
        <div class="config_option">
            <?php $socialK2CommentLatestView = $model->getSetting('social_k2_comment_latest_view'); ?>
            <select name="social_k2_comment_latest_view">
                <option value="1" <?php echo ($socialK2CommentLatestView == '1') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TOP_LABEL'); ?></option>
                <option value="2" <?php echo ($socialK2CommentLatestView == '2') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTTOM_LABEL'); ?></option>
                <option value="3" <?php echo ($socialK2CommentLatestView == '3') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_BOTH_LABEL'); ?></option>
                <?php if (defined('SC30')):?>
                    <option value="4" <?php echo ($socialK2CommentLatestView == '4') ? 'selected' : ""; ?>><?php echo JText::_("COM_JFBCONNECT_SOCIAL_AFTER_TITLE_LABEL"); ?></option>
                <?php endif;?>
                <option value="0" <?php echo ($socialK2CommentLatestView == '0') ? 'selected' : ""; ?>><?php echo JText::_('COM_JFBCONNECT_SOCIAL_NONE_LABEL'); ?></option>
            </select>
        </div>
        <div style="clear:both"></div>
    </div>
    <div id="social_k2_comment_blog_settings">
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COMMENTS_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_COMMENTS_LABEL'); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_comment_max_num" value="<?php echo $model->getSetting('social_k2_blog_comment_max_num') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_WIDTH_LABEL'); ?>:
            </div>
            <div class="config_option">
                <input type="text" name="social_k2_blog_comment_width" value="<?php echo $model->getSetting('social_k2_blog_comment_width') ?>" size="20">
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DESC2'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LABEL'); ?>
                :
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_comment_color_scheme" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_comment_color_schemeL" name="social_k2_blog_comment_color_scheme"
                           value="light" <?php echo $model->getSetting('social_k2_blog_comment_color_scheme') == 'light' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_color_schemeL"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_LIGHT"); ?></label>
                    <input type="radio" id="social_k2_blog_comment_color_schemeD" name="social_k2_blog_comment_color_scheme"
                           value="dark" <?php echo $model->getSetting('social_k2_blog_comment_color_scheme') == 'dark' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_color_schemeD"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_COLOR_SCHEME_DARK"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_DESC"); ?>"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_LABEL"); ?>:
            </div>
            <div class="config_option">
                <fieldset id="social_k2_blog_comment_order_by" class="radio btn-group">
                    <input type="radio" id="social_k2_blog_comment_order_byS" name="social_k2_blog_comment_order_by"
                           value="social" <?php echo $model->getSetting('social_k2_blog_comment_order_by') == 'social' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_order_byS"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_SOCIAL"); ?></label>
                    <input type="radio" id="social_k2_blog_comment_order_byR" name="social_k2_blog_comment_order_by"
                           value="reverse_time" <?php echo $model->getSetting('social_k2_blog_comment_order_by') == 'reverse_time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_order_byR"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_REVERSETIME"); ?></label>
                    <input type="radio" id="social_k2_blog_comment_order_byT" name="social_k2_blog_comment_order_by"
                           value="time" <?php echo $model->getSetting('social_k2_blog_comment_order_by') == 'time' ? 'checked="checked"' : ""; ?> />
                    <label for="social_k2_blog_comment_order_byT"><?php echo JText::_("COM_JFBCONNECT_SOCIAL_ORDERBY_TIME"); ?></label>
                </fieldset>
            </div>
            <div style="clear:both"></div>
        </div>
        <div class="config_row">
            <div class="config_setting hasTip"
                 title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INTRO_TEXT_LABEL'); ?></div>
            <div class="config_option">
                <textarea name="social_k2_blog_comment_intro_text" rows="3"
                          cols="30"><?php echo $model->getSetting('social_k2_blog_comment_intro_text') ?></textarea><br />
            </div>
            <div style="clear:both"></div>
        </div>
    </div>
    <!-- Categories -->
    <div class="config_row">
        <div class="config_setting_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_CATEGORY_SETTING'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting_option hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_CATEGORY_SETTING_DESC'); ?>">
            <?php $k2CatType = $model->getSetting('social_k2_comment_cat_include_type'); ?>
            <fieldset id="social_k2_comment_cat_include_type" class="radio btn-group">
                <input type="radio" id="social_k2_comment_cat_include_type0" name="social_k2_comment_cat_include_type"
                       value="0" <?php echo($k2CatType == '0' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', 'none')" />
                <label for="social_k2_comment_cat_include_type0"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_ALL_LABEL'); ?></label>
                <input type="radio" id="social_k2_comment_cat_include_type1" name="social_k2_comment_cat_include_type"
                       value="1" <?php echo($k2CatType == '1' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', '')" />
                <label for="social_k2_comment_cat_include_type1"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL'); ?></label>
                <input type="radio" id="social_k2_comment_cat_include_type2" name="social_k2_comment_cat_include_type"
                       value="2" <?php echo($k2CatType == '2' ? 'checked="checked"' : ""); ?> onclick="toggleHide('k2_comment_cat_ids', '')" />
                <label for="social_k2_comment_cat_include_type2"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL'); ?></label>
            </fieldset>
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row" id="k2_comment_cat_ids" style="display:<?php echo($k2CatType == "0" ? 'none' : ''); ?>">
        <?php
        $k2catids = $model->getSetting('social_k2_comment_cat_ids');
        $k2categories = unserialize($k2catids);

        $query = "SELECT `id`, `name` FROM #__k2_categories";
        $db = JFactory::getDBO();
        $db->setQuery($query);
        $k2cats = $db->loadAssocList();

        $attribs = 'multiple="multiple"';
        echo '<td>' . JHTML::_('select.genericlist', $k2cats, 'social_k2_comment_cat_ids[]', $attribs, 'id', 'name', $k2categories, 'social_k2_comment_cat_ids') . '</td>';
        ?>
        <div style="clear:both"></div>
    </div>
    <!-- End Categories -->
    <div class="config_row">
        <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_SETTING'); ?></div>
        <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_INCLUDE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_INCLUDE_LABEL'); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_k2_comment_item_include_ids" value="<?php echo $model->getSetting('social_k2_comment_item_include_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
    <div class="config_row">
        <div class="config_setting hasTip"
             title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_K2_ITEM_EXCLUDE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_EXCLUDE_LABEL'); ?>:
        </div>
        <div class="config_option">
            <input type="text" name="social_k2_comment_item_exclude_ids" value="<?php echo $model->getSetting('social_k2_comment_item_exclude_ids'); ?>"
                   size="20">
        </div>
        <div style="clear:both"></div>
    </div>
</div>