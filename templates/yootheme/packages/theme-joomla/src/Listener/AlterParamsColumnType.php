<?php

namespace YOOtheme\Theme\Joomla\Listener;

use Joomla\Database\DatabaseDriver;

class AlterParamsColumnType
{
    protected DatabaseDriver $db;

    /**
     * Constructor.
     */
    public function __construct(DatabaseDriver $db)
    {
        $this->db = $db;
    }

    /**
     * Alter params type to MEDIUMTEXT only in MySQL database
     */
    public function handle(array $values): array
    {
        if (!str_contains($this->db->getName(), 'mysql')) {
            return $values;
        }

        if (
            $this->db
                ->setQuery(
                    "SHOW FIELDS FROM #__template_styles WHERE Field = 'params' AND Type = 'text'",
                )
                ->loadRow()
        ) {
            $this->db
                ->setQuery(
                    'ALTER TABLE #__template_styles CHANGE `params` `params` MEDIUMTEXT NOT NULL',
                )
                ->execute();
        }

        return $values;
    }
}
