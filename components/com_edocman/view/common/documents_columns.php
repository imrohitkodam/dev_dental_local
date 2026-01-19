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
jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
if(!JFolder::exists(JPATH_ROOT.'/media/com_edocman/document/medium')){
    JFolder::create(JPATH_ROOT.'/media/com_edocman/document/medium');
    JFile::copy(JPATH_ROOT.'/media/com_edocman/index.html',JPATH_ROOT.'/media/com_edocman/document/medium/index.html');
}
$session        = JFactory::getSession();
$name           = $session->get('name','');
$email          = $session->get('email','');
$user           = JFactory::getUser();
$userId         = $user->get('id');
$btnClass       = $bootstrapHelper->getClassMapping('btn');
$show_category  = JFactory::getApplication()->input->getInt('show_category',0);
if ($config->show_detail_in_popup)
{
	JHtml::_('behavior.modal', 'a.edocman-modal');
	$popup      = 'class="edocman-modal ' . $btnClass . ' btn-primary" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
	$popupLink  = 'class="edocman-modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
	$popup_img  = ' class="edocman-modal hover-effect" rel="{handler: \'iframe\', size: {x: 800, y: 500}}" ';
}
else
{
	$popup      = ' class="' . $btnClass . ' btn-primary" ';
	$popupLink  = '';
	$popup_img  = '';
}
if (isset($config->number_columns))
{
	$numberColumns = $config->number_columns;
}
else
{
	$numberColumns = 2;
}
$span           = intval(12 / $numberColumns);
$spanClass      = $bootstrapHelper->getClassMapping('span' . $span);
$rowfluidClass  = $bootstrapHelper->getClassMapping('row-fluid');

switch ($span) {
    case "2":
        $nrepeat = 6;
        break;
    case "3":
        $nrepeat = 4;
        break;
    case "4":
        $nrepeat = 3;
        break;
    case "6":
        $nrepeat = 2;
        break;
    case "12":
        $nrepeat = 1;
        break;
}

