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
 * FrameworkOnFramework raw output class. It works like an HTML view, but the
 * output is bare HTML.
 *
 * @since    2.1
 */
class XTF0FViewRaw extends XTF0FView
{
    /**
     * @var (object{\create: \bool, \edit: \bool, \editown: \bool, \editstate: \bool, \delete: \bool} & \stdClass)
     */
    public $aclperms;

    public $items;

    public $pagination;

    public $params;

    public $item;

    /** @var array Data lists */
    protected $lists = null;

    /** @var array Permissions map */
    protected $perms = null;

    /**
     * Class constructor
     *
     * @param array $config Configuration parameters
     */
    public function __construct($config = [])
    {
        // Make sure $config is an array
        if (is_object($config)) {
            $config = (array) $config;
        } elseif (!is_array($config)) {
            $config = [];
        }

        parent::__construct($config);

        $this->config = $config;

        // Get the input
        if (array_key_exists('input', $config)) {
            if ($config['input'] instanceof \Joomla\CMS\Input\Input) {
                $this->input = $config['input'];
            } else {
                $this->input = new \Joomla\CMS\Input\Input($config['input']);
            }
        } else {
            $this->input = self::getJoomlaInput();
        }

        if (!array_key_exists('option', $this->config)) {
            $this->config['option'] = $this->input->getCmd('option', 'com_foobar');
        }

        if (!array_key_exists('view', $this->config)) {
            $this->config['view'] = $this->input->getCmd('view', 'cpanel');
        }

        $this->lists = new XTF0FUtilsObject();

        if (!XTF0FPlatform::getInstance()->isCli()) {
            $platform = XTF0FPlatform::getInstance();
            $perms = (object) [
                'create'	 => $platform->authorise('core.create', $this->input->getCmd('option', 'com_foobar')),
                'edit'		 => $platform->authorise('core.edit', $this->input->getCmd('option', 'com_foobar')),
                'editown'	 => $platform->authorise('core.edit.own', $this->input->getCmd('option', 'com_foobar')),
                'editstate'	 => $platform->authorise('core.edit.state', $this->input->getCmd('option', 'com_foobar')),
                'delete'	 => $platform->authorise('core.delete', $this->input->getCmd('option', 'com_foobar')),
            ];

            $this->aclperms = $perms;
            $this->perms = $perms;
        }
    }

    /**
     * Displays the view
     *
     * @param string $tpl The template to use
     *
     * @return bool|null False if we can't render anything
     */
    public function display($tpl = null)
    {
        // Get the task set in the model
        $model = $this->getModel();
        $task = $model->getState('task', 'browse');

        // Call the relevant method
        $method_name = 'on'.ucfirst($task);

        $result = method_exists($this, $method_name) ? $this->$method_name($tpl) : $this->onDisplay();

        if (false === $result) {
            return;
        }

        // Show the view
        if ($this->doPreRender) {
            $this->preRender();
        }

        parent::display($tpl);

        if ($this->doPostRender) {
            $this->postRender();
        }
    }

