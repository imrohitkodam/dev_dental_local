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
	<h2>Buttons</h2>
	<hr class="es-hr">
	<p>
		These buttons are widely used in EasySocial and it is ideal to use these button classes to ensure that all buttons are standardized across EasySocial.
	</p>
</div>
<h4>EasySocial Buttons</h4>
<div class="es-styleguide-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<button type="button" class="btn btn-es-default">Default</button>
		<button type="button" class="btn btn-es-primary">Primary</button>
		<button type="button" class="btn btn-es-success">Success</button>
		<button type="button" class="btn btn-es-danger">Danger</button>
	</div>
	<hr class="es-hr">
</div>

<h4>EasySocial Buttons (Outline)</h4>
<div class="es-styleguide-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<button type="button" class="btn btn-es-default-o">Default</button>
		<button type="button" class="btn btn-es-primary-o">Primary</button>
		<button type="button" class="btn btn-es-success-o">Success</button>
		<button type="button" class="btn btn-es-danger-o">Danger</button>
	</div>
	<hr class="es-hr">
</div>

<h4>Dropdown Buttons</h4>
<div class="es-styleguide-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<div class="o-btn-group">
			<button type="button" class="btn btn-es-default-o dropdown-toggle_" data-bs-toggle="dropdown">
				Default &nbsp;<i class="fa fa-caret-down"></i>
			</button>

			<ul class="dropdown-menu">
				<li>
					<a href="javascript:void(0);">Some Hyperlink 1</a>
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 2</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 3</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 4</a>
				</li>
			</ul>
		</div>

		<div class="o-btn-group">
			<button type="button" class="btn btn-es-primary-o dropdown-toggle_" data-bs-toggle="dropdown">
				Primary &nbsp;<i class="fa fa-caret-down"></i>
			</button>

			<ul class="dropdown-menu">
				<li>
					<a href="javascript:void(0);">Some Hyperlink 1</a>
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 2</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 3</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 4</a>
				</li>
			</ul>
		</div>

		<div class="o-btn-group">
			<button type="button" class="btn btn-es-success-o dropdown-toggle_" data-bs-toggle="dropdown">
				Success &nbsp;<i class="fa fa-caret-down"></i>
			</button>

			<ul class="dropdown-menu">
				<li>
					<a href="javascript:void(0);">Some Hyperlink 1</a>
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 2</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 3</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 4</a>
				</li>
			</ul>
		</div>

		<div class="o-btn-group">
			<button type="button" class="btn btn-es-success-o dropdown-toggle_" data-bs-toggle="dropdown">
				Success &nbsp;<i class="fa fa-caret-down"></i>
			</button>

			<ul class="dropdown-menu">
				<li>
					<a href="javascript:void(0);">Some Hyperlink 1</a>
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 2</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 3</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 4</a>
				</li>
			</ul>
		</div>

		<div class="o-btn-group">
			<button type="button" class="btn btn-es-danger-o dropdown-toggle_" data-bs-toggle="dropdown">
				Danger &nbsp;<i class="fa fa-caret-down"></i>
			</button>

			<ul class="dropdown-menu">
				<li>
					<a href="javascript:void(0);">Some Hyperlink 1</a>
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 2</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 3</a>
				</li>
				<li class="divider">
				</li>
				<li>
					<a href="javascript:void(0);">Some Hyperlink 4</a>
				</li>
			</ul>
		</div>
	</div>
	<hr class="es-hr">
</div>

