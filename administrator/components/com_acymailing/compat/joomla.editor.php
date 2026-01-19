<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php

use Joomla\CMS\Editor\Editor as Editor;

class acyeditorHelper
{

    var $width = '95%';

    var $height = '600';

    var $cols = 100;

    var $rows = 30;

    var $editor = null;

    var $name = '';

    var $content = '';

    var $editorConfig = [];

    var $editorContent = '';

    public $myEditor;

    function __construct()
    {
        $config = acymailing_config();
        $this->editor = $config->get('editor', null);
        if (empty($this->editor)) $this->editor = null;
        if (!class_exists('Joomla\CMS\Editor\Editor')) {
            $this->myEditor = JFactory::getEditor($this->editor);
        } else {
            if (empty($this->editor)) {
                $user = JFactory::getUser();
                $this->editor = $user->getParam('editor', acymailing_getCMSConfig('editor'));
            }
            $this->myEditor = Editor::getInstance($this->editor);
        }
        $this->myEditor->initialise();

        if (ACYMAILING_J16 && $this->editor == 'tinymce') {
            $this->editorConfig['extended_elements'] = 'table[background|cellspacing|cellpadding|width|align|bgcolor|border|style|class|id],tr[background|width|bgcolor|style|class|id|valign],td[background|width|align|bgcolor|valign|colspan|rowspan|height|style|class|id|nowrap]';
        }
    }

    function setTemplate($id)
    {
        if (empty($id)) return;

        $cssurl = acymailing_completeLink((acymailing_isAdmin() ? '' : 'front').'template&task=load&tempid='.$id.'&time='.time());

        $classTemplate = acymailing_get('class.template');
        $filepath = $classTemplate->createTemplateFile($id);

        if ($this->editor == 'tinymce') {
            $this->editorConfig['content_css_custom'] = $cssurl.'&local=http';
            $this->editorConfig['content_css'] = '0';
        } elseif ($this->editor == 'jckeditor' || $this->editor == 'fckeditor') {
            $this->editorConfig['content_css_custom'] = $filepath;
            $this->editorConfig['content_css'] = '0';
            $this->editorConfig['editor_css'] = '0';
        } else {
            $fileurl = ACYMAILING_MEDIA_FOLDER.'/templates/css/template_'.$id.'.css?time='.time();
            $this->editorConfig['custom_css_url'] = $cssurl;
            $this->editorConfig['custom_css_file'] = $fileurl;
            $this->editorConfig['custom_css_path'] = $filepath;
            acymailing_setVar('acycssfile', $fileurl);
        }
    }

    function prepareDisplay()
    {
        $this->content = htmlspecialchars($this->content, ENT_COMPAT, 'UTF-8');
        ob_start();
        if (!ACYMAILING_J16) {
            echo $this->myEditor->display($this->name, $this->content, $this->width, $this->height, $this->cols, $this->rows, ['pagebreak', 'readmore'], $this->editorConfig);
        } else {
            echo $this->myEditor->display($this->name, $this->content, $this->width, $this->height, $this->cols, $this->rows, ['pagebreak', 'readmore'], null, 'com_content', null, $this->editorConfig);
        }

        $this->editorContent = ob_get_clean();
    }


    function setDescription()
    {
        $this->width = 700;
        $this->height = 200;
        $this->cols = 80;
        $this->rows = 10;
    }

