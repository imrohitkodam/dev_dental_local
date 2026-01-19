<?php
/**
* @Copyright Freestyle Joomla (C) 2010
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
*     
* This file is part of Freestyle Support Portal
* This program is free software: you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation, either version 3 of the License, or
* (at your option) any later version.
* 
* This program is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License
* along with this program.  If not, see <http://www.gnu.org/licenses/>.
**/
?>

<script type="text/javascript">
 var RecaptchaOptions = {
    theme : '<?php echo FSS_Settings::get('recaptcha_theme'); ?>'
 };
</script>

<div class='fss_kb_comment_add' id='add_comment'>
<?php if (FSS_Settings::get('kb_hide_add')): ?>
	<a id="commentaddbutton" href='#' onclick='$("commentadd").style.display="block";$("commentaddbutton").style.display="none";return false;' class='fss_kb_comment_add_text'><?php echo JText::_("ADD_A_COMMENT"); ?></a>
	<div id="commentadd" style="display:none;">
<?php endif; ?>
	<div class='fss_kb_comment_add_text'><?php echo JText::_("ADD_A_COMMENT"); ?></div>
	<form id='addcommentform' action="<?php echo str_replace("&amp;","&",JRoute::_( '&tmpl=component&comment=add&kbartid=' . $this->art['id'] ));?>" method="post">
	<input type='hidden' name='comment' value='add' >
	<table>
		<tr>
			<th><?php echo JText::_("NAME"); ?></th>
			<td><input name='name' id='comment_name' value="<?php echo JView::escape($this->comment['name']) ?>" ></td>
		</tr>
		<tr>
			<th><?php echo JText::_("EMAIL"); ?></th>
			<td><input name='email' value="<?php echo JView::escape($this->comment['email']) ?>"></td>
			<td><?php echo JText::_("WILL_NOT_BE_PUBLISHED"); ?></td>
		</tr>
		<tr>
			<th><?php echo JText::_("WEBSITE"); ?></th>
			<td><input name='website' value="<?php echo JView::escape($this->comment['website']) ?>"></td>
		</tr>
		<tr>
			<th><?php echo JText::_("COMMENT"); ?></th>
			<td colspan=2><textarea name='body' rows='5' cols='40' id='comment_body'><?php echo JView::escape($this->comment['body']) ?></textarea></td>
		</tr>
		<?php if ($this->kb_comments_captcha) : ?>
	<tr>
			<th><?php echo JText::_("VERIFICATION"); ?> <?php if ($this->valid == -1) :?><div class='fss_invalid_captcha'><?php echo JText::_("INVALID_CODE"); ?></div><?php endif; ?></th>
			<td colspan=2 id='captcha_cont'><?php echo $this->captcha ?></td>
		</tr>	
		<?php endif; ?>	
		<tr>
			<td></td>
			<td>
				<input class='button' type='submit' value='<?php echo JText::_("POST_COMMENT"); ?>' id='addcomment'>
			</td>
		</tr>
	</table>
	</form>
<?php if (FSS_Settings::get('kb_hide_add')): ?>
	</div>
<?php endif; ?>
</div>
