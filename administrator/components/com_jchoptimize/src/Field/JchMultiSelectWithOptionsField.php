<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 *  @package   jchoptimize/core
 *  @author    Samuel Marshall <samuel@jch-optimize.net>
 *  @copyright Copyright (c) 2025 Samuel Marshall / JCH Optimize
 *  @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace CodeAlfa\Component\JchOptimize\Administrator\Field;

use SimpleXMLElement;

use function array_merge;

// phpcs:disable PSR1.Files.SideEffects
defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class JchMultiSelectWithOptionsField extends JchMultiSelectField
{
    protected $type = 'JchMultiSelectWithOptions';

    protected string $valueType = 'url';

    protected string $option1 = 'ieo';

    protected string $option2 = 'dontmove';

    protected string $option1Header = 'Ignore execution order';

    protected string $option2Header = 'Don\'t move to bottom';

    protected string $subFieldClass = '';

    public static int $incrementor = 0;

    protected $layout = 'form.field.jch-multiselect-with-options';

    public function setup(SimpleXMLElement $element, $value, $group = null): bool
    {
        if ($element['option1']) {
            $this->option1 = (string)$element['option1'];
        }
        if ($element['option2']) {
            $this->option2 = (string)$element['option2'];
        }
        if ($element['valueType']) {
            $this->valueType = (string)$element['valueType'];
        }
        if ($element['option1Header']) {
            $this->option1Header = (string)$element['option1Header'];
        }
        if ($element['option2Header']) {
            $this->option2Header = (string)$element['option2Header'];
        }
        if ($element['subFieldClass']) {
            $this->subFieldClass = (string)$element['subFieldClass'];
        }

        return parent::setup($element, $value, $group);
    }

    protected function getLayoutData(): array
    {
        return array_merge(
            parent::getLayoutData(),
            [
                'option1' => $this->option1,
                'option2' => $this->option2,
                'valueType' => $this->valueType,
                'multiSelect' => $this->multiSelect,
                'option1Header' => $this->option1Header,
                'option2Header' => $this->option2Header,
                'subFieldClass' => $this->subFieldClass,
                'option1SubFieldLayout' => 'subfield.checkbox',
                'option2SubFieldLayout' => 'subfield.checkbox',
                'option1SubFieldClass' => 'jch-js-ieo',
                'option2SubFieldClass' => 'jch-js-dontmove',
                'option1Obj' => "{\"type\": \"checkbox\", \"name\": \"{$this->option1}\", \"class\": \"$this->subFieldClass\"",
                'option2Obj' => "{\"type\": \"checkbox\", \"name\": \"{$this->option2}\", \"class\": \"$this->subFieldClass\"",
            ]
        );
    }

    protected function getOptions(): array
    {
        return [];
    }
}
