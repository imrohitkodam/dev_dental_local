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

class ToolbarHtml
{
	public $adapter = null;
	public $my = null;

	public function __construct()
	{
		$this->adapter = FDT::getAdapter(FDT::getMainComponent());
		$this->my = JFactory::getUser();
	}

	public function name($args = [])
	{
		$user = FH::normalize($args, 'user', $this->adapter->getUser($this->my->id));
		$useAnchorTag = FH::normalize($args, 'useAnchorTag', true);
		$profileStyling = FH::normalize($args, 'profileStyling', true);
		$showVerified = FH::normalize($args, 'showVerified', true);

		$permalink = '';

		if (method_exists($user, 'getPermalink')) {
			$permalink = $user->getPermalink();
		}

		$name = $user->getName();
		$isVerified = $showVerified && method_exists($user, 'isVerified') && $user->isVerified();

		$options = [
			'useAnchorTag' => $useAnchorTag,
			'permalink' => $permalink,
			'verified' => $isVerified,
			'attributes' => FH::normalize($profileStyling, 'customStyle', ''),
		];

		// Showining ES Profiletype color. #179
		if ($profileStyling) {
			$profileStyling = $this->adapter->getProfileStyling();

			$options['class'] = FH::normalize($profileStyling, 'class', '');
		}

		$options = array_merge($args, $options);

		return FDT::themes()->fd->html('html.name', $name, $options);
	}

	public function qrcode($args = [])
	{
		if (!$this->adapter->showQRCode()) {
			return '';
		}

		// Gonna standardize component qr code to always use foundry qr code in the future.
		$url = $this->adapter->getMobileQrcodeURL();

		// Non easysocial use foundry qr code.
		if (class_exists('FR') && JFactory::getApplication()->input->get('option') !== 'com_easysocial') {
			$url = FR::user()->getMobileQrcodeURL();
		}

		if (!$url) {
			return '';
		}

		$args['url'] = $url;

		$themes = FDT::themes();
		$output = $themes->output('dropdown/qrcode', $args);

		return $output;
	}

	public function avatar($args = [])
	{
		static $loaded = false;

		if (!$loaded) {
			include_once(__DIR__ . '/avatar.php');

			$loaded = new ToolbarHtmlAvatar();
		}
		
		return $loaded->getAvatar($args);
	}
}
