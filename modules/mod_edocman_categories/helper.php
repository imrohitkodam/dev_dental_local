<?php
/**
 * @version        1.9.10
 * @package        Joomla
 * @subpackage     Edocman
 * @author         Tuan Pham Ngoc
 * @copyright      Copyright (C) 2011 - 2018 Ossolution Team
 * @license        GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die;
class ModEdocmanCategoriesHelper{

    public function getCategories($cid,&$arrId=array())
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id')->from('#__edocman_categories')->where("parent_id = $cid");
        $db->setQuery($query);
        $ids   = $db->loadColumn();
        $arrId = array_merge($ids,$arrId);
        if(count($ids)){
           foreach($ids as $id){
               self::getCategories($id,$arrId);
           }
        }
    }
}
?>