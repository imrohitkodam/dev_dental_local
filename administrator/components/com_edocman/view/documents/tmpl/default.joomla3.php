<?php
/**
 * @version        1.11.6
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2019 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('formbehavior.chosen', 'select');
$user	        = JFactory::getUser();
$userId	        = $user->get('id');
$listOrder	    = $this->state->filter_order;
$listDirn	    = $this->state->filter_order_Dir;
$saveOrder	    = $listOrder == 'tbl.ordering';
$config 	    = $this->config;

$toolbar        = JToolBar::getInstance('toolbar');
//JToolbarHelper::custom('copydocument', 'copy', 'copy', JText::_('EDOCMAN_COPY'), true);
$layout         = new JLayoutFile('joomla.toolbar.popup');
// Render the popup button
$dhtml          = $layout->render(array('name' => 'movingcategory', 'text' => JText::_('EDOCMAN_MOVE'), 'class' => 'icon-move'));
$toolbar->appendButton('Custom', $dhtml);
$dhtml          = $layout->render(array('name' => 'movingcategory1', 'text' => JText::_('EDOCMAN_MOVE_ADD_CAT'), 'class' => 'icon-list'));
$toolbar->appendButton('Custom', $dhtml);

// Add a batch button
if ($user->authorise('core.create', 'com_edocman')
    && $user->authorise('core.edit', 'com_edocman')
    && $user->authorise('core.edit.state', 'com_edocman'))
{
    $title = JText::_('JTOOLBAR_BATCH');

    // Instantiate a new JLayoutFile instance and render the batch button
    $layout     = new JLayoutFile('joomla.toolbar.batch');
    $dhtml      = $layout->render(array('title' => $title));
    $toolbar->appendButton('Custom', $dhtml, 'batch');
}


if ($saveOrder)
{
	$saveOrderingUrl = 'index.php?option=com_edocman&task=document.save_order_ajax';
	JHtml::_('sortablelist.sortable', 'documentList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}

$customOptions = array(
	'filtersHidden'       => true,
	'defaultLimit'        => JFactory::getApplication()->get('list_limit', 20),
	'searchFieldSelector' => '#filter_search',
	'orderFieldSelector'  => '#filter_full_ordering'
);
JHtml::_('searchtools.form', '#adminForm', $customOptions);
?>

<script type="text/javascript">
    /*
	Joomla.submitbutton = function(task)
	{

		if (task == 'add') 
		{			
			var form = document.adminForm ;
			if (form.filter_category_id.value == 0)
			{
				alert("<?php echo JText::_('EDOCMAN_CHOOSE_CAT_TO_ADD_DOCUMENTS'); ?>");
			}
		}
	}
	*/
