<?php
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Router\Route;

class JticketingViewRecurringEvents extends HtmlView
{
    public $recurringEvents;
    public $attendeeId;
	public $tmpl;
    /**
     * Display function to render the view with recurring events data.
     *
     * @param   string  $tpl  The name of the template file to parse.
     * @return  bool|void     Returns false if attendee_id is missing; otherwise, renders the view.
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        $jinput = $app->input;
		$this->tmpl  = $jinput->get('tmpl', '');
    
        $this->attendeeId = $jinput->get('attendee_id', 0, 'INT');

        if (empty($this->attendeeId)) {
            $app->enqueueMessage(JText::_('COM_JTICKETING_ERROR_NO_ATTENDEE_ID'), 'error');
            return false;
        }
		JLoader::register('JticketingModelRecurringEvents', JPATH_ADMINISTRATOR . '/components/com_jticketing/models/recurringevent.php');
		$recurringModel = new JticketingModelRecurringEvents();

        $this->recurringEvents = $recurringModel->getRecurringEvents($this->attendeeId);
       
        if (!is_array($this->recurringEvents)) {
            $this->recurringEvents = [];
        }
        $errors = $this->get('Errors');
        if (!is_array($errors)) {
            $errors = []; 
        }
        if (count($errors)) {
            throw new Exception(implode("\n", $errors));
        }
    
        parent::display($tpl);
    }
}
