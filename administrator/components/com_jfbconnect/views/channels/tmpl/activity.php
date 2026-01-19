<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

JFBConnectUtilities::loadLanguage('com_jfbconnect', JPATH_SITE);
$posts = $this->getActivity();
$app = JFactory::getApplication();
$filter_search = $app->getUserStateFromRequest('com_jfbconnect.activity.search', 'search', '', 'string');
?>
<ul class="nav nav-tabs">
    <li><a href="index.php?option=com_jfbconnect&view=channels&layout=default"><?php echo JText::_('COM_JFBCONNECT_CHANNEL_CONFIGURATION_LABEL');?></a></li>
    <li class="active"><a href="#"><?php echo JText::_('COM_JFBCONNECT_CHANNEL_HISTORY_LABEL');?></a></li>
    <li class="pull-right" >

        <fieldset id="filter-bar">
            <div class="filter-search pull-left">
                <div class="btn-group pull-left">
                    <input type="text" name="search" id="search" value="<?php echo $filter_search; ?>"
                           class="filter-search btn-group pull-left"
                           title="<?php echo JText::_('COM_JFBCONNECT_FILTER'); ?>"
                           placeholder="<?php echo JText::_('COM_JFBCONNECT_FILTER'); ?>" />

                    <div class="btn-group pull-left">
                        <button class="btn tip" id="jfbcSubmitButton" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_GO'); ?>" onclick="jfbcJQuery('#filter_search').val(jfbcJQuery('#search').val());document.adminForm.submit();">
                            <?php
                            if (defined('SC30')):
                                echo '<i class="icon-search"></i>';
                            endif; //SC30
                            if (defined('SC16')):
                                echo JText::_('COM_JFBCONNECT_BUTTON_GO');
                            endif; //SC16
                            ?>
                        </button>
                        <?php
                        $resetJavascript = "document.getElementById('search').value='';";
                        $resetJavascript .= "document.getElementById('filter_search').value='';";
                        $resetJavascript .= "document.getElementById('filter_provider').value='';";
                        $resetJavascript .= "document.getElementById('filter_provider').selected='';";                        
                        $resetJavascript .= "document.getElementById('filter_type').value='-1';";
                        $resetJavascript .= "document.getElementById('filter_type').selected='-1';";
                        $resetJavascript .= "document.adminForm.submit();";
                        ?>
                        <button class="btn tip" id="jfbcResetButton" title="<?php echo JText::_('COM_JFBCONNECT_BUTTON_RESET'); ?>"
                                onclick="<?php echo $resetJavascript; ?>">
                            <?php
                            if (defined('SC30')):
                                echo '<i class="icon-remove"></i>';
                            endif; //SC30
                            if (defined('SC16')):
                                echo JText::_('COM_JFBCONNECT_BUTTON_RESET');
                            endif; //SC16
                            ?>
                        </button>
                    </div>
                </div>
                <div class="filter-select pull-right">

                    <?php
                    $providers = array();
                    foreach($this->items as $item)
                    {
                        if(!in_array($item->provider, $providers))
                            $providers[] = $item->provider;
                    }
                    ?>

                    <?php $filter_provider = JFactory::getApplication()->getUserStateFromRequest('com_jfbconnect.activity.provider', 'provider', '', 'string');?>
                    <?php if(count($providers)):?>                        
                        <select onchange="jfbcJQuery('#filter_provider').val(jfbcJQuery(this).val());document.adminForm.submit();" name="provider" id="provider">
                            <option <?php echo $filter_provider == '' ? 'selected="selected"' : ''; ?> value=""><?php echo JText::_('COM_JFBCONNECT_CHANNEL_ACTIVITY_SELECT_PROVIDER');?></option>
                            <?php foreach($providers as $provider):?>
                                <option <?php echo $filter_provider == $provider ? 'selected="selected"' : ''; ?> value="<?php echo $provider;?>"><?php echo ucfirst($provider);?></option>
                            <?php endforeach;?>    
                        </select>
                    <?php endif;?>

                    <?php $filter_type = JFactory::getApplication()->getUserStateFromRequest('com_jfbconnect.activity.type', 'type', -1, 'int');?>
                    <select onchange="jfbcJQuery('#filter_type').val(jfbcJQuery(this).val());document.adminForm.submit();" name="type" id="type">
                        <option <?php echo $filter_type == '-1' || $filter_type == '' ? 'selected="selected"' : ''; ?> value="-1"><?php echo JText::_('COM_JFBCONNECT_CHANNEL_ACTIVITY_SELECT_TYPE');?></option>
                        <option <?php echo $filter_type == '1' ? 'selected="selected"' : ''; ?> value="1"><?php echo JText::_('COM_JFBCONNECT_POST_TYPE_AUTOPOST_OPTION');?></option>
                        <option <?php echo $filter_type == '0' ? 'selected="selected"' : ''; ?> value="0"><?php echo JText::_('COM_JFBCONNECT_POST_TYPE_MANUAL_OPTION');?></option>
                    </select>

                </div>
            </div>
        </fieldset>



    </li>
