<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// No direct access.
defined('_JEXEC') or die();

/**
 * Categories list controller class.
 */
class EdocmanControllerDownloadlog extends EDocmanController
{
	/**
	 * Method to empty the download logs database
	 *
	 */
	function delete()
	{
		$model = $this->getModel();
		$model->delete();
		$this->setRedirect('index.php?option=com_edocman&view=downloadlogs', JText::_('Download logs emptied'));
	}

	/**
	 * Method to export download logs into a csv file
	 */
	function export()
	{
		$db  = JFactory::getDbo();
		$sql = 'SELECT a.*, b.title, c.username AS downloader_username, c.email AS downloader_email FROM #__edocman_statistics AS a ' .
			' LEFT JOIN #__edocman_documents AS b ON a.document_id=b.id' . ' LEFT JOIN #__users AS c ON a.user_id=c.id' . ' ORDER BY a.id ';
		$db->setQuery($sql);
		$rows = $db->loadObjectList();
		if (count($rows))
		{
			$results_arr = array();
			$csv_output  = JText::_('Document');
			$csv_output .= "," . JText::_('Username');
			$csv_output .= ", " . JText::_('Email');
			$csv_output .= ", " . JText::_('Download Time');
			$csv_output .= ', ' . JText::_('User IP');
			$csv_output .= ', ' . JText::_('Browser');
			$csv_output .= ', ' . JText::_('OS');
			$UTC = new DateTimeZone("UTC");
			$newTZ = new DateTimeZone(JFactory::getConfig()->get('offset'));
			foreach ($rows as $r)
			{
				$results_arr   = array();
				$results_arr[] = $r->title;
				if (!$r->user_id)
				{
					$results_arr[] = $r->name;
					$results_arr[] = $r->email;
				}
				else 
				{
					$results_arr[] = $r->downloader_username;
					$results_arr[] = $r->downloader_email;
				}
				$date = new DateTime( $r->download_time, $UTC );
				$date->setTimezone( $newTZ );
				$config = EDocmanHelper::getConfig();
				$date_format = $config->date_format;
				if($date_format == ""){
					$date_format = "m-d-Y H:i:s";
				}
				$results_arr[] = $date->format($date_format);
				$results_arr[] = $r->user_ip;
				$results_arr[] = $r->browser;
				$results_arr[] = $r->os;
				$csv_output .= "\n\"" . implode("\",\"", $results_arr) . "\"";
			}
			$csv_output .= "\n";
			/*
			if (ereg('Opera(/| )([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
			{
				$UserBrowser = "Opera";
			}
			elseif (ereg('MSIE ([0-9].[0-9]{1,2})', $_SERVER['HTTP_USER_AGENT']))
			{
				$UserBrowser = "IE";
			}
			else
			{
				$UserBrowser = '';
			}
			$mime_type = ($UserBrowser == 'IE' || $UserBrowser == 'Opera') ? 'application/octetstream' : 'application/octet-stream';
			*/
			$browser   = JFactory::getApplication()->client->browser;
			$mime_type = ($browser == JApplicationWebClient::IE || $browser == JApplicationWebClient::OPERA) ? 'application/octetstream' : 'application/octet-stream';
			$filename  = "Download_log";
			@ob_end_clean();
			ob_start();
			header('Content-Type: ' . $mime_type);
			header('Expires: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			if ($UserBrowser == 'IE')
			{
				header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
				header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
				header('Pragma: public');
			}
			else
			{
				header('Content-Disposition: attachment; filename="' . $filename . '.csv"');
				header('Pragma: no-cache');
			}
			print $csv_output;
			exit();
		}
	}
}