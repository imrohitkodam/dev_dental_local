<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_ppinstaller
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 * @since		1.5
 */

// no direct access
defined('_JEXEC') or die;
$config = PpinstallerHelperUtils::getConfig();
$installedVersion 	= $config->installedVersion;
$goingToInstall 	= $config->goingToInstall;
$email				= $config->ppinstallerUsername;
$trackingUrl 		= PpinstallerHelperUtils::getServerUrl(true);
$version 			= new JVersion();

$action = 'upgrade';

if (empty($installedVersion)) {
	$action = 'fresh';
}

$event 		= "product.installation";
$event_args = array(
					'domain'=>JURI::getInstance()->toString(array('scheme', 'host', 'port'))
					,'version'=>$goingToInstall
					,'product'=>'Payplans'
					,'email'=>$email
					,'joomla_version'=>$version->RELEASE
					,'old_version'=>$installedVersion
					,'action'=>$action
				);

$event_args = urlencode(json_encode($event_args));

?>

<div class="row">

<h3 class="col-xs-8 pull-left"><?php echo JText::_('COM_PPINSTALLER_THANK_MSG').$config->goingToInstall;  ?></h3>

<?php if(!empty($this->finalizeContent)) : echo "<div class='col-xs-12 pull-left'>".$this->finalizeContent."</div>"; endif; ?>

<div class="clearfix">&nbsp;</div>
<hr>
<div>
	<a class="btn btn-lg btn-primary pull-left" href="<?php echo JRoute::_('index.php?option=com_ppinstaller'); ?>">
		<?php echo JText::_('COM_PPINSTALLER_CHECK_FOR_UPDATE'); ?>
		<span class="glyphicon glyphicon-arrow-up"></span>
	</a>
	<a id="gotodashboard" class="btn btn-lg btn-success pull-right" href="<?php echo JRoute::_('index.php?option=com_payplans'); ?>">
		<?php echo JText::_('COM_PPINSTALLER_HAPPY_PAYPLANS'); ?>
	</a>
</div>
<div style="display:none;">    
	<iframe id="iframe-id" src="http://www.readybytes.net/broadcast/track.html?event=<?php echo $event; ?>&event_args=<?php echo $event_args; ?>" style="display :none;"></iframe>
</div>

</div>
