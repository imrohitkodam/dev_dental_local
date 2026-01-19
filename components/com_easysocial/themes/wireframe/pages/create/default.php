<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-container">
    <div class="es-snackbar">
        <h1 class="es-snackbar__title"><?php echo JText::_('COM_EASYSOCIAL_PAGES_SELECT_CATEGORY');?></h1>
    </div>
    <p><?php echo JText::_('COM_EASYSOCIAL_PAGES_SELECT_CATEGORY_INFO'); ?></p>

    <div class="es-create-category-select">
        <?php foreach($categories as $category){ ?>
            <div class="btn-wrap">
                <a class="btn btn-es" href="<?php echo ESR::pages(array('controller' => 'pages' , 'task' => 'selectCategory' , 'category_id' => $category->id));?>">
                    <img class="avatar" src="<?php echo $category->getAvatar(SOCIAL_AVATAR_SQUARE);?>" alt="<?php echo $this->html('string.escape', $category->getTitle());?>">
                    <div class="es-title">
                        <?php echo $category->getTitle();?>
                    </div>
                </a>
            </div>
        <?php } ?>
    </div>
</div>
