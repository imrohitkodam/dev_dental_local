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

$sections = array(
	'sql' => 'COM_PP_INSTALLATION_INITIALIZING_DB',
	'foundry' => 'COM_PP_INSTALLATION_INITIALIZING_FOUNDRY',
	'admin' => 'COM_PP_INSTALLATION_INITIALIZING_ADMIN',
	'site' => 'COM_PP_INSTALLATION_INITIALIZING_SITE',
	'languages' => 'COM_PP_INSTALLATION_INITIALIZING_LANGUAGES',
	'media' => 'COM_PP_INSTALLATION_INITIALIZING_MEDIA',
	'toolbar' => 'COM_PP_INSTALLATION_INITIALIZING_TOOLBAR',
	'syncdb' => 'COM_PP_INSTALLATION_INITIALIZING_DB_SYNCHRONIZATION',
	'postinstall' => 'COM_PP_INSTALLATION_POST_INSTALLATION_CLEANUP'
);
?>
<?php foreach ($sections as $key => $value) { ?>
<li class="pp-install-logs__item" data-progress-<?php echo $key;?>>
	<div class="pp-install-logs__title">
		<?php echo JText::_($value);?>
	</div>

	<?php include(__DIR__ . '/log.state.php'); ?>
</li>
<?php } ?>