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

class ToolbarSso
{
	public function render()
	{
		$adapter = FDT::getAdapter(FDT::getMainComponent());
		$buttons = $adapter->getSSO();

		// If there is no button, then just return empty.
		if (!$buttons) {
			return '';
		}

		// Format the button.
		$this->format($buttons);

		$themes = FDT::themes();
		return $themes->output('social/default', ['buttons' => $buttons]);
	}

	public function format(&$buttons)
	{
		foreach ($buttons as $key => &$button) {
			if (is_null($button)) {
				unset($buttons[$key]);
				continue;
			}

			// Since the button was already created by ES, and there is no way to re-create the button,
			// we'll just simply get the url from the generated button and put it on the toolbar.
			preg_match('/data-url=\"(.*)\"/i', $button, $redirectUrl);

			$authorizedUrl = '';

			// Facebook seems to behave differently.
			if (!isset($redirectUrl[0])) {
				preg_match('/href=\"(.*)\" /i', $button, $matches);

				if (isset($matches[1])) {
					$authorizedUrl = $matches[1];
				}
			}

			$attributes = '';
			$url = '';

			if (isset($redirectUrl[1])) {
				$url = $redirectUrl[1];
			}

			if (!$authorizedUrl && $url) {
				$attributes .= 'data-fd-oauth-login-button data-url="' . $url . '"';
			}

			// This attribute use to determine that whether need to show popup during social login
			// Current Behavior:
			// Joomla 3 - Show pop up when the user clicks the Linkedin, Twitter, and Google login buttons.
			// Joomla 4 - No show pop-up when the user clicks the Linkedin, Twitter, and Google login buttons.
			// Facebook always no show pop up in Joomla 3/ Joomla 4.
			// data-popup = 1 then show popup
			$showPopup = 0;
			preg_match('/data-popup=\"(.*)\"/i', $button, $showPopupMatches);

			if (isset($showPopupMatches[1])) {
				$showPopup = $showPopupMatches[1];
				$attributes .= ' data-popup="' . $showPopup . '"';
			}

			$button = (object) [
				'title' => JText::_('MOD_SI_TOOLBAR_' . strtoupper($key)),
				'attributes' => $attributes,
				'authorizedUrl' => $authorizedUrl,
			];
		}
	}
}