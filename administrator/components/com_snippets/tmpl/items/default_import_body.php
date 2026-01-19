<?php
/**
 * @package         Snippets
 * @version         9.3.8
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$published = (int) $this->state->get('filter.published');

$user = Factory::getUser();
?>

<div class="container rl-modal">
    <div class="form-vertical">
        <?php echo $this->form->renderFieldset('import'); ?>
    </div>
</div>
