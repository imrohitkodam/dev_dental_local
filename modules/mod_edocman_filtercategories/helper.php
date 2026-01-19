<?php
/*------------------------------------------------------------------------
# helper.php - mod_edocman_filtercategories
# ------------------------------------------------------------------------
# author    
# copyright Copyright (C) 2017 joomdonation.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.joomdonation.com
# Technical Support:  Forum - http://www.joomdonation.com/forum.html
*/

defined('_JEXEC') or die('Restricted access');

class modEdocmanFilterCategoriesHelper
{

	public function getChildCategoriesAjax(){
        $session = JFactory::getSession();
        $app = JFactory::getApplication();
        $db  = JFactory::getDbo();
        $config = EDocmanHelper::getConfig();
        require_once JPATH_ROOT . '/components/com_edocman/helper/bootstrap.php';
        $bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);


        $a = $session->get('choseCat');
        $catID = $app->input->get('catid','');
        $level = $app->input->get('level',0);
        $session->set('currentLevel',$level);
        $session->set('currentCategory',$catID);
        $a[$level] = $catID;
        $maxlevel = $app->input->get('maxlevel',0);
        $sql = "";
        if($maxlevel){
            $sql = " AND level <=".$maxlevel;
        }
        $accessLevel = implode(",",JFactory::getUser()->getAuthorisedViewLevels());
        $query = $db->getQuery(true);
        if($catID){
            $query->select("title AS text, id AS value")->from("#__edocman_categories")->where("parent_id=".$catID." AND published = 1 AND access IN (".$accessLevel.") $sql");
            $db->setQuery($query);
            $listCategories = $db->loadObjectList();
            if(count($listCategories) > 0){
                $level +=1;
                $a[$level]="";
                $CatArr[] = JHTML::_('select.option','','Any');
                $CatArr = array_merge($CatArr,$listCategories);
                echo "<div class='".$bootstrapHelper->getClassMapping('span12')."' id='level".$level."'>";
                echo JHtml::_('select.genericlist',$CatArr, 'categoriesId'.$level,'class="input-medium CategoriesId" data-level="'.$level.'" onchange=getListCategories(this.value,this.getAttribute("data-level"))','value','text');
                echo "</div>";
                $session->set('currentLevel',$level);
            }
            else{
				echo "";
			}
        }
        else{
            echo "";
        }

        $session->set('choseCat',$a);
        $app->close();
	}

    public static function setDropdownAjax(){
        $app      = JFactory::getApplication();
        $session  = JFactory::getSession();
        $config = EDocmanHelper::getConfig();
        $db       = JFactory::getDbo();
        require_once JPATH_ROOT . '/components/com_edocman/helper/bootstrap.php';
        $bootstrapHelper = new EDocmanHelperBootstrap($config->twitter_bootstrap_version);
        $query    = $db->getQuery(true);
        $accessLevel = implode(",",JFactory::getUser()->getAuthorisedViewLevels());
        $choseCats = $session->get('choseCat');
        $currentLevel = $session->get('currentLevel');

        if($currentLevel){
            for($i=0 ; $i<=$currentLevel; $i++){
                if($i==0){
                    $parrentID = 0;
                }else{
                    $parrentID = $choseCats[$i-1];
                }
                $query->clear();
                $query->select("title AS text, id AS value")->from("#__edocman_categories")->where("parent_id=".$parrentID." AND published = 1 AND access IN (".$accessLevel.")");
                $db->setQuery($query);
                $listCategories = $db->loadObjectList();

                if(count($listCategories) > 0){
                    $CatArr = array();
                    $CatArr[] = JHTML::_('select.option','','Any');
                    $CatArr = array_merge($CatArr,$listCategories);
                    echo "<div class='".$bootstrapHelper->getClassMapping('span12')."' id='level".$i."'>";
                    echo JHtml::_('select.genericlist',$CatArr, 'categoriesId'.$i,'class="input-medium CategoriesId" data-level="'.$i.'" onchange=getListCategories(this.value,this.getAttribute("data-level"))','value','text',$choseCats[$i]);
                    echo "</div>";
                }
            }
        }else{
            //echo "";
			$i = 0;
			$parrentID = 0;
			$query->clear();
			$query->select("title AS text, id AS value")->from("#__edocman_categories")->where("parent_id=".$parrentID." AND published = 1 AND access IN (".$accessLevel.")");
			$db->setQuery($query);
			$listCategories = $db->loadObjectList();

			if(count($listCategories) > 0){
				$CatArr = array();
				$CatArr[] = JHTML::_('select.option','','Any');
				$CatArr = array_merge($CatArr,$listCategories);
				echo "<div class='".$bootstrapHelper->getClassMapping('span12')."' id='level".$i."'>";
				echo JHtml::_('select.genericlist',$CatArr, 'categoriesId'.$i,'class="input-medium CategoriesId" data-level="'.$i.'" onchange=getListCategories(this.value,this.getAttribute("data-level"))','value','text',$choseCats[$i]);
				echo "</div>";
			}
        }

        $app->close();
    }

}
?>
