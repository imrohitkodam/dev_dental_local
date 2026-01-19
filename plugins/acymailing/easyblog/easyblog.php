<?php
/**
 * @copyright    Copyright (C) 2009-2018 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgAcymailingEasyblog extends JPlugin{

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
		if(!isset($this->params)){
			$plugin = JPluginHelper::getPlugin('acymailing', 'easyblog');
			$this->params = new acyParameter($plugin->params);
		}

		//check if the component is installed
		$this->component_installed = file_exists(JPATH_SITE.DS.'components'.DS.'com_easyblog');
		$this->acypluginsHelper = acymailing_get('helper.acyplugins');
	}

	function acymailing_getPluginType(){
		if(!$this->component_installed || ($this->params->get('frontendaccess') == 'none' && !acymailing_isAdmin())) return;
		$onePlugin = new stdClass();
		$onePlugin->name = 'EasyBlog';
		$onePlugin->function = 'acymailingeasyblog_show';
		$onePlugin->help = 'plugin-easyblog';

		return $onePlugin;
	}

	function acymailingeasyblog_show(){
		$config = acymailing_config();
		if(version_compare($config->get('version'), '5.7.0', '<')){
			acymailing_display('Please download and install the latest AcyMailing version otherwise this plugin will NOT work', 'error');
			return;
		}

		
		acymailing_loadLanguageFile('com_easyblog', JPATH_SITE);

		$pageInfo = new stdClass();
		$pageInfo->filter = new stdClass();
		$pageInfo->filter->order = new stdClass();
		$pageInfo->limit = new stdClass();
		$pageInfo->elements = new stdClass();

		$paramBase = ACYMAILING_COMPONENT.'.easyblog';
		$pageInfo->filter->order->value = acymailing_getUserVar($paramBase.".filter_order", 'filter_order', 'a.id', 'cmd');
		$pageInfo->filter->order->dir = acymailing_getUserVar($paramBase.".filter_order_Dir", 'filter_order_Dir', 'desc', 'word');
		if(strtolower($pageInfo->filter->order->dir) !== 'desc') $pageInfo->filter->order->dir = 'asc';
		$pageInfo->search = acymailing_getUserVar($paramBase.".search", 'search', '', 'string');
		$pageInfo->search = strtolower(trim($pageInfo->search));
		$pageInfo->filter_cat = acymailing_getUserVar($paramBase.".filter_cat", 'filter_cat', '', 'int');
		$pageInfo->contenttype = acymailing_getUserVar($paramBase.".contenttype", 'contenttype', $this->params->get('default_type', 'intro'), 'string');
		$pageInfo->author = acymailing_getUserVar($paramBase.".author", 'author', 0, 'string');
		$pageInfo->titlelink = acymailing_getUserVar($paramBase.".titlelink", 'titlelink', 1, 'string');
		$pageInfo->lang = acymailing_getUserVar($paramBase.".lang", 'lang', '', 'string');
		$pageInfo->language = acymailing_getUserVar($paramBase.".language", 'language', '', 'string');
		$pageInfo->pict = acymailing_getUserVar($paramBase.".pict", 'pict', 1, 'string');
		$pageInfo->pictheight = acymailing_getUserVar($paramBase.".pictheight", 'pictheight', $this->params->get('maxheight', 150), 'string');
		$pageInfo->pictwidth = acymailing_getUserVar($paramBase.".pictwidth", 'pictwidth', $this->params->get('maxwidth', 150), 'string');
		$pageInfo->cols = acymailing_getUserVar($paramBase.".cols", 'cols', '1', 'string');


		$pageInfo->limit->value = acymailing_getUserVar($paramBase.'.list_limit', 'limit', acymailing_getCMSConfig('list_limit'), 'int');
		$pageInfo->limit->start = acymailing_getUserVar($paramBase.'.limitstart', 'limitstart', 0, 'int');

		$picts = array();
		$picts[] = acymailing_selectOption("1", acymailing_translation('JOOMEXT_YES'));
		$pictureHelper = acymailing_get('helper.acypict');
		if($pictureHelper->available()) $picts[] = acymailing_selectOption("resized", acymailing_translation('RESIZED'));
		$picts[] = acymailing_selectOption("0", acymailing_translation('JOOMEXT_NO'));

		//Content type
		$contenttype = array();
		$contenttype[] = acymailing_selectOption("title", acymailing_translation('TITLE_ONLY'));
		$contenttype[] = acymailing_selectOption("intro", acymailing_translation('INTRO_ONLY'));
		$contenttype[] = acymailing_selectOption("text", acymailing_translation('FIELD_TEXT'));
		$contenttype[] = acymailing_selectOption("full", acymailing_translation('FULL_TEXT'));

		//Title link params
		$choice = array();
		$choice[] = acymailing_selectOption("1", acymailing_translation('JOOMEXT_YES'));
		$choice[] = acymailing_selectOption("0", acymailing_translation('JOOMEXT_NO'));

		$searchFields = array('a.id', 'a.title', 'b.name', 'c.title');

		if(!empty($pageInfo->search)){
			$searchVal = '\'%'.acymailing_getEscaped($pageInfo->search, true).'%\'';
			$filters[] = implode(" LIKE $searchVal OR ", $searchFields)." LIKE $searchVal";
		}

		if($this->params->get('displayart', 'all') == 'onlypub'){
			$filters[] = "a.published = 1";
		}else{
			$filters[] = "a.published != -1";
		}

		if($this->params->get('blockedcat')){
			$filters[] = "a.category_id NOT IN (".trim($this->params->get('blockedcat'), ',').")";
		}

		$query = 'SELECT SQL_CALC_FOUND_ROWS b.name,b.username,a.created_by,a.id,a.title,a.created,c.title as catname ';
		$query .= 'FROM `#__easyblog_post` as a';
		$query .= ' LEFT JOIN `#__users` as b ON a.created_by = b.id';
		$query .= ' LEFT JOIN `#__easyblog_category` as c ON a.category_id = c.id';
		if(!empty($pageInfo->filter_cat)) $filters[] = "a.category_id = ".$pageInfo->filter_cat;

		if(!empty($filters)){
			$query .= ' WHERE ('.implode(') AND (', $filters).')';
		}

		if(!empty($pageInfo->filter->order->value)){
			$query .= ' ORDER BY '.$pageInfo->filter->order->value.' '.$pageInfo->filter->order->dir;
		}

		$rows = acymailing_loadObjectList($query, '', $pageInfo->limit->start, $pageInfo->limit->value);

		if(!empty($pageInfo->search)){
			$rows = acymailing_search($pageInfo->search, $rows);
		}

		$pageInfo->elements->total = acymailing_loadResult('SELECT FOUND_ROWS()');
		$pageInfo->elements->page = count($rows);

		$queryCats = 'SELECT id,title FROM #__easyblog_category';
		if($this->params->get('blockedcat')){
			$queryCats .= " WHERE id NOT IN (".trim($this->params->get('blockedcat'), ',').")";
		}
		$queryCats .= ' ORDER BY published DESC, ordering ASC';
		$categories = acymailing_loadObjectList($queryCats);

		$allCats = new stdClass();
		$allCats->id = 0;
		$allCats->title = acymailing_translation('ACY_ALL');

		array_unshift($categories, $allCats);


		$pagination = new acyPagination($pageInfo->elements->total, $pageInfo->limit->start, $pageInfo->limit->value);


		$tabs = acymailing_get('helper.acytabs');
		echo $tabs->startPane('easyblog_tab');
		echo $tabs->startPanel(acymailing_translation('TAG_ELEMENTS'), 'easyblog_content');

		?>
		<br style="font-size:1px"/>
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
				for(var i = 0; i < document.adminForm.contenttype.length; i++){
					if(document.adminForm.contenttype[i].checked){
						selectedtype = document.adminForm.contenttype[i].value;
						otherinfo += '| type:' + document.adminForm.contenttype[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelink.length; i++){
					if(document.adminForm.titlelink[i].checked && document.adminForm.titlelink[i].value != 0){
						otherinfo += '| link';
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.author.length; i++){
						if(document.adminForm.author[i].checked && document.adminForm.author[i].value != 0){
							otherinfo += '| author';
						}
					}
					for(var i = 0; i < document.adminForm.pict.length; i++){
						if(document.adminForm.pict[i].checked){
							otherinfo += '| pict:' + document.adminForm.pict[i].value;
							if(document.adminForm.pict[i].value == 'resized'){
								document.getElementById('pictsize').style.display = '';
								if(document.adminForm.pictwidth.value) otherinfo += '| maxwidth:' + document.adminForm.pictwidth.value;
								if(document.adminForm.pictheight.value) otherinfo += '| maxheight:' + document.adminForm.pictheight.value;
							}else{
								document.getElementById('pictsize').style.display = 'none';
							}
						}
					}
				}

				if(window.document.getElementById('jflang') && window.document.getElementById('jflang').value != ''){
					otherinfo += '| lang:';
					otherinfo += window.document.getElementById('jflang').value;
				}

				for(var i in selectedContents){
					if(selectedContents[i] && !isNaN(i)){
						tag = tag + '{easyblog:' + selectedContents[i] + otherinfo + '}<br />';
					}
				}
				setTag(tag);
			}
			//-->
		</script>
		<table width="100%" class="adminform">
			<tr>
				<td>
					<?php echo acymailing_translation('DISPLAY'); ?>
				</td>
				<td colspan="2">
					<?php echo acymailing_radio($contenttype, 'contenttype', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->contenttype);?>
				</td>
				<td>
					<?php
					$jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateTag();"';
					echo $jflanguages->display('lang', $pageInfo->lang);
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo acymailing_translation('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($choice, 'titlelink', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->titlelink);?>
				</td>
				<td>
					<?php echo acymailing_translation('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($choice, 'author', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->author); ?>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo acymailing_translation('DISPLAY_PICTURES'); ?></td>
				<td valign="top"><?php echo acymailing_radio($picts, 'pict', 'size="1" onclick="updateTag();"', 'value', 'text', $pageInfo->pict); ?>
					<span id="pictsize" <?php if($pageInfo->pict != 'resized') echo 'style="display:none;"'; ?>><br/><?php echo acymailing_translation('CAPTCHA_WIDTH') ?>
						<input name="pictwidth" type="text" onchange="updateTag();" value="<?php echo $pageInfo->pictwidth; ?>" style="width:30px;"/>
					x <?php echo acymailing_translation('CAPTCHA_HEIGHT') ?>
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
					<input placeholder="<?php echo acymailing_translation('ACY_SEARCH'); ?>" type="text" name="search" id="acymailingsearch" value="<?php echo $pageInfo->search;?>" class="text_area" onchange="document.adminForm.submit();"/>
					<button class="btn" onclick="this.form.submit();"><?php echo acymailing_translation('JOOMEXT_GO'); ?></button>
					<button class="btn" onclick="document.getElementById('acymailingsearch').value='';this.form.submit();"><?php echo acymailing_translation('JOOMEXT_RESET'); ?></button>
				</td>
				<td nowrap="nowrap">
					<?php echo acymailing_select($categories, 'filter_cat', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', (int)$pageInfo->filter_cat);?>
				</td>
			</tr>
		</table>

		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<thead>
			<tr>
				<th class="title">
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('FIELD_TITLE'), 'a.title', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_AUTHOR'), 'b.name', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title">
					<?php echo acymailing_gridSort(acymailing_translation('CREATED_DATE'), 'a.created', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
				<th class="title titleid">
					<?php echo acymailing_gridSort(acymailing_translation('ACY_ID'), 'a.id', $pageInfo->filter->order->dir, $pageInfo->filter->order->value); ?>
				</th>
			</tr>
			</thead>
			<tfoot>
			<tr>
				<td colspan="5">
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
				<tr id="content<?php echo $row->id?>" class="<?php echo "row$k"; ?>" onclick="applyContent(<?php echo $row->id.",'row$k'"?>);" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $row->title;
						?>
					</td>
					<td>
						<?php
						if(!empty($row->name)){
							$text = '<b>'.acymailing_translation('ACY_NAME').' : </b>'.$row->name;
							$text .= '<br /><b>'.acymailing_translation('ACY_USERNAME').' : </b>'.$row->username;
							$text .= '<br /><b>'.acymailing_translation('ACY_ID').' : </b>'.$row->created_by;
							echo acymailing_tooltip($text, $row->name, '', $row->name);
						}
						?>
					</td>
					<td align="center" style="text-align:center">
						<?php echo acymailing_getDate(acymailing_getTime(strip_tags($row->created))); ?>
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
		echo $tabs->startPanel(acymailing_translation('TAG_CATEGORIES'), 'easyblog_auto');

		$column = array();
		for($i = 1; $i < 11; $i++){
			$column[] = acymailing_selectOption("$i", $i);
		}

		//Select all blog teams...
		$teamblogs = acymailing_loadObjectList('SELECT id,title FROM #__easyblog_team ORDER BY title ASC');
		//Add an element at the beginning...
		$firstElement = new stdClass();
		$firstElement->id = 0;
		$firstElement->title = acymailing_translation('FIELD_DEFAULT');
		array_unshift($teamblogs, $firstElement);

		//Select all bloggers...
		$bloggers = acymailing_loadObjectList('SELECT DISTINCT user.id as id,user.username as title FROM #__easyblog_post as post JOIN #__users as user ON post.created_by = user.id ORDER BY user.username ASC');
		array_unshift($bloggers, $firstElement);

		$type = acymailing_getVar('string', 'type');

		?>
		<br style="font-size:1px"/>
		<script language="javascript" type="text/javascript">
			<!--
			var selectedCat = new Array();
			function applyAuto(catid, rowClass){
				if(catid == 'all'){
					if(window.document.getElementById('cat' + catid).className == 'selectedrow'){
						window.document.getElementById('catall').className = rowClass;
					}else{
						window.document.getElementById('cat' + catid).className = 'selectedrow';
						for(key in selectedCat){
							if(!isNaN(key)){
								window.document.getElementById('cat' + key).className = rowClass;
								delete selectedCat[key];
							}
						}
					}
				}else{
					window.document.getElementById('catall').className = 'row0';
					if(selectedCat[catid]){
						window.document.getElementById('cat' + catid).className = rowClass;
						delete selectedCat[catid];
					}else{
						window.document.getElementById('cat' + catid).className = 'selectedrow';
						selectedCat[catid] = 'selectedone';
					}
				}
				updateAutoTag();
			}

			function updateAutoTag(){
				tag = '{autoeasyblog:';

				for(var icat in selectedCat){
					if(selectedCat[icat] == 'selectedone'){
						tag += icat + '-';
					}
				}

				if(document.adminForm.min_article && document.adminForm.min_article.value && document.adminForm.min_article.value != 0){
					tag += '| min:' + document.adminForm.min_article.value;
				}
				if(document.adminForm.max_article.value && document.adminForm.max_article.value != 0){
					tag += '| max:' + document.adminForm.max_article.value;
				}
				if(document.adminForm.contentorder.value){
					tag += "| order:" + document.adminForm.contentorder.value + "," + document.adminForm.contentorderdir.value;
				}
				if(document.adminForm.contentfilter && document.adminForm.contentfilter.value){
					tag += document.adminForm.contentfilter.value;
				}
				if(document.adminForm.meta_article && document.adminForm.meta_article.value){
					tag += '| meta:' + document.adminForm.meta_article.value;
				}
				if(document.adminForm.teamblog && document.adminForm.teamblog.value > 0){
					tag += '| teamblog:' + document.adminForm.teamblog.value;
				}
				if(document.adminForm.blogger && document.adminForm.blogger.value > 0){
					tag += '| blogger:' + document.adminForm.blogger.value;
				}


				for(var i = 0; i < document.adminForm.contenttypeauto.length; i++){
					if(document.adminForm.contenttypeauto[i].checked){
						selectedtype = document.adminForm.contenttypeauto[i].value;
						tag += '| type:' + document.adminForm.contenttypeauto[i].value;
					}
				}
				for(var i = 0; i < document.adminForm.titlelinkauto.length; i++){
					if(document.adminForm.titlelinkauto[i].checked && document.adminForm.titlelinkauto[i].value != 0){
						tag += '| link';
					}
				}
				if(selectedtype != 'title'){
					for(var i = 0; i < document.adminForm.authorauto.length; i++){
						if(document.adminForm.authorauto[i].checked && document.adminForm.authorauto[i].value != 0){
							tag += '| author';
						}
					}
					for(var i = 0; i < document.adminForm.pictauto.length; i++){
						if(document.adminForm.pictauto[i].checked){
							tag += '| pict:' + document.adminForm.pictauto[i].value;
							if(document.adminForm.pictauto[i].value == 'resized'){
								document.getElementById('pictsizeauto').style.display = '';
								if(document.adminForm.pictwidthauto.value) tag += '| maxwidth:' + document.adminForm.pictwidthauto.value;
								if(document.adminForm.pictheightauto.value) tag += '| maxheight:' + document.adminForm.pictheightauto.value;
							}else{
								document.getElementById('pictsizeauto').style.display = 'none';
							}
						}
					}
				}
				if(document.adminForm.cols && document.adminForm.cols.value > 1){
					tag += '| cols:' + document.adminForm.cols.value;
				}
				if(window.document.getElementById('jflangauto') && window.document.getElementById('jflangauto').value != ''){
					tag += '| lang:';
					tag += window.document.getElementById('jflangauto').value;
				}
				if(window.document.getElementById('jlang') && window.document.getElementById('jlang').value != ''){
					tag += '| language:';
					tag += window.document.getElementById('jlang').value;
				}

				tag += '}';

				setTag(tag);
			}
			//-->
		</script>
		<style rel="stylesheet" type="text/css">
			.acymailing_divItemCode{
				width: 260px;
				height: 200px;
				position: absolute;
				padding: 20px !important;
				border: 1px solid #aaaaaa;
				box-shadow: 2px 5px 10px #666;
				background-color: white;
				overflow: auto;
				z-index: 2;
				margin: 0;
				white-space: initial;
			}

			.acymailing_divItemCode .acymailing_itemLine:hover td{
				background-color: #ededed;
				cursor: pointer;
			}
		</style>

		<script language="javascript" type="text/javascript">
			<!--
			var acymailing_idbutton;

			window.addEventListener("load", function(){
				document.onclick = function(){
					allDivItemCode = document.getElementsByClassName("acymailing_divItemCode");
					for(i = 0; i < allDivItemCode.length; i++){
						allDivItemCode[i].style.display = "none";
					}
				};
			});

			function acymailing_displayDivItemCode(id, e){
				divItemCode = document.getElementById("acymailing_divItemCode" + id);
				styleDivBefore = divItemCode.style.display;
				allDivItemCode = document.getElementsByClassName("acymailing_divItemCode");
				for(i = 0; i < allDivItemCode.length; i++){
					allDivItemCode[i].style.display = "none";
				}
				acymailing_idbutton = id;

				if(styleDivBefore == "block"){
					divItemCode.style.display = "none"
				}else{
					divItemCode.style.display = "block"
				}

				document.getElementById("acymailing_searchAItem" + id).focus();

				if(e){
					e.stopPropagation();
				}else{
					window.event.cancelBubble = true;
				}
			}

			function acymailing_selectAItem(itemCode, itemSelected, id, displayItem){
				if(displayItem == 1){
					document.getElementById("acymailing_buttonItemValue" + id).innerHTML = itemSelected;
				}
				document.getElementById("acymailing_divItemCode" + id).style.display = "none";
				document.getElementById("blogger").value = itemCode;
				updateAutoTag();
			}

			function acymailing_searchAItem(idDivItem){
				divItem = document.getElementById("acymailing_divItemCode" + idDivItem);
				filter = document.getElementById("acymailing_searchAItem" + idDivItem).value.toLowerCase();
				countries = divItem.getElementsByClassName("acymailing_itemLine");
				for(i = 0; i < countries.length; i++){
					itemName = countries[i].childNodes[0].innerHTML.toLowerCase();
					if(itemName.indexOf(filter) > -1){
						countries[i].style.display = "table-row";
					}else{
						countries[i].style.display = "none";
					}
				}

			}

			function acymailing_stopPropagationDivItemCode(e){
				if(e){
					e.stopPropagation();
				}else{
					window.event.cancelBubble = true;
				}
			}
			;
			-->
		</script>
		<table width="100%" class="adminform">
			<tr>
				<td>
					<?php echo acymailing_translation('DISPLAY');?>
				</td>
				<td colspan="2">
					<?php echo acymailing_radio($contenttype, 'contenttypeauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_type', 'intro'));?>
				</td>
				<td>
					<?php
					$jflanguages = acymailing_get('type.jflanguages');
					$jflanguages->onclick = 'onchange="updateAutoTag();"';
					$jflanguages->id = 'jflangauto';
					echo $jflanguages->display('langauto');
					if(empty($jflanguages->found) && method_exists($jflanguages, 'displayJLanguages')){
						echo $jflanguages->displayJLanguages('jlangauto');
					}
					?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo acymailing_translation('CLICKABLE_TITLE'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($choice, 'titlelinkauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $pageInfo->titlelink);?>
				</td>
				<td>
					<?php echo acymailing_translation('AUTHOR_NAME'); ?>
				</td>
				<td>
					<?php echo acymailing_radio($choice, 'authorauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $pageInfo->author); ?>
				</td>
			</tr>
			<tr>
				<td valign="top"><?php echo acymailing_translation('DISPLAY_PICTURES'); ?></td>
				<td valign="top"><?php echo acymailing_radio($picts, 'pictauto', 'size="1" onclick="updateAutoTag();"', 'value', 'text', $this->params->get('default_pict', '1')); ?>

					<span id="pictsizeauto" <?php if($this->params->get('default_pict', '1') != 'resized') echo 'style="display:none;"'; ?> ><br/><?php echo acymailing_translation('CAPTCHA_WIDTH') ?>
						<input name="pictwidthauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxwidth', '150');?>" style="width:30px;"/>
					× <?php echo acymailing_translation('CAPTCHA_HEIGHT') ?>
						<input name="pictheightauto" type="text" onchange="updateAutoTag();" value="<?php echo $this->params->get('maxheight', '150');?>" style="width:30px;"/>
				</span>
				</td>
				<td valign="top" nowrap="nowrap"><?php echo acymailing_translation('FIELD_COLUMNS'); ?></td>
				<td valign="top" nowrap="nowrap">
					<?php echo acymailing_select($column, 'cols', 'size="1" onchange="updateAutoTag();" style="width:50px"', 'value', 'text', $pageInfo->cols); ?>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo acymailing_translation('COM_EASYBLOG_TOOLBAR_TEAMBLOGS'); ?>
				</td>
				<td>
					<?php echo acymailing_select($teamblogs, 'teamblog', 'size="1" style="width:150px;" onchange="updateAutoTag();"', 'id', 'title'); ?>
				</td>
				<td>
					<?php echo acymailing_translation('COM_EASYBLOG_TOOLBAR_BLOGGERS'); ?>
				</td>
				<td>
					<input type="hidden" id="blogger" name="bloggerslist" value="0">
					<button type="button" class="acymailing_buttonItemCode" onclick="acymailing_displayDivItemCode(0,event)" value="0">
						<table>
							<tr>
								<td>
									<div id="acymailing_buttonItemValue0">
										<?php echo acymailing_translation('ACY_SEARCH'); ?>
									</div>
								</td>
								<td>
									<img class="arrow" src="<?php echo ACYMAILING_LIVE; ?>/media/com_acymailing/images/arrow.png">
								</td>
							</tr>
						</table>
					</button>
					<div onclick="acymailing_stopPropagationDivItemCode(event)" class="acymailing_divItemCode" id="acymailing_divItemCode0" style="display:none; overflow-y:scroll !important;">
						<div style="position:relative; margin-bottom:10px;">
							<input onkeyup="acymailing_searchAItem(0)" type="text" style="width:100%; margin-bottom:5px;" placeholder="<?php echo acymailing_translation('ACY_SEARCH'); ?>" id="acymailing_searchAItem0" class="acymailing_searchAItem">
						</div>
						<table cellspacing="0">
							<?php
							$k = 0;
							foreach($bloggers as $oneItem){
								echo '<tr '.($k > 100 ? 'style="display:none;" ' : '').'class="acymailing_itemLine" onclick="acymailing_selectAItem('.$oneItem->id.',\''.$oneItem->title.'\',acymailing_idbutton, 1)"><td>'.$oneItem->title.'</td></tr>';
								$k++;
							}
							?>
						</table>
					</div>
				</td>
			</tr>
			<tr>
				<td>
					<?php echo acymailing_translation('MAX_ARTICLE'); ?>
				</td>
				<td>
					<input type="text" name="max_article" style="width:50px" value="20" onchange="updateAutoTag();"/>
				</td>
				<td>
					<?php echo acymailing_translation('ACY_ORDER'); ?>
				</td>
				<td>
					<?php
					$values = array('id' => 'ACY_ID', 'ordering' => 'ACY_ORDERING', 'created' => 'CREATED_DATE', 'modified' => 'MODIFIED_DATE', 'title' => 'FIELD_TITLE');
					echo $this->acypluginsHelper->getOrderingField($values, 'id', 'DESC', 'updateAutoTag');
					?>
				</td>
			</tr>
			<?php if($type == 'autonews'){ ?>
				<tr>
					<td>
						<?php echo acymailing_translation('MIN_ARTICLE'); ?>
					</td>
					<td>
						<input type="text" name="min_article" style="width:50px" value="1" onchange="updateAutoTag();"/>
					</td>
					<td>
						<?php echo acymailing_translation('JOOMEXT_FILTER'); ?>
					</td>
					<td>
						<?php $filter = acymailing_get('type.contentfilter');
						$filter->onclick = "updateAutoTag();";
						echo $filter->display('contentfilter', '|filter:created'); ?>
					</td>
				</tr>
			<?php } ?>
		</table>

		<table class="adminlist table table-striped table-hover" cellpadding="1" width="100%">
			<?php
			$k = 0;
			?>
			<tr id="catall" class="<?php echo "row$k"; ?>" onclick="applyAuto('all','<?php echo "row$k" ?>');" style="cursor:pointer;">
				<td class="acytdcheckbox"></td>
				<td>
					<?php
					echo acymailing_translation('ACY_ALL');
					?>
				</td>
			</tr>
			<?php
			foreach($categories as $oneCat){
				if(empty($oneCat->id)) continue;
				?>
				<tr id="cat<?php echo $oneCat->id ?>" class="<?php echo "row$k"; ?>" onclick="applyAuto(<?php echo $oneCat->id ?>,'<?php echo "row$k" ?>');" style="cursor:pointer;">
					<td class="acytdcheckbox"></td>
					<td>
						<?php
						echo $oneCat->title;
						?>
					</td>
				</tr>
				<?php $k = 1 - $k;
			} ?>
		</table>
		<?php

		echo $tabs->endPanel();
		echo $tabs->endPane();
		//End of the function
	}

	function acymailing_replacetags(&$email, $send = true){
		$this->_replaceAuto($email);
		$this->_replaceArticles($email);
	}

	function _replaceArticles(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		//load the tags
		$tags = $acypluginsHelper->extractTags($email, 'easyblog');
		if(empty($tags)) return;

		if(file_exists(JPATH_SITE.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'helper.php')){
			include_once(JPATH_SITE.DS.'components'.DS.'com_easyblog'.DS.'helpers'.DS.'helper.php');
		}else include_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_easyblog'.DS.'includes'.DS.'easyblog.php');

		if(class_exists('EB') && method_exists('EB', 'loadLanguages')) EB::loadLanguages();

		$this->newslanguage = new stdClass();
		if(!empty($email->language)){
			$this->newslanguage = acymailing_loadObject('SELECT lang_id, lang_code FROM #__languages WHERE sef = '.acymailing_escapeDB($email->language).' LIMIT 1');
		}

		//We will need the mailer class as well
		$this->mailerHelper = acymailing_get('helper.mailer');

		//Set the read more link:
		$this->readmore = empty($email->template->readmore) ? acymailing_translation('JOOMEXT_READ_MORE') : '<img src="'.ACYMAILING_LIVE.$email->template->readmore.'" alt="'.acymailing_translation('JOOMEXT_READ_MORE', true).'" />';

		$htmlreplace = array();
		$textreplace = array();
		$subjectreplace = array();
		foreach($tags as $i => $params){
			if(isset($htmlreplace[$i])) continue;
			$content = $this->_replaceContent($tags[$i]);
			$htmlreplace[$i] = $content;
			$textreplace[$i] = $this->mailerHelper->textVersion($content, true);
			$subjectreplace[$i] = strip_tags($content);
		}
		$email->body = str_replace(array_keys($htmlreplace), $htmlreplace, $email->body);
		$email->altbody = str_replace(array_keys($textreplace), $textreplace, $email->altbody);
		$email->subject = str_replace(array_keys($subjectreplace), $subjectreplace, $email->subject);
	}

	function _replaceContent(&$tag){
		$acypluginsHelper = acymailing_get('helper.acyplugins');

		if(empty($tag->wordwrap)) $tag->wordwrap = $this->params->get('wordwrap', 0);
		if(empty($tag->itemid)) $tag->itemid = $this->params->get('itemid', 0);

		//2 : Load the Joomla article... with the author, the section and the categories to create nice links
		$query = 'SELECT a.*,b.name as authorname, c.title as cattitle FROM #__easyblog_post as a ';
		$query .= 'LEFT JOIN #__users as b ON a.created_by = b.id ';
		$query .= 'LEFT JOIN #__easyblog_category AS c ON c.id = a.category_id ';
		$query .= 'WHERE a.id = '.intval($tag->id);
		if($this->params->get('blockedcat')) $query .= ' AND `category_id` NOT IN ('.trim($this->params->get('blockedcat'), ',').")";
		$query .= ' LIMIT 1';

		$article = acymailing_loadObject($query);

		$result = '';

		//In case of we could not load the article for any reason...
		if(empty($article)){
			if(acymailing_isAdmin()) acymailing_enqueueMessage('The article "'.$tag->id.'" could not be loaded', 'notice');
			return $result;
		}

		//We just loaded the article but we may need to translate it depending on tag->lang...
		if(empty($tag->lang) && !empty($this->newslanguage) && !empty($this->newslanguage->lang_code)) $tag->lang = $this->newslanguage->lang_code.','.$this->newslanguage->lang_id;
		$acypluginsHelper->translateItem($article, $tag, 'easyblog_post');

		$varFields = array();
		foreach($article as $fieldName => $oneField){
			$varFields['{'.$fieldName.'}'] = $oneField;
		}

		$link = 'index.php?option=com_easyblog&view=entry&id='.$tag->id;
		if(!empty($tag->lang)) $link .= '&lang='.substr($tag->lang, 0, strpos($tag->lang, ','));
		if(!empty($tag->itemid)) $link .= '&Itemid='.$tag->itemid;
		$link = acymailing_frontendLink($link);
		$varFields['{link}'] = $link;

		//type == link... we just return the link (it was for a client...)
		if($tag->type == 'link') return $link;

		//Add the title with a link or not on it.
		//If we add a link, we add in the same time a name="content-CONTENTID" so that we will be able to parse the content to create a nice summary
		$styleTitle = '';
		$styleTitleEnd = '';
		if($tag->type != "title"){
			$styleTitle = '<h2 class="acymailing_title">';
			$styleTitleEnd = '</h2>';
		}

		if(empty($tag->notitle)){
			if(!empty($tag->link)){
				$result .= '<a href="'.$link.'" ';
				if($tag->type != "title") $result .= 'style="text-decoration:none" name="content-'.$article->id.'" ';
				$result .= 'target="_blank" >'.$styleTitle.$article->title.$styleTitleEnd.'</a>';
			}else{
				$result .= $styleTitle.$article->title.$styleTitleEnd;
			}
		}

		//Add the author...
		if(!empty($tag->author)){
			$result .= $article->authorname.'<br />';
		}

		$contentText = '';
		//We add the intro text
		if($tag->type != "title"){

			if($tag->type == "intro"){
				$article->content = '';
			}elseif($tag->type == "text") $article->intro = '';

			if(empty($article->content) || $tag->type != "text"){
				$contentText .= $article->intro;
			}

			//We add the full text
			if(!empty($article->content) && $tag->type != "intro"){
				if(!empty($contentText)) $contentText .= ' <br />';
				$contentText .= $article->content;
			}
			$contentText = str_replace('"//localhost', '"http://localhost', $contentText);
			$contentText = preg_replace('#(<img[^>]*src=")(//[^>]*>)#Uis', '$1http:$2', $contentText);
			$contentText = preg_replace('#\[embed=videolink][^}]*youtube[^=]*=([^"/}]*)[^}]*}\[/embed]#i', '<a href="http://www.youtube.com/watch?v=$1"><img src="http://img.youtube.com/vi/$1/0.jpg" alt="youtube video"/></a>', $contentText);
			$contentText = preg_replace('#<video[^>]*youtube\.com/embed/([^"/]*)[^>]*>[^>]*</video>#i', '<a href="http://www.youtube.com/watch?v=$1"><img src="http://img.youtube.com/vi/$1/0.jpg" alt="youtube video"/></a>', $contentText);

			$contentText = preg_replace('#\[embed=videolink][^}]*video":"([^"]*)[^}]*}\[/embed]#i', '<a href="$1"><img src="http://img.youtube.com/vi/0.jpg" alt="youtube video"/></a>', $contentText);
			$contentText = preg_replace('#<video[^>]*src="([^"]*)"[^>]*>[^>]*</video>#i', '<a href="$1"><img src="http://img.youtube.com/vi/0.jpg" alt="youtube video"/></a>', $contentText);

			//Do we need to limit the number of characters for the introtext?
			$forceReadMore = false;

			if(empty($tag->wordwrap)){
				if(class_exists('EasyBlogHelper') && method_exists('EasyBlogHelper', 'truncateContent')) {
					$config = EasyBlogHelper::getConfig();
					if ($config->get('layout_blogasintrotext') == 1) {
						unset($article->source);
						$article->content = $contentText;
						$article->intro = '';
						unset($article->posttype);
						EasyBlogHelper::truncateContent($article, false, false, false);
						$contentText = $article->text;
						$forceReadMore = true;
					}
				}else{
					$config = EB::config();
					if($config->get('composer_truncation_enabled') == 1 && $config->get('main_truncate_type') == 'chars'){
						$tag->wordwrap = $config->get('layout_maxlengthasintrotext', '350');
					}
				}
			}

			if(!empty($tag->wordwrap)){
				$tag->wrap = $tag->wordwrap;
				$contentText = $acypluginsHelper->wrapText($contentText, $tag);
				$forceReadMore = $acypluginsHelper->wraped;
			}

			//Add J2.5 pictures
			if(!empty($article->image) && !empty($tag->pict)){
				$picthtml = '';
				$image = json_decode($article->image);

				if(empty($image)){
					if(strpos($article->image, 'post:') !== false){
						$image = new stdClass();
						$config = EB::config();
						$image->url = $config->get('main_articles_path', 'images/easyblog_articles/').$article->id.'/'.basename($article->image);
					}elseif(strpos($article->image, 'user:') !== false){

						$image = new stdClass();

						$config = EB::config();

						$image->url = $config->get('main_users_path', 'images/easyblog_images/').$article->created_by.'/'.basename($article->image);
					}
				}

				if(!empty($image->url)){
					$style = 'float:left;padding-right:10px;padding-bottom:10px;';
					if(!empty($tag->link)) $picthtml .= '<a href="'.$link.'" style="text-decoration:none" >';
					$varFields['{picthtml}'] = '<img style="'.$style.'" alt="" border="0" src="'.$image->url.'" />';
					$picthtml .= $varFields['{picthtml}'];
					if(!empty($tag->link)) $picthtml .= '</a>';
					//We inser the picture at the beginning.
					$contentText = $picthtml.$contentText;
				}
			}

			//We add the read more link but only if we have a fulltext after...
			if((!empty($article->content) && $tag->type == "intro") || $forceReadMore || !empty($tag->forcereadmore)){
				$readMoreText = empty($tag->readmore) ? $this->readmore : $tag->readmore;
				$contentText .= '<a style="text-decoration:none;" target="_blank" href="'.$link.'"><span class="acymailing_readmore">'.$readMoreText.'</span></a>';
			}

			$result .= $contentText;
			$result = '<div class="acymailing_content"><table><tr><td>'.$result.'</td></tr></table></div>';
		}

		if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'easyblog.php')){
			ob_start();
			require(ACYMAILING_MEDIA.'plugins'.DS.'easyblog.php');
			$result = ob_get_clean();
			$result = str_replace(array_keys($varFields), $varFields, $result);
		}

		//We remove the JS...
		$result = $acypluginsHelper->removeJS($result);
		$result = $acypluginsHelper->replaceVideos($result);

		//We have our content... lets check the pictures options
		if(isset($tag->pict)){
			$pictureHelper = acymailing_get('helper.acypict');
			$pictureHelper->maxHeight = empty($tag->maxheight) ? $this->params->get('maxheight', 150) : $tag->maxheight;
			$pictureHelper->maxWidth = empty($tag->maxwidth) ? $this->params->get('maxwidth', 150) : $tag->maxwidth;
			if($tag->pict == '0'){
				$result = $pictureHelper->removePictures($result);
			}elseif($tag->pict == 'resized'){
				if($pictureHelper->available()){
					$result = $pictureHelper->resizePictures($result);
				}elseif(acymailing_isAdmin()){
					acymailing_enqueueMessage($pictureHelper->error, 'notice');
				}
			}
		}
		return $result;
	}

	function _replaceAuto(&$email){
		$this->acymailing_generateautonews($email);

		if(!empty($this->tags)){
			$email->body = str_replace(array_keys($this->tags), $this->tags, $email->body);
			if(!empty($email->altbody)) $email->altbody = str_replace(array_keys($this->tags), $this->tags, $email->altbody);
			foreach($this->tags as $tag => $result){
				$email->subject = str_replace($tag, strip_tags(preg_replace('#</tr>[^<]*<tr[^>]*>#Uis', ' | ', $result)), $email->subject);
			}
		}
	}

	function acymailing_generateautonews(&$email){
		$acypluginsHelper = acymailing_get('helper.acyplugins');
		$time = time();

		//load the tags
		$tags = $acypluginsHelper->extractTags($email, 'autoeasyblog');
		$return = new stdClass();
		$return->status = true;
		$return->message = '';
		$this->tags = array();

		if(empty($tags)){
			return $return;
		}

		foreach($tags as $oneTag => $parameter){
			if(isset($this->tags[$oneTag])){
				continue;
			}
			$allcats = explode('-', $parameter->id);
			//Load the articles based on all arguments...
			$selectedArea = array();
			foreach($allcats as $oneCat){
				if(empty($oneCat)) continue;
				$selectedArea[] = (int)$oneCat;
			}
			$query = 'SELECT DISTINCT post.id FROM `#__easyblog_post` as post';
			$where = array();

			if(!empty($parameter->teamblog)){
				$query .= ' JOIN #__easyblog_team_post as teamblog ON post.id = teamblog.post_id';
				$where[] = 'teamblog.team_id = '.intval($parameter->teamblog);
			}

			if(!empty($parameter->blogger)){
				$where[] = 'created_by = '.intval($parameter->blogger);
			}

			if(!empty($selectedArea)){
				$where[] = '`category_id` IN ('.implode(',', $selectedArea).')';
			}

			if(empty($parameter->excludedcats)) $parameter->excludedcats = $this->params->get('blockedcat');
			if(!empty($parameter->excludedcats)){
				$excludedCats = explode(',', $parameter->excludedcats);
				acymailing_arrayToInteger($excludedCats);
				$where[] = '`category_id` NOT IN ("'.implode('","', $excludedCats).'")';
			}

			if(!empty($parameter->filter) && !empty($email->params['lastgenerateddate'])){
				$condition = '`publish_up` >\''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
				$condition .= ' OR `created` >\''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
				if($parameter->filter == 'modify'){
					$condition .= ' OR `modified` > \''.date('Y-m-d H:i:s', $email->params['lastgenerateddate'] - date('Z')).'\'';
				}

				$where[] = $condition;
			}

			if(!empty($parameter->meta)){
				$query .= ' JOIN #__easyblog_meta AS meta ON post.id = meta.content_id AND type = "post"';
				$allMetaTags = explode(',', $parameter->meta);
				$metaWhere = array();
				foreach($allMetaTags as $oneMeta){
					if(empty($oneMeta)) continue;
					$metaWhere[] = "meta.`keywords` LIKE '%".acymailing_getEscaped($oneMeta, true)."%'";
				}
				if(!empty($metaWhere)) $where[] = implode(' OR ', $metaWhere);
			}

			$where[] = '`publish_up` < \''.date('Y-m-d H:i:s', $time - date('Z')).'\'';
			$where[] = '`publish_down` > \''.date('Y-m-d H:i:s', $time - date('Z')).'\' OR `publish_down` = 0';
			$where[] = 'published = 1';
			if(!isset($parameter->access)) $parameter->access = $this->params->get('contentaccess', 0);

			//Featured articles?
			if(!empty($parameter->frontpage)){
				$where[] = 'frontpage = 1';
			}

			//No-featured articles?
			if(!empty($parameter->nofrontpage)){
				$where[] = 'frontpage = 0';
			}

			//Add filter on language...
			if(!empty($parameter->language)){
				//We may have several languages separated by a comma
				$allLanguages = explode(',', $parameter->language);
				$langWhere = 'language IN (';
				foreach($allLanguages as $oneLanguage){
					$langWhere .= acymailing_escapeDB(trim($oneLanguage)).',';
				}
				$where[] = trim($langWhere, ',').')';
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

			$allArticles = acymailing_loadResultArray($query);

			if(!empty($parameter->min) && count($allArticles) < $parameter->min){
				//We won't generate the Newsletter
				$return->status = false;
				$return->message = 'Not enough articles for the tag '.$oneTag.' : '.count($allArticles).' / '.$parameter->min.' between '.acymailing_getDate($email->params['lastgenerateddate']).' and '.acymailing_getDate($time);
			}

			$stringTag = '';
			if(!empty($allArticles)){
				if(file_exists(ACYMAILING_MEDIA.'plugins'.DS.'autoeasyblog.php')){
					ob_start();
					require(ACYMAILING_MEDIA.'plugins'.DS.'autoeasyblog.php');
					$stringTag = ob_get_clean();
				}else{
					//we insert the article tag one after the other in a table as they are already sorted
					$arrayElements = array();
					$numArticle = 1;
					foreach($allArticles as $oneArticleId){
						$args = array();
						$args[] = 'easyblog:'.$oneArticleId;
						$args[] = 'num:'.$numArticle++;
						if(!empty($parameter->type)) $args[] = 'type:'.$parameter->type;
						if(!empty($parameter->link)) $args[] = 'link';
						if(!empty($parameter->wordwrap)) $args[] = 'wordwrap:'.$parameter->wordwrap;
						if(!empty($parameter->author)) $args[] = 'author';
						if(!empty($parameter->lang)) $args[] = 'lang:'.$parameter->lang;
						if(!empty($parameter->notitle)) $args[] = 'notitle';
						if(isset($parameter->pict)) $args[] = 'pict:'.$parameter->pict;
						if(!empty($parameter->maxwidth)) $args[] = 'maxwidth:'.$parameter->maxwidth;
						if(!empty($parameter->maxheight)) $args[] = 'maxheight:'.$parameter->maxheight;
						if(!empty($parameter->itemid)) $args[] = 'itemid:'.$parameter->itemid;
						if(!empty($parameter->forcereadmore)) $args[] = 'forcereadmore';
						$arrayElements[] = '{'.implode('|', $args).'}';
					}
					$stringTag = $acypluginsHelper->getFormattedResult($arrayElements, $parameter);
				}
			}
			$this->tags[$oneTag] = $stringTag;
		}

		return $return;
	}
}//endclass