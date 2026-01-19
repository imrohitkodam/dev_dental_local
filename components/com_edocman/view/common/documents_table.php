<?php
/**
 * @version   	   1.11.1
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011-2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
$session		= JFactory::getSession();
if(!$config->onetime_collect){
	$session->set('name','');
	$session->set('email','');
}
$name			= $session->get('name','');
$email			= $session->get('email','');
$btnClass		= $bootstrapHelper->getClassMapping('btn');
$show_category	= JFactory::getApplication()->input->getInt('show_category',0);
if ($config->show_detail_in_popup)
{
	JHtml::_('behavior.modal', 'edocman-modal');
	$popup = 'class="edocman-modal" rel="{handler: \'iframe\', size: {x: 800, y: 500}}"';
}
else
{
	$popup = '';
}
$user  = JFactory::getUser() ;
$userId = $user->id ;

if ($config->collect_downloader_information && !$userId && ($name == '' || $email == ''))
{
	$showDownloadForm = true;
}
else
{
	$showDownloadForm = false;
}
?>
<table class="table-condensed table-document" id="table-document">
	<?php
	if($config->show_tablelayoutheader){
	?>
	<thead>
		<tr>
            <th class="edocman-document-icon-col">
            </th>
			<th class="edocman-document-title-col">
				<?php echo JText::_('EDOCMAN_TITLE'); ?>
			</th>
			<?php
			if($show_category == 1)
			{
                ?>
                <th class="edocman-document-category-col">
                    <?php echo JText::_('EDOCMAN_CATEGORY'); ?>
                </th>
                <?php
			}
            if($config->show_tablelayoutdescription)
            {
                ?>
                <th class="edocman-document-desc-col">
                    <?php echo JText::_('EDOCMAN_DESCRIPTION'); ?>
                </th>
                <?php
            }
            ?>
			<th class="edocman-table-download-col alignright">
				<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>
			</th>
		</tr>
	</thead>
	<?php
	}
	?>
	<tbody>
	<?php
	$total = 0 ;
	$activeItemid = $Itemid;
	for ($i = 0 , $n = count($items) ; $i < $n; $i++)
	{
		$catId                  = $categoryId;
		$category               = EdocmanHelper::getCategory($catId);
		$item                   = $items[$i] ;
		$Itemid                 = EDocmanHelperRoute::getDocumentMenuId($item->id, $catId, $activeItemid);
		if ($config->show_detail_in_popup)
		{
			$url                = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&tmpl=component&Itemid='.$Itemid);
		}
		else
		{
			$url                = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&Itemid='.$Itemid);
		}
		$canEdit	            = $user->authorise('core.edit',			'com_edocman.document.'.$item->id);
		$canEditOwn	            = $user->authorise('core.edit.own',		'com_edocman.document.'.$item->id) && $item->created_user_id == $userId;
		$canDownload            = $user->authorise('edocman.download', 'com_edocman.document.'.$item->id) ;
		$canDownload            = ($item->created_user_id == $userId) || ($item->user_ids == "" && ($canDownload || $canEdit || $canEditOwn)) || ($item->user_ids && in_array($userId, explode(',', $item->user_ids))) ;
		$hide_download_button   = $config->hide_download_button;
		$hide_download          = $category->hide_download;
		if((int)$hide_download == 1)
		{
			$hide_download_button = 1;
		}
		elseif((int)$hide_download == 2)
        {
			$hide_download_button = 0;
		}
		if ($hide_download_button != 1)
		{
			$downloadUrl        = JRoute::_('index.php?option=com_edocman&task=document.download&id='.$item->id.'&Itemid='.$Itemid) ;
		}
		else
		{
			$downloadUrl        = JRoute::_('index.php?option=com_edocman&task=document.viewdoc&id='.$item->id.'&Itemid='.$Itemid) ;
		}
		if ($config->category_table_show_filetype || $config->category_table_show_filesize)
		{
			require_once JPATH_ROOT.'/components/com_edocman/helper/file.class.php' ;
			$item->data         = new EDocman_File($item->id,$item->filename, $config->documents_path) ;
		}
		$fileName               = $item->filename;
		if($fileName != "")
		{
			$fileExt            = strtolower(JFile::getExt($fileName));
		}
		else
		{
			$fileExt            = strtolower(JFile::getExt($item->document_url));
		}

		$accept_license         = 0;
		if(($config->accept_license) && ($item->license_id > 0 || EdocmanHelper::getDefaultLicense() > 0))
		{
			$accept_license     = 1;
		}
		
		?>
		<tr>
            <td class="edocman-document-icon-td" data-label="">
                <i class="<?php echo $item->data->fileicon; ?>"></i>
            </td>
			<td class="edocman-document-title-td" data-label="">
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
						<a href="<?php echo $downloadUrl; ?>" <?php echo $popup; ?>><?php echo $item->title; ?></a>
					<?php
					}
				}
				else
				{
				?>
					<a href="<?php echo $url; ?>" <?php echo $popup; ?>><?php echo $item->title; ?></a>
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
                <div class="clearfix"></div>
                <?php
				if(($config->show_number_downloaded) && ($item->downloads > 0))
				{
					?>
					<div class="downloadinformation">
						<i class="edicon edicon-download2"></i>&nbsp;<?php echo $item->downloads?> <?php echo JText::_('EDOCMAN_DOWNLOADS');?>
					</div>
					<?php
				}
				if($config->category_table_show_filesize == 1 && trim($item->data->size) != "")
				{
					$tempArr[] = $item->data->size;
					?>
					<div class="sizeinformation">
                        <i class="edicon edicon-database"></i>&nbsp;<?php echo $item->data->size; ?>
					</div>
					<?php
				}
                if ($config->show_publish_date)
                {
                    ?>
                    <div class="dateinformation">
                        <i class="edicon edicon-calendar"></i>&nbsp;<?php echo JHtml::_('date', $item->created_time, $config->date_format, true); ?>
                    </div>
                    <?php
                }
				?>
			</td>
			<?php
			if($show_category == 1)
			{
			?>
				<td class="edocman-document-category-td hidden-phone" data-label="">
					<?php
					$category_url = EDocmanHelperRoute::getCategoryRoute($item->category->id);
					?>
					<a href="<?php echo JRoute::_($category_url)?>" title="<?php echo $item->category->title;?>">
					<?php
					echo $item->category->title;
					?>
					</a>
				</td>
			<?php
			}
            if($config->show_tablelayoutdescription)
            {
                ?>
                <td class="edocman-document-desc-col">
                    <?php
                    if (!$item->short_description)
                    {
                        $item->short_description = $item->description;
                    }
                    $description = $item->short_description;
                    $description = strip_tags($description);
                    if ((int)$config->number_words > 0)
                    {
                        $descriptionArr = explode(" ", $description);
                        if (count($descriptionArr) > (int)$config->number_words)
                        {
                            for ($d = 0; $d < (int)$config->number_words - 1; $d++)
                            {
                                echo $descriptionArr[$d] . " ";
                            }
                            echo "..";
                        }
                        else
                        {
                            echo $description;
                        }
                    }
                    else
                    {
                        echo $description;
                    }
                    ?>
                </td>
            <?php
            }
            ?>
			<td class="center edocman-table-download-col" style="text-align:right;" data-label="">
			<?php
				if($item->document_url != ""){
					if($config->external_download_link == 1){
						$target = "_blank";
					}else{
						$target = "_self";
					}
				}else{
					$target = "_self";
				}
				if ($canDownload && $hide_download_button != 1 && $accept_license == 0)
				{
					if ($showDownloadForm)
					{
						?>
							<a data-toggle="modal" data-document-title="<?php echo $item->title; ?>" class="email-popup edocman-download-link" href="#form-content" id="<?php echo $item->id; ?>" target="<?php echo $target;?>" title="<?php echo JText::_('EDOCMAN_CLICK_HERE_TO_DOWNLOAD_DOCUMENT');?>">
								<span class="edocman_download_label">
									<i class="edicon edicon-cloud-download"></i>
									<?php
									$fileName = $item->filename;
									$fileExt  = strtolower(JFile::getExt($fileName));
									echo JText::_('EDOCMAN_DOWNLOAD'); ?>
									<?php
									$tempArr = array();
									if($config->category_table_show_filetype == 1 && $item->document_url == "")
									{
										$tempArr[] = $fileExt;
									}
									if(($item->document_url == "") && (count($tempArr) > 0))
									{
									?>
									(
										<?php echo implode(", ",$tempArr);?>
									)
									<?php
									}
									else
									{
										if($config->category_table_show_filetype == 1)
										{
										?>
											(<?php echo $fileExt; ?>)
										<?php
										}
									}
									?>
								</span>
							</a>
						<?php
					}
					else
					{
					?>
						<a href="<?php echo $downloadUrl; ?>" class="edocman-download-link" target="<?php echo $target;?>">
							<span class="edocman_download_label">
								<?php
								if($item->document_url != "")
								{
									?>
										<i class="edicon edicon-link"></i>
									<?php
									echo JText::_('EDOCMAN_OPEN_DOCUMENT');
								}
								else
								{
									?>
										<i class="edicon edicon-cloud-download"></i>
									<?php
									echo JText::_('EDOCMAN_DOWNLOAD');
								}
								?>
								<?php
								$tempArr = array();
								if($config->category_table_show_filetype == 1 && $item->document_url == "")
								{
									$tempArr[] = $fileExt;
								}
								if(($item->document_url == "") && (count($tempArr) > 0))
								{
								?>
								(
									<?php echo implode(", ",$tempArr);?>
								)
								<?php 
								}
								else
								{
									if($config->category_table_show_filetype == 1)
									{
									?>
										(<?php echo $fileExt; ?>)
									<?php
									}
								}
								?>
							</span>
						</a>
					<?php
					}
				}
				elseif(($config->login_to_download) and ((int)$userId == 0) and ($hide_download_button != 1) and ($accept_license == 0))
                {
					?>
					<a data-toggle="modal" class="email-popup edocman-download-link edocman-download-btn" href="#login-form">
						<span class="edocman_download_label">
							<?php
							echo JText::_('EDOCMAN_LOGIN_TO_DOWNLOAD'); ?>
						</span>
					</a>
					<?php
				}
				elseif($canDownload && $hide_download_button != 1 && ($accept_license == 1))
                {
					?>
						<a href="<?php echo $url; ?>" class="edocman-download-link edocman-download-btn">
							<span class="edocman_download_label">
								<?php
								if($item->document_url != "")
								{
									echo JText::_('EDOCMAN_OPEN_DOCUMENT');
								}
								else
								{
									echo JText::_('EDOCMAN_DOWNLOAD');
								}
								?>
								<?php
                                if($item->document_url == "")
								{
								?>
								(
								<?php echo (($config->category_table_show_filetype == 1 && $item->document_url == "") ? $fileExt:''); ?>
								<?php
								if($config->category_table_show_filetype == 1 && $item->document_url == "" && $config->category_table_show_filesize == 1 && $item->data->size)
								{
									echo ", ";
								}
								?>
								<?php echo ($config->category_table_show_filesize == 1? $item->data->size:''); ?>
								)
								<?php }
								else{
									?>
									(<?php echo $fileExt; ?>)
									<?php
								}
								?>
							</span>
						</a>
					<?php
				}
				?>
			</td>
		</tr>
		<?php
	}
	?>
	</tbody>
</table>

<?php
if ($showDownloadForm)
{
	echo EDocmanHelperHtml::loadCommonLayout('common/modal.php', array('bootstrapHelper' => $bootstrapHelper,'config' => $config));
}

if ((! $canDownload) and ($config->login_to_download) and ((int)$userId == 0)){
	echo EDocmanHelperHtml::loadCommonLayout('common/login.php', array('bootstrapHelper' => $bootstrapHelper));
}
?>