</script>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=documents'); ?>" method="post" name="adminForm" id="adminForm">
	
	<div class="row-fluid">
		<div id="filter-bar" class="btn-toolbar">
			<div class="filter-search btn-group pull-left">
				<label for="filter_search" class="element-invisible"><?php echo JText::_('EDOCMAN_FILTER_SEARCH_DOCUMENTS_DESC');?></label>
				<input type="text" name="filter_search" id="filter_search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo $this->escape($this->state->filter_search); ?>" class="hasTooltip" title="<?php echo JHtml::tooltipText('EDOCMAN_FILTER_SEARCH_DOCUMENTS_DESC'); ?>" />
			</div>
			<div class="btn-group pull-left">
				<button type="submit" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>"><span class="icon-search"></span></button>
				<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('filter_search').value='';this.form.submit();"><span class="icon-remove"></span></button>
			</div>
			<div class="btn-group pull-right hidden-phone">
				<?php
                    echo $this->lists['creator'];
					echo $this->lists['filter_category_id'];
					if ($this->config->activate_multilingual_feature)
					{
						echo $this->lists['filter_language'];
					}
					echo $this->lists['filter_state'];
					echo $this->lists['filter_orphan_state'];
					echo $this->pagination->getLimitBox();
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<table class="adminlist table table-striped" id="documentList">
			<thead>
				<tr>
					<th width="2%" class="nowrap center hidden-phone">
						<?php echo JHtml::_('searchtools.sort', '', 'tbl.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
					</th>
					<th width="2%" class="center">
						<input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" />
					</th>
					<td width="1%">

					</td>
					<th style="text-align: left;" width="25%">
						<?php echo JHtml::_('searchtools.sort',  'EDOCMAN_TITLE', 'tbl.title', $listDirn, $listOrder); ?>
					</th>
					<th width="13%" style="text-align: left;">
						<?php echo JHtml::_('searchtools.sort',  'EDOCMAN_CATEGORY', 'cat.title', $listDirn, $listOrder); ?>
					</th>
					<?php
						if ($this->config->show_uploader_name_in_document_mamangement)
						{
						?>
							<th width="5%">
								<?php echo JHtml::_('searchtools.sort',  'EDOCMAN_UPLOADER', 'uc.username', $listDirn, $listOrder); ?>
							</th>
						<?php
						}
					?>
					<th width="5%">
						<?php echo JHtml::_('searchtools.sort',  'JPUBLISHED', 'tbl.published', $listDirn, $listOrder); ?>
					</th>
					<th width="4%">
						<?php echo JHtml::_('searchtools.sort',  'EDOCMAN_HITS', 'hits', $listDirn, $listOrder); ?>
					</th>
					<th width="4%">
						<?php echo JHtml::_('searchtools.sort',  'EDOCMAN_DOWNLOADS', 'downloads', $listDirn, $listOrder); ?>
					</th>
					<th width="7%" class="center">
						<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
					</th>
					<?php
					if($this->indexer == 1){
						?>
						<th width="10%" class="center">
							<?php echo JText::_('EDOCMAN_INDEX_CONTENT'); ?>
						</th>
						<?php
					}
					?>
					<th width="1%" class="nowrap center">
						<i class="icon-download"></i>
					</th>
					<th width="1%" class="nowrap center">
						<?php echo JHtml::_('searchtools.sort',  'JGRID_HEADING_ID', 'tbl.id', $listDirn, $listOrder); ?>
					</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<?php
						if ($this->config->show_uploader_name_in_document_mamangement)
						{
							$cols = 12;
						}
						else
						{
							$cols = 11;
						}
						if($this->indexer == 1){
							$cols++;
						}
					?>
					<td colspan="<?php echo $cols; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
					</td>
				</tr>
			</tfoot>
			<tbody>
			<?php
			require_once JPATH_ROOT.'/components/com_edocman/helper/file.class.php' ;
			foreach ($this->items as $i => $item) :
				$ordering	= ($listOrder == 'tbl.ordering');
				$canEdit	= $user->authorise('core.edit',			'com_edocman.document.'.$item->id);
				$canCheckin	= $user->authorise('core.admin', 		'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn	= $user->authorise('core.edit.own',		'com_edocman.document.'.$item->id) && $item->created_user_id == $userId;
				$canChange	= $user->authorise('core.edit.state',	'com_edocman.document.'.$item->id) && $canCheckin;
				$item->data = new EDocman_File($item->id,$item->filename, $config->documents_path);
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="order nowrap center hidden-phone">
						<?php
						$iconClass = '';
						if (!$canChange)
						{
							$iconClass = ' inactive';
						}
						elseif (!$saveOrder)
						{
							$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
						}
						?>
						<span class="sortable-handler<?php echo $iconClass ?>">
							<span class="icon-menu"></span>
						</span>
						<?php if ($canChange && $saveOrder) : ?>
							<input type="text" style="display:none;" name="order[]" size="5" value="<?php echo $item->ordering; ?>" />
						<?php endif; ?>
					</td>
					<td class="center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					</td>
					<td class="center" style="padding-top:12px;font-size:16px;padding-left:0px !important;padding-right:0px !important;color:#5871a9;">
						<i class="<?php echo $item->data->fileicon; ?>"></i>
					</td>
					<td style="padding-bottom:2px !important;">
						<?php if ($item->checked_out) : ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $item->editor, $item->checked_out_time, 'documents.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo JRoute::_('index.php?option=com_edocman&task=document.edit&id='.$item->id);?>">
								<?php echo $item->title; ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->title); ?>
						<?php endif; ?>
						<?php
						if(($item->publish_down != "") && ($item->publish_down != "0000-00-00 00:00:00") && (strtotime($item->publish_down) < time()))
						{
							?>
							<span class="expired"><?php echo JText::_('EDOCMAN_EXPIRED');?></span>
							<?php
						}
                        elseif($item->publish_down != "" && $item->publish_down != "0000-00-00 00:00:00")
                        {
                            $unpublished_date = EDocmanHelper::calculateExpiredDates($item->publish_down);
                            if($unpublished_date != "")
                            {
                                ?>
                                <span class="expired"><?php printf(JText::_('EDOCMAN_EXPIRE_IN'),$unpublished_date);?></span>
                                <?php
                            }
                        }
						if(!EDocmanHelper::isDropBoxTurnedOn() && !EDocmanHelper::isGoogleDrivePluginEnabled() && !EDocmanHelper::isAmazonS3TurnedOn())
						{
                            if(!file_exists($config->documents_path.'/'.$item->filename))
                            {
                                ?>
                                <span class="filemissing"><?php echo JText::_('EDOCMAN_FILE_MISSING');?></span>
                                <?php
                            }
                        }
						?>
						<p class="smallsub" style="color:grey;margin:0px !important;font-size:11px;">
							<?php echo JText::sprintf('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
						</p>
						<p class="smallsub" style="color:grey;margin:0px !important;font-size:11px;">
							<?php
							if(($item->name == "") and ($item->document_url != ""))
							{
								echo JText::_('EDOCMAN_REMOTE_PATH');
							}
							else
							{
								?>
								<?php echo $item->original_filename; ?>
								<?php
								$itemdata = new EDocman_File($item->id, $item->filename, $this->config->documents_path);
								if ($itemdata->size != "")
								{
									echo " - " . $itemdata->size;
								}
							}
							?>
						</p>
					</td>
					<td>
						<?php echo $item->category_title ; ?>
					</td>
					<?php
						if ($this->config->show_uploader_name_in_document_mamangement)
						{
						?>
							<td>
								<?php echo $item->username; ?>
							</td>
						<?php
						}
					?>
					<td class="center">
						<div class="btn-group">
							<?php echo JHtml::_('jgrid.published', $item->published, $i, 'documents.', $canChange, 'cb'); ?>
							<a class="btn btn-micro active hasTooltip" title="<?php echo JText::_('EDOCMAN_EDIT');?>" href="index.php?option=com_edocman&task=document.edit&cid[]=<?php echo $item->id;?>" data-original-title="<?php echo JText::_('EDOCMAN_EDIT');?>">
								<span class="icon-edit"></span>
							</a>
							<?php
							if($config->lock_function)
							{
								if($item->is_locked == 1)
								{
									$locked_user = JFactory::getUser($this->item->locked_by);
									$title = sprintf(JText::_('EDOCMAN_LOCKED_INFORMATION'),$locked_user->name,$item->locked_time);
									?>
									<a class="btn btn-micro active hasTooltip" title="<?php echo $title;?>">
										<span class="icon-checkedout"></span>
									</a>
									<?php
								}
								else
								{
                                    ?>
                                    <a class="btn btn-micro hasTooltip" title="<?php echo JText::_('EDOCMAN_UNLOCKED');?>">
                                        <span class="icon-checkedout" style="color:#BBB;"></span>
                                    </a>
                                    <?php
								}
							}
							?>
						</div>
					</td>
					<td class="center">
						<?php echo (int)$item->hits ; ?>
					</td>
					<td class="center">
						<?php echo (int)$item->downloads ; ?>
					</td>
					<td class="center">
						<?php
                        if($item->accesspicker == 0) {
                            echo $item->access_level;
                        }else{
                            echo EDocmanHelper::getAccessGroup(1, $item->id);
                        } ?>
					</td>
					<?php
					if($this->indexer == 1){
						?>
						<td class="center">
							<?php
							$ext = strtolower(JFile::getExt($item->filename)) ;
							if($item->indexed_content != "")
							{
								?>
								<?php
								if ($ext == 'pdf' || $ext == 'doc')
								{
									?>
									<a href="index.php?option=com_edocman&task=document.indexcontent&id=<?php echo $item->id?>" title="<?php echo JText::_('EDOCMAN_CLICK_TO_REINDEX_CONTENT');?>">
									<?php
								}
								?>
									<i class="icon-ok"></i>
								<?php
								if ($ext == 'pdf' || $ext == 'doc')
								{
									?>
									</a>
									<?php
								}
							}else{
								
								if ($ext == 'pdf' || $ext == 'doc')
								{
									?>
									<a href="index.php?option=com_edocman&task=document.indexcontent&id=<?php echo $item->id?>" title="<?php echo JText::_('EDOCMAN_CLICK_TO_INDEX_CONTENT');?>">
									<?php
								}
								?>
									<i class="icon-unpublish" style="color:<?php echo $color;?> !important;"></i>
								<?php
								if ($ext == 'pdf' || $ext == 'doc')
								{
									?>
									</a>
									<?php
								}
							}
							?>
						</td>
						<?php
					}
					?>
					<td class="center">
						<a class="btn" title="<?php echo JText::_('EDOCMAN_DOWNLOAD');?>" href="index.php?option=com_edocman&task=document.download&id=<?php echo $item->id; ?>"><i class="icon-download"></i></a>
					</td>
					<td class="center">
						<?php echo (int) $item->id; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="boxchecked" value="0" />
		<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
		<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

		<input type="hidden" id="filter_full_ordering" name="filter_full_ordering" value="" />
		<?php echo JHtml::_('form.token'); ?>
	</div>

    <div class="modal hide fade" id="modal-movingcategory">
        <div class="modal-header">
            <button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
            <h3><?php echo JText::_('EDOCMAN_MOVING_CATEGORY');?></h3>
        </div>
        <div class="modal-body">
            <?php echo JText::_('EDOCMAN_PLEASE_SELECT_CATEGORY_THAT_YOU_WANT_TO_MOVE_DOCUMENTS_TO');?>
            <BR />
            <BR />
            <?php echo $this->lists['moving_category_id']; ?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-info" type="button" onclick="javascript:submitForm();">
                <?php echo JText::_('JSUBMIT'); ?>
            </button>
            <button class="btn" type="button" data-dismiss="modal">
                <?php echo JText::_('JCANCEL'); ?>
            </button>
        </div>
    </div>
	<div class="modal hide fade" id="modal-movingcategory1">
        <div class="modal-header">
            <button type="button" role="presentation" class="close" data-dismiss="modal">x</button>
            <h3><?php echo JText::_('EDOCMAN_MOVING_ADDITIONAL_CATEGORY');?></h3>
        </div>
        <div class="modal-body">
            <?php echo JText::_('EDOCMAN_PLEASE_SELECT_ADDITIONAL_CATEGORY_THAT_YOU_WANT_TO_MOVE_DOCUMENTS_TO');?>
            <BR />
            <BR />
            <?php echo $this->lists['moving_category_id1']; ?>
        </div>
        <div class="modal-footer">
            <button class="btn btn-info" type="button" onclick="javascript:submitForm1();">
                <?php echo JText::_('JSUBMIT'); ?>
            </button>
            <button class="btn" type="button" data-dismiss="modal">
                <?php echo JText::_('JCANCEL'); ?>
            </button>
        </div>
    </div>
    <?php //Load the batch processing form if user is allowed ?>
    <?php if ($user->authorise('core.create', 'com_edocman')
        && $user->authorise('core.edit', 'com_edocman')
        && $user->authorise('core.edit.state', 'com_edocman')) : ?>
        <?php echo JHtml::_(
            'bootstrap.renderModal',
            'collapseModal',
            array(
                'title' => JText::_('EDOCMAN_BATCH_OPTIONS'),
                'footer' => $this->loadTemplate('batch_footer')
            ),
            $this->loadTemplate('batch_body')
        ); ?>
    <?php endif;?>
</form>

<script type="text/javascript">
    function submitForm(){
        var form = document.adminForm;
        form.task.value = "document.movingcategory";
        form.submit();
    }
	function submitForm1(){
        var form = document.adminForm;
        form.task.value = "document.movingcategory1";
        form.submit();
    }
</script>