    /**
     * Determines if the current Joomla! version and your current table support
     * AJAX-powered drag and drop reordering. If they do, it will set up the
     * drag & drop reordering feature.
     *
     * @return bool|array false if not suported, a table with necessary
     *                    information (saveOrder: should you enabled DnD
     *                    reordering; orderingColumn: which column has the
     *                    ordering information)
     */
    public function hasAjaxOrderingSupport()
    {
        if (version_compare(JVERSION, '3.0', 'lt')) {
            return false;
        }

        $model = $this->getModel();

        if (!method_exists($model, 'getTable')) {
            return false;
        }

        $table = $this->getModel()->getTable();

        if (!method_exists($table, 'getColumnAlias') || !method_exists($table, 'getTableFields')) {
            return false;
        }

        $orderingColumn = $table->getColumnAlias('ordering');
        $fields = $table->getTableFields();

        if (!is_array($fields) || !array_key_exists($orderingColumn, $fields)) {
            return false;
        }

        $listOrder = $this->escape($model->getState('filter_order', null, 'cmd'));
        $listDirn = $this->escape($model->getState('filter_order_Dir', 'ASC', 'cmd'));
        $saveOrder = $listOrder == $orderingColumn;

        if ($saveOrder) {
            $saveOrderingUrl = 'index.php?option='.$this->config['option'].'&view='.$this->config['view'].'&task=saveorder&format=json';
            JHtml::_('sortablelist.sortable', 'itemsList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
        }

        return [
            'saveOrder'		 => $saveOrder,
            'orderingColumn' => $orderingColumn,
        ];
    }

    /**
     * Returns the internal list of useful variables to the benefit of
     * XTF0FFormHeader fields.
     *
     * @return array
     *
     * @since 2.0
     */
    public function getLists()
    {
        return $this->lists;
    }

    /**
     * Returns a reference to the permissions object of this view
     *
     * @return stdClass
     */
    public function getPerms()
    {
        return $this->perms;
    }

    /**
     * Last chance to output something before rendering the view template
     *
     * @return void
     */
    protected function preRender()
    {
    }

    /**
     * Last chance to output something after rendering the view template and
     * before returning to the caller
     *
     * @return void
     */
    protected function postRender()
    {
    }

    /**
     * Executes before rendering the page for the Browse task.
     *
     * @param string $tpl Subtemplate to use
     *
     * @return bool Return true to allow rendering of the page
     */
    protected function onBrowse($tpl = null)
    {
        // When in interactive browsing mode, save the state to the session
        $this->getModel()->savestate(1);

        return $this->onDisplay($tpl);
    }

    /**
     * Executes before rendering a generic page, default to actions necessary
     * for the Browse task.
     *
     * @param string $tpl Subtemplate to use
     *
     * @return bool Return true to allow rendering of the page
     */
    protected function onDisplay($tpl = null)
    {
        $view = $this->input->getCmd('view', 'cpanel');

        if (in_array($view, ['cpanel', 'cpanels'])) {
            return null;
        }

        // Load the model
        $model = $this->getModel();

        // ...ordering
        $this->lists->set('order', $model->getState('filter_order', 'id', 'cmd'));
        $this->lists->set('order_Dir', $model->getState('filter_order_Dir', 'DESC', 'cmd'));

        // Assign data to the view
        $this->items = $model->getItemList();
        $this->pagination = $model->getPagination();

        // Pass page params on frontend only
        if (XTF0FPlatform::getInstance()->isFrontend()) {
            $params = JFactory::getApplication()->getParams();
            $this->params = $params;
        }

        return true;
    }

    /**
     * Executes before rendering the page for the Add task.
     *
     * @param string $tpl Subtemplate to use
     *
     * @return bool Return true to allow rendering of the page
     */
    protected function onAdd($tpl = null)
    {
        \Joomla\CMS\Uri\Uri::getInstance()->setVar('hidemainmenu', true);
        $model = $this->getModel();
        $this->item = $model->getItem();

        return true;
    }

    /**
     * Executes before rendering the page for the Edit task.
     *
     * @param string $tpl Subtemplate to use
     *
     * @return bool Return true to allow rendering of the page
     */
    protected function onEdit($tpl = null)
    {
        // This perms are used only for hestetic reasons (ie showing toolbar buttons), "real" checks
        // are made by the controller
        // It seems that I can't edit records, maybe I can edit only this one due asset tracking?
        if (!$this->perms->edit || !$this->perms->editown) {
            $model = $this->getModel();

            if ($model) {
                $table = $model->getTable();

                // Ok, record is tracked, let's see if I can this record
                if ($table->isAssetsTracked()) {
                    $platform = XTF0FPlatform::getInstance();

                    if (!$this->perms->edit) {
                        $this->perms->edit = $platform->authorise('core.edit', $table->getAssetName());
                    }

                    if (!$this->perms->editown) {
                        $this->perms->editown = $platform->authorise('core.edit.own', $table->getAssetName());
                    }
                }
            }
        }

        return $this->onAdd($tpl);
    }

    /**
     * Executes before rendering the page for the Read task.
     *
     * @param string $tpl Subtemplate to use
     *
     * @return bool Return true to allow rendering of the page
     */
    protected function onRead($tpl = null)
    {
        // All I need is to read the record

        return $this->onAdd($tpl);
    }

    private static function getJoomlaInput()
    {
        if (version_compare(JVERSION, '4', '<')) {
            // Joomla 3 code
            jimport('joomla.filter.input');

            $input = JFactory::getApplication()->input;
            $data = $input->serialize();
            $jinput = new \Joomla\CMS\Input\Input([]);
            $jinput->unserialize($data);

            return $jinput;
        }

        $input = Joomla\CMS\Factory::getApplication()->input;
        $data = $input->getArray();
        $jinput = new \Joomla\CMS\Input\Input($data);

        return $jinput;
    }
}
