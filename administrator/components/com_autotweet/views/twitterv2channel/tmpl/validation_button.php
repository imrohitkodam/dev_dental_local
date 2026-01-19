<?php
/**
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2025 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * @see        https://www.extly.com
 */
defined('_JEXEC') || exit;

$expiresDate = '';

if ($bearerToken) {
    $expires = $bearerToken->getExpires();
    $expiredDate = new \DateTime();
    $expiredDate->setTimestamp($expires);
    $expiresDate = EParameter::convertUTCLocal($expiredDate->format(\DateTimeInterface::RFC2822));
}

?>
<div id="validationGroup" class=" <?php echo $validationGroupStyle; ?>">

    <div class="control-group">

        <label class="control-label">
            <a class="btn btn-info"
                id="twitterv2validationbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTON'); ?></a>&nbsp;
        </label>

        <div id="validation-notchecked" class="controls">
            <span class="lead"><i
                    class="xticon far fa-question-circle"></i>
            </span><span class="loaderspinner">&nbsp;</span>
        </div>

        <div id="validation-success" class="controls"
            style="display: none">
            <span class="lead"><i class="xticon fas fa-check"></i>
                <?php echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_SUCCESS'); ?></span><span
                class="loaderspinner">&nbsp;</span>
        </div>

        <div id="validation-error" class="controls"
            style="display: none">
            <span class="lead"><i class="xticon fas fa-exclamation"></i>
                <?php echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_ERROR'); ?></span><span
                class="loaderspinner">&nbsp;</span>
        </div>
    </div>

    <div id="validation-errormsg"
        class="xt-alert xt-alert-block alert-error"
        style="display: none">
        <!-- Removed button close data-dismiss="alert" -->
        <div id="validation-theerrormsg">
            <?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTH_MSG'); ?>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="raw_user_id"
            id="user_id-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_USERID_TITLE'); ?>
            <span class="star">&nbsp;*</span>
        </label>
        <div class="controls">
            <input type="text" maxlength="255"
                value="<?php echo $userId; ?>" id="raw_user_id"
                name="xtform[user_id]" readonly="readonly">
            <?php

require __DIR__.'/../../channel/tmpl/social_url.php';

?>
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="access_token"
            id="access_token-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_ACCESS_TOKEN'); ?>
            <span class="star">&nbsp;*</span>
        </label>
        <div class="controls">
            <input type="text" maxlength="2048"
                value="<?php echo htmlentities(json_encode($bearerToken)) ?>"
                id="bearer_token" name="xtform[bearer_token]"
                readonly="readonly" class="disabled">

            <input type="hidden" maxlength="2048"
                value="<?php echo htmlentities(json_encode($userData)) ?>"
                id="user_data" name="xtform[user_data]"
                readonly="readonly" class="disabled">
        </div>
    </div>

    <div class="control-group">
        <label class="control-label" for="expiresDate"
            id="expiresDate-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_EXPIRES_DATE'); ?>
            <span class="star">&nbsp;*</span>
        </label>
        <div class="controls">
            <input type="text" maxlength="255"
                value="<?php echo $expiresDate; ?>" id="expiresDate"
                readonly="readonly">

            <a id="authorizeButton" href="#" class="hide" onclick="document.location='<?php

    echo $authUrl;

    ?>'" class="btn btn-info"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TWITTERV2_REFRESH'); ?></a>
        </div>
    </div>
</div>
