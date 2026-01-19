<?php

/*
 * @package     Perfect Publisher
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// No direct access
defined('_JEXEC') || exit('Restricted access');

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Object\CMSObject;

/**
 * Utility class for all HTML drawing classes.
 *
 * @since       11.1
 */
abstract class EHtml extends JHtml
{
    /**
     * Compute the files to be included.
     *
     * @param string $folder         folder name to search into (images, css, js, ...)
     * @param string $file           path to file
     * @param bool   $relative       path to file is relative to /media folder
     * @param bool   $path_only      param
     * @param bool   $detect_browser detect browser to include specific browser files
     * @param bool   $detect_debug   detect debug to include compressed files if debug is on
     *
     * @return array files to be included
     *
     * @see     JBrowser
     * @since   11.1
     */
    public static function getRelativeFiles($folder, $file, $relative = true, $path_only = false, $detect_browser = false, $detect_debug = true)
    {
        return self::includeRelativeFiles($folder, $file, $relative, $detect_browser, false);
    }

    /**
     * Compute the files to be included.
     *
     * @param string $folder         folder name to search into (images, css, js, ...)
     * @param string $file           path to file
     * @param bool   $relative       path to file is relative to /media folder
     * @param bool   $path_only      param
     * @param bool   $detect_browser detect browser to include specific browser files
     * @param bool   $detect_debug   detect debug to include compressed files if debug is on
     *
     * @return array files to be included
     *
     * @see     JBrowser
     * @since   11.1
     */
    public static function getRelativeFile($folder, $file, $relative = true, $path_only = false, $detect_browser = false, $detect_debug = true)
    {
        $app = null;
        $includes = self::includeRelativeFiles($folder, $file, $relative, $detect_browser, false);

        if (count($includes) > 0) {
            return $includes[0];
        }

        return $app;
    }

    /**
     * generateIdTag.
     *
     * @return string
     */
    public static function generateIdTag()
    {
        $idTag = 'id'.random_int(1, 10000);
        $idTag = 'xtform'.$idTag;

        return $idTag;
    }

    /**
     * genericControl.
     *
     * @param string $label         Label
     * @param string $desc          Description
     * @param array  $name          Control name
     * @param string $control       Params
     * @param string $control_class Params
     *
     * @return string HTML
     */
    public static function genericControl($label, $desc, $name = null, $control = null, $control_class = null)
    {
        return '
		<div class="control-group '.$control_class."\">
		<label for=\"{$name}\" class=\"control-label\" rel=\"tooltip\" data-original-title=\"".JText::_($desc).'">'.JText::_($label).'</label>
		<div class="controls">'.$control.'</div>
		</div>

		';
    }

    /**
     * label.
     *
     * @param string $label Label
     * @param string $desc  Description
     * @param array  $name  Control name
     *
     * @return string HTML
     */
    public static function label($label, $desc, $name = null)
    {
        $for = '';

        if ($name) {
            $for = 'for=\"'.$name.'\" ';
        }

        return sprintf('<label %sclass="control-label" rel="tooltip" data-original-title="', $for).JText::_($desc).'">'.JText::_($label).'</label>';
    }

    /**
     * textControl.
     *
     * @param string $selected  Value
     * @param string $name      The name for the field
     * @param string $label     Label
     * @param string $desc      Description
     * @param array  $idTag     Additional HTML attributes for the <select> tag
     * @param int    $maxlength Param
     * @param mixed  $attrs     Param
     *
     * @return string HTML
     */
    public static function textControl($selected, $name, $label, $desc, $idTag = null, $maxlength = 32, $attrs = null)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $class = null;
        $extra = null;

        if (is_string($attrs)) {
            $class = $attrs;
        } elseif (is_array($attrs)) {
            if (array_key_exists('class', $attrs)) {
                $class = $attrs['class'];
                unset($attrs['class']);
            }

            $extra = \Joomla\Utilities\ArrayHelper::toString($attrs);
        }

        if (empty($class)) {
            $class = '';
        }

        if ($maxlength < 7) {
            $class .= ' input-mini';
        }

        if (!empty($class)) {
            $class = 'class="'.$class.'"';
        }

        $control = sprintf('<input type="text" name="%s" id="%s" value="%s" maxlength="%d" %s %s/>', $name, $idTag, $selected, $maxlength, $class, $extra);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * readonlyText.
     *
     * @param string $selected  Value
     * @param string $name      The name for the field
     * @param array  $idTag     Additional HTML attributes for the <select> tag
     * @param int    $maxlength Param
     *
     * @return string HTML
     */
    public static function readonlyText($selected, $name, $idTag = null, $maxlength = 32)
    {
        $mclass = 'readonly ';

        if ($maxlength < 7) {
            $mclass .= ' class="input-mini"';
        }

        $control = sprintf('<input type="text" name="%s" id="%s" value="%s" maxlength="%d" %s readonly="readonly"/>', $name, $idTag, $selected, $maxlength, $mclass);

        return $control;
    }

