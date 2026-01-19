/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

(function() {
    'use strict';

    window.RegularLabs = window.RegularLabs || {};

    window.RegularLabs.Conditions = window.RegularLabs.Conditions || {
        form          : null,
        container     : null,
        options       : {},
        tag_characters: {},
        group         : null,
        tag_type      : '',
        last_form_data: '',

        init: function() {
            this.options = Joomla.getOptions ? Joomla.getOptions('rl_conditions', {}) : Joomla.optionsStorage.rl_conditions || {};

            this.form      = document.querySelector('#conditionsForm');
            this.container = this.form.querySelector('#conditionsFormFields');

            if ( ! this.container) {
                this.container = this.form;
            }

            let previousDOM = this.container.innerHTML;

            setInterval(() => {
                const currentDOM = this.container.innerHTML;

                if (currentDOM === previousDOM) {
                    return;
                }

                this.updateRules();
                this.updateSummaryByForm();
            }, 250);

            this.updateRules();
        },

        updateRules: function() {
            const self = this;

            const groups                    = this.container.querySelectorAll('joomla-field-subform[name$="[groups]"] > div.subform-repeatable-group');
            const nr_of_groups              = groups.length;
            const has_multiple_groups       = nr_of_groups > 1;
            const input_has_multiple_groups = this.container.querySelector('input[name*="[has_multiple_groups]"]');

            if (input_has_multiple_groups.value !== (has_multiple_groups ? '1' : '0')) {
                input_has_multiple_groups.value = has_multiple_groups ? 1 : 0;
                input_has_multiple_groups.dispatchEvent(new Event('change'));
            }

            if ( ! nr_of_groups) {
                this.container.querySelector('joomla-field-subform[name="jform[groups]"] .group-add').click();
            }

            groups.forEach(el => {
                const group_move_button = el.querySelector('button.group-move');

                const rules                    = el.querySelectorAll('joomla-field-subform[name$="[rules]"] > div.subform-repeatable-group');
                const nr_of_rules              = rules.length;
                const has_multiple_rules       = nr_of_rules > 1;
                const input_has_multiple_rules = el.querySelector('input[name*="[has_multiple_rules]"]');

                if (input_has_multiple_rules.value !== (has_multiple_rules ? '1' : '0')) {
                    input_has_multiple_rules.value = has_multiple_rules ? 1 : 0;
                    input_has_multiple_rules.dispatchEvent(new Event('change'));
                }

                Regular.toggle(group_move_button, has_multiple_groups);

                if ( ! nr_of_rules) {
                    el.querySelector('joomla-field-subform[name$="[rules]"] .group-add').click();
                }

                rules.forEach(el => {
                    Regular.toggle(el.querySelector('button.group-move'), has_multiple_rules);
                });
            });

            this.container.querySelectorAll('.rl-exclude-field').forEach(radio => {
                const container = radio.closest('[data-showon]');
                if (Regular.hasClass(container, 'hidden')) {
                    return;
                }

                const parent = radio.closest('.subform-repeatable-group');

                Regular.addClass(parent, 'border-5');
                Regular.toggleClass(parent, 'border-success', radio.querySelector('input:checked').value === '0');
                Regular.toggleClass(parent, 'border-danger', radio.querySelector('input:checked').value === '1');
            });

            setTimeout(() => {
                addEventListeners();
            }, 10);

            function addEventListeners() {
                // Fix broken references to fields in subform (stupid Joomla!)
                self.container.querySelectorAll('.subform-repeatable-group').forEach((group) => {

                    const group_name = group.dataset['group'];
                    const x_name     = group.dataset['baseName'] + 'X';

                    const regex = new RegExp(x_name, 'g');

                    const sub_elements = group.querySelectorAll(
                        `[id*="${group_name}_"],`
                        + `[id*="${x_name}_"],`
                        + `[data-for*="${x_name}_"],`
                        + `[data-for*="${x_name}]"],`
                        + `label[for*="${x_name}_"]`
                    );

                    sub_elements.forEach((el) => {
                        if (el.dataset['for']) {
                            el.dataset['for'] = el.dataset['for'].replace(regex, group_name);
                        }
                        if (el.getAttribute('for')) {
                            el.setAttribute('for', el.getAttribute('for').replace(regex, group_name));
                        }
                        if (el.getAttribute('oninput')) {
                            el.setAttribute('oninput', el.getAttribute('oninput').replace(regex, group_name));
                        }
                        if (el.id) {
                            el.id = el.id.replace(regex, group_name);
                        }
                    });
                });
            }
        },

        updateSummaryByForm: function() {
            const form_data = this.getFormDataHash();

            if (this.last_form_data === form_data) {
                return;
            }

            this.last_form_data = form_data;

            this.updateSummary({
                'form': this.getFormData(),
            });
        },

        updateSummaryByCondition: function(id, extension, message, enabled_types) {
            this.updateSummary({
                'id'           : id,
                'extension'    : extension,
                'message'      : message,
                'enabled_types': enabled_types,
            });
        },

        updateSummaryByExtension: function(extension, item_id, message, enabled_types) {
            this.updateSummary({
                'extension'    : extension,
                'item_id'      : item_id,
                'message'      : message,
                'enabled_types': enabled_types,
            });
        },

        updateSummary: function(params) {
            const summary = document.querySelector('#rules_summary');

            Regular.fadeTo(summary, 0.25, 10);

            Regular.loadUrl(
                'index.php',
                {
                    'option': 'com_conditions',
                    'view'  : 'item',
                    'layout': 'ajax',
                    ...params
                },
                (data) => {
                    this.setSummary(data);
                },
                (data) => {
                    this.setSummary();
                }
            );
        },

        setSummary: function(data) {
            const summary               = document.querySelector('#rules_summary');
            const message               = document.querySelector('#rules_summary_message');
            const container             = document.querySelector('#rules_summary_content');
            const condition_id_field    = document.querySelector('input[id=condition_id],input[id$=_condition_id]');
            const condition_alias_field = document.querySelector('input[id=condition_alias],input[id$=_condition_alias]');
            const condition_name_field  = document.querySelector('input[id=condition_name],input[id$=_condition_name]');
            const has_conditions_field  = document.querySelector('input[id=has_conditions],input[id$=_has_conditions]');
            const elements_to_show      = document.querySelectorAll('.show-on-update-summary.hidden');
            const elements_to_hide      = document.querySelectorAll('.hide-on-update-summary:not(.hidden)');

            if ( ! data) {
                data = '{"has_conditions":false,"content":""}'
            }

            data = JSON.parse(data);

            if (container.innerHTML !== data.content) {
                Regular.fadeIn(summary);
                container.innerHTML = data.content;
            } else {
                Regular.show(summary);
            }

            if (has_conditions_field) {
                has_conditions_field.value = data.has_conditions ? 1 : 0;
                has_conditions_field.dispatchEvent(new Event('change'));
            }

            if (condition_id_field) {
                condition_id_field.value = data.id ? data.id : '';
                condition_id_field.dispatchEvent(new Event('change'));
            }

            if (condition_alias_field) {
                condition_alias_field.value = data.alias ? data.alias : '';
                condition_alias_field.dispatchEvent(new Event('change'));
            }

            if (condition_name_field) {
                condition_name_field.value = data.name ? data.name : '';
                condition_name_field.dispatchEvent(new Event('change'));
            }

            message && Regular.toggleClass(message, 'hidden', data.has_conditions);
            Regular.toggleClass(container, 'hidden', ! data.has_conditions);

            elements_to_show.forEach((el) => {
                Regular.removeClass(el, 'hidden');
            });

            elements_to_hide.forEach((el) => {
                Regular.addClass(el, 'hidden');
            })
        },

        getFormDataHash: function() {
            return JSON.stringify(this.getFormData());
        },

        getFormData: function() {
            const form_data = new FormData(this.form);

            const object = {};
            const rules  = [];

            form_data.forEach((value, key) => {
                // Reflect.has in favor of: object.hasOwnProperty(key)
                if (Reflect.has(object, key)) {
                    if ( ! Array.isArray(object[key])) {
                        object[key] = [object[key]];
                    }

                    object[key].push(value);
                    return;
                }

                const is_rule_type      = key.match(/^(jform\[groups\]\[groups[0-9]+\]\[rules\]\[rules[0-9]+\])\[type\]$/);
                const is_rule_attribute = is_rule_type ? null
                    : key.match(/^(jform\[groups\]\[groups[0-9]+\]\[rules\]\[rules[0-9]+\])\[(.*?)\]/);

                // save the rule type for checking attributes
                if (is_rule_type) {
                    rules[is_rule_type[1]] = value;
                }

                // don't include rule attributes of rules that does not match the selected one
                if (is_rule_attribute && ! rules[is_rule_attribute[1]]) {
                    return;
                }

                if (is_rule_attribute
                    && is_rule_attribute[2] !== 'exclude'
                    && is_rule_attribute[2].indexOf(rules[is_rule_attribute[1]]) !== 0
                ) {
                    return;
                }

                const field = document.querySelector('[name="' + key + '"]');

                if (field.parentNode.nodeName === 'JOOMLA-EDITOR-CODEMIRROR') {
                    let editor = Joomla.editors.instances[key];

                    if (editor) {
                        object[key] = editor.getValue();
                        return;
                    }

                    editor = field.parentNode.querySelector('.CodeMirror');

                    if (editor) {
                        object[key] = editor.CodeMirror.getValue();
                        return;
                    }
                }

                object[key] = value;

            });

            return object;
        },
    };
})();
