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

use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Input as RL_Input;

$extension     = RL_Input::getCmd('extension');
$item_id       = RL_Input::getInt('item_id');
$id            = RL_Input::getInt('id');
$enabled_types = RL_Input::getString('enabled_types');
$message       = RL_Input::get('message', '');

$update = 'parent.RegularLabs.Conditions.updateSummaryByExtension("' . $extension . '", ' . $item_id . ', "' . $message . '", "' . $enabled_types . '");';

if ($id)
{
    $update = 'parent.RegularLabs.Conditions.updateSummaryByCondition(' . $id . ', "' . $extension . '", "' . $message . '", "' . $enabled_types . '");';
}

$script = '
    const modal = window.parent.Joomla.Modal && window.parent.Joomla.Modal.getCurrent();

    if (modal) {
        modal.addEventListener("shown.bs.modal", () => {
            setTimeout(()=>{modal.close();}, 500);
        });
        modal.addEventListener("hidden.bs.modal", () => {
            ' . $update . '
        });
        modal.close();
    } else {
        ' . $update . '
    }
';

RL_Document::scriptDeclaration($script, 'ConditionsModal', true, 'after');
?>
<div class="rl-spinner rl-spinner-lg"></div>
