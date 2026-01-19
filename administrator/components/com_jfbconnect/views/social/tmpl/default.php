<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };
jimport('joomla.filesystem.folder');
JHTML::_('behavior.tooltip');
$model = $this->model;
$k2IsInstalled = JFBConnectUtilities::isJoomlaComponentEnabled('com_k2');

$provider = $this->filter_provider;
$task = JRequest::getVar('task', '', 'post');
if(!empty($task)) { $provider = '';}
?>
<script type="text/javascript">
    function toggleHide(rowId, styleType)
    {
        document.getElementById(rowId).style.display = styleType;
    }
</script>

<div class="sourcecoast">
    <form method="post" id="adminForm" name="adminForm">
        <?php
        if (defined('SC30')):
        ?>
        <div class="row-fluid">
            <ul class="nav nav-tabs">
                <li <?php echo $provider ? '' : 'class="active"';?>><a href="#social_content_comment" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_COMMENTS'); ?></a></li>
                <li><a href="#social_content_like" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_LIKE'); ?></a></li>
                <li><a href="#social_content_quote" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_QUOTE'); ?></a></li>
                <?php
                if ($k2IsInstalled)
                {
                    echo '<li><a href="#social_content_k2_comment" data-toggle="tab">' . JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_COMMENTS') . '</a></li>';
                    echo '<li><a href="#social_content_k2_like" data-toggle="tab">' . JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_LIKE') . '</a></li>';
                    echo '<li><a href="#social_content_k2_quote" data-toggle="tab">' . JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_QUOTE') . '</a></li>';
                }
                ?>
                <li><a href="#social_notifications" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_NOTIFICATIONS'); ?></a></li>
                <li><a href="#social_messenger" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_MESSENGER'); ?></a></li>
                <li><a href="#social_misc" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_MISC'); ?></a></li>
                <li <?php echo $provider ? 'class="active"' : '';?>><a href="#social_examples" data-toggle="tab"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_MENU_EXAMPLES'); ?></a></li>
            </ul>
        </div>
        <div class="tab-content">
            <?php
            endif; //SC30
            if (defined('SC16')):
                jimport('joomla.html.pane');
                $pane = JPane::getInstance('tabs');
                echo $pane->startPane('content-pane');
                echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_COMMENTS'), 'social_content_comment_pane');
            endif; //SC16
            ?>
            <?php echo $this->loadTemplate('comments');?>
            <?php
            if (defined('SC16')):
                echo $pane->endPanel();
                echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_LIKE'), 'social_content_like_pane');
            endif;
            ?>
            <?php echo $this->loadTemplate('like');?>
            <?php
            if (defined('SC16')):
                echo $pane->endPanel();
                echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_QUOTE'), 'social_content_quote_pane');
            endif;
            ?>
            <?php echo $this->loadTemplate('quote');?>
            <?php
            if (defined('SC16')):
                echo $pane->endPanel();
            endif; //SC16

            if ($k2IsInstalled)
            {
                if (defined('SC16')):
                    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_COMMENTS'), 'social_content_k2_comment_pane');
                endif;
                ?>
                <?php echo $this->loadTemplate('k2comments');?>
                <?php
                if (defined('SC16')):
                    echo $pane->endPanel();
                    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_LIKE'), 'social_content_k2_like_pane');
                endif;
                ?>
                <?php echo $this->loadTemplate('k2like');?>
                <?php
                if (defined('SC16')):
                    echo $pane->endPanel();
                    echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_CONTENT_K2_QUOTE'), 'social_content_k2_quote_pane');
                endif;
                ?>
                <?php echo $this->loadTemplate('k2quote');?>
                <?php
                if (defined('SC16')):
                    echo $pane->endPanel();
                endif; //SC16
            }
            if (defined('SC16')):
                echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_NOTIFICATIONS'), 'social_notifications_pane');
            endif;
            echo $this->loadTemplate('notifications');

            if (defined('SC16')):
                echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_MESSENGER'), 'social_messenger_pane');
            endif;
            echo $this->loadTemplate('messenger');
            ?>
            <?php echo $this->loadTemplate('notifications');?>
            <?php
            if (defined('SC16')):
                echo $pane->endPanel();
                echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_MISC'), 'social_misc_pane');
            endif;
            ?>
            <div class="tab-pane" id="social_misc">
                <div class="config_row">
                    <div class="config_setting header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_SOCIAL_SETTING'); ?></div>
                    <div class="config_option header"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_OPTIONS'); ?></div>
                    <div style="clear:both"></div>
                </div>
                <div class="config_row">
                    <div class="config_setting hasTip"
                         title="<?php echo JText::_('COM_JFBCONNECT_SOCIAL_TAG_ADMIN_KEY_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_SOCIAL_TAG_ADMIN_KEY_LABEL'); ?></div>
                    <div class="config_option">
                        <input type="text" name="social_tag_admin_key" value="<?php echo $model->getSetting('social_tag_admin_key') ?>" size="20">
                    </div>
                    <div style="clear:both"></div>
                </div>
            </div>
            <?php
            if (defined('SC16')):
                echo $pane->endPanel();
                echo $pane->startPanel(JText::_('COM_JFBCONNECT_SOCIAL_MENU_EXAMPLES'), 'social_examples_pane');
            endif;
            ?>
            <div class="tab-pane <?php echo $provider ? 'active' : '';?>" id="social_examples">
                <?php $this->setLayout('examples'); ?>
                <?php echo $this->loadTemplate(); ?>
            </div>
            <?php
            if (defined('SC30')):
                echo '</div>';
            endif; //SC30
            if (defined('SC16')):
                echo $pane->endPanel();
                echo $pane->endPane();
            endif; //SC16
            ?>
            <input type="hidden" name="option" value="com_jfbconnect" />
            <input type="hidden" name="controller" value="social" />
            <input type="hidden" name="cid[]" value="0" />
            <input type="hidden" name="task" value="" />
            <?php echo JHTML::_('form.token'); ?>

    </form>