<h4>Button Sizes</h4>
<div class="es-styleguide-wrapper o-button-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<div class="o-grid">
			<div class="o-grid--1of4">
				<div class="o-grid__cell">
					<button type="button" class="btn btn-es-default-o btn-lg">Default (btn-lg)</button>
					<p></p>
					<button type="button" class="btn btn-es-default-o">Default</button>
					<p></p>
					<button type="button" class="btn btn-es-default-o btn-sm">Default (btn-sm)</button>
					<p></p>
					<button type="button" class="btn btn-es-default-o btn-xs">Default (btn-xs)</button>
				</div>
			</div>
			<div class="o-grid--1of4">
				<div class="o-grid__cell">
					<button type="button" class="btn btn-es-primary-o btn-lg">Primary (btn-lg)</button>
					<p></p>
					<button type="button" class="btn btn-es-primary-o">Primary</button>
					<p></p>
					<button type="button" class="btn btn-es-primary-o btn-sm">Primary (btn-sm)</button>
					<p></p>
					<button type="button" class="btn btn-es-primary-o btn-xs">Primary (btn-xs)</button>
				</div>
			</div>
			<div class="o-grid--1of4">
				<div class="o-grid__cell">
					<button type="button" class="btn btn-es-success-o btn-lg">Success (btn-lg)</button>
					<p></p>
					<button type="button" class="btn btn-es-success-o">Success</button>
					<p></p>
					<button type="button" class="btn btn-es-success-o btn-sm">Success (btn-sm)</button>
					<p></p>
					<button type="button" class="btn btn-es-success-o btn-xs">Success (btn-xs)</button>
				</div>
			</div>
			<div class="o-grid--1of4">
				<div class="o-grid__cell">
					<button type="button" class="btn btn-es-danger-o btn-lg">Danger (btn-lg)</button>
					<p></p>
					<button type="button" class="btn btn-es-danger-o">Danger</button>
					<p></p>
					<button type="button" class="btn btn-es-danger-o btn-sm">Danger (btn-sm)</button>
					<p></p>
					<button type="button" class="btn btn-es-danger-o btn-xs">Danger (btn-xs)</button>
				</div>
			</div>
		</div>
		<hr class="es-hr">
	</div>
</div>

<h4>Button Groups</h4>
<div class="es-styleguide-wrapper o-button-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<div class="o-btn-group">
			<button type="button" class="btn btn-es-default-o">Default</button>
			<button type="button" class="btn btn-es-primary-o">Primary</button>
			<button type="button" class="btn btn-es-success-o">Success</button>
			<button type="button" class="btn btn-es-danger-o">Danger</button>
		</div>
	</div>
	<hr class="es-hr">
</div>


<h4>Boolean Buttons</h4>
<div class="es-styleguide-wrapper o-button-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<div class="o-btn-group-yesno">
			<button type="button" class="btn btn--yes is-active">YES</button>
			<button type="button" class="btn btn--no">NO</button>
		</div>
		<div class="o-btn-group-yesno">
			<button type="button" class="btn btn--yes">YES</button>
			<button type="button" class="btn btn--no is-active">NO</button>
		</div>
	</div>
	<hr class="es-hr">
</div>

<h4>Button Groups</h4>
<div class="t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
<div class="o-btn-group">
	<button type="button" class="btn btn-es-default-o btn-sm">Default</button>
	<button type="button" class="btn btn-es-primary-o btn-sm">Primary</button>
	<button type="button" class="btn btn-es-success-o btn-sm">Success</button>
	<button type="button" class="btn btn-es-danger-o btn-sm">Danger</button>
</div>

<div class="o-btn-group">
	<button type="button" class="btn btn-es-default-o btn-sm">Default</button>
	<button type="button" class="btn btn-es-primary-o btn-sm"><i class="fa fa-lock"></i></button>
</div>

	</div>

	<hr class="es-hr">
</div>

<h4>Social Buttons</h4>
<div class="es-styleguide-wrapper o-button-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<a href="" class="btn btn-es-facebook">
			<i class="fab fa-facebook"></i> Facebook
		</a>
		<a href="" class="btn btn-es-twitter">
			<i class="fab fa-twitter"></i> Twitter
		</a>
		<a href="" class="btn btn-es-linkedin">
			<i class="fab fa-linkedin"></i> Linkedin
		</a>
		<a href="" class="btn btn-es-twitch">
			<i class="fab fa-twitch"></i> Twitch
		</a>
		<a href="" class="btn btn-es-apple--d">
			<i class="fab fa-apple"></i> Apple
		</a>
		<a href="" class="btn btn-es-apple--l">
			<i class="fab fa-apple"></i> Apple
		</a>
	</div>
	<hr class="es-hr">
</div>

<h4>Loading States</h4>
<p>
	There are times when a button is performing XHR requests and it's best to always apply the .is-loading class on the button itself as an indicator
	to the end user that an action is being performed so that they will have to wait until the request cycle is completed.
</p>
<div class="es-styleguide-wrapper o-button-wrapper t-lg-mt--xl" data-styleguide-section>
	<div data-behavior="sample_code">
		<button type="button" class="btn btn-es-default is-loading">Default (is-loading)</button>
		<button type="button" class="btn btn-es-primary is-loading">Default (is-loading)</button>
		<button type="button" class="btn btn-es-success is-loading">Default (is-loading)</button>
		<button type="button" class="btn btn-es-danger is-loading">Default (is-loading)</button>
	</div>
	<hr class="es-hr">
</div>
