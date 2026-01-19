<?php

// Config
$site = '~theme.site';
$header = '~theme.mobile.header';
$navbar = '~theme.mobile.navbar';
$dialog = '~theme.mobile.dialog';
$mobile = '~theme.mobile';
$navbar_default = '~theme.navbar';

// Options
$layout = $config("$header.layout");

// Outside
$outside = $config("$site.layout") == 'boxed' && $config("$site.boxed.header_outside");

// Header
$attrs = [];
$attrs['class'][] = 'tm-header-mobile';
$attrs['class'][] = $config("$mobile.breakpoint") ? "uk-hidden@{$config("$mobile.breakpoint")}" : '';

// Navbar Container
$attrs_navbar_container = [];
$attrs_navbar_container['class'][] = 'uk-navbar-container';

// Navbar
$attrs_navbar = [

    'class' => [
        'uk-navbar',
        'uk-navbar-justify' => $layout == 'horizontal-justify',
    ],

    'uk-navbar' => [
        'align' => $config("$navbar_default.dropdown_align"),
        'container' => $config("$header.transparent") && $config("$header.blend") ? ($outside ? '.tm-page-container' : '.tm-page') : '.tm-header-mobile',
        'boundary' => '.tm-header-mobile .uk-navbar-container', // By default, it would be the navbar component's element
        'target-x' => $config("$navbar_default.dropdown_target") ? '.tm-header-mobile .uk-navbar' : null,
    ],

];

if ($config("$navbar_default.dropbar")) {

    $attrs_navbar['uk-navbar']['target-y'] = '.tm-header-mobile .uk-navbar-container';
    $attrs_navbar['uk-navbar']['dropbar'] = true;
    $attrs_navbar['uk-navbar']['dropbar-anchor'] = $config("$header.transparent") && $config("$header.blend") ? ($outside ? '.tm-page-container > .tm-page' : '.tm-page > main') : '.tm-header-mobile .uk-navbar-container'; // Has to be after navbar container because it has uk-light/dark
    $attrs_navbar['uk-navbar']['dropbar-transparent-mode'] = $config("$header.transparent") ? 'behind' : 'remove';

}

// Sticky
$attrs_sticky = [];
if ($sticky = $config("$navbar.sticky")) {

    if ($config("$header.transparent") && $config("$header.blend")) {
        $attrs_navbar['uk-navbar']['close-on-scroll'] = true;
    } else {
        $attrs_navbar['uk-navbar']['container'] = '.tm-header-mobile > [uk-sticky]';
    }

    $attrs_sticky = array_filter([
        'uk-sticky' => true,
        'show-on-up' => $sticky == 2,
        'animation' => $sticky == 2 ? 'uk-animation-slide-top' : '',
        'cls-active' => 'uk-navbar-sticky',
        'sel-target' => '.uk-navbar-container',
    ]);

}

$attrs_navbar['uk-navbar'] = json_encode(array_filter($attrs_navbar['uk-navbar']));

// Search Expand
$search_expand = '';
if ($config("$header.search") && !str_starts_with($config("$header.search"), 'dialog') && str_starts_with($config("$header.search_layout"), 'input') && preg_match('/^horizontal-(left|center|right)$/', $layout) && $config("$header.search_expand")) {
    $layout = 'horizontal-justify';
    $search_expand = 'uk-flex-1';
}

// Transparent
if ($config("$header.transparent")) {

    $attrs_navbar_container['class'][] = 'uk-navbar-transparent';

    if ($config("$header.blend")) {
        $attrs['class'][] = 'uk-blend-difference uk-position-z-index-high';
        $attrs_navbar_container['class'][] = 'uk-light';
    } else {
        if ($config("$header.transparent_color_separately")) {
            $attrs['uk-inverse'] = 'target: .uk-navbar-left, .uk-navbar-center, .uk-navbar-right';
        } else {
            $attrs['uk-inverse'] = 'target: .uk-navbar-container';
        }
        $attrs_navbar_container['class'][] = 'uk-position-relative uk-position-z-index-high';
    }

} elseif (($config("$site.boxed.header_transparent") && $outside) || $config('header.section.transparent')) {

    if ($sticky) {
        $attrs_sticky['cls-inactive'] = 'uk-navbar-transparent';
        if ($sticky == 1) {
            $attrs_sticky['animation'] = 'uk-animation-slide-top';
        }
    } else {
        $attrs_navbar_container['class'][] = 'uk-navbar-transparent';
    }

}

