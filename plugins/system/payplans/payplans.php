<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$app = JFactory::getApplication();
// Do not proceed if this is on CLI environment
if ($app->isClient('cli')) {
	return;
}

jimport('joomla.filesystem.file');

$input = $app->input;
$option = $input->get('option', '', 'default');
$install = $input->get('setup', false, 'bool');

// check if currently is payplans installation.
$installationFile = JPATH_ROOT . '/tmp/payplans.installation';

// Do not load payplans when component is com_installer
if ($option === 'com_installer' || JFile::exists($installationFile) || $install) {
	return true;
}

// Do not load payplans when component is form2content as it's also using the same class name PP
if ($option === 'com_form2content') {
	return;
}

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';
$exists = JFile::exists($file);

if (!$exists) {
	return;
}

require_once($file);

// Initialize foundry
PP::initFoundry();

class plgSystemPayplans extends PPPlugins
{
	public function __construct($event, $options = [])
	{
		parent::__construct($event, $options);

		$this->app = JFactory::getApplication();
		$this->my = PP::user();
		$this->input = $this->app->input;
	}

	/**
	 * Triggered during Joomla's onAfterRoute trigger
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterRoute()
	{
		$uId = PP::session()->get('PP_ACTIVATION_REDIRECTION');

		if ($uId) {

			// clear the session first
			PP::session()->clear('PP_ACTIVATION_REDIRECTION');

			// Get the proper url from config
			$config = PP::config();
			$redirectUrl = $config->get('activation_redirect_url', '');

			if ($redirectUrl) {

				// check if the url belong to payplans
				if (stristr($redirectUrl, 'com_payplans') !== false) {
					$redirectUrl = PPR::_($redirectUrl, false);
				} else {
					$redirectUrl = JRoute::_($redirectUrl, false);
				}

				$this->app->redirect($redirectUrl);
				return;
			}
		}

		// Let us do access check
		$this->checkAccess();

		// Process registrations if needed
		if (!PP::isFromAdmin() && !$this->my->id) {
			$registration = PP::registration();
			$registration->onAfterRoute();
		}

		$option = $this->input->get('option', '', 'default');
		$view = $this->input->get('view', '', 'cmd');
		$task = $this->input->get('task', '', 'cmd');

		$doc = JFactory::getDocument();

		if ($doc->getType() != 'html') {
			return;
		}

		if ($option != 'com_payplans') {
			return;
		}

		// from Payplans 2.0 payment notification will be
		// processed on payment=>notify rather then order=>notify
		if (($view == 'order') && ($task =='notify')) {
			$this->input->set('view', 'payment');
		}

		return true;
	}

	/**
	 * Triggered by Joomla after dispatching
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterDispatch()
	{
		$option = $this->input->get('option', '', 'string');
		$view = $this->input->get('view', '', 'string');

		if (!PP::isFromAdmin() && $option === 'com_payplans' && $this->my->id) {
			$cron = PP::cron();

			// Only show this error on the dashboard view
			if (!$cron->hasBeenRunning() && $view == 'dashboard') {
				PP::info()->set('COM_PAYPLANS_CRON_IS_NOT_RUNNING_PROPERLY', 'error');
			}
		}

		$this->initializePurchasedPopup();

		return true;
	}

	/**
	 * Initialize the purchased popup for the frontend
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	private function initializePurchasedPopup()
	{
		if (PP::isFromAdmin()) {
			return;
		}

		$config = PP::config();
		$purchasedPopupEnabled = $config->get('enable_purchased_popup') || PP::responsive()->isMobile() && !$config->get('purchased_popup_mobile');

		if (!$purchasedPopupEnabled) {
			return;
		}

		$option = $this->input->get('option', '', 'string');
		$view = $this->input->get('view', '', 'string');
		$showSiteWide = $config->get('sitewide_purchased_popup');

		// Disallowed views
		$disallowed = [
			'checkout',
			'payment',
			'dashboard',
			'invoice',
			'thanks'
		];

		// Do not show on payplans plan, checkout, dashboard and checkout page
		if ($option === 'com_payplans' && in_array($view, $disallowed)) {
			return;
		}

		if (!$showSiteWide && $option !== 'com_payplans' && $view !== 'plan') {
			return;
		}

		PP::initialize();

		ob_start();
		?>
		<script>
		PayPlans.require()
			.done(function($) {
				FD.require()
					.script('vendor/toast')
						.done(function($) {
							PayPlans.ajax('site/views/plan/getPurchasedPlans', {
								excludeBuyerId: <?php echo $this->my->id; ?>
							}).done(function(plans, options) {
								if (plans.length < 1) {
									return;
								}

								let index = 0;
								const execPopup = () => {
									// If the items have shown finished
									if (index >= options['total']) {
										if (!!parseInt(options['loop']) !== true) {
											return;
										}

										// Reset it to zero if allowed to reshow the items
										index = 0;
									}

									const onBeforeScrollTo = (item) => {
										item.addClass('is-growing');
									};

									const onAfterScrollTo = (item) => {
										setTimeout(function(){
											item.removeClass('is-growing');
										},5000);
									};

									let obj = {
										avatar: plans[index].avatar,
										created: plans[index].subscribed === undefined ? false : plans[index].subscribed,
										columns: 2,
										position: options['position'],
										timeOut: parseInt(options['duration']) * 1000,
										scrollTo: $('[data-plans-item][data-plan-id="' + plans[index].id + '"]').find('[data-plans-item-card]'),
										onBeforeScrollTo: onBeforeScrollTo,
										onAfterScrollTo: onAfterScrollTo
									};

									// Display the purchase popup for it
									fd.toast(plans[index].message, obj);

									index++;
								};

								setTimeout(function() {
									// First time execute
									execPopup();
								}, parseInt(options['delay']) * 1000);

								// Execute the next item after the current 1 removed
								jQuery(document).on('fd.toast.after.removed', function() {
									setTimeout(function() {
										execPopup();
									}, parseInt(options['interval']) * 1000);
								});
							});
						});
			});
		</script>
		<?php

		$scriptContent = ob_get_contents();
		ob_end_clean();

		$doc = JFactory::getDocument();

		// Only process on html views
		if ($doc->getType() != 'html') {
			return;
		}

		$doc->addCustomTag($scriptContent);
	}

	/**
	 * @TODO: Need to remove this. It is a bad idea.
	 *
	 *
	 * Add a image just before </body> tag
	 * which will href to cron trigger.
	 */
	public function onAfterRender()
	{
		//V. IMP. : During uninstallation of Payplans
		// after uninstall this function get executed
		// so prevent it
		$option = $this->input->get('option');

		if ($option == 'com_installer') {
			return true;
		}

		// PayPlans was not included and loaded
		if (defined('PAYPLANS_DEFINE_ONSYSTEMSTART')==false){
			return;
		}

		// Only do if configuration say so : expert_run_automatic_cron is set to 1
		$config = PP::config();
		if ($config->get('expert_run_automatic_cron') != 1) {
			return;
		}

		// Only render for HTML output
		if (JFactory::getDocument()->getType() !== 'html' ) { 
			return;
		}

		//only add if required, then add call back
		$cron = PP::cron();

		if ($cron->shouldRun()) {
			$image = '<img alt="' . JText::_('COM_PAYPLANS_LOGGER_CRON_START', true) . '" src="' . $cron->getImageUrl() . '" style="display: none;" />';

			$body = $this->app->getBody();
			$body = str_replace('</body>', $image . '</body>', $body);

			$this->app->setBody($body);
		}
	}

