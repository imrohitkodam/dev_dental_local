/**
 * @package         Conditional Content
 * @version         5.5.7
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

(function() {
    'use strict';

    window.RegularLabs = window.RegularLabs || {};

    window.RegularLabs.ConditionalContentButton = window.RegularLabs.ConditionalContentButton || {
        code: '',

        insertText: function(editor_name) {
            Joomla.editors.instances[editor_name].replaceSelection(this.code);
        },

        setCode: function(code) {
            this.code = code;
        },
    };
})();
