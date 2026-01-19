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

require_once(__DIR__ . '/formatter.php');

class PPAppDJCPoints extends PPApp
{
	protected $_resource = 'com_djclassifieds.points';

	/**
	 * Determines if DJ Classifieds is installed and enabled
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function exists()
	{
		static $exists = null;

		if (is_null($exists)) {
			// Determines if DJ Classifieds is installed and enabled
			$enabled = JComponentHelper::isEnabled('com_djclassifieds');

			if (!$enabled) {
				$exists = false;
				return false;
			}

			$file = JPATH_ADMINISTRATOR . '/components/com_djclassifieds/djclassifieds.php';

			if (!JFile::exists($file)) {
				$exists = false;

				return $exists;
			}

			$exists = true;
		}

		return $exists;
	}

	/**
	 * Assigns points to DJ Classifieds
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	private function assignPoints($userId, $points, $description)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_djclassifieds/tables');

		$table = JTable::getInstance('Userspoints', 'DJClassifiedsTable');
		$table->user_id = $userId;
		$table->points = $points;
		$table->description = $description;
		$table->date = JFactory::getDate()->toSql();

		$table->store();

		return $table;
	}

	public function _isApplicable(PPAppTriggerableInterface $refObject, $eventname = '')
	{
		$exists = $this->exists();

		return $exists;
	}

	/**
	 * Triggered when a new subscription is purchaseed
	 *
	 * @since	4.2.0
	 * @access	public
	 */
	public function onPayplansSubscriptionAfterSave($prev, $new)
	{
		// no need to trigger if previous and current state is same
		if (($new->getStatus() == PP_NONE) || ($prev != null && $prev->getStatus() == $new->getStatus())) {
			return true;
		}

		if (!$this->exists()) {
			return true;
		}

		$params = $this->getAppParams();
		$points = $params->get('points');

		if ($new->isActive() && $points) {

			$description = JText::sprintf('Purchased %1$s points from the plan %2$s', $points, $new->getTitle());
			
			$this->assignPoints($new->getBuyer()->getId(), $points, $description);

			$this->_addToResource($new->getId(), $new->getBuyer()->getId(), $points, 'com_djclassifieds.points');
		}
		
		return true;
	 }	
}