if ($outside) {

    if (!$config("$header.transparent") && $config("$site.boxed.header_transparent")) {

        $attrs['uk-inverse'] = 'target: .uk-navbar-container; sel-active: .uk-navbar-transparent';

        if ($sticky) {
            $attrs_sticky['top'] = '300';
        }
    }

} elseif ($config("$header.transparent") || $config('header.section.transparent')) {

    $attrs['uk-header'] = true;
    $attrs['class'][] = 'tm-header-overlay';

    if (!$config("$header.transparent")) {
        $attrs['uk-inverse'] = 'target: .uk-navbar-container; sel-active: .uk-navbar-transparent';

        if ($sticky) {
            $attrs_sticky['tm-section-start'] = true;
        }

    }

}

// Width Container
$attrs_width_container = [];
$attrs_width_container['class'][] = 'uk-container uk-container-expand';

?>

<?php if ($this->countModules('logo-mobile') || $this->countModules('navbar-mobile') || $this->countModules('header-mobile')) :?>

<header<?= $this->attrs($attrs) ?>>

<?php

// Horizontal layouts
if (str_starts_with($layout, 'horizontal')) :

    $attrs_width_container['class'][] = $this->countModules('logo-mobile') && $config("$header.logo_padding_remove") && $layout != 'horizontal-center-logo' ? 'uk-padding-remove-left' : '';

    ?>

    <?php if ($sticky) : ?>
    <div<?= $this->attrs($attrs_sticky) ?>>
    <?php endif ?>

        <div<?= $this->attrs($attrs_navbar_container) ?>>

            <div<?= $this->attrs($attrs_width_container) ?>>
                <nav<?= $this->attrs($attrs_navbar) ?>>

                    <?php if (($layout != 'horizontal-center-logo' && $this->countModules('logo-mobile')) || (preg_match('/^horizontal-(left|justify|center-logo)/', $layout) && $this->countModules('navbar-mobile')) || ($layout == 'horizontal-justify' && $this->countModules('header'))) : ?>
                    <div class="uk-navbar-left <?= $search_expand ?>">

                        <?php if ($layout != 'horizontal-center-logo') : ?>
                            <jdoc:include type="modules" name="logo-mobile" />
                        <?php endif ?>

                        <?php if (preg_match('/^horizontal-(left|justify|center-logo)/', $layout)) : ?>
                            <jdoc:include type="modules" name="navbar-mobile" />
                        <?php endif ?>

                        <?php if ($layout == 'horizontal-justify') : ?>
                            <jdoc:include type="modules" name="header-mobile" />
                        <?php endif ?>

                    </div>
                    <?php endif ?>

                    <?php if (($layout == 'horizontal-center-logo' && $this->countModules('logo-mobile')) || ($layout == 'horizontal-center' && $this->countModules('navbar-mobile'))) : ?>
                    <div class="uk-navbar-center">

                        <?php if ($layout == 'horizontal-center-logo') : ?>
                            <jdoc:include type="modules" name="logo-mobile" />
                        <?php endif ?>

                        <?php if ($layout == 'horizontal-center') : ?>
                            <jdoc:include type="modules" name="navbar-mobile" />
                        <?php endif ?>

                    </div>
                    <?php endif ?>

                    <?php if (($layout != 'horizontal-justify' && $this->countModules('header-mobile')) || ($layout == 'horizontal-right' && $this->countModules('navbar-mobile'))) : ?>
                    <div class="uk-navbar-right">

                        <?php if ($layout == 'horizontal-right') : ?>
                            <jdoc:include type="modules" name="navbar-mobile" />
                        <?php endif ?>

                        <?php if ($layout != 'horizontal-justify') : ?>
                            <jdoc:include type="modules" name="header-mobile" />
                        <?php endif ?>

                    </div>
                    <?php endif ?>

                </nav>
            </div>

        </div>

    <?php if ($sticky) : ?>
    </div>
    <?php endif ?>

