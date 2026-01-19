<?php

namespace YOOtheme;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

if (Path::get(__FILE__) !== $file = Path::get('~theme/html/plg_content_pagenavigation/default.php')) {
    return include $file;
}

/** @var View $view */
[$view, $config] = app(View::class, Config::class);

$props = [
    'isPage' => $row->catid == $config('~theme.page_category'),
];

$el = $view->el('nav', [
    'aria-label' => Text::_('TPL_YOOTHEME_PAGINATION'),
    'class' => [
        'uk-margin-xlarge {@isPage}',
        'uk-margin-medium {@!isPage}',
    ],
]);

$list = $view->el('ul', [
    'class' => [
        'uk-pagination',
        'uk-margin-remove-bottom',
        'uk-flex-between {@isPage}',
    ],
]);

$prev = $view->el('li');
$next = $view->el('li', [
    'class' => [
        'uk-margin-auto-left {@!isPage}',
    ],
]);

?>

<?= $el() ?>

    <?= $list($props) ?>

    <?php if ($row->prev) : ?>
        <?= $prev($props) ?>
            <a href="<?= $row->prev ?>"><span uk-pagination-previous></span> <?= $row->prev_label ?></a>
        <?= $prev->end() ?>
    <?php endif ?>

    <?php if ($row->next) : ?>
        <?= $next($props) ?>
        <a href="<?= $row->next ?>"><?= $row->next_label ?> <span uk-pagination-next></span></a>
        <?= $next->end() ?>
    <?php endif ?>

    <?= $list->end() ?>

<?= $el->end() ?>
