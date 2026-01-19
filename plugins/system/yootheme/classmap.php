<?php

namespace Joomla;

$classes = [];

if (version_compare(JVERSION, '4.0', '<')) {
    $site = JPATH_SITE;
    $admin = JPATH_ADMINISTRATOR;
    $classes += [
        CMS\HTML\Helpers\Content::class => 'JHtmlContent',
        CMS\HTML\Helpers\Menu::class => 'JHtmlMenu',
        Component\Contact\Administrator\Model\ContactsModel::class => [
            'ContactModelContacts',
            "{$admin}/components/com_contact/models/contacts.php",
        ],
        Component\Contact\Site\Helper\RouteHelper::class => [
            'ContactHelperRoute',
            "{$site}/components/com_contact/helpers/route.php",
        ],
        Component\Content\Site\Helper\RouteHelper::class => [
            'ContentHelperRoute',
            "{$site}/components/com_content/helpers/route.php",
        ],
        Component\Content\Site\Model\ArticlesModel::class => [
            'ContentModelArticles',
            "{$site}/components/com_content/models/articles.php",
        ],
        Component\Fields\Administrator\Helper\FieldsHelper::class => [
            'FieldsHelper',
            "{$admin}/components/com_fields/helpers/fields.php",
        ],
        Component\Fields\Administrator\Plugin\FieldsPlugin::class => [
            'FieldsPlugin',
            "{$admin}/components/com_fields/libraries/fieldsplugin.php",
        ],
        Component\Finder\Site\Helper\RouteHelper::class => [
            'FinderHelperRoute',
            "{$site}/components/com_finder/helpers/route.php",
        ],
        Component\Tags\Administrator\Table\TagTable::class => [
            'TagsTableTag',
            "{$admin}/components/com_tags/tables/tag.php",
        ],
        Component\Tags\Site\Helper\RouteHelper::class => [
            'TagsHelperRoute',
            "{$site}/components/com_tags/helpers/route.php",
        ],
        Component\Tags\Site\Model\TagModel::class => [
            'TagsModelTag',
            "{$site}/components/com_tags/models/tag.php",
        ],
        Component\Tags\Site\Model\TagsModel::class => [
            'TagsModelTags',
            "{$site}/components/com_tags/models/tags.php",
        ],
        Component\Users\Administrator\Helper\UsersHelper::class => [
            'UsersHelper',
            "{$admin}/components/com_users/helpers/users.php",
        ],
        Database\DatabaseDriver::class => 'JDatabaseDriver',
        Module\Breadcrumbs\Site\Helper\BreadcrumbsHelper::class => [
            'ModBreadCrumbsHelper',
            "{$site}/modules/mod_breadcrumbs/helper.php",
        ],
        Module\Finder\Site\Helper\FinderHelper::class => [
            'ModFinderHelper',
            "{$site}/modules/mod_finder/helper.php",
        ],
    ];
}

if (version_compare(JVERSION, '3.9', '<')) {
    $classes += [
        CMS\Filesystem\File::class => 'JFile',
        CMS\Filesystem\Folder::class => 'JFolder',
        CMS\Filesystem\Path::class => 'JPath',
    ];
}

if (version_compare(JVERSION, '3.8', '<')) {
    $classes += [
        CMS\Access\Access::class => 'JAccess',
        CMS\Component\ComponentHelper::class => 'JComponentHelper',
        CMS\Date\Date::class => 'JDate',
        CMS\Document\DocumentRenderer::class => 'JDocumentRenderer',
        CMS\Editor\Editor::class => 'JEditor',
        CMS\Factory::class => 'JFactory',
        CMS\Form\FormField::class => 'JFormField',
        CMS\Helper\MediaHelper::class => 'JHelperMedia',
        CMS\Helper\ModuleHelper::class => 'JModuleHelper',
        CMS\Helper\RouteHelper::class => 'JHelperRoute',
        CMS\Helper\TagsHelper::class => 'JHelperTags',
        CMS\HTML\HTMLHelper::class => 'JHtml',
        CMS\Http\HttpFactory::class => 'JHttpFactory',
        CMS\Language\Multilanguage::class => 'JLanguageMultilang',
        CMS\Language\Text::class => 'JText',
        CMS\Layout\LayoutHelper::class => 'JLayoutHelper',
        CMS\Menu\AbstractMenu::class => 'JMenu',
        CMS\MVC\Controller\BaseController::class => 'JControllerLegacy',
        CMS\MVC\Model\BaseDatabaseModel::class => 'JModelLegacy',
        CMS\Plugin\CMSPlugin::class => 'JPlugin',
        CMS\Plugin\PluginHelper::class => 'JPluginHelper',
        CMS\Router\Route::class => 'JRoute',
        CMS\Router\Router::class => 'JRouter',
        CMS\Session\Session::class => 'JSession',
        CMS\Uri\Uri::class => 'JUri',
    ];
}

if ($classes) {
    spl_autoload_register(function ($class_name) use ($classes) {
        if (empty($classes[$class_name])) {
            return;
        }

        if (is_array($class = $classes[$class_name])) {
            [$class, $path] = $class;
            if (!class_exists($class, false)) {
                require_once $path;
            }
        }

        class_alias($class, $class_name);
    });
}
