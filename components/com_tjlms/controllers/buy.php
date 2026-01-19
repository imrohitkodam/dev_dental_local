<?php
/**
 * @package     TJLms
 * @subpackage  com_shika
 *
 * @author      Techjoomla <extensions@techjoomla.com>
 * @copyright   Copyright (C) 2009 - 2019 Techjoomla. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;

require_once JPATH_COMPONENT . DS . 'controller.php';
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\User\UserHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

jimport('joomla.application.component.controller');

/**
 * Tjmodules list controller class.
 *
 * @since  1.0.0
 */
class TjlmsControllerbuy extends tjlmsController
{
	protected $tnc;

	protected $article;

	protected $doesArticleExists;

	protected $res;

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct()
	{
		parent::__construct();

		// Initialise the session object
		$this->session = Factory::getSession();

		// Language
		$language = Factory::getLanguage();

		// Set the base directory for the language
		$base_dir = JPATH_SITE;

		// Load the language. IMPORTANT Becase we use ajax to load cart
		$language->load('com_tjlms', $base_dir, $language->getTag(), true);
	}

	/**
	 * Function used to load state of a country.
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function loadState()
	{
			$db = Factory::getDBO();
			$jinput = Factory::getApplication()->input;
			$country = $jinput->get('country', '', 'STRING');

			$model = $this->getModel('buy');
			$state = $model->getuserState($country);
			echo json_encode($state);
			jexit();
	}

	/**
	 * Save step 1 of check out...save order details
	 *
	 * @return  mixed object
	 *
	 * @since  1.0.0
	 */
	public function save_step_select_subsplan()
	{
		$session = Factory::getSession();
		$model = $this->getModel('buy');
		$post = Factory::getApplication()->input->post;
		$orderData = array();
		$orderData['user_id'] = Factory::getUser()->id;
		$orderData['course_id'] = $post->get('course_id', '', 'INT');
		$orderData['plan_id'] = $post->get('selected_plan', '', 'INT');
		$orderData['coupon_code'] = $post->get('coupon_code', '', 'STRING');
		$res = $model->createOrder($orderData, 'step_select_subsplan');

		echo json_encode($res);
		jexit();
	}

