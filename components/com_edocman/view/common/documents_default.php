<?php
/**
 * @version        1.9.8
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$session	    = JFactory::getSession();
if(!$config->onetime_collect){
	$session->set('name','');
	$session->set('email','');
}
$name		    = $session->get('name','');
$email		    = $session->get('email','');
$user		    = JFactory::getUser();
$userId		    = $user->get('id');
$btnClass	    = $bootstrapHelper->getClassMapping('btn');
$show_category  = JFactory::getApplication()->input->getInt('show_category',0);
JHtml::_('behavior.modal', 'a.edocman-modal');
if ($config->show_detail_in_popup)
{
	$popup      = 'class="edocman-modal ' . $btnClass . ' btn-primary" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
	$popupLink  = 'class="edocman-modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
}
else
{
	$popup      = ' class="' . $btnClass . ' btn-primary" ';
	$popupLink  = '';
}
if (isset($config->number_documents_in_grid))
{
	$numberColumns = $config->number_documents_in_grid;
}
else
{
	$numberColumns = 2;
}
$span      = intval(12 / $numberColumns);
$spanClass = $bootstrapHelper->getClassMapping('span' . $span);


if ($config->collect_downloader_information && !$userId && ($name == '' || $email == ''))
{
	$showDownloadForm = true;
}
else
{
	$showDownloadForm = false;
}
?>
<script src="<?php echo JUri::base(); ?>components/com_edocman/assets/js/jquery.cookie.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(); ?>components/com_edocman/assets/js/layout.js" type="text/javascript"></script>
<script type="text/javascript">
	var spanClass = '<?php echo $spanClass; ?>';
</script>
<div class="<?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
<div id="edocman-documents" class="<?php echo $bootstrapHelper->getClassMapping('span12'); ?>">
	<?php
        require_once JPATH_ROOT.'/components/com_edocman/helper/file.class.php' ;
		$activeItemid       = $Itemid;
		for ($i = 0 , $n = count($items) ;  $i < $n ; $i++)
		{
			if( !empty($category))
			{
				$catId      = $category->id;
			}
			else
			{
				$catId = 0;
			}
			$item           = $items[$i] ;
			$Itemid         = EDocmanHelperRoute::getDocumentMenuId($item->id, $catid, $activeItemid);
			$item->data     = new EDocman_File($item->id,$item->filename, $config->documents_path);
            $imgSrc         = '';
			if ($item->image && JFile::exists(JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$item->image))
			{
				$imgSrc     = JUri::base().'media/com_edocman/document/thumbs/'.$item->image ;
			}
			else
			{
				if (!isset($config->show_default_document_thumbnail) || $config->show_default_document_thumbnail)
				{
					//$imgSrc = '<i class="'.$item->data->fileicon.'"></i>';
				}
				else
				{
					$imgSrc = '' ;
				}
			}
			if ($config->show_detail_in_popup)
			{
				$url        = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&tmpl=component&Itemid='.$Itemid);
			}
			else
			{
				$url        = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&Itemid='.$Itemid);
			}
			$downloadUrl    = JRoute::_('index.php?option=com_edocman&task=document.download&id='.$item->id.'&Itemid='.$Itemid) ;
			$canDownload    = $user->authorise('edocman.download', 'com_edocman.document.'.$item->id) ;
			$canEdit	    = $user->authorise('core.edit',			'com_edocman.document.'.$item->id);
			$canDelete	    = $user->authorise('core.delete',		'com_edocman.document.'.$item->id);
			if(!$canDelete){
				$canDelete	= $user->authorise('edocman.deleteown',	'com_edocman.document.'.$item->id) && $item->created_user_id == $userId;
			}
			$canCheckin	    = $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn	    = $user->authorise('core.edit.own',		'com_edocman.document.'.$item->id) && $item->created_user_id == $userId;
			$canChange	    = $user->authorise('core.edit.state',	'com_edocman.document.'.$item->id) && $canCheckin;
			$canDownload    = ($item->created_user_id == $userId) || ($item->user_ids == "" && ($canDownload || $canEdit)) || ($item->user_ids && in_array($userId, explode(',', $item->user_ids))) ;
			$accept_license = 0;
			if(($config->accept_license) && ($item->license_id > 0 || EdocmanHelper::getDefaultLicense() > 0)){
				$accept_license = 1;
			}
			
			$hide_download_button = $config->hide_download_button;
			$hide_download = $category->hide_download;
			if((int)$hide_download == 1){
				$hide_download_button = 1;
			}elseif((int)$hide_download == 2){
				$hide_download_button = 0;
			}
			$show_view_button = $config->show_view_button;
			$show_view = $category->show_view;
			if((int)$show_view == 1){
				$show_view_button = 1;
			}elseif((int)$show_view == 2){
				$show_view_button = 0;
			}

			if($item->document_url != ""){
				if($config->external_download_link == 1){
					$target = "_blank";
				}else{
					$target = "_self";
				}
			}else{
				$target = "_self";
			}
			if ($i % $numberColumns == 0)
			{
			?>
				<div class="clearfix <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
			<?php
			}
		?>
			<div class="edocman-document <?php echo $bootstrapHelper->getClassMapping('col-md-12'); ?>">
				<div class="edocman-box-heading clearfix">
					<h3 class="edocman-document-title pull-left">
						<?php
							//check 3 options below: 
							//1. Use Download Link instead of Details link
							//2. Check Download permission
							//3. In case user should Accept License before they can download, all link must be go to document details
							if ($config->use_download_link_instead_of_detail_link && $canDownload && ($accept_license == 0))
							{
								if ($showDownloadForm)
								{
								?>
									<a data-toggle="modal" data-document-title="<?php echo $item->title; ?>" title="<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>"  id="<?php echo $item->id; ?>" class="email-popup edocman-document-title-link" href="#form-content" target="<?php echo $target;?>"> 
										<?php
										if($config->show_icon_beside_title){
											echo '<i class="'.$item->data->fileicon.'"></i>';
										} ?>
										<?php echo $item->title; ?>
									</a>
								<?php
								}
								else
								{
								?>
									<a href="<?php echo $downloadUrl; ?>" title="<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>" class="edocman-document-title-link" target="<?php echo $target;?>">
										<?php
										if($config->show_icon_beside_title){
											echo '<i class="'.$item->data->fileicon.'"></i>';
										}
										?>
										<?php echo $item->title; ?>
									</a>
								<?php
								}
							}
							else
							{
							?>
								<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>" <?php echo $popupLink; ?> class="edocman-document-title-link">
									<?php
									if($config->show_icon_beside_title){
                                        echo '<i class="'.$item->data->fileicon.'"></i>';
                                    } ?>
									<?php echo $item->title; ?>
								</a>
							<?php
							}
							if($item->indicators != '' || !empty($item->new_indicator) || !empty($item->update_indicator))
							{
								$indicators = explode(',', $item->indicators);
							?>
								<span class="indicators">
									<?php
									if (!empty($item->new_indicator))
									{
									?>
										<span class="edocman_new">
												<?php echo JText::_('EDOCMAN_NEW');?>
										</span>
									<?php
									}elseif(!empty($item->update_indicator)){
										?>
										<span class="edocman_updated">
												<?php echo JText::_('EDOCMAN_UPDATED');?>
										</span>
										<?php
									}
									if(in_array('featured', $indicators))
									{
									?>
										<span class="edocman_featured">
												<?php echo JText::_('EDOCMAN_FEATURED');?>
										</span>
									<?php
									}
									if(in_array('hot', $indicators))
									{
									?>
										<span  class="edocman_hot">
											<?php echo JText::_('EDOCMAN_HOT');?>
										</span>
									<?php
									}
									?>
								</span>
							<?php
							}
							?>
					</h3>
				</div>
				<div class="edocman-description clearfix">
					<div class="edocman-description-details clearfix">
						<?php
							if ($imgSrc)
							{
							?>
								<div style="float:left;margin-right:10px;">
									<img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="edocman-thumb-left img-polaroid" />
								</div>
							<?php
							}

							if((int)$show_category == 1){
								$category_url = EDocmanHelperRoute::getCategoryRoute($item->category->id,$activeItemid);
								?>
								<strong><?php echo JText::_('EDOCMAN_CATEGORY');?>:</strong>
								<a href="<?php echo JRoute::_($category_url)?>" title="<?php echo $item->category->title;?>">
								<?php
								echo $item->category->title;
								?>
								</a>
								<br />
								<?php
							}

							//output event description
							if (!$item->short_description)
							{
								$item->short_description = $item->description;
							}
							if((int)$config->number_words > 0){
								$description = strip_tags($item->short_description);
								$descriptionArr = explode(" ",$description);
								if(count($descriptionArr) > (int)$config->number_words){
									for($d = 0;$d < (int)$config->number_words - 1;$d++){
										echo $descriptionArr[$d]." ";
									}
									echo "..";
								}else{
									echo $description;
								}
							}else{
								echo $item->short_description;
							}
						?>
					</div>
					<div class="clearfix"></div>
					<div class="edocman-taskbar clearfix">
						<ul>
							<?php
							if ($canDownload && $hide_download_button !=  1  && ($accept_license == 0))
							{
								if ($showDownloadForm)
								{
								?>
									<li>
										<a data-toggle="modal" data-document-title="<?php echo $item->title; ?>"  id="<?php echo $item->id; ?>" class="email-popup <?php echo $btnClass; ?>" href="#form-content">
											<?php if($item->document_url != ""){?>
												<i class="edicon edicon-link"></i>
												<?php echo JText::_('EDOCMAN_OPEN_DOCUMENT'); ?>
											<?php }else{ ?>
												<i class="edicon edicon-cloud-download"></i>
												<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>
											<?php } ?>

										</a>
									</li>
								<?php
								}
								else
								{
								?>
									<li>
										<a class="<?php echo $btnClass; ?>" href="<?php echo $downloadUrl; ?>" target="<?php echo $target;?>">
											<?php if($item->document_url != ""){?>
												<i class="edicon edicon-link"></i>
											<?php }else{ ?>
												<i class="edicon edicon-cloud-download"></i>
											<?php } ?>
											<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>
										</a>
									</li>
								<?php
								}
							}elseif(!$canDownload && $hide_download_button != 1 && $config->login_to_download && (int)$userId == 0){
								?>
								<li>
									<a data-toggle="modal" class="email-popup edocman-download-link <?php echo $btnClass;?> edocman-download-btn" href="#login-form">
										<span class="edocman_download_label">
											<?php
											echo JText::_('EDOCMAN_LOGIN_TO_DOWNLOAD'); ?>
										</span>
									</a>
								</li>
								<?php
							}elseif($canDownload && $hide_download_button != 1 && ($accept_license == 1)){
								?>
									<li>
										<a class="<?php echo $btnClass; ?>" href="<?php echo $url; ?>">
											<?php if($item->document_url != ""){?>
												<i class="edicon edicon-link"></i>
											<?php }else{ ?>
												<i class="edicon edicon-cloud-download"></i>
											<?php } ?>
											<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>
										</a>
									</li>
								<?php
							}

							if ($canDownload && $show_view_button == 1 && $item->canView)
							{
								$playextension = array('mp4','flv','mp3','ogg','ogv');
								$audio_array = array('mp3','ogg');
								$frame_array = array('flv');
								$ext = \Joomla\String\StringHelper::strtolower(EDocmanHelper::getFileExtension($item)) ;
								if(in_array($ext,$playextension) && !EDocmanHelper::isDropBoxTurnedOn() && !EDocmanHelper::isAmazonS3TurnedOn()){
									$viewUrl = JUri::root()."index.php?option=com_edocman&view=play&id=".$item->id."&tmpl=component";
									if(in_array($ext,$audio_array)){
										$audio_player = "rel=\"{handler: 'iframe', size: {x: 300, y: 50}, iframeOptions: {scrolling: 'no'}}\"";
									}elseif(in_array($ext,$frame_array)){
										$audio_player = "rel=\"{handler: 'iframe', size: {x: 450, y: 300}, iframeOptions: {scrolling: 'no'}}\"";
									}else{
										$audio_player = "";
									}
									?>
									<li>
										<?php
										if($config->view_option == 0){
										?>
											<a href="<?php echo $viewUrl; ?>" class="<?php echo $btnClass; ?> edocman-modal" data-toggle="modal" <?php echo $audio_player;?>>
												<i class="edicon edicon-eye"></i>
												<?php echo JText::_('EDOCMAN_VIEW'); ?>
											</a>
										<?php
										}else{
										?>
											<a href="<?php echo $viewUrl; ?>" class="<?php echo $btnClass; ?>" target="_blank" data-toggle="modal" <?php echo $audio_player;?>>
												<i class="edicon edicon-eye"></i>
												<?php echo JText::_('EDOCMAN_VIEW'); ?>
											</a>
										<?php
										}
										?>
									</li>
									<?php
								}else {

									$viewUrl = JRoute::_('index.php?option=com_edocman&task=document.viewdoc&id=' . $item->id . '&Itemid=' . $Itemid);
									?>
									<li>
										<a class="<?php echo $btnClass; ?>" href="<?php echo $viewUrl; ?>"
										   target="_blank">
											<i class="edicon edicon-eye"></i>
											<?php echo JText::_('EDOCMAN_VIEW'); ?>
										</a>
									</li>
									<?php
								}
							}

							if ($config->hide_details_button !== '1')
							{
							?>
								<li>
									<a href="<?php echo $url; ?>" <?php echo $popup; ?>>
										<?php echo JText::_('EDOCMAN_DETAILS'); ?>
									</a>
								</li>
							<?php
							}
							$pass_lock = true;
							if($config->lock_function){ //lock function is turned on
								if(($item->locked_by != $user->id) && ($item->is_locked == 1)){
									$pass_lock = false;
								}
							}
							if (($canEdit || $canEditOwn) && ($pass_lock))
							{
								$url = JRoute::_('index.php?option=com_edocman&task=document.edit&id='.$item->id.'&Itemid='.$Itemid) ;
								?>
								<li>
									<a class="<?php echo $btnClass; ?>" href="<?php echo $url; ?>">
										<i class="edocman-icon-pencil"></i>
										<?php echo JText::_('EDOCMAN_EDIT'); ?>
									</a>
								</li>
								<?php
							}
							if ($canDelete)
							{
							?>
								<li>
									<a class="<?php echo $btnClass; ?>" href="javascript:deleteConfirm(<?php echo $item->id; ?>);">
										<i class="edocman-icon-trash"></i>
										<?php echo JText::_('EDOCMAN_DELETE'); ?>
									</a>
								</li>
							<?php
							}
							if ($canChange)
							{
								if ($item->published)
								{
									$text = JText::_('EDOCMAN_UNPUBLISH');
									$url = "javascript:publishConfirm($item->id, 0)";
									$class = 'edocman-icon-remove';
								}
								else
								{
									$url = $url = "javascript:publishConfirm($item->id, 1)";
									$text = JText::_('EDOCMAN_PUBLISH');
									$class = 'edocman-icon-ok';
								}
							?>
							<li>
								<a href="<?php echo $url; ?>" class="<?php echo $btnClass; ?>">
									<i class="<?php echo $class; ?>"></i>
									<?php echo $text; ?>
								</a>
							</li>
							<?php
							}
							?>
						</ul>
					</div>
				</div>
			</div>
		<?php
			if (($i + 1) % $numberColumns == 0)
			{
			?>
				</div>
			<?php
			}
		}
		if ($i % $numberColumns != 0)
		{
			echo "</div>" ;
		}
	?>
</div>
</div>

<?php
if ($showDownloadForm)
{
	echo EDocmanHelperHtml::loadCommonLayout('common/modal.php', array('bootstrapHelper' => $bootstrapHelper,'config' => $config));
}
if(!$canDownload && $hide_download_button != 1 && $config->login_to_download && (int)$userId == 0){
	echo EDocmanHelperHtml::loadCommonLayout('common/login.php', array('bootstrapHelper' => $bootstrapHelper));
}
?>
