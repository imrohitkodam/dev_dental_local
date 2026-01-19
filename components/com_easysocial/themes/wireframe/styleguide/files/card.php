<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="t-lg-mt--xl t-lg-mb--xl">
	<h2>Card</h2>
	<hr class="es-hr">
</div>
<div class="es-styleguide-wrapper t-lg-mt--xl">
	<code>es-card</code>
	<div class="es-container t-lg-mt--xl">
		<div class="es-content">
			<div class="es-card">
				<div class="es-card__bd">
					<div class="o-flag" data-behavior="sample_code">
						<div class="o-flag__image o-flag--top t-lg-pr--lg">
							<a href="" class="o-avatar">
								<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
							</a>
						</div>
						<div class="o-flag__body">
							<b class=" t-mb--sm">Registered Users</b>
							<div class=" t-mb--sm">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Dignissimos, dolor mollitia molestiae dicta odit sit, quidem cumque sint id optio, a illo aut. Veniam architecto quam voluptatum nam, fugiat obcaecati!</div>
						</div>
					</div>
				</div>
				<div class="es-card__ft es-card--border">
					<a href="" class="btn btn-es-primary pull-right">Join now</a>
				</div>
			</div>
		</div>
	</div>
</div>

<h4>album</h4>

<div class="es-card es-card--album-item is-locked">
	<div class="es-card__hd">
		<div data-es-photo-group="album:7">
			<span class="embed-responsive embed-responsive-16by9 es-card__cover-lock-icon">
				<div class="embed-responsive-item embed-responsive-item--slide1" style="">
				</div>
			</span>
		</div>
	</div>
	<div class="es-card__bd es-card--border">
		<div class="es-card__title">
			<a href="/index.php?option=com_easysocial&amp;view=albums&amp;id=7:222&amp;layout=item&amp;uid=482:admin&amp;type=user&amp;Itemid=125" data-album-view-button="">222</a>
		</div>
		<div class="">
			<div class="t-lg-mb--sm t-text--bold">
				<i class="fas fa-lock t-text--muted"></i> Private Album
			</div>
			<div class="t-lg-mb--md">
			This is a private album. Enter password below to view photos in the album.
			</div>

			<div class="o-input-group">
				<input type="text" class="o-form-control" placeholder="Enter password for this album...">
				<span class="o-input-group__btn">
					<button class="btn btn-es-default" type="button">View Album</button>
				</span>
			</div>

		</div>
	</div>
	<div class="es-card__ft es-card--border">
		<div class="t-lg-pull-left">
			<ul class="g-list-inline">
				<li class="t-lg-mr--lg">
					<i class="fa fa-user"></i>&nbsp; <a href="/index.php?option=com_easysocial&amp;view=profile&amp;id=482:admin&amp;Itemid=125" alt="admin" class="" style="">admin</a>
					<i class="es-verified" data-es-provide="tooltip" data-original-title="Verified User"></i> </li>
				<li class="t-lg-mr--lg">
					<i class="fa fa-heart"></i>&nbsp; 0 </li>
				<li class="t-lg-mr--lg">
					<i class="fa fa-comment"></i>&nbsp; 0 </li>
				<li class="t-lg-mr--lg">
					<i class="far fa-image"></i>&nbsp; 1 </li>
				<li class="t-lg-mr--lg">
					<i class="fa fa-eye"></i>&nbsp; 1 </li>
				<li>
					<i class="fa fa-calendar"></i>&nbsp; 20 September 2019 </li>
			</ul>
		</div>
	</div>
</div>


