<?php
// Since EasyBlog does not use online / offline states, we don't rely on this yet.
?>
<div class="l-stack">
	<div class="o-avatar  o-avatar--xl is-mobile is-online">
		<div class="o-avatar__mobile"></div>
		<div class="o-avatar__content">
			<img src="/components/com_easyblog/assets/images/default_blogger.png"/>
		</div>
		<div class="o-avatar__action">
			<a href="javascript:void(0);" class="dropdown-toggle_" data-bp-toggle="dropdown">
				<i class="fdi fas fa-cog"></i>
			</a>
			<ul class="dropdown-menu">
				<li data-avatar-upload-button="">
					<a href="javascript:void(0);">Upload Picture</a>
				</li>

				<li data-avatar-select-button="">
					<a href="javascript:void(0);">Choose from photos</a>
				</li>

					<li class="divider"></li>
				<li data-avatar-webcam="">
					<a href="javascript:void(0);">Take a photo</a>
				</li>
			</ul>

		</div>
	</div>

	<!-- Remove action only -->
	<div class="o-avatar  o-avatar--xl is-online">
		<div class="o-avatar__mobile"></div>
		<div class="o-avatar__content">
			<img src="/components/com_easyblog/assets/images/default_blogger.png"/>
		</div>
		<div class="o-avatar__action">
			<div class="o-avatar__remove-tag">
				<a href="javascript:void(0);" data-placement="top" data-eb-provide="tooltip" data-original-title="<?php echo JText::_('COM_ES_REMOVE_TAG');?>" data-remove-tag>
					<i class="fdi fa fa-times"></i>
				</a>
			</div>
		</div>
	</div>
</div>
