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

use Foundry\Libraries\StyleSheets;

class PPStyleSheet
{
	public $location = '';
	public $environment = 'development';

	public function __construct($location = 'site')
	{
		$this->app = JFactory::getApplication();
		$this->doc = JFactory::getDocument();
		$this->config = PP::config();
		$this->location = $location;
		$this->environment = $this->config->get('environment');
	}

	/**
	 * Attaches the stylesheet to the head of the document
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function attach()
	{
		$uri = '/media/com_payplans/themes/site/css/style';

		if ($this->location === 'admin') {

			$this->attachFontawesome();

			// use foundry admin css for backend
			$this->attachStylesheet('/media/foundry/css/joomla-backend');
			
			$uri = '/media/com_payplans/themes/admin/css/style';

			return $this->attachStylesheet($uri);
		}

		// Do not load the built-in font-awesome file if the setting is turn off
		if ($this->config->get('enable_fontawesome')) {
			$this->attachFontawesome();
		}

		$this->attachStylesheet($uri);

		// Attach the custom css first
		$this->attachCustomCss();
	}

	/**
	 * Internal method to attach stylesheets 
	 *
	 * @since	5.0.0
	 * @access	private
	 */
	private function attachStylesheet($file, $applyMinify = true, $cacheBusting = true)
	{
		if ($cacheBusting) {
			static $hash = null;

			if (is_null($hash)) {
				$hash = md5(PP::getLocalVersion());
			}
		}

		// RTL support
		if (FH::isRTL()) {
			$file .= '-rtl';
		}

		if ($this->environment === 'production' && $applyMinify) {
			$file .= '.min';
		}

		$file .= '.css';

		if ($cacheBusting) {
			$file .= '?' . $hash . '=1';
		}

		return StyleSheets::add($file, 'component');
	}

	/**
	 * Attaches font awesome css library
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function attachFontawesome()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			StyleSheets::load('fontawesome');

			$loaded = true;
		}

		return $loaded;
	}

	/**
	 * Generates the file name for the stylesheets being used
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function getFileName($rtl = false)
	{
		$file = 'style';

		if ($rtl) {
			$file .= '-rtl';
		}

		// Should we be using a minified version


		if ($this->environment == 'production') {
			$file .= '.min';
		}

		$file .= '.css';

		return $file;
	}

	/**
	 * if there is a custom.css overriding, we need to attach this custom.css file.
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	private function attachCustomCss()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			$currentTemplate = FH::getCurrentTemplate();

			$path = JPATH_ROOT . '/templates/' . $currentTemplate . '/html/com_payplans/css/custom.css';
			$exists = JFile::exists($path);

			if ($exists) {
				$customURI = '/templates/' . $currentTemplate . '/html/com_payplans/css/custom.css';

				$this->attachStylesheet($customURI);
			}

			$loaded = true;
		}

		return $loaded;
	}
}