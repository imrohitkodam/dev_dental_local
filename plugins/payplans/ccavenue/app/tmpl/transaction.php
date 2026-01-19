<?php
/**
* @package		PayPlans
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* PayPlans is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php if (!empty($transaction_html)) { ?>
<div class="form-horizontal">  
	<?php foreach ($transaction_html as $key => $value) { ?>	
		<div class="row-fluid">
			<div class="span6"><?php echo $key;?></div>
 	     	<div class="span6"><?php echo $value;?></div>
 	     </div>
     <?php } ?>
</div>
<?php } ?>
<?php 