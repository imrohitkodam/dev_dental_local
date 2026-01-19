<?php
// namespace administrator\components\com_jmap\models;
/**
 * @package JMAP::PINGOMATIC::administrator::components::com_jmap
 * @subpackage models
 * @author Joomla! Extensions Store
 * @copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Pingomatic links model concrete implementation <<testable_behavior>>
 *
 * @package JMAP::PINGOMATIC::administrator::components::com_jmap
 * @subpackage models
 * @since 2.0
 */
class JMapModelPingomatic extends JMapModel {
	/**
	 * Build list entities query
	 * 
	 * @access protected
	 * @return string
	 */
	protected function buildListQuery() {
		// WHERE
		$where = array ();
		$whereString = null;
		$orderString = null;

		// TEXT FILTER
		if ($this->state->get ( 'searchword' )) {
			$where [] = "(s.title LIKE " . $this->_db->quote("%" . $this->state->get ( 'searchword' ) . "%") . ") OR" .
						"(s.blogurl LIKE " . $this->_db->quote("%" . $this->state->get ( 'searchword' ) . "%") . ") OR" .
						"(s.rssurl LIKE " . $this->_db->quote("%" . $this->state->get ( 'searchword' ) . "%") . ")";
		}
		
		if($this->state->get('fromPeriod')) {
			$where[] = "\n s.lastping > " . $this->_db->quote(($this->state->get('fromPeriod')));
		}
		
		if($this->state->get('toPeriod')) {
			$where[] = "\n s.lastping < " . $this->_db->quote(date('Y-m-d', strtotime("+1 day", strtotime($this->state->get('toPeriod')))));
		}
		
		if (count ( $where )) {
			$whereString = "\n WHERE " . implode ( "\n AND ", $where );
		}
		
		// ORDERBY
		if ($this->state->get ( 'order' )) {
			$orderString = "\n ORDER BY " . $this->state->get ( 'order' ) . " ";
		}
		
		// ORDERDIR
		if ($this->state->get ( 'order_dir' )) {
			$orderString .= $this->state->get ( 'order_dir' );
		}
		
		$query = "SELECT s.*, u.name AS editor" . 
				 "\n FROM #__jmap_pingomatic AS s" .
				 "\n LEFT JOIN #__users AS u" .
				 "\n ON s.checked_out = u.id" . 
				 $whereString . $orderString;
		return $query;
	}

	/**
	 * Main get data methods
	 * 
	 * @access public
	 * @return Object[]
	 */
	public function getData() {
		// Build query
		$query = $this->buildListQuery ();
		$this->_db->setQuery ( $query, $this->getState ( 'limitstart' ), $this->getState ( 'limit' ) );
		try {
			$result = $this->_db->loadObjectList ();
			if($this->_db->getErrorNum()) {
				throw new JMapException(JText::_('COM_JMAP_ERROR_RETRIEVING_PINGOMATIC_LINKS') . $this->_db->getErrorMsg(), 'error');
			}
		} catch (JMapException $e) {
			$this->app->enqueueMessage($e->getMessage(), $e->getErrorLevel());
			$result = array();
		} catch (Exception $e) {
			$jmapException = new JMapException($e->getMessage(), 'error');
			$this->app->enqueueMessage($jmapException->getMessage(), $jmapException->getErrorLevel());
			$result = array();
		}
		return $result;
	}
	
	/**
	 * Return select lists used as filter for editEntity
	 *
	 * @access public
	 * @param Object& $record
	 * @return array
	 */
	public function getLists($record = null) {
		$lists = array ();

		// Common services
		$lists ['ajs_google'] = JHtml::_ ( 'select.booleanlist', 'ajs_google', 'data-host="https://www.google.com/search?q="', $record->services->get('ajs_google', 1));
		$lists ['ajs_bing'] = JHtml::_ ( 'select.booleanlist', 'ajs_bing', 'data-host="https://www.bing.com/search?q="', $record->services->get('ajs_bing', 1));
		$lists ['ajs_yandex'] = JHtml::_ ( 'select.booleanlist', 'ajs_yandex', 'data-host="https://yandex.com/search/?text="', $record->services->get('ajs_yandex', 1));
		$lists ['ajs_entireweb'] = JHtml::_ ( 'select.booleanlist', 'ajs_entireweb', 'data-host="https://www.entireweb.com/web?q="', $record->services->get('ajs_entireweb', 1));
		$lists ['ajs_viesearch'] = JHtml::_ ( 'select.booleanlist', 'ajs_viesearch', 'data-host="https://viesearch.com/?q="', $record->services->get('ajs_viesearch', 1));
		$lists ['ajs_webcrawler'] = JHtml::_ ( 'select.booleanlist', 'ajs_webcrawler', 'data-host="https://www.webcrawler.com/serp?q="', $record->services->get('ajs_webcrawler', 1));
		$lists ['ajs_yahoo'] = JHtml::_ ( 'select.booleanlist', 'ajs_yahoo', 'data-host="https://search.yahoo.com/search?p="', $record->services->get('ajs_yahoo', 1));
		$lists ['ajs_duckduckgo'] = JHtml::_ ( 'select.booleanlist', 'ajs_duckduckgo', 'data-host="https://duckduckgo.com/?q="', $record->services->get('ajs_duckduckgo', 1));
		$lists ['ajs_ask'] = JHtml::_ ( 'select.booleanlist', 'ajs_ask', 'data-host="https://www.ask.com/web?q="', $record->services->get('ajs_ask', 1));
		
		// Specialized services
		$lists ['chk_blogs'] = JHtml::_ ( 'select.booleanlist', 'chk_blogs', null, $record->services->get('chk_blogs', 1));
		$lists ['chk_feedburner'] = JHtml::_ ( 'select.booleanlist', 'chk_feedburner', null, $record->services->get('chk_feedburner', 1));
		$lists ['chk_tailrank'] = JHtml::_ ( 'select.booleanlist', 'chk_tailrank', null, $record->services->get('chk_tailrank', 1));
		$lists ['chk_superfeedr'] = JHtml::_ ( 'select.booleanlist', 'chk_superfeedr', null, $record->services->get('chk_superfeedr', 1));
		
		return $lists;
	}

	/**
	 * Get Pingomatic server stats by Alexa
	 *
	 * @access public
	 * @return mixed HTML code
	 */
	public function getPingomaticStats(JMapHttp $httpClient) {
		return '<img src="https://traffic.alexa.com/graph?&amp;o=f&amp;c=1&amp;y=q&amp;b=ffffff&amp;n=666666&amp;w=800&amp;h=320&amp;r=12m&amp;u=pingomatic.com" style="width:70%;margin:auto;display:block;margin-top:88px"/>';
	}
}