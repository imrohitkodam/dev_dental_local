<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'support_ticket.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'support_canned.php');
require_once (JPATH_SITE.DS.'components'.DS.'com_fss'.DS.'helper'.DS.'parser.php');

class FSJ_FSSADDViewCanned_Form extends FSJ_FSSADDViewCanned
{
	function display($tpl = NULL)
	{
		$this->getReply();
		$this->loadTicket();
		$this->makeForm();

		$this->buildJSKeys();

		if ($this->form_data)
		{
			$this->makePreview();
		}

		$this->_display();
	}	
	
	function buildJSKeys()
	{
		$this->ticket_vars = array();
		$this->ticket->forParser($this->ticket_vars, true, false);
	}
	
	function getReply()
	{
		$db = JFactory::getDBO();
		$sql = "SELECT * FROM #__fsj_fssadd_canned WHERE id = " . $db->escape(JRequest::getInt('canned'));
		$db->setQuery($sql);

		$this->canned = $db->loadObject();
	}

	function loadTicket()
	{
		$this->ticket = new SupportTicket();
		$this->ticket->load(JRequest::getVar('ticket'), "force");
		$this->ticket->loadAll();
	}

	function makeForm()
	{
		require_once(JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjcfdisp.php');
		JForm::addFieldPath(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field');

		// load previously submitted data
		$params = JRequest::getVar('jform');
		$value = json_encode($params['data_form']);
		$this->form_data = $params['data_form'];

		$xml = "<field
					tpye='fsjcfdisp'
					label='fsjcfdisp'
					fsjcfdisp_tabs='tab'
					fsjcfdisp_deftab='" . JText::_('CANNED_FORM_DATA') . "'
					fsjcfdisp_sql='SELECT f.*, f.alias as name FROM #__fsj_fssadd_canned_field as f WHERE f.canned_id = " . $this->canned->id . " ORDER BY ordering'
					 />";

		$element = new SimpleXMLElement($xml);
		$this->form = new JFormFieldFSJCFDisp();
		$this->form->setup($element, $value);
	}

	function makePreview()
	{
		$template = array();
		$template['subject'] = $this->ticket->title;
		$template['body'] = "";
		$template['tmpl'] = '';
		$template['ishtml'] = 0;

		$parser = new FSSParser();
		$this->ticket->forParser($parser->vars, true, false);
		
		//FSS_EMail::ParseTemplate($template, $this->ticket_array, $this->ticket->title, "", true, true);
		//$vars = FSS_EMail::$last_vars;
		
		foreach ($this->form_data as $var => $value)
		{
			$parser->setVar($var, $value);
		}
		$parser->setVar("signature", SupportCanned::AppendSig(SupportUsers::getSetting("default_sig"), $this->ticket));

		if ($this->canned->parsetype == 1) // smarty
		{
			$this->subject = $this->parseSmarty($this->canned->subject, 1, $parser->vars);
			$this->preview = $this->parseSmarty($this->canned->description, 0, $parser->vars);
		} else {
			$this->preview = $parser->ParseInt($this->canned->description);
			$this->subject = $parser->ParseInt($this->canned->subject);
		}
	}

	function parseSmarty($template, $isSubject, $vars)
	{

		$smarty = new Smarty();
		$smarty->setCompileDir(JPATH_CACHE . DS . 'fsj' . DS . 'smarty' . DS . 'fssadd');
		$smarty->assign("base", JURI::root());
		$smarty->assign("imgbase", JURI::root() . "images/");
		$smarty->assign("user", JFactory::getUser());
		$smarty->assign("doc", JFactory::getDocument());
		$smarty->assign("db", JFactory::getDBO());
		$smarty->assign("app", JFactory::getApplication());

		foreach ($vars as $var => $value)
		{
			$template = str_replace('{'.$var.'}', '{$' . $var . '}', $template);
			$smarty->assign($var, $value);
		}	
		
		$smarty->registerResource('canned', new Smarty_Resource_FSJCanned($template));

		try {
			return $smarty->fetch("canned:tpl_" . $this->canned->id. "_" . $isSubject);
		} catch (Exception $e) {
			$error = $e->getMessage();
			$this->SetError(html_entity_decode($error));
			print_p($error);
		} 

		return $template;
	}
}

class Smarty_Resource_FSJCanned extends Smarty_Resource_Custom {
	
	public function __construct($template) {
		$this->template = $template;
	}
	
	protected function fetch($name, &$source, &$mtime)
	{
		$source = $this->template;
		$mtime = time();
	}
}
