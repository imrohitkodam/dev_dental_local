<?php
/**
 * @version         1.9.7
 * @package        Joomla
 * @subpackage     EDocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

require_once JPATH_ROOT.'/components/com_edocman/helper/helper.php';

class EDocmanHelperRoute
{
    protected static $config;
	protected static $lookup;
    protected static $documents;
	/**
	 * 
	 * Function to get Document Route
	 * @param int $id
	 * @param int $catId
	 * @return string
	 */
	public static function getDocumentRoute($id, $catId = 0, $itemId = 0, $extra=null)
	{
		$needles = array (
			'document'  => array((int) $id)
		);
		$link = 'index.php?option=com_edocman&view=document&id=' . $id.$extra;
		if (!$catId)
        {
            //Find the main category of the document
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('category_id')
                ->from('#__edocman_document_category AS a')
                ->where('document_id='.(int)$id)
                ->where('is_main_category=1');
            $db->setQuery($query);
            $catId = (int)$db->loadResult();
        }
		if ($catId)
		{
			$needles['category'] = self::getCategoriesPath($catId, 'id', false);
			$needles['categories'] = $needles['category'];
			$link .= '&catid=' . $catId;
		}
		if ($item = self::findItem($needles, $itemId))
        {
            $link .= '&Itemid='.$item;
        }
		return $link;
	}


    public static function getDocumentMenuId($id, &$catId = 0, $itemId = 0)
    {
        $needles = array (
            'document'  => array((int) $id)
        );
        if (!$catId)
        {
            //Find the first category which has lowest ordering of this event
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('category_id')
                ->from('#__edocman_document_category AS a')
                ->where('document_id='.(int)$id)
                ->where('is_main_category=1');
            $db->setQuery($query);
            $catId = (int)$db->loadResult();
        }
        if ($catId)
        {
            $needles['category'] = self::getCategoriesPath($catId, 'id', false);
            $needles['categories'] = $needles['category'];
        }
        if ($item = self::findItem($needles, $itemId))
        {
            return $item;
        }
        else
        {
            $itemId;
        }
    }
	
	/**
	 * 
	 * Function to get Category Route
	 * @param int $id
	 * @return string
	 */
	public static function getCategoryRoute($id, $itemId = 0)
	{	
		if(!$id)
		{
			$link = '';
		}
		else
		{
			//Create the link
			$link = 'index.php?option=com_edocman&view=category&id='.$id;
			$catIds = self::getCategoriesPath($id, 'id', false);
			$needles = array (
					'category' => $catIds,
					'categories' => $catIds
			);
			if ($item = self::findItem($needles, $itemId))
				$link .= '&Itemid='.$item;
		}

		return $link;
	}

	/**
	 * 
	 * Function to get View Route
	 * @param string $view (cart, checkout)
	 * @return string
	 */
	public static function getViewRoute($view, $itemId)
	{
		//Create the link
		$link = 'index.php?option=com_edocman&view='.$view;
		if ($item = self::findView($view, $itemId))
        {
            $link .= '&Itemid='.$item;
        }

		return $link;
	}

    /**
     * Get event title, used for building the router
     *
     * @param $id
     * @return mixed
     */
    public static function getDocumentTitle($id)
    {
        if (self::$config == null)
        {
            self::$config = EDocmanHelper::getConfig();
        }
        if (!isset(self::$documents[$id]))
        {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query->select('alias')
                ->from('#__edocman_documents')
                ->where('id=' . $id);
            $db->setQuery($query);
            if (self::$config->insert_document_id)
            {
                self::$documents[$id] = $id.'-'.$db->loadResult();
            }
            else
            {
                self::$documents[$id] = $db->loadResult();
            }
        }

        return self::$documents[$id];
    }

    public static function getCategoriesPath($id, $type = 'id', $reverse = true, $parentId = 0)
    {
        static $categories;
        if (self::$config == null)
        {
            self::$config = EDocmanHelper::getConfig();
        }
        $db = JFactory::getDbo();
        if (empty($categories))
        {
            $query = $db->getQuery(true);
            $query->select('id, alias, parent_id')->from('#__edocman_categories');
            $db->setQuery($query);
            $categories = $db->loadObjectList('id');
        }
        $paths = array();
        if ($type == 'id' || self::$config->insert_category == 0)
        {
            do
            {
                if (!isset($categories[$id]))
                {
                    break;
                }
                if ($type == 'alias' &&  self::$config->insert_category_id)
                {
                    $paths[] = $categories[$id]->id.'-'.$categories[$id]->alias;
                }
                else
                {
                    $paths[] = $categories[$id]->{$type};
                }
                $id = $categories[$id]->parent_id;
            }
            while ($id != $parentId);
            if ($reverse)
            {
                $paths = array_reverse($paths);
            }
        }
        else
        {
            $paths[] = $categories[$id]->{$type};
        }
        return $paths;
    }

    /**
     * Find item id variable corresponding to the view
     *
     * @param $view
     * @return int
     */
    public static function findView($view, $itemId)
    {
        $needles = array (
            $view => array(0)
        );
        if ($item = self::findItem($needles, $itemId))
        {
            return $item;
        }
        else
        {
            return 0;
        }
    }

    /**
     * Find upload document menu id
     *
     * @param int $itemId
     *
     * @return int
     * @throws Exception
     */
    public static function findUploadMenuId($itemId = 0)
    {
        $app       = JFactory::getApplication();
        $menus     = $app->getMenu('site');
        $component = JComponentHelper::getComponent('com_edocman');
        $items     = $menus->getItems('component_id', $component->id);
        foreach ($items as $item)
        {
            if (isset($item->query) && isset($item->query['view']) && isset($item->query['layout']) && $item->query['view'] == 'document' && $item->query['layout'] == 'edit')
            {
                return $item->id;
            }
        }

        return $itemId;
    }
	/**
	 * 
	 * Function to find Itemid
	 * @param string $needles
	 * @return int
	 */
	public  static function findItem($needles = null, $itemId = 0)
	{
		$app		= JFactory::getApplication();
		$menus		= $app->getMenu('site');
		
		// Prepare the reverse lookup array.
		if (self::$lookup === null)
		{
			self::$lookup = array();
			$component	= JComponentHelper::getComponent('com_edocman');
			$items		= $menus->getItems('component_id', $component->id);
			foreach ($items as $item)
			{
				if (isset($item->query) && isset($item->query['view']))
				{
					$view = $item->query['view'];
					if (!isset(self::$lookup[$view]))
					{
						self::$lookup[$view] = array();
					}
					if (isset($item->query['id']))
					{
						self::$lookup[$view][$item->query['id']] = $item->id;
					}
					else 
					{
						self::$lookup[$view][0] = $item->id;
					}
				}
			}
		}

		if ($needles)
		{
			foreach ($needles as $view => $ids)
			{
				if (isset(self::$lookup[$view]))
				{
					foreach($ids as $id)
					{
						if (isset(self::$lookup[$view][(int)$id]))
						{
							return self::$lookup[$view][(int)$id];
						}
					}
				}
			}
		}

        //Return default item id
        return $itemId;
	}
}