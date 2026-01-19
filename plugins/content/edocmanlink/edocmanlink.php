<?php
/**
 * @package     Edocman
 * @copyright   Copyright (C) 2010 - 2015 Ossolution
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomdonation.com
 */

 // Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');

class PlgContentEdocmanlink extends JPlugin
{
    /**
     * A reused view instance to render templates
     *
     */

    public function __construct( &$subject, $params )
    {
        parent::__construct( $subject, $params );
        $this->loadLanguage();
    }

	private function _replaceDocumentlink($matches){
		$db						= JFactory::getDbo();
		static $loadmodal;
        JHtml::_('behavior.modal', 'a.edocman-modal');
		$lang					= JFactory::getLanguage();
		$tag					= $lang->getTag();
		if (!$tag) $tag			= 'en-GB';
		$lang->load('com_edocman', JPATH_ROOT, $tag);
        jimport('joomla.filesystem.file');
		include_once(JPATH_ROOT.'/components/com_edocman/helper/helper.php');
        include_once(JPATH_ROOT.'/components/com_edocman/helper/html.php');
		include_once(JPATH_ROOT.'/components/com_edocman/helper/file.class.php');
		include_once(JPATH_ROOT.'/components/com_edocman/helper/bootstrap.php');
		include_once(JPATH_ROOT.'/components/com_edocman/helper/route.php');
		$config					= EdocmanHelper::getConfig();
		$user					= JFactory::getUser();
		$userId					= $user->get('id', 0);
		$session				= JFactory::getSession();
		$name					= $session->get('name','');
		$email					= $session->get('email','');
		if ($config->collect_downloader_information && !$userId && ($name == '' || $email == ''))
		{
			$showDownloadForm	= true;
		}
		else
		{
			$showDownloadForm	= false;
		}



		$bootstrapHelper		= new EDocmanHelperBootstrap($config->twitter_bootstrap_version);

		
       // echo $parameters;die();
        if($matches == ""){
        	return "";
        }else{
			$id = $matches;
			if (!EdocmanHelper::canAccessDocument($id))
			{
				if (!$user->id)
				{
					return '';
				}
				else
				{
					return '';
				}	
			}
			$db->setQuery("Select * from #__edocman_documents where id = '".(int)$id."'");
			$item = $db->loadObject();
			if (!$item->id)
			{
				return '';
			}
			
			$viewLevels			= $user->getAuthorisedViewLevels();
			$canDownload		= $user->authorise('edocman.download', 'com_edocman.document.'.$id) ;
			$canEdit			= $user->authorise('core.edit',			'com_edocman.document.'.$item->id);
			$canDownload		= ($item->created_user_id == $userId) || ($item->user_ids =="" && ($canDownload || $canEdit) || ($item->user_ids != '' && in_array($userId, explode(',', $item->user_ids)))) ;
			$accept_license = 0;
			if(($config->accept_license) && ($item->license_id > 0 || EdocmanHelper::getDefaultLicense() > 0)){
				$accept_license = 1;
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
			if (($showDownloadForm) && !$loadmodal)
			{
				echo EDocmanHelperHtml::loadCommonLayout('common/modal.php', array('bootstrapHelper' => $bootstrapHelper));
				$loadmodal = true;
			}
			if ($this->params->get('show_size')) {
				$itemdata = new EDocman_File($item->id,$item->filename, $config->documents_path);
				$itemsize = "&nbsp;(".$itemdata->size.")";
			}else{
				$itemsize = "";
			}
			if ($this->params->get('btnclass')) {
				$btnClass = $this->params->get('btnclass');
			}
			$icon = "";
			if ($this->params->get('icon')) {
				$icon = "<i class='".$this->params->get('icon')."'></i>";
			}

			$function   = $this->params->get('button_function');

			ob_start();
			if($function == 0) { //download
                if ($canDownload && $config->hide_download_button !== '1' && $accept_license == 0) {
                    $itemid = EDocmanHelperRoute::getDocumentMenuId($id);
                    $downloadUrl = JRoute::_('index.php?option=com_edocman&task=document.download&id=' . $id . '&Itemid=' . $itemid);
                    if ($showDownloadForm) {
                        ?>
                        <a data-toggle="modal" data-document-title="<?php echo $item->title; ?>"
                           title="<?php echo JText::_('EDOCMAN_DOWNLOAD'); ?>" id="<?php echo $item->id; ?>"
                           class="email-popup edocman-document-title-link <?php echo $btnClass; ?>"
                           href="#form-content">
                            <?php echo $icon; ?>
                            <?php echo JText::_('EDOCMAN_DOWNLOAD'); ?><?php echo $itemsize; ?>
                        </a>
                        <?php
                    } elseif ($item->document_url == "") {
                        ?>
                        <a href="<?php echo $downloadUrl; ?>" class="<?php echo $btnClass; ?>">
                            <?php echo $icon; ?>
                            <?php echo JText::_('EDOCMAN_DOWNLOAD'); ?><?php echo $itemsize; ?>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="<?php echo $downloadUrl; ?>" class="<?php echo $btnClass; ?>"
                           target="<?php echo $target; ?>">
                            <?php echo $icon; ?>
                            <?php echo JText::_('EDOCMAN_DOWNLOAD'); ?><?php echo $itemsize; ?>
                        </a>
                        <?php
                    }
                } elseif ($canDownload && $config->hide_download_button !== '1' && $accept_license == 1) {
                    $Itemid = EDocmanHelperRoute::getDocumentMenuId($item->id);
                    $url = JRoute::_('index.php?option=com_edocman&view=document&id=' . $item->id . '&Itemid=' . $Itemid);
                    ?>
                    <a href="<?php echo $url; ?>" class="<?php echo $btnClass; ?>">
                        <?php echo $icon; ?>
                        <?php echo JText::_('EDOCMAN_DOWNLOAD'); ?><?php echo $itemsize; ?>
                    </a>
                    <?php
                }
            }else{ //view
                if ($canDownload && EDocmanHelper::canView($item) == 1)
                {
                    $itemid = EDocmanHelperRoute::getDocumentMenuId($id);
                    $playextension = array('mp4','flv','mp3','ogg','ogv');
                    $audio_array = array('mp3','ogg');
                    $frame_array = array('flv');
                    $ext = \Joomla\String\StringHelper::strtolower(EDocmanHelper::getFileExtension($item)) ;
                    if(in_array($ext,$playextension) && !EDocmanHelper::isDropBoxTurnedOn() && !EDocmanHelper::isAmazonS3TurnedOn()) {
                        $viewUrl = JUri::root() . "index.php?option=com_edocman&view=play&id=" . $item->id . "&tmpl=component";
                        if (in_array($ext, $audio_array)) {
                            $audio_player = "rel=\"{handler: 'iframe', size: {x: 300, y: 50}, iframeOptions: {scrolling: 'no'}}\"";
                        } elseif (in_array($ext, $frame_array)) {
                            $audio_player = "rel=\"{handler: 'iframe', size: {x: 450, y: 300}, iframeOptions: {scrolling: 'no'}}\"";
                        } else {
                            $audio_player = "";
                        }
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
                    }else{
                        $viewUrl = JRoute::_('index.php?option=com_edocman&task=document.viewdoc&id=' . $item->id . '&Itemid=' . $itemid);
                        ?>
                        <a href="<?php echo $viewUrl; ?>" target="_blank" class="<?php echo $btnClass; ?>">
                            <i class="edicon edicon-eye"></i>
                            <?php echo JText::_('EDOCMAN_VIEW'); ?>
                        </a>
                        <?php
                    }
                }
            }
			$download_button = ob_get_contents();
			ob_end_clean();
		}
		return $download_button;
	}

    function onContentPrepare($context, &$article, & $params, $limitstart){
        if ($context == 'com_finder.indexer') {
            return;
        }

        $app = JFactory::getApplication();
        if ($app->getName() != 'site') {
            return true;
        }
        if (strpos($article->text, 'edocmanlink') === false) {
            return true;
        }
        $regex = "#{edocmanlink (.*)}#";
		preg_match_all($regex, $article->text, $matches);
		$matches = $matches[1];
		foreach($matches as $match){
			$pattern = "{edocmanlink ".$match."}";
			$text = $this->_replaceDocumentlink($match);
			$article->text = str_replace("{edocmanlink ".$match."}",$text,$article->text);
		}

        //$article->text = preg_replace_callback($regex, array(&$this, '_replaceDocumentlink'), $article->text);
    }
}
