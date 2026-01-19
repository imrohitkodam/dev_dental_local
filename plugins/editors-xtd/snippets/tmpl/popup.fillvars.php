<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

use Joomla\CMS\Factory as JFactory;
use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;
use RegularLabs\Library\RegEx as RL_RegEx;
use RegularLabs\Library\StringHelper as RL_String;

defined('_JEXEC') or die;

RL_Document::style('regularlabs.admin-form');
RL_Language::load('com_snippets');

$params = RL_Parameters::getComponent('snippets');

$request = RL_Input::getBase64('request');

if ($request)
{
    $request = json_decode(base64_decode($request));
}

$editor_name = RL_Input::getString('editor', 'text');
$type        = RL_Input::getString('insert_type');
$item_id     = RL_Input::getString('item_id');
$vars        = RL_Input::getString('vars');

$vars = explode(',', $vars);

// Remove any dangerous character to prevent cross site scripting
$editor_name = RL_RegEx::replace('[\'\";\s]', '', $editor_name);

RL_Document::script('snippets.popup');

$var_js_string = '{';
foreach ($vars as $i => $var)
{
    $var_js_string .= '\'' . $var . '\':form.var_' . $i . '.value,';
}

$var_js_string = trim($var_js_string, ',');
$var_js_string .= '}';

?>

<div class="form-vertical rl-w-24em mx-auto">
    <h1><?php echo JText::_('SNP_FILL_VARIABLES'); ?></h1>

    <form id="adminForm" name="adminForm" class="rl-form labels-sm">
        <div class="my-4">
            <?php foreach ($vars as $i => $var) : ?>
                <div class="control-group">
                    <div class="control-label">
                        <label for="var_<?php echo $i; ?>"><?php echo $var; ?></label>
                    </div>
                    <div class="controls">
                        <input type="text" id="var_<?php echo $i; ?>" name="var_<?php echo $i; ?>" value="" class="form-control w-100" />
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="rl-form-group">
            <button type="button" class="btn btn-primary w-100"
                    onclick="parent.RegularLabs.SnippetsButton.insertText('<?php echo $editor_name; ?>', '<?php echo $type; ?>', '<?php echo RL_String::escape($item_id); ?>', <?php echo $var_js_string; ?>);">
                <?php echo JText::_('RL_INSERT'); ?>
            </button>
        </div>
    </form>
</div>
