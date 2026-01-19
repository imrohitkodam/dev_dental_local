<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<div data-field-easyblog-bio class="data-field-textarea" data-error-required="<?php echo JText::_('PLG_FIELDS_USER_EASYBLOG_BIO_EMPTY', true);?>">
	<?php echo $editor->display($inputName, $value , '100%', '150', '10', '10', false); ?>

	<div class="es-fields-error-note" data-field-error></div>
</div>

