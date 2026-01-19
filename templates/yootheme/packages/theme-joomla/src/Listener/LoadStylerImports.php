<?php

namespace YOOtheme\Theme\Joomla\Listener;

use YOOtheme\Path;
use YOOtheme\Theme\Styler\Styler;

class LoadStylerImports
{
    public Styler $styler;

    public function __construct(Styler $styler)
    {
        $this->styler = $styler;
    }

    public function handle(array $imports): array
    {
        if (version_compare(JVERSION, '4.0', '<')) {
            $bootstrap = Path::get(
                '~theme/packages/theme-joomla/assets/less/bootstrap-joomla3/bootstrap.less',
            );

            foreach ($this->styler->resolveImports($bootstrap) as $file => $content) {
                $imports[str_replace('/bootstrap-joomla3/', '/bootstrap/', $file)] = $content;
            }
        }

        return $imports;
    }
}
