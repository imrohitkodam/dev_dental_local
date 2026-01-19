<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class PayplansControllerLanguages extends PayplansController
{
	public function __construct()
	{
		parent::__construct();

		$this->checkAccess('languages');
	}

	/**
	 * Purges the cache of language items
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function purge()
	{
		$model = PP::model('Languages');
		$model->purge();

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_LANGUAGES_PURGED', 'languages');

		$this->info->set('COM_PP_LANGUAGE_PURGED_SUCCESSFULLY', 'success');

		$this->redirectToView('languages');
	}

	/**
	 * Discover new language files from remote language server
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function discover()
	{
		$model = PP::model('Languages');
		$result = $model->discover();

		$actionlog = PP::actionlog();
		$actionlog->log('COM_PP_ACTIONLOGS_LANGUAGES_DISCOVERED', 'languages');

		$this->info->set('COM_PP_LANGUAGE_DISCOVERED_SUCCESSFULLY', 'success');

		$this->redirectToView('languages');
	}

	/**
	 * Install language file on the site
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function install()
	{
		$ids = $this->input->get('cid', array(), 'array');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$table = PP::table('Language');
			$table->load($id);

			$state = $table->install();

			if (!$state) {
				$this->info->set($table->getError(), 'danger');
				return $this->redirectToView('languages');
			}

			$actionlog->log('COM_PP_ACTIONLOGS_LANGUAGES_INSTALLED', 'languages', array(
					'languageTitle' => $table->title
			));
		}

		$this->info->set('COM_PP_LANGUAGE_INSTALLED_SUCCESSFULLY', 'success');
		return $this->redirectToView('languages');
	}

	/**
	 * Uninstall language file on the site
	 *
	 * @since   4.0.0
	 * @access  public
	 */
	public function uninstall()
	{
		$ids = $this->input->get('cid', array(), 'array');
		$actionlog = PP::actionlog();

		foreach ($ids as $id) {
			$id = (int) $id;

			$table = PP::table('Language');
			$table->load($id);

			if (!$table->isInstalled()) {
				$table->delete();
				continue;
			}

			$table->uninstall();
			$table->delete();

			$actionlog->log('COM_PP_ACTIONLOGS_LANGUAGES_UNINSTALLED', 'languages', array(
					'languageTitle' => $table->title
			));
		}

		$this->info->set('COM_PP_LANGUAGE_UNINSTALLED_SUCCESSFULLY', 'success');
		return $this->redirectToView('languages');
	}
}