</ul>
<div class="sourcecoast">
    <form action="index.php?option=com_jfbconnect&view=channels&layout=activity" method="post" name="adminForm" id="adminForm">
        <table class="adminlist table table-striped">
            <thead>
            <tr>
                <th>#</th>
                <th width="20">
                    <?php
                    if (defined('SC30')):
                        echo JHtml::_('grid.checkall');
                    endif; //SC30
                    if (defined('SC16')):
                        echo '<input type="checkbox" name="toggle" value="" onclick="checkAll('.count($posts).');" />';
                    endif; //SC16
                    ?>
                </th>
                <th class="title"><?php echo JText::_('COM_JFBCONNECT_POST_ID_LABEL') ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_TYPE_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_URL_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_CHANNEL_PROVIDER_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_CHANNEL_TYPE_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_EXT_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_LAYOUT_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_ITEMID_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_STATUS_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_RESPONSE_LABEL'); ?></th>
                <th><?php echo JText::_('COM_JFBCONNECT_POST_CREATED_LABEL'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            $key = 0;
            if ($this->posts && count($this->posts) > 0)
            {
                foreach ($this->posts as $post) :
                    $key++;
                    ?>
                    <tr class="row<?php echo($key % 2); ?>">
                        <td><?php echo $key; ?></td>
                        <td><?php echo $checked = JHTML::_('grid.id', $key, $post->id); ?></td>
                        <td class="center"><?php echo $post->id;?></td>
                        <td class="center"><?php echo ($post->type == 1 ? JText::_('COM_JFBCONNECT_POST_TYPE_AUTOPOST_OPTION') : JText::_('COM_JFBCONNECT_POST_TYPE_MANUAL_OPTION'));?></td>
                        <td class="center"><a href="<?php echo JURI::root() . $post->url; ?>" target="_blank"><?php echo (empty($post->url)?JURI::root():$post->url); ?></a></td>
                        <td class="center"><?php echo '<img src="' . JURI::root() . '/media/sourcecoast/images/provider/' . strtolower($post->provider) . '/icon.png" />'; ?></td>
                        <td class="center"><?php echo $post->channel_type;?></td>
                        <td class="center"><?php echo $post->ext;?></td>
                        <td class="center"><?php echo $post->layout;?></td>
                        <td class="center"><?php echo $post->item_id;?></td>
                        <td class="center">
                            <?php
                            if($post->status == 0)
                                echo '<span class="icon-unpublish" title="'.JText::_('COM_JFBCONNECT_CHANNEL_AUTOPOST_STATUS_FAILED').'"></span>';
                            else if($post->status == 1)
                                echo '<span class="icon-publish" title="'.JText::_('COM_JFBCONNECT_CHANNEL_AUTOPOST_STATUS_SUCCESS').'"></span>';
                            else
                                echo '<span class="icon-pending" title="'.JText::_('COM_JFBCONNECT_CHANNEL_AUTOPOST_STATUS_PENDING').'"></span>';
                            ?>
                        </td>
                        <td class="center"><?php echo $post->response;?></td>
                        <td class="center"><?php echo $post->created;?></td>
                    </tr>
                <?php endforeach;
            }
            ?>
            </tbody>
            <tfoot>
            <tr>
                <td colspan="13"><?php echo $this->pagination->getListFooter(); ?></td>
            </tr>
            </tfoot>
        </table>

        <input type="hidden" name="option" value="com_jfbconnect" />
        <input type="hidden" name="view" value="<?php echo JRequest::getVar('view'); ?>" />
        <input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
        <input type="hidden" name="search" id="filter_search" value="<?php echo $filter_search;?>" />
        <input type="hidden" name="type" id="filter_type" value="<?php echo $filter_type;?>" />
        <input type="hidden" name="provider" id="filter_provider" value="<?php echo $filter_provider;?>" />
        <input type="hidden" name="formtype" value="autopost_activity" />
        <input type="hidden" name="boxchecked" value="0" />
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
