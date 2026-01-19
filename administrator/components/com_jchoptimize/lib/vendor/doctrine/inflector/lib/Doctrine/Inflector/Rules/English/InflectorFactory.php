<?php

declare (strict_types=1);
namespace _JchOptimizeVendor\Doctrine\Inflector\Rules\English;

use _JchOptimizeVendor\Doctrine\Inflector\GenericLanguageInflectorFactory;
use _JchOptimizeVendor\Doctrine\Inflector\Rules\Ruleset;
final class InflectorFactory extends GenericLanguageInflectorFactory
{
    protected function getSingularRuleset() : Ruleset
    {
        return Rules::getSingularRuleset();
    }
    protected function getPluralRuleset() : Ruleset
    {
        return Rules::getPluralRuleset();
    }
}
