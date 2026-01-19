<?php

namespace YOOtheme\Joomla;

use Joomla\CMS\Factory;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Storage as AbstractStorage;
use function YOOtheme\app;

class Storage extends AbstractStorage
{
    /**
     * Constructor.
     *
     * @param string   $element
     * @param string   $folder
     *
     * @throws \Exception
     */
    public function __construct($element = 'yootheme', $folder = 'system')
    {
        /** @var DatabaseDriver $db */
        $db = app(DatabaseDriver::class);
        $query = sprintf(
            'SELECT custom_data FROM #__extensions WHERE element = %s AND folder = %s LIMIT 1',
            $db->quote($element),
            $db->quote($folder),
        );

        if ($result = $db->setQuery($query)->loadResult()) {
            $this->addJson($result);
        }

        $joomla = Factory::getApplication();
        $joomla->registerEvent('onAfterRespond', function () use ($db, $element, $folder) {
            if ($this->isModified()) {
                $data = json_encode($this, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                if ($data === false) {
                    return;
                }

                $this->alterCustomDataColumn($db);

                $extension = (object) [
                    'element' => $element,
                    'folder' => $folder,
                    'custom_data' => $data,
                ];
                $db->updateObject('#__extensions', $extension, ['element', 'folder']);
            }
        });
    }

    /**
     * Alter custom_data type to MEDIUMTEXT only in MySQL database
     */
    protected function alterCustomDataColumn($db)
    {
        if (!str_contains($db->getName(), 'mysql')) {
            return;
        }

        if (
            $db
                ->setQuery(
                    "SHOW FIELDS FROM #__extensions WHERE Field = 'custom_data' AND Type = 'text'",
                )
                ->loadRow()
        ) {
            $db->setQuery(
                'ALTER TABLE #__extensions CHANGE `custom_data` `custom_data` MEDIUMTEXT NOT NULL',
            )->execute();
        }
    }
}
