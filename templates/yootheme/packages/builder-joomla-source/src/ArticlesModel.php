<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\Component\Content\Site\Model\ArticlesModel as BaseModel;
use Joomla\Database\DatabaseDriver;
use function YOOtheme\app;

class ArticlesModel extends BaseModel
{
    protected function getListQuery()
    {
        $fieldId = false;
        $ordering = $this->getState('list.ordering', '');

        if (str_starts_with($ordering, 'a.field:')) {
            $fieldId = (int) substr($ordering, 8);
            $this->setState('list.ordering', 'fields.value');
        }

        $categoryId = $this->getState('filter.category_id');
        $includeChildCategories = $this->getState('filter.include_child_categories');

        if ($categoryId && $includeChildCategories) {
            $this->setState('filter.category_id');
        }

        $query = parent::getListQuery();

        if ($categoryId && $includeChildCategories) {
            $categories = implode(',', array_map('intval', (array) $categoryId));

            $include = $this->getState('filter.category_id.include', true);
            $type = $include ? 'IN' : 'NOT IN';

            $where = [];
            if ($includeChildCategories === 'include') {
                $where[] = "a.catid {$type} ({$categories})";
            }

            $subQuery = "SELECT sub.id FROM #__categories AS sub JOIN #__categories AS this ON sub.lft > this.lft AND sub.rgt < this.rgt WHERE this.id IN ({$categories})";
            $where[] = "a.catid {$type} ({$subQuery})";

            $query->andWhere($where, $include ? 'OR' : 'AND');
        }

        $tags = array_filter((array) $this->getState('filter.tags', []));

        if ($tags) {
            $tagOperator = $this->getState('filter.tag_operator', 'IN');
            $tagCount = count($tags);
            $tags = implode(',', array_map('intval', $tags));
            $includeChildTags = $this->getState('filter.include_child_tags');

            if (in_array($tagOperator, ['IN', 'NOT IN'])) {
                $where = [];
                if (!$includeChildTags || $includeChildTags === 'include') {
                    $subQuery = "SELECT content_item_id FROM #__contentitem_tag_map WHERE tag_id IN ({$tags}) AND type_alias = 'com_content.article'";
                    $where[] = "a.id {$tagOperator} ({$subQuery})";
                }

                if ($includeChildTags) {
                    $subQuery = "SELECT map.content_item_id FROM #__tags AS sub JOIN #__tags AS this ON sub.lft > this.lft AND sub.rgt < this.rgt JOIN #__contentitem_tag_map as map ON sub.id = map.tag_id WHERE this.id IN ({$tags}) and map.type_alias = 'com_content.article'";
                    $where[] = "a.id {$tagOperator} ({$subQuery})";
                }

                $query->andWhere($where, $tagOperator === 'IN' ? 'OR' : 'AND');
            }

            if ($tagOperator === 'AND') {
                $greaterThan = $includeChildTags === 'include' ? '>=' : '>';
                $lesserThan = $includeChildTags === 'include' ? '<=' : '<';
                $tagQuery = $includeChildTags
                    ? "SELECT sub.id FROM #__tags AS sub JOIN #__tags AS this ON sub.lft {$greaterThan} this.lft AND sub.rgt {$lesserThan} this.rgt WHERE this.id IN ({$tags})"
                    : $tags;
                $tagCountQuery = $includeChildTags
                    ? "(SELECT COUNT(sub.id) FROM #__tags AS sub JOIN #__tags AS this ON sub.lft {$greaterThan} this.lft AND sub.rgt {$lesserThan} this.rgt WHERE this.id IN ({$tags}))"
                    : $tagCount;
                $subQuery = "SELECT COUNT(1) FROM #__contentitem_tag_map WHERE tag_id IN ({$tagQuery}) AND content_item_id = a.id AND type_alias = 'com_content.article'";
                $query->where("({$subQuery}) = {$tagCountQuery}");
            }
        }

        if ($fieldId) {
            $query->leftJoin(
                "#__fields_values AS fields ON a.id = fields.item_id AND fields.field_id = {$fieldId}",
            );
        }

        if (
            $this->getState('list.alphanum') &&
            $ordering != app(DatabaseDriver::class)->getQuery(true)->Rand()
        ) {
            $ordering = $this->getState('list.ordering', 'a.ordering');
            $order = $this->getState('list.direction', 'ASC');
            $query->clear('order');
            $query->order(
                "(substr({$ordering}, 1, 1) > '9') {$order}, {$ordering}+0 {$order}, {$ordering} {$order}",
            );
        }

        // Filter by language
        if ($lang = $this->getState('filter.lang')) {
            $db = app(DatabaseDriver::class);
            $query->where('a.language IN (' . $db->quote($lang) . ',' . $db->quote('*') . ')');
        }

        return $query;
    }
}
