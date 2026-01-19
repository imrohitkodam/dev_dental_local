<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\Component\Contact\Administrator\Model\ContactsModel as BaseModel;

class ContactsModel extends BaseModel
{
    protected function getListQuery()
    {
        $categoryId = $this->getState('filter.category_id');
        $includeChildCategories = $this->getState('filter.include_child_categories');

        if ($categoryId && $includeChildCategories) {
            $this->setState('filter.category_id');
        }

        $tags = (array) $this->getState('filter.tags', []);
        $includeChildTags = $this->getState('filter.include_child_tags');

        if ($tags && !$includeChildTags && !version_compare(JVERSION, '4.0', '<')) {
            $this->setState('filter.tag', $tags);
        }

        $query = parent::getListQuery();

        if ($categoryId && $includeChildCategories) {
            $categories = implode(',', array_map('intval', (array) $categoryId));

            $where = [];
            if ($includeChildCategories === 'include') {
                $where[] = "a.catid IN ({$categories})";
            }

            $subQuery = "SELECT sub.id FROM #__categories AS sub JOIN #__categories AS this ON sub.lft > this.lft AND sub.rgt < this.rgt WHERE this.id IN ({$categories})";
            $where[] = "a.catid IN ({$subQuery})";

            $query->andWhere($where);
        }

        if ($tags && ($includeChildTags || version_compare(JVERSION, '4.0', '<'))) {
            $tags = implode(',', array_map('intval', $tags));
            $where = [];

            if (!$includeChildTags || $includeChildTags === 'include') {
                $subQuery = "SELECT content_item_id FROM #__contentitem_tag_map WHERE tag_id IN ({$tags}) AND type_alias = 'com_contact.contact'";
                $where[] = "a.id IN ({$subQuery})";
            }

            if ($includeChildTags) {
                $subQuery = "SELECT map.content_item_id FROM #__tags AS sub JOIN #__tags AS this ON sub.lft > this.lft AND sub.rgt < this.rgt JOIN #__contentitem_tag_map as map ON sub.id = map.tag_id WHERE this.id IN ({$tags}) and map.type_alias = 'com_contact.contact'";
                $where[] = "a.id IN ({$subQuery})";
            }

            $query->andWhere($where);
        }

        return $query;
    }
}