    /**
     * readonlyTextControl.
     *
     * @param string $selected  Value
     * @param string $name      The name for the field
     * @param string $label     Label
     * @param string $desc      Description
     * @param array  $idTag     Additional HTML attributes for the <select> tag
     * @param int    $maxlength Param
     *
     * @return string HTML
     */
    public static function readonlyTextControl($selected, $name, $label, $desc, $idTag = null, $maxlength = 32)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $control = self::readonlyText($selected, $name, $idTag, $maxlength);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * requiredTextControl.
     *
     * @param string $selected  Value
     * @param string $name      The name for the field
     * @param string $label     Label
     * @param string $desc      Description
     * @param array  $idTag     Additional HTML attributes for the <select> tag
     * @param int    $maxlength Param
     *
     * @return string HTML
     */
    public static function requiredTextControl($selected, $name, $label, $desc, $idTag = null, $maxlength = 32)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $mclass = '';

        if ($maxlength < 7) {
            $mclass = ' input-mini';
        }

        $control = sprintf('<input type="text" name="%s" id="%s" value="%s" class="required%s" maxlength="%d" required="required" />', $name, $idTag, $selected, $mclass, $maxlength);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * textareaControl.
     *
     * @param string $selected Value
     * @param string $name     The name for the field
     * @param string $label    Label
     * @param string $desc     Description
     * @param string $idTag    Additional HTML attributes for the <select> tag
     * @param array  $attrs    Params
     *
     * @return string HTML
     */
    public static function textareaControl($selected, $name, $label, $desc, $idTag = null, $attrs = [])
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        if (!array_key_exists('rows', $attrs)) {
            $attrs['rows'] = 2;
        }

        if (!array_key_exists('cols', $attrs)) {
            $attrs['cols'] = 30;
        }

        $extras = \Joomla\Utilities\ArrayHelper::toString($attrs);

        $control = sprintf('<textarea id="%s" %s name="%s">%s</textarea>', $idTag, $extras, $name, $selected);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * idControl.
     *
     * @param string $selected Value
     * @param string $name     The name for the field
     * @param array  $idTag    Additional HTML attributes for the <select> tag*
     *
     * @return string HTML
     */
    public static function idControl($selected, $name = 'id', $idTag = null)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $control = sprintf('<input type="text" name="%s" id="%s" value="%s" class="disabled" readonly="readonly">', $name, $idTag, $selected);

