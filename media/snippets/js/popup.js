/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

(function() {
    'use strict';

    window.RegularLabs = window.RegularLabs || {};

    window.RegularLabs.SnippetsPopup = window.RegularLabs.SnippetsPopup || {
        insert: function(editor_name, type, id, vars, fill_vars) {
            if ( ! fill_vars || ! Object.keys(vars).length) {
                parent.RegularLabs.SnippetsButton.insertText(editor_name, type, id, vars);
                return;
            }

            location.href = 'index.php?rl_qp=1&class=Plugin.EditorButton.Snippets.Popup&editor=' + editor_name
                + '&tmpl=component'
                + '&layout=fillvars'
                + '&insert_type=' + type
                + '&item_id=' + encodeURIComponent(id)
                + '&vars=' + encodeURIComponent(Object.keys(vars).join(','))
                + '&' + Joomla.getOptions('csrf.token') + '=1';
        },

        insertById: function(editor_name, id, vars, fill_vars) {
            RegularLabs.SnippetsPopup.insert(editor_name, 'id', id, vars, fill_vars);
        },

        insertByAlias: function(editor_name, alias, vars, fill_vars) {
            RegularLabs.SnippetsPopup.insert(editor_name, 'alias', alias, vars, fill_vars);
        },

        insertByTitle: function(editor_name, title, vars, fill_vars) {
            RegularLabs.SnippetsPopup.insert(editor_name, 'title', title, vars, fill_vars);
        },
    };
})();
