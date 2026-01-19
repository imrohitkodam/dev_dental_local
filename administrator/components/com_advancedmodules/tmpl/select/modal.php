<?php
/**
 * @package         Advanced Module Manager
 * @version         10.4.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

$this->modalLink = '&tmpl=component&view=module&layout=modal';
?>
<div class="container-popup">
    <?php $this->setLayout('default'); ?>
    <?php echo $this->loadTemplate(); ?>
</div>
