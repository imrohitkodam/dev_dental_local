<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<h1>Marketplace</h1>

<div class="es-marketplace">
	<div class="es-entry-actionbar es-island">
		<div class="o-grid-sm">
			<div class="o-grid-sm__cell">
				<a href="#" class="btn btn-es-default-o btn-sm">← Back To Marketplace</a>
			</div>

			<div class="o-grid-sm__cell">
				<div class="o-btn-group t-lg-pull-right" role="group">
					<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-es-toggle="dropdown">
						 <i class="fa fa-ellipsis-h"></i>
						 <span class="t-hidden">Manage</span>
					</button>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a href="javascript:void(0);">Featured Item</a>
						</li>
						<li>
							<a href="#">Edit</a>
						</li>

						<li>
							<a href="javascript:void(0);">Delete</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="es-apps-entry-section es-island">
		<div class="es-apps-entry-section__content">
			<div class="swiper-container es-mkp-gallery-top">
				<div class="swiper-wrapper">
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=1')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=2')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=3')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=4')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=5')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=6')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=7')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=8')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=9')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=10')"></div>
				</div>
				<!-- Add Arrows -->
				<div class="swiper-button-next swiper-button-white"></div>
				<div class="swiper-button-prev swiper-button-white"></div>
			</div>
			<div class="swiper-container es-mkp-gallery-thumbs">
				<div class="swiper-wrapper">
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=1')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=2')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=3')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=4')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=5')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=6')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=7')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=8')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=9')"></div>
					<div class="swiper-slide" style="background-image:url('https://picsum.photos/800/600?random=10')"></div>
				</div>
			</div>


<script>
	EasySocial.require()
	.script('site/vendors/prism')
	.library(
		'swiper'
	).done(function($) {
		// Marketplace swiper
		var esMkpGalleryThumbs = new Swiper('.es-mkp-gallery-thumbs', {
			spaceBetween: 10,
			slidesPerView: 4,
			freeMode: true,
			watchSlidesVisibility: true,
			watchSlidesProgress: true,
		});
		var esMkpGalleryTop = new Swiper('.es-mkp-gallery-top', {
			spaceBetween: 10,
			navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
			},
			thumbs: {
			swiper: esMkpGalleryThumbs
			}
		});
	});
</script>

			<div class="es-swiper-market-embed t-hidden">
				<div class="embed-responsive embed-responsive-16by9">
					<img src="https://unsplash.it/640/360/?random" alt="" class="embed-responsive-item">
				</div>
				<div class="es-swiper-embed">
					Use JS swiper
				</div>
			</div>

			<div class="es-apps-title">
				Apps title
			</div>
			<div class="l-cluster l-spaces--xs t-lg-mb--lg">
				<div>
					<div class="">
						<div class="o-label2 o-label2--success">MYR 2,222</div>
					</div>
					<div class="">
						<div class="o-label2 o-label2--default">In Stock</div>
					</div>
					<div class="">
						<div class="o-label2 o-label2--danger">Out of Stock</div>
					</div>
					<div class="">
						<button type="button" class="btn btn-es-default-o"><i class="far fa-comment-dots"></i> Message Seller</button>
					</div>
				</div>
			</div>

			<div class="l-cluster l-spaces--xs es-apps-entry__meta t-lg-mb--lg">
				<div>
					<span>By Sarah</span>
					<span>&middot;</span>
					<span><a href="#">Categories</a></span>
					<span>&middot;</span>
					<span>11,111 Views</span>
				</div>
			</div>

			<div class="es-market-content t-lg-mb--lg">
				Lorem ipsum dolor, sit amet consectetur, adipisicing elit. Autem, dicta, voluptate? Reiciendis commodi, corrupti neque debitis eligendi molestias mollitia distinctio aperiam fuga quae fugiat rerum enim dicta architecto quas inventore?
			</div>
			<table class="table-es-market t-lg-mb--lg">
				<thead>
					<tr>
						<th colspan="3">
							More Information
						</th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<th  width="200">
							Brand
						</th>
						<td>
							Apple
						</td>
					</tr>
					<tr>
						<th>
							Brand
						</th>
						<td>
							Apple
						</td>
					</tr>
					<tr>
						<th>
							Brand
						</th>
						<td>
							Apple
						</td>
					</tr>
				</tbody>
			</table>


			<table class="table-es-market t-lg-mb--lg">
				<thead>
					<tr>
						<th colspan="3">Item Information</th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<th width="200">Price</th>
						<td>B 23,00	</td>
					</tr>
					<tr>
						<th width="200">Condition</th>
						<td>New</td>
					</tr>
				</tbody>
			</table>

			<table class="table-es-market t-lg-mb--lg">
				<thead>
					<tr>
						<th colspan="3">Next Step</th>
					</tr>
				</thead>

				<tbody>
					<tr>
						<th width="200">Set a title</th>
						<td>2</td>
					</tr>
				</tbody>
			</table>

			<div class="">
				Follow [data-stream-actions]
			</div>
		</div>
	</div>
</div>
