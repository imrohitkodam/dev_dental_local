<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Dang Thuc Dam
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die ;
/**
 * HTML View class for Edocman component
 *
 * @static
 * @package        Joomla
 * @subpackage     EDocman
 */
class EdocmanViewConfigurationHtml extends OSViewHtml
{
    public static function showCheckboxfield($name, $value ,$option1='',$option2='')
    {

        if($option1 == ""){
            $option1 = JText::_('JNO');
        }
        if($option2 == ""){
            $option2 = JText::_('JYES');
        }

        JHtml::_('jquery.framework');
        $field = JFormHelper::loadFieldType('Radio');

        $element = new SimpleXMLElement('<field />');
        $element->addAttribute('name', $name);

        if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
        {
            $element->addAttribute('class', 'switcher');
        }
        else
        {
            $element->addAttribute('class', 'radio btn-group btn-group-yesno');
        }

        $element->addAttribute('default', '0');

        $node = $element->addChild('option', $option1);
        $node->addAttribute('value', '0');

        $node = $element->addChild('option', $option2);
        $node->addAttribute('value', '1');

        $field->setup($element, $value);

        return $field->input;
    }

	function display($tpl = null)
	{	
		EDocmanHelperHtml::renderSubmenu('configuration');
		$config = EdocmanHelper::getConfig();		
		$lists['overwrite_existing_file']		= self::showCheckboxfield('overwrite_existing_file',(int)$config->overwrite_existing_file);   

		$lists['enable_rss']					= self::showCheckboxfield('enable_rss',(int)$config->enable_rss); 

		$lists['process_plugin']				= self::showCheckboxfield('process_plugin',(int)$config->process_plugin);  

		$lists['download_log']					= self::showCheckboxfield('download_log',(int)$config->download_log);  

        $lists['load_twitter_bootstrap']		= self::showCheckboxfield('load_twitter_bootstrap',isset($config->load_twitter_bootstrap) ? $config->load_twitter_bootstrap : 1); 

		$lists['load_jquery']					= self::showCheckboxfield('load_jquery',isset($config->load_jquery) ? $config->load_jquery : 1);

		$lists['activate_herachical_folder_structure'] = self::showCheckboxfield('activate_herachical_folder_structure',(int)$config->activate_herachical_folder_structure); 

		$lists['activate_multilingual_feature'] = self::showCheckboxfield('activate_multilingual_feature',(int)$config->activate_multilingual_feature);

        $lists['access_level_inheritance']      = self::showCheckboxfield('access_level_inheritance',(int)$config->access_level_inheritance);

		$lists['user_group_ids'] 				= self::showCheckboxfield('user_group_ids',(int)$config->user_group_ids);

        $lists['remove_category_folder'] 		= self::showCheckboxfield('remove_category_folder',(int)$config->remove_category_folder);

		$options 								= array() ;
		$options[] 								= JHtml::_('select.option', 0, JText::_('Upload new file for each document'));
		$options[] 								= JHtml::_('select.option', 1, JText::_('Choose file from server'));
		
		$lists['file_upload_method'] = JHtml::_('select.genericlist', $options, 'file_upload_method', 'class="inputbox chosen"', 'value', 'text', $config->file_upload_method);
		
		$options 								= array() ;
		$options[] 								=  JHtml::_('select.option',  0, JText::_('Select'));
		$options[] 								= JHtml::_('select.option', '1', JText::_('Byte'));
		$options[] 								= JHtml::_('select.option', '2', JText::_('Kb'));
		$options[] 								= JHtml::_('select.option', '3', JText::_('Mb'));
		$lists['max_filesize_type'] = JHtml::_('select.genericlist', $options, 'max_filesize_type', 'class="input-small chosen"', 'value', 'text', $config->max_filesize_type ? $config->max_filesize_type : 3);
		
		$lists['require_admin_approve']			= self::showCheckboxfield('require_admin_approve',(int)$config->require_admin_approve);  
																								
		$lists['download_notification']			= self::showCheckboxfield('download_notification',(int)$config->download_notification);  

		//User upload settings			
		$lists['upload_notification']			= self::showCheckboxfield('upload_notification',(int)$config->upload_notification);  
		
		$lists['document_assigned_notification'] = self::showCheckboxfield('document_assigned_notification',$config->document_assigned_notification); 

		$lists['jcomment_integration'] = self::showCheckboxfield('jcomment_integration',(int)$config->jcomment_integration);  
		
		$lists['delete_file_when_document_deleted'] = self::showCheckboxfield('delete_file_when_document_deleted',(int)$config->delete_file_when_document_deleted);

        $lists['move_document_when_changing_category'] = self::showCheckboxfield('move_document_when_changing_category',(int)$config->move_document_when_changing_category);
		
		$lists['show_default_category_thumbnail'] = self::showCheckboxfield('show_default_category_thumbnail',(int)$config->show_default_category_thumbnail);  

		$lists['show_default_document_thumbnail'] = self::showCheckboxfield('show_default_document_thumbnail',(int)$config->show_default_document_thumbnail);
		
		$lists['show_icon_beside_title']		= self::showCheckboxfield('show_icon_beside_title',(int)$config->show_icon_beside_title); 
		
		$lists['show_empty_cat']				= self::showCheckboxfield('show_empty_cat',(int)$config->show_empty_cat);  

		$lists['show_number_documents']			= self::showCheckboxfield('show_number_documents',(int)$config->show_number_documents);   

		$lists['number_categories']				= self::showCheckboxfield('number_categories',(int)$config->number_categories);  
				
		$lists['number_documents']				= self::showCheckboxfield('number_documents',(int)$config->number_documents);   

		//Sub-categories settings
		$lists['show_subcategory_icon'] = self::showCheckboxfield('show_subcategory_icon',(int)$config->show_subcategory_icon);  

		$lists['show_subcategory_description']	= self::showCheckboxfield('show_subcategory_description',(int)$config->show_subcategory_description);  
		
		$lists['use_download_link_instead_of_detail_link'] = self::showCheckboxfield('use_download_link_instead_of_detail_link',(int)$config->use_download_link_instead_of_detail_link);  

		$lists['category_table_show_filesize']	= self::showCheckboxfield('category_table_show_filesize',(int)$config->category_table_show_filesize);  

		$lists['category_table_show_filetype']	= self::showCheckboxfield('category_table_show_filetype',(int)$config->category_table_show_filetype); 

		
		$lists['show_view_button']				= self::showCheckboxfield('show_view_button',(int)$config->show_view_button);
		$lists['view_url']						= self::showCheckboxfield('view_url',(int)$config->view_url);

		$lists['hide_download_button']			= self::showCheckboxfield('hide_download_button',(int)$config->hide_download_button); 

		$lists['show_hits']						= self::showCheckboxfield('show_hits',(int)$config->show_hits); 

		$lists['show_filename']					= self::showCheckboxfield('show_filename',(int)$config->show_filename); 

		$lists['show_filesize']					= self::showCheckboxfield('show_filesize',(int)$config->show_filesize); 

		$lists['show_filetype']					= self::showCheckboxfield('show_filetype',(int)$config->show_filetype); 

		$lists['show_creation_date']			= self::showCheckboxfield('show_creation_date',(int)$config->show_creation_date);

		$lists['show_creation_user']			= self::showCheckboxfield('show_creation_user',(int)$config->show_creation_user);

		$lists['show_number_downloaded']		= self::showCheckboxfield('show_number_downloaded',(int)$config->show_number_downloaded);

		$lists['show_modified_date']			= self::showCheckboxfield('show_modified_date',(int)$config->show_modified_date); 

		$lists['show_detail_in_popup']			= self::showCheckboxfield('show_detail_in_popup',(int)$config->show_detail_in_popup); 

		$lists['show_publish_date']				= self::showCheckboxfield('show_publish_date',(int)$config->show_publish_date); 

		$lists['hide_details_button']			= self::showCheckboxfield('hide_details_button',(int)$config->hide_details_button);

        $lists['show_sort_options']				= self::showCheckboxfield('show_sort_options',(int)$config->show_sort_options);

		$lists['show_document_version']			= self::showCheckboxfield('show_document_version',(int)$config->show_document_version);

        $lists['reset_downloadlog']				= self::showCheckboxfield('reset_downloadlog',(int)$config->reset_downloadlog);

		$lists['use_googleviewer']				= self::showCheckboxfield('use_googleviewer',(int)$config->use_googleviewer);

		$lists['showing_document_googleviewer']	= self::showCheckboxfield('showing_document_googleviewer',(int)$config->showing_document_googleviewer);

		$lists['show_tablelayoutheader']		= self::showCheckboxfield('show_tablelayoutheader',(int)$config->show_tablelayoutheader);

        $lists['show_tablelayoutdescription']   = self::showCheckboxfield('show_tablelayoutdescription',(int)$config->show_tablelayoutdescription);

        $lists['login_to_download']				= self::showCheckboxfield('login_to_download',(int)$config->login_to_download);

		$lists['lock_function']					= self::showCheckboxfield('lock_function',(int)$config->lock_function);

		$lists['owner_assigned']				= self::showCheckboxfield('owner_assigned',(int)$config->owner_assigned);

		$options = array();
		$options[] 								= JHtml::_('select.option', 'tbl.title', JText::_('EDOCMAN_TITLE'));
		$options[] 								= JHtml::_('select.option', 'tbl.created_time', JText::_('JGLOBAL_CREATED_DATE'));
        $options[] 								= JHtml::_('select.option', 'tbl.modified_time', JText::_('JGLOBAL_FIELD_MODIFIED_LABEL'));
		$options[]								= JHtml::_('select.option', 'tbl.ordering', JText::_('EDOCMAN_ORDERING'));
        $options[]  							= JHtml::_('select.option', 'tbl.file_size', JText::_('EDOCMAN_FILESIZE'));
		$lists['default_sort_option'] = JHtml::_('select.genericlist', $options, 'default_sort_option', 'class="chosen"', 'value', 'text', $config->default_sort_option ? $config->default_sort_option : 'tbl.ordering');

		$options								= array();
		$options[] 								= JHtml::_('select.option', 2, JText::_('EDOCMAN_VERSION_2'));
		$options[]								= JHtml::_('select.option', 3, JText::_('EDOCMAN_VERSION_3'));
		$options[]								= JHtml::_('select.option', 4, JText::_('EDOCMAN_VERSION_4'));
		$lists['twitter_bootstrap_version']		= JHtml::_('select.genericlist', $options, 'twitter_bootstrap_version', 'class="chosen"', 'value', 'text', $config->twitter_bootstrap_version ? $config->twitter_bootstrap_version : 2);

		$options 								= array();
		$options[] 								= JHtml::_('select.option', 0, JText::_('Self'));
		$options[] 								= JHtml::_('select.option', 1, JText::_('Blank'));
		$lists['external_download_link'] = JHtml::_('select.genericlist', $options, 'external_download_link', 'class="chosen"', 'value', 'text', $config->external_download_link ? $config->external_download_link : 0);

		$options 								= array();
		$options[] 								= JHtml::_('select.option', 'asc', JText::_('EDOCMAN_ASC'));
		$options[] 								= JHtml::_('select.option', 'desc', JText::_('EDOCMAN_DESC'));
		$lists['default_sort_direction']		= JHtml::_('select.genericlist', $options, 'default_sort_direction', 'class="chosen"', 'value', 'text', $config->default_sort_direction);
		$lists['show_uploader_name_in_document_mamangement'] = self::showCheckboxfield('show_uploader_name_in_document_mamangement',(int)$config->show_uploader_name_in_document_mamangement); 

        $lists['show_social_sharing_buttons']	= self::showCheckboxfield('show_social_sharing_buttons',(int)$config->show_social_sharing_buttons); 

		$lists['show_bookmark_button']			= self::showCheckboxfield('show_bookmark_button',(int)$config->show_bookmark_button); 

		//Fields configuration		
        //$lists['use_simple_upload_form']		= self::showCheckboxfield('use_simple_upload_form',(int)$config->use_simple_upload_form);

        $options 								= array();
        $options[] 								= JHtml::_('select.option', '0', JText::_('EDOCMAN_DEFAULT_FORM'));
        $options[] 								= JHtml::_('select.option', '1', JText::_('EDOCMAN_SIMPLE_FORM'));
        $options[] 								= JHtml::_('select.option', '2', JText::_('EDOCMAN_AJAX_UPLOAD'));
        $lists['use_simple_upload_form']		= JHtml::_('select.genericlist', $options, 'use_simple_upload_form', 'class="chosen"', 'value', 'text', $config->use_simple_upload_form);

		$lists['show_alias_form']				= self::showCheckboxfield('show_alias_form',isset($config->show_alias_form) ? $config->show_alias_form : 1);

		$lists['show_thumb_form']				= self::showCheckboxfield('show_thumb_form',isset($config->show_thumb_form) ? $config->show_thumb_form : 1);

		$lists['show_lock_form']				= self::showCheckboxfield('show_lock_form',isset($config->show_lock_form) ? $config->show_lock_form : 0);

		$lists['show_published_form']			= self::showCheckboxfield('show_published_form',isset($config->show_published_form) ? $config->show_published_form : 0);

		$lists['show_meta_form']				= self::showCheckboxfield('show_meta_form',isset($config->show_meta_form) ? $config->show_meta_form : 1);

		$lists['show_license_form']				= self::showCheckboxfield('show_license_form',isset($config->show_license_form) ? $config->show_license_form : 1);

		$lists['show_tag_form']					= self::showCheckboxfield('show_tag_form',isset($config->show_tag_form) ? $config->show_tag_form : 1);

		$lists['insert_document_id']			= self::showCheckboxfield('insert_document_id',(int)$config->insert_document_id);

        $lists['insert_category_id']			= self::showCheckboxfield('insert_category_id',(int)$config->insert_category_id);

		$lists['use_default_license']			= self::showCheckboxfield('use_default_license',(int)$config->use_default_license);

        $lists['accept_license']				= self::showCheckboxfield('accept_license',(int)$config->accept_license);

        $lists['search_with_sub_cats']			= self::showCheckboxfield('search_with_sub_cats',(int)$config->search_with_sub_cats);

		$lists['turn_on_sharing']				= self::showCheckboxfield('turn_on_sharing',(int)$config->turn_on_sharing);

        $lists['turn_on_privacy']				= self::showCheckboxfield('turn_on_privacy',(int)$config->turn_on_privacy);

        $lists['default_style']				    = self::showCheckboxfield('default_style',(int)$config->default_style, JText::_('EDOCMAN_LIST'), JText::_('EDOCMAN_GRID'));;

        $lists['show_category_name']			= self::showCheckboxfield('show_category_name',(int)$config->show_category_name);

		$lists['view_option']					= self::showCheckboxfield('view_option',(int)$config->view_option,JText::_('EDOCMAN_MODAL_BOX'),JText::_('EDOCMAN_NEW_WINDOW'));

        $lists['show_newsletter_subscription']  = self::showCheckboxfield('view_option',(int)$config->show_newsletter_subscription);

        $lists['show_related_documents']        = self::showCheckboxfield('show_related_documents',(int)$config->show_related_documents);

        $lists['related_documents_in_same_cat'] = self::showCheckboxfield('related_documents_in_same_cat',(int)$config->related_documents_in_same_cat);

        $lists['related_documents_in_author']   = self::showCheckboxfield('related_documents_in_author',(int)$config->related_documents_in_author);

        $lists['increase_document_version']     = self::showCheckboxfield('increase_document_version',(int)$config->increase_document_version);

		$options								= array() ;
		$options[]								= JHtml::_('select.option', 0, JText::_('All nested categories'));
		$options[]								= JHtml::_('select.option', 1, JText::_('Only the last one'));
		$options[]								= JHtml::_('select.option', 2, JText::_('No insert'));
		$lists['insert_category']				= JHtml::_('select.genericlist', $options, 'insert_category', ' class="input-medium chosen"', 'value', 'text', $config->insert_category);

		$options								= array();
		$options[]								= JHtml::_('select.option', 1, 1);
		$options[]								= JHtml::_('select.option', 2, 2);
		$options[]								= JHtml::_('select.option', 3, 3);
		$options[]								= JHtml::_('select.option', 6, 6);
		$lists['number_categories_per_row']		= JHtml::_('select.genericlist', $options, 'number_categories_per_row', ' class="input-mini chosen"', 'value', 'text', isset($config->number_categories_per_row) ? $config->number_categories_per_row : 2);

        $options								= array();
        $options[]								= JHtml::_('select.option', 2, 2);
        $options[]								= JHtml::_('select.option', 3, 3);
        $options[]								= JHtml::_('select.option', 4, 4);
        $lists['number_documents_in_grid']		= JHtml::_('select.genericlist', $options, 'number_documents_in_grid', ' class="input-mini chosen"', 'value', 'text', isset($config->number_documents_in_grid) ? $config->number_documents_in_grid : 2);

        $options								= array();
        $options[]								= JHtml::_('select.option', 2, 2);
        $options[]								= JHtml::_('select.option', 3, 3);
        $options[]								= JHtml::_('select.option', 4, 4);
        $lists['number_columns']		        = JHtml::_('select.genericlist', $options, 'number_columns', ' class="input-mini chosen"', 'value', 'text', isset($config->number_columns) ? $config->number_columns : 2);

		$options 								= array();
		$options[]								= JHtml::_('select.option', 1, 1);
		$options[]								= JHtml::_('select.option', 2, 2);
		$options[] 								= JHtml::_('select.option', 3, 3);
		$options[] 								= JHtml::_('select.option', 6, 6);
		$lists['number_subcategories']			= JHtml::_('select.genericlist', $options, 'number_subcategories', ' class="input-mini chosen"', 'value', 'text', isset($config->number_subcategories) ? $config->number_subcategories : 2);

		// Collect downloader information
		$lists['collect_downloader_information'] = self::showCheckboxfield('collect_downloader_information',(int)$config->collect_downloader_information); 
		$lists['onetime_collect'] = self::showCheckboxfield('onetime_collect',(int)$config->onetime_collect); 

		$options 								= array() ;
		$options[] 								= JHtml::_('select.option', 0, JText::_('EDOCMAN_DIRECT_DOWNLOAD'));
		$options[] 								= JHtml::_('select.option', 1, JText::_('EDOCMAN_SEND_DOWNLOAD_LINK'));
		$lists['download_type']					= JHtml::_('select.genericlist', $options, 'download_type', ' class="inputbox chosen"', 'value', 'text', isset($config->download_type) ? $config->download_type : 1);

		$path = JPATH_ROOT.'/edocman' ;
		$path = str_replace("\\", "/", $path) ;
		//Get tab object					
		$this->lists = $lists;
		$this->config =	$config;				
		$this->path = $path;	
		// We don't need toolbar in the modal window.
		if (version_compare(JVERSION, '3.0', 'ge')) {
			if ($this->getLayout() !== 'modal')
			{
				//EdocmanHelper::addSideBarmenus('configuration');
				$this->sidebar = JHtmlSidebar::render();
			}
		}
		parent::display();
	}
}