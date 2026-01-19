<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form method="post" name="adminForm" id="adminForm" data-table-grid>
	<div class="flex">
		<div class="flex-grow">
			<span data-table-grid-search class="o-input-group">
				<?php echo $this->fd->html('form.text', 'search', $states->search, '', [
					'readOnly' => true,
					'placeholder' => 'COM_PP_APP_FRIEND_SUBSCRIPTION_SEARCH',
					'attributes' => 'data-table-grid-search-input'
				]); ?>

				<?php echo $this->fd->html('button.standard', 'COM_PP_BROWSE_BUTTON', 'default', 'default', [
					'outline' => true,
					'attributes' => 'data-table-grid-search-reset',
					'icon' => 'fdi fa fa-times',
					'class' => !$states->search ? 't-hidden' : ''
				]); ?>

				<?php echo $this->fd->html('button.standard', 'COM_PP_BROWSE_BUTTON', 'default', 'default', [
					'outline' => true,
					'attributes' => 'data-table-grid-search-button',
				]); ?>
			</span>
		</div>
	</div>

	<div class="panel-table">
		<table class="app-table table">
			<thead>
				<tr>
					<th>
						<?php echo $this->html('grid.sort', 'name', 'COM_PP_TABLE_COLUMN_NAME', $states); ?>
					</th>
				</tr>
			</thead>

			<tbody>
				<?php if ($users) { ?>
					<?php $i = 0; ?>
					<?php $currentLoggedInUser = PP::user(); ?>
					<?php foreach ($users as $user) { 

						// if ($user instanceof SocialUser) {
						// 	$user = PP::user($user->id);
						// }

						// remove current logged in user's from userlist
						if ($currentLoggedInUser->getId() == $user->getId()) {
							continue;
						}

						$esUser = ES::user($user->getId());
					?>
					<tr>
						<td>
							<div id="es">
								<a class="t-d--flex t-align-items--c" 
									href="javascript:void(0);" 
									data-pp-user-item
									data-title="<?php echo $this->html('string.escape', $user->getDisplayName());?>"
									data-id="<?php echo $user->getId();?>"
								>
									<?php echo ES::template()->html('avatar.mini', $esUser->getName(), '', $esUser->getAvatar(), 'md', '', '', false); ?>

									<label style="margin-left: 10px;">
										<?php echo $user->getDisplayName();?>
									</label>
								</a>

							</div>
						</td>
					</tr>
					<?php $i++; ?>
					<?php } ?>
				<?php } ?>

				<?php if (!$users) { ?>
					<?php echo $this->html('grid.emptyBlock', 'COM_PAYPLANS_ADMIN_BLANK_USER_MSG', 2); ?>
				<?php } ?>
			</tbody>

			<?php echo $this->html('grid.pagination', $pagination, 2); ?>
		</table>
	</div>

	<?php echo $this->fd->html('form.hidden', 'ordering', $states->ordering, '', 'data-fd-table-ordering'); ?>
	<?php echo $this->fd->html('form.hidden', 'direction', $states->direction, '', 'data-fd-table-direction'); ?>
</form>


<style type="text/css">
#pp .jfp-filterbar {
	display: flex;
	width: 100%;
	padding: 0 0 0 16px;
	margin-bottom:  16px;
	border-top:  1px solid #e1e1e1;
	border-bottom:  1px solid #e1e1e1;
}
#pp .jfp-filterbar__search-input-group {
	width: 100%;
	display: inline-flex;
}
#pp .jfp-filterbar__cell {
	display: flex;
	/*align-items: center;*/
	background-color: yellow;
}
#pp .jfp-filterbar__filter-wrap {
	width:  100%;
}

#pp .jfp-filterbar__search-input {
	border-radius: 0;
	height: 52px;
	border: none;
	box-shadow: none;
	/*width: 220px;*/
}
#pp .jfp-filterbar__search-input-reset,
#pp .jfp-filterbar__search-input-submit {
	border-radius: 0;
	border-top: 0;
	border-bottom: 0;
	border-right:  0;
	padding: 0.25em 1.5em;
}

#pp .jfp-filterbar__search-input-reset {

}
#pp .jfp-filterbar__search-input-submit {
	
}

#pp .t-flex-grow--1 {
	flex-grow: 1;
}
#pp .t-flex-grow--0 {
	flex-grow: 0;
}
#pp .t-flex-shrink--1 {
	flex-shrink: 1;
}
#pp .t-flex-shrink--0 {
	flex-shrink: 0;
}


</style>