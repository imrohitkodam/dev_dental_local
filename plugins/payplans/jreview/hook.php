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

require_once(__DIR__ . '/app/lib.php');

$app = JFactory::getApplication();
$lib = new PPJReview();

// This file is responsible to hook into the internal triggers of JReviews.
// This allows us to capture their events and trigger PayPlans internally

if (!PP::isFromAdmin() && $lib->exists()) {
	$option = $app->input->get('option', 'com_content', 'string');

	if ($option !== 'com_jreviews') {
		return;
	}

	// Ensure that JReviews' defines.php is required first
	// For some reason, it does not being required in '/components/com_jreviews/jreviews/framework.php' on JReviews 4.1. Maybe is their bug.
	require_once(JPATH_ROOT . '/components/com_jreviews/jreviews/cms_compat/joomla/defines.php');

	$lib->load();

	defined('MVC_FRAMEWORK') or die('Direct Access to this location is not allowed.');

	$jreviewsApp = S2App::getInstance();
	$jreviewsApp->jreviewsPaths['Plugin']['payplans.php'] = "components/com_jreviews/jreviews/plugins/cron_functions.php";

	abstract class payplansJreview extends S2Component
	{
	}
}

if (!class_exists('payplansJreview')) {
	abstract class payplansJreview
	{
	}
}

class PayplansComponent extends payplansJreview
{
	var $plugin_order = 101;
	var $name = 'payplans';
	var $published = true;
	var $autoPublishTranslation = true;
	var $controllerActions = [
		'media_upload' => ['create','_save'],
		'listings' => ['create','_loadForm','_save']
	];

	var $responseSend = false;

	public function startup(&$controller)
	{
		if (!defined('MVC_FRAMEWORK_ADMIN')) {
			$this->c = $controller;
		}
	}

	/**
	 * Triggered by JReviews internally
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function plgBeforeSave(&$model,&$data)
	{
		$listingModels = ['Discussion', 'Fvorite', 'Listing'];
		$mediaModels = ['Review', 'Vote', 'Media'];

		$result = '';

		if (in_array($model->name, $listingModels)) {
			$result = $this->_plgBeforeSaveListing($data);
		}

		if (in_array($model->name, $mediaModels)) {
			$result = $this->_plgBeforeSaveMedia($data);
		}

		if ($this->responseSend) {
			return $data;
		}

		$this->responseSend = true;

		if (!is_array($result)) {
			return $data;
		}

		$result = array_shift($result);

		if (!$result) {
			$message = 'COM_PAYPLANS_APP_JREVIEW_YOU_ARE_NOT_ALLOWED';

			if (in_array($model->name, $mediaModels)) {
				$message = 'COM_PAYPLANS_APP_JREVIEW_YOU_ARE_NOT_ALLOWED_ADD_MEDIA';
			}

			$message = JText::_($message);

			ob_start();
			?>
<div class="alert alert-error">
	<?php echo JText::_($message);?>

	<div style="margin-top: 15px;">
		<a href="<?php echo PPR::_('index.php?option=com_payplans&view=plan');?>" class="btn btn-primary"><?php echo JText::_('COM_PAYPLANS_APP_JOOMLA_ARTICLE_SUBSCRIBE_PLAN');?></a>
	</div>
</div>
			<?php
			$contents = ob_get_contents();
			ob_end_clean();

			$result   = [
				'success' => false,
				'str' => $contents 
			];

			header('Content-Type: application/json');
			echo json_encode($result);
			exit();
		}

		return $data;
	}

	/**
	 * Before saving the media, trigger our own events
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function _plgBeforeSaveMedia($data)
	{
		if (isset($data['Media'])) {
			$args = [$data['Media']];
			$results = PP::event()->trigger('onPayplansJreviewBeforeSaveMedia', $args);

			return $results;
		}

		return true;
	}

	/**
	 * Before saving listing, trigger our own events
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function _plgBeforeSaveListing($data)
	{
		$args = [$data];
		$results = PP::event()->trigger('onPayplansJreviewBeforeSaveList', $args);

		return $results;
	}
}