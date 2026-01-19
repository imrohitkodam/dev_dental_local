<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\Database\DatabaseDriver;

class ApiKey
{
    public const ELEMENT = 'pkg_yootheme';

    protected DatabaseDriver $db;

    /**
     * Constructor.
     */
    public function __construct(DatabaseDriver $db)
    {
        $this->db = $db;
    }

    public function get(): string
    {
        $updateSite = $this->getUpdateSite(static::ELEMENT);

        parse_str($updateSite->extra_query ?? '', $params);

        return $params['key'] ?? '';
    }

    public function set($key): void
    {
        $key = "key={$key}";
        $updateSite = $this->getUpdateSite(static::ELEMENT);

        if ($updateSite && $updateSite->extra_query !== $key) {
            $query = $this->db
                ->getQuery(true)
                ->update('#__update_sites')
                ->set("extra_query = {$this->db->quote($key)}")
                ->where("update_site_id = {$updateSite->update_site_id}");

            $this->db->setQuery($query)->execute();
        }
    }

    protected function getUpdateSite(
        $element,
        $type = 'package',
        $folder = '',
        $clientId = 0
    ): ?object {
        $query = $this->db
            ->getQuery(true)
            ->select(['us.update_site_id', 'us.extra_query'])
            ->from('#__extensions AS e')
            ->innerJoin('#__update_sites_extensions AS se ON e.extension_id = se.extension_id')
            ->innerJoin('#__update_sites AS us ON se.update_site_id = us.update_site_id')
            ->where([
                "e.type = {$this->db->quote($type)}",
                "e.folder = {$this->db->quote($folder)}",
                "e.element = {$this->db->quote($element)}",
                "e.client_id = {$clientId}",
            ]);

        return $this->db->setQuery($query)->loadObject();
    }
}
