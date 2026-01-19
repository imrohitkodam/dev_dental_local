<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.13.0
 * @author	acyba.com
 * @copyright	(C) 2009-2023 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><?php
if (!include_once(rtrim(
        JPATH_ADMINISTRATOR,
        DIRECTORY_SEPARATOR
    ).DIRECTORY_SEPARATOR.'components'.DIRECTORY_SEPARATOR.'com_acymailing'.DIRECTORY_SEPARATOR.'helpers'.DIRECTORY_SEPARATOR.'helper.php')) {
    echo 'This module can not work without the AcyMailing Component';
}

if (!ACYMAILING_J16) {
    class JElementYear extends JElement
    {
        function fetchElement($name, $value, &$node, $control_name)
        {
            $values = [0 => 'All'];
            $yearMax = date('Y');
            for ($i = 2009 ; $i <= $yearMax ; $i++) {
                $values[$i] = $i;
            }

            return acymailing_select(
                $values,
                $control_name.'[year]',
                'class="inputbox" style="max-width:220px" size="1" ',
                'value',
                'text',
                (int)$value,
                str_replace(['[', ']'], ['_', ''], $control_name.'[year]')
            );
        }
    }
} else {
    class JFormFieldYear extends JFormField
    {
        var $type = 'year';

        function getInput()
        {
            $values = [0 => 'All'];
            $yearMax = date('Y');
            for ($i = 2009 ; $i <= $yearMax ; $i++) {
                $values[$i] = $i;
            }

            return acymailing_select(
                $values,
                $this->name,
                'class="inputbox" style="max-width:220px" size="1" ',
                'value',
                'text',
                (int)$this->value,
                str_replace(['[', ']'], ['_', ''], $this->name)
            );
        }
    }
}
