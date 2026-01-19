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
 * FrameworkOnFramework CSV View class. Automatically renders the data in CSV
 * format.
 *
 * @since    1.0
 */
class XTF0FViewCsv extends XTF0FViewHtml
{
    public $items;

    /**
     *  Should I produce a CSV header row.
     *
     * @var bool
     */
    protected $csvHeader = true;

    /**
     * The filename of the downloaded CSV file.
     *
     * @var string
     */
    protected $csvFilename = null;

    /**
     * The columns to include in the CSV output. If it's empty it will be ignored.
     *
     * @var array
     */
    protected $csvFields = [];

    /**
     * Public constructor. Instantiates a XTF0FViewCsv object.
     *
     * @param array $config The configuration data array
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

        if (array_key_exists('csv_header', $config)) {
            $this->csvHeader = $config['csv_header'];
        } else {
            $this->csvHeader = $this->input->getBool('csv_header', true);
        }

        if (array_key_exists('csv_filename', $config)) {
            $this->csvFilename = $config['csv_filename'];
        } else {
            $this->csvFilename = $this->input->getString('csv_filename', '');
        }

        if (empty($this->csvFilename)) {
            $view = $this->input->getCmd('view', 'cpanel');
            $view = XTF0FInflector::pluralize($view);
            $this->csvFilename = strtolower($view);
        }

        if (array_key_exists('csv_fields', $config)) {
            $this->csvFields = $config['csv_fields'];
        }
    }

    /**
     * Executes before rendering a generic page, default to actions necessary for the Browse task.
     *
     * @param string $tpl Subtemplate to use
     *
     * @return bool Return true to allow rendering of the page
     */
    protected function onDisplay($tpl = null)
    {
        // Load the model
        $model = $this->getModel();

        $items = $model->getItemList();
        $this->items = $items;

        $xtf0FPlatform = XTF0FPlatform::getInstance();
        $jDocument = $xtf0FPlatform->getDocument();

        if ($jDocument instanceof JDocument) {
            $jDocument->setMimeEncoding('text/csv');
        }

        $xtf0FPlatform->setHeader('Pragma', 'public');
        $xtf0FPlatform->setHeader('Expires', '0');
        $xtf0FPlatform->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
        $xtf0FPlatform->setHeader('Cache-Control', 'public', false);
        $xtf0FPlatform->setHeader('Content-Description', 'File Transfer');
        $xtf0FPlatform->setHeader('Content-Disposition', 'attachment; filename="'.$this->csvFilename.'"');

        if (null === $tpl) {
            $tpl = 'csv';
        }

        XTF0FPlatform::getInstance()->setErrorHandling(\E_ALL, 'ignore');

        $hasFailed = false;

        try {
            $result = $this->loadTemplate($tpl, true);

            if ($result instanceof Exception) {
                $hasFailed = true;
            }
        } catch (Exception $exception) {
            $hasFailed = true;
        }

        if (!$hasFailed) {
            echo $result;
        } else {
            // Default CSV behaviour in case the template isn't there!

            if (empty($items)) {
                return null;
            }

            $item = array_pop($items);
            $keys = get_object_vars($item);
            $keys = array_keys($keys);
            $items[] = $item;
            reset($items);

            if (!empty($this->csvFields)) {
                $temp = [];

                foreach ($this->csvFields as $csvField) {
                    if (in_array($csvField, $keys)) {
                        $temp[] = $csvField;
                    }
                }

                $keys = $temp;
            }

            if ($this->csvHeader) {
                $csv = [];

                foreach ($keys as $key) {
                    $key = str_replace('"', '""', $key);
                    $key = str_replace("\r", '\\r', $key);
                    $key = str_replace("\n", '\\n', $key);
                    $key = '"'.$key.'"';

                    $csv[] = $key;
                }

                echo implode(',', $csv)."\r\n";
            }

            foreach ($items as $item) {
                $csv = [];
                $item = (array) $item;

                foreach ($keys as $key) {
                    $v = $item[$key] ?? '';

                    if (is_array($v)) {
                        $v = 'Array';
                    } elseif (is_object($v)) {
                        $v = 'Object';
                    }

                    $v = str_replace('"', '""', $v);
                    $v = str_replace("\r", '\\r', $v);
                    $v = str_replace("\n", '\\n', $v);
                    $v = '"'.$v.'"';

                    $csv[] = $v;
                }

                echo implode(',', $csv)."\r\n";
            }
        }

        return false;
    }
}