if ($config->collect_downloader_information && !$userId && ($name == '' || $email == ''))
{
	$showDownloadForm = true;
}
else
{
	$showDownloadForm = false;
}
?>
<div id="edocman-documents" class="clearfix columnlayouts">
    <div class="<?php echo $rowfluidClass;?>">
	<?php
		$activeItemid = $Itemid;
		$j = 0;
		$documentImagePath              = JPATH_ROOT . '/media/com_edocman/document/';
        $thumbDocumentImagePath         = $documentImagePath.'/medium/';
		for ($i = 0 , $n = count($items) ;  $i < $n ; $i++)
		{
		    $j++;
			if( !empty($category))
			{
                $catId = $category->id;
			}
			else
			{
				$catId = 0;
			}
			$item = $items[$i] ;
			$Itemid = EDocmanHelperRoute::getDocumentMenuId($item->id, $catId, $activeItemid);
			if($item->image){
			    if( ! JFile::exists(JPATH_ROOT.'/media/com_edocman/document/medium/'.$item->image)){
                    $width  = 360;
                    $height = 240;
                    EDocmanHelper::resizeImage($documentImagePath . $item->image, $thumbDocumentImagePath . $item->image, $width, $height);
                }
                $imgSrc = JUri::base().'media/com_edocman/document/medium/'.$item->image ;
            }else {
                if (!isset($config->show_default_document_thumbnail) || $config->show_default_document_thumbnail) {
                    $ext = JString::strtolower(JFile::getExt($item->filename));
                    if (JFile::exists(JPATH_ROOT . '/components/com_edocman/assets/images/icons/thumbs/' . $ext . '.png')) {
                        $imgSrc = JUri::base() . 'components/com_edocman/assets/images/icons/thumbs/' . $ext . '.png';
                    } else {
                        $imgSrc = JUri::base() . 'components/com_edocman/assets/images/icons/thumbs/noimage.png';
                    }
                } else {
                    $imgSrc = '';
                }
            }
			if ($config->show_detail_in_popup)
			{
				$url = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&tmpl=component&Itemid='.$Itemid);
			}
			else
			{
				$url = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&Itemid='.$Itemid);
			}
			$downloadUrl = JRoute::_('index.php?option=com_edocman&task=document.download&id='.$item->id.'&Itemid='.$Itemid) ;
			$canDownload = $user->authorise('edocman.download', 'com_edocman.document.'.$item->id) ;
			$canEdit	= $user->authorise('core.edit',			'com_edocman.document.'.$item->id);
			$canDelete	= $user->authorise('core.delete',			'com_edocman.document.'.$item->id);
			if(!$canDelete){
				$canDelete	= $user->authorise('edocman.deleteown',	'com_edocman.document.'.$item->id) && $item->created_user_id == $userId;
			}
			$canCheckin	= $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn	= $user->authorise('core.edit.own',		'com_edocman.document.'.$item->id) && $item->created_user_id == $userId;
			$canChange	= $user->authorise('core.edit.state',	'com_edocman.document.'.$item->id) && $canCheckin;
			$canDownload = ($item->created_user_id == $userId) || ($item->user_ids == "" && ($canDownload || $canEdit || $canEditOwn)) || ($item->user_ids && in_array($userId, explode(',', $item->user_ids))) ;
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
			?>
			<div class="edocman-document <?php echo $spanClass; ?>">
				<?php
				if($imgSrc != ''){
				?>
                <div class="property-item table-list">
                    <div class="table-cell">
                        <div class="figure-block">
                            <figure class="item-thumb">
                                <?php
                                if($item->indicators != '' || !empty($item->new_indicator))
                                {
                                    $indicators = explode(',', $item->indicators);
                                    ?>

									<?php
                                    if(in_array('featured', $indicators))
                                    {
                                        ?>
                                        <span class="label-featured label label-success">
												<?php echo JText::_('EDOCMAN_FEATURED');?>
										</span>
                                        <?php
                                    }
                                    ?>
                                    <div class="label-wrap label-right hide-on-list">
                                        <?php
                                        if (!empty($item->new_indicator))
                                        {
                                            ?>
                                            <span class="label-status label-status-7 label label-default">
                                                <?php echo JText::_('EDOCMAN_NEW');?>
                                            </span>
                                            <?php
                                        }
                                        if(in_array('hot', $indicators))
                                        {
                                            ?>
                                            <span class="label label-default label-color-201">
                                                <?php echo JText::_('EDOCMAN_HOT');?>
                                            </span>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                    <?php
                                }
                                ?>
                                <div class="price hide-on-list">
                                    <span class="item-price">
                                        <?php
                                        if($item->category->id == 0){
                                            $category = EDocmanHelper::getDocumentCategory($item->id);
                                            $category_id = $category->id;
                                        }else{
                                            $category_id = $item->category->id;
                                        }
                                        $category_url = EDocmanHelperRoute::getCategoryRoute($category_id);
                                        if($item->category->title == ""){
                                            $category_title = $category->title;
                                        }else{
                                            $category_title = $item->category->title;
                                        }
                                        ?>
                                        <a href="<?php echo JRoute::_($category_url)?>" title="<?php echo $category_title;?>">
                                            <?php echo $category_title;?>
                                        </a>
                                    </span>
                                </div>
                                <?php
                                if ($config->use_download_link_instead_of_detail_link && $canDownload && ($accept_license == 0))
                                {
                                    if ($showDownloadForm)
                                    {
                                        ?>
                                        <div class="tg-featureditem">
                                            <figure class="tg-img-holder">
                                                <img src="<?php echo $imgSrc; ?>" alt="image description">
                                                <figcaption>
                                                    <div class="tg-featureditemcontent">
                                                        <div class="tg-border-heading">
                                                            <h3><a data-toggle="modal" data-document-title="<?php echo $item->title; ?>" title="<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>"  id="<?php echo $item->id; ?>" class="hover-effect email-popup edocman-document-title-link" href="#form-content"><i class="edicon edicon-link"></i></a></h3>
                                                        </div>
                                                    </div>
                                                </figcaption>
                                            </figure>
                                        </div>
                                        <!--
                                        <a data-toggle="modal" data-document-title="<?php echo $item->title; ?>" title="<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>"  id="<?php echo $item->id; ?>" class="hover-effect email-popup edocman-document-title-link" href="#form-content">
                                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="attachment-houzez-property-thumb-image size-houzez-property-thumb-image wp-post-image" />
                                        </a>
                                        -->
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <div class="tg-featureditem">
                                            <figure class="tg-img-holder">
                                                <img src="<?php echo $imgSrc; ?>" alt="image description">
                                                <figcaption>
                                                    <div class="tg-featureditemcontent">
                                                        <div class="tg-border-heading">
                                                            <h3><a href="<?php echo $downloadUrl; ?>" class="hover-effect" <?php echo $popup_img; ?>><i class="edicon edicon-link"></i></a></h3>
                                                        </div>
                                                    </div>
                                                </figcaption>
                                            </figure>
                                        </div>
                                        <!--
                                        <a href="<?php echo $downloadUrl; ?>" class="hover-effect" <?php echo $popup_img; ?>>
                                            <img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="attachment-houzez-property-thumb-image size-houzez-property-thumb-image wp-post-image" />
                                        </a>

                                        -->
                                        <?php
                                    }
                                }
                                else
                                {
                                    ?>
                                    <div class="tg-featureditem">
                                        <figure class="tg-img-holder">
                                            <img src="<?php echo $imgSrc; ?>" alt="image description">
                                            <figcaption>
                                                <div class="tg-featureditemcontent">
                                                    <div class="tg-border-heading">
                                                        <h3>
															<a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>" <?php echo $popup_img; ?>>
																<i class="edicon edicon-link"></i>
															</a>
														</h3>
                                                    </div>
                                                </div>
                                            </figcaption>
                                        </figure>
                                    </div>
                                    <!--
                                    <a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>" <?php echo $popup_img; ?>>
                                        <img src="<?php echo $imgSrc; ?>" alt="<?php echo $item->title; ?>" class="attachment-houzez-property-thumb-image size-houzez-property-thumb-image wp-post-image" />
                                    </a>
                                    -->
                                    <?php
                                }
                                ?>
                            </figure>
                        </div>
                    </div>
                </div>
				<?php } ?>
                <div class="item-body table-cell">
                    <div class="body-left table-cell">
                        <div class="info-row">
                            <h2 class="property-title">
                                <?php
                                if ($config->use_download_link_instead_of_detail_link && $canDownload && ($accept_license == 0))
                                {
                                    if ($showDownloadForm)
                                    {
                                        ?>
                                        <a data-toggle="modal" data-document-title="<?php echo $item->title; ?>" title="<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>"  id="<?php echo $item->id; ?>" class="email-popup edocman-document-title-link" href="#form-content">
                                            <?php echo $item->title; ?>
                                        </a>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <a href="<?php echo $downloadUrl; ?>" title="<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>" class="edocman-document-title-link" target="<?php echo $target;?>"><?php echo $item->title; ?></a>
                                        <?php
                                    }
                                }
                                else
                                {
                                    ?>
                                    <a href="<?php echo $url; ?>" title="<?php echo $item->title; ?>" <?php echo $popup_img; ?>><?php echo $item->title; ?></a>
                                    <?php
                                }
                                ?>
                            </h2>
                            <span class="edocman-description-column">
                                <?php
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
                            </span>
                        </div>
                    </div>
                </div>
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
                                <a data-toggle="modal" class="<?php echo $btnClass; ?>" href="#login-form">
                                    <i class="edicon edicon-user"></i>
                                    <?php
                                    echo JText::_('EDOCMAN_LOGIN_TO_DOWNLOAD'); ?>
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
                                    <a class="<?php echo $btnClass; ?> btn-warning" href="<?php echo $viewUrl; ?>"
                                       target="_blank">
                                        <i class="edicon edicon-eye"></i>
                                        <?php echo JText::_('EDOCMAN_VIEW'); ?>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        /*
                        if ($config->hide_details_button !== '1')
                        {
                            ?>
                            <li>
                                <a href="<?php echo $url; ?>" <?php echo $popup; ?>>
                                    <i class="edicon edicon-info"></i>
                                    <?php echo JText::_('EDOCMAN_DETAILS'); ?>
                                </a>
                            </li>
                            <?php
                        }
                        */
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
                <?php
                if( $config->show_creation_user || $config->show_publish_date){
                ?>
                    <div class="item-foot date hide-on-list">
                        <?php
                        $created_user_id = $item->created_user_id;
                        if ($config->show_creation_user  && (int)$created_user_id > 0)
                        {
                            ?>
                            <div class="item-foot-left">
                                <p class="prop-user-agent"><i class="edicon edicon-user"></i>
                                    &nbsp;
                                    <?php
                                    $created_user = JFactory::getUser($created_user_id);
                                    echo $created_user->name;
                                    ?>
                                </p>
                            </div>
                            <?php
                        }
                        if ($config->show_publish_date) {
                            ?>
                            <div class="item-foot-right">
                                <p class="prop-date"><i class="edicon edicon-calendar"></i>
                                    &nbsp;
                                    <?php
                                    echo JHtml::_('date', $item->created_time, $config->date_format, null);
                                    ?>
                                </p>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                <?php } ?>
			</div>
		    <?php
			if ($j == $nrepeat)
			{
			    $j = 0;
			    ?>
				</div>
                <div class="<?php echo $rowfluidClass?>">
			    <?php
			}
		}
	?>
    </div>
</div>

<?php
if ($showDownloadForm)
{
	echo EDocmanHelperHtml::loadCommonLayout('common/modal.php', array('bootstrapHelper' => $bootstrapHelper));
}

if ($config->login_to_download && (int)$userId == 0){
    echo EDocmanHelperHtml::loadCommonLayout('common/login.php', array('bootstrapHelper' => $bootstrapHelper));
}
?>