<h4>Card - Style 1</h4>
<div class="es-styleguide-wrapper t-lg-mt--xl">
	<code>es-cards es-cards--1</code>
	<div class="es-container t-lg-mt--xl">
		<div class="es-content">
			<div class="es-cards es-cards--1">

				<div class="es-cards__item">
					<div class="es-card is-passed">
						<div class="es-card__hd">
							<div class="embed-responsive embed-responsive-16by9">
								<div class="embed-responsive-item es-card__cover"
									style="
									background-image   : url(/media/com_easysocial/defaults/covers/user/default.jpg);
									background-position: 0% 0%;
								 "
								>
								</div>
							</div>
						</div>
						<div class="es-card__bd es-card--border">
							<div class="es-card__calendar-date">
								<div class="es-card__calendar-day">
									Jul </div>
								<div class="es-card__calendar-mth">
									04 </div>
							</div>
							<div data-es-provide="tooltip" data-original-title="Passed Event" class="es-label-state es-card__state es-label-state--passed">
								<i class="es-label-state__icon"></i>
							</div>
							<h1 class="es-card__title"><a href="/index.php?option=com_easysocial&amp;view=events&amp;id=5:first-event1&amp;layout=item&amp;Itemid=127">first event</a></h1>
							<div class="es-card__meta t-lg-mb--sm">
								<ol class="g-list-inline g-list-inline--delimited">
									<li>
										<i class="fa fa-folder"></i>&nbsp; <a href="/index.php?option=com_easysocial&amp;view=events&amp;layout=category&amp;id=6:general-2&amp;Itemid=127">General</a>
									</li>
									<li>
										<span data-placement="bottom" data-es-provide="tooltip" data-original-title="This is an open event. Anyone can join and participate in this event.">
										<i class="fa fa-globe"></i>&nbsp; Open Event
										</span>
									</li>
								</ol>
							</div>
							<div class="es-card__meta t-lg-mb--sm">
								<ol class="g-list-inline g-list-inline--delimited">
									<li>
										<i class="fa fa-calendar"></i>&nbsp; 4th Jul, 2016 3:00AM - 31st Jul, 2016 10:30AM </li>
								</ol>
							</div>
							<div class="es-card__meta">
								<span data-es-truncater="">
									<span data-text="">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi...</span>
								<span data-original="" class="t-hidden">Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</span>
								</span>
							</div>
						</div>
						<div class="es-card__ft es-card--border">
							<div class="es-card__meta">
								<ol class="g-list-inline g-list-inline--delimited">
									<li>
										<i class="fa fa-user"></i>&nbsp; <a href="/">admin</a>
									</li>
									<li>
										<a data-original-title="1 Guest" data-es-provide="tooltip" href="/">
											<i class="fa fa-users"></i>&nbsp; 1 </a>
									</li>
									<li class="t-lg-pull-right">
										<div data-id="6" data-es-events-rsvp="" class="o-btn-group">
											<button data-button="" data-bs-toggle="dropdown" class="btn btn-es-default-o btn-sm dropdown-toggle_" type="button">
														<span data-text="">Attending</span>

														<i class="fa fa-caret-down"></i>
											</button>
											<ul class="dropdown-menu dropdown-menu-right dropdown-menu--rsvp">
												<li class="es-rsvp-notice">
													Sorry, but the event is over and you will no longer be able to RSVP or make any RSVP updates for this event </li>
											</ul>
										</div>
									</li>
								</ol>
							</div>
						</div>
					</div>
				</div>

				<div class="es-cards__item">
					<div class="es-card is-featured">
						<div class="es-card__hd">
							<div class="embed-responsive embed-responsive-16by9">
								<div class="embed-responsive-item es-card__cover"
									style="
									background-image   : url(/media/com_easysocial/defaults/covers/user/default.jpg);
									background-position: 0% 0%;
								 "
								>
								</div>
							</div>
						</div>
						<div class="es-card__bd es-card--border">
							<div class="es-label-state es-label-state--featured es-card__state"><i class="es-label-state__icon"></i></div>
							<div class="es-card__title">
								<a href="/" class="">Home - Salomon Running TV S3 E026:15 Home - Salomon Running TV S3 E02</a>
							</div>

							<div class="es-card__meta">Jan 14 - Jan 19 2016, 8AM - 10PM</div>
							<div class="es-card__meta">Hotel j, Kuala Lumpur</div>
						</div>
						<div class="es-card__ft es-card--border">

							<div role="toolbar" class="btn-toolbar t-lg-mt--no">

								<div class="o-btn-group " role="group">
									<button type="button" class="btn btn-es-success-o btn-sm dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Success <i class="i-chevron i-chevron--down"></i>
									</button>
									<ul class="dropdown-menu ">
										 <li><a href="#">Unfriend</a></li>
										 <li><a href="#">action</a></li>
									</ul>
								</div>

								<div class="o-btn-group " role="group">
									<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										 <i class="fa fa-ellipsis-h"></i>
									</button>
									<ul class="dropdown-menu ">
										 <li><a href="#">Unfriend</a></li>
										 <li><a href="#">action</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="es-cards__item">
					<div class="es-card is-featured">
						<div class="es-card__hd">
							<div class="embed-responsive embed-responsive-16by9">
								<div class="embed-responsive-item es-card__cover"
									style="
									background-image   : url(/media/com_easysocial/defaults/covers/user/default.jpg);
									background-position: 0% 0%;
								 "
								>
								</div>
							</div>
						</div>
						<div class="es-card__bd es-card--border">
							<div class="es-label-state es-label-state--featured es-card__state"><i class="es-label-state__icon"></i></div>
							<div class="es-card__title">
								<a href="/" class="">Home - Salomon Running TV S3 E02</a>
							</div>
							<div class="es-card__meta">Description</div>
							<div class="es-card__meta">Uploaded by Super User</div>
						</div>
						<div class="es-card__ft es-card--border">

							<ul class="g-list-inline g-list-inline--space-right">
								<li>
									<a href="/index.php?option=com_easysocial&amp;view=albums&amp;uid=2:superheroes&amp;type=group&amp;Itemid=528">
									<i class="fa fa-heart"></i> 4 </a>
								</li>
								<li>
									<i class="fa fa-comment"></i> 4 </li>
								<li>
									<i class="fa fa-eye"></i> 47
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<h4>Card - Style 2</h4>
<div class="es-styleguide-wrapper t-lg-mt--xl">
	<code>es-cards es-cards--2</code>
	<div class="es-container t-lg-mt--xl">
		<div class="es-content">
			<div class="es-cards es-cards--2">
				<div class="es-cards__item">
					<div class="es-card is-featured">
						<div class="es-card__hd">
							<div class="es-card__action-group">
								<div class="es-card__admin-action">
									<div class="pull-right dropdown_">
										<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
										<ul class="dropdown-menu">
											<li>
												<a href="">Admin action</a>
											</li>
											<li>
												<a href="">Admin action</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="embed-responsive embed-responsive-16by9">
								<div class="embed-responsive-item"
									style="
									background-image   : url(/media/com_easysocial/defaults/covers/user/default.jpg);
									background-position: 0% 0%;
								 "
								>
								</div>
							</div>
						</div>
						<div class="es-card__bd es-card--border">
							<div class="es-label-state es-label-state--featured es-card__state"><i class="es-label-state__icon"></i></div>
							<div class="es-card__title">
								<a href="/" class="">Home - Salomon Running TV S3 E026:15 Home - Salomon Running TV S3 E02</a>
							</div>

							<div class="es-card__meta">Jan 14 - Jan 19 2016, 8AM - 10PM</div>
							<div class="es-card__meta">Hotel j, Kuala Lumpur Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cupiditate ex ipsum fugit necessitatibus a, incidunt at voluptate voluptatum voluptates repellat, hic ducimus accusamus perferendis sint eligendi in veritatis asperiores mollitia!</div>
						</div>
						<div class="es-card__ft es-card--border">

							<div role="toolbar" class="btn-toolbar t-lg-mt--no">

								<div class="o-btn-group " role="group">
									<button type="button" class="btn btn-es-success-o btn-sm dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Success <i class="i-chevron i-chevron--down"></i>
									</button>
									<ul class="dropdown-menu ">
										 <li><a href="#">Unfriend</a></li>
										 <li><a href="#">action</a></li>
									</ul>
								</div>

								<div class="o-btn-group " role="group">
									<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										 <i class="fa fa-ellipsis-h"></i>
									</button>
									<ul class="dropdown-menu ">
										 <li><a href="#">Unfriend</a></li>
										 <li><a href="#">action</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="es-cards__item">
					<div class="es-card is-featured">
						<div class="es-card__hd">
							<div class="es-card__action-group">
								<div class="es-card__admin-action">
									<div class="pull-right dropdown_">
										<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
										<ul class="dropdown-menu">
											<li>
												<a href="">Admin action</a>
											</li>
											<li>
												<a href="">Admin action</a>
											</li>
										</ul>
									</div>
								</div>
							</div>
							<div class="embed-responsive embed-responsive-16by9">
								<div class="embed-responsive-item"
									style="
									background-image   : url(/media/com_easysocial/defaults/covers/user/default.jpg);
									background-position: 0% 0%;
								 "
								>
								</div>
							</div>

							 <!-- <div class="es-card__cover-label" style="bottom: 8px; top: auto; left: 66px">
								<span class="o-label o-label--success-o">
									<i class="fa fa-globe"></i> Open group
								</span>
							 </div> -->
						</div>
						<div class="es-card__bd es-card--border">
							<div class="es-card__avatar">
								<span>
									<img data-avatar-image="" src="/media/com_easysocial/defaults/avatars/user/square.png" alt="super">
								</span>
							</div>
							<div class="es-label-state es-label-state--featured es-card__state"><i class="es-label-state__icon"></i></div>
							<div class="es-card__title">
								<a href="/" class="">Home - Salomon Running TV S3 E02</a>
							</div>
							<div class="es-card__meta">Description</div>
							<div class="es-card__meta">Uploaded by Super User</div>
						</div>
						<div class="es-card__ft es-card--border">
							<div class="t-lg-pull-left">
								<ul class="g-list-inline g-list-inline--delimited">
									<li>
										<i class="fa fa-folder"></i>&nbsp; <a href="/index.php?option=com_easysocial&amp;view=groups&amp;layout=category&amp;id=1:general&amp;Itemid=127">General long long text</a>
									</li>

									<li>
										<i class="fa fa-user"></i>&nbsp; <a href="/index.php?option=com_easysocial&amp;view=profile&amp;id=740:admin&amp;Itemid=127">admin</a>
									</li>

									<li>
										<i class="fa fa-users"></i>&nbsp; 2 Members
										</li>
								</ul>
							</div>
							<div class="t-lg-pull-right">
								<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Join Group <!--i class="i-chevron i-chevron--down"></i-->
								</button>
							</div>


						</div>
					</div>
				</div>
			</div>
			<hr class="es-hr">
			<div class="es-cards es-cards--2">
				<?php for ($i=0; $i < 2; $i++) { ?>
					<div class="es-cards__item">
						<div class="es-card no-hd is-featured">

							<div class="es-card__bd">

								<div class="es-label-state es-label-state--featured es-card__state"><i class="es-label-state__icon"></i></div>

								<div class="es-card__title">
									<a href="/" class="">Who will win the Champions</a>
								</div>

								<div class="es-card__meta">Created by Admin</div>

							</div>
							<div class="es-card__ft es-card--border">

								<ul class="g-list-flex">
									<li class="g-list-flex__item-truncated">
										<div>
											<i class="fa fa-folder t-lg-mr--sm"></i> General super duper long General super duper long </div>
									</li>
									<li class="g-list-flex__item-truncated">
										<a href="javascript:void(0);" alt="admin" class="text-ellipsis" style="">
											<i class="fa fa-user"></i>&nbsp; admin dsfasdfsdfsdfsdfds asdfsd admin dsfasdfsdfsdfsdfds asdfsd
										</a>
									</li>
									<li>
										<div class="">
											<i class="fa fa-eye"></i> 199
										</div>
									</li>
									<li>
										<div>
											<i class="fa fa-heart"></i> 110
										</div>
									</li>
									<li>
										<div>
											<i class="fa fa-comment"></i> 990
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>

					<div class="es-cards__item">
						<div class="es-card no-hd is-xxx">

							<div class="es-card__bd">

								<div class="es-label-state es-label-state--featured es-card__state"><i class="es-label-state__icon"></i></div>

								<div class="es-card__title">
									<a href="/" class="">Who will win the Champions</a>
								</div>

								<div class="es-card__meta">Created by Admin</div>

							</div>
							<div class="es-card__ft es-card--border">

								111 Votes
							</div>
						</div>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>

<h4>Card - Style 3</h4>
<div class="es-styleguide-wrapper t-lg-mt--xl">
	<code>es-cards es-cards--3</code>
	<div class="es-container t-lg-mt--xl">
		<div class="es-content">
			<div class="es-cards es-cards--3">

				<?php for ($i=0; $i < 3; $i++) { ?>
				<div class="es-cards__item">
					<div class="es-card ">
						<div class="es-card__hd">
							<div class="embed-responsive embed-responsive-16by9">
								<div class="embed-responsive-item es-card__cover"
									style="
									background-image   : url(/media/com_easysocial/defaults/covers/user/default.jpg);
									background-position: 0% 0%;
								 "
								>
								</div>
							</div>
						</div>
						<div class="es-card__bd es-card--border">
							<div class="es-label-state es-card__state"><i class="es-label-state__icon"></i></div>
							<a href="/" class="es-card__title">Event title</a>
							<div class="es-card__meta">Jan 14 - Jan 19 2016, 8AM - 10PM</div>
							<div class="es-card__meta">Hotel j, Kuala Lumpur</div>
						</div>
						<div class="es-card__ft es-card--border">

							<div role="toolbar" class="btn-toolbar t-lg-mt--no">

								<div class="o-btn-group " role="group">
									<button type="button" class="btn btn-es-success-o btn-sm dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										Success <i class="i-chevron i-chevron--down"></i>
									</button>
									<ul class="dropdown-menu ">
										 <li><a href="#">Unfriend</a></li>
										 <li><a href="#">action</a></li>
									</ul>
								</div>

								<div class="o-btn-group " role="group">
									<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										 <i class="fa fa-ellipsis-h"></i>
									</button>
									<ul class="dropdown-menu ">
										 <li><a href="#">Unfriend</a></li>
										 <li><a href="#">action</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>

			</div>
			<hr class="es-hr">
			<div class="es-cards es-cards--3">

				<div data-id="1" data-app-item="" class="es-cards__item t-lg-mb--xl">

					<div class="es-card es-card--featured">

						<div class="es-card__bd">
							<div class="es-card__label-txt">
								<div class="o-label o-label--warning-o">Featured</div>
							</div>
							<div class="es-app-type es-app-type--ctf es-card__app-type"><i class="es-app-type__icon"></i></div>
							<div class="o-avatar o-avatar--lg t-lg-mb--lg">
								<img src="https://stackideas.com/images/apps/364/logo.png">
							</div>
							<div class="es-card__title">
								<a class="" href="/">Quick2cart</a>
							</div>
							<div class="es-card__meta">
								<ul class="g-list-inline g-list-inline--dashed">
									<li>
										<b>e-Commerce</b>
									</li>
									<li>
										<b>v2.8</b>
									</li>
								</ul>
							</div>
							<div class="es-card__meta">
								Flexible, Social E-commerce for Joomla! Quick2Cart is a super flexible shopping cart with Multi Vendor, Multi Store and awesome Social Integrations. Plus you have the option to us... </div>
						</div>
						<div class="es-card__ft es-card--border">
							<div class="t-lg-pull-right">
								<button data-app-install="" class="btn btn-es-primary-o btn-sm" type="button">
									<b>Install ($33.99)</b>
								</button>
							</div>
							<div class="es-card__ft-ratings">
								<div style="display: inline-block;" data-score="5" data-ratings="" class="stars" title="gorgeous"><i class="fa fa-fw fa-star" title="gorgeous" data-score="1"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="2"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="3"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="4"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="5"></i>
									<input type="hidden" name="score" value="5" readonly="readonly">
								</div>
								1 reviews
							</div>
						</div>
					</div>
				</div>
				<div data-id="1" data-app-item="" class="es-cards__item t-lg-mb--xl">
					<div class="es-card">

						<div class="es-card__bd">
							<div class="es-app-type es-app-type--app es-card__app-type"><i class="es-app-type__icon"></i></div>
							<div class="o-avatar o-avatar--lg t-lg-mb--lg">
								<img src="https://stackideas.com/images/apps/364/logo.png">
							</div>
							<div class="es-card__title">
								<a class="" href="/">Quick2cart</a>
							</div>
							<div class="es-card__meta">
								<ul class="g-list-inline g-list-inline--dashed">
									<li>
										<b>e-Commerce</b>
									</li>
									<li>
										<b>v2.8</b>
									</li>
								</ul>
							</div>
							<div class="es-card__meta">
								Flexible, Social E-commerce for Joomla! Quick2Cart is a super flexible shopping cart with Multi Vendor, Multi Store and awesome Social Integrations. Plus you have the option to us... </div>
						</div>
						<div class="es-card__ft es-card--border">
							<div class="t-lg-pull-right">
								<button data-app-install="" class="btn btn-es-primary-o btn-sm" type="button">
									<b>Install ($33.99)</b>
								</button>
							</div>
							<div class="es-card__ft-ratings">
								<div style="display: inline-block;" data-score="5" data-ratings="" class="stars" title="gorgeous"><i class="fa fa-fw fa-star" title="gorgeous" data-score="1"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="2"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="3"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="4"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="5"></i>
									<input type="hidden" name="score" value="5" readonly="readonly">
								</div>
								1 reviews
							</div>
						</div>
					</div>
				</div>
				<div data-id="1" data-app-item="" class="es-cards__item t-lg-mb--xl">
					<div class="es-card">

						<div class="es-card__bd">
							<div class="es-app-type es-app-type--tpl es-card__app-type"><i class="es-app-type__icon"></i></div>
							<div class="o-avatar o-avatar--lg t-lg-mb--lg">
								<img src="https://stackideas.com/images/apps/364/logo.png">
							</div>
							<div class="es-card__title">
								<a class="" href="/">Quick2cart</a>
							</div>
							<div class="es-card__meta">
								<ul class="g-list-inline g-list-inline--dashed">
									<li>
										<b>e-Commerce</b>
									</li>
									<li>
										<b>v2.8</b>
									</li>
								</ul>
							</div>
							<div class="es-card__meta">
								Flexible, Social E-commerce for Joomla! Quick2Cart is a super flexible shopping cart with Multi Vendor, Multi Store and awesome Social Integrations. Plus you have the option to us... </div>
						</div>
						<div class="es-card__ft es-card--border">
							<div class="t-lg-pull-right">
								<button data-app-install="" class="btn btn-es-primary-o btn-sm" type="button">
									<b>Install ($33.99)</b>
								</button>
							</div>
							<div class="es-card__ft-ratings">
								<div style="display: inline-block;" data-score="5" data-ratings="" class="stars" title="gorgeous"><i class="fa fa-fw fa-star" title="gorgeous" data-score="1"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="2"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="3"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="4"></i>&nbsp;<i class="fa fa-fw fa-star" title="gorgeous" data-score="5"></i>
									<input type="hidden" name="score" value="5" readonly="readonly">
								</div>
								1 reviews
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
