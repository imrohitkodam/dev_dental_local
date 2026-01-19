<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Certificates
 * @author     Parth Lawate <contact@techjoomla.com>
 * @copyright  2016 Parth Lawate
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// @deprecated  1.3.32 Use TJCertificate template view instead
// No direct access
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

$document = Factory::getDocument();
$conf        = Factory::getConfig();
$editor_name = $conf->get('editor'); 
$document->addScriptDeclaration('tjlmsAdmin.certificateTemplate.editor = "' . $editor_name . '"');
$document->addScriptDeclaration('tjlmsAdmin.certificateTemplate.usedCertCount = ' . $this->usedCertCount);
?>

<script type="text/javascript">
	tjlmsAdmin.certificateTemplate.init();
</script>
<form
	action="<?php echo Route::_('index.php?option=com_tjlms&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" enctype="multipart/form-data" name="adminForm" id="certificatetemplate-form" class="form-validate">

	<div class="form-horizontal">
	
		
		<?php if ($this->canDo->get('core.admin')) : ?>
				<?php echo HTMLHelper::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

				<?php echo HTMLHelper::_('bootstrap.addTab', 'myTab', 'general', Text::_('COM_TJLMS_TITLE_CERTIFICATE', true)); ?>
		<?php endif; ?>

		<div class="row-fluid">
			<div class="span9 form-horizontal">
				<fieldset class="adminform">
				<?php
				echo $this->form->renderField('id'); 
				echo $this->form->renderField('created_by'); 
				echo $this->form->renderField('modified_by'); 
				echo $this->form->renderField('modified_date'); 
				echo $this->form->renderField('ordering'); 
				echo $this->form->renderField('state'); 
				echo $this->form->renderField('checked_out'); 
				echo $this->form->renderField('checked_out_time');
				echo $this->form->renderField('title');
				echo $this->form->renderField('certificatetemplate'); 
				echo $this->form->renderField('body');
				echo $this->form->renderField('access');
			 ?>

				</fieldset>
			</div>
			<div class="span3">
				<table class="table">
					<tr>
						<td colspan="2"><div class="alert alert-info"><?php echo Text::_('COM_TJLMS_CSS_EDITOR_MSG') ?> </div>
							<?php echo $this->form->getInput('template_css');?>
						</td>
					</tr>
					<tr>
						<td colspan="2"><div class="alert alert-info"><?php echo Text::_('COM_TJLMS_EB_TAGS_DESC') ?> </div>
					</tr>
					<tr>
						<td width="30%"><b>&nbsp;&nbsp;[STUDENTNAME] </b> </td>
						<td><?php echo Text::_('COM_TJLMS_TAG_STUDENTNAME'); ?></td>
					</tr>
					<tr>
						<td width="30%"><b>&nbsp;&nbsp;[STUDENTUSERNAME] </b> </td>
						<td><?php echo Text::_('COM_TJLMS_TAG_STUDENTUSERNAME'); ?></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;[COURSE]</b></td>
						<td><?php echo Text::_('COM_TJLMS_TAG_COURSE'); ?></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;[GRANTED_DATE]</b></td>
						<td><?php echo Text::_('COM_TJLMS_TAG_CER_GRANT_DATE'); ?></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;[EXPIRY_DATE]</b></td>
						<td><?php echo Text::_('COM_TJLMS_TAG_CER_EXPIRY_DATE'); ?></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;[TOTAL_TIME]</b></td>
						<td><?php echo Text::_('COM_TJLMS_TAG_CER_TIME_SPENT'); ?></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;[CERT_ID]</b></td>
						<td><?php echo Text::_('COM_TJLMS_TAG_CER_ID'); ?></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;[jsfield:FIELD_CODE]</b></td>
						<td><?php echo Text::_('COM_TJLMS_TAG_JS_FIELD'); ?></td>
					</tr>
					<tr>
						<td><b>&nbsp;&nbsp;[esfield:unique_key]</b></td>
						<td><?php echo Text::_('COM_TJLMS_TAG_ES_FIELD'); ?></td>
					</tr>
				</table>
			</div>
		</div>
		<?php if ($this->canDo->get('core.admin')) : ?>
					<?php echo HTMLHelper::_('bootstrap.endTab'); ?>
		<?php endif;
		
		// Loading joomla's params layout to show the fields and field group added in params layout.
		echo LayoutHelper::render('joomla.edit.params', $this);
		?>

		<input type="hidden" name="task" value=""/>
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
