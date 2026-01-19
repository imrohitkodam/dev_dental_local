<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_tjlms
 *
 * @copyright   Copyright (C) 2005 - 2021 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;

?>

<button class="btn" type="button" data-dismiss="modal">
    <?php echo Text::_('JCANCEL'); ?>
</button>
<button class="btn btn-success" type="button" onclick="Joomla.submitbutton('attemptreport.changeLessonStatus');">
    <?php echo Text::_('JGLOBAL_BATCH_PROCESS'); ?>
</button>

