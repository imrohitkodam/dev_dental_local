<?php
/**
 * @version        1.9.5
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright	   Copyright (C) 2010 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
abstract class EDocmanHelperHtml
{
    /**
     * Function to render a common layout which is used in different views
     * @param string $layout	Relative path to the layout file
     * @param array $data	An array contains the data passed to layout for rendering
     */
    public static function loadCommonLayout($layout, $data = array())
    {
        $app = JFactory::getApplication();
        $themeFile = str_replace('/tmpl', '', $layout);
        if (JFile::exists(JPATH_THEMES . '/' . $app->getTemplate() . '/html/com_edocman/' . $themeFile))
        {
            $path = JPATH_THEMES . '/' . $app->getTemplate() . '/html/com_edocman/' . $themeFile;
        }
        elseif (JFile::exists(JPATH_ROOT . '/components/com_edocman/view/' . $layout))
        {
            $path = JPATH_ROOT . '/components/com_edocman/view/' . $layout;
        }
        else
        {
            throw new RuntimeException(JText::_('The given shared template path is not exist'));
        }
        // Start an output buffer.
        ob_start();
        extract($data);
        // Load the layout.
        include $path;
        // Get the layout contents.
        $output = ob_get_clean();

        return $output;
    }

	/**
	 * Function to add dropdown menu
	 *
	 * @param string $vName
	 */
	public static function renderSubmenu($vName = 'dashboard')
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__edocman_menus')
			->where('published = 1')
			->where('menu_parent_id = 0')
			->order('ordering');
		$db->setQuery($query);
		$menus = $db->loadObjectList();
		$html  = '';
		$html  .= '<div class="clearfix"></div><ul id="mp-dropdown-menu" class="nav nav-tabs nav-hover">';

		$currentLink = 'index.php' . JUri::getInstance()->toString(array('query'));
		for ($i = 0; $n = count($menus), $i < $n; $i++)
		{
			$menu = $menus[$i];
			$query->clear();
			$query->select('*')
				->from('#__edocman_menus')
				->where('published = 1')
				->where('menu_parent_id = ' . intval($menu->id))
				->order('ordering');
			$db->setQuery($query);
			$subMenus = $db->loadObjectList();

			switch ($i)
			{
				case 2:
				case  3:
					$view = 'subscriptions';
					break;
				case 4:
					$view = 'coupons';
					break;
				case 5:
					$view = 'plugins';
					break;
				case 1:
				case 6:
				case 7:
				case 8:
					$view = 'configuration';
					break;
				default:
					$view = '';
					break;
			}

			//if ($view && !OSMembershipHelper::canAccessThisView($view))
			//{
				//continue;
			//}

			if (!count($subMenus))
			{
				$class = '';
				if ($menu->menu_link == $currentLink)
				{
					$class = ' class="active"';
				}
				$html .= '<li' . $class . '><a href="' . $menu->menu_link . '"><span class="icon-' . $menu->menu_class . '"></span> ' . JText::_($menu->menu_name) .
					'</a></li>';
			}
			else
			{
				$class = ' class="dropdown"';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					if ($subMenu->menu_link == $currentLink)
					{
						$class = ' class="dropdown active"';
						break;
					}
				}
				$html .= '<li' . $class . '>';
				$html .= '<a id="drop_' . $menu->id . '" href="#" data-toggle="dropdown" role="button" class="dropdown-toggle"><span class="icon-' . $menu->menu_class . '"></span> ' .
					JText::_($menu->menu_name) . ' <b class="caret"></b></a>';
				$html .= '<ul aria-labelledby="drop_' . $menu->id . '" role="menu" class="dropdown-menu" id="menu_' . $menu->id . '">';
				for ($j = 0; $m = count($subMenus), $j < $m; $j++)
				{
					$subMenu = $subMenus[$j];
					$class   = '';

					$vars = array();
					parse_str($subMenu->menu_link, $vars);
					$view = isset($vars['view']) ? $vars['view'] : '';

					//if ($view && !OSMembershipHelper::canAccessThisView($view))
					///{
						//continue;
					//}

					if ($subMenu->menu_link == $currentLink)
					{
						$class = ' class="active"';
					}
					$html .= '<li' . $class . '><a href="' . $subMenu->menu_link .
						'" tabindex="-1"><span class="icon-' . $subMenu->menu_class . '"></span> ' . JText::_($subMenu->menu_name) . '</a></li>';
				}
				$html .= '</ul>';
				$html .= '</li>';
			}
		}
		$html .= '</ul>';

		echo $html;
	}

    /**
     * Get BootstrapHelper class for admin UI
     *
     * @return EventbookingHelperBootstrap
     */
    public static function getAdminBootstrapHelper()
    {
        if (version_compare(JVERSION, '4.0.0-dev', 'ge'))
        {
            return 4;
        }
        return 2;
    }
}