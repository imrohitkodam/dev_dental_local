<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH'))) {
    die('Restricted access');
};
JHTML::_('behavior.tooltip');

?>

<div class="sourcecoast">
    <form method="post" id="adminForm" name="adminForm">
        <div class=row-fluid>
            <div class="span7">
                <div class="well">
                    <legend><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_OBJECT_SETTINGS_LABEL'); ?></legend>
                    <div class="control-group">
                        <label
                            class="readonly config_setting"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_EXTENSION_LABEL'); ?></label>

                        <div class="controls">
                            <input type="text"
                                   class="readonly"
                                   readonly="readonly"
                                   name="plugin"
                                   value="<?php echo $this->object->plugin; ?>">
                        </div>
                    </div>
                    <div class="control-group">
                        <label
                            class="readonly config_setting"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_LAYOUT_LABEL'); ?></label>

                        <div class="controls">
                            <input type="text"
                                   class="readonly"
                                   readonly="readonly"
                                   name="system_name"
                                   value="<?php echo $this->object->system_name; ?>"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="display_name" class="required hasTip config_setting"
                               title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_NAME_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_NAME_LABEL'); ?></label>

                        <div class="controls">
                            <input id="display_name" class="inputbox" type="text" name="display_name"
                                   value="<?php echo $this->object->display_name; ?>"/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label for="type" class="required hasTip config_setting"
                               title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_TYPE_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_TYPE_LABEL'); ?></label>

                        <div class="controls">
                            <select id="type" class="list" name="type">
                                <?php foreach ($this->object_types as $type => $fields) {
                                        $selected = $type == $this->object->type ? 'selected="selected"' : '';
                                    ?>
                                        <option value="<?php echo $type ?>" <?php echo $selected ?> ><?php echo $fields->title ?></option>
                                  <?php }  ?>
                            </select>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="hasTip config_setting" for="published"
                               title="<?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_STATUS_DESC'); ?>"><?php echo JText::_('COM_JFBCONNECT_OPENGRAPH_OBJECTEDIT_STATUS_LABEL'); ?></label>
                        <div class="controls">
                            <select name="published">
                                <option
                                    value="1" <?php echo $this->object->published ? 'selected' : "" ?> ><?php echo JText::_('JPUBLISHED'); ?></option>
                                <option
                                    value="0" <?php echo $this->object->published ? "" : 'selected' ?> ><?php echo JText::_('JUNPUBLISHED'); ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($this->params) {
                echo '<div class="span5">';
                foreach ($this->params->getFieldsets() as $fieldsets => $fieldset) {
                    ?>
                    <fieldset class="adminform">
                        <legend><?php echo $fieldset->name; ?></legend>
                        <dl>
                            <?php
                            // Iterate through the fields and display them.
                            foreach ($this->params->getFieldset($fieldset->name) as $field) {
                                // If the field is hidden, only use the input.
                                if ($field->hidden) {
                                    echo $field->input;
                                } else {
                                    ?>
                                    <dt>
                                        <?php echo $field->label; ?>
                                    </dt>
                                    <dd>
                                        <?php echo $field->input ?>
                                    </dd>
                                <?php
                                }
                            }
                            ?>
                        </dl>
                    </fieldset>
                <?php
                }
                echo '</div>';
                ?>
            <?php } ?>
        </div>
        <input type="hidden" name="plugin" value="<?php echo $this->object->plugin ?>"/>
        <input type="hidden" name="system_name" value="<?php echo $this->object->system_name ?>"/>

        <input type="hidden" name="id" value="<?php echo $this->object->id; ?>"/>
        <input type="hidden" name="option" value="com_jfbconnect"/>
        <input type="hidden" name="controller" value="opengraph"/>
        <input type="hidden" name="formtype" value="object"/>
        <input type="hidden" name="task" value=""/>
        <?php echo JHTML::_('form.token'); ?>

    </form>
</div>