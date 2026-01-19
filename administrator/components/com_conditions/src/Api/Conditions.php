<?php
/**
 * @package         Conditions
 * @version         25.11.2254
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            https://regularlabs.com
 * @copyright       Copyright © 2025 Regular Labs All Rights Reserved
 * @license         GNU General Public License version 2 or later
 */

namespace RegularLabs\Component\Conditions\Administrator\Api;

use RegularLabs\Component\Conditions\Administrator\Helper\Cache;
use RegularLabs\Component\Conditions\Administrator\Helper\Convert;
use RegularLabs\Component\Conditions\Administrator\Helper\Helper;
use RegularLabs\Component\Conditions\Administrator\Model\ItemModel;

defined('_JEXEC') or die;

/**
 * Class Conditions
 *
 * @package RegularLabs\Library
 */
class Conditions
{
    static  $params;
    private $article;
    private $category_id;
    private $condition;
    private $module;
    private $request;

    public function __construct($article = null, $module = null)
    {
        $this->article = $article;
        $this->module  = $module;
    }

    public function pass(array $enabled_types = []): bool
    {
        if (empty($this->condition) || $this->condition->published < 1)
        {
            return true;
        }

        $cache = new Cache([
            __METHOD__, $this->article->id ?? '', $this->module->id ?? '', $this->condition->hash,
            $enabled_types,
        ]);

        if ($cache->exists())
        {
            return $cache->get();
        }

        $pass = true;

        foreach ($this->condition->groups as $group)
        {
            if (empty($group))
            {
                continue;
            }

            $pass_group = $this->passGroup($group, $enabled_types);

            // Break if not passed and matching method is ALL
            if ( ! $pass_group && $this->condition->match_all)
            {
                $pass = false;
                break;
            }

            // Break if passed and matching method is ANY
            if ($pass_group && ! $this->condition->match_all)
            {
                $pass = true;
                break;
            }

            $pass = $pass_group;
        }


        return $cache->set($pass);
    }

    public function setCategory(?int $category_id): self
    {
        $this->category_id = $category_id;

        return $this;
    }

    public function setConditionByAttributes(object $attributes, string $name = ''): self
    {
        $this->condition = Convert::fromObject($attributes, $name);

        return $this;
    }

    public function setConditionByExtensionItem(string $extension, int $item_id): self
    {
        $this->condition = (new ItemModel)->getConditionByExtensionItem($extension, $item_id, false);

        return $this;
    }

    public function setConditionById(int $condition_id): self
    {
        $this->condition = (new ItemModel)->getConditionById($condition_id, false);

        return $this;
    }

    public function setConditionByMixed(mixed $condition): self
    {
        $this->condition = (new ItemModel)->setConditionByMixed($condition, false);

        return $this;
    }

    public function setRequest(object|false|null $request): self
    {
        $this->request = $request;

        return $this;
    }

    private function passGroup(object $group, array $enabled_types = []): bool
    {
        $cache = new Cache([
            __METHOD__, $this->article->id ?? '', $this->module->id ?? '', $group->hash,
            $enabled_types,
        ]);

        if ($cache->exists())
        {
            return $cache->get();
        }

        $pass = true;

        foreach ($group->rules as $rule)
        {
            if ( ! empty($enabled_types) && ! in_array($rule->type, $enabled_types))
            {
                continue;
            }

            $pass_rule = $this->passRule($rule);

            // Break if not passed and matching method is ALL
            if ( ! $pass_rule && $group->match_all)
            {
                $pass = false;
                break;
            }

            // Break if passed and matching method is ANY
            if ($pass_rule && ! $group->match_all)
            {
                $pass = true;
                break;
            }

            $pass = $pass_rule;
        }

        return $cache->set($pass);
    }

    private function passRule(object $rule): bool
    {
        $cache = new Cache([__METHOD__, $this->article->id ?? '', $this->module->id ?? '', $rule]);

        if ($cache->exists())
        {
            return $cache->get();
        }

        if ( ! Helper::conditionIsActive($rule))
        {
            return $cache->set(true);
        }

        $class = Helper::getConditionsClass($rule, $this->article, $this->module, $this->request, $this->category_id);

        if ( ! $class)
        {
            return $cache->set(true);
        }

        $class->beforePass();

        $pass = $class->pass();

        if ($rule->exclude ?? false)
        {
            $pass = ! $pass;
        }

        return $cache->set($pass);
    }
}
