<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use RegularLabs\Library\User as RL_User;

$displayData = [
    'textPrefix' => 'COM_MODULES',
    'formURL'    => 'index.php?option=com_advancedmodules&view=select&client_id=' . $this->clientId,
    'helpURL'    => 'https://docs.joomla.org/Special:MyLanguage/Module',
    'icon'       => 'icon-cube module',
    // Although it is (almost) impossible to get to this page with no created Administrator Modules, we add this for completeness.
    'title'      => Text::_('COM_MODULES_EMPTYSTATE_TITLE_' . ($this->clientId ? 'ADMINISTRATOR' : 'SITE')),
];

if (RL_User::authorise('core.create', 'com_modules'))
{
    $displayData['createURL'] = 'index.php?option=com_advancedmodules&view=select&client_id=' . $this->clientId;
}

echo LayoutHelper::render('joomla.content.emptystate', $displayData);
