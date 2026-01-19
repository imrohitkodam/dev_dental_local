<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\HTML\HTMLHelper as JHtml;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Layout\LayoutHelper as JLayout;
use Joomla\CMS\Router\Route as JRoute;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\User as RL_User;

$canEdit = RL_User::authorise('core.edit', 'com_conditions');

if ( ! $canEdit)
{
    throw new NotAllowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
}

$extension     = RL_Input::getCmd('extension');
$item_id       = RL_Input::getInt('item_id');
$id            = RL_Input::getInt('id');
$table         = RL_Input::getCmd('table');
$name_column   = RL_Input::getCmd('name_column');
$enabled_types = RL_Input::getString('enabled_types');
$message       = RL_Input::get('message', '');

RL_Document::useScript('keepalive');
RL_Document::useScript('form.validate');
RL_Document::usePreset('choicesjs');
RL_Document::useScript('webcomponent.field-fancy-select');
RL_Document::script('regularlabs.regular');
RL_Document::script('regularlabs.admin-form');
RL_Document::script('regularlabs.admin-form-descriptions');
RL_Document::script('regularlabs.treeselect');
RL_Document::script('conditions.script');

$script = "document.addEventListener('DOMContentLoaded', function(){RegularLabs.Conditions.init()});";
RL_Document::scriptDeclaration($script, 'Conditions', true, 'after');

$update = '';

if ($extension && $item_id)
{
    $update = 'parent.RegularLabs.Conditions.updateSummaryByExtension("' . $extension . '", ' . $item_id . ', "' . $message . '", "' . $enabled_types . '");';
}

if ($id)
{
    $update = 'parent.RegularLabs.Conditions.updateSummaryByCondition(' . $id . ', "' . $extension . '", "' . $message . '", "' . $enabled_types . '");';
}

$script = '
    document.addEventListener("DOMContentLoaded", function(){
        window.parent.document.querySelectorAll(".modal-dialog .conditions-button").forEach((btn) => {
            Regular.removeClass(btn, "hidden");
        });
    });';

if ($update)
{
    $script .= '
    document.addEventListener("DOMContentLoaded", function(){
        const modal = window.parent.Joomla.Modal && window.parent.Joomla.Modal.getCurrent();

        if (modal) {
            modal.addEventListener("hidden.bs.modal", () => {
                ' . $update . '
            });
        }
    });';
}

RL_Document::scriptDeclaration($script, 'ConditionsModal', true, 'after', true);

RL_Language::load('com_content', JPATH_ADMINISTRATOR);

$params = [
    'id'            => (int) $this->item->id,
    'extension'     => $extension,
    'item_id'       => $item_id,
    'table'         => $table,
    'name_column'   => $name_column,
    'enabled_types' => $enabled_types,
    'message'       => $message,
    'tmpl'          => RL_Input::getString('tmpl'),
];

$append = http_build_query($params);

$url = 'index.php?option=com_conditions&' . $append;

?>

<?php if (count($this->item->usage) > 1) : ?>
    <div class="alert alert-warning">
        <?php echo JText::_('CON_WARNING_FOR_EDITING_MULTIPLE_USAGE'); ?>
    </div>
<?php endif; ?>

<form action="<?php echo JRoute::_($url); ?>"
      method="post" name="adminForm" id="conditionsForm"
      aria-label="<?php echo JText::_('COM_CONDITIONS_FORM_' . ((int) $this->item->id === 0 ? 'NEW' : 'EDIT'), true); ?>"
      class="form-validate rl-form">
    <?php echo JLayout::render('joomla.edit.title_alias', $this); ?>

    <div class="hide-on-update-summary position-relative">
        <div class="rl-spinner rl-spinner-lg"></div>
    </div>
    <div class="row show-on-update-summary hidden">
        <div id="conditionsFormFields" class="col-lg-8">
            <?php echo $this->form->renderFieldset('rules'); ?>
        </div>
        <div class="col-lg-4">
            <h3><?php echo JText::_('CON_SUMMARY'); ?></h3>
            <div id="rules_summary" class="position-relative">
                <div id="rules_summary_content" class="hidden"></div>
            </div>
        </div>
    </div>

    <input type="hidden" name="extension" value="<?php echo $extension; ?>">
    <input type="hidden" name="item_id" value="<?php echo $item_id; ?>">
    <input type="hidden" name="enabled_types" value="<?php echo $enabled_types; ?>">
    <input type="hidden" name="message" value="<?php echo $message; ?>">
    <input type="hidden" name="task" value="">
    <?php echo JHtml::_('form.token'); ?>
</form>
