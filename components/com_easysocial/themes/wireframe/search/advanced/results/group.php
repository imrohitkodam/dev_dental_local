<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-search-group__item o-grid">
	<div class="o-grid__cell">
		<div class="o-flag__image o-flag--top">
			<?php echo $this->html('avatar.cluster', $item); ?>
		</div>

		<div class="o-flag__body">
			<a href="<?php echo $item->getPermalink();?>"><?php echo $item->getName();?></a>
			
			<ul class="g-list-inline g-list-inline--dashed t-text--muted">
                <li>
                    <i class="fa fa-folder"></i>&nbsp; <a href="<?php echo $item->getCategory()->getPermalink();?>"><?php echo $item->getCategory()->getTitle();?></a>
                </li>
                <li>
                    <i class="fa fa-user"></i>&nbsp; <a href="<?php echo $item->getCreator()->getPermalink();?>"><?php echo $item->getCreator()->getName();?></a>
                </li>
                <li>
                    <i class="fa fa-users"></i>&nbsp; <?php echo JText::sprintf(ES::string()->computeNoun('COM_EASYSOCIAL_GROUPS_MEMBERS', $item->getTotalMembers()), $item->getTotalMembers() ); ?>
                </li>
                <li>
                    <i class="fa fa-calendar"></i>&nbsp; <?php echo $item->getCreatedDate()->format(JText::_('DATE_FORMAT_LC')); ?>
                </li>
			</ul>
		</div>
	</div>

	<div class="o-grid__cell o-grid__cell--auto-size">
		<?php echo $this->html('group.action', $item); ?>
	</div>
</div>
