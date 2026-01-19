<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldWidgetSettings extends JFormField
{
    public $type = 'WidgetSettings';

    protected function getInput()
    {
        $html = '';
        if($this->form->getValue('params.provider_type') && $this->form->getValue('params.widget_type') != 'widget')
        {
            JFBConnectUtilities::loadLanguage('com_jfbconnect', JPATH_ADMINISTRATOR);

            $xmlPath = JPATH_ROOT .'/components/com_jfbconnect/libraries/provider/'.$this->form->getValue('params.provider_type').'/widget';
            if(!JFolder::exists($xmlPath))
            {
                $xmlPath = JPATH_ROOT . '/plugins/jfbconnect/provider_'. $this->form->getValue('params.provider_type').'/provider/'. $this->form->getValue('params.provider_type') . '/widget';
            }
            JForm::addFieldPath($xmlPath);
            $xmlFile = $xmlPath.'/'.$this->form->getValue('params.widget_type').'.xml';

            if(JFile::exists($xmlFile))
            {
                $options = array('control' => 'jform');
                $form = JForm::getInstance('com_jfbconnect_' . $this->form->getValue('params.widget_type'), $xmlFile, $options);

                $registry = $this->form->getValue('params');

                $settings = new JRegistry();
                $settings->set('params.widget_settings', $registry->widget_settings);
                $form->bind($settings);
                ob_start();
                foreach ($form->getFieldsets() as $fieldsets => $fieldset)
                {
                    foreach ($form->getFieldset($fieldset->name) as $field)
                        $this->formShowField($field);
                }
                $html = ob_get_clean();
            }
        }

        return '<div id="widget_settings">' . $html . '</div>';
    }

    protected function getLabel()
    {
        return '';
    }

    public function formShowField($field)
    {
        echo "  <div class=\"control-group\">\n";
        echo "    <div class=\"control-label\">\n";
        echo "   " . $field->getLabel() . "\n";
        echo "   </div>\n";
        echo "     <div class=\"controls\">\n";
        echo "       " . $field->getInput() . "\n";
        echo "     </div>\n";
        echo "  </div>\n";
    }

    public function getControlGroup()
   	{
        return $this->getInput();
   	}
}