<?php endif ?>

<?php

// Dialog
$attrs_dialog = [];
$attrs_dialog_push = [];

if (preg_match('/^(offcanvas|modal|dropbar)-center/', $config("$dialog.layout"))) {
    $attrs_dialog['class'][] = 'uk-margin-auto-vertical';
} else {
    $attrs_dialog['class'][] = 'uk-margin-auto-bottom';
}
$attrs_dialog_push['class'][] = 'uk-grid-margin';

$attrs_dialog['class'][] = $config("$dialog.text_center") ? 'uk-text-center' : '';
$attrs_dialog_push['class'][] = $config("$dialog.text_center") ? 'uk-text-center' : '';

// Modal
$attrs_modal = [];
$attrs_modal['class'][] = 'uk-modal-body uk-padding-large uk-margin-auto uk-flex uk-flex-column uk-box-sizing-content';
$attrs_modal['class'][] = $config("$dialog.modal.width") ? 'uk-width-' .  $config("$dialog.modal.width") : 'uk-width-auto@s';
$attrs_modal['uk-height-viewport'] = true;
$attrs_modal['uk-toggle'] = json_encode(array_filter([
    'cls' => 'uk-padding-large',
    'mode' => 'media',
    'media' => '@s',
]));

// Dropbar
if (str_starts_with($config("$dialog.layout"), 'dropbar')) {

    $attrs_dropbar = [];
    $attrs_dropbar['class'][] = 'uk-dropbar';
    $attrs_dropbar['class'][] = $config("$dialog.dropbar.padding_remove_horizontal") ? 'uk-padding-remove-horizontal' : '';
    $attrs_dropbar['class'][] = $config("$dialog.dropbar.padding_remove_vertical") ? 'uk-padding-remove-vertical' : '';

    if (!$config("$dialog.dropbar.animation") || $config("$dialog.dropbar.animation") == 'reveal-top') {
        $attrs_dropbar['class'][] = 'uk-dropbar-top';
    } elseif ($config("$dialog.dropbar.animation") == 'slide-left') {
        $attrs_dropbar['class'][] = 'uk-dropbar-left';
    }
    elseif ($config("$dialog.dropbar.animation") == 'slide-right') {
        $attrs_dropbar['class'][] = 'uk-dropbar-right';
    }

    $attrs_dropbar['uk-drop'] = [
        // Default
        'clsDrop' => 'uk-dropbar',
        'flip' => 'false', // Has to be a string
        'container' => $sticky ? '.tm-header-mobile > [uk-sticky]' : '.tm-header-mobile',
        'target-y' => '.tm-header-mobile .uk-navbar-container',
        // New
        'mode' => 'click',
        'target-x' => '.tm-header-mobile .uk-navbar-container',
        'stretch' => true,
        'pos' => $config("$dialog.dropbar.animation") == 'slide-right' ? "bottom-right" : "bottom-left",
        'bgScroll' => 'false', // Has to be a string
        'animation' => $config("$dialog.dropbar.animation") ?: null,
        'animateOut' => true,
        'duration' => 300,
        'toggle' => 'false', // Has to be a string
    ];

    // Behind navbar
    if ($config("$header.transparent")) {
        $attrs_dropbar['uk-drop']['inset'] = true;
        $attrs_dropbar['class'][] = 'uk-dropbar-inset';
        $attrs_dropbar['uk-drop']['pos'] = $config("$dialog.dropbar.animation") == 'slide-right' ? "top-right" : "top-left";

        if ($config("$header.blend")) {
            $attrs_dropbar['uk-drop']['container'] = $outside ? '.tm-page-container' : '.tm-page';
        }
        // Set same z-index as dropnav (high but behind navbar, which is set to high). Needed in two cases: 1. blend and 2. not sticky and outside
        $attrs_dropbar['style'][] = 'z-index: 980;';
    }

    $attrs_dropbar['uk-drop'] = json_encode(array_filter($attrs_dropbar['uk-drop']));

    $attrs_dropbar_content = [];
    $attrs_dropbar_content['class'][] = 'tm-height-min-1-1 uk-flex uk-flex-column';

}

