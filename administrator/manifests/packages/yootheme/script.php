<?php

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\Database\DatabaseInterface;

class pkg_yoothemeInstallerScript
{
    /**
     * @var InstallerAdapter
     */
    protected $adapter;

    /**
     * @var DatabaseInterface
     */
    protected $database;

    public function preflight()
    {
        if (!$this->requirePHP('7.4')) {
            return false;
        }
    }

    public function postflight($type, $adapter)
    {
        if (!in_array($type, ['install', 'update'])) {
            return true;
        }

        $this->adapter = $adapter;
        $this->database = version_compare(JVERSION, '4.0', '<')
            ? Factory::getDbo()
            : Factory::getContainer()->get(DatabaseInterface::class);

        $this->patchUpdateSite();
        $this->removeOldUpdateSites();

        return true;
    }

    protected function patchUpdateSite()
    {
        $site = $this->getUpdateSite($this->getExtensionId());
        $server = $this->adapter->manifest->updateservers->children()[0];

        if (!$site) {
            return;
        }

        // set name and location
        $site->name = strval($server['name']);
        $site->location = trim(strval($server));

        // set installer api key
        if (!$site->extra_query && ($key = $this->getInstallerApikey())) {
            $site->extra_query = "key={$key}";
        }

        $this->database->updateObject('#__update_sites', $site, 'update_site_id');
    }

    protected function getExtensionId()
    {
        return \Closure::bind(fn() => $this->currentExtensionId, $this->adapter, $this->adapter)();
    }

    protected function getInstallerApikey()
    {
        $query = $this->database
            ->getQuery(true)
            ->select('params')
            ->from('#__extensions')
            ->where("type = {$this->database->quote('plugin')}")
            ->where("folder = {$this->database->quote('installer')}")
            ->where("element = {$this->database->quote('yootheme')}");

        if ($extension = $this->database->setQuery($query)->loadObject()) {
            $params = json_decode($extension->params);
        }

        return $params->apikey ?? null;
    }

    protected function getUpdateSite($extensionId)
    {
        $query = $this->database
            ->getQuery(true)
            ->select('s.*')
            ->from('#__update_sites AS s')
            ->innerJoin('#__update_sites_extensions AS se ON se.update_site_id = s.update_site_id')
            ->where("se.extension_id = {$extensionId}");

        return $extensionId ? $this->database->setQuery($query)->loadObject() : null;
    }

    protected function removeUpdateSites(array $siteIds)
    {
        foreach (['#__update_sites', '#__update_sites_extensions'] as $table) {
            $query = $this->database
                ->getQuery(true)
                ->delete($table)
                ->where('update_site_id IN (' . implode(',', $siteIds) . ')');

            $this->database->setQuery($query)->execute();
        }
    }

    protected function removeOldUpdateSites()
    {
        $query = $this->database
            ->getQuery(true)
            ->select('update_site_id')
            ->from('#__update_sites')
            ->where("location LIKE '%/yootheme.com/api/update/yootheme_j33.xml'");

        if ($ids = $this->database->setQuery($query)->loadColumn()) {
            $this->removeUpdateSites($ids);
        }
    }

    protected function requirePHP($version)
    {
        if (version_compare(PHP_VERSION, $version, '>=')) {
            return true;
        }

        Factory::getApplication()->enqueueMessage(
            "<p>You need PHP {$version} or later to install the template.</p>",
            'warning',
        );

        return false;
    }
}
