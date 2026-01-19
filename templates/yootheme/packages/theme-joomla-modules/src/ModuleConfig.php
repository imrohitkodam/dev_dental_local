<?php

namespace YOOtheme\Theme\Joomla;

use Joomla\CMS\Language\Language;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\User\User;
use Joomla\Database\DatabaseDriver;
use YOOtheme\Config;
use YOOtheme\ConfigObject;

/**
 * @property array $types
 * @property array $modules
 * @property array $positions
 * @property bool  $canCreate
 */
class ModuleConfig extends ConfigObject
{
    public User $user;
    public Config $config;
    public Language $language;
    public DatabaseDriver $db;

    /**
     * Constructor.
     */
    public function __construct(User $user, Config $config, Language $language, DatabaseDriver $db)
    {
        $this->db = $db;
        $this->user = $user;
        $this->config = $config;
        $this->language = $language;

        $component = PluginHelper::isEnabled('system', 'advancedmodules')
            ? 'com_advancedmodules'
            : 'com_modules';

        parent::__construct([
            'types' => $this->getTypes(),
            'modules' => $this->getModules(),
            'positions' => $this->getPositions(),
            'canCreate' => $this->user->authorise('core.create', 'com_modules'),
            'url' => "administrator/index.php?option={$component}",
        ]);
    }

    protected function getTypes()
    {
        $query =
            'SELECT name, element FROM #__extensions WHERE client_id = 0 AND type = ' .
            $this->db->quote('module');

        $types = array_map(function (object $type): string {
            $this->language->load("{$type->element}.sys", JPATH_SITE, null, false, true);
            return Text::_($type->name);
        }, $this->db->setQuery($query)->loadObjectList('element'));

        natsort($types);

        return $types;
    }

    protected function getModules()
    {
        $query =
            'SELECT id, title, module, position, ordering FROM #__modules WHERE client_id = 0 AND published != -2 ORDER BY position, ordering';

        return array_map(
            fn(object $module): array => [
                'id' => (string) $module->id, // In Joomla 4 `id` is int
                'type' => $module->module,
                'title' => $module->title,
                'builder' => $module->module === 'mod_yootheme_builder',
                'position' => $module->position,
                'canEdit' => $this->user->authorise(
                    'core.edit',
                    "com_modules.module.{$module->id}",
                ),
                'canDelete' => $this->user->authorise(
                    'core.edit.state',
                    "com_modules.module.{$module->id}",
                ),
            ],
            $this->db->setQuery($query)->loadObjectList(),
        );
    }

    protected function getPositions()
    {
        $query = 'SELECT DISTINCT(position) FROM #__modules WHERE client_id = 0 ORDER BY position';

        return array_values(
            array_unique(
                array_merge(
                    array_keys($this->config->get('theme.positions', [])),
                    $this->db->setQuery($query)->loadColumn(),
                ),
            ),
        );
    }
}