        return self::genericControl(JText::_('JGLOBAL_FIELD_ID_LABEL'), JText::_('JGLOBAL_FIELD_ID_DESC'), $name, $control);
    }

    /**
     * ajaxButtonControl.
     *
     * @param string $url       Value
     * @param string $selected  Value
     * @param string $name      The name for the field
     * @param string $label     The label for the field
     * @param string $desc      The desc for the field
     * @param string $button    The button for the field
     * @param string $idTag     The id for the field
     * @param string $class     The class for the field
     * @param string $configUrl Param
     *
     * @return string HTML
     */
    public static function ajaxButtonControl($url, $selected, $name, $label, $desc, $button, $idTag = null, $class = null, $configUrl = null)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        if ($configUrl) {
            $configUrl = sprintf('&nbsp;<a href="%s" rel="tooltip" data-original-title="Configure"><i class="xticon fas fa-cogs"></i></a>', $configUrl);
        }

        $control = '<a class="btn xt-float-left xt-ajax-button '.$class.'" href="'.$url.'">'.JText::_($button).'</a>'.$configUrl.'&nbsp;<input type="text" value="'.JText::_($selected).'" class="xt-col-span-3 xt-ajax-message disabled" readonly="readonly">';

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * downloadButtonControl.
     *
     * @param string $url       Value
     * @param string $selected  Value
     * @param string $name      The name for the field
     * @param string $label     The label for the field
     * @param string $desc      The desc for the field
     * @param string $button    The button for the field
     * @param string $idTag     The id for the field
     * @param string $class     The class for the field
     * @param string $configUrl Param
     *
     * @return string HTML
     */
    public static function downloadButtonControl($url, $selected, $name, $label, $desc, $button, $idTag = null, $class = null, $configUrl = null)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        if ($configUrl) {
            $configUrl = sprintf('&nbsp;<a href="%s" rel="tooltip" data-original-title="Configure"><i class="xticon fas fa-cogs"></i></a>', $configUrl);
        }

        $control = '<a class="btn xt-float-left '.$class.'" href="'.$url.'">'.JText::_($button).'</a>'.$configUrl;

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * calendarControl.
     *
     * @param string $selected    Value
     * @param string $name        The name for the field
     * @param string $label       Label
     * @param string $desc        Description
     * @param array  $idTag       Additional HTML attributes for the <select> tag
     * @param string $date_format Date format
     * @param string $class       Class
     *
     * @return string HTML
     */
    public static function calendarControl($selected, $name, $label, $desc, $idTag = null, $date_format = null, $class = 'input')
    {
        if (version_compare(JVERSION, '3.999.999', 'le')) {
            JHtml::_('behavior.calendar');
        }

        if (empty($date_format)) {
            $date_format = 'DATE_FORMAT_LC4';
        }

        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        if (!empty($selected)) {
            $selected = JHtml::_('date', $selected, JText::_($date_format));
        }

        $control = JHTML::_(
            'calendar',
            $selected,
            $name,
            $idTag,
            '%Y-%m-%d',
            [
                'class' => $class,
            ]
        );

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * imageControl.
     *
     * @param string $selected Value
     * @param string $name     The name for the field
     * @param string $label    Label
     * @param string $desc     Description
     * @param array  $idTag    Additional HTML attributes for the <select> tag
     * @param bool   $preview  Param
     * @param array  $attrs    Param
     *
     * @return string HTML
     */
    public static function imageControl($selected, $name, $label, $desc, $idTag = null, $preview = false, $attrs = [])
    {
        static $inserted = false;

        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        if (!array_key_exists('maxlength', $attrs)) {
            $attrs['maxlength'] = 512;
        }

        if (!array_key_exists('class', $attrs)) {
            $attrs['class'] = 'xt-col-span-4';
        }

        $extra = \Joomla\Utilities\ArrayHelper::toString($attrs);

        $control[] = '<div class="xt-input-append">';
        $control[] = '<input type="text" name="'.$name.
            '" id="'.$idTag.
            '" value="'.$selected.'" '.$extra.'/>';

        $input = new \Joomla\Input\Input($_REQUEST);
        $manage = XTF0FPlatform::getInstance()->authorise('core.manage', $input->getCmd('option', 'com_foobar'));

        $document = JFactory::getDocument();

        if ($manage) {
            if (EXTLY_J3) {
                self::defineJ3ImageControl($control, $idTag);
            } else {
                self::defineJ45ImageControl($control, $idTag);
            }
        }

        $control[] = '<a onclick="jInsertFieldValue(\'\', \''.$idTag.'\'); return false;" href="#" title="" class="btn hasTooltip" data-original-title="'.
            JText::_('JCLEAR').'"><i class="xticon fas fa-times"></i></a>';
        $control[] = '<a onclick="xtRefreshPreview(\'\', \''.$idTag.'\'); return false;" href="#" title="" class="btn hasTooltip" data-original-title="'.
                JText::_('JLIB_FORM_MEDIA_PREVIEW_TIP_TITLE').'"><i class="xticon fas fa-eye"></i></a>';

        $control[] = '</div>';
        $img_preview = null;

        if (($preview) && (!empty($selected))) {
            $img_preview = '<img src="'.$selected.'" class="xt-image-control img-polaroid">';
        }

        $imageControlContainer = '<div id="'.$idTag.'-image" class="xt-image-control-container">'.$img_preview.'</div>';

        $control = implode('', $control);

        self::addJModalIntegration();

        if (!$inserted) {
            $inserted = true;
            $document->addScriptDeclaration("
	// Extly's imageControl
	window.xtRefreshPreview = function(value, id) {
			var img_preview = jQuery('#' + id).val();
			var url_root = '".JUri::root()."';
			var id_image = '#' + id + '-image';

			if (img_preview.length == 0) {
				jQuery(id_image).html('');

				return true;
			};

			if (!img_preview.match(/http(s?):\\/\\//)) {
				img_preview = url_root + img_preview;
			};

			jQuery(id_image).html('<img src=\"' + img_preview + '\" class=\"xt-image-control img-polaroid\">');
	};
	");
        }

        return self::genericControl($label, $desc, $name, $control).$imageControlContainer;
    }

    private static function defineJ3ImageControl(&$control, $idTag): void
    {
        $remoteUrl = 'index.php?option=com_media&amp;view=images&amp;tmpl=component&amp;asset=com_autotweet&amp;author='.JFactory::getUser()->id.'&amp;fieldid='.$idTag;
        $buttonId = $idTag.'_btn';

        JHtml::_('bootstrap.modal');
        $control[] = '<a id="'.$buttonId.'" href="#'.$idTag.'_modal" role="button" class="btn" data-toggle="modal" title="'.JText::_('JSELECT').'">'.JText::_('JSELECT').'</a>';
        $control[] = JHtmlBootstrap::renderModal(
            $idTag.'_modal',
            [
                'url' => $remoteUrl,
                'title' => JText::_('JSELECT'),
                'height' => '600px', 'width' => '500px', ]
        );
    }

    private static function defineJ45ImageControl(&$control, $idTag): void
    {
        $buttonId = $idTag.'_btn';
        $modalId = $idTag.'_modal';
        $name = $idTag;

        // Code adapted from plugins/editors-xtd/image/src/Extension/Image.php
        $jApplication = Factory::getApplication();
        $doc       = $jApplication->getDocument();
        $user      = $jApplication->getIdentity();
        $extension = $jApplication->getInput()->get('option');

        // For categories we check the extension (ex: component.section)
        if ($extension === 'com_categories') {
            $parts     = explode('.', $jApplication->getInput()->get('extension', 'com_content'));
            $extension = $parts[0];
        }

        if (
            $user->authorise('core.edit', $extension)
            || $user->authorise('core.create', $extension)
            || (count($user->getAuthorisedCategories($extension, 'core.create')) > 0)
            || (count($user->getAuthorisedCategories($extension, 'core.edit')) > 0)
        ) {
            $doc->getWebAssetManager()
                ->useScript('webcomponent.media-select')
                ->useScript('webcomponent.field-media')
                ->useStyle('webcomponent.media-select');

            JHtml::script(
                'lib_perfect-publisher/utils/xt-media-select.min.js',
                [
                    'version' => 'auto',
                    'relative' => true,
                ]
            );

            $doc->addScriptOptions('xtdImageModal', [$name . '_ImageModal']);
            $doc->addScriptOptions('media-picker-api', ['apiBaseUrl' => Uri::base() . 'index.php?option=com_media&format=json']);
            $doc->addStyleDeclaration('#'.$modalId.' joomla-field-mediamore {display: none}');

            if (count($doc->getScriptOptions('media-picker')) === 0) {
                $imagesExt = array_map(
                    'trim',
                    explode(
                        ',',
                        ComponentHelper::getParams('com_media')->get(
                            'image_extensions',
                            'bmp,gif,jpg,jpeg,png,webp'
                        )
                    )
                );

                $doc->addScriptOptions('media-picker', [
                    'images'    => $imagesExt,
                ]);
            }

            Text::script('JFIELD_MEDIA_LAZY_LABEL');
            Text::script('JFIELD_MEDIA_ALT_LABEL');
            Text::script('JFIELD_MEDIA_ALT_CHECK_LABEL');
            Text::script('JFIELD_MEDIA_ALT_CHECK_DESC_LABEL');
            Text::script('JFIELD_MEDIA_CLASS_LABEL');
            Text::script('JFIELD_MEDIA_FIGURE_CLASS_LABEL');
            Text::script('JFIELD_MEDIA_FIGURE_CAPTION_LABEL');
            Text::script('JFIELD_MEDIA_LAZY_LABEL');
            Text::script('JFIELD_MEDIA_SUMMARY_LABEL');
            Text::script('JFIELD_MEDIA_EMBED_CHECK_DESC_LABEL');
            Text::script('JFIELD_MEDIA_DOWNLOAD_CHECK_DESC_LABEL');
            Text::script('JFIELD_MEDIA_DOWNLOAD_CHECK_LABEL');
            Text::script('JFIELD_MEDIA_EMBED_CHECK_LABEL');
            Text::script('JFIELD_MEDIA_WIDTH_LABEL');
            Text::script('JFIELD_MEDIA_TITLE_LABEL');
            Text::script('JFIELD_MEDIA_HEIGHT_LABEL');
            Text::script('JFIELD_MEDIA_UNSUPPORTED');
            Text::script('JFIELD_MEDIA_DOWNLOAD_FILE');

            $link = 'index.php?option=com_media&view=media&tmpl=component&e_name=' . $name . '&asset=' . $extension . '&mediatypes=0';

            $button = new CMSObject();

            if (EXTLY_J5) {
                $button = new class () extends CMSObject {
                    public function getOptions()
                    {
                        return $this->options;
                    }
                };
            }

            $button->id      = $modalId;
            $button->modal   = true;
            $button->link    = $link;
            $button->text    = Text::_('JSELECT');
            $button->name    = $buttonId;
            $button->icon    = 'pictures';
            $button->iconSVG = '<svg width="24" height="24" viewBox="0 0 512 512"><path d="M464 64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48'
                . ' 48 48h416c26.51 0 48-21.49 48-48V112c0-26.51-21.49-48-48-48zm-6 336H54a6 6 0 0 1-6-6V118a6 6 0 0 1 6-6h404a6 6'
                . ' 0 0 1 6 6v276a6 6 0 0 1-6 6zM128 152c-22.091 0-40 17.909-40 40s17.909 40 40 40 40-17.909 40-40-17.909-40-40-40'
                . 'zM96 352h320v-80l-87.515-87.515c-4.686-4.686-12.284-4.686-16.971 0L192 304l-39.515-39.515c-4.686-4.686-12.284-4'
                . '.686-16.971 0L96 304v48z"></path></svg>';
            $button->options = [
                'height'          => '400px',
                'width'           => '800px',
                'bodyHeight'      => '70',
                'modalWidth'      => '80',
                'tinyPath'        => $link,
                'confirmCallback' => "XTGetMedia(Joomla.selectedMediaFile).then((response) => {if (response.success) jInsertFieldValue(response.data[0].url, '" . $idTag . "')})",
                'confirmText'     => Text::_('JSELECT'),
            ];
            $control[] = '<button id="'.$buttonId.'" class="btn" type="button" data-bs-target="#'.$modalId.'" data-bs-toggle="modal">'.JText::_('JSELECT').'</button>';
            $control[] = LayoutHelper::render('joomla.editors.buttons.modal', $button);
        }
    }

    /**
     * userControl.
     *
     * @param string $selected Value
     * @param string $name     The name for the field
     * @param string $label    Label
     * @param string $desc     Description
     * @param array  $idTag    Additional HTML attributes for the <select> tag
     * @param string $class    Class
     *
     * @return string HTML
     */
    public static function userControl($selected, $name, $label, $desc, $idTag = null, $class = null)
    {
        $control = EHtmlSelect::userSelect($selected, $name, $idTag);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * accessLevelControl.
     *
     * @param string $selected Value
     * @param string $name     The name for the field
     * @param string $label    Label
     * @param string $desc     Description
     * @param array  $idTag    Additional HTML attributes for the <select> tag
     * @param string $class    Class
     *
     * @return string HTML
     */
    public static function accessLevelControl($selected, $name, $label, $desc, $idTag = null, $class = null)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $attr = [];

        if (!empty($class)) {
            $attr['class'] = $class;
        }

        $control = JHtml::_('access.level', $name, $selected, $attr, null, $idTag);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * numericUnitsControl.
     *
     * @param string $selectedNumeric Value
     * @param string $nameNumeric     The name for the field
     * @param string $selectedUnit    Value
     * @param string $nameUnit        The name for the field
     * @param string $units           The name for the field
     * @param string $label           Label
     * @param string $desc            Description
     * @param array  $idTag           Additional HTML attributes for the <select> tag
     * @param string $class           Class
     *
     * @return string HTML
     */
    public static function numericUnitsControl($selectedNumeric, $nameNumeric, $selectedUnit, $nameUnit, $units, $label, $desc, $idTag = null, $class = null)
    {
        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $bogusNameTag = self::generateIdTag();

        if (!empty($class)) {
            $class = 'class="'.$class.'"';
        }

        $control = '<input type="text" name="'.$nameNumeric.'" id="'.$idTag.'" value="'.$selectedNumeric.'" '.$class.'/> &nbsp;';
        $control .= EHtmlSelect::btnGroupList($selectedUnit, $nameUnit, [], $units, $idTag.'_units');

        return self::genericControl($label, $desc, $nameNumeric, $control);
    }

    /**
     * datePickerField.
     *
     * @param string $selected        Value
     * @param string $name            The name for the field
     * @param array  $idTag           Additional HTML attributes
     * @param array  $attribs         Additional HTML attributes
     * @param string $extensionmainjs Module name
     *
     * @return string HTML
     */
    public static function datePickerField($selected, $name, $idTag = null, $attribs = [], $extensionmainjs = null)
    {
        static $initialized = false;

        if ($selected) {
            $selected = EParameter::convertUTCLocal($selected);
            $selected = EParameter::getDatePart($selected);
        }

        if (!$initialized) {
            $initialized = true;
            JHtml::stylesheet(
                'lib_perfect-publisher/bootstrap-datepicker.min.css',
                [
                    'version' => 'auto',
                    'relative' => true,
                ]
            );

            if ($extensionmainjs) {
                $dependencies = [];
                $file = 'media/lib_perfect-publisher/js/utils/bootstrap-datepicker-nohide.min';
                $paths = ['bootstrap-datepicker-nohide' => $file];
                Extly::addAppDependency($extensionmainjs, $dependencies, $paths);
            } else {
                JHtml::script(
                    'lib_perfect-publisher/utils/bootstrap-datepicker-nohide.min.js',
                    [
                        'version' => 'auto',
                        'relative' => true,
                    ]
                );
            }
        }

        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $jlang = JFactory::getLanguage();
        $langTag = $jlang->getTag();

        $script = sprintf("jQuery('#%s').datepicker({autoclose:true, format: 'yyyy-mm-dd'});", $idTag);

        if (Extly::hasApp()) {
            Extly::addPostRequireScript($script);
        } else {
            JFactory::getDocument()->addScriptDeclaration('jQuery(document).ready(function(){'.$script.'});');
        }

        if (empty($attribs)) {
            $attribs = ['class' => 'xt-col-span-6'];
        }

        $field_class = '';

        if (array_key_exists('field-class', $attribs)) {
            $field_class = $attribs['field-class'];
        }

        $attribs = \Joomla\Utilities\ArrayHelper::toString($attribs);

        $control = "<div class=\"xt-input-append date {$field_class}\">
<input id=\"{$idTag}\" name=\"{$name}\" type=\"text\" value=\"{$selected}\" {$attribs}/>
<span class=\"add-on\"><i class=\"xticon fas fa-calendar\"></i></span>
</div>";

        return $control;
    }

    /**
     * datePickerControl.
     *
     * @param string $selected        Value
     * @param string $name            The name for the field
     * @param string $label           Label
     * @param string $desc            Description
     * @param array  $idTag           Additional HTML attributes for the <select> tag
     * @param array  $attribs         Additional HTML attributes
     * @param string $extensionmainjs Module name
     *
     * @return string HTML
     */
    public static function datePickerControl($selected, $name, $label, $desc, $idTag = null, $attribs = [], $extensionmainjs = null)
    {
        $control = self::datePickerField($selected, $name, $idTag, $class, $extensionmainjs);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * timePickerField.
     *
     * @param string $selected        Value
     * @param string $name            The name for the field
     * @param array  $idTag           Additional HTML attributes for the <select> tag
     * @param array  $attribs         Additional HTML attributes
     * @param string $extensionmainjs Module name
     *
     * @return string HTML
     */
    public static function timePickerField($selected, $name, $idTag = null, $attribs = [], $extensionmainjs = null)
    {
        static $initialized = false;

        if ($selected) {
            $selected = EParameter::convertUTCLocal($selected);
            $selected = EParameter::getTimePart($selected);
        }

        if (!$initialized) {
            $initialized = true;
            JHtml::stylesheet(
                'lib_perfect-publisher/bootstrap-timepicker.min.css',
                [
                    'version' => 'auto',
                    'relative' => true,
                ]
            );

            if ($extensionmainjs) {
                $dependencies = [];
                $file = 'media/lib_perfect-publisher/js/utils/bootstrap-timepicker-nohide.min';
                $paths = ['bootstrap-timepicker' => $file];
                Extly::addAppDependency($extensionmainjs, $dependencies, $paths);
            } else {
                JHtml::script(
                    'lib_perfect-publisher/utils/bootstrap-timepicker-nohide.min.js',
                    [
                        'version' => 'auto',
                        'relative' => true,
                    ]
                );
            }
        }

        if (!$idTag) {
            $idTag = self::generateIdTag();
        }

        $jlang = JFactory::getLanguage();
        $langTag = $jlang->getTag();

        if (empty($attribs)) {
            $attribs = ['class' => 'xt-col-span-6'];
        }

        $field_class = '';

        if (array_key_exists('field-class', $attribs)) {
            $field_class = $attribs['field-class'];
        }

        $attribs = \Joomla\Utilities\ArrayHelper::toString($attribs);

        $script = sprintf("jQuery('#%s').timepicker({showMeridian: false}).timepicker('setTime', '%s');", $idTag, $selected);

        if (Extly::hasApp()) {
            Extly::addPostRequireScript($script);
        } else {
            JFactory::getDocument()->addScriptDeclaration('jQuery(document).ready(function(){'.$script.'});');
        }

        $control = "<div class=\"xt-input-append time {$field_class}\">
<input id=\"{$idTag}\" name=\"{$name}\" type=\"text\" {$attribs}/>
<input id=\"{$idTag}_value\" type=\"hidden\" value=\"{$selected}\"/>
<span class=\"add-on\"><i class=\"xticon far fa-clock\"></i></span>
</div>";

        return $control;
    }

    /**
     * timePickerControl.
     *
     * @param string $selected        Value
     * @param string $name            The name for the field
     * @param string $label           Label
     * @param string $desc            Description
     * @param array  $idTag           Additional HTML attributes for the <select> tag
     * @param string $class           Class
     * @param string $extensionmainjs Module name
     *
     * @return string HTML
     */
    public static function timePickerControl($selected, $name, $label, $desc, $idTag = null, $class = null, $extensionmainjs = null)
    {
        $control = self::timePickerField($selected, $name, $idTag, $class, $extensionmainjs);

        return self::genericControl($label, $desc, $name, $control);
    }

    /**
     * dateTimePickerControl.
     *
     * @param string $selectedDate    Value
     * @param string $selectedTime    Value
     * @param string $name            The name for the field
     * @param string $label           Label
     * @param string $desc            Description
     * @param array  $idTag           Additional HTML attributes for the <select> tag
     * @param array  $attribs         Additional HTML attributes
     * @param string $extensionmainjs Module name
     *
     * @return string HTML
     */
    public static function dateTimePickerControl($selectedDate, $selectedTime, $name, $label, $desc, $idTag = null, $attribs = [], $extensionmainjs = null)
    {
        $control1 = self::datePickerField($selectedDate, $name.'_date', $idTag.'_date', $attribs, $extensionmainjs);
        $control2 = self::timePickerField($selectedTime, $name.'_time', $idTag.'_time', $attribs, $extensionmainjs);

        return self::genericControl($label, $desc, $name, $control1).self::genericControl('', '', $name, $control2);
    }

    /**
     * cronjobExpressionControl.
     *
     * @param string $selected        Value
     * @param string $name            The name for the field
     * @param string $label           Label
     * @param string $desc            Description
     * @param array  $idTag           Additional HTML attributes for the <select> tag
     * @param string $class           Class
     * @param string $extensionmainjs Module name
     *
     * @return string HTML
     */
    public static function cronjobExpressionControl($selected, $name, $label, $desc, $idTag = null, $class = null, $extensionmainjs = null)
    {
        static $initialized = false;

        if (!$initialized) {
            $initialized = true;

            if ($extensionmainjs) {
                $dependencies = [];

                // $dependencies['xtcronjob-expression-field'] = array('backbone');

                $file = 'media/lib_perfect-publisher/js/utils/xtcronjob-expression-field.min';
                $paths = ['xtcronjob-expression-field' => $file];
                Extly::addAppDependency($extensionmainjs, $dependencies, $paths);
            } else {
                JHtml::script(
                    'lib_perfect-publisher/utils/xtcronjob-expression-field.js',
                    [
                        'version' => 'auto',
                        'relative' => true,
                    ]
                );
            }
        }

        $blankText = false;

        if (empty($selected)) {
            $blankText = true;
            $selected = '* * * * *';
        }

        $cronExpression = Scheduler::getParser($selected);

        $minute = $cronExpression->getExpression(0);
        $hour = $cronExpression->getExpression(1);
        $day = $cronExpression->getExpression(2);
        $month = $cronExpression->getExpression(3);
        $weekday = $cronExpression->getExpression(4);

        $controlI = EHtmlSelect::minuteList($minute, $idTag.'_minute', ['class' => 'minute-part']);
        $controlH = EHtmlSelect::hourList($hour, $idTag.'_hour', ['class' => 'hour-part']);
        $controlD = EHtmlSelect::dayList($day, $idTag.'_day', ['class' => 'day-part']);
        $controlM = EHtmlSelect::monthList($month, $idTag.'_month', ['class' => 'month-part']);
        $controlW = EHtmlSelect::weekdayList($weekday, $idTag.'_weekday', ['class' => 'weekday-part']);

        $attrs = [
            'class' => 'unix_mhdmd-part',
        ];

        $controlT = self::textControl(($blankText ? '' : $selected), $name, $label, $desc, $idTag, 256, $attrs);

        $controls = [];
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_MINUTE', 'COM_XTCRONJOB_TASKS_FIELD_MINUTE_DESC', $idTag.'_minute', $controlI);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_HOUR', 'COM_XTCRONJOB_TASKS_FIELD_HOUR_DESC', $idTag.'_hour', $controlH);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_DAY', 'COM_XTCRONJOB_TASKS_FIELD_DAY_DESC', $idTag.'_day', $controlD);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_MONTH', 'COM_XTCRONJOB_TASKS_FIELD_MONTH_DESC', $idTag.'_month', $controlM);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_WEEKDAY', 'COM_XTCRONJOB_TASKS_FIELD_WEEKDAY_DESC', $idTag.'_weekday', $controlW);

        $controls[] = $controlT;

        return implode("\n", $controls);
    }

    /**
     * ngCronjobExpressionControl.
     *
     * @param string $selected        Value
     * @param string $name            The name for the field
     * @param string $label           Label
     * @param string $desc            Description
     * @param array  $idTag           Additional HTML attributes for the <select> tag
     * @param string $class           Class
     * @param string $extensionmainjs Module name
     *
     * @return string HTML
     */
    public static function ngCronjobExpressionControl($selected, $name, $label, $desc, $idTag = null, $class = null, $extensionmainjs = null)
    {
        JHtml::script(
            'lib_perfect-publisher/utils/xtcronjob-expression-field-ng.js',
            [
                'version' => 'auto',
                'relative' => true,
            ]
        );

        $blankText = false;

        if (empty($selected)) {
            $blankText = true;
            $selected = '* * * * *';
        }

        $cronExpression = Scheduler::getParser($selected);

        $minute = $cronExpression->getExpression(0);
        $hour = $cronExpression->getExpression(1);
        $day = $cronExpression->getExpression(2);
        $month = $cronExpression->getExpression(3);
        $weekday = $cronExpression->getExpression(4);

        $controlI = EHtmlSelect::minuteList(
            $minute,
            $idTag.'_minute',
            [
                'class' => 'minute-part',
                // 'chosen' => true,
                'ng-model' => 'cronjobExprCtlr.minute_value',
                'ng-init' => "cronjobExprCtlr.minute_value = '*'",
                'ng-change' => 'cronjobExprCtlr.update()',
            ]
        );

        $controlH = EHtmlSelect::hourList(
            $hour,
            $idTag.'_hour',
            [
                'class' => 'hour-part',
                // 'chosen' => true,
                'ng-model' => 'cronjobExprCtlr.hour_value',
                'ng-init' => "cronjobExprCtlr.hour_value = '*'",
                'ng-change' => 'cronjobExprCtlr.update()',
            ]
        );

        $controlD = EHtmlSelect::dayList(
            $day,
            $idTag.'_day',
            [
                'class' => 'day-part',
                // 'chosen' => true,
                'ng-model' => 'cronjobExprCtlr.day_value',
                'ng-init' => "cronjobExprCtlr.day_value = '*'",
                'ng-change' => 'cronjobExprCtlr.update()',
            ]
        );

        $controlM = EHtmlSelect::monthList(
            $month,
            $idTag.'_month',
            [
                'class' => 'month-part',
                // 'chosen' => true,
                'ng-model' => 'cronjobExprCtlr.month_value',
                'ng-init' => "cronjobExprCtlr.month_value = '*'",
                'ng-change' => 'cronjobExprCtlr.update()',
            ]
        );

        $controlW = EHtmlSelect::weekdayList(
            $weekday,
            $idTag.'_weekday',
            [
                'class' => 'weekday-part',
                // 'chosen' => true,
                'ng-model' => 'cronjobExprCtlr.weekday_value',
                'ng-init' => "cronjobExprCtlr.weekday_value = '*'",
                'ng-change' => 'cronjobExprCtlr.update()',
            ]
        );

        $attrs = [
            'class' => 'unix_mhdmd-part',
            'ng-model' => 'cronjobExprCtlr.unix_mhdmd_value',
        ];

        $controlT = self::textControl(($blankText ? '' : $selected), $name, $label, $desc, $idTag, 32, $attrs);

        $controls = [];
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_MINUTE', 'COM_XTCRONJOB_TASKS_FIELD_MINUTE_DESC', $idTag.'_minute', $controlI);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_HOUR', 'COM_XTCRONJOB_TASKS_FIELD_HOUR_DESC', $idTag.'_hour', $controlH);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_DAY', 'COM_XTCRONJOB_TASKS_FIELD_DAY_DESC', $idTag.'_day', $controlD);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_MONTH', 'COM_XTCRONJOB_TASKS_FIELD_MONTH_DESC', $idTag.'_month', $controlM);
        $controls[] = self::genericControl('COM_XTCRONJOB_TASKS_FIELD_WEEKDAY', 'COM_XTCRONJOB_TASKS_FIELD_WEEKDAY_DESC', $idTag.'_weekday', $controlW);

        $controls[] = $controlT;

        // $output = '<div ng-controller="CronjobExprController as cronjobExprCtlr">' . implode("\n", $controls) . '</div>';
        $output = implode("\n", $controls);

        return $output;
    }

    /**
     * renderPagination.
     *
     * @param object $view Param
     *
     * @return string HTML
     */
    public static function renderPagination($view)
    {
        if ($view->pagination->total > 0) {
            echo $view->pagination->getListFooter();

            if (EXTLY_J3) {
                echo $view->pagination->getLimitBox();
            }
        }
    }

    /**
     * renderRouting.
     *
     * @return string HTML
     */
    public static function renderRoutingTags()
    {
        $formToken = JFactory::getSession()->getFormToken();

        $input = new \Joomla\Input\Input($_REQUEST);
        $Itemid = $input->getInt('Itemid', 0);

        $lang = EParameter::getLanguageSef();

        $output = [];

        if ($formToken) {
            $output[] = '<input type="hidden" id="XTtoken" name="'.$formToken.'" value="1" />';
        }

        if ($Itemid) {
            $output[] = '<input type="hidden" id="XTItemid" name="Itemid" value="'.$Itemid.'" />';
        }

        if ($lang) {
            $output[] = '<input type="hidden" id="XTlang" name="lang" value="'.$lang.'" />';
        }

        return implode("\n", $output);
    }

    /**
     * addJModalIntegration.
     */
    public static function addJModalIntegration()
    {
        static $isAdded = false;

        if (!$isAdded) {
            $isAdded = true;
            $document = JFactory::getDocument();
            $document->addScriptDeclaration("

window.jInsertFieldValue = function(value, id) {
	jQuery('#' + id).val(value);

	if (window.xtRefreshPreview) {
		window.xtRefreshPreview(value, id);
	}
};

window.jModalClose = function() {
	jQuery('div.modal').modal('hide');
};

");
        }
    }
}