?>

<?php if ($this->countModules('dialog-mobile') || $this->countModules('dialog-mobile-push')) : ?>

    <?php if (str_starts_with($config("$dialog.layout"), 'offcanvas')) : ?>
    <div id="tm-dialog-mobile" uk-offcanvas="container: true; overlay: true"<?= $this->attrs($config("$dialog.offcanvas") ?: []) ?>>
        <div class="uk-offcanvas-bar uk-flex uk-flex-column">

            <?php if ($config("$dialog.close")) : ?>
            <button class="uk-offcanvas-close uk-close-large" type="button" uk-close uk-toggle="cls: uk-close-large; mode: media; media: @s"></button>
            <?php endif ?>

            <?php if (($this->countModules('dialog-mobile'))) : ?>
            <div<?= $this->attrs($attrs_dialog) ?>>
                <jdoc:include type="modules" name="dialog-mobile" style="grid-stack" />
            </div>
            <?php endif ?>

            <?php if ($this->countModules('dialog-mobile-push')) : ?>
            <div<?= $this->attrs($attrs_dialog_push) ?>>
                <jdoc:include type="modules" name="dialog-mobile-push" style="grid-stack" />
            </div>
            <?php endif ?>

        </div>
    </div>
    <?php endif ?>

    <?php if (str_starts_with($config("$dialog.layout"), 'modal')) : ?>
    <div id="tm-dialog-mobile" class="uk-modal uk-modal-full" uk-modal>
        <div class="uk-modal-dialog uk-flex">

            <?php if ($config("$dialog.close")) : ?>
            <button class="uk-modal-close-full uk-close-large" type="button" uk-close uk-toggle="cls: uk-modal-close-full uk-close-large uk-modal-close-default; mode: media; media: @s"></button>
            <?php endif ?>

            <div<?= $this->attrs($attrs_modal) ?>>

                <?php if (($this->countModules('dialog-mobile'))) : ?>
                <div<?= $this->attrs($attrs_dialog) ?>>
                    <jdoc:include type="modules" name="dialog-mobile" style="grid-stack" />
                </div>
                <?php endif ?>

                <?php if ($this->countModules('dialog-mobile-push')) : ?>
                <div<?= $this->attrs($attrs_dialog_push) ?>>
                    <jdoc:include type="modules" name="dialog-mobile-push" style="grid-stack" />
                </div>
                <?php endif ?>

            </div>

        </div>
    </div>
    <?php endif ?>

    <?php if (str_starts_with($config("$dialog.layout"), 'dropbar')) : ?>
    <div id="tm-dialog-mobile"<?= $this->attrs($attrs_dropbar) ?>>

        <div<?= $this->attrs($attrs_dropbar_content) ?>>

            <?php if ($config("$header.transparent")) : ?>
            <div uk-height-placeholder=".tm-header-mobile .uk-navbar-container"></div>
            <?php endif ?>

            <?php if (($this->countModules('dialog-mobile'))) : ?>
            <div<?= $this->attrs($attrs_dialog) ?>>
                <jdoc:include type="modules" name="dialog-mobile" style="grid-stack" />
            </div>
            <?php endif ?>

            <?php if ($this->countModules('dialog-mobile-push')) : ?>
            <div<?= $this->attrs($attrs_dialog_push) ?>>
                <jdoc:include type="modules" name="dialog-mobile-push" style="grid-stack" />
            </div>
            <?php endif ?>

        </div>

    </div>
    <?php endif ?>

<?php endif ?>

</header>

<?php endif ?>
