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

use Foundry\Libraries\Scripts;

class ToolbarScripts
{
	private $scripts = [
		'toolbar',
		'search',
		'library',
		'social',
		'responsive',
		'notifications'
	];

	// Main toolbar scripts' path.
	private $path = null;

	public function __construct()
	{
		if (!defined('TOOLBAR_CLI')) {
			$this->path = FDT_SCRIPTS;
		}
	}

	/**
	 * Responsible to attach the main toolbar and the provided script.
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function attach()
	{
		static $loaded = null;

		// Prevent attaching the site's scripts multiple times.
		if (is_null($loaded)) {
			$loaded = true;

			// Initialize Foundry scripts first
			Scripts::init();

			// Render 3rd party libraries from foundry
			// Scripts::load('perfect-scrollbar');
			// Scripts::load('popper');
			// Scripts::load('tippy');

			$doc = JFactory::getDocument();

			// Attached module configuration.
			$configuration = $this->getJSConfiguration();
			$doc->addCustomTag($configuration);

			// In production mode, we do not need to render the core files separately since they are already pre-compiled into a single file
			if (FDT_ENVIRONMENT === 'production') {
				$this->scripts = ['module.min'];
			}

			// Only load this when in mobile view.
			// if (FH::responsive()->isMobile() || FH::responsive()->isTablet()) {
			// 	Scripts::load('mmenu');
			// }

			foreach ($this->scripts as $script) {
				$path = $this->path . '/' . $script . '.js';

				// If it is an absolute url, no further processing is needed
				if (strpos($script, 'http') !== false) {
					$doc->addScript($script);
					continue;
				}

				if (!file_exists($path)) {
					throw new Exception('Failed to load ' . $script . ' script for StackIdeas Toolbar.');
				}

				$script = FDT_SCRIPTS_URI . '/' . $script . '.js';

				$doc->addScript($script);
			}
		}

		return $loaded;
	}

	/**
	 * Retrieves the list of scripts on the queue
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function getScripts()
	{
		return $this->scripts;
	}

	/**
	 * Compiles all the neccessary js files into a single module.js file
	 *
	 * @since	1.0.0
	 * @access	public
	 */
	public function compile()
	{
		$contents = '';
		$path = dirname(dirname(__DIR__)) . '/assets/scripts';

		foreach ($this->scripts as $script) {
			$file = $path . '/' . $script . '.js';
			$contents .= "
			";

			$contents .= file_get_contents($file);
		}

		$destination = $path . '/module.js';
		JFile::write($destination, $contents);
	}

	public function getJSConfiguration()
	{
		$adapter = FDT::getAdapter(FDT::getMainComponent());

		ob_start();
?>
<!--googleoff: index-->
<script>
window.tb = {
"token": "<?php echo FH::token();?>",
"ajaxUrl": "<?php echo $adapter->getAjaxUrl();?>",
"userId": "<?php echo JFactory::getUser()->id;?>",
"appearance": "<?php echo FDT::getAppearance();?>",
"theme": "<?php echo FDT::getAccent();?>",
"ios": <?php echo FH::responsive()->isIphone() ? 'true' : 'false';?>,
"mobile": <?php echo FH::responsive()->isMobile() ? 'true' : 'false'; ?>,
};
</script>
<!--googleon: index-->
<?php
		$contents = ob_get_contents();
		ob_end_clean();

		return $contents;
	}
}