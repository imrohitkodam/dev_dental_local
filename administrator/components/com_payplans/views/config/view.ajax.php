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

require_once(PP_LIB . '/vendor/autoload.php');

use Nahid\JsonQ\Jsonq;

class PayPlansViewConfig extends PayPlansAdminView
{
	/**
	 * Renders confirmation before allowing users to edit the encryption key
	 *
	 * @since	4.0.0
	 * @access	public
	 */
	public function editKeyDialog()
	{
		$theme = PP::themes();
		$contents = $theme->output('admin/settings/dialogs/edit.key');

		return $this->resolve($contents);
	}

	/**
	 * Searches for a settings
	 *
	 * @since	4.1.0
	 * @access	public
	 */
	public function search()
	{
		$query = $this->input->get('text', '', 'word');
		$query = strtolower($query);

		$jsonString = file_get_contents(PP_DEFAULTS . '/cache.json');
		$jsonString = strtolower($jsonString);

		$jsonq = new Jsonq();
		$jsonq->json($jsonString);

		$result = @$jsonq->from('items')
				->where('keywords', 'contains', $query)
				->groupBy('page')
				->get();
		
		$theme = PP::themes();
		$theme->set('result', $result);
		$contents = $theme->output('admin/settings/search/result');

		return $this->ajax->resolve($contents);
	}
}