	/**
	 * Content plugin triggers which needs to be binded to internal plugins
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onContentPrepare($context, &$row, &$params, $page = 0)
	{
		$args = [$context, &$row, &$params, $page];
		$results = PPEvent::trigger('onContentPrepare', $args);

		return true;
	}

	/**
	 * Content plugin triggers which needs to be binded to internal plugins
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onContentAfterSave($context, $article, $isNew)
	{
		$args = [$context, $article, $isNew];
		$results = PPEvent::trigger('onContentAfterSave', $args);

		return true;
	}

	/**
	 * Triggered when a new user is created. This is to allow us to facilitate user registrations
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterStoreUser($user, $isnew, $success, $msg)
	{
		// Process registration systems
		$registration = PP::registration();
		$registration->onAfterStoreUser($user, $isnew, $success, $message);
	}

	/**
	 * We need to trigger events from PayPlans plugins since they are not exposed to events from Joomla.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onAfterInitialise()
	{
		//trigger system start event after loading of joomla framework
		if (defined('PAYPLANS_DEFINE_ONSYSTEMSTART') == false) {

			require_once(JPATH_ADMINISTRATOR . '/components/com_payplans/includes/user/user.php');

			PP::event()->trigger('onPayplansSystemStart');

			define('PAYPLANS_DEFINE_ONSYSTEMSTART', true);
		}
	}

	/**
	 * Triggered before deleting a user. Seems like we need to remove all their orders.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function onUserBeforeDelete($user)
	{
		$userId = $user['id'];

		$options = [
			'buyer_id' => $userId
		];

		// Delete Orders
		$ordersModel = PP::model('Order');
		$orders = $ordersModel->loadRecords($options);

		if ($orders) {
			foreach ($orders as $order) {
				$order = PP::order($order);
				$order->delete();
			}
		}

		$options = [
			'user_id' => $userId
		];

		// Delete invoices
		$invoiceModel = PP::model('Invoice');
		$invoiceModel->deleteMany($options);

		// Delete transactions
		$transactionModel = PP::model('Transaction');
		$transactionModel->deleteMany($options);

		// Delete payments
		$paymentModel = PP::model('Payment');
		$paymentModel->deleteMany($options);

		// Delete Resources
		$resourceModel = PP::model('Resource');
		$resourceModel->deleteMany($options);

		PP::deleteCustomDetailFiles(PP_CUSTOM_DETAILS_TYPE_USER, $userId);
	}

	/**
	 * Performs access check
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function checkAccess()
	{
		if (PP::isFromAdmin()) {
			return;
		}

		$user = PP::user($this->my->id);

		// Access should never be applied on system administrator
		if ($user->isSiteAdmin()) {
			return;
		}

		// Any App and plugin can handle this event
		$dispatcher = PP::event();
		$args = [$user, []];
		$result = $dispatcher->trigger('onPayplansAccessCheck', $args, '', null);

		// We only trigger this if registration plugin is enabled
		$isPluginEnabled = JPluginHelper::isEnabled('payplans', 'registration');

		if ($isPluginEnabled) {
			$registration = PP::registration();
			$registration->onPayplansAccessCheck();
		}

		// is access check failed
		if (in_array(false, $result, true)) {
			$result = $dispatcher->trigger('onPayplansAccessFailed', $args, '', null);
			return false;
		}

		return true;
	}
}
