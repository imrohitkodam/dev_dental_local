<?php
/**
 * @package     Edocman
 * @subpackage  Module Edocman Documents
 *
 * @copyright   Copyright (C) 2010 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="edocmandocuments<?php echo $moduleclass_sfx; ?>">
<?php		
	if (count($rows)) {
	    jimport('joomla.filesystem.file') ;
	?>
        <table class="edocman_document_list" width="100%">
            <?php
            $tabs = array('sectiontableentry1' , 'sectiontableentry2');
            $k = 0 ;
            foreach ($rows as  $row)
            {
                $row->data = new EDocman_File($row->id,$row->filename, $config->documents_path);
                $tab = $tabs[$k] ;
                $k = 1 - $k ;
                if ($row->filename)
                {
                    $ext = JFile::getExt($row->filename) ;
                }
                else
                {
                    $ext = '' ;
                }
                if ($linkType && $row->canDownload)
                {
                    if ($linkType == 2 && $row->canView)
                        $url = JRoute::_('index.php?option=com_edocman&task=document.viewdoc&id='.$row->id.'&Itemid='.$itemId) ;
                    else
                        $url = JRoute::_('index.php?option=com_edocman&task=document.download&id='.$row->id.'&Itemid='.$itemId) ;
                }
                else
                {
                    $url = JRoute::_('index.php?option=com_edocman&view=document&id='.$row->id.'&Itemid='.$itemId);
                }
                ?>
                <tr>
                    <td class="edocman_document_list_icon">
                        <i class="<?php echo $row->data->fileicon; ?>"></i>
                    </td>
                    <td class="edocman_document_list_title">
                        <a href="<?php echo $url; ?>" class="edocman_document_link" <?php echo $target;?> title="<?php echo $row->title ; ?>">
                            <?php echo $row->title ; ?>
                        </a>
                        <?php
                        if($showdownload == 1)
                        {
                            ?>
                            <div class="clearfix"></div>
                            <span class="ndownloads">
                                    <?php
                                    echo $row->downloads;
                                    ?>
                                    <?php echo JText::_('EDOCMAN_DOWNLOADS'); ?>
                                </span>
                            <?php
                        }
                        ?>
                    </td>
                    <td class="edocman_document_list_size">
                        <?php
                        if($showfilesize == 1 && trim($row->data->size) != "")
                        {
                            echo $row->data->size;
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
	<?php	
	} else {
	?>
		<div class="eb_empty"><?php echo JText::_('EDOCMAN_NO_DOCUMENTS') ?></div>
	<?php	
	}
?>
</div>