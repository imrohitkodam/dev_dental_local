<?php

/*
 * @package     XT Transitional Package from FrameworkOnFramework
 *
 * @author      Extly, CB. <team@extly.com>
 * @copyright   Copyright (c)2012-2024 Extly, CB. All rights reserved.
 *              Based on Akeeba's FrameworkOnFramework
 * @license     https://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 *
 * @see         https://www.extly.com
 */

// Protect from unauthorized access
defined('XTF0F_INCLUDED') || exit;

/**
 * This class is taken verbatim from:
 *
 * lessphp v0.3.9
 * http://leafo.net/lessphp
 *
 * LESS css compiler, adapted from http://lesscss.org
 *
 * Copyright 2012, Leaf Corcoran <leafot@gmail.com>
 * Licensed under MIT or GPLv3, see LICENSE
 *
 * Responsible for taking a string of LESS code and converting it into a syntax tree
 *
 * @since  2.0
 */
class XTF0FLessParser
{
    public $eatWhiteDefault = true;

    public $lessc;

    public $sourceName;

    public $writeComments = false;

    public $count;

    public $line;

    public $env;

    public $buffer;

    /**
     * @var never[]
     */
    public $seenComments;

    /**
     * @var bool
     */
    public $inExp;

    /**
     * @var string
     */
    public $currentProperty;

    public $commentsSeen;

    // Used to uniquely identify blocks
    protected static $nextBlockId = 0;

    protected static $precedence = [
        '=<'					 => 0,
        '>='					 => 0,
        '='						 => 0,
        '<'						 => 0,
        '>'						 => 0,
        '+'						 => 1,
        '-'						 => 1,
        '*'						 => 2,
        '/'						 => 2,
        '%'						 => 2,
    ];

    protected static $whitePattern;

    protected static $commentMulti;

    protected static $commentSingle = '//';

    protected static $commentMultiLeft = '/*';

    protected static $commentMultiRight = '*/';

    // Regex string to match any of the operators
    protected static $operatorString;

    // These properties will supress division unless it's inside parenthases
    protected static $supressDivisionProps = ['/border-radius$/i', '/^font$/i'];

    protected $blockDirectives = ['font-face', 'keyframes', 'page', '-moz-document'];

    protected $lineDirectives = ['charset'];

    /**
     * if we are in parens we can be more liberal with whitespace around
     * operators because it must evaluate to a single value and thus is less
     * ambiguous.
     *
     * Consider:
     *     property1: 10 -5; // is two numbers, 10 and -5
     *     property2: (10 -5); // should evaluate to 5
     */
    protected $inParens = false;

    // Caches preg escaped literals
    protected static $literalCache = [];

    /**
     * Constructor
     *
     * @param   [type]  $lessc       [description]
     * @param string $sourceName [description]
     */
    public function __construct($lessc, $sourceName = null)
    {
        // Reference to less needed for vPrefix, mPrefix, and parentSelector
        $this->lessc = $lessc;

        // Name used for error messages
        $this->sourceName = $sourceName;

        if (!self::$operatorString) {
            self::$operatorString = '('.implode('|', array_map(['XTF0FLess', 'preg_quote'], array_keys(self::$precedence))).')';

            $commentSingle = XTF0FLess::preg_quote(self::$commentSingle);
            $commentMultiLeft = XTF0FLess::preg_quote(self::$commentMultiLeft);
            $commentMultiRight = XTF0FLess::preg_quote(self::$commentMultiRight);

            self::$commentMulti = $commentMultiLeft.'.*?'.$commentMultiRight;
            self::$whitePattern = '/'.$commentSingle.'[^\n]*\s*|('.self::$commentMulti.')\s*|\s+/Ais';
        }
    }

    /**
     * Parse text
     *
     * @param string $buffer [description]
     *
     * @return  [type]           [description]
     */
    public function parse($buffer)
    {
        $this->count = 0;
        $this->line = 1;

        // Block stack
        $this->env = null;
        $this->buffer = $this->writeComments ? $buffer : $this->removeComments($buffer);
        $this->pushSpecialBlock('root');
        $this->eatWhiteDefault = true;
        $this->seenComments = [];

        /*
         * trim whitespace on head
         * if (preg_match('/^\s+/', $this->buffer, $m)) {
         * 	$this->line += substr_count($m[0], "\n");
         * 	$this->buffer = ltrim($this->buffer);
         * }
         */
        $this->whitespace();

        // Parse the entire file
        $lastCount = $this->count;
        while (false !== $this->parseChunk()) {
        }

        if ($this->count != strlen($this->buffer)) {
            $this->throwError();
        }

        // TODO report where the block was opened
        if (null !== $this->env->parent) {
            throw new Exception('parse error: unclosed block');
        }

        return $this->env;
    }

    /**
     * Consume a list of values for a property
     *
     * @param   [type]  &$value   [description]
     * @param   [type]  $keyName  [description]
     *
     * @return bool
     */
    public function propertyValue(&$value, $keyName = null)
    {
        $values = [];

        if (null !== $keyName) {
            $this->env->currentProperty = $keyName;
        }

        $s = null;

        while ($this->expressionList($v)) {
            $values[] = $v;
            $s = $this->seek();

            if (!$this->literal(',')) {
                break;
            }
        }

        if ($s) {
            $this->seek($s);
        }

        if (null !== $keyName) {
            unset($this->env->currentProperty);
        }

        if (0 == count($values)) {
            return false;
        }

        $value = XTF0FLess::compressList($values, ', ');

        return true;
    }