	/**
	 * Save step 2 of check out...save billing details
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function save_step_billinginfo()
	{
		$res = array();
		$session = Factory::getSession();
		$post = Factory::getApplication()->input->post;

		$user = Factory::getUser();
		$com_params = ComponentHelper::getParams('com_tjlms');
		$silentRegEnabled = $com_params->get('allow_silent_registration', 0);

		if ($user->guest && $silentRegEnabled)
		{
			// Get billing data
			$billData = $post->get('bill', array(), 'ARRAY');
			if (!empty($billData['fnam']) && !empty($billData['lnam']) && !empty($billData['email1']))
			{
				// Validate required fields
				if (empty($billData['fnam']) || empty($billData['lnam']) || empty($billData['email1']))
				{
					$res = array('error' => 1, 'msg' => Text::_('COM_TJLMS_SILENT_REG_REQUIRED_FIELDS'));
					echo json_encode($res);
					jexit();
				}
				
				// Validate email format
				if (!filter_var($billData['email1'], FILTER_VALIDATE_EMAIL))
				{
					$res = array('error' => 1, 'msg' => Text::_('COM_TJLMS_INVALID_EMAIL_FORMAT'));
					echo json_encode($res);
					jexit();
				}
				
				// Check if user already exists with this email
				$db = Factory::getDBO();
				$query = $db->getQuery(true);
				$query->select('id, username')
					  ->from('#__users')
					  ->where('email = ' . $db->quote($billData['email1']));
				$db->setQuery($query);
				$existingUser = $db->loadObject();
				
				if ($existingUser)
				{
					$res = array('error' => 1, 'msg' => Text::_('COM_TJLMS_USER_ALREADY_EXISTS_LOGIN'));
					echo json_encode($res);
					jexit();
				}
				
				 // Generate random password using Joomla's built-in method
                 $randomPassword = UserHelper::genRandomPassword(8);
				
				// Use email as username
				$username = $billData['email1'];
				
				// Create user account
				$userData = array(
					'name' => trim($billData['fnam'] . ' ' . $billData['lnam']),
					'username' => $username,
					'email' => $billData['email1'],
					'password' => $randomPassword,
					'password2' => $randomPassword,
					'block' => 0,
					'sendEmail' => 0
				);
				
				// Register user
				$userId   = $this->createNewUser($userData);
				
				if ($userId)
				{
					// Send email with credentials
					// $emailSent = $this->sendCredentialsEmail($billData['email1'], $billData['fnam'], $username, $randomPassword);

					if (!$existingUser)
					{
						$this->sendMailNewUser($userData);
					}
					
					// Auto-login the user
					$credentials = array(
						'username' => $username,
						'password' => $randomPassword
					);
					
					$app = Factory::getApplication();
					$loginResult = $app->login($credentials);
					
					if ($loginResult)
					{
						if ($emailSent)
						{
							$app->enqueueMessage(Text::_('COM_TJLMS_SILENT_REG_SUCCESS_EMAIL_SENT'), 'success');
						}
						else
						{
							$app->enqueueMessage(Text::_('COM_TJLMS_SILENT_REG_SUCCESS_EMAIL_FAILED'), 'warning');
						}
					}
					else
					{
						$res = array('error' => 1, 'msg' => Text::_('COM_TJLMS_REGISTRATION_SUCCESS_LOGIN_FAILED'));
						echo json_encode($res);
					}
				}
				else
				{
					$res = array('error' => 1, 'msg' => Text::_('COM_TJLMS_REGISTRATION_FAILED'));
					echo json_encode($res);
				}
			}
		}
	
		$model = $this->getModel('buy');
		$orderData['user_id'] = Factory::getUser()->id;
		$orderData['bill'] = $post->get('bill', '', 'ARRAY');
		$orderData['comment'] = $post->get('comment', '', 'STRING');
		$orderData['accpt_terms'] = $post->get('accpt_terms', 'off', 'STRING');

		$com_params = ComponentHelper::getParams('com_tjlms');
		$this->tnc = $com_params->get('terms_condition', 0, 'INT');

		if ($this->tnc)
		{
			$this->article = $com_params->get('tnc_article', '', 'INT');

			// Check if the article exists
			$this->doesArticleExists = $model->doesArticleExists($this->article);
		}

		if ($this->tnc && $this->doesArticleExists)
		{
			if ($orderData['accpt_terms'] == 'on')
			{
				$res = $model->createOrder($orderData, 'save_step_billinginfo');

				if (!empty($res['order_id']))
				{
					// Terms & condition for techjoomla extension
					$userPrivacyData = array();

					$userPrivacyData['client'] = 'com_tjlms.buy';
					$userPrivacyData['client_id'] = $res['order_id'];
					$userPrivacyData['user_id'] = $orderData['user_id']?$orderData['user_id']:0;
					$userPrivacyData['purpose'] = Text::_('COM_TJLMS_USER_PRIVACY_TERMS_PURPOSE_PAYMENT');
					$userPrivacyData['accepted'] = ($orderData['accpt_terms'] === 'on')?1:0;
					$userPrivacyData['date'] = Factory::getDate('now')->toSQL();

					$model->savePrivacyData($userPrivacyData);
				}
			}
			else
			{
				$res['tnc'] = 0;
			}
		}
		else
		{
				$res = $model->createOrder($orderData, 'save_step_billinginfo');
		}

		echo json_encode($res);
		jexit();
	}

	/**
	 * Function called for applying tax.
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function applytax()
	{
		$input = Factory::getApplication()->input;
		$post = $input->post;
		$total_calc_amt = $input->get('total_calc_amt', '', 'STRING');
		PluginHelper::importPlugin('lmstax');

		// Call the plugin and get the result
		$taxresults = Factory::getApplication()->triggerEvent('onAddTax', array($total_calc_amt));

		echo json_encode($taxresults['0']);
		jexit();
	}

	/**
	 * Function used to get coupon
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function getcoupon()
	{
		$user = Factory::getUser();
		$db = Factory::getDBO();
		$input = Factory::getApplication()->input;
		$data = $input->post;
		$course_id = $data->get('course_id', '0', 'int');
		$subscriptionPlan = $data->get('selected_plan', '', 'int');
		$c_code = $data->get('coupon_code', '0', 'STRING');

		$count = '';
		$model = $this->getModel('buy');
		$count = $model->getcoupon($c_code, $course_id, $subscriptionPlan);

		switch ($count->status)
		{
			case 'invalid' :
			$c[] = array("error" => 1, "msg" => Text::_('COM_TJLMS_COP_INVALID'));
			break;

			case 'none' :
			$c[] = array("error" => 1, "msg" => Text::_('COM_TJLMS_COP_EXISTS'));
			break;

			case 'expired' :
			$c[]  = array("error" => 1, "msg" => Text::_('COM_TJLMS_COP_EXPIRED'));
			break;

			case 'exceed' :
			$c[]  = array("error" => 1, "msg" => Text::_('COM_TJLMS_COP_EXCEEDS'));
			break;

			case 'ok' :
			$data = $count->data;
			$c[] = array("value" => $data[0]->value, "val_type" => $data[0]->val_type);
			break;
		}

		echo json_encode($c);
		jexit();
	}

	/**
	 * Function to get order data for google analytics.
	 *
	 * @return  json
	 *
	 * @since  1.3.20
	 */
	public function generateOrderData()
	{
		$com_params = ComponentHelper::getParams('com_tjlms');
		$ecTrackingDataArray = array();
		$input = Factory::getApplication()->input;
		$orderId = $input->get('order_id', '', 'INT');
		$orderModel = BaseDatabaseModel::getInstance('Orders', 'TjlmsModel', array('ignore_request' => true));
		$ecTrackingData   = $orderModel->getEcTrackingData($orderId);
		$ecTrackingData->step_number = 4;
		$dimension = $com_params->get('ga_product_type_dimension');

		if ($com_params->get('track_attendee_step') == 1)
		{
			$ecTrackingData->step_number = 5;
		}

		$ecTrackingData->productTypeDimensionValue = $dimension ? $dimension : '';

		$ecTrackingDataArray[] = $ecTrackingData;
		echo json_encode($ecTrackingDataArray);
		jexit();
	}

