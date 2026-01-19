<?php
/**
 * @version        1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
$config = EDocmanHelper::getConfig();
EDocmanHelper::chosen('select.chosen');
if (version_compare(JVERSION, '3.0', 'ge')) {
	$sidebar = JHtmlSidebar::render();
}
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'cancel' || document.formvalidator.isValid(document.getElementById('item-form')))
		{
			<?php echo $this->form->getField('description')->save(); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} 
		else 
		{
			alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>
<?php
?>
<form action="<?php echo JRoute::_('index.php?option=com_edocman&view=category&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="item-form" class="form-validate" enctype="multipart/form-data">
	<div class="row-fluid">
        <?php echo JHtml::_('bootstrap.startTabSet', 'categoryTab', array('active' => 'general-page')); ?>
            <?php echo JHtml::_('bootstrap.addTab', 'categoryTab', 'general-page', JText::_('EDOCMAN_GENERAL')); ?>
            <table width="100%">
                <tr>
                    <td width="65%" valign="top" style="padding-right:10px;">
                        <table width="100%" class="adminform">
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('title'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('title'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('alias'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('alias'); ?>
                                </td>
                            </tr>

                            <tr>
                                <td colspan="2">
                                    <?php echo $this->form->getLabel('description'); ?>
                                    <div class="clr"></div>
                                    <?php echo $this->form->getInput('description'); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                    <td width="45%" valign="top">
                        <table width="100%" class="adminform rightsidetable">
                            <tr>
                                <th colspan="2">
                                    <strong><?php echo JText::_('JGLOBAL_FIELDSET_PUBLISHING');?></strong>
                                </th>
                            </tr>
                            <tr>
                                <td class="key" valign="top" width="35%">
                                    <?php echo $this->form->getLabel('accesspicker'); ?>
                                </td>
                                <td width="65%">
                                    <?php
                                    EDocmanHelper::showCheckboxfield('accesspicker', $this->item->accesspicker, JText::_('EDOCMAN_PRESETS'), JText::_('EDOCMAN_GROUPS'));
                                    ?>
                                    <div class="clr"></div>
                                    <?php
                                    if($this->item->accesspicker == 0){
                                        $style1 = "display:block";
                                        $style2 = "display:none";
                                    }else{
                                        $style2 = "display:block";
                                        $style1 = "display:none";
                                    }
                                    ?>
                                    <div class="control-group" id="presetsdiv" style="<?php echo $style1;?>" data-showon='<?php echo EDocmanHelper::renderShowon(array('jform[accesspicker]' => '0')); ?>'>
                                        <?php echo $this->form->getInput('access'); ?>
                                    </div>
                                    <div class="control-group" id="groupsdiv" style="<?php echo $style2;?>" data-showon='<?php echo EDocmanHelper::renderShowon(array('jform[accesspicker]' => '1')); ?>'>
                                        <?php echo $this->form->getLabel('groups'); ?>
                                        <?php echo $this->form->getInput('groups'); ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('published'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('published'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('auto_approval'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('auto_approval'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('hide_download'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('hide_download'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('show_view'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('show_view'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('user_ids'); ?>
                                </td>
                                <td>
                                    <?php echo EdocmanHelper::getUserInput($this->item->user_ids); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('notification_emails'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('notification_emails'); ?>
                                </td>
                            </tr>

                            <?php
                            if (JPluginHelper::isEnabled('edocman', 'notification'))
                            {
                                ?>
                                <tr>
                                    <td class="key">
                                        <?php echo $this->form->getLabel('notify_group_ids'); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->form->getInput('notify_group_ids'); ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('id'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('id'); ?>
                                </td>
                            </tr>
                        </table>
                        <BR />
                        <table width="100%" class="adminform rightsidetable">
                            <tr>
                                <th colspan="2">
                                    <strong><?php echo JText::_('EDOCMAN_FOLDER');?></strong>
                                </th>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('parent_id'); ?>
                                </td>
                                <td style="padding-bottom: 10px;">
                                    <?php echo $this->form->getInput('parent_id'); ?>
                                </td>
                            </tr>
                            <?php
                            if ($config->activate_herachical_folder_structure) {
                                ?>
                                <tr>
                                    <td class="key">
                                        <?php echo $this->form->getLabel('path'); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->form->getInput('path'); ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('category_layout'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('category_layout'); ?>
                                </td>
                            </tr>


                            <?php
                            if ($config->activate_multilingual_feature) {
                                ?>
                                <tr>
                                    <td class="key">
                                        <?php echo $this->form->getLabel('language'); ?>
                                    </td>
                                    <td>
                                        <?php echo $this->form->getInput('language'); ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </table>
                        <BR />
                        <table width="100%" class="adminform rightsidetable">
                            <tr>
                                <th colspan="2">
                                    <strong><?php echo JText::_('EDOCMAN_SORT_OPTION');?></strong>
                                </th>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('sort_option'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('sort_option'); ?>
                                </td>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('sort_direction'); ?>
                                </td>
                                <td>
                                    <?php echo $this->form->getInput('sort_direction'); ?>
                                </td>
                            </tr>
                        </table>
                        <BR />
                        <table width="100%" class="adminform rightsidetable">
                            <tr>
                                <th colspan="2">
                                    <strong><?php echo JText::_('EDOCMAN_IMAGE');?></strong>
                                </th>
                            </tr>
                            <tr>
                                <td class="key">
                                    <?php echo $this->form->getLabel('image'); ?>
                                </td>
                                <td>
                                    <?php
                                    if ($this->item->image && file_exists(JPATH_ROOT.'/media/com_edocman/category/thumbs/'.$this->item->image)) {
                                        ?>
                                        <img src="<?php echo JUri::root()?>/media/com_edocman/category/thumbs/<?php echo $this->item->image;?>"  class="img-polaroid" />
                                        <div class="clearfix"></div>
                                        <input type="checkbox" name="del_image" value="1" title="<?php echo JText::_('EDOCMAN_DEL_IMAGE_DESC'); ?>" />
                                        <?php echo JText::_('EDOCMAN_DELETE_THUMBNAIL');?>?
                                        <div class="clearfix"></div>
                                    <?php
                                    }
                                    ?>
                                    <?php echo $this->form->getInput('image'); ?>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php echo JHtml::_('bootstrap.addTab', 'categoryTab', 'permission-page', JText::_('Permissions')); ?>
                <?php
                if ($this->canDo->get('core.admin'))
                {
                    echo $this->form->getInput('rules');
                }
                ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php echo JHtml::_('bootstrap.addTab', 'categoryTab', 'publishing-details', JText::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
                <?php echo $this->loadTemplate('options'); ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
            <?php echo JHtml::_('bootstrap.addTab', 'categoryTab', 'meta-options', JText::_('JGLOBAL_FIELDSET_METADATA_OPTIONS')); ?>
                <?php echo $this->loadTemplate('metadata'); ?>
            <?php echo JHtml::_('bootstrap.endTab'); ?>
        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
		<input type="hidden" name="task" value="" />	
		<?php echo JHtml::_('form.token'); ?>
	</div>
</form>
<script type="text/javascript">
    function updateRadioButton(select_item){
        if(select_item == '0'){
            jQuery('#presetsdiv').slideDown();
            jQuery('#groupsdiv').slideUp();
        }else{
            jQuery('#presetsdiv').slideUp();
            jQuery('#groupsdiv').slideDown();
        }
    }
</script>