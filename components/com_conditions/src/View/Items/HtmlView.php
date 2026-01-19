<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Site\View\Items;

use Joomla\CMS\Form\Form as JForm;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Pagination\Pagination;
use Joomla\Registry\Registry;
use RegularLabs\Library\Document as RL_Document;
use RegularLabs\Library\Language as RL_Language;
use RegularLabs\Library\Parameters as RL_Parameters;

defined('_JEXEC') or die;

/**
 * List View
 */
class HtmlView extends BaseHtmlView
{
    /**
     * @var    array
     */
    public $activeFilters;
    /**
     * @var    JForm
     */
    public $filterForm;
    /**
     * @var  boolean
     */
    protected $collect_urls_enabled;
    /**
     * @var  boolean
     */
    protected $enabled;
    /**
     * @var  array
     */
    protected $items;
    /**
     * @var    Pagination
     */
    protected $pagination;
    /**
     * @var  Registry
     */
    protected $params;
    /**
     * @var    object
     */
    protected $state;

    /**
     * @param string $tpl The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  False if unsuccessful, otherwise void.
     *
     * @throws  GenericDataException
     */
    public function display($tpl = null)
    {

        RL_Language::load('com_conditions', JPATH_ADMINISTRATOR);
        RL_Document::style('regularlabs.admin-form');
        RL_Document::style('media/templates/administrator/atum/css/template.css', [], false);
        JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_conditions/forms');

        $this->items         = $this->get('Items');
        $this->pagination    = $this->get('Pagination');
        $this->state         = $this->get('State');
        $this->config        = RL_Parameters::getComponent('conditions');
        $this->filterForm    = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->hasCategories = $this->get('HasCategories');

        $errors = $this->get('Errors');
        if (count($errors))
        {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        parent::display($tpl);
    }

}
