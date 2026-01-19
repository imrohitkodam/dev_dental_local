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
<div class="container-fluid">
	<div class="row-fluid">
		<div class="control-group span6">
			<div class="controls">
				<?php echo Text::_('COM_TJLMS_ATTEMPT_STATUS_CHANGE'); ?>

				<?php $lessonStatus = $this->lessonStatusList();

				echo HTMLHelper::_('select.genericlist',$lessonStatus,"attempt_status".$i,'class="pad_status input-small" id="attempt_status"',"value","text"); ?>
			</div>
		</div>
	</div>
</div>