	/**
	 * Create user
	 *
	 * @param   Array   $data      User Information
	 * @param   String  $randpass  Password
	 *
	 * @return  boolean true/false
	 *
	 * @since   2.7
	 */
	public function createNewUser($userData)
	{
		global $message;
		jimport('joomla.user.helper');
		$app       = Factory::getApplication();
		$user      = clone Factory::getUser();
		$user->set('username', $userData['username']);
		$user->set('password1', $userData['password']);
		$user->set('name', $userData['name']);
		$user->set('email', $userData['email']);

		// Password encryption
		$salt           = UserHelper::genRandomPassword(32);
		$crypt          = UserHelper::hashPassword($user->password1);
		$user->password = "$crypt";

		// User group/type
		$user->set('id', '');
		$user->set('usertype', 'Registered');

		$userConfig       = ComponentHelper::getParams('com_users');

		// Default to Registered.
		$defaultUserGroup = $userConfig->get('new_usertype', 2);
		$user->set('groups', array($defaultUserGroup));

		$date = Factory::getDate();
		$user->set('registerDate', $date->toSQL());
		$user->set('lastvisitDate', '');

		// True on success, false otherwise
		if (!$user->save())
		{
			echo $message = Text::_('COM_TJLMS_UNABLE_TO_CREATE_USER_BZ_OF') . $user->getError();

			return false;
		}
		else
		{
			$message = Text::sprintf('COM_TJLMS_CREATED_USER_AND_SEND_ACCOUNT_DETAIL_ON_EMAIL', $user->username);
		}

		$app->enqueueMessage($message);

		return $user->id;
	}

