<?php
/**
 * @copyright    Copyright (C) 2011-2018 Ossolution - All rights reserved..
 * @license      GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
error_reporting(E_ALL);
class plgAcymailingEdocman extends JPlugin{
	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'edocman');
			$this->params = new acyParameter($plugin->params);
		}
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');
	}

	function acymailing_getPluginType(){

		$onePlugin = new stdClass();
		$onePlugin->name = 'Edocman';
		$onePlugin->function = 'acymailingtagedocman_show';
		$onePlugin->help = 'plugin-edocman';

		return $onePlugin;
	}

	function acymailingtagedocman_show(){
		$config = acymailing_config();
		if(version_compare('5.5.0',$config->get('version'),'gt')){
		//if($config->get('version') < '5.5.0'){
			acymailing_display('Please download and install the latest AcyMailing version otherwise this plugin will NOT work', 'error');
			return;
		}

		// Load language file
		$lang = JFactory::getLanguage();
		$lang->load('com_edocman', JPATH_ADMINISTRATOR);

		$app = JFactory::getApplication();

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		require_once(JPATH_ROOT.'/components/com_edocman/helper/helper.php');

		$paramBase = ACYMAILING_COMPONENT.'.edocman';
		$pageInfo->filter->order->value = $app->getUserStateFromRequest($paramBase.".filter_order", 'filter_order', 'a.id', 'cmd');
		$pageInfo->filter->order->dir = $app->getUserStateFromRequest($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = $app->getUserStateFromRequest($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = JString::strtolower(trim($pageInfo->search));
		$pageInfo->filter_cat = $app->getUserStateFromRequest($paramBase.".filter_cat", 'filter_cat', '', 'int');
		
		$pageInfo->titlelink = $app->getUserStateFromRequest($paramBase.".titlelink", 'titlelink', 'link', 'string');
		$pageInfo->showcreator = $app->getUserStateFromRequest($paramBase.".showcreator", 'showcreator', 'showcreator', 'string');
		$pageInfo->showcreated = $app->getUserStateFromRequest($paramBase.".showcreated", 'showcreated', 'showcreated', 'showcreated');

		$pageInfo->category = $app->getUserStateFromRequest($paramBase.".category", 'category', 'category', 'string');

		$pageInfo->lang = $app->getUserStateFromRequest($paramBase.".lang", 'lang', '', 'string');
		$pageInfo->author = $app->getUserStateFromRequest($paramBase.".author", 'author', $this->params->get('default_author', '0'), 'string');
		$pageInfo->images = $app->getUserStateFromRequest($paramBase.".images", 'images', '1', 'string');
		$pageInfo->contenttype = $app->getUserStateFromRequest($paramBase.".contenttype", 'contenttype', 'intro', 'string');
		$pageInfo->limit->value = $app->getUserStateFromRequest($paramBase.'.list_limit', 'limit', $app->getCfg('list_limit'), 'int');
		$pageInfo->limit->start = $app->getUserStateFromRequest($paramBase.'.limitstart', 'limitstart', 0, 'int');
		$pageInfo->autotitlelink = $app->getUserStateFromRequest($paramBase.".autotitlelink", 'autotitlelink', 'link', 'string');
		$pageInfo->autoimages = $app->getUserStateFromRequest($paramBase.".autoimages", 'autoimages', '1', 'string');
		$pageInfo->automaxvalue = $app->getUserStateFromRequest($paramBase.".max", 'max', '20', 'int');
		$pageInfo->contentfilter = $app->getUserStateFromRequest($paramBase.".contentfilter", 'contentfilter', 'created', 'string');
		$pageInfo->contentorder = $app->getUserStateFromRequest($paramBase.".contentorder", 'contentorder', 'id', 'string');
		$pageInfo->autocontenttype = $app->getUserStateFromRequest($paramBase.".autocontenttype", 'autocontenttype', 'intro', 'string');
		$pageInfo->pict = $app->getUserStateFromRequest($paramBase.".pict", 'pict', $this->params->get('default_pict', 1), 'string');
		$pageInfo->pictheight = $app->getUserStateFromRequest($paramBase.".pictheight", 'pictheight', $this->params->get('maxheight', 150), 'string');
		$pageInfo->pictwidth = $app->getUserStateFromRequest($paramBase.".pictwidth", 'pictwidth', $this->params->get('maxwidth', 150), 'string');


		$db = JFactory::getDBO();

		$searchFields = array('a.id', 'a.title', 'a.alias', 'description', 'd.short_description');

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchFields)." LIKE $searchVal";
		}

		$my = JFactory::getUser();
		$db = JFactory::getDbo();
		$nullDate = $db->quote($db->getNullDate());
		$nowDate  = $db->quote(JFactory::getDate()->toSql());
		$filters[] = "a.published = '1'";
		$filters[] = '(a.publish_up = ' . $nullDate . ' OR a.publish_up <= ' . $nowDate . ') AND (a.publish_down = ' . $nullDate . ' OR a.publish_down >= ' . $nowDate . ')';

		$query = "SELECT SQL_CALC_FOUND_ROWS a.*,c.title as category_name,u.name as creator";
		$query .= " FROM `#__edocman_documents` as a "
				." INNER JOIN #__edocman_document_category as b on b.document_id = a.id"
				." INNER JOIN #__edocman_categories as c on c.id = b.category_id"
				." INNER JOIN #__users as u on u.id = a.created_user_id";
		$filters[] = " 1=1 ";
		$filters[] = " b.is_main_category = '1' ";

		if(!empty($pageInfo->filter_cat)) $filters[] = "b.category_id = '".$pageInfo->filter_cat."'";

		if(!empty($filters)){
			$query .= ' WHERE ('.implode(') AND (', $filters).')';
		}

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$db->setQuery($query, $pageInfo->limit->start, $pageInfo->limit->value);
		$rows = $db->loadObjectList();
		if(!empty($rows[0]) && !isset($rows[0]->acy_created)){
			$db->setQuery('ALTER TABLE `#__edocman_documents` ADD `acy_created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP');
			$db->query();
		}

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$db->setQuery('SELECT FOUND_ROWS()');
		$pageInfo->elements->total = $db->loadResult();
		$pageInfo->elements->page = count($rows);

		jimport('joomla.html.pagination');
		$pagination = new JPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);

		$type = JRequest::getString('type');

		?>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedContents = new Array();
			function applyContent(contentid, rowClass){
				var tmp = selectedContents.indexOf(contentid)
				if(tmp != -1){
					window.document.getElementById('content' + contentid).className = rowClass;
					delete selectedContents[tmp];
				}else{
					window.document.getElementById('content' + contentid).className = 'selectedrow';
					selectedContents.push(contentid);
				}
				updateTag();
			}

			function updateTag(){
				var tag = '';
				var otherinfo = '';
				var selectedtype = '';

				for(var i = 0; i < document.adminForm.contenttype.length; i++){
					if(document.adminForm.contenttype[i].checked){
						selectedtype = document.adminForm.contenttype[i].value;
						otherinfo += '|type:' + document.adminForm.contenttype[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelink.length; i++){
					if(document.adminForm.titlelink[i].checked && document.adminForm.titlelink[i].value.length > 1){
						otherinfo += '|' + document.adminForm.titlelink[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.category.length; i++){
					if(document.adminForm.category[i].checked && document.adminForm.category[i].value.length > 1){
						otherinfo += '|' + document.adminForm.category[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.showcreated.length; i++){
					if(document.adminForm.showcreated[i].checked && document.adminForm.showcreated[i].value.length > 1){
						otherinfo += '|' + document.adminForm.showcreated[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.author.length; i++){
						if(document.adminForm.author[i].checked && document.adminForm.author[i].value.length > 1){
							otherinfo += '|' + document.adminForm.author[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pict.length; i++){
						if(document.adminForm.pict[i].checked){
							otherinfo += '|pict:' + document.adminForm.pict[i].value;
							if(document.adminForm.pict[i].value == 'resized'){
								document.getElementById('pictsize').style.display = '';
								if(document.adminForm.pictwidth.value) otherinfo += '|maxwidth:' + document.adminForm.pictwidth.value;
								if(document.adminForm.pictheight.value) otherinfo += '|maxheight:' + document.adminForm.pictheight.value;
							}else{
								document.getElementById('pictsize').style.display = 'none';
							}
						}
					}
				}

				if(window.document.getElementById('jflang') && window.document.getElementById('jflang').value != ''){
					otherinfo += '|lang:';
					otherinfo += window.document.getElementById('jflang').value;
				}

				for(var i in selectedContents){
					if(selectedContents[i] && !isNaN(i)){
						tag = tag + '{edocman:' + selectedContents[i] + otherinfo + '}<br />';
					}
				}
				setTag(tag);
			}

			var selectedCat = new Array();
			function applyAuto(catid, rowClass){
				if(selectedCat[catid]){
					window.document.getElementById('cat' + catid).className = rowClass;
					delete selectedCat[catid];
				}else{
					window.document.getElementById('cat' + catid).className = 'selectedrow';
					selectedCat[catid] = 'selectedone';
				}

				updateAutoTag();
			}

			function updateAutoTag(){
				var selectedtype = '';
				
				var temptag = '';
				tag = '{autoedocman:';
				for(var i = 0; i < document.adminForm.contenttype.length; i++){
					if(document.adminForm.contenttype[i].checked){
						selectedtype = document.adminForm.contenttype[i].value;
						tag += '|type:' + document.adminForm.contenttype[i].value;
					}
				}
				for(var icat in selectedCat){
					if(selectedCat[icat] == 'selectedone'){
						temptag += icat + '-';
					}
				}
				if(temptag != ""){
					tag += "|categories:" + temptag;
				}
				if(document.adminForm.contentorder.value){
					tag += document.adminForm.contentorder.value;
				}
				if(document.adminForm.contentfilter && document.adminForm.contentfilter.value){
					tag += document.adminForm.contentfilter.value;
				}
				if(document.adminForm.meta_article && document.adminForm.meta_article.value){
					tag += '|meta:' + document.adminForm.meta_article.value;
				}
				for(var i = 0; i < document.adminForm.titlelinkauto.length; i++){
					if(document.adminForm.titlelinkauto[i].checked && document.adminForm.titlelinkauto[i].value.length > 1){
						tag += '|' + document.adminForm.titlelinkauto[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.categoryauto.length; i++){
					if(document.adminForm.categoryauto[i].checked && document.adminForm.categoryauto[i].value.length > 1){
						tag += '|' + document.adminForm.categoryauto[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.showcreatedauto.length; i++){
					if(document.adminForm.showcreatedauto[i].checked && document.adminForm.showcreatedauto[i].value.length > 1){
						tag += '|' + document.adminForm.showcreatedauto[i].value;
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.authorauto.length; i++){
						if(document.adminForm.authorauto[i].checked && document.adminForm.authorauto[i].value.length > 1){
							tag += '|' + document.adminForm.authorauto[i].value;
						}
					}
					for(var i = 0; i < document.adminForm.pictauto.length; i++){
						if(document.adminForm.pictauto[i].checked){
							tag += '|pict:' + document.adminForm.pictauto[i].value;
							if(document.adminForm.pictauto[i].value == 'resized'){
								document.getElementById('pictsizeauto').style.display = '';
								if(document.adminForm.pictwidthauto.value) tag += '|maxwidth:' + document.adminForm.pictwidthauto.value;
								if(document.adminForm.pictheightauto.value) tag += '|maxheight:' + document.adminForm.pictheightauto.value;
							}else{
								document.getElementById('pictsizeauto').style.display = 'none';
							}
						}
					}
				}
				if(document.adminForm.cols && document.adminForm.cols.value > 1){
					tag += '|cols:' + document.adminForm.cols.value;
				}
				if(window.document.getElementById('jflangauto') && window.document.getElementById('jflangauto').value != ''){
					tag += '|lang:';
					tag += window.document.getElementById('jflangauto').value;
				}

				tag += '}';
				setTag(tag);
			}
			//-->
		</script>
		<?php

		$picts = array();
		$picts[] = JHTML::_('select.option', "1", JText::_('JOOMEXT_YES'));
		$pictureHelper = acymailing_get('helper.acypict');
		if($pictureHelper->available()) $picts[] = JHTML::_('select.option', "resized", JText::_('RESIZED'));
		$picts[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		//Content type
		$contenttype = array();
		$contenttype[] = JHTML::_('select.option', "title", JText::_('TITLE_ONLY'));
		$contenttype[] = JHTML::_('select.option', "intro", JText::_('INTRO_ONLY'));
		$contenttype[] = JHTML::_('select.option', "full", JText::_('FULL_TEXT'));

		//Title link params
		$titlelink = array();
		$titlelink[] = JHTML::_('select.option', "link", JText::_('JOOMEXT_YES'));
		$titlelink[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		//Show Category params
		$categoryshow = array();
		$categoryshow[] = JHTML::_('select.option', "category", JText::_('JOOMEXT_YES'));
		$categoryshow[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		//Show Created params
		$createdshow = array();
		$createdshow[] = JHTML::_('select.option', "showcreated", JText::_('JOOMEXT_YES'));
		$createdshow[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		//Author name
		$authorname = array();
		$authorname[] = JHTML::_('select.option', "author", JText::_('JOOMEXT_YES'));
		$authorname[] = JHTML::_('select.option', "0", JText::_('JOOMEXT_NO'));

		$ordering = array();
		$ordering[] = JHTML::_('select.option', "|order:id,DESC", JText::_('ACY_ID'));
		$ordering[] = JHTML::_('select.option', "|order:created,DESC", JText::_('CREATED_DATE'));
		$ordering[] = JHTML::_('select.option', "|order:modified,DESC", JText::_('MODIFIED_DATE'));
		$ordering[] = JHTML::_('select.option', "|order:title,ASC", JText::_('FIELD_TITLE'));
		$ordering[] = JHTML::_('select.option', "|order:rand", JText::_('ACY_RANDOM'));

		$tabs = acymailing_get('helper.acytabs');
		echo $tabs->startPane('edocman_tab');
		echo $tabs->startPanel(JText::_('TAG_ELEMENTS'), 'edocman_listings');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td><?php echo JText::_('DISPLAY');?></td>
				<td colspan="2"><?php echo JHTML::_('acyselect.radiolist', $contenttype, 'contenttype', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->contenttype); ?></td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateTag();"';
					echo $jflanguages->display('lang', $pageInfo->lang); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $titlelink, 'titlelink', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->titlelink);?>
				</td>
				<td>
					<?php echo JText::_('JGLOBAL_FIELD_CREATED_BY_LABEL'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $authorname, 'author', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->author); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('Show Created Date'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $createdshow, 'showcreated', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->showcreated); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('Show Category'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $categoryshow, 'category', 'size="1" onclick="updateTag();"', 'value', 'text', (string)$pageInfo->category); ?>
				</td>
				<td valign="top">
					<?php echo JText::_('DISPLAY_PICTURES');?>
				</td>
				<td valign="top"><?php echo JHTML::_('acyselect.radiolist', $picts, 'pict', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->pict); ?>
					<span id="pictsize" <?php if($pageInfo->pict != 'resized') echo 'style="display:none;"'; ?>><br/><?php echo JText::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidth" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;"/>
					× <?php echo JText::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheight" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictheight; ?>" style="width:30px;"/>
				</span>
				</td>
				<td></td>
				<td></td>
			</tr>
		</table>
		<table>
			<tr>
				<td width="100%">
					<input placeholder="<?php echo JText::_('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $pageInfo->search;?>" class="text_area" onchange="document.adminForm.submit();"/>
					<button class="btn" onclick="this.form.submit();"><?php echo JText::_('JOOMEXT_GO'); ?></button>
					<button class="btn" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo JText::_('JOOMEXT_RESET'); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php echo $this->_categories($pageInfo->filter_cat); ?>
				</td>
			</tr>
		</table>

		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<thead>
			<tr>
				<th class="title">
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', JText::_('FIELD_TITLE'), 'a.title', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo JHTML::_('grid.sort', 'Creator', 'b.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort', JText::_('EDOCMAN_THUMBNAIL'), '', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo JHTML::_('grid.sort', JText::_('ACY_ID'), 'a.id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $pagination->getListFooter(); ?>
					<?php echo $pagination->getResultsCounter(); ?>
				</td>
			</tr>
			</tfoot>
			<tbody>
			<?php
			$k = 0;
			for($i = 0, $a = count($rows); $i < $a; $i++){
				$row =& $rows[$i];
				?>
				<tr id="content<?php echo $row->id; ?>" class="<?php echo "row$k"; ?>" onclick="applyContent(<?php echo $row->id.",'row$k'"?>);" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						$text = '<b>'.JText::_('JOOMEXT_ALIAS').' : </b>'.$row->alias;
						echo acymailing_tooltip($text, $row->title, '', $row->title);
						?>
							<BR />
							<span style="color:#0088CC;font-size:11px;">
							<strong><?php echo JText::_('EDOCMAN_CATEGORY') ?>: </strong>
							<?php
							echo $row->category_name;
							?>
							<BR />
							<?php
							echo JText::_('JGLOBAL_CREATED').": ";
							echo JHTML::_('date', $row->created_time , 'D, jS F Y');
							if(($row->modified_time != "") and ($row->modified_time != "0000-00-00") and ($row->modified_time != $row->created_time)){
								echo "<BR />";
								echo JText::_('JGLOBAL_MODIFIED').": ";
								echo JHTML::_('date', $row->modified_time , 'D, jS F Y');
							}
							?>
						</span>
					</td>
					<td>
						<?php
						if(!empty($row->creator)){
							$text = '<b>'.JText::_('ACY_NAME', true).' : </b>'.$row->creator;
							echo acymailing_tooltip($text, $row->creator, '', $row->creator);
						}else{
							echo "N.A";
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php 
						$image = $row->image;
						if($image != ''){
							if(file_exists(JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$image)){
								?>
								<IMG src="<?php echo JURI::root()?>media/com_edocman/document/thumbs/<?php echo $image;?>" class="img-polaroid" style="width:90px; max-width:none !important;" />
								<?php
							} 
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo $row->id; ?>
					</td>
				</tr>
				<?php
				$k = 1 - $k;
			}
			?>
			</tbody>
		</table>
		<input type="hidden" name="boxchecked" value="0"/>
		<input type="hidden" name="filter_order" value="<?php echo $pageInfo->filter->order->value; ?>"/>
		<input type="hidden" name="filter_order_Dir" value="<?php echo $pageInfo->filter->order->dir; ?>"/>
		<?php

		echo $tabs->endPanel();
		
		echo $tabs->startPanel(JText::_('Auto generate documents'), 'edocman_auto');
		?>
		<br style="font-size:1px"/>
		<table width="100%" class="adminform">
			<tr>
				<td>
					<?php echo JText::_('DISPLAY');?>
				</td>
				<td colspan="2">
					<?php echo JHTML::_('acyselect.radiolist', $contenttype, 'contenttypeauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_type', 'intro'));?>
				</td>
				<td>
					<?php $jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateAutoTag();"';
					$jflanguages->id = 'jflangauto';
					echo $jflanguages->display('langauto'); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $titlelink, 'titlelinkauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_titlelink', 'link'));?>
				</td>
				<td>
					<?php echo JText::_('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $authorname, 'authorauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$this->params->get('default_author', '0')); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('Show Created Date'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $createdshow, 'showcreatedauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$pageInfo->showcreated); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('Show Category'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.radiolist', $categoryshow, 'categoryauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', (string)$pageInfo->category); ?>
				</td>
				<td valign="top"><?php echo JText::_('DISPLAY_PICTURES'); ?></td>
				<td valign="top"><?php echo JHTML::_('acyselect.radiolist', $picts, 'pictauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_pict', '1')); ?>
					<span id="pictsizeauto" <?php if($this->params->get('default_pict', '1') != 'resized') echo 'style="display:none;"'; ?> ><br/><?php echo JText::_('CAPTCHA_WIDTH') ?>
						<input name="pictwidthauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxwidth', '150');?>" style="width:30px;"/>
					× <?php echo JText::_('CAPTCHA_HEIGHT') ?>
						<input name="pictheightauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxheight', '150');?>" style="width:30px;"/>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo JText::_('MAX_ARTICLE'); ?>
				</td>
				<td>
					<input type="text" name="max_article" style="width:50px" value="20" onchange="updateAutoTag();"/>
				</td>
				<td>
					<?php echo JText::_('ACY_ORDER'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.genericlist', $ordering, 'contentorder', 'size="1" style="width:150px;" onchange="updateAutoTag();"', 'value', 'text', $pageInfo->contentorder); ?>
				</td>
			</tr>
			<?php if($type == 'autonews'){ ?>
				<tr>
					<td>
						<?php echo JText::_('MIN_ARTICLE'); ?>
					</td>
					<td>
						<input type="text" name="min_article" style="width:50px" value="1" onchange="updateAutoTag();"/>
					</td>
					<td>
						<?php echo JText::_('JOOMEXT_FILTER'); ?>
					</td>
					<td>
						<?php $filter = acymailing_get('type.contentfilter');
						$filter->onclick = "updateAutoTag();";
						echo $filter->display('contentfilter', '|filter:created'); ?>
					</td>
				</tr>
			<?php } ?>
		</table>
		<h4><?php echo JText::_('EDOCMAN_CATEGORIES');?></h4>
		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<?php $k = 0;
			foreach($this->catvalues as $oneCat){
				if(empty($oneCat->value)) continue;
				?>
				<tr id="cat<?php echo $oneCat->value ?>" class="<?php echo "row$k"; ?>" onclick="applyAuto(<?php echo $oneCat->value ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $oneCat->text;
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<?php
		echo $tabs->endPanel();
		echo $tabs->endPane();
	}

	private function _categories($filter_cat){
		//select all cats
		$db = JFactory::getDBO();
		$db->setQuery('SELECT id,alias,title,parent_id FROM `#__edocman_categories` WHERE published = 1 ORDER BY `ordering` ASC');
		$mosetCats = $db->loadObjectList();
		$this->cats = array();
		foreach($mosetCats as $oneCat){
			$this->cats[$oneCat->parent_id][] = $oneCat;
		}

		$this->catvalues = array();
		$this->catvalues[] = JHTML::_('select.option', 0, JText::_('EDOCMAN_CATEGORY'));
		$this->_handleChildrens();
		return JHTML::_('select.genericlist', $this->catvalues, 'filter_cat', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'value', 'text', (int)$filter_cat);
	}

	private function _handleChildrens($parentId = 0, $level = 0){
		if(!empty($this->cats[$parentId])){
			foreach($this->cats[$parentId] as $cat){
				//$cat->name = str_repeat($this->separator,$level).$cat->cat_name;
				$this->catvalues[] = JHTML::_('select.option', $cat->id, str_repeat(" - - ", $level).$cat->title);
				$this->_handleChildrens($cat->id, $level + 1);
			}
		}
	}

	public function acymailing_replacetags(&$email, $send = true){
		$this->_replaceAuto($email);
		$this->_replaceOne($email);
	}

	private function _replaceOne(&$email){
		$lang = JFactory::getLanguage();
		$lang->load('com_edocman', JPATH_ADMINISTRATOR);
		$match = '#{edocman:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything...
		if(!$found) return;

		$this->newslanguage = new stdClass();
		if(!empty($email->language)){
			$db = JFactory::getDBO();
			$db->setQuery('SELECT lang_id, lang_code FROM #__languages WHERE sef = '.$db->quote($email->language).' LIMIT 1');
			$this->newslanguage = $db->loadObject();
		}

		//we set the current catid so it can work with several Newsletters...
		$this->currentcatid = -1;
		//Set the read more link:
		$this->readmore = '';

		//Load the K2 model file
		require_once(JPATH_ROOT.'/components/com_edocman/helper/helper.php');
		require_once(JPATH_ROOT.'/components/com_edocman/helper/route.php');

		//We will need the mailer class as well
		$this->mailerHelper = acymailing_get('helper.mailer');

		$htmlreplace = array();
		$textreplace = array();
		$subjectreplace = array();

		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				//Don't need to process twice a tag we already have!
				if(isset($htmlreplace[$oneTag])) continue;

				$content = $this->_replaceContent($allresults, $i);
				$htmlreplace[$oneTag] = $content;
				$textreplace[$oneTag] = $this->mailerHelper->textVersion($content, true);
				$subjectreplace[$oneTag] = strip_tags($content);
			}
		}

		$email->body = str_replace(array_keys($htmlreplace), $htmlreplace, $email->body);
		$email->altbody = str_replace(array_keys($textreplace), $textreplace, $email->altbody);
		$email->subject = str_replace(array_keys($subjectreplace), $subjectreplace, $email->subject);
	}

	private function _replaceContent(&$results, $i){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		//1 : Transform the tag properly...
		$arguments = explode('|', strip_tags($results[1][$i]));
		$tag = new stdClass();
		$tag->id = (int)$arguments[0];
		$tag->itemid = (int)$this->params->get('itemid');
		$tag->wrap = (int)$this->params->get('wordwrap', 0);
		for($i = 1, $a = count($arguments); $i < $a; $i++){
			$args = explode(':', $arguments[$i]);
			$arg0 = trim($args[0]);
			if(empty($arg0)) continue;
			if(isset($args[1])){
				$tag->$arg0 = $args[1];
			}else{
				$tag->$arg0 = true;
			}
		}

		//We used to call it "images" but to make it consistent with the joomlacontent plugin, we rename it into pict.
		if(isset($tag->images) && !isset($tag->pict)) $tag->pict = $tag->images;

		//2 : Load the Joomla article... with the author, the section and the categories to create nice links
		$query = 'SELECT a.*, b.name as author_name FROM `#__edocman_documents` as a inner join #__users as b on b.id = a.created_user_id';
		$query .= ' WHERE a.id = '.intval($tag->id).' LIMIT 1';

		$db->setQuery($query);
		$document = $db->loadObject();

		$result = '';

		//In case of we could not load the article for any reason...
		if(empty($document)){
			if($app->isAdmin()) $app->enqueueMessage('The document "'.$tag->id.'" could not be loaded', 'notice');
			return $result;
		}

		//We just loaded the article but we may need to translate it depending on tag->lang...
		if(empty($tag->lang) && !empty($this->newslanguage) && !empty($this->newslanguage->lang_code)) $tag->lang = $this->newslanguage->lang_code.','.$this->newslanguage->lang_id;

		$acypluginsHelper->translateItem($article, $tag, 'edocman');
		if(!empty($tag->lang)){
			//We will load the correct article in the jf tables
			$langid = (int)substr($tag->lang, strpos($tag->lang, ',') + 1);
			$suffix = "_".substr($tag->lang,0,2);
		}

		$varFields = array();
		foreach($document as $fieldName => $oneField){
			$varFields['{'.$fieldName.'}'] = $oneField;
		}

		//When we load an artice, we may have a wrong content... we clean it.
		$short_description	= $document->short_description;
		$description		= $document->description;
		$acypluginsHelper->cleanHtml($short_description);
		$acypluginsHelper->cleanHtml($description);

		$db->setQuery("Select category_id from #__edocman_document_category where document_id = '$document->id' and is_main_category = '1'");
		$catid				= $db->loadResult();

		$Itemid				= EDocmanHelperRoute::getDocumentMenuId($item->id, $catid, 0);
		//get the link
		$link		= "index.php?option=com_edocman&view=document&id=".$document->id."&Itemid=".$Itemid;

		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		//Add the title with a link or not on it.
		//If we add a link, we add in the same time a name="content-CONTENTID" so that we will be able to parse the content to create a nice summary
		$styleTitle = '';
		$styleTitleEnd = '';
		if($tag->type != "title"){
			$styleTitle = '<h2 class="acymailing_title" style="font-size:20px;font-weight:bold;">';
			$styleTitleEnd = '</h2>';
		}

		$resultTitle = '';

		if(empty($tag->notitle)){
			if(!empty($tag->link)){
				$resultTitle = '<a href="'.$link.'" ';
				if($tag->type != "title") $resultTitle .= 'style="text-decoration:none;font-style:normal !important;" name="edocman-'.$document->id.'" ';
				$resultTitle .= 'target="_blank">'.$document->title.'</a>';
			}else{
				$resultTitle = $document->title;
			}
			$resultTitle = $styleTitle.$resultTitle.$styleTitleEnd;
		}
		if(!empty($tag->showcreated)){
			$dateFormat = empty($tag->dateformat) ? JText::_('DATE_FORMAT_LC2') : $tag->dateformat;
			$result .= '<span class="createddate" style="font-size:11px;color:#AAA;font-family:Arial;">'.JHTML::_('date', $document->created_time, $dateFormat).'</span><br />';
			$varFields['{created}'] = JHTML::_('date', $document->created_time, $dateFormat);
		}

		//Add the author...
		if(!empty($tag->author)){
			$authorName = $document->author_name;
			if($tag->type == 'title') $result .= '<br />';
			$result .= '<span class="acymailing_authorname" style="font-weight:bold;">'.JText::_('OS_OWNER').': '.$authorName.'</span><br />';
			$varFields['{author}'] = $authorName;
		}

		//We add the intro text
		$desc = "";
		if($tag->type != "title"){
			if($tag->type == "intro"){
				$forceReadMore = false;
				$desc .= $acypluginsHelper->wrapText($short_description, $tag);
				if(!empty($acypluginsHelper->wraped)) $forceReadMore = true;
			}
			if(!empty($description) AND $tag->type == "text"){
				$desc .= $description;
			}

			//We add the full text
			if($tag->type == "intro"){
				//We add the read more link but only if we have a fulltext after...
				if(empty($tag->noreadmore) && (!empty($article->fulltext) OR $forceReadMore)){
					$readMoreText = empty($tag->readmore) ? $this->readmore : $tag->readmore;
				}
			}elseif(!empty($property_small_desc)){
				if($tag->type != "text" && !empty($article->introtext) && !preg_match('#^<[div|p]#i', trim($article->fulltext))) $result .= '<br />';
				$desc .= $property_small_desc;
			}
			
			if(!empty($tag->category)){
				$category = EdocmanHelper::getCategory($catid);
				$desc .= '<BR /><SPAN class="cattitle" style="font-weight:bold;">'.JText::_('EDOCMAN_CATEGORY').': '.$category->title.'</SPAN><BR />';
			}

			$desc .= '<BR /><a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore" style="font-style:normal !important;padding:10px!important;font-family:Arial;background-color:#0DBAE8 !important;">Read More</span></a>';

			$photo = $document->image;
			if ($photo != "") {
				if(file_exists(JPATH_ROOT.'/media/com_edocman/document/thumbs/'.$photo)){
					$imagePath = JURI::root().'/media/com_edocman/document/thumbs/'.$photo;

					if(empty($imagePath) || !empty($tag->removemainpict)){
						$result = $resultTitle.$result;
					}else{
						$varFields['{imagePath}'] = $imagePath;
						$imageLink = '<img src="'.$imagePath.'" alt="'.$document->title.'" />';
						if(!empty($tag->link)) $imageLink = '<a href="'.$link.'" target="_blank" style="text-decoration:none" >'.$imageLink.'</a>';
						$varFields['{imageLink}'] = $imageLink;
						$result = $resultTitle.'<table cellspacing="5" cellpadding="0" border="0" ><tr><td valign="top" width="20%">'.$imageLink.'</td><td valign="top" width="80%">'.$result.''.$desc.'</td></tr></table>';
					}
				}
			} else {
				$result = $resultTitle.'<table cellspacing="5" cellpadding="0" border="0" ><tr><td valign="top" width="80%">'.$result.''.$desc.'</td></tr></table>';
			}
			$result = '<div class="acymailing_content" style="clear:both;font-family:Arial;padding-bottom:10px;">'.$result.'</div>';
		}else{
			$result = $resultTitle.$result;
		}

		//Add the cat title...
		//if(!empty($tag->cattitle)){
			
		//}

		if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'edocman.php')){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins'.DS.'edocman.php');
			$result = ob_get_clean();
			$result = str_replace(array_keys($varFields), $varFields, $result);
		}

		$result = $acypluginsHelper->removeJS($result);

		//We have our content... lets check the pictures options
		if(isset($tag->pict)){
			$pictureHelper = acymailing_get('helper.acypict');
			if($tag->pict == '0'){
				$result = $pictureHelper->removePictures($result);
			}elseif($tag->pict == 'resized'){
				$pictureHelper->maxHeight = empty($tag->maxheight) ? $this->params->get('maxheight', 150) : $tag->maxheight;
				$pictureHelper->maxWidth = empty($tag->maxwidth) ? $this->params->get('maxwidth', 150) : $tag->maxwidth;
				if($pictureHelper->available()){
					$result = $pictureHelper->resizePictures($result);
				}elseif($app->isAdmin()){
					$app->enqueueMessage($pictureHelper->error, 'notice');
				}
			}
		}

		return $result;
	}

	private function _replaceAuto(&$email){
		$this->acymailing_generateautonews($email);

		if(!empty($this->tags)){
			$email->body = str_replace(array_keys($this->tags), $this->tags, $email->body);
			if(!empty($email->altbody)) $email->altbody = str_replace(array_keys($this->tags), $this->tags, $email->altbody);
			foreach($this->tags as $tag => $result){
				$email->subject = str_replace($tag, strip_tags(preg_replace('#</tr>[^<]*<tr[^>]*>#Uis', ' | ', $result)), $email->subject);
			}
		}
	}

	public function acymailing_generateautonews(&$email){

		$return = new stdClass();
		$return->status = true;
		$return->message = '';

		$time = time();

		//Check if we should generate the SmartNewsletter or not...
		$match = '#{autoedocman:(.*)}#Ui';
		$variables = array('subject', 'body', 'altbody');
		$found = false;
		foreach($variables as $var){
			if(empty($email->$var)) continue;
			$found = preg_match_all($match, $email->$var, $results[$var]) || $found;
			//we unset the results so that we won't handle it later... it will save some memory and processing
			if(empty($results[$var][0])) unset($results[$var]);
		}

		//If we didn't find anything... so we won't try to stop the generation
		if(!$found) return $return;

		$this->tags = array();
		$db = JFactory::getDBO();

		foreach($results as $var => $allresults){
			foreach($allresults[0] as $i => $oneTag){
				if(isset($this->tags[$oneTag])) continue;

				$arguments = explode('|', strip_tags($allresults[1][$i]));
				//The first argument is a list of sections and cats...
				
				//$alltypes = $arguments[1];
				//$alltypes = str_replace("type:","",$alltypes);

				$allcats  = $arguments[2];
				$allcats  = str_replace("categories:","",$allcats);
				$allcats  = substr($allcats,0,strlen($allcats)-1);
				$allcats  = explode("-",$allcats);

				$parameter = new stdClass();
				for($i = 2; $i < count($arguments); $i++){
					$args = explode(':', $arguments[$i]);
					$arg0 = trim($args[0]);
					if(empty($arg0)) continue;
					if(isset($args[1])){
						$parameter->$arg0 = $args[1];
					}else{
						$parameter->$arg0 = true;
					}
				}
				//Load the articles based on all arguments...
				$selectedArea = array();
				foreach($allcats as $oneCat){
					if(empty($oneCat)) continue;
					$selectedArea[] = (int)$oneCat;
				}

				$query = 'SELECT a.id FROM `#__edocman_documents` as a';
				$where = array();

				if(count($allcats) > 0){
					$where[] = ' a.id IN (Select document_id from #__edocman_document_category where category_id in ('.implode(',', $allcats).'))';
				}

				if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate'])){
					$condition = ' a.publish_up > \''.date('Y-m-d', $email->params['lastgenerateddate'] - date('Z')).'\'';
					// We need acy_created, the hour is not stored by K2 in the created date field
					$condition .= ' OR a.acy_created > DATE_FORMAT(CURRENT_TIMESTAMP()-SEC_TO_TIME('.intval(time() - $email->params['lastgenerateddate']).'), \'%Y-%m-%d %H:%i:%s\')';
					if($parameter->filter == 'modify'){
						$condition .= ' OR a.modified > \''.date('Y-m-d', $email->params['lastgenerateddate'] - date('Z')).'\'';
					}
					$where[] = $condition;
				}

				$where[] = ' a.publish_up < \''.date('Y-m-d H:i:s', $time - date('Z')).'\'';
				if(empty($parameter->nopublished)) $where[] = '`published` = 1';

				//Handle a date range in the query
				if(!empty($parameter->maxcreated)){
					$date = strtotime($parameter->maxcreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->maxcreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = ' a.created_time < '.$db->Quote(date('Y-m-d H:i:s', $date));
				}

				if(!empty($parameter->mincreated)){
					$date = strtotime($parameter->mincreated);
					if(empty($date)){
						acymailing_display('Wrong date format ('.$parameter->mincreated.' in '.$oneTag.'), please use YYYY-MM-DD', 'warning');
					}
					$where[] = ' a.created_time > '.$db->Quote(date('Y-m-d H:i:s', $date));
				}

				//Access for J1.5.0 only
				if(!ACYMAILING_J16){
					if(isset($parameter->access)){
						$where[] = ' a.access <= '.intval($parameter->access);
					}else{
						if($this->params->get('contentaccess', 'registered') == 'registered'){
							$where[] = ' a.access <= 1';
						}elseif($this->params->get('contentaccess', 'registered') == 'public') $where[] = 'access = 0';
					}
				}elseif(isset($parameter->access)){
					//We set it only if the access is defined in the tag
					$where[] = ' a.access = '.intval($parameter->access);
				}

				$query .= ' WHERE ('.implode(') AND (', $where).')';

				if(!empty($parameter->order)){
					if($parameter->order == 'rand'){
						$query .= ' ORDER BY rand()';
					}else{
						$ordering = explode(',', $parameter->order);
						$query .= ' ORDER BY `'.acymailing_secureField($ordering[0]).'` '.acymailing_secureField($ordering[1]);
					}
				}

				$start = '';
				if(!empty($parameter->start)) $start = intval($parameter->start).',';
				if(empty($parameter->max)) $parameter->max = 100;
				//We add a limit for the preview otherwise we could break everything
				$query .= ' LIMIT '.$start.(int)$parameter->max;

				$db->setQuery($query);
				$allArticles = acymailing_loadResultArray($db);

				if(!empty($parameter->min) && count($allArticles) < $parameter->min){
					//We won't generate the Newsletter
					$return->status = false;
					$return->message = 'Not enough k2 content for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min.' between '.acymailing_getDate($email->params['lastgenerateddate']).' and '.acymailing_getDate($time);
				}

				$stringTag = empty($parameter->noentrytext) ? '' : $parameter->noentrytext;
				if(!empty($allArticles)){
					//we insert the tags one after the other in a table as they are already sorted (using |cols parameter)
					$arrayElements = array();
					unset($parameter->id);
					$numArticle = 1;
					foreach($allArticles as $oneArticleId){
						$args = array();
						$args[] = 'edocman:'.$oneArticleId;
						$args[] = $arguments[1];
						foreach($parameter as $oneParam => $val){
							if(is_bool($val)){
								$args[] = $oneParam;
							}else $args[] = $oneParam.':'.$val;
						}
						$arrayElements[] = '{'.implode('|', $args).'}';
					}
					$stringTag = $this->acypluginsHelper->getFormattedResult($arrayElements, $parameter);
				}
				$this->tags[$oneTag] = $stringTag;
			}
		}

		return $return;
	}
}//endclass