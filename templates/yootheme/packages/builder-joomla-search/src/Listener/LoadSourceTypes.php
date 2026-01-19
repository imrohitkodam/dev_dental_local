<?php

namespace YOOtheme\Builder\Joomla\Search\Listener;

use YOOtheme\Builder\Joomla\Search\Type;

class LoadSourceTypes
{
    public static function handle($source): void
    {
        $query = [Type\SearchQueryType::config(), Type\SearchItemsQueryType::config()];

        $types = [
            ['Search', Type\SearchType::config()],
            ['SearchItem', Type\SearchItemType::config()],
        ];

        foreach ($query as $args) {
            $source->queryType($args);
        }

        foreach ($types as $args) {
            $source->objectType(...$args);
        }
    }
}
