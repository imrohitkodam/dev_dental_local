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
<div class="t-lg-mb--xl">
	<h3>Modules</h3>
	<hr class="es-hr" />

</div>

<div>

	<h4>Listing</h4>
	<p>Mainly for group/event/pages</p>
	<div class="mod-es">
		<div class="mod-es-list--vertical">
			<div class="mod-es-item">
				<div class="o-flag">
					<div class="o-flag__img t-lg-mr--md">
						<a class="o-avatar" href="">
							<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
						</a>
					</div>
					<div class="o-flag__body">
						<a href="#" class="mod-es-title">
							Listing title
						</a>
						<div class="mod-es-meta">meta</div>

						<div class="mod-es-meta">
							<ol class="g-list-inline g-list-inline--delimited">
								<li>
									<i class="fa fa-folder"></i>&nbsp; <a href="/index.php?option=com_easysocial&amp;view=groups&amp;layout=category&amp;id=1:general&amp;Itemid=127">General</a>
								</li>
								<li>
									<i class="fa fa-user"></i>&nbsp; <a href="/">admin</a>
								</li>
								<li>
									<i class="fa fa-users"></i>&nbsp; 2 Members
								</li>
								<li>
									<i class="fa fa-calendar"></i>&nbsp; Thursday, 12 May 2016
								</li>
							</ol>
						</div>

						<div class="mod-es-action">
							<div class="btn btn-es-default btn-sm">Join Group</div>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

	<hr class="es-hr">


	<h4>Avatar Listing</h4>
	<p>Mainly for recent users</p>
	<div class="mod-es">
		<ul class="g-list-inline">
			<?php for ($i=0; $i < 30; $i++) { ?>
			<li class="t-lg-mb--md t-lg-mr--lg">
				<div class="o-avatar-status is-online">
					<div class="o-avatar-status__indicator"></div>
					<a href="" class="o-avatar">
						<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
					</a>
				</div>
			</li>
			<?php } ?>
		</ul>
		<div class="mod-es-action">
			<a href="">View all users</a>
		</div>
	</div>



	<hr class="es-hr">
	<h4>Leader board</h4>
	<div class="mod-es mod-es-leaderboard">

		<div class="mod-es-leader-item">
			<div class="es-leader-badge es-leader-badge--1">
				<span>1</span>
			</div>
			<div class="es-leader-context o-flag">
				<div class="o-flag__image">
					<a class="o-avatar" href="">
						<img src="/media/com_easysocial/defaults/avatars/user/medium.png">
					</a>
				</div>
				<div class="o-flag__body">
					<a class="" href="">Jake Rocheleau</a>
					<div class="mod-es-leader__points">9,888 <span>Points</span></div>
				</div>
			</div>
		</div>

		<div class="mod-es-leader-item">
			<div class="es-leader-badge es-leader-badge--2">
				<span>2</span>
			</div>
			<div class="es-leader-context o-flag">
				<div class="o-flag__image">
					<a class="o-avatar" href="">
						<img src="/media/com_easysocial/defaults/avatars/user/medium.png">
					</a>
				</div>
				<div class="o-flag__body">
					<a class="" href="">Jake Rocheleau</a>
					<div class="mod-es-leader__points">9,888 <span>Points</span></div>
				</div>
			</div>
		</div>

		<div class="mod-es-leader-item">
			<div class="es-leader-badge es-leader-badge--3">
				<span>3</span>
			</div>
			<div class="es-leader-context o-flag">
				<div class="o-flag__image">
					<a class="o-avatar" href="">
						<img src="/media/com_easysocial/defaults/avatars/user/medium.png">
					</a>
				</div>
				<div class="o-flag__body">
					<a class="" href="">Jake Rocheleau</a>
					<div class="mod-es-leader__points">9,888 <span>Points</span></div>
				</div>
			</div>
		</div>

	</div>


	<hr class="es-hr">
	<h4>Albums</h4>
	<h5>To make it looks nicer photos have to be in 16:9 ratio</h5>
	<div class="mod-es mod-es-albums">

		<div class="mod-es-albums-item">
			<div class="es-photos photos-3 pattern-tile">
				<div class="es-photo ar-16x9">
					<a title="" href="/" class="fit-width">
						<u><b>
						<img alt="Untitled 2016-08-29" src="https://unsplash.it/480/270/?random">
						<!-- <img src="media/com_easysocial/images/distortion-check-16x9.png" alt=""> -->
						</b></u>
					</a>
				</div>
				<div class="es-photo ar-16x9">
					<a title="" href="/" class="fit-width">
						<u><b>
						<img alt="Untitled 2016-08-29" src="https://unsplash.it/480/270/?random">
						<!-- <img src="media/com_easysocial/images/distortion-check-16x9.png" alt=""> -->
						</b></u>
					</a>
				</div>
				<div class="es-photo ar-16x9">
					<a title="" href="/" class="fit-width">
						<u><b>
						<img alt="Untitled 2016-08-29" src="https://unsplash.it/480/270/?random">
						<!-- <img src="media/com_easysocial/images/distortion-check-16x9.png" alt=""> -->
						</b></u>
					</a>
				</div>
			</div>

			<div class="mod-es-action">
				<a href=""><b>Random title</b></a>
			</div>
			<div class="">
				by <a href="">Ben Johnson</a>
			</div>
		</div>

		<div class="mod-es-albums-item">
			<div class="es-photos photos-2 pattern-tile">
				<div class="es-photo ar-16x9">
					<a title="" href="/" class="fit-width">
						<u><b>
						<img alt="Untitled 2016-08-29" src="https://unsplash.it/480/270/?random">
						<!-- <img src="media/com_easysocial/images/distortion-check-16x9.png" alt=""> -->
						</b></u>
					</a>
				</div>

				<div class="es-photo ar-16x9">
					<a title="" href="/" class="fit-width">
						<u><b>
						<img alt="Untitled 2016-08-29" src="https://unsplash.it/480/270/?random">
						<!-- <img src="media/com_easysocial/images/distortion-check-16x9.png" alt=""> -->
						</b></u>
					</a>
				</div>
			</div>

			<div class="mod-es-action">
				<a href=""><b>Random title</b></a>
			</div>
			<div class="">
				by <a href="">Ben Johnson</a>
			</div>
		</div>

		<div class="mod-es-albums-item">
			<div class="es-photos photos-1 pattern-tile">
				<div class="es-photo ar-16x9">
					<a title="" href="/" class="fit-width">
						<u><b>
						<img alt="Untitled 2016-08-29" src="https://unsplash.it/480/270/?random">
						<!-- <img src="media/com_easysocial/images/distortion-check-16x9.png" alt=""> -->
						</b></u>
					</a>
				</div>
			</div>

			<div class="mod-es-action">
				<a href=""><b>Random title</b></a>
			</div>
			<div class="">
				by <a href="">Ben Johnson</a>
			</div>
		</div>


		<div class="mod-es-action">
			<a href="" class="btn btn-es-default-o btn-sm btn-block"><b>View all albums</b></a>
		</div>
	</div>

	<hr class="es-hr">
	<div class="mod-es mod-es-event">
		<div class="mod-card">
			<div class="mod-card__cover-wrap">
				<div style="
					background-image : url(/media/com_easysocial/defaults/covers/user/default.jpg);" class="mod-card__cover">
				</div>
			</div>
			<div class="mod-card__context">
				<div class="mod-card__avatar-holder">
					<div class="mod-card__calendar-date">
						<div class="mod-card__calendar-day">
							Sep
						</div>
						<div class="mod-card__calendar-mth">
							07
						</div>
					</div>
				</div>
				<a class="mod-card__title" href="/">Event title</a>
				<div class="mod-card__meta">Jan 14 - Jan 19 2016, 8AM - 10PM</div>
				<div class="mod-es-action">
					<a href="" class="btn btn-es-default-o btn-sm">Join Event</a>
				</div>
			</div>
		</div>

		<div class="mod-card">
			<div class="mod-card__cover-wrap">
				<div style="
					background-image : url(/media/com_easysocial/defaults/covers/user/default.jpg);" class="mod-card__cover">
				</div>
			</div>
			<div class="mod-card__context">
				<div class="mod-card__avatar-holder">
					<div class="mod-card__avatar">
						<img alt="super" src="/media/com_easysocial/defaults/avatars/user/square.png" data-avatar-image="">
					</div>
				</div>
				<a class="mod-card__title" href="/">Event title</a>
				<div class="mod-card__meta">Jan 14 - Jan 19 2016, 8AM - 10PM</div>
				<div class="mod-es-action">
					<a href="" class="btn btn-es-default-o btn-sm">Join Event</a>
				</div>
			</div>
		</div>

		<div class="mod-es-action">
			<a href="" class="btn btn-es-default-o btn-sm">View all xxxx</a>
		</div>
	</div>

	<hr class="es-hr">
	<h4>Recent polls</h4>
	<div class="mod-es mod-es-recentpolls">
		<div class="o-box">
			<div class="es-polls">
				<div class="o-flag t-lg-mb--lg">
					<div class="o-flag__image o-flag--top">
						<div class="o-avatar-status ">
							<a href="" class="o-avatar o-avatar--sm">
								<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
							</a>
						</div>
					</div>
					<div class="o-flag__body">
						<a href="/">
							<b>Who will win Lorem ipsum dolor sit amet, consectetur adipisicing elit. Suscipit voluptate debitis eveniet modi animi ea dignissimos rerum sapiente, tempore ut totam placeat dicta eius provident perferendis eos iusto inventore porro.?</b>
						</a>
					</div>
				</div>
				<div class="es-polls__list ">
					<div class="es-polls__item">
						<div>
							Spain!
							<div class="es-polls__progress progress">
								<div data-progress="" style="width: 100%;" class="progress-bar progress-bar-primary"></div>
							</div>
							<a data-view-voters="" class="es-polls__count" href="javascript:void(0);">
								<span data-counter="">1</span> vote(s)
							</a>
						</div>
					</div>
					<div class="es-polls__item">
						<div>
							Italy
							<div class="es-polls__progress progress">
								<div data-progress="" style="width: 0%;" class="progress-bar progress-bar-primary"></div>
							</div>
							<a data-view-voters="" class="es-polls__count" href="javascript:void(0);">
								<span data-counter="">0</span> vote(s)
							</a>
						</div>
					</div>
				</div>
			</div>

			<div class="es-polls">
				<div class="o-flag t-lg-mb--lg">
					<div class="o-flag__image o-flag--top">
						<div class="o-avatar-status ">
							<a href="" class="o-avatar o-avatar--sm">
								<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
							</a>
						</div>
					</div>
					<div class="o-flag__body">
						<a href="/">
							<b>Who will win?</b>
						</a>
					</div>
				</div>
				<div class="es-polls__list ">
					<div class="es-polls__item">
						<div>
							Spain!
							<div class="es-polls__progress progress">
								<div data-progress="" style="width: 100%;" class="progress-bar progress-bar-primary"></div>
							</div>
							<a data-view-voters="" class="es-polls__count" href="javascript:void(0);">
								<span data-counter="">1</span> vote(s)
							</a>
						</div>
					</div>
					<div class="es-polls__item">
						<div>
							Italy
							<div class="es-polls__progress progress">
								<div data-progress="" style="width: 0%;" class="progress-bar progress-bar-primary"></div>
							</div>
							<a data-view-voters="" class="es-polls__count" href="javascript:void(0);">
								<span data-counter="">0</span> vote(s)
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="mod-es-action">
			<a href="" class="btn btn-es-default-o btn-sm">View all xxxx</a>
		</div>
	</div>

	<hr class="es-hr">
	<h4>Menu</h4>
	<div class="mod-es mod-es-menu">
		<div class="mod-es-menu-bar">
			<nav class="o-nav">
				<div class="o-nav__item">
					<a class="o-nav__link mod-es-menu-bar__icon-link" href="javascript:void(0);">
						<i class="fa fa-users"></i>
						<span data-counter="" class="mod-es-menu-bar__link-bubble">0</span>
					</a>
				</div>
				<div class="o-nav__item">
					<a class="o-nav__link mod-es-menu-bar__icon-link has-new" href="javascript:void(0);">
						<i class="fa fa-globe"></i>
						<span data-counter="" class="mod-es-menu-bar__link-bubble">0</span>
					</a>
				</div>
				<div class="o-nav__item">
					<a class="o-nav__link mod-es-menu-bar__icon-link has-new" href="javascript:void(0);">
						<i class="fa fa-user"></i>
						<span data-counter="" class="mod-es-menu-bar__link-bubble">0</span>
					</a>
				</div>

				<div class="o-nav__item pull-right">
					<a class="o-nav__link mod-es-menu-bar__icon-link has-new" href="javascript:void(0);">
						<i class="fa fa-edit"></i>
					</a>
				</div>
			</nav>
		</div>
		<div class="mod-es-pf-hd">
			<div class="mod-es-pf-hd__cover-wrap">
				<div class="mod-es-pf-hd__cover" style="
					background-image : url(/media/com_easysocial/defaults/covers/user/default.jpg);">
				</div>
			</div>
			<div class="mod-es-pf-hd__content">
				<div class="mod-es-pf-hd__avatar">
					<a href="" class="o-avatar o-avatar--lg">
						<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
					</a>
				</div>
				<a href="/" class="mod-es-title">Thomas</a>
				<div class="mod-es-meta">4,435 points</div>
				<div class="mod-es-pf-hd__badges">
					<?php for ($i=0; $i < 12; $i++) { ?>
					<a href="/index.php?" data-es-provide="tooltip" data-placement="top" data-original-title="Photogenic">
						<img alt="Photogenic" src="media/com_easysocial/badges/photogenic.png">
					</a>
					<?php } ?>
				</div>
			</div>
		</div>
		<div class="mod-es-menu-list">
			<?php for ($i=0; $i < 6; $i++) { ?>
			<a href="/" class="mod-es-menu-list__item">Conversations</a>
			<?php } ?>
		</div>
		<div class="mod-es-menu-list">
			<a href="/" class="mod-es-menu-list__item">Sign Out</a>
		</div>
	</div>


	<hr class="es-hr">
	<h4>Dating Search</h4>
	<div id="es" class="mod-es mod-es-dating-search">
		<form action="" class="mod-es-dating-search-form">
			<div class="o-form-group">
				<label for="es-dating-search-name">Name</label>
				<input type="text" placeholder="Name" id="es-dating-search-name" class="o-form-control">
			</div>
			<div class="o-form-group">
				<label for="">Gender</label>
				<div class="o-radio">
					<input type="radio" id="item-radio-1">
					<label for="item-radio-1">
						Male
					</label>
				</div>
				<div class="o-radio">
					<input type="radio" id="item-radio-1">
					<label for="item-radio-1">
						Female
					</label>
				</div>
				<div class="o-radio">
					<input type="radio" id="item-radio-1">
					<label for="item-radio-1">
						Others
					</label>
				</div>
			</div>
			<div class="o-grid o-grid--gutters">
				<div class="o-grid__cell">
					<div class="o-form-group">
						<label for="es-dating-search-name">From age:</label>
						<input type="number" placeholder="" id="es-dating-search-name" class="o-form-control">
					</div>
				</div>
				<div class="o-grid__cell">
					<div class="o-form-group">
						<label for="es-dating-search-name">To age:</label>
						<input type="number" placeholder="" id="es-dating-search-name" class="o-form-control">
					</div>
				</div>
			</div>
			<div class="o-form-group">
				<label for="es-dating-search-name">Distance within (miles):</label>
				<input type="number" placeholder="Set distance" id="es-dating-search-name" class="o-form-control">
			</div>
			<div class="o-input-group">
			  <input class="o-form-control" placeholder="Set your location" type="text">
			  <span class="o-input-group__btn">
				<button class="btn btn-es-default" type="button"><i class="fa fa-map-marker-alt"></i></button>
			  </span>
			</div>
			<button class="btn btn-es-primary btn-block t-lg-mt--lg">Search</button>
		</form>
	</div>

	<hr class="es-hr">
	<h4>Dropdown menu</h4>
	<div id="es" class="mod-es mod-es-dropdown-menu">

		<div class="dropdown_">
			<div class="btn btn-es-default-o btn-block dropdown-toggle_ fd-cf" data-bs-toggle="dropdown">
				<div class="o-flag o-flag--rev">
					<div class="o-flag__body t-text--left">
						Hello, <b>Apple</b> <i class="i-chevron i-chevron--down t-lg-ml--sm t-lg-mt--sm"></i>
					</div>
					<div class="o-flag__image">
						<div class="o-avatar o-avatar--xsm pull-right">
							<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
						</div>
					</div>
				</div>
			</div>

			<ul class="dropdown-menu dropdown-menu-full">
				<li>
					<a href="javascript:void(0);">
						Some Hyperlink 1
					</a>
				</li>
				<li>
					<a href="javascript:void(0);">
						Some Hyperlink 2
					</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">
						Some Hyperlink 3
					</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">
						Some Hyperlink 4
					</a>
				</li>
			</ul>
		</div>

	</div>

	<hr class="es-hr">
	<h4>profile completeness</h4>
	<div id="es" class="mod-es mod-es-profile-completeness">

		<div class="o-box">
			<div class="t-lg-mt--sm">
				<div class="progress">
					<div data-progress="" style="width: 30%;" class="progress-bar progress-bar-success"></div>
				</div>
				<div class=""><b>Your profile is 30% completed</b> Do more to complete your profile and earn points!</div>
			</div>
			<div class="o-box--border">
				<div class="es-completeness-check-list">

					<div class="o-flag es-completeness-check-list__item is-completed">
						<div class="o-flag__image">
							<div class="es-completeness-check-list__icon">
								<i class="fa fa-check"></i>
							</div>
						</div>
						<div class="o-flag__body">
							<a href="/">Add birthday and location</a>
						</div>
					</div>
					<div class="o-flag es-completeness-check-list__item">
						<div class="o-flag__image">
							<div class="es-completeness-check-list__icon">
								<i class="fa fa-question"></i>
							</div>
						</div>
						<div class="o-flag__body">
							<a href="/">Add birthday and location</a>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>

	<hr class="es-hr">
	<h4>Login module (horizontal layout)</h4>
	<div id="es" class="mod-es mod-es-login">
		<div class="es-mod-login-wrap is-horizontal">
			<div class="es-mod-login-wrap__hd" style="background-image: url('/media/com_easysocial/images/bg-register-pattern.png');">
				<form action="">
					<div class="o-grid o-grid--gutters">
						<div class="o-grid__cell">
							<div class="o-form-group">
								<!-- <label for="exampleInputEmail1">Username</label> -->
								<input type="text" placeholder="Username" id="" class="o-form-control">
							</div>
						</div>
						<div class="o-grid__cell">
							<div class="o-form-group">
								<!-- <label for="">Password</label> -->
								<input type="password" placeholder="Password" id="" class="o-form-control">
							</div>
						</div>
						<div class="o-grid__cell o-grid__cell--auto-size">
							<div class="btn btn-es-primary btn-block">Login to my account</div>
						</div>
					</div>
					<div class="o-grid">
						<div class="o-grid__cell">
							<div class="o-form-group">
								<div class="o-checkbox">
									<input type="checkbox" id="item-checkbox-1">
									<label for="item-checkbox-1">
										Remember me
									</label>
								</div>
							</div>
						</div>
						<div class="o-grid__cell t-text--right">
							<span class="t-lg-mr--md">Or login using your social account </span>
							<div class="btn btn-es-facebook btn-sm"><i class="fa fa-facebook"></i> Login</div>
							<div class="btn btn-es-twitter btn-sm"><i class="fab fa-twitter"></i> Login</div>
						</div>
					</div>
				</form>
			</div>
			<div class="es-mod-login-wrap__ft">
				First time here? <a href="/">Register an account</a> and start sharing!
			</div>
		</div>
	</div>

	<hr class="es-hr">
	<div id="es" class="mod-es mod-es-passed-event">


		<div class="mod-card is-passed">
			<div class="mod-card__cover-wrap">
				<div style="
					background-image : url(/media/com_easysocial/defaults/covers/user/default.jpg);" class="mod-card__cover">
				</div>
			</div>
			<div class="mod-card__context">
				<div class="mod-card__avatar-holder">
					<div class="mod-card__calendar-date">
						<div class="mod-card__calendar-day">
							Sep
						</div>
						<div class="mod-card__calendar-mth">
							07
						</div>
					</div>
				</div>

				<div data-es-provide="tooltip" data-original-title="Passed Event" class="es-label-state mod-card__state es-label-state--passed">
					<i class="es-label-state__icon"></i>
				</div>

				<a class="mod-card__title" href="/">Event title</a>
				<div class="mod-card__meta">Jan 14 - Jan 19 2016, 8AM - 10PM</div>

			</div>
			<div class="mod-card__ft mod-card--border">
				<div class="mod-card__meta">
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

	<hr class="es-hr">
	<div id="es" class="mod-es mod-es-customfieldsearch">
		<div class="es-list">

			<div class="es-list-item">
				<div class="es-list-item__media">
					<i class="fa fa-folder"></i>
				</div>
				<div class="es-list-item__context">
					<div class="es-list-item__hd">
						<div class="es-list-item__content">

							<div class="es-list-item__title">
								<span>Check this</span>
							</div>
						</div>

						<div class="es-list-item__action">
							<a class="t" data-bs-toggle="collapse" href="#eb-field-3">
								<i class="fa es-mod-chevron"></i>
							</a>
						</div>
					</div>
					<div class="es-list-item__bd t-lg-pt--no">
						<div id="eb-field-3" class="in">
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="chrome" name="field-3[]" value="chrome" data-checkbox-option="" type="checkbox">
									<label for="chrome">Chrome</label>
								</div>
							</div>
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="ff" name="field-3[]" value="ff" data-checkbox-option="" type="checkbox">
									<label for="ff">Firefox</label>
								</div>
							</div>
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="opera" name="field-3[]" value="opera" data-checkbox-option="" type="checkbox">
									<label for="opera">Opera</label>
								</div>
							</div>
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="ie" name="field-3[]" value="ie" data-checkbox-option="" type="checkbox">
									<label for="ie">IE</label>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

			<div class="es-list-item">
				<div class="es-list-item__media">
					<i class="fa fa-folder"></i>
				</div>
				<div class="es-list-item__context">
					<div class="es-list-item__hd">
						<div class="es-list-item__content">

							<div class="es-list-item__title">
								<span>Check this</span>
							</div>
						</div>

						<div class="es-list-item__action">
							<a class="" data-bs-toggle="collapse" href="#eb-field-2">
								<i class="fa es-mod-chevron"></i>
							</a>
						</div>
					</div>
					<div class="es-list-item__bd t-lg-pt--no">
						<div id="eb-field-2" class="in">
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="chrome" name="field-3[]" value="chrome" data-checkbox-option="" type="checkbox">
									<label for="chrome">Chrome</label>
								</div>
							</div>
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="ff" name="field-3[]" value="ff" data-checkbox-option="" type="checkbox">
									<label for="ff">Firefox</label>
								</div>
							</div>
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="opera" name="field-3[]" value="opera" data-checkbox-option="" type="checkbox">
									<label for="opera">Opera</label>
								</div>
							</div>
							<div class="t-lg-mt--sm">
								<div class="o-checkbox">
									<input id="ie" name="field-3[]" value="ie" data-checkbox-option="" type="checkbox">
									<label for="ie">IE</label>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>


		</div>

		<div class="t-lg-mt--lg">
			<button class="btn btn-es-primary btn-sm" data-save-filter="">Save Filter</button>

			<button class="btn btn-es-default btn-sm" data-clear-filter="">Clear Filter</button>
			<div class="t-text--center t-hidden" data-filter-saved="">Filter Saved</div>
		</div>

	</div>

	<hr class="es-hr">
	<div id="es" class="mod-es mod-es-ads-showcase">
		<div id="es-ads-showcase" class="es-ads-showcase carousel slide mootools-noconflict" data-es-ads-showcase>
			<ol class="es-ads-showcase__indicators carousel-indicators">
				<li data-target=".es-ads-showcase" data-bp-slide-to="0" class="active"></li>
				<li data-target=".es-ads-showcase" data-bp-slide-to="1" class=""></li>
				<li data-target=".es-ads-showcase" data-bp-slide-to="2" class=""></li>
			</ol>
			<div class="carousel-inner">

				<div class="item active">
					<div class="es-stream-embed is-ads">
						<a href="javascript:void(0);" class="es-stream-embed__cover" data-ads-link="">
							<div class="es-stream-embed__cover-img" style="background-image: url('https://unsplash.it/480/270/?random');"></div>
						</a>
						<div class="o-grid o-grid--center es-stream-embed--border">
							<div class="o-grid__cell">
								<a href="javascript:void(0);" class="es-stream-embed__title es-stream-embed--border" data-ads-link="">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;view=ads&amp;layout=form							</a>
								<div class="es-stream-embed__meta">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;vihttp://es30.j38/administrator/index.php?option=com_easysocial&amp;vi </div>
								<div class="es-stream-embed__desc t-text--muted">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;view=ads&amp;layout=formhttp://es30.j38/administrator/index.php?option=com_easysocial&amp;vi </div>


							</div>

						</div>
						<div class="es-stream-embed__action">
							<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-ads-link="">Shop Now</a>
						</div>
					</div>
				</div>

				<div class="item">
					<div class="es-stream-embed is-ads">
						<a href="javascript:void(0);" class="es-stream-embed__cover" data-ads-link="">
							<div class="es-stream-embed__cover-img" style="background-image: url('https://unsplash.it/480/270/?random');"></div>
						</a>
						<div class="o-grid o-grid--center es-stream-embed--border">
							<div class="o-grid__cell">
								<a href="javascript:void(0);" class="es-stream-embed__title es-stream-embed--border" data-ads-link="">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;view=ads&amp;layout=form							</a>
								<div class="es-stream-embed__meta">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;vi </div>
								<div class="es-stream-embed__desc t-text--muted">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;view=ads&amp;layout=form </div>
							</div>

						</div>
						<div class="es-stream-embed__action">
							<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-ads-link="">Shop Now</a>
						</div>
					</div>
				</div>

				<div class="item">
					<div class="es-stream-embed is-ads">
						<a href="javascript:void(0);" class="es-stream-embed__cover" data-ads-link="">
							<div class="es-stream-embed__cover-img" style="background-image: url('https://unsplash.it/480/270/?random');"></div>
						</a>
						<div class="o-grid o-grid--center es-stream-embed--border">
							<div class="o-grid__cell">
								<a href="javascript:void(0);" class="es-stream-embed__title es-stream-embed--border" data-ads-link="">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;view=ads&amp;layout=form							</a>
								<div class="es-stream-embed__meta">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;vi </div>
								<div class="es-stream-embed__desc t-text--muted">
									http://es30.j38/administrator/index.php?option=com_easysocial&amp;view=ads&amp;layout=form </div>
							</div>

						</div>
						<div class="es-stream-embed__action">
							<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm" data-ads-link="">Shop Now</a>
						</div>
					</div>
				</div>
			</div>

			<div class="es-ads-showcase__control o-btn-group">
				<a class="btn btn-es-default-o btn-sm" href="javascript:void(0);" role="button" data-bp-slide="prev">
					<span class="fa fa-angle-left"></span>
				</a>
				<a class="btn btn-es-default-o btn-sm" href="javascript:void(0);" role="button" data-bp-slide="next">
					<span class="fa fa-angle-right"></span>
				</a>
			</div>
		</div>
	</div>

</div>
