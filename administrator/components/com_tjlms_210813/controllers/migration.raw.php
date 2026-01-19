<?php
/**
 * @version    SVN: <svn_id>
 * @package    Payplan_Tjlms
 * @copyright  Copyright (C) 2005 - 2014. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 * Shika is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.log.log');
use Joomla\CMS\Log\Log;
use Joomla\CMS\Log\Logger\FormattedtextLogger;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
/**
 * Controller
 *
 * @since  1.6
 */
class TjlmsControllerMigration extends JControllerLegacy
{
	/**
	 * Call function after click on sync button
	 * Sync data payplan & shika
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	public function migration()
	{
		$notEnrolledUsers = array();

		$current_date = HTMLHelper::date($input = 'now', 'Y-m-d H:i:s', false);
		$jinput       = Factory::getApplication()->input;
		$appId   = $jinput->get('appId');

		$config = array('text_file' => 'migrationOfEnrolledUsers.log');
		jimport('joomla.log.logger.formattedtext');
		$logger = new FormattedtextLogger($config);

		// Add the logger.
		Log::addLogger(
				// Pass an array of configuration options
				array(
						// Set the name of the log file
						'text_file' => 'migrationOfEnrolledUsers.log',
						// (optional) you can change the directory
						'text_file_path' => 'logs'
				),
				Log::ALL
			);

		Log::add(
			"App Id " . '|' . " User ID " . '|' . " Course ID " . '|' . " Subscription ID " . '|' .
			"  Enrolment end time " . '|' . "              Status ", Log::INFO, '');

		// Reading file here for getting limit start and end
		$fileData = file(JPATH_ROOT . '/administrator/components/com_tjlms/controllers/migrationLimit.txt');
		$limit = 0;

		foreach ($fileData as $line)
		{
			if (strpos($line, 'limit') !== false)
			{
				$limit = explode(":", $line)[1];
			}
		}

		echo 'Processing app ' . $appId . '<br/>';

		if (!empty($appId))
		{
			// Get records from tjlms_payplanApp xref table
			$db           = Factory::getDBO();
			$query        = $db->getQuery(true);
			$query->select('*');
			$query->from('`#__tjlms_payplanApp` AS tpa');
			$query->where('tpa.pp_app_id =' . (int) $appId);
			$db->setQuery($query);
			$appData = $db->loadObjectlist();
			$appData = array_chunk($appData, 200);

			if ($appData[(int) $limit])
			{
				foreach ($appData[(int) $limit] as $data)
				{
					echo 'For User- ' . $data->user_id . '<br/>';
					echo 'For Course- ' . $data->course_id . '<br/>';

					// Get Subscription details as dates using subscription id
					$query        = $db->getQuery(true);
					$query->select('ps.subscription_date,ps.expiration_date');
					$query->from('`#__payplans_subscription` AS ps');
					$query->where('ps.subscription_id =' . (int) $data->pp_subscription_id);
					$db->setQuery($query);
					$subsciptionData = $db->loadObject();

					if (!empty($subsciptionData))
					{
						if ($subsciptionData->subscription_date != '0000-00-00 00:00:00')
						{
							// Get Enrolled users entry for user and course
							$query = $db->getQuery(true);
							$query->select('*');
							$query->from('`#__tjlms_enrolled_users` AS eu');
							$query->where('eu.user_id = ' . (int) $data->user_id);
							$query->where('eu.course_id = ' . (int) $data->course_id);
							$db->setQuery($query);
							$enrolledUserEntry = $db->loadObject();

							if (!empty($enrolledUserEntry))
							{
								// Update dates in enrolled users table of tjlms
								$query = $db->getQuery(true);

								// Fields to update.
								$fields = array(
									$db->quoteName('enrolled_on_time') . ' = ' . $db->quote($subsciptionData->subscription_date),
									$db->quoteName('end_time') . ' = ' . $db->quote($subsciptionData->expiration_date),
									$db->quoteName('modified_time') . ' = ' . $db->quote($current_date)
								);

								// Conditions for which records should be updated.
								$conditions = array(
									$db->quoteName('user_id') . ' = ' . (int) $data->user_id,
									$db->quoteName('course_id') . ' = ' . (int) $data->course_id
								);

								$query->update($db->quoteName('#__tjlms_enrolled_users'))->set($fields)->where($conditions);

								$db->setQuery($query);

								$result = $db->execute();

								$query = $db->getQuery(true);
								$query->select('c.id');
								$query->from($db->qn('#__tjlms_enrolled_users_history', 'c'));
								$query->where($db->qn('c.enrollment_id') . ' = ' . $db->q((int) $enrolledUserEntry->id));
								$query->order($db->quoteName('c.id') . ' DESC');

								$db->setQuery($query);
								$enrollmentHistoryEntry = $db->loadAssoc();

								if (!empty($enrollmentHistoryEntry['id']))
								{
									$query = $db->getQuery(true);
									$fieldsForEnrolledHistory = array(
										$db->quoteName('end_date') . ' = ' . $db->quote($subsciptionData->expiration_date),
										$db->quoteName('start_date') . ' = ' . $db->quote($subsciptionData->subscription_date)
										);
									$conditionsForEnrolledHistory = array($db->quoteName('id') . ' = ' . $enrollmentHistoryEntry['id']);
									$query->update($db->quoteName('#__tjlms_enrolled_users_history'))->set($fieldsForEnrolledHistory)->where($conditionsForEnrolledHistory);

									$db->setQuery($query);
									$result2 = $db->execute();
								}

								echo 'Updated' . '<br/>';

								// Add the log entry
								Log::add(
								$appId . '    | ' . $data->user_id . '    | ' . $data->course_id . '       | ' . $data->pp_subscription_id . '             |  ' .
								$subsciptionData->expiration_date . '             |  Updated', Log::INFO, ''
								);
							}
							else
							{
								// Add the log entry
								Log::add(
								$appId . '    | ' . $data->user_id . '    | ' . $data->course_id . '       | ' . $data->pp_subscription_id . '             |  '
								. $subsciptionData->expiration_date . '             |  Enrolled users entry is not found', Log::INFO, ''
								);
								echo 'Enrolled users entry is not found' . '<br/>';
							}

							echo '-------------------------------------------------------------------' . '<br/>';
						}
						else
						{
							// Add the log entry
							Log::add(
							$appId . '    | ' . $data->user_id . '    | ' . $data->course_id . '       | ' .
							$data->pp_subscription_id . '             |  ' . $subsciptionData->expiration_date . '             |
							Subscription is not active nor expired(With no status)', Log::INFO, ''
							);
							echo 'Subscription is not active nor expired(With no status)' . '<br/>';
							echo '-------------------------------------------------------------------' . '<br/>';
						}
					}
					else
					{
						// Add the log entry
						Log::add(
							$appId . '    | ' . $data->user_id . '    | ' . $data->course_id . '       | ' .
							$data->pp_subscription_id . '             |  ' . $subsciptionData->expiration_date . '             |  Subscription not exist', Log::INFO, ''
						);
						echo 'Subscription not exist' . '<br/>';
						echo '-------------------------------------------------------------------' . '<br/>';
					}
				}

				// Update limitstart and limitend value
				$filePath    = fopen(JPATH_ROOT . '/administrator/components/com_tjlms/controllers/migrationLimit.txt', 'w+');

				if ($filePath !== false)
				{
					$newContents = "limit:" . ($limit + 1) . "\n";
					fwrite($filePath, $newContents);
					fclose($filePath);
				}
			}
			else
			{
				$filePath    = fopen(JPATH_ROOT . '/administrator/components/com_tjlms/controllers/migrationLimit.txt', 'w+');

				if ($filePath !== false)
				{
					$newContents = "limit:" . (0) . "\n";
					fwrite($filePath, $newContents);
					fclose($filePath);
				}

				echo "Successfully completed.";
			}
		}
	}
}
