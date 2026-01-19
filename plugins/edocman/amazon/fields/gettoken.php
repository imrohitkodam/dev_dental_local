<?php 
 /** 
  * @version     1.0 
  * @package     Edocman
  * @copyright   Copyright (C) 2015. All rights reserved. 
  * @license     GNU General Public License version 2 or later; see LICENSE.txt 
  * @author      <your_name> http://www.joellipman.com 
  */ 
  
defined('JPATH_BASE') or die; 
jimport('joomla.html.html');
jimport('joomla.filesystem.folder');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
  
 /** 
  * Supports an HTML select list of options driven by SQL 
  */ 
 class JFormFieldGetToken extends JFormField
 { 
     /** 
      * The form field type. 
      */ 
     public $type = 'gettoken';
  
     /** 
      * Overrides parent's getinput method 
      */ 
     protected function getInput() 
     { 
         // Initialize variables. 
         $html[] = '<strong>To integrate with Amazon S3, you should fill below inputboxes.</strong>';
		 $html[] = '<BR /><strong>1.</strong>&nbsp;Account ID';
		 $html[] = '<BR /><strong>2.</strong>&nbsp;Access Key';
		 $html[] = '<BR /><strong>3.</strong>&nbsp;Secret Key';
		 $html[] = '<BR /><strong>4.</strong>&nbsp;Bucket Name';
		 $html[] = '<BR />To create Amazon S3 Cridential, please read <a href=\'https://www.amazon.com\' target=\'_blank\'>https://www.amazon.com/</a>';

         return implode($html); 
     } 
 } 
 ?> 