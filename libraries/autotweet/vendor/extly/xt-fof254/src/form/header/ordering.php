<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * Ordering field header
 *
 * @since    2.0
 */
class XTF0FFormHeaderOrdering extends XTF0FFormHeader
{
    /**
     * Get the header
     *
     * @return string The header HTML
     */
    protected function getHeader()
    {
        $sortable = ('false' != $this->element['sortable']);

        $xtf0FView = $this->form->getView();
        $xtf0FModel = $this->form->getModel();

        $hasAjaxOrderingSupport = $xtf0FView->hasAjaxOrderingSupport();

        if (!$sortable) {
            // Non sortable?! I'm not sure why you'd want that, but if you insist...
            return JText::_('JGRID_HEADING_ORDERING');
        }

        if (!$hasAjaxOrderingSupport) {
            // Ye olde Joomla! 2.5 method
            $html = JHtml::_('grid.sort', 'JFIELD_ORDERING_LABEL', 'ordering', $xtf0FView->getLists()->order_Dir, $xtf0FView->getLists()->order, 'browse');
            $html .= JHtml::_('grid.order', $xtf0FModel->getList());

            return $html;
        } else {
            // The new, drag'n'drop ordering support WITH a save order button
            $html = JHtml::_(
                'grid.sort',
                '<i class="icon-menu-2"></i>',
                'ordering',
                $xtf0FView->getLists()->order_Dir,
                $xtf0FView->getLists()->order,
                null,
                'asc',
                'JGRID_HEADING_ORDERING'
            );

            $ordering = 'ordering' == $xtf0FView->getLists()->order;

            if ($ordering) {
                $html .= '<a href="javascript:saveorder('.(count($xtf0FModel->getList()) - 1).', \'saveorder\')" '.
                    'rel="tooltip" class="save-order btn btn-micro xt-float-right" title="'.JText::_('JLIB_HTML_SAVE_ORDER').'">'
                    .'<span class="icon-ok"></span></a>';
            }

            return $html;
        }
    }
}
