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
<div class="l-stack">
	<div class="eb-bar eb-bar--snackbar">
		<div class="t-d--flex t-align-items--c sm:t-flex-direction--c t-w--100">
			<div class="t-flex-grow--1 sm:t-mb--md">
				<h2 class="eb-head-title reset-heading">
					Posts
				</h2>
			</div>
			<div class="">
				<a href="javascript:void(0);" class="btn btn-primary">
					<i class="fdi fa fa-pencil-alt"></i>
					&nbsp; New Post
				</a>
			</div>
		</div>
	</div>

	<div class="eb-bar eb-bar--search-filter-bar">
		<div class="t-d--flex sm:t-flex-direction--c t-flex-grow--1">
			<div class="sm:t-mb--md lg:t-mr--xs t-min-width--0 ">
				<div class="dropdown_">
					<a data-bp-toggle="dropdown" href="javascript:void(0);" class="btn btn-default dropdown-toggle_ t-border--1 t-bg--100 t-d--flex t-align-items--c t-w--100">
						Category
						<i class="fdi fa fa-caret-down t-ml--lg t-text--500"></i>
					</a>
					<div class="dropdown-menu  sm:t-mb--md t-text--truncate " style="width: 320px;max-height: 320px; overflow: hidden;">

						<div class="dl-menu-wrapper">
							<ul class="eb-filter-menu eb-filter-menu--parent o-tabs--dlmenu" data-ed-category-group="">
								<li class="eb-filter-menu__item active" data-category-filter="0">
									<a href="javascript:void(0);" class="eb-filter-menu__link t-text--truncate" data-eb-filter="category" data-id="0">
										<i class="fdi far fa-folder-open"></i>&nbsp; All Categories </a>
								</li>
								<li class="eb-filter-menu__item" data-category-filter="0">
									<a href="javascript:void(0);" class="eb-filter-menu__link t-text--truncate" data-eb-filter="category" data-id="0">
										<i class="fdi far fa-folder-open"></i>&nbsp; All Categories </a>
								</li>
								<li class="eb-filter-menu__item " data-category-filter="3">
									<a href="javascript:void(0);" title="1111" data-eb-filter="category" data-id="3" class="eb-filter-menu__link">
										contain sub-categories
									</a>

									<a href="javascript:void(0);" class="eb-filter-menu__toggle">
										<i class="fdi fa fa-angle-right"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="sm:t-mb--md lg:t-mr--sm">
				<div class="dropdown_">
					<!-- @TODO Use t-border--primary if label is active state -->
					<a data-bp-toggle="dropdown" href="javascript:void(0);" class="btn btn-default dropdown-toggle_ t-border--1 t-bg--100 t-d--flex t-align-items--c t-w--100 t-border--primary">
						Tags (2) <i class="fdi fa fa-plus t-ml--lg t-text--500"></i>
					</a>
					<div class="dropdown-menu t-mt--2xs sm:t-w--100" style="min-width: 280px; max-width: 350px; max-height: 350px;" >
						<div class="eb-dropdown-menu-hd">
							<b>Add tags to your search results</b>
						</div>
						<div class="t-px--xs t-py--md">
							<div class="l-cluster l-spaces--xs t-mb--lg">
								<div class="must-have">
									<div class="t-min-width--0">
										<div class="t-d--flex t-text--truncate">
											<a href="javascript:void(0);" class="eb-filter-label " data-ed-filter="label" data-id="4">
												Tag label
											</a>
										</div>
									</div>

									<div class="t-min-width--0">
										<div class="t-d--flex t-text--truncate">
											<a href="javascript:void(0);" class="eb-filter-label  is-active" data-ed-filter="label" data-id="4">
												Tag
											</a>
										</div>
									</div>
									<div class="t-min-width--0">
										<div class="t-d--flex t-text--truncate">
											<a href="javascript:void(0);" class="eb-filter-label" data-ed-filter="label" data-id="4">
												testing
											</a>
										</div>
									</div>
									<div class="t-min-width--0">
										<div class="t-d--flex t-text--truncate">
											<a href="javascript:void(0);" class="eb-filter-label  is-active" data-ed-filter="label" data-id="4">
												Tag
											</a>
										</div>
									</div>
								</div>
							</div>
							<div class="">
								<a href="javascript:void(0);" class="fd-link">Clear selection</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="t-flex-grow--1">
				<input type="text" name="query" class="eb-search-filter-input t-w--100" autocomplete="off" placeholder="Search..." value="">
			</div>
		</div>
		<div class="t-d--flex t-align-items--c sm:t-pl--no lg:t-pl--md sm:t-pt--md sm:t-justify-content--c">
			<button type="button" class="btn btn-default-ghost"><i class="fdi fa fa-search"></i></button>
		</div>
	</div>

	<div class="eb-bar eb-bar--filter-bar">
		<div class="t-d--flex t-align-items--c sm:t-flex-direction--c t-w--100">
			<div class="t-flex-grow--1 sm:t-mb--md">
				<b>1 of 3 results found for "you"</b>
			</div>
			<div class="t-d--flex sm:t-flex-direction--c sm:t-w--100">

				<div class="dropdown_ t-mr--md sm:t-mb--md sm:t-mr--no">
					<a data-bp-toggle="dropdown" href="javascript:void(0);" class="btn btn-default dropdown-toggle_ t-border--1  t-d--flex t-align-items--c t-w--100 sm:t-justify-content--c">
						Sort by
						<i class="fdi fa fa-caret-down t-ml--lg t-text--500"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu--filter-menu sm:t-mb--md t-text--truncate has-active-markers" style="width: 220px;overflow: hidden;">
						<li class="">
							<a href="javascript:void(0);" class="">
								Most Popular
							</a>
						</li>
						<li class="active">
							<a href="javascript:void(0);" class="">
								Most Popular
							</a>
						</li>
					</ul>
				</div>

				<div class="dropdown_ sm:t-mr--no">
					<a data-bp-toggle="dropdown" href="javascript:void(0);" class="btn btn-default dropdown-toggle_ t-border--1  t-d--flex t-align-items--c t-w--100 sm:t-justify-content--c">
						Ascending
						<i class="fdi fa fa-caret-down t-ml--lg t-text--500"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu--filter-menu sm:t-mb--md t-text--truncate has-active-markers" style="width: 220px;overflow: hidden;">
						<li class="">
							<a href="javascript:void(0);" class="">
								Most Popular
							</a>
						</li>
						<li class="active">
							<a href="javascript:void(0);" class="">
								Most Popular
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>


	<h3>Filterbar main wrapper/template</h3>
	<button class="btn btn-default-o" toggle-bulk-action>toggle bulk</button>
	<div class="eb-bar eb-bar--filter-bar" my-filter-bar>
		<div class="eb-bar__bulk-action sm:t-w--100" data-eb-table-actions="">
			[Render bulk action on each view]
		</div>
		<div class="eb-bar__search-action">
			[Render search action on each view]
		</div>
	</div>

	<h4>e.g. Narrow layout</h4>

	<div class="narrow" style="width: 600px">
		<div class="eb-bar eb-bar--filter-bar" my-filter-bar>
			<div class="eb-bar__bulk-action sm:t-w--100" data-eb-table-actions="">
				<div class="t-d--flex sm:t-flex-direction--c t-mr--md sm:t-mr--no">
					<div class="sm:t-w--100 t-mr--md sm:t-mb--md sm:t-mr--no" style="width:120px;">
						<div class="o-select-group">
							<select name="templateActions" class="form-control" data-eb-table-task="">
								<option value="" selected="selected">
									Bulk Actions </option>
								<option value="posts.copy">
									Copy </option>
								<option value="posts.publish">
									Publish </option>
								<option value="posts.unpublish">
									Unpublish </option>
								<option value="posts.trash">
									Trash </option>
							</select>
							<label for="templateActions" class="o-select-group__drop"></label>
						</div>
					</div>
					<a class="btn btn-default sm:t-w--100" href="javascript:void(0);" data-eb-table-apply="">
						Apply
					</a>
				</div>
			</div>
			<div class="eb-bar__search-action">
				<div class="t-d--flex t-align-items--c sm:t-flex-direction--c t-w--100">
					<div class="t-flex-grow--1"></div>
					<div class="t-d--flex sm:t-flex-direction--c t-align-items--s t-flex-grow--0 sm:t-w--100">
						<div class="t-d--flex sm:t-flex-direction--c t-flex-grow--0 t-justify-content--fe">
							<div class="sm:t-w--100 t-mr--md sm:t-mb--md sm:t-mr--no" style="width:160px;">
								<div class="o-select-group">
									<select name="filter" class="form-control">
										<option value="all" selected="selected">
											Select Filter </option>
										<option value="1">
											Published </option>
										<option value="0" selected="selected">
											Unpublished </option>
										<option value="4">
											Under Review </option>
										<option value="2">
											Scheduled </option>
										<option value="3">
											Drafts </option>
										<option value="-1">
											Trashed </option>
									</select>
									<label for="filter" class="o-select-group__drop"></label>
								</div>
							</div>
							<div class="sm:t-w--100 t-mr--md sm:t-mb--md sm:t-mr--no" style="width:160px;">
								<div class="o-select-group">
									<select name="category" id="category" class="form-control pull-right" data-eb-filter-dropdown="">
										<option value="0">Select Category</option>
										<option value="2">Will be able to create new category</option>
										<option value="1">Uncategorized</option>
										<option value="5">&nbsp;&nbsp;&nbsp;|_sub 1</option>
										<option value="6">&nbsp;&nbsp;&nbsp;|_sub 2</option>
										<option value="3">dynamic modifier and advance pricing</option>
										<option value="4">Automatically repost posts from this category on respective social networks</option>
									</select>
									<label for="" class="o-select-group__drop"></label>
								</div>
							</div>
						</div>
						<div class="t-d--flex sm:t-flex-direction--c">
							<input type="text" name="post-search" class="form-control t-mr--md sm:t-mb--md sm:t-mr--no" placeholder="Search..." value="">
							<a href="javascript:void(0);" class="btn btn-default t-d--flex t-align-items--c t-px--md t-justify-content--c">
								<i class="fdi fa fa-search"></i>
							</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>


	<script>
		let toggleBulkAction = document.querySelector("[toggle-bulk-action]");

		toggleBulkAction.onclick = function() {

			var filterBar = document.querySelectorAll("[my-filter-bar]");
				for (i = 0; i < filterBar.length; i++) {
					filterBar[i].classList.toggle('is-bulk-action');
			}
		};
	</script>



</div>
