<?php
/**
* @package 		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div class="x">
	<h1>Lists</h1>
	<code>g-list-flex</code>
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

	<code>g-list-inline</code>
	<ol class="g-list-inline" data-behavior="sample_code">
		<li><a href="#">Home</a></li>
		<li><a href="#">About</a></li>
		<li><a href="#">The Board</a></li>
		<li class="current"><a href="#">Directors</a></li>
	</ol>

	<code>g-list-inline g-list-inline--delimited</code>
	<ol class="g-list-inline g-list-inline--delimited" data-behavior="sample_code">
		<li><a href="#">Home</a></li>
		<li data-breadcrumb="&raquo;"><a href="#">About</a></li>
		<li data-breadcrumb="&raquo;"><a href="#">The Board</a></li>
		<li data-breadcrumb="&raquo;" class="current"><a href="#">Directors</a></li>
	</ol>

	<code>g-list-inline g-list-inline--delimited</code>
	<ol class="g-list-inline g-list-inline--delimited" data-behavior="sample_code">
		<li><a href="#">Home</a></li>
		<li data-breadcrumb="|"><a href="#">About</a></li>
		<li data-breadcrumb="|"><a href="#">The Board</a></li>
		<li data-breadcrumb="|" class="current"><a href="#">Directors</a></li>
	</ol>

	<code>g-list-inline g-list-inline--dashed</code>
	<ol class="g-list-inline g-list-inline--dashed" data-behavior="sample_code">
		<li><a class="" href="#">Posts</a></li>
		<li><a class="" href="#">Categories</a></li>
		<li><a class="" href="#">Tags</a></li>
		<li class="current"><a class="" href="#">Members</a></li>
	</ol>

	<code>g-list-inline g-list-inline--space-right</code>
	<ol class="g-list-inline g-list-inline--space-right" data-behavior="sample_code">
		<li><a href="#">Home</a></li>
		<li><a href="#">About</a></li>
		<li><a href="#">The Board</a></li>
		<li class="current"><a href="#">Directors</a></li>
	</ol>

	<code>g-list-inline g-list-inline--space-left</code>
	<ol class="g-list-inline g-list-inline--space-left" data-behavior="sample_code">
		<li><a href="#">Home</a></li>
		<li><a href="#">About</a></li>
		<li><a href="#">The Board</a></li>
		<li class="current"><a href="#">Directors</a></li>
	</ol>

	<p class="t-lg-mt--lg">Experimental flex method</p>
	<code>g-list--horizontal </code>
	<ol class="g-list--horizontal" data-behavior="sample_code">
		<li class="g-list__item"><a href="#">Home</a></li>
		<li class="g-list__item"><a href="#">About</a></li>
		<li class="g-list__item"><a href="#">The Board</a></li>
		<li class="g-list__item current"><a href="#">Directors</a></li>
	</ol>

	<code>g-list--horizontal has-dividers--right</code>
	<ol class="g-list--horizontal has-dividers--right" data-behavior="sample_code">
		<li class="g-list__item"><a href="#">Home</a></li>
		<li class="g-list__item"><a href="#">About</a></li>
		<li class="g-list__item"><a href="#">The Board</a></li>
		<li class="g-list__item"><a href="#">Profile</a></li>
		<li class="g-list__item"><a href="#">Contact Us</a></li>
		<li class="g-list__item current"><a href="#">Directors</a></li>
	</ol>

	<h1>Navigations</h1>
	<p>Navigations with basic cosmetic styles</p>
	<code>o-nav</code>
	<ul class="o-nav" data-behavior="sample_code">
		<li class="o-nav__item"><a href=#>Home</a></li>
		<li class="o-nav__item"><a href=#>About</a></li>
		<li class="o-nav__item"><a href=#>Portfolio</a></li>
		<li class="o-nav__item"><a href=#>Contact</a></li>
	</ul>

	<code>o-nav o-nav--stacked</code>
	<ul class="o-nav  o-nav--stacked" data-behavior="sample_code">
		<li class="o-nav__item"><a class="o-nav__link" href=#>Home</a></li>
		<li class="o-nav__item"><a class="o-nav__link" href=#>About</a></li>
		<li class="o-nav__item"><a class="o-nav__link" href=#>Portfolio</a></li>
		<li class="o-nav__item"><a class="o-nav__link" href=#>Contact</a></li>
	</ul>

	<ul class="o-nav  o-nav--banner" data-behavior="sample_code">
		<li><a href=#>Home</a></li>
		<li><a href=#>About</a></li>
		<li><a href=#>Portfolio</a></li>
		<li><a href=#>Contact</a></li>
	</ul>

	<ul class="o-nav  o-nav--block  o-nav--foo" data-behavior="sample_code">
		<li><a href=#>Home</a></li><!--
	 --><li><a href=#>About</a></li><!--
	 --><li><a href=#>Portfolio</a></li><!--
	 --><li><a href=#>Contact</a></li>
	</ul>

	<ul class="o-nav  o-nav--fit  o-nav--foo" data-behavior="sample_code">
		<li><a href=#>Home</a></li>
		<li><a href=#>About</a></li>
		<li><a href=#>Portfolio</a></li>
		<li><a href=#>Contact</a></li>
	</ul>

	<ul class="o-nav  o-nav--keywords" data-behavior="sample_code">
		<li><a href=#>Home</a></li>
		<li><a href=#>About</a></li>
		<li><a href=#>Portfolio</a></li>
		<li><a href=#>Contact</a></li>
	</ul>


</div>
