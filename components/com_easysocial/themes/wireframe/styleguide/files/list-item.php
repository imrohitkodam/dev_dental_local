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
	<h2>List item</h2>
	<hr class="es-hr" />

</div>

<div>
	<div class="t-lg-mt--xl">
		<div class="es-styleguide-wrapper t-lg-mt--xl" data-styleguide-section>

			<div class="es-list">
				<?php for ($i=0; $i < 4; $i++) { ?>
				<div class="es-list__item">



					<div class="es-list-item es-island is-featured">

						<div class="es-list-item__checkbox">
							<div class="o-checkbox">
								<input type="checkbox" id="item-checkbox-1">
								<label for="item-checkbox-1">&nbsp;</label>
							</div>
						</div>

						<div class="es-list-item__media">
							<a href="#" class="o-avatar o-avatar--rounded">
								<img src="/media/com_easysocial/defaults/avatars/user/large.png"/>
							</a>
						</div>

						<div class="es-list-item__context">
							<div class="es-list-item__hd">
								<div class="es-list-item__content">

									<div class="es-list-item__title">
										<a href="">Event one Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit fugiat eveniet temporibus distinctio quisquam quis quo, quasi, nobis ratione, perferendis debitis dicta autem eum nesciunt perspiciatis numquam reiciendis sit dignissimos!</a>
									</div>

									<div class="es-list-item__meta">
										<ol class="g-list-inline g-list-inline--delimited">
											<li>
												<a href="/index.php?option=com_easysocial">General</a>
											</li>

											<li data-breadcrumb="&#183;">
												<span >Public</span>
											</li>
											<li data-breadcrumb="&#183;">
												<span >1,234 Members</span>
											</li>
										</ol>
									</div>
								</div>
								<div class="es-list-item__state">
									<div class="es-label-state es-label-state--featured" data-original-title="Featured" data-es-provide="tooltip">
										<i class="es-label-state__icon"></i>
									</div>
								</div>

								<div class="es-list-item__action">
									<a href="javascript:void(0);" class="btn btn-sm btn-es-default">Follow</a>
									<a href="javascript:void(0);" class="btn btn-sm btn-es-default">Join</a>
									<div class="dropdown_">
										<a href="javascript:void(0);" class="btn btn-sm btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
											<i class="fa fa-ellipsis-v"></i>
										</a>

										<ul class="dropdown-menu dropdown-menu-right">
											<li>
												<a href="javascript:void(0);">Some Hyperlink 1</a>
											</li>
											<li>
												<a href="javascript:void(0);">Some Hyperlink 1</a>
											</li>
											<li>
												<a href="javascript:void(0);">Some Hyperlink 1</a>
											</li>
										</ul>
									</div>
								</div>
							</div>

							<div class="es-list-item__bd">
								<div class="es-list-item__desc">
									Event one Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit fugiat eveniet temporibus distinctio quisquam quis quo, quasi, nobis ratione, perferendis debitis dicta autem eum nesciunt perspiciatis numquam reiciendis sit dignissimos!
								</div>
								<div class="">
									<ol class="g-list-inline g-list-inline--delimited" data-behavior="sample_code">
											<li data-breadcrumb="|"><a href="#">Cat 1</a></li>
											<li data-breadcrumb="|"><a href="#">Cat 2</a></li>
											<li data-breadcrumb="|"><a href="#">Cat 3</a></li>
											<li data-breadcrumb="|" class="current"><a href="#">Cat 4</a></li>
										</ol>
								</div>
							</div>

						</div>

					</div>

				</div>
				<?php } ?>
			</div>
			<div class="es-list-title">
				Random Groups
			</div>
			<div class="es-list">
				<?php for ($i=0; $i < 4; $i++) { ?>
				<div class="es-list__item">

					<div class="es-list-item es-island is-featured">

						<div class="es-list-item__media">
							<a href="#" class="o-avatar o-avatar--rounded">
								<img src="/media/com_easysocial/defaults/avatars/user/large.png"/>
							</a>
						</div>

						<div class="es-list-item__context">
							<div class="es-list-item__hd">
								<div class="es-list-item__content">

									<div class="es-list-item__title">
										<a href="">Event one Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit fugiat eveniet temporibus distinctio quisquam quis quo, quasi, nobis ratione, perferendis debitis dicta autem eum nesciunt perspiciatis numquam reiciendis sit dignissimos!</a>
									</div>

									<div class="es-list-item__meta">
										<ol class="g-list-inline g-list-inline--delimited">
											<li>
												<a href="/index.php?option=com_easysocial">General</a>
											</li>

											<li data-breadcrumb="&#183;">
												<span >Public</span>
											</li>
											<li data-breadcrumb="&#183;">
												<span >1,234 Members</span>
											</li>
										</ol>
									</div>
								</div>
								<div class="es-list-item__state">
									<div class="es-label-state es-label-state--featured" data-original-title="Featured" data-es-provide="tooltip">
										<i class="es-label-state__icon"></i>
									</div>
								</div>

								<div class="es-list-item__action">
									<div class="dropdown_">
										<a href="javascript:void(0);" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
											<i class="fa fa-ellipsis-v"></i>
										</a>

										<ul class="dropdown-menu dropdown-menu-right">
											<li>
												<a href="javascript:void(0);">Some Hyperlink 1</a>
											</li>
											<li>
												<a href="javascript:void(0);">Some Hyperlink 1</a>
											</li>
											<li>
												<a href="javascript:void(0);">Some Hyperlink 1</a>
											</li>
										</ul>
									</div>
								</div>
							</div>

							<!-- <div class="es-list-item__bd">
								<div class="es-list-item__desc">
									Event one Lorem ipsum dolor sit amet, consectetur adipisicing elit. Odit fugiat eveniet temporibus distinctio quisquam quis quo, quasi, nobis ratione, perferendis debitis dicta autem eum nesciunt perspiciatis numquam reiciendis sit dignissimos!
								</div>
							</div> -->

						</div>

					</div>

				</div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>
