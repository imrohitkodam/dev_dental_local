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

require_once(JPATH_ROOT . '/plugins/payplans/jreview/app/lib.php');

$jreview = new PPJReview();

if (!$jreview->exists()) {
	echo '<div ' . $attributes . '>JReviews is not installed on the site yet.</div>';
	return;
}

if (!is_array($value)) {
	$value = array($value);
}

$categories = $jreview->getCategories();

$name = $name . '[]';

$options = [];
foreach ($categories as $category) {
	$options[$category->cat_id] = JText::_($category->cat_title);
} ?>

<?php echo $this->fd->html('form.select2', $name, $value, $options, ['attributes' => $attributes, 'multiple' => true, 'theme' => 'fd']);?>