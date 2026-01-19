<?php
/**
* @package      EasyBlog
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

$ratioList = array(
    array(
        'name'      => 'Wide',
        'caption'   => '16:9',
        'value'     => '16:9',
        'padding'   => '56.25%',
        'classname' => 'ar-wide'
    ),
    array(
        'name'      => 'Normal',
        'caption'   => '4:3',
        'value'     => '4:3',
        'padding'   => '75%',
        'classname' => 'ar-photo'
    ),
    array(
        'name'      => 'Square',
        'caption'   => '1:1',
        'value'     => '1:1',
        'padding'   => '100%',
        'classname' => 'ar-square'
    ),
    array(
        'name'      => 'Unlocked',
        'caption'   => '<i class="fa fa-unlock-alt"></i>',
        'value'     => '0',
        'padding'   => '100%',
        'classname' => 'ar-unlocked'
    )
);
?>
<div class="eb-composer-fieldset eb-video-size-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_FIELDS_VIDEO_SIZE'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">

        <div class="eb-composer-field style-bordered eb-video-size-field" data-eb-video-size-field>

            <div class="eb-composer-fieldgroup eb-video-dimension-field">
                <div class="eb-composer-fieldgroup-content">
                    <?php echo $this->output('site/composer/fields/numslider', array(
                            'name'   => 'video-width',
                            'type'   => 'video-width',
                            'label'  => JText::_('COM_EASYBLOG_COMPOSER_FIELDS_WIDTH'),
                            'toggle' => false,
                            'units'  => array('pixel', 'percent'),
                            'defaultUnit' => 'percent'
                        )); ?>

                    <?php echo $this->output('site/composer/fields/numslider', array(
                            'name'   => 'video-height',
                            'type'   => 'video-height',
                            'label'  => JText::_('COM_EASYBLOG_COMPOSER_FIELDS_HEIGHT'),
                            'toggle' => false,
                            'units'  => array('pixel')
                        )); ?>

                    <div class="eb-video-ratio-toggle">
                        <div>
                            <button type="button" class="btn btn-default eb-video-ratio-button" data-eb-video-ratio-button>
                                <i class="fa fa-lock"></i>
                                <i class="fa fa-unlock-alt"></i>
                                <span class="eb-video-ratio-label" data-eb-video-ratio-label>16:9</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eb-composer-fieldgroup eb-video-ratio-field">
                <div class="eb-composer-fieldgroup-content">
                    <div class="eb-composer-field">
                        <div class="eb-composer-fieldrow-label">
                            <?php echo JText::_('COM_EASYBLOG_COMPOSER_FIELDS_SELECT_ASPECT_RATIO'); ?>
                        </div>
                        <div class="eb-swatch swatch-grid eb-video-ratio-swatch">
                            <div class="row">
                                <?php foreach ($ratioList as $ratio) { ?>
                                <div class="col-xs-3">
                                    <div class="eb-swatch-item eb-video-ratio-selection" data-eb-video-ratio-selection data-value="<?php echo $ratio['value']; ?>">
                                        <div class="eb-swatch-preview is-responsive">
                                            <div><div>
                                                <div class="eb-video-ratio-preview <?php echo $ratio['classname']; ?>">
                                                    <div style="padding-top: <?php echo $ratio['padding']; ?>">
                                                        <div><span><?php echo $ratio['caption']; ?></span></div>
                                                    </div>
                                                </div>
                                            </div></div>
                                        </div>
                                        <div class="eb-swatch-label">
                                            <span><?php echo $ratio['name']; ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="eb-video-ratio-actions">
                            <button type="button" class="btn btn-sm btn-primary" data-eb-video-ratio-customize-button>
                                <span><?php echo JText::_('COM_EASYBLOG_COMPOSER_CUSTOMIZE_BUTTON'); ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-default" data-eb-video-ratio-cancel-button>
                                <span><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_CANCEL_BUTTON'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eb-composer-fieldgroup eb-video-ratio-custom-field">
                <div class="eb-composer-fieldgroup-content">
                    <div class="eb-composer-field">
                        <div class="eb-composer-fieldrow-label">
                            <?php echo JText::_('COM_EASYBLOG_USE_CUSTOM_ASPECT_RATIO'); ?>
                        </div>
                        <input type="text" class="form-control eb-video-ratio-input" placeholder="16:9 or 1.77" data-eb-video-ratio-input/>
                        <div class="eb-video-ratio-actions">
                            <button type="button" class="btn btn-sm btn-primary" data-eb-video-ratio-use-custom-button>
                                <span><?php echo JText::_('COM_EASYBLOG_USE_ASPECT_RATIO_BUTTON'); ?></span>
                            </button>
                            <button type="button" class="btn btn-sm btn-default" data-eb-video-ratio-cancel-custom-button>
                                <span><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_RATIO_CUSTOM_CANCEL_BUTTON'); ?></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="eb-composer-fieldgroup eb-video-alignment-field">
                <div class="eb-composer-fieldgroup-content">
                    <div class="row-table eb-composer-fieldrow">
                        <div class="col-cell eb-composer-fieldrow-label">
                            <span>Alignment</span>
                        </div>
                        <div class="col-cell eb-composer-fieldrow-content">
                            <select class="form-control eb-composer-fieldrow-select" data-eb-video-alignment-selection>
                                <option value="left">Left</option>
                                <option value="center" selected="">Center</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

<div class="eb-composer-fieldset eb-video-controls-fieldset">
    <div class="eb-composer-fieldset-header">
        <strong><?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_CONTROLS'); ?></strong>
    </div>
    <div class="eb-composer-fieldset-content">
        <div class="eb-composer-field row">
            <label for="frontpage" class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_AUTOPLAY'); ?>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <?php echo $this->html('grid.boolean', 'autoplay', false, 'autoplay', 'data-video-fieldset-autoplay'); ?>
            </div>
        </div>
        <div class="eb-composer-field row">
            <label for="frontpage" class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_LOOP'); ?>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <?php echo $this->html('grid.boolean', 'loop', false, 'loop', 'data-video-fieldset-loop'); ?>
            </div>
        </div>
        <div class="eb-composer-field row">
            <label for="frontpage" class="eb-composer-field-label col-sm-5">
                <?php echo JText::_('COM_EASYBLOG_BLOCKS_VIDEO_MUTED'); ?>
            </label>
            <div class="eb-composer-field-content col-sm-7">
                <?php echo $this->html('grid.boolean', 'muted', false, 'muted', 'data-video-fieldset-muted'); ?>
            </div>
        </div>
    </div>
</div>