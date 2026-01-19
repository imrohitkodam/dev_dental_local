<?php
/**
* @version    SVN: <svn_id>
* @package    Quick2cart
* @author     Techjoomla <extensions@techjoomla.com>
* @copyright  Copyright (c) 2009-2015 TechJoomla. All rights reserved.
* @license    GNU General Public License version 2 or later.
*/

// No direct access
defined('_JEXEC') or die();
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

$lang      = Factory::getLanguage();
$extension = 'com_jticketing';
$base_dir  = JPATH_SITE;
$lang->load($extension, $base_dir);
$input       = Factory::getApplication()->input;
$category_id = $input->get('category_id', '', 'INT');
$jticketingfrontendhelper = JPATH_ROOT .'/components/com_jticketing/helpers/frontendhelper.php';

if (!class_exists('jticketingfrontendhelper'))
{
	JLoader::register('jticketingfrontendhelper', $jticketingfrontendhelper);
	JLoader::load('jticketingfrontendhelper');
}

// Load assets
JT::utilities()->loadjticketingAssetFiles();

   if ($categorylists)
   {
   ?>
<div class="es-filterbar">
   <div class="filterbar-title h5 pull-left">
      <?php
         echo Text::_('APP_JTICKETMYEVENTS_USER_TITLE');
         ?>
   </div>
   <div class="pull-right">
      <?php
         $options = array();

         $default = '';

         foreach ($categorylists as $categorylist)
         {
         	$options[] = HTMLHelper::_('select.option', $categorylist->value, $categorylist->text);
         }

         $text = Text::_('APP_JTICKETMYEVENTS_SELECT_CATEGORY_DESC');
         $class = 'class="chzn-done "  size="1" title="' . $text . '" data-chosen="com_jticketing"';
         echo $this->dropdown = HTMLHelper::_('select.genericlist', $options, 'category_id', $class, 'value', 'text', $default, 'category_id');
         ?>
      <div class=""> &nbsp;</div>
   </div>
</div>
<div class="clearfix"> &nbsp;</div>
<?php
   }
   ?>
<div class="<?php echo JTICKETING_WRAPPER_CLASS; ?>">

<div class="app-jticketMyEvents" data-jticketMyEvents>
   <div class="row-fluid app-contents<?php
      echo !$events ? ' is-empty' : '';
      ?>">
      <?php
         if ($events)
         {
         	$random_container = 'jticket_pc_es_app_my_products';
         ?>
      <div id="jticket_pc_es_app_my_products">
         <?php
            foreach ($events as $eventdata)
            {
            	include JPATH_SITE . '/components/com_jticketing/views/events/tmpl/eventpin.php';
            }
            ?>
      </div>
      <?php
         }
         else
         {
         ?>
      <div class="empty">
         <i class="ies-droplet"></i>
         <?php
            echo Text::sprintf('APP_JTICKETMYEVENTS_NO_EVENTS_FOUND', $user->getName());
            ?>
      </div>
      <?php
         }
         ?>
   </div>
   <?php
      if ($eventsCount > $limit)
      {
      	echo $html = "
      		<div class='row-fluid span12'>
      			<div class='pull-right'>
      				<a href='" . $allevent_link . "'>" . Text::_('APP_JTICKETMYEVENTS_SHOW_ALL') . " (" . $eventsCount . ") </a>
      			</div>
      			<div class='clearfix'>&nbsp;</div>
      		</div>";
      }
      ?>
</div>
</div>
<?php
   // Calulate columnWidth (columnWidth = pin_width+pin_padding)
   $columnWidth = $pin_width + $pin_padding;
   ?>
<style type="text/css">
   .jticket_pin_item_<?php
      echo $random_container;
      ?> { width: <?php
      echo $pin_width . 'px';
      ?> !important; }
</style>
<?php
Factory::getDocument()->addScriptDeclaration("
   var pin_container_" . $random_container . " = 'jticket_pc_es_app_my_products'

   techjoomla.jQuery(document).ready(function()
   {
   	initiateJticketPins();
   });

   function initiateJticketPins()
   {
   	var container_" . $random_container . " = document.getElementById(pin_container_" . $random_container . ");
   	var msnry = new Masonry( container_" . $random_container . ",
      {
   		columnWidth:" . $columnWidth . ",
   		itemSelector: '.jticket_pin_item_" . $random_container . "',
   		gutter: " . $pin_padding . "
      });

      setTimeout(function(){
         var container_" . $random_container . " = document.getElementById(pin_container_" . $random_container . ");
         var msnry = new Masonry( container_" . $random_container . ",
         {
         	columnWidth:" . $columnWidth . ",
         	itemSelector: '.jticket_pin_item_" . $random_container . "',
         	gutter: " . $pin_padding. "
         });
      }, 1000);

      setTimeout(function(){
      	var container_" . $random_container . " = document.getElementById(pin_container_" . $random_container . ");
      	var msnry = new Masonry( container_" . $random_container . ", 
            {
         		columnWidth: " . $columnWidth . ",
         		itemSelector: '.jticket_pin_item_" . $random_container . "',
         		gutter: " . $pin_padding . "
            });
      	}, 3000);
   }

   techjoomla.jQuery('#category_id').change(function()
   {
   	var val = techjoomla.jQuery(this).val();
   	var url = window.location;
   	javascript:void(0);
   	var newURLString = window.location.href + '&category_id=' + val;

   	jQuery.ajax({
   		type     : 'post',
   		url      : '?option=com_jticketing&task=updateEasysocialApp',
   		data     : {'category_id' : val,'uid' : " . $userId . ",'total' : " . $total . "},
   		dataType : 'json',
   		success  : function(data)
   		{
   			jQuery('.app-jticketMyEvents').html(data.html);
   			eval(data.js);
   		},
   		error : function(data)
   		{
   			console.log('error');
   		}
   	});
   });");