    function GetInitialisationFunction($id)
    {
        $texteSuppression = acymailing_translation('ACYEDITOR_DELETEAREA');
        $tooltipSuppression = acymailing_translation('ACY_DELETE');
        $tooltipEdition = acymailing_translation('ACY_EDIT');
        $urlBase = acymailing_rootURI();
        $urlAdminBase = acymailing_baseURI();
        $cssurl = acymailing_getVar('none', 'acycssfile');
        $forceComplet = (acymailing_getVar('cmd', 'option') != 'com_acymailing' || acymailing_getVar('cmd', 'ctrl') == 'template' || acymailing_getVar('cmd', 'ctrl') == 'list');
        $modeList = (acymailing_getVar('cmd', 'option') == 'com_acymailing' && acymailing_getVar('cmd', 'ctrl') == 'list');
        $modeTemplate = (acymailing_getVar('cmd', 'option') == 'com_acymailing' && acymailing_getVar('cmd', 'ctrl') == 'template');
        $modeArticle = (acymailing_getVar('cmd', 'option') == 'com_content' && acymailing_getVar('cmd', 'view') == 'article');
        $joomla2_5 = ACYMAILING_J16;
        $joomla3 = ACYMAILING_J30;
        $titleTemplateDelete = acymailing_translation('ACYEDITOR_TEMPLATEDELETE');
        $titleTemplateText = acymailing_translation('ACYEDITOR_TEMPLATETEXT');
        $titleTemplatePicture = acymailing_translation('ACYEDITOR_TEMPLATEPICTURE');
        $titleShowAreas = acymailing_translation('ACYEDITOR_SHOWAREAS');
        $isBack = 0;
        if (acymailing_isAdmin()) {
            $isBack = 1;
        }
        $tagAllowed = 0;
        $config = acymailing_config();
        if (acymailing_getVar('cmd', 'option') == 'com_acymailing' && acymailing_getVar('cmd', 'ctrl') != 'list' && acymailing_getVar('cmd', 'ctrl') != 'campaign' && acymailing_isAllowed($config->get('acl_tags_view', 'all')) && acymailing_getVar('cmd', 'tmpl') != 'component') {
            $tagAllowed = 1;
        }
        $type = 'news';
        if (acymailing_getVar('cmd', 'ctrl') == 'autonews' || acymailing_getVar('cmd', 'ctrl') == 'followup') {
            $type = acymailing_getVar('cmd', 'ctrl');
        }

        $acyEditor = JPluginHelper::getPlugin('editors', 'acyeditor');
        if (is_string($acyEditor->params)) {
            $acyEditor->params = json_decode($acyEditor->params, true);
            $pasteType = !isset($acyEditor->params['pasteType']) ? 'plain' : $acyEditor->params['pasteType'];
            $enterMode = !isset($acyEditor->params['enterMode']) ? 'br' : $acyEditor->params['enterMode'];
            $inlineSource = !isset($acyEditor->params['inlineSource']) ? 1 : $acyEditor->params['inlineSource'];
        } else {
            $pasteType = $acyEditor->params->get('pasteType', 'plain');
            $enterMode = $acyEditor->params->get('enterMode', 'br');
            $inlineSource = $acyEditor->params->get('inlineSource', 1);
        }

        $js = "
		acyEnterMode='".$enterMode."';
		pasteType='".$pasteType."';
		urlSite='".$urlBase."';
		defaultText='".str_replace("'", "\'", acymailing_translation('ACYEDITOR_DEFAULTTEXT'))."';
		titleBtnMore='".str_replace("'", "\'", acymailing_translation('ACYEDITOR_TEMPLATEMORE'))."';
		titleBtnDupliAfter='".str_replace("'", "\'", acymailing_translation('ACYEDITOR_DUPLICATE_AFTER'))."';
		tooltipInitAreas='".str_replace("'", "\'", acymailing_translation('ACYEDITOR_REINIT_ZONE_TOOLTIP'))."';
		confirmInitAreas='".str_replace("'", "\'", acymailing_translation('ACYEDITOR_REINIT_ZONE_CONFIRMATION'))."';
		tooltipTemplateSortable='".str_replace("'", "\'", acymailing_translation('ACYEDITOR_SORTABLE_AREA_TOOLTIP'))."';
		var bgroundColorTxt='".str_replace("'", "\'", acymailing_translation('BACKGROUND_COLOUR'))."';
		var confirmDeleteBtnTxt='".str_replace("'", "\'", acymailing_translation('ACY_DELETE'))."';
		var confirmCancelBtnTxt='".str_replace("'", "\'", acymailing_translation('ACY_CANCEL'))."';
		inlineSource='".$inlineSource."';
		var emojis = false;
		";

        $installedPlugin = JPluginHelper::getPlugin('acymailing', 'emojis');
        if (!empty($installedPlugin)) {
            $params = new acyParameter($installedPlugin->params);
            if (JPluginHelper::isEnabled('acymailing', 'emojis') && $params->get('editor', 1) == 1) {
                $js .= "emojis = true;";
            }
        }

        acymailing_addScript(true, $js);

        $ckEditorFileVersion = @filemtime(ACYMAILING_ROOT.'plugins'.DS.'editors'.DS.'acyeditor'.DS.'acyeditor'.DS.'ckeditor'.DS.'ckeditor.js');

        return "
		    Initialisation(\"$id\", \"$type\", \"$urlBase\", \"$urlAdminBase\", \"$cssurl\", \"$forceComplet\", \"$modeList\", \"$modeTemplate\", \"$modeArticle\", \"$joomla2_5\", \"$joomla3\", \"$isBack\", \"$tagAllowed\", \"$texteSuppression\", \"$tooltipSuppression\", \"$tooltipEdition\", \"$titleTemplateDelete\", \"$titleTemplateText\", \"$titleTemplatePicture\", \"$titleShowAreas\", \"$ckEditorFileVersion\");\n
		";
    }

    function setContent($var)
    {
        if (method_exists($this->myEditor, 'setContent')) {
            $function = "try{ Joomla.editors.instances['".$this->name."'].setValue(".$var."); }catch(err){alert('Error using the setContent function of the wysiwyg editor')} ";
            $function = "try{".$this->myEditor->setContent($this->name, $var)." }catch(err){".$function."}";
        } else {
            if (ACYMAILING_J40 && $this->editor === 'acyeditor') {
                $initialisation = $this->GetInitialisationFunction($this->name);
                $function = "try{ Joomla.editors.instances['".$this->name."'].setValue(".$var."); }catch(err){alert('Error using the setContent function of the wysiwyg editor')} ";
                $function = "try{document.getElementById('$this->name').value = $var;$initialisation"." }catch(err){".$function."}";
            } else {
                $function = "alert('There is no setContent method defined for this editor');";
            }
        }

        if (!empty($this->editor)) {
            if ($this->editor == 'jce') {
                return " try{JContentEditor.setContent('".$this->name."', $var ); }catch(err){try{WFEditor.setContent('".$this->name."', $var )}catch(err){".$function."} }";
            }
            if ($this->editor == 'fckeditor') {
                return " try{FCKeditorAPI.GetInstance('".$this->name."').SetHTML( $var ); }catch(err){".$function."} ";
            }
            if ($this->editor == 'jckeditor') {
                return " try{oEditor.setData(".$var.");}catch(err){(!oEditor) ? CKEDITOR.instances.".$this->name.".setData($var) : oEditor.insertHtml = ".$var.'}';
            }
            if ($this->editor == 'ckeditor') {
                return " try{CKEDITOR.instances.".$this->name.".setData( $var ); }catch(err){".$function."} ";
            }
            if ($this->editor == 'artofeditor') {
                return " try{CKEDITOR.instances.".$this->name.".setData( $var ); }catch(err){".$function."} ";
            }
            if ($this->editor == 'tinymce') {
                return ' try{ Joomla.editors.instances["'.$this->name.'"].setValue('.$var.'); }catch(err){'.$function.'} ';
            }
        }

        return $function;
    }

    function setEditorStylesheet($tempid)
    {
        $cssurl = acymailing_completeLink((acymailing_isAdmin() ? '' : 'front').'template&task=load&time='.time().'&tempid=');

        $function = 'if('.$tempid.' !== 0){
						try{
							setEditorStylesheet(\''.$this->name.'\',\''.$cssurl.'\'+'.$tempid.',\''.ACYMAILING_MEDIA_FOLDER.'/templates/css/template_\'+'.$tempid.'+\'.css\');
						}catch(err){
							var iframe = document.getElementById("'.$this->name.'_ifr");
							if(typeof iframe != undefined && iframe){
								var css = iframe.contentDocument.querySelector(\'link[href*="'.ACYMAILING_MEDIA_FOLDER.'/templates/css/template_"]\');
								if(typeof css != undefined && css){
									css.href = css.href.replace(/template_\d{1,10}.css/, "template_"+'.$tempid.'+".css");
								}else{
									var css = iframe.contentDocument.querySelector(\'link[href*="com_acymailing&ctrl=template&task=load&tempid="]\');
									if(typeof css != undefined && css){
										css.href = css.href.replace(/&tempid=\d{1,10}&time/, "&tempid="+'.$tempid.'+"&time");
									}
								}
							}
						}
					}';

        return $function;
    }

    function getContent()
    {
        return $this->myEditor->getContent($this->name);
    }

    function display()
    {
        if (empty($this->editorContent)) {
            $this->prepareDisplay();
        }

        return $this->editorContent;
    }

    function jsCode()
    {
        return method_exists($this->myEditor, 'save') ? $this->myEditor->save($this->name) : '';
    }

    function jsMethods()
    {
        return '';
    }

}//endclass

