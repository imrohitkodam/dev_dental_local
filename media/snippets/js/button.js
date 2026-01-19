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

    window.RegularLabs.SnippetsButton = window.RegularLabs.SnippetsButton || {
        insert: function(editor_name, string) {
            const source_editor = Joomla.editors.instances[editor_name];

            source_editor.replaceSelection(string);

            if (Joomla.Modal.getCurrent()) {
                Joomla.Modal.getCurrent().close();
            }
        },

        wrap: function(editor_name, string_start, string_end) {
            const source_editor = Joomla.editors.instances[editor_name];

            const selection = source_editor.getSelection();
            const string    = string_start + selection + string_end;

            RegularLabs.SnippetsButton.insert(editor_name, string);
        },

        insertText: function(editor_name, type, id, vars) {
            const options = Joomla.getOptions ? Joomla.getOptions('rl_snippets_button', {}) : Joomla.optionsStorage.rl_snippets_button || {};

            const tag_word   = options.syntax_word;
            const char_start = options.tag_characters[0];
            const char_end   = options.tag_characters[1];

            const attributes = [];

            attributes.push(type + '="' + id.toString().replace(/"/g, '\\"') + '"');

            if( typeof vars === 'object' ) {
                // loop through object properties
                Object.keys(vars).forEach(function(key) {
                    attributes.push(key + '="' + vars[key].toString().replace(/"/g, '\\"') + '"');
                });
            }
             else {
                vars.forEach((variable) => {
                    attributes.push(variable + '=""');
                });
            }

            const string = char_start + (tag_word + ' ' + attributes.join(' ')).trim() + char_end;

            RegularLabs.SnippetsButton.insert(editor_name, string);
        },

        insertTagVariable: function(editor_name, alias) {
            const options = Joomla.getOptions ? Joomla.getOptions('rl_snippets_button', {}) : Joomla.optionsStorage.rl_snippets_button || {};

            const char_start = options.tag_characters_variables[0];
            const char_end   = options.tag_characters_variables[1];

            let string_start = char_start + (alias).trim() + char_end;

            RegularLabs.SnippetsButton.insert(editor_name, string_start);
        },

        insertTagDynamic: function(editor_name, tag_start, tag_end) {
            const options = Joomla.getOptions ? Joomla.getOptions('rl_snippets_button', {}) : Joomla.optionsStorage.rl_snippets_button || {};

            const char_start = options.tag_characters_dynamic[0];
            const char_end   = options.tag_characters_dynamic[1];

            let string_start = char_start + (tag_start).trim() + char_end;

            if ( ! tag_end) {
                RegularLabs.SnippetsButton.insert(editor_name, string_start);
                return;
            }

            let string_end = char_start + '/' + (tag_end).trim() + char_end;

            RegularLabs.SnippetsButton.wrap(editor_name, string_start, string_end);
        },
    };
})();
