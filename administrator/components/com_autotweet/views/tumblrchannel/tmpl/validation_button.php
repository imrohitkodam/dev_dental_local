<?php
/**
 * @package     Extly.Components
 * @subpackage  com_autotweet - A powerful social content platform to manage multiple social networks.
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2020 Extly, CB. All rights reserved.
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 * @link        https://www.extly.com
 */

defined('_JEXEC') or die;

$channeltypeId = $this->input->get('channeltype_id', AutotweetModelChanneltypes::TYPE_TUMBLR_CHANNEL, 'cmd');

$accessTokenEncoded = htmlentities($accessToken);
$accessTokenSecretEncoded = htmlentities($accessTokenSecret);

?>

<div id="validationGroup" class=" <?php echo $validationGroupStyle; ?>">

	<div class="control-group">

		<label class="control-label">

		<a class="btn btn-info" id="tumblrvalidationbutton"><?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_VALIDATEBUTTON'); ?></a>&nbsp;

		</label>

		<div id="validation-notchecked" class="controls">
			<span class="lead"><i class="xticon xticon-question-circle"></i> </span><span class="loaderspinner">&nbsp;</span>
		</div>

		<div id="validation-success" class="controls" style="display: none">
			<span class="lead"><i class="xticon xticon-check"></i> <?php echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_SUCCESS'); ?></span><span class="loaderspinner">&nbsp;</span>
		</div>

		<div id="validation-error" class="controls" style="display: none">
			<span class="lead"><i class="xticon xticon-exclamation"></i> <?php echo JText::_('COM_AUTOTWEET_STATE_PUBSTATE_ERROR'); ?></span><span class="loaderspinner">&nbsp;</span>
		</div>

	</div>

	<div id="validation-errormsg" class="alert alert-block alert-error" style="display: none">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<div id="validation-theerrormsg">
			<?php echo JText::_('COM_AUTOTWEET_VIEW_CHANNEL_AUTH_MSG'); ?>
		</div>
	</div>

<?php

	$attribs = array(
		'class' => 'required',
		'required' => 'required'
	);

	$control = SelectControlHelper::tumblrPostTypes(
			$this->item->xtform->get('posttype', 'text'),
			'xtform[posttype]',
			$attribs

	);

	echo EHtml::genericControl(
			'COM_AUTOTWEET_CHANNEL_TUMBLR_POSTTYPE',
			'COM_AUTOTWEET_CHANNEL_TUMBLR_POSTTYPE_DESC',
			'xtform[posttype]',
			$control
	);

	$blogs = array();

	if (isset($user->blogs))
	{
		$blogs = $user->blogs;
	}

	$control = SelectControlHelper::tumblrBlogs(
		$blogs,
		'xtform[basehostname]',
		$attribs,
		$this->item->xtform->get('basehostname'),
		'blogs'
	);

	echo EHtml::genericControl(
		'COM_AUTOTWEET_CHANNEL_TUMBLR_BLOGID',
		'COM_AUTOTWEET_CHANNEL_TUMBLR_BLOGID_DESC',
		'xtform[basehostname]',
		$control
	);

?>

	<div class="control-group">
		<label class="required control-label" for="raw_user_id" id="user_id-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_GPLUS_USERID_TITLE'); ?> <span class="star">&nbsp;*</span>
		</label>
		<div class="controls">
			<input type="text" maxlength="255" size="64" value="<?php echo $userId; ?>" id="raw_user_id" name="xtform[user_id]" readonly="readonly" class="required" required="required">
<?php

		require dirname(__FILE__) . '/../../channel/tmpl/social_url.php';

?>
		</div>
	</div>

	<div class="control-group">
		<label class="required control-label" for="access_token" id="access_token-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TUMBLR_ACCESS_TOKEN'); ?> <span class="star">&nbsp;*</span>
		</label>
		<div class="controls">
			<input type="text" maxlength="255" size="64" value="<?php echo $accessTokenEncoded; ?>" id="access_token" name="xtform[access_token]" readonly="readonly" class="required" required="required">
		</div>
	</div>

	<div class="control-group">
		<label class="required control-label" for="access_secret" id="access_secret-lbl"><?php echo JText::_('COM_AUTOTWEET_CHANNEL_TUMBLR_ACCESS_SECRET'); ?> <span class="star">&nbsp;*</span>
		</label>
		<div class="controls">
			<input type="password" maxlength="255" size="64" value="<?php echo $accessTokenSecretEncoded; ?>" id="access_secret" name="xtform[access_secret]" readonly="readonly" class="required" required="required">
		</div>
	</div>

</div>
