<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2017 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
$jticketingMainHelper = new jticketingmainhelper;
?>
<div class="event-counter-details" id="event-org-info">
	<div class="mb-15">
		<!--Promoter Details-->
		<h6 class="text-uppercase"><strong><?php echo strtoupper(JText::_('COM_JTICKETING_EVENT_ORGANIZER'));?></strong></h6>
		<div class="row">
			<div class="col-xs-3 organizer-profile p-5 center col-md-2">
			<?php
				if ($this->item->organizerAvatar)
				{
					$profileImg = $this->item->organizerAvatar;
				}
				else
				{
					$profileImg = JUri::root(true) . '/media/com_jticketing/images/default_avatar.png';
				}
				?>
				<img src="<?php echo $profileImg; ?>" class="img-circle img-responsive" alt="<?php echo JText::_('COM_JTICKETING_EVENT_OWNER_AVATAR')?>">
			</div>
			<div class="col-xs-9 text-muted pl-0">
			<?php
				$userinfo = JFactory::getUser($this->item->created_by);
				echo $userinfo->name . '<br>'; ?>
				<span class="word-wrap"> <?php echo $userinfo->email; ?> </span>

			<?php
				if ($this->item->organizerProfileUrl)
				{
					echo '<br>';
					?>
					<a href="<?php echo $this->item->organizerProfileUrl;?>">
						<?php echo ucfirst(JText::_('COM_JTICKETING_EVENT_ORGANIZER_DETAILS'));?>
					</a>
			<?php
				}
				?>
			</div>
		</div>
		<!--Promoter Details end here-->
	</div>
</div>
