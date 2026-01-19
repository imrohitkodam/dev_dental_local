<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<dialog>
    <width><?php echo $this->config->get('main_subscription_agreement') ? 550 : 500; ?></width>
    <height><?php echo $registration && $this->my->guest ? 280 : 220;?></height>
    <selectors type="json">
    {
        "{closeButton}" : "[data-close-button]",
        "{form}" : "[data-form-response]",
        "{submitButton}" : "[data-submit-button]",

        "{email}" : "[data-subscribe-email]",
        "{name}"  : "[data-subscribe-name]",
        "{username}" : "[data-subscribe-username]",
        "{register}" : "[data-subscribe-register]",

        "{agreement}": "[data-subscription-agreement]",
        "{agreementInput}": "[data-subscription-agreement] input[type=checkbox]"
    }
    </selectors>
    <bindings type="javascript">
    {
        "{closeButton} click": function() {
            this.parent.close();
        },
        
        "{submitButton} click" : function() {

            <?php if ($this->config->get('main_subscription_agreement')) { ?>
            var agreed = this.agreementInput().is(':checked');

            if (!agreed) {
                this.agreement().addClass('text-error');
                return false;
            }
            <?php } ?>

            EasyBlog.dialog({
                content : EasyBlog.ajax('site/views/subscription/subscribe', {
                                "type" : "<?php echo $type;?>",
                                "email" : this.email().val(),
                                "name"  : this.name().val(),
                                "username" : this.username().val(),
                                "register" : this.register().is(':checked') ? 1 : 0,
                                "id" : "<?php echo $id;?>",
                                "userId" : "<?php echo $userId;?>"
                          })
            })
        }
    }
    </bindings>
    <title>
        <?php echo $title;?>
    </title>
    <content>
        <p style="padding: 10px 0;"><?php echo $desc;?></p>

        <form method="post" action="<?php echo JRoute::_('index.php');?>" data-form-response>
            <div class="form-group">
                <label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_FULLNAME'); ?></label>
                <div class="col-cell">
                    <input class="form-control input-sm" type="text" id="esfullname" name="esfullname" size="45" value="<?php echo $this->html('string.escape', $this->my->name);?>" data-subscribe-name />
                </div>
            </div>

            <div class="form-group">
                <label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></label>
                <div class="col-cell">
                    <input type="text" id="title" name="title" class="form-control input-sm" value="<?php echo $this->html('string.escape', $this->my->email); ?>" data-subscribe-email />
                </div>
            </div>

            <?php if ($registration && $this->my->guest) { ?>
            <div class="form-group">
                <label class="col-cell control-label"><?php echo JText::_('COM_EASYBLOG_USERNAME'); ?></label>
                <div class="col-cell">
                    <input class="form-control input-sm" type="text" id="esfullname" name="esfullname" size="45" value="<?php echo $this->html('string.escape', $this->my->name);?>" data-subscribe-username />
                </div>
            </div>

            <div class="form-group">
                <div class="col-cell control-label">&nbsp;</div>
                <div class="col-cell">
                    <div class="eb-checkbox">
                        <input type="checkbox" id="subscriptionregister" name="subscriptionregister" value="1" data-subscribe-register />
                        <label for="subscriptionregister">
                            <?php echo JText::_('COM_EASYBLOG_REGISTER_AS_SITE_MEMBER'); ?>
                        </label>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if ($this->config->get('main_subscription_agreement')) { ?>
            <div class="form-group" data-subscription-agreement style="margin-top: 20px;">
                <div class="col-cell control-label">&nbsp;</div>
                <div class="col-cell">
                    <div class="eb-checkbox">
                        <input type="checkbox" id="agreement" name="agreement" value="1" />
                        <label for="agreement">
                            <?php echo JText::_($this->config->get('main_subscription_agreement_message')); ?>
                        </label>
                    </div>
                </div>
            </div>
            <?php } ?>
        </form>
    </content>
    <buttons>
        <button data-close-button type="button" class="btn btn-default btn-sm"><?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON'); ?></button>
        <button data-submit-button type="button" class="btn btn-primary btn-sm"><?php echo JText::_('COM_EASYBLOG_SUBSCRIBE_BUTTON'); ?></button>
    </buttons>
</dialog>