    /* misc functions */

    /**
     * [throwError description]
     *
     * @param string $msg [description]
     * @param   [type]  $count  [description]
     *
     * @return void
     */
    public function throwError($msg = 'parse error', $count = null)
    {
        $count ??= $this->count;

        $line = $this->line + substr_count(substr($this->buffer, 0, $count), "\n");

        $loc = empty($this->sourceName) ? 'line: ' . $line : sprintf('%s on line %s', $this->sourceName, $line);

        // TODO this depends on $this->count
        if ($this->peek("(.*?)(\n|$)", $m, $count)) {
            throw new Exception(sprintf('%s: failed at `%s` %s', $msg, $m[1], $loc));
        } else {
            throw new Exception(sprintf('%s: %s', $msg, $loc));
        }
    }

    /**
     * Parse a single chunk off the head of the buffer and append it to the
     * current parse environment.
     * Returns false when the buffer is empty, or when there is an error.
     *
     * This function is called repeatedly until the entire document is
     * parsed.
     *
     * This parser is most similar to a recursive descent parser. Single
     * functions represent discrete grammatical rules for the language, and
     * they are able to capture the text that represents those rules.
     *
     * Consider the function lessc::keyword(). (all parse functions are
     * structured the same)
     *
     * The function takes a single reference argument. When calling the
     * function it will attempt to match a keyword on the head of the buffer.
     * If it is successful, it will place the keyword in the referenced
     * argument, advance the position in the buffer, and return true. If it
     * fails then it won't advance the buffer and it will return false.
     *
     * All of these parse functions are powered by lessc::match(), which behaves
     * the same way, but takes a literal regular expression. Sometimes it is
     * more convenient to use match instead of creating a new function.
     *
     * Because of the format of the functions, to parse an entire string of
     * grammatical rules, you can chain them together using &&.
     *
     * But, if some of the rules in the chain succeed before one fails, then
     * the buffer position will be left at an invalid state. In order to
     * avoid this, lessc::seek() is used to remember and set buffer positions.
     *
     * Before parsing a chain, use $s = $this->seek() to remember the current
     * position into $s. Then if a chain fails, use $this->seek($s) to
     * go back where we started.
     *
     * @return bool
     */
    protected function parseChunk()
    {
        if (empty($this->buffer)) {
            return false;
        }

        $s = $this->seek();

        // Setting a property
        if ($this->keyword($key) && $this->assign()
            && $this->propertyValue($value, $key) && $this->end()) {
            $this->append(['assign', $key, $value], $s);

            return true;
        } else {
            $this->seek($s);
        }

        // Look for special css blocks
        if ($this->literal('@', false)) {
            $this->count--;

            // Media
            if ($this->literal('@media')) {
                if (($this->mediaQueryList($mediaQueries) || true)
                    && $this->literal('{')) {
                    $media = $this->pushSpecialBlock('media');
                    $media->queries = $mediaQueries ?? [];

                    return true;
                } else {
                    $this->seek($s);

                    return false;
                }
            }

            if ($this->literal('@', false) && $this->keyword($dirName)) {
                if ($this->isDirective($dirName, $this->blockDirectives)) {
                    if (($this->openString('{', $dirValue, null, [';']) || true)
                        && $this->literal('{')) {
                        $dir = $this->pushSpecialBlock('directive');
                        $dir->name = $dirName;

                        if (isset($dirValue)) {
                            $dir->value = $dirValue;
                        }

                        return true;
                    }
                } elseif ($this->isDirective($dirName, $this->lineDirectives)) {
                    if ($this->propertyValue($dirValue) && $this->end()) {
                        $this->append(['directive', $dirName, $dirValue]);

                        return true;
                    }
                }
            }

            $this->seek($s);
        }

        // Setting a variable
        if ($this->variable($var) && $this->assign()
            && $this->propertyValue($value) && $this->end()) {
            $this->append(['assign', $var, $value], $s);

            return true;
        } else {
            $this->seek($s);
        }

        if ($this->import($importValue)) {
            $this->append($importValue, $s);

            return true;
        }

        // Opening parametric mixin
        if ($this->tag($tag, true) && $this->argumentDef($args, $isVararg)
            && ($this->guards($guards) || true)
            && $this->literal('{')) {
            $block = $this->pushBlock($this->fixTags([$tag]));
            $block->args = $args;
            $block->isVararg = $isVararg;

            if (!empty($guards)) {
                $block->guards = $guards;
            }

            return true;
        } else {
            $this->seek($s);
        }

        // Opening a simple block
        if ($this->tags($tags) && $this->literal('{')) {
            $tags = $this->fixTags($tags);
            $this->pushBlock($tags);

            return true;
        } else {
            $this->seek($s);
        }

        // Closing a block
        if ($this->literal('}', false)) {
            try {
                $block = $this->pop();
            } catch (Exception $e) {
                $this->seek($s);
                $this->throwError($e->getMessage());
            }

            $hidden = false;

            if (null === $block->type) {
                $hidden = true;

                if (!isset($block->args)) {
                    foreach ($block->tags as $tag) {
                        if (!is_string($tag) || $tag[0] != $this->lessc->mPrefix) {
                            $hidden = false;
                            break;
                        }
                    }
                }

                foreach ($block->tags as $tag) {
                    if (is_string($tag)) {
                        $this->env->children[$tag][] = $block;
                    }
                }
            }

            if (!$hidden) {
                $this->append(['block', $block], $s);
            }

            // This is done here so comments aren't bundled into he block that was just closed
            $this->whitespace();

            return true;
        }

        // Mixin
        if ($this->mixinTags($tags)
            && ($this->argumentValues($argv) || true)
            && ($this->keyword($suffix) || true)
            && $this->end()) {
            $tags = $this->fixTags($tags);
            $this->append(['mixin', $tags, $argv, $suffix], $s);

            return true;
        } else {
            $this->seek($s);
        }

        // Spare ;
        // Got nothing, throw error
        return $this->literal(';');
    }

