<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h2>Avatar Sizes</h2>

<hr />

<div class="l-stack">

	<div class="t-mb--lg">
		<h3>Small</h3>

		<?php echo $render('avatars/size.small'); ?>
	</div>

	<div class="t-mb--lg">
		<h3>Default</h3>

		<?php echo $render('avatars/size.default'); ?>
	</div>

	<div class="t-mb--lg">
		<h3>Large</h3>

		<?php echo $render('avatars/size.large'); ?>
	</div>

	<div class="t-mb--lg">
		<h3>Extra Large (xl)</h3>

		<?php echo $render('avatars/size.xl'); ?>
	</div>

</div>

<h2>Avatar Styles</h2>

<hr />

<div class="l-stack">

	<div class="t-mb--lg">
		<h3>Square</h3>

		<?php echo $render('avatars/square'); ?>
	</div>

	<div class="t-mb--lg">
		<h3>Rounded</h3>
		<?php echo $render('avatars/rounded'); ?>
	</div>
</div>

<div class="t-mt--lg">
	<h2>Avatar States</h2>

	<hr />

	<div class="l-stack">
		<div class="t-mb--lg">
			<h3>Offline</h3>
			<?php echo $render('avatars/state.offline'); ?>
		</div>

		<div class="t-mb--lg">
			<h3>Online</h3>

			<?php echo $render('avatars/state.online'); ?>
		</div>
	</div>
</div>

<div class="t-mt--lg">
	<h2>Avatar with Actions</h2>

	<hr />

	<div class="l-stack">
		<div class="t-mb--lg">
			<h3>Actions</h3>
			<?php echo $render('avatars/actions'); ?>
		</div>
	</div>
</div>

<div class="mb-lg">
	<div class="space-y-md">
		<h4>Listings</h4>
		<div class="flex flex-wrap">

			<template x-for="i in 24" :key="i" hidden>
				<div class="pr-sm pb-sm">
					<div class="o-avatar-v2 o-avatar-v2--lg o-avatar-v2--rounded is-online">
						<div class="o-avatar-v2__mobile"></div>
						<div class="o-avatar-v2__content">
							<img src="/components/com_easyblog/assets/images/default_blogger.png"/>
						</div>
						<div class="o-avatar-v2__action hidden">
							<a href="javascript:void(0);" class="dropdown-toggle_" data-es-toggle="dropdown">
								<i class="fdi fas fa-cog"></i>
							</a>
							<ul class="dropdown-menu">
								<li data-avatar-upload-button="">
									<a href="javascript:void(0);">Upload Picture</a>
								</li>

								<li data-avatar-select-button="">
									<a href="javascript:void(0);">Choose from photos</a>
								</li>

									<li class="divider"></li>
								<li data-avatar-webcam="">
									<a href="javascript:void(0);">Take a photo</a>
								</li>
							</ul>

						</div>

					</div>
				</div>
			</template>
		</div>
		<h4>Tailwind method experimental</h4>
		<div class="p-lg space-y-lg">
			<div class="flex xp-lg -space-x-2xs ">
				<template x-for="i in 5" :key="i" hidden>
					<img class="inline-block h-3xl w-3xl rounded-full
					ring-2
					ring-success-500
					ring-offset-2
					ring-offset-inverse
					" src="https://images.unsplash.com/photo-1491528323818-fdd1faba62cc?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
				</template>
			</div>

		</div>
	</div>
</div>





