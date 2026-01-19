<?php
/**
 * @version        1.11.3
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011-2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;

require_once JPATH_ROOT.'/components/com_edocman/helper/file.class.php' ;
$bootstrapHelper    = $this->bootstrapHelper;
$config             = $this->config;

if($this->state->get('filter_order_Dir') == "asc")
{
    $icon = '<i class="edicon edicon-circle-up"></i>';
}
else
{
    $icon = '<i class="edicon edicon-circle-down"></i>';
}
?>
<div id="edocman-category-page-default" class="edocman-container <?php echo $bootstrapHelper->getClassMapping('row-fluid'); ?>">
	<form method="post" name="edocman_form" id="edocman_form" action="<?php echo JRoute::_('index.php?option=com_edocman&view=userdocuments&Itemid='.$this->Itemid); ?>">
		<h1 class="edocman-page-heading">
			<?php echo JText::_('EDOCMAN_BOOKMARK_DOCUMENTS'); ?>
		</h1>
		<?php
			if (count($this->items))
			{
			?>
				<table class="table-condensed table-document" id="table-document">
					<thead>
						<tr>
                            <th class="edocman-document-icon-col">
                            </th>
                            <th class="edocman-document-title-col">
                                <?php echo JText::_('EDOCMAN_TITLE'); ?>
                                <?php
                                if($this->state->get('filter_order') == "tbl.title")
                                {
                                    echo $icon;
			                    }
                                ?>
							</th>
                            <th class="edocman-table-download-col aligncenter">
                                <?php
                                echo JText::_('EDOCMAN_DELETE');
                                ?>
                            </th>
						</tr>
					</thead>
					<tbody>
						<?php
						$total = 0 ;
						$activeItemid = $this->defaultItemid;
						$catId = 0;
						for ($i = 0 , $n = count($this->items) ; $i < $n; $i++)
						{
							$catId = $categoryId;
							$item = $this->items[$i] ;
							$item->data = new EDocman_File($item->id,$item->filename, $this->path);
							$Itemid = EDocmanHelperRoute::getDocumentMenuId($item->id, $catId, $activeItemid);
							$url = JRoute::_('index.php?option=com_edocman&view=document&id='.$item->id.'&catid='.$catId.'&Itemid='.$Itemid);
							?>
							<tr>
                                <td class="edocman-document-icon-td hidden-phone" data-label="">
                                    <i class="<?php echo $item->data->fileicon; ?>"></i>
                                </td>
								<td data-label="<?php echo JText::_('EDOCMAN_TITLE'); ?>" class="edocman-document-title-td">
									<a href="<?php echo $url; ?>" target="_blank"><?php echo $item->title; ?></a>
									<?php
									if(($item->publish_down != "") && ($item->publish_down != "0000-00-00 00:00:00") && (strtotime($item->publish_down) < time()))
									{
										?>
										<span class="expired"><?php echo JText::_('EDOCMAN_EXPIRED');?></span>
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
                                            <i class="edicon edicon-calendar"></i>&nbsp;<?php echo JHtml::_('date', $item->created_time, $config->date_format, null); ?>
                                        </div>
                                        <?php
                                    }
                                    ?>
								</td>
								<td class="center edocman-table-download-col" data-label="<?php echo JText::_('EDOCMAN_DELETE'); ?>" style="text-align:center;">
									<a class="edocman-download-link" href="javascript:deleteConfirm(<?php echo $item->id; ?>);">
										<i class="edicon edicon-bin white"></i>
									</a>
								</td>
							</tr>
						<?php
						}
						?>
					</tbody>
				</table>
			<?php
			}
			else
			{
				echo JText::_('EDOCMAN_NO_DOCUMENT_IN_BOOKMARK_LIST');
			}
			if ($this->pagination->total > $this->pagination->limit)
			{
				?>
				<div class="pagination">
					<?php echo $this->pagination->getPagesLinks(); ?>
				</div>
			<?php
			}

		?>
		<input type="hidden" name="cid[]" value="0" id="document_id" />
		<input type="hidden" name="category_id" value="0" />
        <input type="hidden" name="view" value="bookmark" />
		<input type="hidden" name="task" value="" />
        <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
        <input type="hidden" name="filter_order" id="filter_order" value="<?php echo $this->state->get('filter_order');?>" />
        <input type="hidden" name="filter_order_Dir" id="filter_order_Dir" value="<?php echo $this->state->get('filter_order_Dir');?>" />
		<?php echo JHtml::_('form.token'); ?>

		<script type="text/javascript">
			function deleteConfirm(id)
			{
				var msg = "<?php echo JText::_('EDOCMAN_DELETE_CONFIRM'); ?>";
				if (confirm(msg))
				{
					var form = document.edocman_form ;
					form.task.value = 'document.removebookmark';
					document.getElementById('document_id').value = id;
					form.submit();
				}
			}
			function sortItems(col)
            {
                var filter_order = document.getElementById('filter_order');
                var filter_order_Dir = document.getElementById('filter_order_Dir');
                if (col == filter_order.value)
                {
                    if(filter_order_Dir.value == "asc")
                    {
                        filter_order_Dir.value = "desc";
                    }
                    else
                    {
                        filter_order_Dir.value = "asc";
                    }
                }
                else
                {
                    filter_order.value = col;
                    filter_order_Dir.value = "asc"
                }
                var form = document.edocman_form ;
                form.submit();
            }
		</script>
	</form>
</div>