<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
jimport('joomla.filesystem.file') ;
JHtml::_('behavior.tooltip');
// Set toolbar items for the page
JToolBarHelper::title(   JText::_( 'EDOCMAN_CONFIG' ),'cog');
JToolBarHelper::save();
JToolBarHelper::apply();	
JToolBarHelper::cancel();
//$document = JFactory::getDocument();
//$document->addStyleSheet(JUri::root().'components/com_edocman/assets/css/tab.css');
//$document->addScript(JUri::root().'components/com_edocman/assets/js/bootstrap.min.js');
$editorPlugin = null;
if (JPluginHelper::isEnabled('editors', 'codemirror'))
{
	$editorPlugin = 'codemirror';
}
elseif(JPluginHelper::isEnabled('editor', 'none'))
{
	$editorPlugin = 'none';
}
if ($editorPlugin)
{
	$showCustomCss = 1;
}else{
	$showCustomCss = 0;
}
?>
<style>
	table.alignleft td {
		text-align: left;
	}
</style>
<form action="index.php?option=com_edocman&view=configuration" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="span12">
		<?php echo JHtml::_('bootstrap.startTabSet', 'configTab', array('active' => 'general-page')); ?>
			<?php echo JHtml::_('bootstrap.addTab', 'configTab', 'general-page', JText::_('EDOCMAN_GENERAL')); ?>
				<table class="adminform" style="width:100%;" id="configurationTable">
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_MAIN_CONFIGURATION') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td class="key" width="10%" style="width:10%;">
							<?php echo JText::_('EDOCMAN_DOCUMENT_PATH') ; ?>
						</td>
						<td width="30%" style="width:30%;">
							<input type="text" name="documents_path" class="inputbox" value="<?php echo $this->config->documents_path; ?>" size="50" />
							<?php
							if (!is_writable($this->config->documents_path)) {
								?>
								<span style="color:red; font-weight: bold;"><?php echo JText::_('EDOCMAN_DOCUMENT_PATH_NOT_WRITABLE') ?></span>
							<?php
							}
							?>
							<input type="button" class="button btn" onclick="resetPath();" value="<?php echo JText::_('EDOCMAN_RESET');?>" />
						</td>
						<td class="tdexplanation" width="60%">
							<?php echo JText::_('EDOCMAN_DOCUMENT_PATH_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_LOAD_JQUERY') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['load_jquery'];?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_LOAD_JQUERY_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_LOAD_TWITTER_BOOTSTRAP') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['load_twitter_bootstrap'];?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_LOAD_TWITTER_BOOTSTRAP_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_TWITTER_BOOTSTRAP_VERSION') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['twitter_bootstrap_version'];?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_TWITTER_BOOTSTRAP_VERSION_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_ACCESS_LEVEL_INHERITANCE') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['access_level_inheritance'];?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_ACCESS_LEVEL_INHERITANCE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_ACTIVATE_MULTILINGUAL_FEATURE') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['activate_multilingual_feature']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_ACTIVATE_MULTILINGUAL_FEATURE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_ACTIVATE_HERACHICAL_FOLDER_STRUCTURE') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['activate_herachical_folder_structure']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_ACTIVATE_HERACHICAL_FOLDER_STRUCTURE_EXPLAIN'); ?>
						</td>
					</tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_REMOVE_CATEGORY_FOLDER'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['remove_category_folder']; ?>
                        </td>
                        <td class="tdexplanation">
                            &nbsp;<?php echo JText::_('EDOCMAN_REMOVE_CATEGORY_FOLDER_EXPLAIN'); ?>
                        </td>
                    </tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_ACTIVATE_ONWER_GROUPS') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['user_group_ids']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_ACTIVATE_ONWER_GROUPS_EXPLAIN'); ?>
						</td>
					</tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_UPLOAD_CONFIGURATION') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_FILE_UPLOAD_METHOD'); ?>
						</td>
						<td>
							<?php echo $this->lists['file_upload_method']; ?>
						</td>
						<td class="tdexplanation">
							&nbsp;<?php echo JText::_('EDOCMAN_FILE_UPLOAD_METHOD_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_ALLOWED_FILE_TYPES') ; ?>
						</td>
						<td>
							<input type="text" name="allowed_file_types" class="inputbox" value="<?php echo $this->config->allowed_file_types; ?>" size="50" />
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_ALLOWED_FILE_TYPES_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_DELETE_FILE_WHEN_DELETE_DOCUMENT'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['delete_file_when_document_deleted']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_DELETE_FILE_WHEN_DELETE_DOCUMENT_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_MAX_UPLOAD_FILE_SIZE') ; ?>
                        </td>
                        <td>
                            <input type="text" name="max_file_size" class="input-mini" value="<?php echo $this->config->max_file_size; ?>" size="5" />
                            <?php echo $this->lists['max_filesize_type'] ; ?>&nbsp;&nbsp;&nbsp;<?php echo JText::_('EDOCMAN_DEFAULT_INI_SETTING'); ?>: <strong><?php echo ini_get('upload_max_filesize'); ?></strong>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_MAX_UPLOAD_FILE_SIZE_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_REQUIRE_APPROVE_DOCUMENT'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['require_admin_approve']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_REQUIRE_APPROVE_DOCUMENT_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_AUTO_ASSIGN_AS_OWNER'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['owner_assigned']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_AUTO_ASSIGN_AS_OWNER_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_CHANGE_DOCUMENT_PATH_WHEN_CHANGIN_MAIN_CATEGORY'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['move_document_when_changing_category']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_CHANGE_DOCUMENT_PATH_WHEN_CHANGIN_MAIN_CATEGORY_EXPLAIN'); ?>
                        </td>
                    </tr>
					<tr>
						<td>
							<?php echo JText::_('EDOCMAN_HIDE_DOWNLOAD_BUTTON'); ?>
						</td>
						<td>
							<?php echo $this->lists['hide_download_button']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_HIDE_DOWNLOAD_BUTTON_EXPLAIN'); ?>
						</td>
					</tr>
                    <tr>
                        <td class="key" width="10%">
                            <?php echo JText::_('EDOCMAN_OVERWRITE_EXISTING_FILE') ; ?>
                        </td>
                        <td width="30%">
                            <?php echo $this->lists['overwrite_existing_file']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_OVERWRITE_EXISTING_FILE_EXPLAIN') ; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" width="10%">
                            <?php echo JText::_('EDOCMAN_SHOW_UPLOADER_NAME') ; ?>
                        </td>
                        <td width="30%">
                            <?php echo $this->lists['show_uploader_name_in_document_mamangement']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_SHOW_UPLOADER_NAME_EXPLAIN') ; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_AUTO_INCREASE_DOCUMENT_VERSION'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['increase_document_version']; ?>
                        </td>
                        <td class="tdexplanation">
                            &nbsp;<?php echo JText::_('EDOCMAN_AUTO_INCREASE_DOCUMENT_VERSION_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_DOWNLOAD_CONFIGURATION') ; ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="key">
                            <?php echo JText::_('EDOCMAN_DOWNLOAD_TYPE');?>
                        </td>
                        <td>
                            <?php
                            echo $this->lists['download_type'] ;
                            ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_DOWNLOAD_TYPE_EXPLAIN');?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_LOG_DOWNLOADS'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['download_log']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_LOG_DOWNLOADS_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_RESET_DOWNLOAD_LOGS_AFTER_UPDATING'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['reset_downloadlog']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_RESET_DOWNLOAD_LOGS_AFTER_UPDATING_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="key">
                            <?php echo JText::_('EDOCMAN_COLLECT_DOWNLOADER_INFORMATION');?>
                        </td>
                        <td>
                            <?php
                            echo $this->lists['collect_downloader_information'] ;
                            ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_COLLECT_DOWNLOADER_INFORMATION_EXPLAIN');?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="key">
                            <?php echo JText::_('EDOCMAN_ONETIME_COLLECTION');?>
                        </td>
                        <td>
                            <?php
                            echo $this->lists['onetime_collect'] ;
                            ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_ONETIME_COLLECTION_EXPLAIN');?>
                        </td>
                    </tr>
                    <tr>
                        <td width="30%" class="key">
                            <?php echo JText::_('EDOCMAN_EXTERNAL_DOWNLOAD_LINK');?>
                        </td>
                        <td>
                            <?php
                            echo $this->lists['external_download_link'] ;
                            ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_EXTERNAL_DOWNLOAD_LINK_EXPLAIN');?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" width="10%">
                            <?php echo JText::_('EDOCMAN_LOGIN_TO_DOWNLOAD') ; ?>
                        </td>
                        <td width="30%">
                            <?php echo $this->lists['login_to_download']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_LOGIN_TO_DOWNLOAD_EXPLAIN') ; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_VIEW_DOCUMENT_CONFIGURATION') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td>
							<?php echo JText::_('EDOCMAN_SHOW_VIEW_BUTTON'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_view_button']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_VIEW_BUTTON_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('EDOCMAN_USE_GOOGLE_VIEWER'); ?>
						</td>
						<td>
							<?php echo $this->lists['use_googleviewer']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_USE_GOOGLE_VIEWER_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('EDOCMAN_USE_GOOGLE_VIEW_WITH_ANY_PERMISSIONS'); ?>
						</td>
						<td>
							<?php echo $this->lists['showing_document_googleviewer']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_USE_GOOGLE_VIEW_WITH_ANY_PERMISSIONS_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_FILE_EXTENSIONS_FOR_VIEWING') ; ?>
						</td>
						<td>
							<input type="text" name="exts_for_view" class="inputbox" value="<?php echo $this->config->exts_for_view; ?>" size="50" />
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_FILE_EXTENSIONS_FOR_VIEWING_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_VIEW_OPTION') ; ?>
						</td>
						<td>
							<?php echo $this->lists['view_option'];?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_VIEW_OPTION_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo JText::_('EDOCMAN_ENABLE_DOCUMENT_VIEW_URL'); ?>
						</td>
						<td>
							<?php echo $this->lists['view_url']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_ENABLE_DOCUMENT_VIEW_URL_EXPLAIN'); ?>
						</td>
					</tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_MEDIA_CONFIGURATION') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_CATEGORY_THUMBNAIL_WIDTH') ; ?>
						</td>
						<td>
							<input type="text" name="category_thumb_width" class="input-mini" value="<?php echo $this->config->category_thumb_width; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_CATEGORY_THUMBNAIL_HEIGHT') ; ?>
						</td>
						<td>
							<input type="text" name="category_thumb_height" class="input-mini" value="<?php echo $this->config->category_thumb_height; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOCUMENT_THUMB_WIDTH1') ; ?>
						</td>
						<td>
							<input type="text" name="document_thumb_width" class="input-mini" value="<?php echo $this->config->document_thumb_width; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOCUMENT_THUMB_HEIGHT1') ; ?>
						</td>
						<td>
							<input type="text" name="document_thumb_height" class="input-mini" value="<?php echo $this->config->document_thumb_height; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DATE_FORMAT') ; ?>
						</td>
						<td>
							<input type="text" name="date_format" class="input-mini" value="<?php echo $this->config->date_format; ?>" size="10" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DAYS_FOR_NEW') ; ?>
						</td>
						<td>
							<input type="text" name="day_for_new" class="input-mini" value="<?php echo $this->config->day_for_new; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_DAYS_FOR_NEW_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DAYS_FOR_UPDATE') ; ?>
						</td>
						<td>
							<input type="text" name="day_for_update" class="input-mini" value="<?php echo $this->config->day_for_update; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_DAYS_FOR_UPDATE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_TO_HOT') ; ?>
						</td>
						<td>
							<input type="text" name="downloads_to_hot" class="input-mini" value="<?php echo $this->config->downloads_to_hot; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_TO_HOT_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_ENABLE_RSS') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['enable_rss']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_PROCESSING_CONTENT_PLUGIN'); ?>
						</td>
						<td>
							<?php echo $this->lists['process_plugin']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_SEARCH_CONFIGURATION') ; ?></h3>
                        </td>
                    </tr>
                    <tr>
                        <td class="key" width="10%">
                            <?php echo JText::_('EDOCMAN_SEARCH_WITH_ALL_SUB_CATEGORIES') ; ?>
                        </td>
                        <td width="30%">
                            <?php echo $this->lists['search_with_sub_cats']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_SEARCH_WITH_ALL_SUB_CATEGORIES_EXPLAIN') ; ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_OTHER_CONFIGURATION') ; ?></h3>
                        </td>
                    </tr>
					<?php
					$comments = JPATH_ROOT.'/components/com_jcomments/jcomments.php';
					if (file_exists($comments))
					{
						?>
						<tr>
							<td class="key">
								<?php echo JText::_('EDOCMAN_JCOMMENT_INTEGRATION'); ?>
							</td>
							<td>
								<?php echo $this->lists['jcomment_integration']; ?>
							</td>
							<td>
								&nbsp;
							</td>
						</tr>
					<?php
					}
					?>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_USING_DEFAULT_LICENSE') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['use_default_license']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_USING_DEFAULT_LICENSE_EXPLAIN') ; ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_ACCEPT_LICENSE_BEFORE_DOWNLOAD') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['accept_license']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_ACCEPT_LICENSE_BEFORE_DOWNLOAD_EXPLAIN') ; ?>
						</td>
					</tr>
					<tr>
						<td class="key" width="10%">
							<?php echo JText::_('EDOCMAN_TURNON_LOCK_FEATURE') ; ?>
						</td>
						<td width="30%">
							<?php echo $this->lists['lock_function']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_TURNON_LOCK_FEATURE_EXPLAIN') ; ?>
						</td>
					</tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_TURN_ON_PRIVACY'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['turn_on_privacy']; ?>
                        </td>
                        <td class="tdexplanation">
                            &nbsp;<?php echo JText::_('EDOCMAN_TURN_ON_PRIVACY_EXPLAIN'); ?>
                        </td>
                    </tr>
				</table>
			<?php echo JHtml::_('bootstrap.endTab') ?>

			<?php echo JHtml::_('bootstrap.addTab', 'configTab', 'message-page', JText::_('EDOCMAN_MESSAGES')); ?>
				<table class="adminform" width="100%">
					<tr>
						<td class="key" width="18%">
							<?php echo JText::_('EDOCMAN_CAT_NOTIFICATION_EMAILS') ; ?>
						</td>
						<td>
							<input type="text" name="notification_emails" class="inputbox" value="<?php echo $this->config->notification_emails; ?>" size="50" />
						</td>
						<td class="tdexplanation">
							Email of users who will receive notificaiton when someone upload documents from front-end.You can put multiple email here, command seperated (For example sales@joomdonation.com,accounting@joomdonation.com)
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_NOTIFICATION') ; ?>
						</td>
						<td>
							<?php echo $this->lists['download_notification']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_NOTIFICATION_EXPLAIN') ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('Download notification email subject'); ?>
						</td>
						<td>
							<input type="text" name="download_email_subject" class="inputbox" value="<?php echo $this->config->download_email_subject; ?>" size="70" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_EMAIL_BODY'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="download_email_body" class="input-xxlarge"><?php echo $this->config->download_email_body;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags : [USERNAME], [DOCUMENT_TITLE], [USER_IP]
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_LINK_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="download_link_email_subject" class="inputbox" value="<?php echo $this->config->download_link_email_subject; ?>" size="70" />
						</td>
						<td class="tdexplanation">
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_LINK_BODY'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="download_link_email_body" class="input-xxlarge"><?php echo $this->config->download_link_email_body;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags : [NAME], [EMAIL], [DOCUMENT_TITLE], [DOWNLOAD_LINK]
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_COMPLETE_MESSAGE'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="download_complete_message" class="input-xxlarge"><?php echo $this->config->download_complete_message;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags : [DOCUMENT_TITLE], [DOWNLOAD_LINK]
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOWNLOAD_COMPLETE_MESSAGE1'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="download_complete_message_send_download_link" class="input-xxlarge"><?php echo $this->config->download_complete_message_send_download_link;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags : [DOCUMENT_TITLE]
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_UPLOAD_NOTIFICATION') ; ?>
						</td>
						<td>
							<?php echo $this->lists['upload_notification']; ?>
						</td>
						<td class="tdexplanation">
							If set to Yes, administrators will received notification email when someone download free documents
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_UPLOAD_EMAIL_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="upload_email_subject" class="inputbox" value="<?php echo $this->config->upload_email_subject; ?>" size="70" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_UPLOAD_EMAIL_BODY'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="upload_email_body" class="input-xxlarge"><?php echo $this->config->upload_email_body;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags : [USERNAME], [DOCUMENT_TITLE], [USER_IP]
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOCUMENT_ASSIGNED_NOTIFICATION') ; ?>
						</td>
						<td>
							<?php echo $this->lists['document_assigned_notification']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_DOCUMENT_ASSIGNED_NOTIFICATION_EXPLAIN') ; ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOCUMENT_ASSIGNED_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="document_assigned_email_subject" class="inputbox" value="<?php echo $this->config->document_assigned_email_subject; ?>" size="70" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOCUMENT_ASSIGNED_BODY'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="document_assigned_email_body" class="input-xxlarge"><?php echo $this->config->document_assigned_email_body;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags :[NAME], [USERNAME], [OWNER_USERNAME], [OWNER_NAME], [DOCUMENT_TITLE]
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_EMAIL_SHARE_SUBJECT'); ?>
						</td>
						<td>
							<input type="text" name="document_share_email_subject" class="inputbox" value="<?php echo $this->config->document_share_email_subject; ?>" size="70" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_EMAIL_SHARE_CONTENT'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="document_share_email_content" class="input-xxlarge"><?php echo $this->config->document_share_email_content;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags :[NAME], [FRIEND_NAME], [DOCUMENT_TITLE], [LINK], [MESSAGE]
						</td>
					</tr>

					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_HEADERTEXT_UPLOAD_FORM'); ?>
						</td>
						<td>
							<textarea rows="10" cols="70" name="header_text" class="input-xxlarge"><?php echo $this->config->header_text;?></textarea>
						</td>
						<td class="tdexplanation">
							Available tags : [CATEGORY]
						</td>
					</tr>
				</table>
			<?php echo JHtml::_('bootstrap.endTab') ?>

			<?php echo JHtml::_('bootstrap.addTab', 'configTab', 'theme-page', JText::_('EDOCMAN_THEMES')); ?>
				<table class="adminform" width="100%">
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_FRONTEND_UPLOAD') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_UPLOAD_DOCUMENT_FORM'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['use_simple_upload_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_UPLOAD_DOCUMENT_FORM_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_ALIAS'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_alias_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_ALIAS_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_THUMB'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_thumb_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_THUMB_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_PUBLISHED'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_published_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_PUBLISHED_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_TAG'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_tag_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_TAG_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_META'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_meta_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_META_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_LICENSE'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_license_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_LICENSE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_LOCK'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_lock_form']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_LOCK_EXPLAIN'); ?>
						</td>
					</tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_GENERAL') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_DEFAULT_CATEGORY_THUMBNAIL'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_default_category_thumbnail']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_DEFAULT_CATEGORY_THUMBNAIL_DESC'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_DEFAULT_DOCUMENT_THUMBNAIL'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_default_document_thumbnail']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_DEFAULT_DOCUMENT_THUMBNAIL_DESC'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_ICON_BESIDE_TITLE'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_icon_beside_title']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_ICON_BESIDE_TITLE_DESC'); ?>
						</td>
					</tr>
					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_USE_DOWNLOAD_LINK_TO_DOCUMENT_TITLE'); ?>
						</td>
						<td>
							<?php echo $this->lists['use_download_link_instead_of_detail_link']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_USE_DOWNLOAD_LINK_TO_DOCUMENT_TITLE_EXPLAIN'); ?>
						</td>
					</tr>

                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_CATEGORY_VIEW') ; ?></h3>
                        </td>
                    </tr>

					<tr>
						<td width="20%" class="key">
							<?php echo JText::_('EDOCMAN_SHOW_EMPTY_CATEGORIES'); ?>
						</td>
						<td width="15%">
							<?php echo $this->lists['show_empty_cat']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_NBER_DOCUMENTS'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_number_documents']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_CATEGORIES_PER_ROW'); ?>
						</td>
						<td>
							<?php echo $this->lists['number_categories_per_row']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_CATEGORIES_PER_PAGE'); ?>
						</td>
						<td>
							<input type="text" name="number_categories" class="input-mini" value="<?php echo $this->config->number_categories; ?>" size="10" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_NUMBER_CATEGORY_DESCRIPTION_WORDS'); ?>
						</td>
						<td>
							<input type="text" name="number_cwords" class="input-mini" value="<?php echo (int)$this->config->number_cwords; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_NUMBER_CATEGORY_DESCRIPTION_WORDS_EXPLAIN'); ?>
						</td>
					</tr>

                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_SUB_CATEGORIES') ; ?></h3>
                        </td>
                    </tr>

					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_SUB_CAT_ICON'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_subcategory_icon']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_SUB_CAT_DESC'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_subcategory_description']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_NBER_SUB_CATS_PER_ROW'); ?>
						</td>
						<td>
							<?php echo $this->lists['number_subcategories']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_CATEGORY_VIEW'); ?> - <?php echo JText::_('EDOCMAN_DOCUMENTS'); ?></h3>
                        </td>
                    </tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DOCUMENTS_PER_PAGE'); ?>
						</td>
						<td>
							<input type="text" name="number_documents" class="input-mini" value="<?php echo $this->config->number_documents; ?>" size="10" />
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_DEFAULT_STYLE'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['default_style'];?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_DEFAULT_STYLE_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_NCOLUMNS_IN_GRID_VIEW'); ?>
                        </td>
                        <td>
                            <?php
                            echo $this->lists['number_documents_in_grid'];
                            ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_NCOLUMNS_IN_GRID_VIEW_EXPLAIN'); ?>
                        </td>
                    </tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_NCOLUMNS_IN_COLUMN_LAYOUT'); ?>
						</td>
						<td>
                            <?php
                            echo $this->lists['number_columns'];
                            ?>
						</td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_NCOLUMNS_IN_COLUMN_LAYOUT_EXPLAIN'); ?>
                        </td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_DOCUMENT_DETAILS_IN_POPUP'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_detail_in_popup']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_DOCUMENT_DETAILS_IN_POPUP_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_NUMBER_DESCRIPTION_WORDS'); ?>
						</td>
						<td>
							<input type="text" name="number_words" class="input-mini" value="<?php echo (int)$this->config->number_words; ?>" size="10" />
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_NUMBER_DESCRIPTION_WORDS_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_PUBLISH_DATE'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_publish_date']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_HIDE_DETAILS_BUTTON'); ?>
						</td>
						<td>
							<?php echo $this->lists['hide_details_button']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_SORT_OPTIONS'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_sort_options']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DEFAULT_SORT_OPTION'); ?>
						</td>
						<td>
							<?php echo $this->lists['default_sort_option']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_DEFAULT_SORT_DIRECTION'); ?>
						</td>
						<td>
							<?php echo $this->lists['default_sort_direction']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>


                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_CATEGORY_TABLE_LAYOUT') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td class="key">
							<?php echo JText::_('Show File Type'); ?>
						</td>
						<td>
							<?php echo $this->lists['category_table_show_filetype']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_FILE_SIZE'); ?>
						</td>
						<td>
							<?php echo $this->lists['category_table_show_filesize']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_HEADER'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_tablelayoutheader']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_SHOW_DESCRIPTION'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['show_tablelayoutdescription']; ?>
                        </td>
                        <td>
                            &nbsp;
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="config_heading">
                            <h3><?php echo JText::_('EDOCMAN_DOCUMENT_DETAILS_VIEW') ; ?></h3>
                        </td>
                    </tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_SHARING_BUTTON'); ?>
						</td>
						<td>
							<?php echo $this->lists['turn_on_sharing']; ?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_SHOW_CATEGORY'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['show_category_name']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_SHOW_CATEGORY_EXPLAIN'); ?>
                        </td>
                    </tr>
					<tr>
						<td>
							<?php echo JText::_('EDOCMAN_SHOW_VERSION'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_document_version']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_VERSION_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_SOCIAL_SHARING_BUTTONS'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_social_sharing_buttons']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_SOCIAL_SHARING_BUTTONS_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_BOOKMARK_BUTTON'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_bookmark_button']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_BOOKMARK_BUTTON_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_HITS'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_hits']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_HITS_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_NDOWNLOADED'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_number_downloaded']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_NDOWNLOADED_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_FILE_NAME'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_filename']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_FILE_NAME_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_FILE_SIZE'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_filesize']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_FILE_SIZE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_FILE_TYPE'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_filetype']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_FILE_TYPE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_CREATED_USER'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_creation_user']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_CREATED_USER_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_CREATED_DATE'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_creation_date']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_CREATED_DATE_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<?php echo JText::_('EDOCMAN_SHOW_MODIFIED_DATE'); ?>
						</td>
						<td>
							<?php echo $this->lists['show_modified_date']; ?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_SHOW_MODIFIED_DATE_EXPLAIN'); ?>
						</td>
					</tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_SHOW_RELATED_DOCUMENTS'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['show_related_documents']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_SHOW_RELATED_DOCUMENTS_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_RELATED_DOCUMENTS_IN_SAME_CATEGORY'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['related_documents_in_same_cat']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_RELATED_DOCUMENTS_IN_SAME_CATEGORY_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_RELATED_DOCUMENTS_IN_AUTHOR'); ?>
                        </td>
                        <td>
                            <?php echo $this->lists['related_documents_in_author']; ?>
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_RELATED_DOCUMENTS_IN_AUTHOR_EXPLAIN'); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="key">
                            <?php echo JText::_('EDOCMAN_NUMBER_RELATED_DOCUMENTS'); ?>
                        </td>
                        <td>
                            <input type="text" name="number_related_documents" class="input-mini" value="<?php echo ($this->config->number_related_documents > 0) ? $this->config->number_related_documents : 6; ?>" size="10" />
                        </td>
                        <td class="tdexplanation">
                            <?php echo JText::_('EDOCMAN_NUMBER_RELATED_DOCUMENTS_EXPLAIN'); ?>
                        </td>
                    </tr>
				</table>
			<?php echo JHtml::_('bootstrap.endTab') ?>

			<?php echo JHtml::_('bootstrap.addTab', 'configTab', 'sef-setting-page', JText::_('EDOCMAN_SEF_SETTINGS')); ?>
				<table class="adminform">
					<tr>
						<td width="30%" class="key">
							<?php echo JText::_('EDOCMAN_INSERT_ID'); ?>
						</td>
						<td>
							<?php
							echo $this->lists['insert_document_id'] ;
							?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_INSERT_ID_EXPLAIN'); ?>
						</td>
					</tr>

					<tr>
						<td width="30%" class="key">
							<?php echo JText::_('EDOCMAN_INSERT_CATID'); ?>
						</td>
						<td>
							<?php
							echo $this->lists['insert_category_id'] ;
							?>
						</td>
						<td class="tdexplanation">
							<?php echo JText::_('EDOCMAN_INSERT_CATID_EXPLAIN'); ?>
						</td>
					</tr>
					<tr>
						<td width="30%" class="key">
							<?php echo JText::_('EDOCMAN_INSERT_CATEGORY'); ?>
						</td>
						<td>
							<?php
							echo $this->lists['insert_category'] ;
							?>
						</td>
						<td>
							&nbsp;
						</td>
					</tr>
				</table>
			<?php echo JHtml::_('bootstrap.endTab') ?>
			<?php
			if($showCustomCss == 1){
			?>
				<?php echo JHtml::_('bootstrap.addTab', 'configTab', 'custom-css-page', JText::_('EDOCMAN_CUSTOM_CSS')); ?>
					<table  width="100%">
						<tr>
							<td>
								<?php
								$customCss = '';
								if (file_exists(JPATH_ROOT.'/components/com_edocman/assets/css/custom.css'))
								{
									$customCss = file_get_contents(JPATH_ROOT.'/components/com_edocman/assets/css/custom.css');
								}
								echo JEditor::getInstance($editorPlugin)->display('custom_css', $customCss, '100%', '550', '75', '8', false, null, null, null, array('syntax' => 'css'));
								?>
							</td>
						</tr>
					</table>
				<?php echo JHtml::_('bootstrap.endTab') ?>
			<?php
			}
			?>

			<?php echo JHtml::_('bootstrap.addTab', 'configTab', 'download-id', JText::_('DOWNLOAD ID')); ?>
				<table class="adminform">
					<tr>
						<td width="20%" class="key" style="vertical-align:top;">
							<?php echo JText::_('EDOCMAN_DOWNLOADID'); ?>
						</td>
						<td width="70%">
							<input type="text" name="download_id" class="input-xlarge" value="<?php echo $this->config->download_id; ?>" size="50" />
							<BR />
							Enter your <strong>Download ID</strong> into this config option to be able to use Joomla Update to update your site to latest version of Edocman whenever there is new version available. To register Download ID, please go to: <a href="http://joomdonation.com" target="_blank">www.joomdonation.com</a> and click on menu <a href="http://joomdonation.com/download-ids.html" target="_blank">Download ID</a>. <BR /><strong>Notice:</strong> You should login before you access to this page.
						</td>
					</tr>
				</table>
			<?php echo JHtml::_('bootstrap.endTab') ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
</div>
<input type="hidden" name="option" value="com_edocman" />
<input type="hidden" name="task" value="" />	
<script type="text/javascript">
	function resetPath() {
		var form = document.adminForm ;
		var path = '<?php echo $this->path; ?>';
		form.documents_path.value = path ;
	}
</script>
</form>
