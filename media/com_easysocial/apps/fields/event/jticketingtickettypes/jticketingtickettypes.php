<?php
/**
 * @version    SVN: <svn_id>
 * @package    JTicketing
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */
defined('_JEXEC') or die(';)');
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/**
 * Class for easysocial to add ticket types
 *
 * @package     JTicketing
 * @subpackage  component
 * @since       1.0
 */
class SocialFieldsEventJticketingtickettypes extends SocialFieldItem
{
	public $event;

	public $element;

	public $group;

	public $field;

	public $params;

	public $value;

	public $inputName;

	/**
	 * Class constructor.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function __construct()
	{
		// Load language file for plugin

		$tjStrapperPath = JPATH_SITE . '/media/techjoomla_strapper/tjstrapper.php';

		if (File::exists($tjStrapperPath))
		{
			require_once $tjStrapperPath;
			TjStrapper::loadTjAssets('com_jticketing');
		}

		HTMLHelper::script(Juri::root() . 'media/com_jticketing/integrations/js/integrations.js');

		parent::__construct();
	}

	/**
	 * function to validate Integration
	 *
	 * @return  boolean  true or false
	 *
	 * @since   1.0
	 */
	public function onJtValidateIntegration()
	{
		$com_params  = ComponentHelper::getParams('com_jticketing');
		$integration = $com_params->get('integration');

		if ($integration != 4)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get Contry list from tjfields
	 *
	 * @param   object  &$post          post data
	 * @param   object  &$registration  Registration data
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onRegister(&$post, &$registration)
	{
		$this->getCustomFields();
		$app  = Factory::getApplication();
		$site = $app->isClient("site");

		$document   = Factory::getDocument();
		HTMLHelper::_('stylesheet', 'media/com_jticketing/css/jticketing.css');

		return $this->display();
	}

	/**
	 * Called on edit easysocial Event
	 *
	 * @param   object  &$post  post data
	 * @param   object  &$user  User object
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onEdit(&$post, &$user)
	{
		$this->getCustomFields();
		$app  = Factory::getApplication();
		$site = $app->isClient("site");

		$document   = Factory::getDocument();
		HTMLHelper::_('stylesheet', 'media/com_jticketing/css/jticketing.css');

		$this->defineErrorMsg();

		return $this->display();
	}

	/**
	 * Called on display easysocial Event
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onSample()
	{
		$app = Factory::getApplication();
		$site = $app->isClient("site");

		$document   = Factory::getDocument();
		HTMLHelper::_('stylesheet', 'media/com_jticketing/css/jticketing.css');

		return $this->display();
	}

	/**
	 * Called on before saving easysocial Event
	 *
	 * @param   object  &$post  post data
	 * @param   object  &$user  User object
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onRegisterBeforeSave(array &$post, &$user)
	{
	}

	/**
	 * Called on after saving easysocial Event
	 *
	 * @param   object  &$post  post data
	 * @param   object  &$user  User object
	 *
	 * @return  false
	 *
	 * @since   1.0
	 */
	public function onRegisterAfterSave(array &$post, &$user)
	{
		if (!$this->onJtValidateIntegration())
		{
			return false;
		}

		$jinput     = Factory::getApplication()->input;
		$fieldValue = $jinput->get('allowfield');

		$this->onLoadJTclasses();
		$jteventHelper = new jteventHelper;
		$eventid       = $user->id;
		$eventCreator  = '';

		// Pass 4 as easysocial integration in backend
		$jteventHelper->saveEvent($eventid, '4', $eventCreator, $fieldValue);
	}

	/**
	 * Called on after edit easysocial Event
	 *
	 * @param   object  &$post  post data
	 * @param   object  &$user  User object
	 *
	 * @return  false
	 *
	 * @since   1.0
	 */
	public function onEditAfterSave(array &$post, &$user)
	{
		if (!$this->onJtValidateIntegration())
		{
			return false;
		}

		$jinput = Factory::getApplication()->input;
		$fieldValue = $jinput->get('allowfield');

		$this->onLoadJTclasses();
		$jteventHelper = new jteventHelper;
		$eventid       = $user->id;
		$eventCreator  = '';

		// Pass 4 as easysocial integration in backend
		$jteventHelper->saveEvent($eventid, '4', $eventCreator, $fieldValue);
	}

	/**
	 * Called on display easysocial Event
	 *
	 * @param   object  &$post  post data
	 * @param   object  &$user  User object
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onRegisterAfterSaveFields(array &$post, &$user)
	{
	}

	/**
	 * Called on display easysocial Event
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function onLoadJTclasses()
	{
		require_once  JPATH_SITE . '/components/com_jticketing/includes/jticketing.php';
		require_once JPATH_SITE . '/components/com_tjvendors/includes/tjvendors.php';

		// Load all required helpers.
		$jticketingmainhelperPath = JPATH_ROOT . '/components/com_jticketing/helpers/main.php';

		if (!class_exists('jticketingmainhelper'))
		{
			JLoader::register('jticketingmainhelper', $jticketingmainhelperPath);
			JLoader::load('jticketingmainhelper');
		}

		$jticketingfrontendhelper = JPATH_ROOT . '/components/com_jticketing/helpers/frontendhelper.php';

		if (!class_exists('jticketingfrontendhelper'))
		{
			JLoader::register('jticketingfrontendhelper', $jticketingfrontendhelper);
			JLoader::load('jticketingfrontendhelper');
		}

		$jteventHelperPath = JPATH_ROOT . '/components/com_jticketing/helpers/event.php';

		if (!class_exists('jteventHelper'))
		{
			JLoader::register('jteventHelper', $jteventHelperPath);
			JLoader::load('jteventHelper');
		}

		$this->defineErrorMsg();
	}

	/**
	 * Get Custom fields
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	public function getCustomFields()
	{
		if (!$this->onJtValidateIntegration())
		{
			return false;
		}

		$com_params = ComponentHelper::getParams('com_jticketing');
		$attendeeCheckoutConfig = $com_params->get('collect_attendee_info_checkout');
		$this->accessLevel = $com_params->get('show_access_level');
		$document = Factory::getDocument();
		HTMLHelper::_('stylesheet', 'media/com_jticketing/css/jticketing.css');

		if (!$this->accessLevel)
		{
		?>
		<style>
		.subform-repeatable-group .form-group:last-child{
		display: none;
		}
		</style>
		<?php
		}

		$app                      = Factory::getApplication();
		$site                     = $app->isClient("site");
		$input                    = $app->input;
		$lang                     = Factory::getLanguage();
		$extension                = 'com_jticketing';
		$base_dir                 = JPATH_ADMINISTRATOR;
		$event_id                 = $input->get('id', '', 'INTEGER');
		$lang->load($extension, $base_dir);
		$this->onLoadJTclasses();
		$jticketingfrontendhelper = new jticketingfrontendhelper;
		$attendeeGlobalFields     = array();
		$attendeeCoreFields       = JT::model('attendeecorefields', array('ignore_request' => true));
		$attendeeCoreFields->setState('filter.state', 1);
		$attendeeGlobalFields     = $attendeeCoreFields->getItems();
		$ticketTitle              = Text::_('COM_JTICKETING_INTEGRATION_TITLE_BAR_TICKET_TYPES');
		$seperationTicketBar      = '<div class="es-snackbar"><h1 class="es-snackbar__title"> ' . $ticketTitle . ' </h1></div>';
		$userId                   = Factory::getUser()->id;
		$vendor                   = Tjvendors::vendor()->loadByUserId($userId, 'com_jticketing');
		$emailCheck               = $vendor->getPaymentConfig() ? true : false;
		$vendor_id                = $vendor->getId();
		$integration              = JT::getIntegration();
		$eventObj                 = JT::event($event_id, $integration);
		$xrefId                   = $eventObj->integrationId;
		$params                   = JT::config();
		$handle_transactions      = $params->get('handle_transactions');
		$adaptivePayment          = $params->get('gateways');
		$customTicketFields       = $eventObj->getCustomFieldTypes('ticketFields');
		$enableTicketing          = $eventObj->isTicketingEnabled();
		$display                  = "display: none";
		$checked                  = "";

		if ($event_id && !empty($enableTicketing))
		{
			$checked = "checked";
			$display = "display: block";
		}

		echo '<div class="o-form-group">
				<label class="o-control-label" for="allowfield">
					' . Text::_("COM_JTICKETING_ENABLE_TICKETING_FOR_THIS_EVENT") . '
				</label>
				<div class="o-control-input" data-content="">
					<div class="o-onoffswitch" data-es-toggler="">
						<div>
							<input type="checkbox" name="allowfield" id="allowfield"
							class="o-onoffswitch__checkbox"  value=' . $enableTicketing . ' data-toggler-checkbox="" ' . $checked . '>
							<label class="o-onoffswitch__label" for="allowfield"></label>

							<input type="hidden" class="ticketFields" name="allowfield" value=' . $enableTicketing . ' onChange="valid.fieldDisplay()">
						</div>
					</div>
					<div class="o-help-block">
						<div class="">
							<strong>' .
							Text::_("COM_JTICKETING_ENABLE_TICKETING_NOTE") . '</strong>' .
							Text::_("COM_JTICKETING_ENABLE_TICKETING_FOR_THIS_EVENT_DESC") .
							'</div>
					</div>
				</div>
			</div>';

		echo '<div id="fieldTicket" style="' . $display . '">
				<div class="jticketing-wrapper tjBs3">';
		echo $seperationTicketBar;

		if ($site)
		{
				if ($emailCheck == "true" && ($handle_transactions == 1 || in_array('adaptive_paypal', $adaptivePayment)))
				{
				?>
					<div class="alert alert-warning">
					<?php
						if ($site)
						{
							$link = 'index.php?option=com_tjvendors&view=vendor&layout=profile&client=com_jticketing';
						}
						else
						{
							$link = 'index.php?option=com_tjvendors&view=vendor&layout=update&client=com_jticketing';
						}

						echo Text::_('COM_JTICKETING_PAYMENT_DETAILS_ERROR_MSG1');?>
							<a href="<?php echo Route::_($link . '&vendor_id=' . $vendor_id, false);?>" target="_blank">
							<?php echo Text::_('COM_JTICKETING_VENDOR_FORM_LINK'); ?></a>
						<?php echo Text::_('COM_JTICKETING_PAYMENT_DETAILS_ERROR_MSG2');?>
						</div>
				<?php
				}

			echo '	<div class="jticketing_params_container">
						<div>' . $customTicketFields . '</div>
					</div>
				</div>';
		}
		else
		{
			echo $customTicketFields;
		}

		if ($attendeeCheckoutConfig == 1)
		{
			$attendeeTitle = JTExt::_('COM_JTICKETING_INTEGRATION_TITLE_BAR_ATTENDEE_FIELDS');
			$seperationAttendeeBar = '<div class="es-snackbar"><h1 class="es-snackbar__title">' . $attendeeTitle . '</h1></div>';
			echo $seperationAttendeeBar;
		?>
				<table class="table table-bordered table-responsive">
				<p></p>
				<thead>
					<tr>
						<th><?php echo Text::_('COM_JTICKETING_INTEGRATION_FIELD_TITLE'); ?></th>
						<th><?php echo Text::_('COM_JTICKETING_INTEGRATION_FIELD_TYPE'); ?></th>
					</tr>
				</thead>
			<?php
				foreach ($attendeeGlobalFields as $key => $field)
				{
					?>
					<tr class="row<?php echo $key % 2; ?>">
							<td >
								<?php echo htmlspecialchars(Text::_($field->label), ENT_COMPAT, 'UTF-8'); ?>
							</td>
							<td >
								<?php echo $field->type; ?>
							</td>
						</tr>
				<?php
				}
				?>
				</table>
		<?php
			$customAttendeeFields = JT::event($event_id, 'com_easysocial')->getCustomFieldTypes('attendeeFields');

			if ($site)
			{
				echo '<div class="jticketing-wrapper">
				<div class="jticketing_params_container">
					<div>' . $customAttendeeFields . '</div>
				</div>
			</div>';
			}
			else
			{
				echo $customAttendeeFields;
			}
		}

		echo "</div>";

		if ($app->isClient("administrator"))
		{
			echo "</div>";
		}
	}

	/**
	 * Define All Language Constant
	 *
	 * @return  void
	 *
	 * @since   3.2.0
	 */
	public function defineErrorMsg()
	{
		$document   = Factory::getDocument();
		$seatCountMsg = Text::_('COM_JTICKETING_JOMSOCIAL_EVENT_TICKET_TYPES_SAVE_ERROR');
		$document->addScriptDeclaration('var seatCountMsg= "' . $seatCountMsg . '";');
		$eventDateMsg = Text::_('COM_JTICKETING_EASYSOCIAL_DATE_VALIDATION');
		$document->addScriptDeclaration('var eventDateMsg= "' . $eventDateMsg . '";');
		
		Text::script('COM_JTICKETING_ENDDATE_GREATER_STARTDATE_VALIDATION');
		Text::script('COM_JTICKETING_STARTDATE_LESS_ENDDATE_VALIDATION');
		Text::script('COM_JTICKETING_STARTDATE_VALIDATION');
		Text::script('COM_JTICKETING_ENDDATE_VALIDATION');
	}
}
