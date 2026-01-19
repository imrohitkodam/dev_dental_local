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

namespace RegularLabs\Component\Conditions\Administrator\Condition\Other;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;
use RegularLabs\Component\Conditions\Administrator\Condition\Condition;
use RegularLabs\Component\Conditions\Administrator\Condition\HasArraySelection;

class Template extends Condition
{
    use HasArraySelection;

    public function getTemplate(): object
    {
        $template = JFactory::getApplication()->getTemplate(true);

        if (isset($template->id))
        {
            return $template;
        }

        $params = json_encode($template->params);

        // Find template style id based on params, as the template style id is not always stored in the getTemplate
        $query = $this->db->getQuery(true)
            ->select('id')
            ->from('#__template_styles AS s')
            ->where('s.client_id = 0')
            ->where('s.template = ' . $this->db->quote($template->template))
            ->where('s.params = ' . $this->db->quote($params))
            ->setLimit(1);
        $this->db->setQuery($query);
        $template->id = $this->db->loadResult('id');

        if ($template->id)
        {
            return $template;
        }

        // No template style id is found, so just grab the first result based on the template name
        $query->clear('where')
            ->where('s.client_id = 0')
            ->where('s.template = ' . $this->db->quote($template->template))
            ->setLimit(1);
        $this->db->setQuery($query);
        $template->id = $this->db->loadResult('id');

        return $template;
    }

    public function pass(): bool
    {
        $template = $this->getTemplate();

        // Put template name and name + style id into array
        // The '::' separator was used in pre Joomla 3.3
        $template = [
            $template->template, $template->template . '--' . $template->id,
            $template->template . '::' . $template->id,
        ];

        return $this->passSimple($template, null, true);
    }
}
