/*!
 * @package     Extly.Solutions
 * @subpackage  com_perfectpub - Publish your content easily and engage your audience.
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     http://https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        https://www.extly.com
 */

/* global jQuery, Request, Joomla, alert, _, Backbone, validationHelper, appParamsHelper, Core, messagesview, FB */

"use strict";

var BskyValidationView = Backbone.View.extend({
  events: {
    'click #bskyvalidationbutton': 'onValidationReq'
  },

  initialize: function () {
    this.collection.on('add', this.loadvalidation, this);
  },

  onValidationReq: function onValidationReq() {
    var view = this,

      identifier = view.$('#identifier').val().trim(),
      password = view.$('#password').val().trim(),

      token = view.$('#XTtoken').attr('name');

    view.$('#identifier').val(identifier);
    view.$('#password').val(password);

    view.$(".loaderspinner").addClass('loading');

    this.collection.create(this.collection.model, {
      attrs: {
        identifier: identifier,
        password: password,
        token: token
      },

      wait: true,
      dataType: 'text',
      error: function (model, fail, xhr) {
        view.$(".loaderspinner").removeClass('loading');
        validationHelper.showError(view, fail.responseText);
      }
    });
  },

  loadvalidation: function loadvalidation(resp) {
    var status = resp.get('status'),
      error_message = resp.get('message'),
      user = resp.get('user'),
      icon = resp.get('icon'),
      url = resp.get('url');

    this.$(".loaderspinner").removeClass('loading');

    if (status) {
      validationHelper.showSuccess(this, user, icon, url);
    } else {
      validationHelper.showError(this, error_message);
    }
  }

});
