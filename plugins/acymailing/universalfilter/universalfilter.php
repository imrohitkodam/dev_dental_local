<?php
/**
 * @copyright    Copyright (C) 2009-2018 ACYBA SAS - All rights reserved..
 * @license        GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

class plgAcymailingUniversalfilter extends JPlugin{

	function __construct(&$subject, $config){
		parent::__construct($subject, $config);
	}

	function onAcyDisplayFilters(&$type, $context = "massactions"){

		if($this->params->get('displayfilter_'.$context, true) == false) return;

		$type['universalfilter'] = 'Simple Query';
		$type['filterquery'] = 'Advanced Query';
		$type['import'] = acymailing_translation('IMPORT');
		$type['extractemail'] = 'Extract addresses';


		$operators = acymailing_get('type.operators');
		$operators->extra = 'onchange="countresults(__num__)"';

		$inoperator = acymailing_get('type.operatorsin');
		$inoperator->js = 'onchange="countresults(__num__)"';

		$return = '<div id="filter__num__universalfilter"><label for="universalfilter__num__tablename" style="width: 80px; float: left;">'.acymailing_translation('TABLENAME').' : </label><input onchange="countresults(__num__)" id="universalfilter__num__tablename" size="80" type="text" name="filter[__num__][universalfilter][tablename]" /><br /><label for="universalfilter__num__userfield" style="width: 80px; float: left;"  >User field : </label><input onchange="countresults(__num__)" id="universalfilter__num__userfield" type="text" style="width:200px" name="filter[__num__][universalfilter][userfield]" value="user_id" />';
		$return .= '<br /><label for="universalfilter__num__where" style="width: 80px; float: left;">Where : </label><input onchange="countresults(__num__)" id="universalfilter__num__where" type="text" name="filter[__num__][universalfilter][where]" /> '.$operators->display("filter[__num__][universalfilter][operator]").' <input onchange="countresults(__num__)" type="text" name="filter[__num__][universalfilter][value]" /></div>';

		$return .= '<div id="filter__num__filterquery"><select onchange="countresults(__num__)" name="filter[__num__][filterquery][field]"><option value="email">Email</option><option value="userid" >Joomla User ID</option><option value="subid" >AcyMailing subscriber ID</option></select>'.$inoperator->display("filter[__num__][filterquery][type]").'<br /><textarea onchange="countresults(__num__)" name="filter[__num__][filterquery][query]" style="width: 450px; height: 100px;" placeholder="SELECT email FROM ..."></textarea>';
		$return .= '<br /><label style="float:left;width:100px" for="filterquery__num__dbhost">Database Host</label> <input type="text" id="filterquery__num__dbhost" name="filter[__num__][filterquery][dbhost]" />';
		$return .= '<br /><label style="float:left;width:100px" for="filterquery__num__dbname">Database Name</label> <input type="text" id="filterquery__num__dbname" name="filter[__num__][filterquery][dbname]" />';
		$return .= '<br /><label style="float:left;width:100px" for="filterquery__num__dbuser">Username</label> <input type="text" id="filterquery__num__dbuser" name="filter[__num__][filterquery][dbuser]" />';
		$return .= '<br /><label style="float:left;width:100px" for="filterquery__num__dbpass">Password</label> <input readonly="readonly" onmouseover="this.removeAttribute(\'readonly\');" type="password" id="filterquery__num__dbpass" name="filter[__num__][filterquery][dbpass]" />';
		$return .= '</div>';

		//Only if
		$type['onlyif'] = 'Conditional execution';
		$return .= '<div id="filter__num__onlyif">Select users only if there is at least one value matching the query:<br /><label for="onlyif__num__tablename" style="width: 80px; float: left;">'.acymailing_translation('TABLENAME').' : </label><input onchange="countresults(__num__)" id="onlyif__num__tablename" size="80" type="text" name="filter[__num__][onlyif][tablename]" />';
		$return .= '<br /><label for="onlyif__num__where" style="width: 80px; float: left;">Where : </label><input onchange="countresults(__num__)" id="onlyif__num__where" type="text" name="filter[__num__][onlyif][where]" /> '.$operators->display("filter[__num__][onlyif][operator]").' <input onchange="countresults(__num__)" type="text" name="filter[__num__][onlyif][value]" /></div>';


		$return .= '<div id="filter__num__import">';

		$importoptions = array();
		$importoptions[] = acymailing_selectOption('', 'Do not update the field if the user already exists');
		$importoptions[] = acymailing_selectOption('updateifempty', 'Update the field if the user does not already have a value for it');
		$importoptions[] = acymailing_selectOption('alwaysupdate', 'Update the field regardless its current value');

		$js = "function onAcyDisplayFilter_import(num){
				if(document.getElementById('filterimport'+num+'dbhost').value.length > 0){
					display = 'none';
				}else{
					display = 'table-cell';
				}
				elements = document.getElementsByClassName('importoption');
				for (var i = 0; i < elements.length; i++) {
					elements[i].style.display = display;
				}
			}";
		acymailing_addScript(true, $js);


		$subfields = acymailing_getColumns('#__acymailing_subscriber');
		$return .= '<fieldset><legend>Select</legend><table class="admintable" cellspacing="1">';
		foreach($subfields as $oneField => $fieldtype){
			if(in_array($oneField, array('subid', 'confirmed', 'enabled', 'key', 'accept', 'html', 'created', 'confirmed_date', 'confirmed_ip', 'lastopen_date', 'lastclick_date', 'lastopen_ip', 'lastsent_date'))) continue;
			$return .= '<tr><td class="acykey">'.$oneField.'</td><td><input style="width:200px" type="text" name="filter[__num__][import]['.$oneField.']" />'.(($oneField == 'email') ? '*' : '').'</td><td class="importoption">'.($oneField == 'email' ? '' : acymailing_select($importoptions, 'filter[__num__][import][importoption]['.$oneField.']', 'class="inputbox" size="1"', 'value', 'text', '')).'</td></tr>';
		}
		$return .= '</table></fieldset>';
		$return .= '<fieldset><legend>From</legend><table class="admintable" cellspacing="1"><tr><td class="acykey"><label for="filterimport__num__tablename">'.acymailing_translation('TABLENAME').'</label></td><td><input type="text" id="filterimport__num__tablename" name="filter[__num__][import][tablename]" size="80" />*</td></tr>';
		$return .= '<tr><td class="acykey"><label for="filterimport__num__dbname">Database Name</label></td><td><input type="text" id="filterimport__num__dbname" name="filter[__num__][import][dbname]" /></td></tr>';
		$return .= '<tr><td class="acykey"><label for="filterimport__num__dbhost">Database Host</label></td><td><input onchange="onAcyDisplayFilter_import(__num__);" type="text" id="filterimport__num__dbhost" name="filter[__num__][import][dbhost]" /></td></tr>';
		$return .= '<tr><td class="acykey"><label for="filterimport__num__dbuser">Username</label></td><td><input type="text" id="filterimport__num__dbuser" name="filter[__num__][import][dbuser]" /></td></tr>';
		$return .= '<tr><td class="acykey"><label for="filterimport__num__dbpass">Password</label></td><td><input readonly="readonly" onmouseover="this.removeAttribute(\'readonly\');" type="password" id="filterimport__num__dbpass" name="filter[__num__][import][dbpass]" /></td></tr></table>';
		$return .= '</fieldset><fieldset><legend>Where</legend><table class="admintable" cellspacing="1"><tr><td class="acykey"><label for="filterimport__num__where">Where</label></td><td><textarea cols="50" rows="5" id="filterimport__num__where" name="filter[__num__][import][where]" ></textarea></td></tr></table></fieldset>';

		$return .= '</div>';

		//Display an error to extract e-mail addresses
		$return .= '<div id="filter__num__extractemail"><textarea style="width:80%;height:100px;" name="filter[__num__][extractemail][text]" placeholder="Write here your text, we will extract email addresses from it"></textarea></div>';
		return $return;
	}

	function onAcyProcessFilter_extractemail(&$query, $filter, $num){
		$detectEmail = '/'.acymailing_getEmailRegex().'/i';
		preg_match_all($detectEmail, $filter['text'], $results);
		$allEmails = empty($results[0]) ? array('-1') : $results[0];
		$query->where[] = 'sub.email IN ("'.implode('","', $allEmails).'")';
	}

	function onAcyProcessFilter_import(&$query, $filter, $num){
		$import = acymailing_get('helper.import');
		if(empty($filter['dbhost'])){
			$tableName = $filter['tablename'];
			if(!empty($filter['dbname'])) $tableName = $filter['dbname'].'.'.$filter['tablename'];

			$import->tablename = $tableName;
			$newFilter = array();
			$newFilter['query'] = 'SELECT import.`'.$filter['email'].'` FROM '.$tableName.' as import WHERE import.`'.$filter['email'].'` LIKE \'%@%\'';
			$newFilter['type'] = 'IN';
			$newFilter['field'] = 'email';
			$whereCond = '';
			if(!empty($filter['where'])){
				$import->dbwhere[] = $filter['where'];
				$newFilter['query'] .= ' AND '.$filter['where'];
				$whereCond = $filter['where'];
			}

			$importoption = @$filter['importoption'];

			unset($filter['tablename']);
			unset($filter['dbname']);
			unset($filter['dbhost']);
			unset($filter['dbpass']);
			unset($filter['dbuser']);
			unset($filter['where']);
			unset($filter['importoption']);

			$import->equFields = $filter;

			if(!$import->database(true)){
				$query->where[] = '1 = 0';
				return false;
			}

			//We update values based on the importoption...
			if(!empty($importoption)){
				foreach($importoption as $key => $option){
					if(empty($key) || empty($filter[$key])) continue;
					$q = 'UPDATE #__acymailing_subscriber AS sub JOIN '.$tableName.' as ext ON ext.`'.$filter['email'].'` = sub.email';
					$q .= ' SET sub.`'.$key.'` = ext.'.'`'.$filter[$key].'` WHERE 1=1 ';
					if($option == 'updateifempty') $q .= ' AND (sub.`'.$key.'` IS NULL OR sub.`'.$key.'` = "")';
					if(!empty($whereCond)) $q .= ' AND '.$whereCond;
					acymailing_query($q);
				}
			}

			//Ok we imported the contacts... we gonna select them now
			$this->onAcyProcessFilter_filterquery($query, $newFilter, $num);
		}else{
			//We need to create an extra connection
			$conn = mysqli_connect($filter['dbhost'], $filter['dbuser'], $filter['dbpass'], $filter['dbname']);

			if($conn->connect_error){
				acymailing_display('Error connecting to mysqli server '.$filter['dbhost'].' '.$filter['dbname'].': ('.$conn->connect_errno.') '.$conn->connect_error, 'error');
				$query->where[] = '1 = 0';
				return false;
			}

			//Make sure we handle utf8 results.
			mysqli_set_charset($conn, 'utf8');

			//We load users
			$acyFields = array();
			$dbFields = array();
			foreach($filter as $acyfield => $dbfield){
				if(empty($dbfield) OR in_array($acyfield, array('dbname', 'dbhost', 'dbpass', 'dbuser', 'tablename', 'where', 'importoption'))) continue;
				$acyFields[] = $acyfield;
				$dbFields[] = $dbfield;
			}
			$querySelect = 'SELECT '.implode(',', $dbFields).' FROM '.$filter['tablename'];
			$querySelect .= ' WHERE '.$filter['email']." LIKE '%@%' ";
			if(!empty($filter['where'])) $querySelect .= ' AND ('.$filter['where'].')';
			$myQuery = mysqli_query($conn, $querySelect);
			if(!$myQuery){
				acymailing_display('Error executing '.$querySelect.'<br />'.mysqli_error($conn), 'error');
				$query->where[] = '1 = 0';
				return false;
			}
			$importFile = implode(',', $acyFields)."\n";
			while($row = mysqli_fetch_row($myQuery)){
				$importFile .= implode(',', $row)."\n";
			}

			$myQuery->close();
			$conn->close();

			//We import users
			$import->_handleContent($importFile);

			//We add the imported users in the query
			if(!empty($import->allSubid)){
				$query->where[] = 'sub.subid IN ('.implode(',', $import->allSubid).')';
			}else{
				$query->where[] = '1 = 0';
			}
		}
	}

	function onAcyProcessFilterCount_filterquery(&$query, $filter, $num){
		if($filter['query'] == 'SELECT email FROM ...') return;
		$this->onAcyProcessFilter_filterquery($query, $filter, $num);
		return acymailing_translation_sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyProcessFilterCount_onlyif(&$query, $filter, $num){
		if(empty($filter['tablename'])) return;
		$this->onAcyProcessFilter_onlyif($query, $filter, $num);
		if(!empty($this->error)){
			return '<span style="color:red">SQL Error : '.$this->error.'</span>';
		}

		if(isset($query->where['blockfilter']) && $query->where['blockfilter'] == '1=0'){
			return '<span style="color:orange">Filter blocked, no entry found</span>';
		}

		return '<span style="color:green">The filter can be executed, at least one entry found</span>';
	}

	function onAcyProcessFilter_onlyif(&$query, $filter, $num){
		$exeQuery = 'SELECT * FROM '.$filter['tablename'].' as mytable';
		if(!empty($filter['where'])){
			if(strpos($filter['value'], '{time}') !== false){
				$filter['value'] = acymailing_replaceDate($filter['value']);
				if(!is_numeric($filter['value'])) $filter['value'] = strtotime($filter['value']);
				$tablefields = acymailing_getColumns($filter['tablename']);
				$fieldType = $tablefields[$filter['where']];
				if($fieldType == 'datetime'){
					$filter['value'] = strftime('%Y-%m-%d %H:%M:%S', $filter['value']);
				}elseif($fieldType == 'date') $filter['value'] = strftime('%Y-%m-%d', $filter['value']);
			}
			$exeQuery .= ' WHERE '.$query->convertQuery('mytable', $filter['where'], $filter['operator'], $filter['value']);
		}
		$exeQuery .= ' LIMIT 1';

		try{
			$val = acymailing_loadObjectList($exeQuery);
		}catch(Exception $e){
			$val = null;
		}

		if($val === null) $this->error = acymailing_getDBError();

		//We always add a where to avoid the check on the empty filter...
		$query->where['blockfilter'.$num] = empty($val) ? '1=0' : '1=1';
	}

	function onAcyDisplayFilter_filterquery($filter){
		return 'Query : '.$filter['field'].' '.$filter['type'].' '.$filter['query'];
	}

	function onAcyProcessFilter_filterquery(&$query, $filter, $num){
		if(empty($filter['query'])) return;

		if(strpos($filter['query'], '{time') != false){
			$toReplace = array();
			$found = preg_match_all('#(?:{|%7B)time(.*)(?:}|%7D)#Ui', $filter['query'], $results);
			if($found){
				foreach($results[0] as $i => $tag){
					if(!empty($results[1][$i])){
						if(substr($results[1][$i], 0, 1) == '-'){
							$val = time() - substr($results[1][$i], 1);
						}else $val = time() + substr($results[1][$i], 1);
					}else{
						$val = time();
					}
					$toReplace[$tag] = $val;
				}
				$filter['query'] = str_replace(array_keys($toReplace), $toReplace, $filter['query']);
			}
		}

		if(empty($filter['dbhost'])){
			//We do a "WHERE sub.xxx in (SELECT...)"
			if(!in_array($filter['field'], array('subid', 'email', 'userid'))) $filter['field'] = 'userid';
			$myQuery = 'sub.'.$filter['field'];
			$myQuery .= $filter['type'] == 'IN' ? ' IN ' : ' NOT IN ';
			$myQuery .= '('.$filter['query'].')';

			$query->where[] = $myQuery;

			return true;
		}

		//We need to load from another database apparently...
		$conn = mysqli_connect($filter['dbhost'], $filter['dbuser'], $filter['dbpass'], $filter['dbname']);
		if($conn->connect_error){
			acymailing_display('Error connecting to mysqli server '.$filter['dbhost'].' '.$filter['dbname'].': ('.$conn->connect_errno.') '.$conn->connect_error, 'error');
			$query->where[] = '1 = 0';
			return false;
		}

		//Ok, we have another database opened with the right database selected... let's execute the query then
		$myQuery = mysqli_query($conn, $filter['query']);
		if(!$myQuery){
			acymailing_display('Error executing '.$filter['query'].'<br />'.mysqli_error($conn), 'error');
			$query->where[] = '1 = 0';
			return false;
		}

		$loadArrayResult = array();
		while($row = mysqli_fetch_row($myQuery)){
			$loadArrayResult[] = $row[0];
		}

		$myQuery->close();
		$conn->close();

		if(empty($loadArrayResult)) $loadArrayResult = array('-1');
		$myQuery = $filter['field'] == 'email' ? 'sub.email' : 'sub.userid';
		$myQuery .= $filter['type'] == 'IN' ? ' IN' : ' NOT IN';
		$myQuery .= " ('".implode("','", $loadArrayResult)."')";

		$query->where[] = $myQuery;
	}

	function onAcyProcessFilterCount_universalfilter(&$query, $filter, $num){
		$this->onAcyProcessFilter_universalfilter($query, $filter, $num);
		return acymailing_translation_sprintf('SELECTED_USERS', $query->count());
	}

	function onAcyDisplayFilter_universalfilter($filter){
		$where = empty($filter['where']) ? '' : ' : '.$filter['where'].' '.$filter['operator'].' '.$filter['value'];
		return 'Universal Filter '.$filter['tablename'].' '.$where;
	}


	function onAcyProcessFilter_universalfilter(&$query, $filter, $num){
		if(empty($filter['tablename'])){
			$listTables = acymailing_getTableList();
			acymailing_enqueueMessage(acymailing_translation_sprintf('SPECIFYTABLE', implode(' | ', $listTables)), 'notice');
			$query->where[] = '1 = 0';
			return false;
		}

		$tablefields = acymailing_getColumns($filter['tablename']);
		$fields = array_keys($tablefields);

		if(empty($fields)){
			$listTables = acymailing_getTableList();
			acymailing_enqueueMessage(acymailing_translation_sprintf('SPECIFYTABLE', implode(' | ', $listTables)), 'notice');
			$query->where[] = '1 = 0';
			return false;
		}

		//Check the user field...
		if(empty($filter['userfield']) || !in_array($filter['userfield'], $fields)){
			acymailing_enqueueMessage(acymailing_translation_sprintf('SPECIFYFIELD', @$filter['userfield'], implode(' | ', $fields)), 'notice');
			$query->where[] = '1 = 0';
			return false;
		}

		//check the where
		if(!empty($filter['where']) && !in_array($filter['where'], $fields)){
			acymailing_enqueueMessage(acymailing_translation_sprintf('SPECIFYFIELD', $filter['where'], implode(' | ', $fields)), 'notice');
			$query->where[] = '1 = 0';
			return false;
		}

		if(in_array(strtolower($filter['userfield']), array('email', 'e-mail', 'mail', 'e_mail', 'courriel'))){
			$acyoperator = 'email';
		}else{
			$acyoperator = 'userid';
		}

		$query->leftjoin['universalfiltertable'.$num] = $filter['tablename'].' AS universalfiltertable'.$num.' ON universalfiltertable'.$num.'.'.$filter['userfield'].' = sub.'.$acyoperator;

		if(!empty($filter['where'])){
			if(strpos($filter['value'], '{time}') !== false){
				$filter['value'] = acymailing_replaceDate($filter['value']);
				if(!is_numeric($filter['value'])) $filter['value'] = strtotime($filter['value']);
				$fieldType = $tablefields[$filter['where']];
				if($fieldType == 'datetime'){
					$filter['value'] = strftime('%Y-%m-%d %H:%M:%S', $filter['value']);
				}elseif($fieldType == 'date') $filter['value'] = strftime('%Y-%m-%d', $filter['value']);
			}

			$query->where[] = $query->convertQuery('universalfiltertable'.$num, $filter['where'], $filter['operator'], $filter['value']);
		}else{
			//WE set IS NOT NULL by default otherwise it's useless to do the join
			$query->where[] = 'universalfiltertable'.$num.'.'.$filter['userfield'].' IS NOT NULL';
		}
	}

}//endclass