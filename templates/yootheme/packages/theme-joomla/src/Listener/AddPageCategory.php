<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\Database\DatabaseDriver;
use YOOtheme\Config;

class AddPageCategory
{
    public Config $config;
    public DatabaseDriver $db;

    public function __construct(Config $config, DatabaseDriver $db)
    {
        $this->db = $db;
        $this->config = $config;
    }

    public function handle(): void
    {
        $catid = $this->config->get('~theme.page_category');

        if ($catid === '' || is_numeric($catid)) {
            return;
        }

        $result = $this->db
            ->setQuery(
                "SELECT id FROM #__categories WHERE alias = 'uncategorised' AND extension = 'com_content'",
            )
            ->loadResult();

        $this->config->set('~theme.page_category', strval($result));
    }
}
