<?php

include_once __DIR__.DIRECTORY_SEPARATOR.'AcymJFormField.php';

class JFormFieldHelp extends AcymJFormField
{
    var $type = 'help';

    public function getInput()
    {
        if ('Joomla' === 'Joomla') {
            $ds = DIRECTORY_SEPARATOR;
            $helper = rtrim(JPATH_ADMINISTRATOR, $ds).$ds.'components'.$ds.'com_acym'.$ds.'Core'.$ds.'init.php';
            if (!include_once $helper) {
                echo 'This extension cannot work without AcyMailing';
            }
        }

        $config = acym_config();
        $level = $config->get('level');
        $link = ACYM_DOCUMENTATION;

        return '<a class="btn" target="_blank" href="'.$link.'">'.acym_translation('ACYM_HELP').'</a>';
    }
}
