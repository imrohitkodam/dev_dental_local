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
?>
<form name="installation" data-installation-form>
	<div class="pp-card pp-container-overflow mb-4 p-3" style="max-height:15vh" data-addons-retrieving>
		<div>
			<?php echo JText::_('COM_PP_INSTALLATION_ADDONS_RETRIEVING_LIST');?>		
		</div>
	</div>

	<div class="d-none mt-4 mb-4" data-addons-progress>
		<div class="mb-3 d-none" data-progress-complete-message><?php echo JText::_('COM_PP_INSTALLATION_MODULE_AND_PLUGINS_COMPLETED');?></div>
		<div class="mb-3" data-progress-active-message>
			<?php echo JText::_('COM_PP_INSTALLATION_INSTALLING_MODULES');?>
		</div>

		<div class="pp-progress mb-3">
			<div class="pp-progress__bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" data-progress-bar style="width: 1%;"></div>
		</div>
	</div>

	<div class="d-none mt-5" data-sync-progress>
		<div class="mb-3 d-none" data-sync-progress-complete-message>
			<?php echo JText::_('COM_PP_INSTALLATION_MAINTENANCE_EXEC_SCRIPTS_SUCCESS');?>
		</div>
		
		<div class="mb-3" data-sync-progress-active-message>
			<?php echo JText::_('COM_PP_INSTALLATION_MAINTENANCE_EXEC_SCRIPTS');?>
		</div>

		<div class="pp-progress mb-3">
			<div class="pp-progress__bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" data-sync-progress-bar style="width: 1%;"></div>
		</div>

		<ol class="pp-install-logs" data-progress-logs>
		</ol>

		<div class="install-progress d-none" style="border-bottom: 1px #ddd;">
			<div class="row-table">
				<div class="col-cell">
					
				</div>
				<div class="col-cell cell-result text-right">
					<div class="progress-result" data-sync-progress-bar-result="">0%</div>
				</div>
			</div>

			<div class="progress">
				<div class="progress-bar progress-bar-info progress-bar-striped" data-sync-progress-bar="" style="width: 0%;"></div>
			</div>

			<ol class="install-logs list-reset" data-progress-logs="">
				<li class="pending" data-progress-execscript>
					<div class="notes">
						<ul style="list-unstyled" data-progress-execscript-items>
						</ul>
					</div>
				</li>
			</ol>
		</div>
	</div>

	<div data-addons-container></div>

	<input type="hidden" name="option" value="com_payplans" />
	<input type="hidden" name="active" value="complete" />
</form>

<?php
$source = $input->get('source', '', 'default');
?>
<script type="text/javascript">
$(document).ready(function(){
	pp.ajaxUrl	= "<?php echo JURI::root();?>administrator/index.php?option=com_payplans&ajax=1";

	// Immediately proceed with synchronization
	pp.options.path = '<?php echo addslashes($source);?>';

	// Retrieve the list of addons
	pp.addons.retrieveList();
});
</script>
