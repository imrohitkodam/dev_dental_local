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

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text as JText;
use RegularLabs\Library\Document as RL_Document;

?>
<button type="button" class="btn btn-danger" data-bs-dismiss="modal">
    <span class="icon-cancel" aria-hidden="true"></span>
    <?php echo JText::_('JCANCEL'); ?>
</button>
<button type="submit" id='import-submit-button-id' class="btn btn-success" data-submit-task='items.import'>
    <span class="icon-file-import" aria-hidden="true"></span>
    <?php echo JText::_('RL_IMPORT'); ?>
</button>

<?php
RL_Document::scriptDeclaration("
(function(document, submitForm) {
    'use strict';

    var buttonDataSelector = 'data-submit-task';
    var formId             = 'adminForm';

    var submitTask = function submitTask(task) {
        var form = document.getElementById(formId);

        if (form && task === 'items.import') {
            submitForm(task, form);
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        var button = document.getElementById('import-submit-button-id');

        if (button) {
            button.addEventListener('click', function(e) {
                var task = e.target.getAttribute(buttonDataSelector);
                submitTask(task);
                return false;
            });
        }
    });
})(document, Joomla.submitform);
");
?>