</div>
<script type="text/javascript">
    function showHideSettings(settingsId, selectors)
    {
        var hide = true;
        jfbcJQuery.each(selectors, function (i, selector)
            {
                if (jfbcJQuery("select[name=" + selector + "]").val() != "0")
                    hide = false;
            }
        );
        if (hide)
            jfbcJQuery("#" + settingsId).css("display", "none");
        else
            jfbcJQuery("#" + settingsId).css("display", "block");
    }
    ;

    var selectNames = {
        'social_comment_article_settings': ['social_comment_article_view'],
        'social_comment_blog_settings': ['social_comment_frontpage_view', 'social_comment_category_view'],
        'social_like_article_settings': ['social_like_article_view'],
        'social_like_blog_settings': ['social_like_frontpage_view', 'social_like_category_view'],
        'social_k2_comment_item_settings': ['social_k2_comment_item_view'],
        'social_k2_comment_blog_settings': ['social_k2_comment_category_view', 'social_k2_comment_tag_view', 'social_k2_comment_userpage_view', 'social_k2_comment_latest_view'],
        'social_k2_like_item_settings': ['social_k2_like_item_view'],
        'social_k2_like_blog_settings': ['social_k2_like_category_view', 'social_k2_like_tag_view', 'social_k2_like_userpage_view', 'social_k2_like_latest_view']
    };

    jfbcJQuery(document).ready(function ()
    {
        jfbcJQuery.each(selectNames, function (settingsId, selectors)
        {
            jfbcJQuery.each(selectors, function (i, selector)
            {
                showHideSettings(settingsId, selectors);
                jfbcJQuery("select[name=" + selector + "]").change(function ()
                {
                    showHideSettings(settingsId, selectors);
                });
            });
        });
    });

</script>