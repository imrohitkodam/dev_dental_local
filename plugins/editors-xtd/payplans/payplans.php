<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

jimport('joomla.filesystem.file');

$app = JFactory::getApplication();
$input = $app->input;
$option = $input->get('option', '', 'default');
$install = $input->get('setup', false, 'bool');

// check if currently is payplans installation.
$installationFile = JPATH_ROOT . '/tmp/payplans.installation';

// Do not load payplans when component is com_installer
if ($option == 'com_installer' || JFile::exists($installationFile) || $install) {
	return true;
}

$file = JPATH_ADMINISTRATOR . '/components/com_payplans/includes/payplans.php';
$exists = JFile::exists($file);

if (!$exists) {
	return;
}

require_once($file);

class plgButtonPayPlans extends PPPlugins
{
	public function __construct(& $subject, $config)
	{
		parent::__construct($subject, $config);
		$this->loadLanguage();
	}

	/**
	 * Renders the button's script
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function getScript($editorName)
	{
		$url = rtrim(JURI::root(), '/') . '/administrator/index.php?option=com_payplans&view=plan&tmpl=component&jscallback=payplansCallback';

ob_start();
?>
window.insertPayPlansPlan = function() {
	PayPlans.dialog({
		'content': '<?php echo $url;?>',
		width: 860,
		height: 800
	});
};

window.payplansCallback = function(plan) {
	var html = ' <a href="' + plan.permalink + '">' + plan.title + '</a> ';
	var editor = "<?php echo $editorName;?>";

	/** Use the API, if editor supports it **/
	if (window.parent.Joomla && window.parent.Joomla.editors && window.parent.Joomla.editors.instances && window.parent.Joomla.editors.instances.hasOwnProperty(editor)) {
		window.Joomla.editors.instances[editor].replaceSelection(html);
	} else {
		window.jInsertEditorText(html, editor);
	}

	PayPlans.dialog().close();
};
<?php
$contents = ob_get_contents();
ob_end_clean();

		return $contents;
	}

	/**
	 * Renders the button when editor is rendered
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function onDisplay($name)
	{
		PP::initialize();

		$script = $this->getScript($name);

		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($script);

		$button = new JObject;
		$button->modal = false;
		$button->onclick = 'window.insertPayPlansPlan();return false;';
		$button->name = 'credit';
		$button->text = JText::_('Plans');
		$button->link = '#';

		// $button->set('link', '#');
		// $button->set('data-btn-payplans', 1);

		return $button;
	}
}
