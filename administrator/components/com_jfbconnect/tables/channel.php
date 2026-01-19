<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2019 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v8.3.1
 * @build-date      2019/11/19
 */

if (!(defined('_JEXEC') || defined('ABSPATH'))) {     die('Restricted access'); };

class TableChannel extends JTable
{
    public $id = null;
    public $provider = null;
    public $type = null;
    public $title = null;
    public $description = null;
    public $attribs = null;
    public $published = 0;
    public $created = null;
    public $modified = null;

    function __construct(&$db)
    {
        parent::__construct('#__jfbconnect_channel', 'id', $db);
    }

    function bind($src, $ignore = array())
    {
        if (isset($src['attribs']) && is_array($src['attribs']))
        {
            $attribs = new JRegistry();
            $attribs->loadArray($src['attribs']);
            $src['attribs'] = (string)$attribs;
        }
        return parent::bind($src, $ignore);
    }

    public function load($keys = null, $reset = true)
    {
        $return = parent::load($keys, $reset);
        $this->attribs = json_decode($this->attribs);
        return $return;
    }
}