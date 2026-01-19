<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h3>Objects &mdash; Loading Indicator</h3>
<hr class="es-hr" />
<p>
	There are times where you need to add loading indicators in the content before the ajax response returns. Adding a loading indicator will help inform the user that the content is being loaded.
</p>

<hr class="es-hr">

<p>Apply <code>is-loading</code> to parent wrap show <code>o-loader</code></p>
<hr class="es-hr">
<h4>Center (without text aligned to top)</h4>
<div data-styleguide-section>
	<div data-behavior="sample_code">

<div class="is-loading" style="position: relative;height: 500px;border:1px solid #ddd;">
	<div class="o-loader o-loader--top"></div>
	<p>o-loader parent have to position relative</p>
</div>
	</div>
</div>

<hr class="es-hr">
<h3>Experimental Loader</h3>
<p>Apply <code>is-active</code> to show <code>o-loader</code></p>
<hr class="es-hr">
<h4>Center (without text aligned to top)</h4>
<div data-styleguide-section>
	<div data-behavior="sample_code">

<div class="" style="position: relative;height: 500px;border:1px solid #ddd;">
	<div class="o-loader o-loader--top is-active"></div>
	<p>o-loader parent have to position relative</p>
</div>
	</div>
</div>

<hr class="es-hr">

<h4>Center (with text)</h4>
<div class="is-loading" style="position: relative;height: 500px;border:1px solid #ddd;">
	<div class="o-loader with-text">Loading</div>
	<p>o-loader parent have to position relative</p>
</div>
<hr class="es-hr">
<h4>Inline with size</h4>
<div data-styleguide-section>
	<div data-behavior="sample_code">

<div class="">
	<div class="o-loader o-loader--inline is-active"></div>
	<div class="o-loader o-loader--sm o-loader--inline is-active"></div>
</div>

	</div>
</div>

<hr class="es-hr">

<div class="o-form-group">
	<div class="o-control-label">Audio</div>
	<div class="o-control-input">
		<div class="o-input-group">
			<input class="o-form-control validation keyup length-4" placeholder="Address line 1" name="es-fields" value="" type="text">
			<div class="o-loader o-loader--sm is-active"></div>
		</div>

	</div>
</div>

<div class="o-form-group">
	<div class="o-control-label">Audio</div>
	<div class="o-control-input">
		<div class="o-input-group">
			<input type="text" placeholder="your-name" data-permalink-input="" autocomplete="off" value="" id="permalink" name="es-fields-8" class="o-form-control validation keyup length-4 required">
			<span class="o-input-group__btn">
				<button data-permalink-check="" class="btn btn-es-default-o is-loading" type="button">Check</button>
			</span>

		</div>

	</div>
</div>

<hr class="es-hr">
<div class="t-lg-mt--xl">
	<h4>Loading States</h4>

	<p>
		There are times when a button is performing XHR requests and it's best to always apply the .is-loading class on the button itself as an indicator
		to the end user that an action is being performed so that they will have to wait until the request cycle is completed.
	</p>

	<p data-behavior="sample_code">
		<button type="button" class="btn btn-es-default is-loading">Default (is-loading)</button>
		<button type="button" class="btn btn-es-primary is-loading">Default (is-loading)</button>
		<button type="button" class="btn btn-es-success is-loading">Default (is-loading)</button>
		<button type="button" class="btn btn-es-danger is-loading">Default (is-loading)</button>

	</p>
</div>
