<?php
/**
 * @version    SVN: <svn_id>
 * @package    Jlike
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

// No direct access.
defined('_JEXEC') or die();

$lang = JFactory::getLanguage();
$lang->load('mod_jlikemylikes', JPATH_ROOT);

// Declare javascript language constant
JText::script('COM_JLIKE_DELETE_LIST_CONFIRMATION', true);
JText::script('MOD_JLIKE_DELETE_LIKE_SEL', true);
?>

<script type="text/javascript">
	/*function jlike_mailMyLikes(key)
	{
		var count = techjoomla.jQuery("#mod_jlikeAdminForm input[name='cid[]']:checked").length;

		if (count < 1)
		{
			alert(Joomla.JText._('MOD_JLIKE_DELETE_LIKE_SEL'));
		}
		else
		{
			var postParam = techjoomla.jQuery('#mod_jlikeAdminForm').serialize();

			techjoomla.jQuery.ajax({
				url: "<?php echo $InviteURI ?>" ,
				type: 'POST',
				data:postParam,
				cache: false,
				dataType: 'json',
				beforeSend: function(){
					techjoomla.jQuery('#jlike_sendMailLoading').show();
				},
				complete: function(){
					techjoomla.jQuery('#jlike_sendMailLoading').hide();
				},
				success: function(res)
				{
					if(res.status == 1)
					{
						window.location = res.nextUrl;
					}
				},
				error: function(response)
				{
					//techjoomla.jQuery('#mod_jlikeError').show('slow');
				}
			});
		}
	}*/

	function jlike_deleteMyLikes()
	{
		var count = techjoomla.jQuery("#mod_jlikeAdminForm input[name='cid[]']:checked").length;

		if (count < 1)
		{
			alert(Joomla.JText._('MOD_JLIKE_DELETE_LIKE_SEL'));
		}
		else
		{
			var flag= confirm(Joomla.JText._('COM_JLIKE_DELETE_LIST_CONFIRMATION'));

			if (flag==true)
			{
				var postParam = techjoomla.jQuery('#mod_jlikeAdminForm').serialize();
				techjoomla.jQuery.ajax({
					url: Joomla.getOptions('system.paths').base + "/index.php?option=com_jlike&task=likes.delete&ajaxCall=1",
					type: 'POST',
					data:postParam,
					cache: false,
					/*crossDomain: true,*/
					dataType: 'json',
					/*beforeSend: setHeader,*/
					beforeSend: function(){
						techjoomla.jQuery('#jlike_delLoading').show();
					},
					complete: function(){
						techjoomla.jQuery('#jlike_delLoading').hide();
					},
					success: function(res)
					{
						if(res.msg)
						{
							window.location.reload();

						}
					},
					error: function(response)
					{
						/*techjoomla.jQuery('#mod_jlikeError').show('slow');*/
					}
				});
			}
		}
	}
</script>

<div class="<?php echo JLIKE_WRAPPER_CLASS . ' ' . $params->get('moduleclass_sfx');?>" >
	<?php
	if (isset($beforeModDis))
	{
		echo $beforeModDis;
	}
	?>

	<form method="post" name="mod_jlikeAdminForm" id="mod_jlikeAdminForm" action="index.php?option=com_jlike&controller=likes&task=mailMyLikes">

		<table class="table table-condensed ">
			<tbody class="jlike_modulebody">
				<?php
				// IF cart is empty
				if (!empty($likeList))
				{
					$i = 1;

					foreach ($likeList as $likedata)
					{
						// For reach list of records
						?>
						<tr id="mylikeRow_<?php echo $likedata->id; ?>">
							<td class="nowrap center jlike_width_1">
								<label class="checkbox">
									<input type="checkbox"   name = "cid[]" value="<?php echo $likedata->id; ?>"  />
								</label>
							</td>
							<td>
								<div>
									<strong>
										<a href="<?php echo $likedata->url;?>">
											<?php echo $likedata->title;?>
										</a>
									</strong>
								</div>
								<div class="com_jlike_clear_both"></div>
							</td>
						</tr>
					<?php
						$i++;
					}

					$mylike_itemid = $comjlikeHelper->getitemid('index.php?option=com_jlike&view=likes&layout=my');
					$link = JRoute::_('index.php?option=com_jlike&view=likes&layout=my&Itemid=' . $mylike_itemid, false);
					?>

					<tr>
						<td colspan=2">
							<strong>
								<a class="pull-right"
									href="<?php echo $link;?>">
										<?php echo JText::_('MOD_JLIKE_SEE_MORE');?>
								</a>
							</strong>
						</td>
					</tr>

					<tr>
						<td colspan=2">
							<div class="btn-wrapper" id="jlike-delete">
								<?php
								$compParams        = JComponentHelper::getParams('com_jlike');
								$enableMailLikedBtn = $compParams->get('enableMailLikedBtn', 0);

								if ($enableMailLikedBtn == 1)
								{
								?>
								<span id='jlike_sendMailLoading' style="display:none;">
									<img class="" src="<?php echo JUri::root(true) ?>/components/com_jlike/assets/images/ajax-loading.gif" height="15" width="15">
								</span>
								<button type="button" class="btn hasTooltip"
									title="<?php echo JText::_('MOD_JLIKE_SEND_MY_LIKE_DESC'); ?>"
									onclick="document.mod_jlikeAdminForm.submit()" >
										<i class="icon-mail"></i>
										<?php echo JText::_('MOD_JLIKE_SEND_MY_LIKE'); ?>
								</button>
								<?php
								}
								?>

								<button type="button"
									title="<?php echo JText::_("MOD_JLIKE_DELETE_BTN_DESC"); ?>"
									onclick="jlike_deleteMyLikes()"
									class="btn btn-small btn-danger">
										<span class="icon-trash icon-white"></span>
											<?php echo JText::_("MOD_JLIKE_DELETE_BTN"); ?>
								</button>
								<span id='jlike_delLoading' style="display:none;">
									<img class="" src="<?php echo JUri::root(true) ?>/components/com_jlike/assets/images/ajax-loading.gif" height="15" width="15">
								</span>
							</div>
						</td>
					</tr>

					<?php
				}
				else
				{
					?>
					<tr>
						<td colspan="2">
							<div class="well"><?php echo JText::_('MOD_JLIKE_NO_LIKES_TO_DISPLAY'); ?></div>
						</td>
					</tr>
					<?php
				}

				if (isset($afterModDis))
				{
					?>
					<tr>
						<td colspan="2">
							<?php echo $afterModDis; ?>
						</td>
					</tr>
					<?php
				}
				?>

			</tbody>
		</table>

			<!--
			<div class="error alert alert-danger mod_jlikeError" style="display: none;">
				<?php echo JText::_('COM_JLIKE_ERROR'); ?>
				<i class="icon-cancel pull-right" style="align: right;"
					onclick="jlike.jQuery(this).parent().fadeOut();"> </i> <br />
				<hr />
				<div id="JlikeErrorContentDiv">
					<?php echo JText::_('COM_JLIKE_MY_LIKE_ERROR_MSG')?>
				</div>
			</div> -->
		<input type="hidden" name="boxchecked" value="0" />

	</form>
</div>
