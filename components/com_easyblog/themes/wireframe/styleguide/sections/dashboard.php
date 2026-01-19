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
<div class="l-stack l-spaces--lg">
	<div class="grid grid-cols-1 md:grid-cols-3 gap-sm">
		<?php for ($i=0; $i < 3; $i++) { ?>
		<div class="">
			<a href="/administrator/index.php?option=com_easyblog&amp;view=bloggers" class="db-post-item">
				<div class="t-flex-grow--1 t-min-width--0 t-pr--md">
					<div class="t-d--flex t-w-100 t-align-items--c">
						<div class="t-mr--sm">
							<i class="fdi fa fa-user-friends text-gray-500"></i>
						</div>
						<div class="t-min-width--0 t-flex-grow--1 t-overflow--hidden">
							<div class="t-text--truncate">Categories Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
							tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
							quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
							consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
							cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
							proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</div>
						</div>
					</div>
				</div>
				<div class="ml-auto">
					<div>
						<b>21</b>
					</div>
				</div>
			</a>
		</div>
		<?php } ?>
	</div>

	<div class="t-font-weight--bold l-spaces--lg">Blog posts</div>

	<div class="fd-tab l-spaces--xs">
		<div class="fd-tab__item is-active">
			<a class="fd-tab__link" href="javascript:void(0);">
				Description
			</a>
		</div>
		<div class="fd-tab__item">
			<a class="fd-tab__link" href="javascript:void(0);">
				Reviews
			</a>
		</div>
	</div>

	<div class="eb-stats-listing">
		<?php for ($i=0; $i < 5; $i++) { ?>
		<div>
			<div class="t-d--flex">
				<div class="t-min-width--0 t-flex-grow--1 t-text--truncate">
					<div class="t-text--truncate">
						<i class="fdi far fa-file-alt text-muted t-mr--md"></i>
						<a href="/index.php?option=com_easyblog&amp;view=entry&amp;id=1&amp;Itemid=139" class="t-font-weight--bold t-text--800">You have successfully installed EasyBlog Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
						tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
						quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
						consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
						cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
						proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</a>
					</div>
				</div>
				<div class="t-flex-shrink--0">
					<div class="text-small text-muted t-ml--md">
						2 hits
					</div>
				</div>
			</div>
		</div>
		<?php } ?>

	</div>



	<div class="t-font-weight--bold l-spaces--lg">Comments</div>

	<div class="fd-tab l-spaces--xs">
		<div class="fd-tab__item is-active">
			<a class="fd-tab__link" href="javascript:void(0);">
				Description
			</a>
		</div>
		<div class="fd-tab__item">
			<a class="fd-tab__link" href="javascript:void(0);">
				Reviews
			</a>
		</div>
	</div>

	<div class="eb-stats-listing">

		<?php for ($i=0; $i < 2; $i++) { ?>

		<div>

			<div class="t-d--flex">
				<div class="t-flex-shrink--0 t-pr--md">
					<div class="o-avatar o-avatar--md o-avatar--rounded">
						<div class="o-avatar__mobile"></div>
						<div class="o-avatar__content">
							<img src="/media/com_easyblog/images/avatars/author.png" alt="Super User" width="32" height="32">
						</div>
					</div>
				</div>
				<div class="t-min-width--0 t-flex-grow--1 l-stack l-spaces--xs">
					<div class="t-text--truncate">
						<a href="/index.php?option=com_easyblog&amp;view=entry&amp;id=1&amp;Itemid=139" class="t-font-weight--bold t-text--800">You have successfully installed EasyBlog Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
						tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
						quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
						consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
						cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
						proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</a>

					</div>
					<div class="">
						comment Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
						tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
						quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
						consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
						cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
						proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
					</div>
					<div class="fd-inline-list">
						<div class="" fd-breadcrumb="">
							Tuesday, 21 September 2021 5:05 AM
						</div>

						<div class="" fd-breadcrumb="·">
							<a href="javascript:void(0);" class="t-text--500">View comments</a>
						</div>

					</div>
				</div>

			</div>

		</div>
		<div>

			<div class="t-d--flex">
				<div class="t-flex-shrink--0 t-pr--md">
					<div class="o-avatar o-avatar--md o-avatar--rounded">
						<div class="o-avatar__mobile"></div>
						<div class="o-avatar__content">
							<img src="/media/com_easyblog/images/avatars/author.png" alt="Super User" width="32" height="32">
						</div>
					</div>
				</div>
				<div class="t-min-width--0 t-flex-grow--1 l-stack l-spaces--xs">
					<div class="t-text--truncate">
						<a href="/index.php?option=com_easyblog&amp;view=entry&amp;id=1&amp;Itemid=139" class="t-font-weight--bold t-text--800">You have successfully installed EasyBlog Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod
						tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam,
						quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo
						consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
						cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
						proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</a>

					</div>

					<div class="fd-inline-list">
						<div class="" fd-breadcrumb="">
							999 Comments
						</div>

					</div>
				</div>

			</div>

		</div>

		<?php } ?>

	</div>


</div>