    /**
     * [isDirective description]
     *
     * @param string $dirname [description]
     * @param   [type]  $directives  [description]
     *
     * @return bool
     */
    protected function isDirective($dirname, $directives)
    {
        // TODO: cache pattern in parser
        $pattern = implode('|', array_map(['XTF0FLess', 'preg_quote'], $directives));
        $pattern = '/^(-[a-z-]+-)?('.$pattern.')$/i';

        return preg_match($pattern, $dirname);
    }

    /**
     * [fixTags description]
     *
     * @param   [type]  $tags  [description]
     *
     * @return  [type]         [description]
     */
    protected function fixTags($tags)
    {
        // Move @ tags out of variable namespace
        foreach ($tags as &$tag) {
            if ($tag[0] == $this->lessc->vPrefix) {
                $tag[0] = $this->lessc->mPrefix;
            }
        }

        return $tags;
    }

    /**
     * a list of expressions
     *
     * @param   [type]  &$exps  [description]
     *
     * @return bool
     */
    protected function expressionList(&$exps)
    {
        $values = [];

        while ($this->expression($exp)) {
            $values[] = $exp;
        }

        if (0 == count($values)) {
            return false;
        }

        $exps = XTF0FLess::compressList($values, ' ');

        return true;
    }

    /**
     * Attempt to consume an expression.
     *
     * @param string &$out [description]
     *
     * @see http://en.wikipedia.org/wiki/Operator-precedence_parser#Pseudo-code
     *
     * @return bool
     */
    protected function expression(&$out)
    {
        if ($this->value($lhs)) {
            $out = $this->expHelper($lhs, 0);

            // Look for / shorthand
            if (!empty($this->env->supressedDivision)) {
                unset($this->env->supressedDivision);
                $s = $this->seek();

                if ($this->literal('/') && $this->value($rhs)) {
                    $out = ['list', '',
                        [$out, ['keyword', '/'], $rhs], ];
                } else {
                    $this->seek($s);
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Recursively parse infix equation with $lhs at precedence $minP
     *
     * @param type $lhs  [description]
     * @param type $minP [description]
     *
     * @return string
     */
    protected function expHelper($lhs, $minP)
    {
        $this->inExp = true;
        $ss = $this->seek();

        while (true) {
            $whiteBefore = isset($this->buffer[$this->count - 1]) && ctype_space($this->buffer[$this->count - 1]);

            // If there is whitespace before the operator, then we require
            // whitespace after the operator for it to be an expression
            $needWhite = $whiteBefore && !$this->inParens;

            if ($this->match(self::$operatorString.($needWhite ? '\s' : ''), $m) && self::$precedence[$m[1]] >= $minP) {
                if (!$this->inParens && isset($this->env->currentProperty) && '/' == $m[1] && empty($this->env->supressedDivision)) {
                    foreach (self::$supressDivisionProps as $supressDivisionProp) {
                        if (preg_match($supressDivisionProp, $this->env->currentProperty)) {
                            $this->env->supressedDivision = true;
                            break 2;
                        }
                    }
                }

                $whiteAfter = isset($this->buffer[$this->count - 1]) && ctype_space($this->buffer[$this->count - 1]);

                if (!$this->value($rhs)) {
                    break;
                }

                // Peek for next operator to see what to do with rhs
                if ($this->peek(self::$operatorString, $next) && self::$precedence[$next[1]] > self::$precedence[$m[1]]) {
                    $rhs = $this->expHelper($rhs, self::$precedence[$next[1]]);
                }

                $lhs = ['expression', $m[1], $lhs, $rhs, $whiteBefore, $whiteAfter];
                $ss = $this->seek();

                continue;
            }

            break;
        }

        $this->seek($ss);

        return $lhs;
    }

    /**
     * [parenValue description]
     *
     * @param   [type]  &$out  [description]
     *
     * @return bool
     */
    protected function parenValue(&$out)
    {
        $s = $this->seek();

        // Speed shortcut
        if (isset($this->buffer[$this->count]) && '(' != $this->buffer[$this->count]) {
            return false;
        }

        $inParens = $this->inParens;

        if ($this->literal('(') && ($this->inParens = true) && $this->expression($exp) && $this->literal(')')) {
            $out = $exp;
            $this->inParens = $inParens;

            return true;
        } else {
            $this->inParens = $inParens;
            $this->seek($s);
        }

        return false;
    }

    /**
     * a single value
     *
     * @param   [type]  &$value  [description]
     *
     * @return bool
     */
    protected function value(&$value)
    {
        $s = $this->seek();

        // Speed shortcut
        if (isset($this->buffer[$this->count]) && '-' == $this->buffer[$this->count]) {
            // Negation
            if ($this->literal('-', false) && (($this->variable($inner) && $inner = ['variable', $inner])
                || $this->unit($inner) || $this->parenValue($inner))) {
                $value = ['unary', '-', $inner];

                return true;
            } else {
                $this->seek($s);
            }
        }

        if ($this->parenValue($value)) {
            return true;
        }

        if ($this->unit($value)) {
            return true;
        }

        if ($this->color($value)) {
            return true;
        }

        if ($this->func($value)) {
            return true;
        }

        if ($this->string($value)) {
            return true;
        }

        if ($this->keyword($word)) {
            $value = ['keyword', $word];

            return true;
        }

        // Try a variable
        if ($this->variable($var)) {
            $value = ['variable', $var];

            return true;
        }

        // Unquote string (should this work on any type?
        if ($this->literal('~') && $this->string($str)) {
            $value = ['escape', $str];

            return true;
        } else {
            $this->seek($s);
        }

        // Css hack: \0
        if ($this->literal('\\') && $this->match('(\d+)', $m)) {
            $value = ['keyword', '\\'.$m[1]];

            return true;
        } else {
            $this->seek($s);
        }

        return false;
    }

    /**
     * an import statement
     *
     * @param   [type]  &$out  [description]
     *
     * @return bool
     */
    protected function import(&$out)
    {
        $s = $this->seek();

        if (!$this->literal('@import')) {
            return false;
        }

        /*
         * @import "something.css" media;
         * @import url("something.css") media;
         * @import url(something.css) media;
         */

        if ($this->propertyValue($value)) {
            $out = ['import', $value];

            return true;
        }

        return null;
    }

    /**
     * [mediaQueryList description]
     *
     * @param   [type]  &$out  [description]
     *
     * @return bool
     */
    protected function mediaQueryList(&$out)
    {
        if ($this->genericList($list, 'mediaQuery', ',', false)) {
            $out = $list[2];

            return true;
        }

        return false;
    }

    /**
     * [mediaQuery description]
     *
     * @param   [type]  &$out  [description]
     *
     * @return  [type]        [description]
     */
    protected function mediaQuery(&$out)
    {
        $s = $this->seek();

        $expressions = null;
        $parts = [];

        if (($this->literal('only') && ($only = true) || $this->literal('not') && ($not = true) || true) && $this->keyword($mediaType)) {
            $prop = ['mediaType'];

            if (isset($only)) {
                $prop[] = 'only';
            }

            if (isset($not)) {
                $prop[] = 'not';
            }

            $prop[] = $mediaType;
            $parts[] = $prop;
        } else {
            $this->seek($s);
        }

        if (!empty($mediaType) && !$this->literal('and')) {
            // ~
        } else {
            $this->genericList($expressions, 'mediaExpression', 'and', false);

            if (is_array($expressions)) {
                $parts = array_merge($parts, $expressions[2]);
            }
        }

        if (0 == count($parts)) {
            $this->seek($s);

            return false;
        }

        $out = $parts;

        return true;
    }

    /**
     * [mediaExpression description]
     *
     * @param   [type]  &$out  [description]
     *
     * @return bool
     */
    protected function mediaExpression(&$out)
    {
        $s = $this->seek();
        $value = null;

        if ($this->literal('(') && $this->keyword($feature) && ($this->literal(':')
            && $this->expression($value) || true) && $this->literal(')')) {
            $out = ['mediaExp', $feature];

            if ($value) {
                $out[] = $value;
            }

            return true;
        } elseif ($this->variable($variable)) {
            $out = ['variable', $variable];

            return true;
        }

        $this->seek($s);

        return false;
    }

    /**
     * An unbounded string stopped by $end
     *
     * @param   [type]  $end          [description]
     * @param   [type]  &$out         [description]
     * @param   [type]  $nestingOpen  [description]
     * @param   [type]  $rejectStrs   [description]
     *
     * @return bool
     */
    protected function openString($end, &$out, $nestingOpen = null, $rejectStrs = null)
    {
        $oldWhite = $this->eatWhiteDefault;
        $this->eatWhiteDefault = false;

        $stop = ["'", '"', '@{', $end];
        $stop = array_map(['XTF0FLess', 'preg_quote'], $stop);

        // $stop[] = self::$commentMulti;

        if (null !== $rejectStrs) {
            $stop = array_merge($stop, $rejectStrs);
        }

        $patt = '(.*?)('.implode('|', $stop).')';

        $nestingLevel = 0;

        $content = [];

        while ($this->match($patt, $m, false)) {
            if (!empty($m[1])) {
                $content[] = $m[1];

                if ($nestingOpen) {
                    $nestingLevel += substr_count($m[1], $nestingOpen);
                }
            }

            $tok = $m[2];

            $this->count -= strlen($tok);

            if ($tok == $end) {
                if (0 == $nestingLevel) {
                    break;
                } else {
                    $nestingLevel--;
                }
            }

            if (("'" == $tok || '"' == $tok) && $this->string($str)) {
                $content[] = $str;
                continue;
            }

            if ('@{' == $tok && $this->interpolation($inter)) {
                $content[] = $inter;
                continue;
            }

            if (in_array($tok, $rejectStrs)) {
                $count = null;
                break;
            }

            $content[] = $tok;
            $this->count += strlen($tok);
        }

        $this->eatWhiteDefault = $oldWhite;

        if (0 == count($content)) {
            return false;
        }

        // Trim the end
        if (is_string(end($content))) {
            $content[count($content) - 1] = rtrim(end($content));
        }

        $out = ['string', '', $content];

        return true;
    }

    /**
     * [string description]
     *
     * @param   [type]  &$out  [description]
     *
     * @return bool
     */
    protected function string(&$out)
    {
        $s = $this->seek();

        if ($this->literal('"', false)) {
            $delim = '"';
        } elseif ($this->literal("'", false)) {
            $delim = "'";
        } else {
            return false;
        }

        $content = [];

        // Look for either ending delim , escape, or string interpolation
        $patt = '([^\n]*?)(@\{|\\\\|'.XTF0FLess::preg_quote($delim).')';

        $oldWhite = $this->eatWhiteDefault;
        $this->eatWhiteDefault = false;

        while ($this->match($patt, $m, false)) {
            $content[] = $m[1];

            if ('@{' == $m[2]) {
                $this->count -= strlen($m[2]);

                if ($this->interpolation($inter, false)) {
                    $content[] = $inter;
                } else {
                    $this->count += strlen($m[2]);

                    // Ignore it
                    $content[] = '@{';
                }
            } elseif ('\\' == $m[2]) {
                $content[] = $m[2];

                if ($this->literal($delim, false)) {
                    $content[] = $delim;
                }
            } else {
                $this->count -= strlen($delim);

                // Delim
                break;
            }
        }

        $this->eatWhiteDefault = $oldWhite;

        if ($this->literal($delim)) {
            $out = ['string', $delim, $content];

            return true;
        }

        $this->seek($s);

        return false;
    }

    /**
     * [interpolation description]
     *
     * @param   [type]  &$out  [description]
     *
     * @return bool
     */
    protected function interpolation(&$out)
    {
        $oldWhite = $this->eatWhiteDefault;
        $this->eatWhiteDefault = true;

        $s = $this->seek();

        if ($this->literal('@{') && $this->openString('}', $interp, null, ["'", '"', ';']) && $this->literal('}', false)) {
            $out = ['interpolate', $interp];
            $this->eatWhiteDefault = $oldWhite;

            if ($this->eatWhiteDefault) {
                $this->whitespace();
            }

            return true;
        }

        $this->eatWhiteDefault = $oldWhite;
        $this->seek($s);

        return false;
    }

    /**
     * [unit description]
     *
     * @param   [type]  &$unit  [description]
     *
     * @return bool
     */
    protected function unit(&$unit)
    {
        // Speed shortcut
        if (isset($this->buffer[$this->count])) {
            $char = $this->buffer[$this->count];

            if (!ctype_digit($char) && '.' != $char) {
                return false;
            }
        }

        if ($this->match('([0-9]+(?:\.[0-9]*)?|\.[0-9]+)([%a-zA-Z]+)?', $m)) {
            $unit = ['number', $m[1], empty($m[2]) ? '' : $m[2]];

            return true;
        }

        return false;
    }

    /**
     * a # color
     *
     * @param   [type]  &$out  [description]
     *
     * @return bool
     */
    protected function color(&$out)
    {
        if ($this->match('(#(?:[0-9a-f]{8}|[0-9a-f]{6}|[0-9a-f]{3}))', $m)) {
            $out = strlen($m[1]) > 7 ? ['string', '', [$m[1]]] : ['raw_color', $m[1]];
            return true;
        }

        return false;
    }

    /**
     * Consume a list of property values delimited by ; and wrapped in ()
     *
     * @param   [type]  &$args  [description]
     * @param   [type]  $delim  [description]
     *
     * @return bool
     */
    protected function argumentValues(&$args, $delim = ',')
    {
        $s = $this->seek();

        if (!$this->literal('(')) {
            return false;
        }

        $values = [];

        while (true) {
            if ($this->expressionList($value)) {
                $values[] = $value;
            }

            if (!$this->literal($delim)) {
                break;
            } else {
                if (null == $value) {
                    $values[] = null;
                }

                $value = null;
            }
        }

        if (!$this->literal(')')) {
            $this->seek($s);

            return false;
        }

        $args = $values;

        return true;
    }

    /**
     * Consume an argument definition list surrounded by ()
     * each argument is a variable name with optional value
     * or at the end a ... or a variable named followed by ...
     *
     * @param   [type]  &$args      [description]
     * @param   [type]  &$isVararg  [description]
     * @param   [type]  $delim      [description]
     *
     * @return bool
     */
    protected function argumentDef(&$args, &$isVararg, $delim = ',')
    {
        $s = $this->seek();
        if (!$this->literal('(')) {
            return false;
        }

        $values = [];

        $isVararg = false;

        while (true) {
            if ($this->literal('...')) {
                $isVararg = true;
                break;
            }

            if ($this->variable($vname)) {
                $arg = ['arg', $vname];
                $ss = $this->seek();

                if ($this->assign() && $this->expressionList($value)) {
                    $arg[] = $value;
                } else {
                    $this->seek($ss);

                    if ($this->literal('...')) {
                        $arg[0] = 'rest';
                        $isVararg = true;
                    }
                }

                $values[] = $arg;

                if ($isVararg) {
                    break;
                }

                continue;
            }

            if ($this->value($literal)) {
                $values[] = ['lit', $literal];
            }

            if (!$this->literal($delim)) {
                break;
            }
        }

        if (!$this->literal(')')) {
            $this->seek($s);

            return false;
        }

        $args = $values;

        return true;
    }

    /**
     * Consume a list of tags
     * This accepts a hanging delimiter
     *
     * @param   [type]  &$tags   [description]
     * @param   [type]  $simple  [description]
     * @param   [type]  $delim   [description]
     *
     * @return bool
     */
    protected function tags(&$tags, $simple = false, $delim = ',')
    {
        $tags = [];

        while ($this->tag($tt, $simple)) {
            $tags[] = $tt;

            if (!$this->literal($delim)) {
                break;
            }
        }

        return 0 != count($tags);
    }

    /**
     * List of tags of specifying mixin path
     * Optionally separated by > (lazy, accepts extra >)
     *
     * @param   [type]  &$tags  [description]
     *
     * @return bool
     */
    protected function mixinTags(&$tags)
    {
        $s = $this->seek();
        $tags = [];

        while ($this->tag($tt, true)) {
            $tags[] = $tt;
            $this->literal('>');
        }

        return 0 != count($tags);
    }

    /**
     * A bracketed value (contained within in a tag definition)
     *
     * @param   [type]  &$value  [description]
     *
     * @return bool
     */
    protected function tagBracket(&$value)
    {
        // Speed shortcut
        if (isset($this->buffer[$this->count]) && '[' != $this->buffer[$this->count]) {
            return false;
        }

        $s = $this->seek();

        if ($this->literal('[') && $this->to(']', $c, true) && $this->literal(']', false)) {
            $value = '['.$c.']';

            // Whitespace?
            if ($this->whitespace()) {
                $value .= ' ';
            }

            // Escape parent selector, (yuck)
            $value = str_replace($this->lessc->parentSelector, '$&$', $value);

            return true;
        }

        $this->seek($s);

        return false;
    }

    /**
     * [tagExpression description]
     *
     * @param   [type]  &$value  [description]
     *
     * @return bool
     */
    protected function tagExpression(&$value)
    {
        $s = $this->seek();

        if ($this->literal('(') && $this->expression($exp) && $this->literal(')')) {
            $value = ['exp', $exp];

            return true;
        }

        $this->seek($s);

        return false;
    }

    /**
     * A single tag
     *
     * @param   [type]   &$tag    [description]
     * @param bool $simple [description]
     *
     * @return bool
     */
    protected function tag(&$tag, $simple = false)
    {
        $chars = $simple ? '^@,:;{}\][>\(\) "\'' : '^@,;{}["\'';

        $s = $this->seek();

        if (!$simple && $this->tagExpression($tag)) {
            return true;
        }

        $hasExpression = false;
        $parts = [];

        while ($this->tagBracket($first)) {
            $parts[] = $first;
        }

        $oldWhite = $this->eatWhiteDefault;

        $this->eatWhiteDefault = false;

        while (true) {
            if ($this->match('(['.$chars.'0-9]['.$chars.']*)', $m)) {
                $parts[] = $m[1];

                if ($simple) {
                    break;
                }

                while ($this->tagBracket($brack)) {
                    $parts[] = $brack;
                }

                continue;
            }

            if (isset($this->buffer[$this->count]) && '@' == $this->buffer[$this->count]) {
                if ($this->interpolation($interp)) {
                    $hasExpression = true;

                    // Don't unescape
                    $interp[2] = true;
                    $parts[] = $interp;

                    continue;
                }

                if ($this->literal('@')) {
                    $parts[] = '@';

                    continue;
                }
            }

            // For keyframes
            if ($this->unit($unit)) {
                $parts[] = $unit[1];
                $parts[] = $unit[2];
                continue;
            }

            break;
        }

        $this->eatWhiteDefault = $oldWhite;

        if ($parts === []) {
            $this->seek($s);

            return false;
        }

        $tag = $hasExpression ? ['exp', ['string', '', $parts]] : trim(implode('', $parts));

        $this->whitespace();

        return true;
    }

    /**
     * A css function
     *
     * @param   [type]  &$func  [description]
     *
     * @return bool
     */
    protected function func(&$func)
    {
        $s = $this->seek();

        if ($this->match('(%|[\w\-_][\w\-_:\.]+|[\w_])', $m) && $this->literal('(')) {
            $fname = $m[1];

            $sPreArgs = $this->seek();

            $args = [];

            while (true) {
                $ss = $this->seek();

                // This ugly nonsense is for ie filter properties
                if ($this->keyword($name) && $this->literal('=') && $this->expressionList($value)) {
                    $args[] = ['string', '', [$name, '=', $value]];
                } else {
                    $this->seek($ss);

                    if ($this->expressionList($value)) {
                        $args[] = $value;
                    }
                }

                if (!$this->literal(',')) {
                    break;
                }
            }

            $args = ['list', ',', $args];

            if ($this->literal(')')) {
                $func = ['function', $fname, $args];

                return true;
            } elseif ('url' == $fname) {
                // Couldn't parse and in url? treat as string
                $this->seek($sPreArgs);

                if ($this->openString(')', $string) && $this->literal(')')) {
                    $func = ['function', $fname, $string];

                    return true;
                }
            }
        }

        $this->seek($s);

        return false;
    }

    /**
     * Consume a less variable
     *
     * @param   [type]  &$name  [description]
     *
     * @return bool
     */
    protected function variable(&$name)
    {
        $s = $this->seek();

        if ($this->literal($this->lessc->vPrefix, false) && ($this->variable($sub) || $this->keyword($name))) {
            $name = empty($sub) ? $this->lessc->vPrefix.$name : ['variable', $sub];
            return true;
        }

        $name = null;
        $this->seek($s);

        return false;
    }

    /**
     * Consume an assignment operator
     * Can optionally take a name that will be set to the current property name
     *
     * @param string $name [description]
     *
     * @return bool
     */
    protected function assign($name = null)
    {
        if ($name) {
            $this->currentProperty = $name;
        }

        return $this->literal(':') || $this->literal('=');
    }

    /**
     * Consume a keyword
     *
     * @param   [type]  &$word  [description]
     *
     * @return bool
     */
    protected function keyword(&$word)
    {
        if ($this->match('([\w_\-\*!"][\w\-_"]*)', $m)) {
            $word = $m[1];

            return true;
        }

        return false;
    }

    /**
     * Consume an end of statement delimiter
     *
     * @return bool
     */
    protected function end()
    {
        if ($this->literal(';')) {
            return true;
        } elseif ($this->count == strlen($this->buffer) || '}' == $this->buffer[$this->count]) {
            // If there is end of file or a closing block next then we don't need a ;
            return true;
        }

        return false;
    }

    /**
     * [guards description]
     *
     * @param   [type]  &$guards  [description]
     *
     * @return bool
     */
    protected function guards(&$guards)
    {
        $s = $this->seek();

        if (!$this->literal('when')) {
            $this->seek($s);

            return false;
        }

        $guards = [];

        while ($this->guardGroup($g)) {
            $guards[] = $g;

            if (!$this->literal(',')) {
                break;
            }
        }

        if (0 == count($guards)) {
            $guards = null;
            $this->seek($s);

            return false;
        }

        return true;
    }

    /**
     * A bunch of guards that are and'd together
     *
     * @param   [type]  &$guardGroup  [description]
     *
     * @todo rename to guardGroup
     *
     * @return bool
     */
    protected function guardGroup(&$guardGroup)
    {
        $s = $this->seek();
        $guardGroup = [];

        while ($this->guard($guard)) {
            $guardGroup[] = $guard;

            if (!$this->literal('and')) {
                break;
            }
        }

        if (0 == count($guardGroup)) {
            $guardGroup = null;
            $this->seek($s);

            return false;
        }

        return true;
    }

    /**
     * [guard description]
     *
     * @param   [type]  &$guard  [description]
     *
     * @return bool
     */
    protected function guard(&$guard)
    {
        $s = $this->seek();
        $negate = $this->literal('not');

        if ($this->literal('(') && $this->expression($exp) && $this->literal(')')) {
            $guard = $exp;

            if ($negate) {
                $guard = ['negate', $guard];
            }

            return true;
        }

        $this->seek($s);

        return false;
    }

    /* raw parsing functions */

    /**
     * [literal description]
     *
     * @param   [type]  $what           [description]
     * @param   [type]  $eatWhitespace  [description]
     *
     * @return bool
     */
    protected function literal($what, $eatWhitespace = null)
    {
        if (null === $eatWhitespace) {
            $eatWhitespace = $this->eatWhiteDefault;
        }

        // Shortcut on single letter
        if (!isset($what[1]) && isset($this->buffer[$this->count])) {
            if ($this->buffer[$this->count] == $what) {
                if (!$eatWhitespace) {
                    $this->count++;

                    return true;
                }
            } else {
                return false;
            }
        }

        if (!isset(self::$literalCache[$what])) {
            self::$literalCache[$what] = XTF0FLess::preg_quote($what);
        }

        return $this->match(self::$literalCache[$what], $m, $eatWhitespace);
    }

    /**
     * [genericList description]
     *
     * @param   [type]   &$out       [description]
     * @param   [type]   $parseItem  [description]
     * @param string $delim   [description]
     * @param bool   $flatten [description]
     *
     * @return bool
     */
    protected function genericList(&$out, $parseItem, $delim = '', $flatten = true)
    {
        $s = $this->seek();
        $items = [];

        while ($this->$parseItem($value)) {
            $items[] = $value;

            if ($delim && !$this->literal($delim)) {
                break;
            }
        }

        if (0 == count($items)) {
            $this->seek($s);

            return false;
        }

        $out = $flatten && 1 == count($items) ? $items[0] : ['list', $delim, $items];

        return true;
    }

    /**
     * Advance counter to next occurrence of $what
     * $until - don't include $what in advance
     * $allowNewline, if string, will be used as valid char set
     *
     * @param   [type]   $what          [description]
     * @param   [type]   &$out          [description]
     * @param bool $until        [description]
     * @param bool $allowNewline [description]
     *
     * @return bool
     */
    protected function to($what, &$out, $until = false, $allowNewline = false)
    {
        if (is_string($allowNewline)) {
            $validChars = $allowNewline;
        } else {
            $validChars = $allowNewline ? '.' : "[^\n]";
        }

        if (!$this->match('('.$validChars.'*?)'.XTF0FLess::preg_quote($what), $m, !$until)) {
            return false;
        }

        if ($until) {
            // Give back $what
            $this->count -= strlen($what);
        }

        $out = $m[1];

        return true;
    }

    /**
     * Try to match something on head of buffer
     *
     * @param   [type]  $regex          [description]
     * @param   [type]  &$out           [description]
     * @param   [type]  $eatWhitespace  [description]
     *
     * @return bool
     */
    protected function match($regex, &$out, $eatWhitespace = null)
    {
        if (null === $eatWhitespace) {
            $eatWhitespace = $this->eatWhiteDefault;
        }

        $r = '/'.$regex.($eatWhitespace && !$this->writeComments ? '\s*' : '').'/Ais';

        if (preg_match($r, $this->buffer, $out, null, $this->count)) {
            $this->count += strlen($out[0]);

            if ($eatWhitespace && $this->writeComments) {
                $this->whitespace();
            }

            return true;
        }

        return false;
    }

    /**
     * Watch some whitespace
     *
     * @return bool
     */
    protected function whitespace()
    {
        if ($this->writeComments) {
            $gotWhite = false;

            while (preg_match(self::$whitePattern, $this->buffer, $m, null, $this->count)) {
                if (isset($m[1]) && empty($this->commentsSeen[$this->count])) {
                    $this->append(['comment', $m[1]]);
                    $this->commentsSeen[$this->count] = true;
                }

                $this->count += strlen($m[0]);
                $gotWhite = true;
            }

            return $gotWhite;
        } else {
            $this->match('', $m);

            return '' !== $m[0];
        }
    }

    /**
     * Match something without consuming it
     *
     * @param   [type]  $regex  [description]
     * @param   [type]  &$out   [description]
     * @param   [type]  $from   [description]
     *
     * @return bool
     */
    protected function peek($regex, &$out = null, $from = null)
    {
        if (null === $from) {
            $from = $this->count;
        }

        $r = '/'.$regex.'/Ais';
        $result = preg_match($r, $this->buffer, $out, null, $from);

        return $result;
    }

    /**
     * Seek to a spot in the buffer or return where we are on no argument
     *
     * @param   [type]  $where  [description]
     *
     * @return bool
     */
    protected function seek($where = null)
    {
        if (null === $where) {
            return $this->count;
        } else {
            $this->count = $where;
        }

        return true;
    }

    /**
     * [pushBlock description]
     *
     * @param   [type]  $selectors  [description]
     * @param   [type]  $type       [description]
     *
     * @return stdClass
     */
    protected function pushBlock($selectors = null, $type = null)
    {
        $b = new stdClass();
        $b->parent = $this->env;

        $b->type = $type;
        $b->id = self::$nextBlockId++;

        // TODO: kill me from here
        $b->isVararg = false;
        $b->tags = $selectors;

        $b->props = [];
        $b->children = [];

        $this->env = $b;

        return $b;
    }

    /**
     * Push a block that doesn't multiply tags
     *
     * @param   [type]  $type  [description]
     *
     * @return stdClass
     */
    protected function pushSpecialBlock($type)
    {
        return $this->pushBlock(null, $type);
    }

    /**
     * Append a property to the current block
     *
     * @param   [type]  $prop  [description]
     * @param   [type]  $pos   [description]
     *
     * @return void
     */
    protected function append($prop, $pos = null)
    {
        if (null !== $pos) {
            $prop[-1] = $pos;
        }

        $this->env->props[] = $prop;
    }

    /**
     * Pop something off the stack
     *
     * @return  [type]  [description]
     */
    protected function pop()
    {
        $old = $this->env;
        $this->env = $this->env->parent;

        return $old;
    }

    /**
     * Remove comments from $text
     *
     * @param   [type]  $text  [description]
     *
     * @todo: make it work for all functions, not just url
     *
     * @return  [type]         [description]
     */
    protected function removeComments($text)
    {
        $look = [
            'url(', '//', '/*', '"', "'",
        ];

        $out = '';
        $min = null;

        while (true) {
            // Find the next item
            foreach ($look as $token) {
                $pos = strpos($text, $token);

                if (false !== $pos && (!isset($min) || $pos < $min[1])) {
                    $min = [$token, $pos];
                }
            }

            if (null === $min) {
                break;
            }

            $count = $min[1];
            $skip = 0;
            $newlines = 0;

            switch ($min[0]) {
                case 'url(':

                    if (preg_match('/url\(.*?\)/', $text, $m, 0, $count)) {
                        $count += strlen($m[0]) - strlen($min[0]);
                    }

                    break;
                case '"':
                case "'":

                    if (preg_match('/'.$min[0].'.*?'.$min[0].'/', $text, $m, 0, $count)) {
                        $count += strlen($m[0]) - 1;
                    }

                    break;
                case '//':
                    $skip = strpos($text, "\n", $count);

                    if (false === $skip) {
                        $skip = strlen($text) - $count;
                    } else {
                        $skip -= $count;
                    }

                    break;
                case '/*':

                    if (preg_match('/\/\*.*?\*\//s', $text, $m, 0, $count)) {
                        $skip = strlen($m[0]);
                        $newlines = substr_count($m[0], "\n");
                    }

                    break;
            }

            if (0 == $skip) {
                $count += strlen($min[0]);
            }

            $out .= substr($text, 0, $count).str_repeat("\n", $newlines);
            $text = substr($text, $count + $skip);

            $min = null;
        }

        return $out.$text;
    }
}
