<?php
/**
 * @version		   1.9.10
 * @package        Joomla
 * @subpackage	   Edocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2018-2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die ;
	
JToolBarHelper::title(JText::_('EDOCMAN_IMPORT_DOCUMENTS'),'upload');
JToolBarHelper::save('save', JText::_('Import'));
JToolBarHelper::cancel('cancel');	 
?>
<form action="index.php?option=com_edocman&view=import" method="post" name="adminForm" id="adminForm">
<div class="row-fluid">
	<div class="span12">
		<fieldset class="adminform">
			<legend><?php echo JText::_('EDOCMAN_IMPORT_OPTIONS'); ?></legend>
				<table class="admintable" style="width: 100%;">				
					<tr>
						<td width="100" align="right" class="key" style="padding-right:10px;">
							<?php echo  JText::_('Folder'); ?>
						</td>
						<td>
							<?php echo $this->lists['folder'] ; ?>
						</td>
						<td>
							<small>
								Choose the where your documents are stored and you want to import. If you want to scan the root folder (the <strong>Document path</strong> in the configuration), leave this field empty
							</small>
						</td>
					</tr>
					<tr>
						<td width="100" align="right" class="key" style="padding-right:10px;">
							File Extensions
						</td>
						<td>
							<input class="text_area" type="text" name="exts" id="exts" size="40" maxlength="250" value="<?php echo $this->config->allowed_file_types;?>" />
						</td>
						<td>
							<small>Choose the file types you want to scan and import into EDocman</small>
						</td>
					</tr>							
					<tr>
						<td width="100" align="right" class="key" style="padding-right:10px;">
							<?php echo JText::_('EDOCMAN_CATEGORY'); ?>
						</td>
						<td>
							<?php echo $this->lists['category_id'] ; ?>
						</td>
						<td>
							<small>Choose the category to which the imported documents will be assigned</small>
						</td>
					</tr>
                    <?php
                    if(!$this->config->access_level_inheritance) {
                        ?>
                        <tr>
                            <td width="100" align="right" class="key" style="padding-right:10px;padding-top:10px;" valign="top">
                                Access
                            </td>
                            <td>
                                <?php
                                EDocmanHelper::showCheckboxfield('accesspicker', 0, JText::_('EDOCMAN_PRESETS'), JText::_('EDOCMAN_GROUPS'));
                                ?>
                                <div id="presetsdiv">
                                    <?php
                                    echo $this->lists['access'];
                                    ?>
                                </div>
                                <div id="groupsdiv" style="display:none;">
                                    <?php
                                    echo $this->lists['groups'];
                                    ?>
                                </div>
                            </td>
                            <td>
                                <small>Choose the access level for the importing documents</small>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
			</table>						
		</fieldset>
	</div>
</div>
<div class="clr"></div>	
<?php echo JHtml::_( 'form.token' ); ?>
<input type="hidden" name="task" value="" />
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