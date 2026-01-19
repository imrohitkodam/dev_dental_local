<?php

namespace YOOtheme\Builder\Joomla\Source;

use Joomla\Component\Tags\Site\Model\TagModel as BaseModel;
use Joomla\Database\DatabaseDriver;
use function YOOtheme\app;

class TagModel extends BaseModel
{
    protected function getListQuery()
    {
        $query = parent::getListQuery();
        $ordering = $this->getState('list.ordering', '');

        if ($ordering === 'c.rand') {
            $query->clear('order');
            $query->order(app(DatabaseDriver::class)->getQuery(true)->Rand());
        } elseif ($this->getState('list.alphanum')) {
            $ordering = $this->getState('list.ordering', 'c.core_ordering');
            $order = $this->getState('list.direction', 'ASC');
            $query->clear('order');
            $query->order(
                "(substr({$ordering}, 1, 1) > '9') {$order}, {$ordering}+0 {$order}, {$ordering} {$order}",
            );
        }

        return $query;
    }
}
