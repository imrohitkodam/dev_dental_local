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

class PPThemesHelperAttachment extends PPThemesHelperAbstract
{
	/**
	 * Renders the attachment list
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function list($files, $container, $options = [])
	{
		$options['container'] = $container;

		$themes = PP::themes();
		$themes->set('files', $files);
		$themes->set('container', $container);
		$themes->set('options', $options);

		$output = $themes->output('admin/helpers/attachment/list');

		return $output;
	}

	/**
	 * Renders the attachment item
	 *
	 * @since	4.2.8
	 * @access	public
	 */
	public function item($name, $options = [])
	{
		// A flag to indicate if the file is being stored in the site currently
		$saved = PP::normalize($options, 'saved', false);

		$action = PP::normalize($options, 'action', false);
		$download = PP::normalize($options, 'download', true);
		$container = PP::normalize($options, 'container', '');
		$group = PP::normalize($options, 'group', '');
		$objId = PP::normalize($options, 'objId', '');
		$type = PP::normalize($options, 'type', '');

		$downloadLink = 'javascript:void(0);';

		if ($download) {
			$root = rtrim(JURI::root(), '/');

			if (PP::isFromAdmin()) {
				$root .= '/administrator';
			}

			$downloadLink = $root . '/index.php?option=com_payplans&task=attachment.download&tmpl=component&group=' . $group . '&objId=' . $objId . '&type=' . $type . '&name=' . $name . '&container=' . $container;
		}

		$themes = PP::themes();
		$themes->set('name', $name);
		$themes->set('saved', $saved);
		$themes->set('action', $action);
		$themes->set('download', $download);
		$themes->set('downloadLink', $downloadLink);
		$themes->set('container', $container);
		$themes->set('group', $group);
		$themes->set('objId', $objId);
		$themes->set('type', $type);
		$output = $themes->output('admin/helpers/attachment/item');

		return $output;
	}
}