	/**
	 * Send Email To user.
	 *
	 * @param   Array   $data      User Information Data
	 *
	 * @return  Boolean
	 *
	 * @since   2.7
	 */
	public function sendMailNewUser($data)
	{
		$app      = Factory::getApplication();
		$mailfrom = $app->get('mailfrom');
		$fromname = $app->get('fromname');
		$sitename = $app->get('sitename');
		$email    = $data['email'];
		$subject  = Text::_('COM_TJLMS_EMAIL_ACCOUNT_DETAILS');
		$find1    = array('{NAME}','{SITENAME}');
		$replace1 = array($data['name'], $sitename);
		$subject  = str_replace($find1, $replace1, $subject);
		$message  = Text::_('COM_TJLMS_EMAIL_REGISTERED_BODY');
		$find     = array(
			'{NAME}',
			'{SITENAME}',
			'{SITEURL}',
			'{USERNAME}',
			'{PASSWORD}'
		);
		$replace = array(
			$data['name'],
			$sitename,
			Uri::root(),
			$data['username'],
			$data['password']
		);
		$message = str_replace($find, $replace, $message);

		if ($app->get('mailonline') == true)
		{
		    Factory::getMailer()->sendMail($mailfrom, $fromname, $email, $subject, $message);
		    $messageadmin = Text::_('COM_TJLMS_REGISTRATION_ADMIN');
		    $find2        = array(
		        '{sitename}',
		        '{username}',
		    );
		    $replace2     = array(
		        $sitename,
		        $data['username'],
		    );
		    $messageadmin = str_replace($find2, $replace2, $messageadmin);

		    Factory::getMailer()->sendMail($mailfrom, $fromname, $mailfrom, $subject, $messageadmin);
		}

		return true;
	}

	public function loginValidate()
	{
		$app          = Factory::getApplication();
		$input        = $app->input;
		$user         = Factory::getUser();
		$itemId       = $input->get('Itemid');
		$courseId     = $input->get('course_id');

		$redirect_url = Route::_('index.php?option=com_tjlms&view=buy&course_id=' . $courseId . '&Itemid=' . $itemId, false);
		$json         = array();

		// Check if user is already logged in
		if ($user->id)
		{
			$json['success'] = true;
			$json['message'] = 'User already logged in';
			$json['redirect'] = $redirect_url;
			echo json_encode($json);
			$app->close();
			return;
		}

		// Get login credentials
		$userLoginDetail['username'] = $input->get('email', '', 'STRING');
		$userLoginDetail['password'] = $input->get('password', '', 'STRING');

		// Validate input
		if (empty($userLoginDetail['username']) || empty($userLoginDetail['password']))
		{
			$json['error']['warning'] = 'Please enter both username and password';
			echo json_encode($json);
			$app->close();
			return;
		}

		// Attempt to login
		$status = $app->login($userLoginDetail, array('silent' => true));

		if ($status)
		{
			// Login successful
			$json['success'] = true;
			$json['message'] = 'Login successful';
			$json['redirect'] = $redirect_url;
		}
		else
		{
			// Login failed
			$json['error']['warning'] = Text::_('COM_TJLMS_ERROR_LOGIN');
		}

		echo json_encode($json);
		$app->close();
	}
	
	/**
	 * Check if user already exists with given email
	 *
	 * @return  json
	 *
	 * @since  1.0.0
	 */
	public function checkExistingUser()
	{
		$app = Factory::getApplication();
		$input = $app->input;
		$email = $input->get('email', '', 'STRING');
		
		$json = array();
		
		// Validate email
		if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
		{
			$json['exists'] = false;
			echo json_encode($json);
			$app->close();
			return;
		}
		
		// Check if user already exists with this email
		$db = Factory::getDBO();
		$query = $db->getQuery(true);
		$query->select('id')
			  ->from('#__users')
			  ->where('email = ' . $db->quote($email));
		$db->setQuery($query);
		$existingUser = $db->loadObject();
		
		$json['exists'] = ($existingUser) ? true : false;
		echo json_encode($json);
		$app->close();
	}
}
