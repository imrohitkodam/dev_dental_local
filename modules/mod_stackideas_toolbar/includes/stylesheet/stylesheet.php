<?php
/**
* @package      StackIdeas
* @copyright    Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* StackIdeas Toolbar is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

use Foundry\Libraries\StyleSheets;

class ToolbarStylesheet
{
	/**
	 * Responsible to attach the main toolbar stylesheets.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function attach()
	{
		static $loaded = null;

		if (is_null($loaded)) {
			if (FH::responsive()->isMobile() || FH::responsive()->isTablet()) {
				StyleSheets::load('mmenu');
			}

			$extension = FDT_ENVIRONMENT === 'production' ? '.min.css' : '.css';
			$path = 'modules/mod_stackideas_toolbar/assets/css/toolbar' . $extension;

			StyleSheets::add($path, 'component');

			$config = FDT::config();
			if ($config->get('fontawesome', true)) {
				StyleSheets::load('fontawesome');
			}

			$loaded = true;
		}

		return $loaded;
	}
}
