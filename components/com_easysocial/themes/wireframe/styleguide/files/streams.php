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
	<h3>Stream Objects</h3>
	<hr class="es-hr" />
	<p>
		These stream objects are used within the activity stream and it should be standardized to ensure that all the stream items appears as standardized as possible.
	</p>
</div>

<div>
	<div data-styleguide-section>

		<h4>filter bar</h4>

		<div class="es-stream-filter-bar t-lg-mb--lg">

				<div class="es-stream-filter-bar__cell">
				 <div class="o-media">
					<div class="o-media__image">
						Filter Timeline:
					</div>
					<div class="o-media__body">
						<div class="o-btn-group" data-filter-wrapper="">
							<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown" data-active-filter-button="">
								<div class="o-loader o-loader--sm"></div>
								<span data-active-filter-text="">
																					Me &amp; Friends																	</span> &nbsp;<i class="fa fa-caret-down"></i>
							</button>

							<ul class="dropdown-menu dropdown-menu-left es-timeline-filter-dropdown">
								<li>
									<span class="es-timeline-filter-dropdown__title">News Feed</span>
								</li>

														<li class="" data-dashboard-filter="" data-type="everyone">
									<a href="/index.php?option=com_easysocial&amp;view=dashboard&amp;type=everyone&amp;Itemid=126">
										<span data-filter-text="">Everyone</span>
										<div class="o-tabs__bubble" data-counter="">0</div>
									</a>
								</li>

								<li class="active" data-dashboard-filter="" data-type="me">
									<a href="/index.php?option=com_easysocial&amp;view=dashboard&amp;type=me&amp;Itemid=126">
										<span data-filter-text="">
																					Me &amp; Friends																	</span>
										<div class="o-tabs__bubble" data-counter="">0</div>
									</a>
									<div class="o-loader o-loader--sm"></div>
								</li>

														<li class="" data-dashboard-filter="" data-type="following">
									<a href="/index.php?option=com_easysocial&amp;view=dashboard&amp;type=following&amp;Itemid=126">
										<span data-filter-text="">Following</span>
										<div class="o-tabs__bubble" data-counter="">0</div>
									</a>
								</li>

														<li class="" data-dashboard-filter="" data-type="bookmarks">
									<a href="/index.php?option=com_easysocial&amp;view=dashboard&amp;type=bookmarks&amp;Itemid=126">
										<span data-filter-text="">My Favourites</span>
									</a>
								</li>

														<li class="" data-dashboard-filter="" data-type="sticky">
									<a href="/index.php?option=com_easysocial&amp;view=dashboard&amp;type=sticky&amp;Itemid=126">
										<span data-filter-text="">Pinned Items</span>
									</a>
								</li>


								<li class="divider"></li>
								<li>
									<span class="es-timeline-filter-dropdown__title">Custom Filters</span>
								</li>
								<li class="has-bubble">
									<a href="/">
										<span>loremdsfadsfdaslfjlsadkfjl;asdjf;laskdjfl;dasfaasadjkf</span>
									</a>
										<span class="es-timeline-filter-dropdown__indicator"></span>
								</li>
								<li class="has-bubble">

									<a href="/">
										<span>loremdsfadsfdaslfjlsadkfjl;asdjf;laskdjfl;sadjkf</span>
									</a>
									<span class="es-timeline-filter-dropdown__bubble">99</span>
								</li>



													</ul>
						</div>
					</div>
				</div>
			</div>

			<div class="es-stream-filter-bar__cell">
				 <div class="o-media">
					<div class="o-media__image">
					</div>
					<div class="o-media__body">
						<div class="o-btn-group">
							<button type="button" class="btn btn-es-default-o btn-sm dropdown-toggle_" data-bs-toggle="dropdown">
								<i class="es-stream-filter-icon"><i></i></i>&nbsp; Post Types &nbsp;<i class="fa fa-caret-down"></i>
							</button>

							<div class="dropdown-menu dropdown-menu-right es-stream-filter-dropdown" data-filter-post-type-wrapper="">
								<div>
									<span class="es-stream-filter-dropdown__title"><?php echo JText::_('COM_ES_FILTER_POSTS_DROPDOWN_TITLE');?></span>
									<p class="es-stream-filter-dropdown__desc"><?php echo JText::_('COM_ES_FILTER_POSTS_DROPDOWN_INFO');?></p>
								</div>
								<ul class="es-stream-filter-dropdown__list">
									<li class="es-stream-filter-dropdown__item">
										<div class="o-checkbox">
											<input id="post-type-audios" name="postTypes[]" value="audios" data-filter-post-type="" type="checkbox">
											<label for="post-type-audios" data-filter-post-type-label="">Audio</label>
										</div>
									</li>
															<li class="es-stream-filter-dropdown__item">
										<div class="o-checkbox">
											<input id="post-type-events" name="postTypes[]" value="events" data-filter-post-type="" type="checkbox">
											<label for="post-type-events" data-filter-post-type-label="">Events</label>
										</div>
									</li>
															<li class="es-stream-filter-dropdown__item">
										<div class="o-checkbox">
											<input id="post-type-feeds" name="postTypes[]" value="feeds" data-filter-post-type="" type="checkbox">
											<label for="post-type-feeds" data-filter-post-type-label="">Feeds</label>
										</div>
									</li>
															<li class="es-stream-filter-dropdown__item">
										<div class="o-checkbox">
											<input id="post-type-followers" name="postTypes[]" value="followers" data-filter-post-type="" type="checkbox">
											<label for="post-type-followers" data-filter-post-type-label="">Followers</label>
										</div>
									</li>
															<li class="es-stream-filter-dropdown__item">
										<div class="o-checkbox">
											<input id="post-type-friends" name="postTypes[]" value="friends" data-filter-post-type="" type="checkbox">
											<label for="post-type-friends" data-filter-post-type-label="">Friends</label>
										</div>
									</li>
															<li class="es-stream-filter-dropdown__item">
										<div class="o-checkbox">
											<input id="post-type-groups" name="postTypes[]" value="groups" data-filter-post-type="" type="checkbox">
											<label for="post-type-groups" data-filter-post-type-label="">Groups</label>
										</div>
									</li>
															<li class="es-stream-filter-dropdown__item">
										<div class="o-checkbox">
											<input id="post-type-links" name="postTypes[]" value="links" data-filter-post-type="" type="checkbox">
											<label for="post-type-links" data-filter-post-type-label="">Links</label>
										</div>
									</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
			</div>

		<h4>Discussion Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-pinned-divider"><span><i class="fa fa-thumbtack"></i> Pinned Items</span></div>
			<div class="es-stream-apps type-discuss is-">
				<div class="es-stream-apps__hd">
					<a href="/" class="es-stream-apps__title ">
						Joomla! The CMS Trusted By Millions for their Websites Joomla! The CMS Trusted By Millions for their Websites Joomla! The CMS Trusted By for their Websites Joomla! The CMS Trusted By Millions for their Websites Joomla! The CMS Trusted By Millions for their Websites Joomla! The CMS Trusted By for their Websites
					</a>
					<div class="es-stream-apps__meta">
						Posted on Friday July 2016.
					</div>
					<i class="fa fa-lock es-stream-apps__state"></i>
				</div>
				<div class="es-stream-apps__bd es-stream-apps--border">
					<div class="es-stream-apps__desc">
						Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
					</div>
					<ol class="g-list--horizontal has-dividers--right">
						<li class="g-list__item"><a href="#">View Discussion</a></li>
						<li class="g-list__item"><a href="#">Add Reply</a></li>
					</ol>
				</div>
			</div>

			<div class="es-stream-apps type-discuss is-locked">
				<div class="es-stream-apps__hd">
					<div class="o-flag">
						<div class="o-flag__image">
							<a href="" class="o-avatar o-avatar--sm">
								<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
							</a>
						</div>
						<div class="o-flag__body">
							<a href="" class="">Jake Rocheleau</a>
							<span>reply:</span>
						</div>
					</div>
					<i class="fa fa-lock es-stream-apps__state"></i>
				</div>
				<div class="es-stream-apps__bd es-stream-apps--border">
					<div class="es-stream-apps__desc is-quote">
						<div class="o-flag">
							<div class="o-flag__image">
								<a href="" class="o-avatar o-avatar--sm">
									<img src="/media/com_easysocial/defaults/avatars/user/medium.png"/>
								</a>
							</div>
							<div class="o-flag__body">
								<a href="" class="">Jake Rocheleau</a>
								<span>reply:</span>
								<i class="fa fa-check-circle es-stream-apps__state"></i>
							</div>
						</div>
						Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
						Joomla! is the mobile-ready way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
					</div>
					<ol class="g-list--horizontal has-dividers--right">
						<li class="g-list__item"><a href="#">View Reply</a></li>
						<li class="g-list__item"><a href="#">Add Reply</a></li>
					</ol>

					<hr class="es-hr">
					<div class="es-stream-apps__desc is-file">
						<span>
						attachmentfileattachmentfileattachmentfileattachmentfileattachmentfileattachmentfileattachmentfileattachmentfileattachmentfileattachmentfile.zip
						</span>

					</div>
					<ol class="g-list--horizontal has-dividers--right">
						<li class="g-list__item"><a href="#">View Attachment</a></li>
						<li class="g-list__item"><a href="#">Add Reply</a></li>
					</ol>
				</div>
			</div>

		</div>
	</div>

	<div data-styleguide-section>
		<h4>Repost Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-repost">
				<div class="es-stream-repost__text">test</div>
				<div class="es-stream-repost__meta">
					<div class="es-stream-repost__meta-inner">
						<div class="es-stream-repost__title"><a class="" alt="admin" href="/index.php?option=com_easysocial&amp;view=profile&amp;id=740:admin&amp;Itemid=127">
						admin    </a> uploaded 7 photos in the album <a href="/index.php?option=com_easysocial&amp;view=albums&amp;id=3:site-com-easysocial-view-albums-layout-form-no-task-itemid-127-fluid-ds020992839131933505&amp;layout=item&amp;uid=740:admin&amp;type=user&amp;Itemid=127">site com_easysocial view-albums layout-form no-task itemid-127 fluid DS020992839131933505</a></div>
						<div class="es-stream-repost__content">
							placeholder
						</div>

					</div>
				</div>
			</div>

		</div>
	</div>

	<div data-styleguide-section>
		<h4>User Object</h4>

		<div data-behavior="sample_code">

		</div>
	</div>

	<div data-styleguide-section>
		<h4>Group Object</h4>

		<div data-behavior="sample_code">
			<?php
				// Get the first group it can find
				$groupsModel = ES::model('Groups');
				$groups = $groupsModel->getGroups(array('limit' => 1));
				$group = $groups[0];
			?>
			<?php echo $this->html('group.stream', $group); ?>
		</div>
	</div>

	<div data-styleguide-section>
		<h4>Event Object</h4>

		<div data-behavior="sample_code">

		</div>
	</div>

	<div data-styleguide-section>
		<h4>Page Object</h4>

		<div data-behavior="sample_code">



		</div>
	</div>

	<div data-styleguide-section>
		<h4>Links Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-embed is-link">
				<a href="/" class="es-stream-embed__cover">
					<div class="es-stream-embed__cover-img" style="background-image: url('https://unsplash.it/480/270/?random');"></div>

					<!-- <img alt="Joomla! The CMS Trusted By Millions for their Websites" src=""> -->
				</a>
				<a href="/" class="es-stream-embed__title es-stream-embed--border">
					 Joomla! The CMS Trusted By Millions for their Websites
				</a>
				<div class="es-stream-embed__meta">
					joomla.org
				</div>
				<div class="es-stream-embed__desc">
					Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
				</div>
			</div>

			<hr class="es-hr">

			<div class="es-stream-embed is-link">
				<a href="/" class="es-stream-embed__cover">
					<div class="es-stream-embed__cover-img" style="background-image: url('https://unsplash.it/270/480/?random');"></div>
				</a>
				<a href="/" class="es-stream-embed__title es-stream-embed--border">
					 Joomla! The CMS Trusted By Millions for their Websites
				</a>
				<div class="es-stream-embed__meta">
					joomla.org
				</div>
				<div class="es-stream-embed__desc">
					Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
				</div>
			</div>

			<hr class="es-hr">

			<div class="es-stream-embed is-link">
				<a href="/" class="es-stream-embed__cover">
					<div class="es-stream-embed__cover-img" style="background-image: url('https://cdn.joomla.org/images/joomla-org-og.jpg');"></div>
					<!-- <img alt="Joomla! The CMS Trusted By Millions for their Websites" src="https://cdn.joomla.org/images/joomla-org-og.jpg"> -->
				</a>
				<a href="/" class="es-stream-embed__title es-stream-embed--border">
					 Joomla! The CMS Trusted By Millions for their Websites
				</a>
				<div class="es-stream-embed__meta">
					joomla.org
				</div>
				<div class="es-stream-embed__desc">
					Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
				</div>
			</div>


		</div>
	</div>

	<div data-styleguide-section>
		<h4>Ads</h4>
		<div data-behavior="sample_code">
			<div class="es-stream-embed is-ads">
				<a href="/" class="es-stream-embed__cover">
					<div class="es-stream-embed__cover-img" style="background-image: url('https://unsplash.it/480/270/?random');"></div>
				</a>
				<div class="o-grid o-grid--center es-stream-embed--border">
					<div class="o-grid__cell">
						<a href="/" class="es-stream-embed__title">
							 Joomla! The CMS Trusted By Millions for their Websites
						</a>

						<div class="es-stream-embed__meta">
							joomla.org
						</div>
						<div class="es-stream-embed__desc t-text--muted">
							Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
						</div>

					</div>
					<div class="o-grid__cell o-grid__cell--auto-size">
						<div class="es-stream-embed__action">
							<a href="javascript:void(0);" class="btn btn-es-default-o">Shop Now</a>
						</div>
					</div>
				</div>






			</div>
		</div>
	</div>

	<div data-styleguide-section>
		<h4>Video/iframe Object</h4>
		<p>Mostly for 3rd party player iframe type</p>
		<div data-behavior="sample_code">
			<div class="es-stream-embed is-video">
				<div class="es-stream-embed__player">
					<div class="video-container">
						<iframe width="1280" height="720" frameborder="0" allowfullscreen="" src="https://www.youtube.com/embed/7SWvDHvWXok?feature=oembed"></iframe>
					</div>
				</div>
				<a href="/" class="es-stream-embed__title es-stream-embed--border">
					 Joomla! The CMS Trusted By Millions for their Websites
				</a>
				<div class="es-stream-embed__meta">
					joomla.org
				</div>
				<div class="es-stream-embed__desc">
					Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
				</div>
			</div>
		</div>
	</div>


	<div data-styleguide-section>
		<h4>Achievements/Apps Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-embed is-achievement">
				<img src="/media/com_easysocial/badges/story-teller.png" alt="">
				<div class="es-stream-embed__achievement-context">
					<div class="es-stream-embed__achievement-title">
						 Story Teller
					</div>
					<b>Loves sharing stories.</b>
				</div>

			</div>

			<div class="es-stream-embed is-apps">
				<div class="o-avatar o-avatar--lg o-avatar--text o-avatar--bg-2 es-app-item__avatar">G</div>
				<div class="es-stream-embed__apps-context">
					<div class="es-stream-embed__apps-title">
						 Apps name <span>1.2.3</span>
					</div>
					<b>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ipsam iste totam eveniet voluptatibus quidem aliquam, accusantium, hic ea quo excepturi repellendus possimus, eius dolore delectus, expedita aliquid aspernatur amet quia?.</b>
				</div>

			</div>
		</div>
	</div>

	<div data-styleguide-section>
		<h4>Broadcasts / Restrict Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-embed is-broadcasts">
				<div class="es-stream-embed__context">
					<a href="/" class="es-stream-embed__broadcasts-title">
						 Joomla! The CMS Trusted By Millions for their Websites
					</a>

					<div class="es-stream-embed__broadcasts-text">
						Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
						Joomla! is the mobile-ready and user-friendly way to build your website. Choose from thousands of features and designs. Joomla! is free and open source.
					</div>
				</div>

				<div class="es-stream-embed__broadcasts-icon">
					<i class="fa fa-bullhorn"></i>
				</div>
			</div>
			<hr class="es-hr">

			<div class="es-stream-embed is-broadcasts">
				<div class="es-stream-embed__context">
					<div class="es-stream-embed__broadcasts-title">
						 Restricted Content
					</div>

					<div class="es-stream-embed__broadcasts-text">
						You need to <a href="/">login</a> to see the rest of the stream items. If you not yet have an account, register new account today.
					</div>
				</div>

				<div class="es-stream-embed__broadcasts-icon">
					<i class="fa fa-lock"></i>
				</div>

			</div>
			<hr class="es-hr">

			<div class="es-stream-embed is-rss">
				<div>
					<a class="t-text--bold">
						 <i class="fa fa-rss-square"></i> Rss feed app needs some love
					</a>

					<div class="">
						Lorem ipsum dolor sit amet, consectetur adipisicing elit. Expedita dolorum similique necessitatibus voluptatibus ratione quisquam eos, vitae sit nisi hic. Iusto asperiores mollitia nemo dignissimos provident maxime exercitationem est. Aperiam.
					</div>
				</div>
			</div>
			<hr class="es-hr">




			<hr class="es-hr">
		</div>
	</div>

	<div data-styleguide-section>
		<h4>File Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-embed is-file">
				<div class="es-stream-embed__file-icon">
					<i class="fa fa-file-archive-o"></i>
				</div>
				<div class="es-stream-embed__file-context">
					<a href="/" class="es-stream-embed__file-link">
						 filename-01.zip
					</a>
					<b>234 kb</b>
				</div>

			</div>
		</div>
	</div>


	<div data-styleguide-section>
		<h4>Polls Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-embed is-polls">
				<form data-polls-form="" id="pollsForm" name="pollsForm" class="form-horizontal">
					<div class="es-polls__title">
						test 2
						<a data-polls-edit-button="" class="t-hiddenx" href="javascript:void(0);"> [Edit]</a>
					</div>
					<div data-polls-questions-list="" class="es-polls__list">
						<div data-count="0" data-id="3" data-vote-item="" class="es-polls__item o-checkbox">
							<input type="checkbox" id="item-checkbox-3" data-id="3" data-vote-item-option="" name="optionsRadios">
							<label for="item-checkbox-3">
								test
								<div data-poll-bar-3="" class="es-polls__progress progress">
									<div class="progress-bar progress-bar-primary" style="width: 50%;"></div>
								</div>
								<div data-poll-voters-4="" class="es-polls__voters">
									<a data-user-id="740" data-popbox="module://easysocial/profile/popbox" class="o-avatar o-avatar-sm" href="/">
										<img src="/media/com_easysocial/defaults/avatars/user/medium.png" alt="admin">
									</a>
									<a data-user-id="740" data-popbox="module://easysocial/profile/popbox" class="o-avatar o-avatar-sm" href="/">
										<img src="/media/com_easysocial/defaults/avatars/user/medium.png" alt="admin">
									</a>
									<a data-user-id="740" data-popbox="module://easysocial/profile/popbox" class="o-avatar o-avatar-sm" href="/">
										<img src="/media/com_easysocial/defaults/avatars/user/medium.png" alt="admin">
									</a>
								</div>
								<a data-poll-count-button="" href="javascript:void(0);" class="es-polls__count">
									<span data-poll-count-label-3="">1</span> vote(s) </a>
							</label>
						</div>
						<div data-count="0" data-id="4" data-vote-item="" class="es-polls__item o-checkbox">
							<input type="checkbox" id="item-checkbox-4" data-id="4" data-vote-item-option="" name="optionsRadios">
							<label for="item-checkbox-4">
								tet 2
								<div data-poll-bar-4="" class="es-polls__progress progress">
									<div class="progress-bar progress-bar-primary" style="width: 50%;"></div>
								</div>
								<div data-poll-voters-4="" class="es-polls-voters hide">
								</div>
								<a data-poll-count-button="" href="javascript:void(0);" class="es-polls__count">
									<span data-poll-count-label-4="">1</span> vote(s) </a>
							</label>
						</div>
						<div data-count="0" data-id="4" data-vote-item="" class="es-polls__item o-checkbox">
							<input type="checkbox" id="item-checkbox-4" data-id="4" data-vote-item-option="" name="optionsRadios">
							<label for="item-checkbox-4">
								tet 2
								<div data-poll-bar-4="" class="es-polls__progress progress">
									<div class="progress-bar progress-bar-primary" style="width: 50%;"></div>
								</div>
								<div data-poll-voters-4="" class="es-polls-voters hide">
								</div>
								<a data-poll-count-button="" href="javascript:void(0);" class="es-polls__count">
									<span data-poll-count-label-4="">1</span> vote(s) </a>
							</label>
						</div>
					</div>
					<div class="pull-right text-right">
						<div data-polls-notice="" class="alert hide"></div>
					</div>
				</form>


			</div>
		</div>
	</div>

	<div data-styleguide-section>
		<h4>Calendar Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-embed is-calendar">
				<div>
					<div class="o-grid o-grid--center">
						<div class="o-grid__cell">
							<a class="t-text--bold">
								 Event link
							</a>
							<div class="">
								16th Jul, 2016 12:00AM - 16th Jul, 2016 12:00AM
							</div>
							<div class="">
								All Day Event
							</div>
						</div>
						<div class="o-grid__cell o-grid__cell--auto-size ">
							<div class="es-calendar-date">
								<div class="es-calendar-date__date">
									19
								</div>
								<div class="es-calendar-date__mth">
									Sep
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="o-box--border">
					<b>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Praesentium asperiores, non reprehenderit natus! Porro non voluptatum nostrum, mollitia corrupti cum enim, soluta? Voluptates aspernatur similique voluptate vitae nam. Cumque, ipsa.</b>
				</div>
			</div>
		</div>
	</div>


	<div data-styleguide-section>
		<h4>Tasks/Milestone Object</h4>

		<div data-behavior="sample_code">
			<div class="es-stream-apps type-milestone">
				<div class="es-stream-apps__hd">

					<a href="/index.php?option=com_easysocial&amp;view=apps&amp;layout=canvas&amp;customView=item&amp;uid=2:bmw-i8&amp;type=group&amp;id=42:tasks&amp;milestoneId=3&amp;Itemid=127" class="es-stream-apps__title">test</a>
					<div class="es-stream-apps__meta t-fs--sm">
									<span class="t-lg-mr--md"><i class="fa fa-user"></i> <a class="" alt="admin" href="/index.php?option=com_easysocial&amp;view=profile&amp;id=740:admin&amp;Itemid=127">
				admin</a> is responsible</span>

						<span>
						Due on Tuesday, 09 August 2016            </span>
					</div>
				</div>

				<div class="es-stream-apps__bd es-stream-apps--border">
					<div class="es-stream-apps__desc">
									<hr>
						<p><b>tetstedsfs dsfsdafds</b> <u>asdfasdfsdfsdfsd</u></p>
								</div>

				</div>
			</div>

			<div class="es-stream-apps type-tasks">
				<div class="es-stream-apps__hd">
					<div class="es-stream-apps__title">Tasks:</div>
				</div>

				<div class="es-stream-apps__bd es-stream-apps--border">
					<div class="es-stream-apps__desc">
									<div class="completed">
							<div class="o-checkbox">
								<input type="checkbox" checked="checked" data-task-104-checkbox="" value="7" id="task-7">
								<label for="task-7">dddxx</label>
							</div>
						</div>
								</div>
				</div>
			</div>
		</div>
	</div>

	<div data-styleguide-section>
		<h4>Hikashop app Object</h4>

		<div data-behavior="sample_code">
			<h4>Single</h4>
			<div class="es-hika-items">
				<div class="es-hika-item">
					<div class="es-hika-item__img" style="background-image: url('https://unsplash.it/480/270/?random');">
					</div>
					<div class="es-hika-item__context">
						<a href="/" class="es-hika-item__title">
							 iPhone 7 (black)
						</a>
						<ul class="g-list-inline g-list-inline--delimited es-hika-item__meta">
							<li>
								MYR 45
							</li>
							<li data-breadcrumb="·">
								<a href="">Mobile Accessories</a>
							</li>
						</ul>
						<div class="es-hika-item__desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cumque magnam in est rem pariatur? Eius sed veritatis a error quaerat amet nisi temporibus, nulla, laudantium odio quasi, excepturi magnam aspernatur?</div>
					</div>
				</div>
			</div>
			<h4>Listing</h4>
			<div class="es-hika-items">
				<?php for ($i=0; $i < 3; $i++) { ?>
				<div class="es-hika-item">
					<div class="es-hika-item__img" style="background-image: url('https://unsplash.it/480/270/?random');">
					</div>
					<div class="es-hika-item__context">
						<a href="/" class="es-hika-item__title">
							 iPhone 7 (black)
						</a>
						<ul class="g-list-inline g-list-inline--delimited es-hika-item__meta">
							<li>
								MYR 45
							</li>
							<li data-breadcrumb="·">
								<a href="">Mobile Accessories</a>
							</li>
						</ul>
						<div class="es-hika-item__desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Cumque magnam in est rem pariatur? Eius sed veritatis a error quaerat amet nisi temporibus, nulla, laudantium odio quasi, excepturi magnam aspernatur?</div>
					</div>

				</div>
				<?php } ?>
			</div>
			<hr>

		</div>
	</div>
</div>

