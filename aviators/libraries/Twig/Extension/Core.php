<?php

if (!defined('ENT_SUBSTITUTE')) {
    define('ENT_SUBSTITUTE', 8);
}

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncExtension_Core extends CreasyncExtension
{
    protected $dateFormats = array('F j, Y H:i', '%d days');
    protected $numberFormat = array(0, '.', ',');
    protected $timezone = null;

    /**
     * Sets the default format to be used by the date filter.
     *
     * @param string $format             The default date format string
     * @param string $dateIntervalFormat The default date interval format string
     */
    public function setDateFormat($format = null, $dateIntervalFormat = null)
    {
        if (null !== $format) {
            $this->dateFormats[0] = $format;
        }

        if (null !== $dateIntervalFormat) {
            $this->dateFormats[1] = $dateIntervalFormat;
        }
    }

    /**
     * Gets the default format to be used by the date filter.
     *
     * @return array The default date format string and the default date interval format string
     */
    public function getDateFormat()
    {
        return $this->dateFormats;
    }

    /**
     * Sets the default timezone to be used by the date filter.
     *
     * @param DateTimeZone|string $timezone The default timezone string or a DateTimeZone object
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone instanceof DateTimeZone ? $timezone : new DateTimeZone($timezone);
    }

    /**
     * Gets the default timezone to be used by the date filter.
     *
     * @return DateTimeZone The default timezone currently in use
     */
    public function getTimezone()
    {
        if (null === $this->timezone) {
            $this->timezone = new DateTimeZone(date_default_timezone_get());
        }

        return $this->timezone;
    }

    /**
     * Sets the default format to be used by the number_format filter.
     *
     * @param integer $decimal      The number of decimal places to use.
     * @param string  $decimalPoint The character(s) to use for the decimal point.
     * @param string  $thousandSep  The character(s) to use for the thousands separator.
     */
    public function setNumberFormat($decimal, $decimalPoint, $thousandSep)
    {
        $this->numberFormat = array($decimal, $decimalPoint, $thousandSep);
    }

    /**
     * Get the default format used by the number_format filter.
     *
     * @return array The arguments for number_format()
     */
    public function getNumberFormat()
    {
        return $this->numberFormat;
    }

    /**
     * Returns the token parser instance to add to the existing list.
     *
     * @return array An array of CreasyncTokenParser instances
     */
    public function getTokenParsers()
    {
        return array(
            new CreasyncTokenParser_For(),
            new CreasyncTokenParser_If(),
            new CreasyncTokenParser_Extends(),
            new CreasyncTokenParser_Include(),
            new CreasyncTokenParser_Block(),
            new CreasyncTokenParser_Use(),
            new CreasyncTokenParser_Filter(),
            new CreasyncTokenParser_Macro(),
            new CreasyncTokenParser_Import(),
            new CreasyncTokenParser_From(),
            new CreasyncTokenParser_Set(),
            new CreasyncTokenParser_Spaceless(),
            new CreasyncTokenParser_Flush(),
            new CreasyncTokenParser_Do(),
            new CreasyncTokenParser_Embed(),
        );
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        $filters = array(
            // formatting filters
            new CreasyncSimpleFilter('date', 'twig_date_format_filter', array('needs_environment' => true)),
            new CreasyncSimpleFilter('date_modify', 'twig_date_modify_filter', array('needs_environment' => true)),
            new CreasyncSimpleFilter('format', 'sprintf'),
            new CreasyncSimpleFilter('replace', 'strtr'),
            new CreasyncSimpleFilter('number_format', 'twig_number_format_filter', array('needs_environment' => true)),
            new CreasyncSimpleFilter('abs', 'abs'),

            // encoding
            new CreasyncSimpleFilter('url_encode', 'twig_urlencode_filter'),
            new CreasyncSimpleFilter('json_encode', 'twig_jsonencode_filter'),
            new CreasyncSimpleFilter('convert_encoding', 'twig_convert_encoding'),

            // string filters
            new CreasyncSimpleFilter('title', 'twig_title_string_filter', array('needs_environment' => true)),
            new CreasyncSimpleFilter('capitalize', 'twig_capitalize_string_filter', array('needs_environment' => true)),
            new CreasyncSimpleFilter('upper', 'strtoupper'),
            new CreasyncSimpleFilter('lower', 'strtolower'),
            new CreasyncSimpleFilter('striptags', 'strip_tags'),
            new CreasyncSimpleFilter('trim', 'trim'),
            new CreasyncSimpleFilter('nl2br', 'nl2br', array('pre_escape' => 'html', 'is_safe' => array('html'))),

            // array helpers
            new CreasyncSimpleFilter('join', 'twig_join_filter'),
            new CreasyncSimpleFilter('split', 'twig_split_filter'),
            new CreasyncSimpleFilter('sort', 'twig_sort_filter'),
            new CreasyncSimpleFilter('merge', 'twig_array_merge'),
            new CreasyncSimpleFilter('batch', 'twig_array_batch'),

            // string/array filters
            new CreasyncSimpleFilter('reverse', 'twig_reverse_filter', array('needs_environment' => true)),
            new CreasyncSimpleFilter('length', 'twig_length_filter', array('needs_environment' => true)),
            new CreasyncSimpleFilter('slice', 'twig_slice', array('needs_environment' => true)),
            new CreasyncSimpleFilter('first', 'twig_first', array('needs_environment' => true)),
            new CreasyncSimpleFilter('last', 'twig_last', array('needs_environment' => true)),

            // iteration and runtime
            new CreasyncSimpleFilter('default', '_twig_default_filter', array('node_class' => 'CreasyncNode_Expression_Filter_Default')),
            new CreasyncSimpleFilter('keys', 'twig_get_array_keys_filter'),

            // escaping
            new CreasyncSimpleFilter('escape', 'twig_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe')),
            new CreasyncSimpleFilter('e', 'twig_escape_filter', array('needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe')),
        );

        if (function_exists('mb_get_info')) {
            $filters[] = new CreasyncSimpleFilter('upper', 'twig_upper_filter', array('needs_environment' => true));
            $filters[] = new CreasyncSimpleFilter('lower', 'twig_lower_filter', array('needs_environment' => true));
        }

        return $filters;
    }

    /**
     * Returns a list of global functions to add to the existing list.
     *
     * @return array An array of global functions
     */
    public function getFunctions()
    {
        return array(
            new CreasyncSimpleFunction('range', 'range'),
            new CreasyncSimpleFunction('constant', 'twig_constant'),
            new CreasyncSimpleFunction('cycle', 'twig_cycle'),
            new CreasyncSimpleFunction('random', 'twig_random', array('needs_environment' => true)),
            new CreasyncSimpleFunction('date', 'twig_date_converter', array('needs_environment' => true)),
            new CreasyncSimpleFunction('include', 'twig_include', array('needs_environment' => true, 'needs_context' => true)),
        );
    }

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return array An array of tests
     */
    public function getTests()
    {
        return array(
            new CreasyncSimpleTest('even', null, array('node_class' => 'CreasyncNode_Expression_Test_Even')),
            new CreasyncSimpleTest('odd', null, array('node_class' => 'CreasyncNode_Expression_Test_Odd')),
            new CreasyncSimpleTest('defined', null, array('node_class' => 'CreasyncNode_Expression_Test_Defined')),
            new CreasyncSimpleTest('sameas', null, array('node_class' => 'CreasyncNode_Expression_Test_Sameas')),
            new CreasyncSimpleTest('none', null, array('node_class' => 'CreasyncNode_Expression_Test_Null')),
            new CreasyncSimpleTest('null', null, array('node_class' => 'CreasyncNode_Expression_Test_Null')),
            new CreasyncSimpleTest('divisibleby', null, array('node_class' => 'CreasyncNode_Expression_Test_Divisibleby')),
            new CreasyncSimpleTest('constant', null, array('node_class' => 'CreasyncNode_Expression_Test_Constant')),
            new CreasyncSimpleTest('empty', 'twig_test_empty'),
            new CreasyncSimpleTest('iterable', 'twig_test_iterable'),
        );
    }

    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators()
    {
        return array(
            array(
                'not' => array('precedence' => 50, 'class' => 'CreasyncNode_Expression_Unary_Not'),
                '-'   => array('precedence' => 500, 'class' => 'CreasyncNode_Expression_Unary_Neg'),
                '+'   => array('precedence' => 500, 'class' => 'CreasyncNode_Expression_Unary_Pos'),
            ),
            array(
                'or'     => array('precedence' => 10, 'class' => 'CreasyncNode_Expression_Binary_Or', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'and'    => array('precedence' => 15, 'class' => 'CreasyncNode_Expression_Binary_And', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'b-or'   => array('precedence' => 16, 'class' => 'CreasyncNode_Expression_Binary_BitwiseOr', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'b-xor'  => array('precedence' => 17, 'class' => 'CreasyncNode_Expression_Binary_BitwiseXor', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'b-and'  => array('precedence' => 18, 'class' => 'CreasyncNode_Expression_Binary_BitwiseAnd', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '=='     => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_Equal', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '!='     => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_NotEqual', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '<'      => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_Less', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '>'      => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_Greater', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '>='     => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_GreaterEqual', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '<='     => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_LessEqual', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'not in' => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_NotIn', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'in'     => array('precedence' => 20, 'class' => 'CreasyncNode_Expression_Binary_In', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '..'     => array('precedence' => 25, 'class' => 'CreasyncNode_Expression_Binary_Range', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '+'      => array('precedence' => 30, 'class' => 'CreasyncNode_Expression_Binary_Add', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '-'      => array('precedence' => 30, 'class' => 'CreasyncNode_Expression_Binary_Sub', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '~'      => array('precedence' => 40, 'class' => 'CreasyncNode_Expression_Binary_Concat', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '*'      => array('precedence' => 60, 'class' => 'CreasyncNode_Expression_Binary_Mul', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '/'      => array('precedence' => 60, 'class' => 'CreasyncNode_Expression_Binary_Div', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '//'     => array('precedence' => 60, 'class' => 'CreasyncNode_Expression_Binary_FloorDiv', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '%'      => array('precedence' => 60, 'class' => 'CreasyncNode_Expression_Binary_Mod', 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'is'     => array('precedence' => 100, 'callable' => array($this, 'parseTestExpression'), 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                'is not' => array('precedence' => 100, 'callable' => array($this, 'parseNotTestExpression'), 'associativity' => CreasyncExpressionParser::OPERATOR_LEFT),
                '**'     => array('precedence' => 200, 'class' => 'CreasyncNode_Expression_Binary_Power', 'associativity' => CreasyncExpressionParser::OPERATOR_RIGHT),
            ),
        );
    }

    public function parseNotTestExpression(CreasyncParser $parser, $node)
    {
        return new CreasyncNode_Expression_Unary_Not($this->parseTestExpression($parser, $node), $parser->getCurrentToken()->getLine());
    }

    public function parseTestExpression(CreasyncParser $parser, $node)
    {
        $stream = $parser->getStream();
        $name = $stream->expect(CreasyncToken::NAME_TYPE)->getValue();
        $arguments = null;
        if ($stream->test(CreasyncToken::PUNCTUATION_TYPE, '(')) {
            $arguments = $parser->getExpressionParser()->parseArguments(true);
        }

        $class = $this->getTestNodeClass($parser, $name, $node->getLine());

        return new $class($node, $name, $arguments, $parser->getCurrentToken()->getLine());
    }

    protected function getTestNodeClass(CreasyncParser $parser, $name, $line)
    {
        $env = $parser->getEnvironment();
        $testMap = $env->getTests();
        if (!isset($testMap[$name])) {
            $message = sprintf('The test "%s" does not exist', $name);
            if ($alternatives = $env->computeAlternatives($name, array_keys($env->getTests()))) {
                $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
            }

            throw new CreasyncError_Syntax($message, $line, $parser->getFilename());
        }

        if ($testMap[$name] instanceof CreasyncSimpleTest) {
            return $testMap[$name]->getNodeClass();
        }

        return $testMap[$name] instanceof CreasyncTest_Node ? $testMap[$name]->getClass() : 'CreasyncNode_Expression_Test';
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'core';
    }
}

/**
 * Cycles over a value.
 *
 * @param ArrayAccess|array $values   An array or an ArrayAccess instance
 * @param integer           $position The cycle position
 *
 * @return string The next value in the cycle
 */
function twig_cycle($values, $position)
{
    if (!is_array($values) && !$values instanceof ArrayAccess) {
        return $values;
    }

    return $values[$position % count($values)];
}

/**
 * Returns a random value depending on the supplied parameter type:
 * - a random item from a Traversable or array
 * - a random character from a string
 * - a random integer between 0 and the integer parameter
 *
 * @param CreasyncEnvironment                 $env    A CreasyncEnvironment instance
 * @param Traversable|array|integer|string $values The values to pick a random item from
 *
 * @throws CreasyncError_Runtime When $values is an empty array (does not apply to an empty string which is returned as is).
 *
 * @return mixed A random value from the given sequence
 */
function twig_random(CreasyncEnvironment $env, $values = null)
{
    if (null === $values) {
        return mt_rand();
    }

    if (is_int($values) || is_float($values)) {
        return $values < 0 ? mt_rand($values, 0) : mt_rand(0, $values);
    }

    if ($values instanceof Traversable) {
        $values = iterator_to_array($values);
    } elseif (is_string($values)) {
        if ('' === $values) {
            return '';
        }
        if (null !== $charset = $env->getCharset()) {
            if ('UTF-8' != $charset) {
                $values = twig_convert_encoding($values, 'UTF-8', $charset);
            }

            // unicode version of str_split()
            // split at all positions, but not after the start and not before the end
            $values = preg_split('/(?<!^)(?!$)/u', $values);

            if ('UTF-8' != $charset) {
                foreach ($values as $i => $value) {
                    $values[$i] = twig_convert_encoding($value, $charset, 'UTF-8');
                }
            }
        } else {
            return $values[mt_rand(0, strlen($values) - 1)];
        }
    }

    if (!is_array($values)) {
        return $values;
    }

    if (0 === count($values)) {
        throw new CreasyncError_Runtime('The random function cannot pick from an empty array.');
    }

    return $values[array_rand($values, 1)];
}

/**
 * Converts a date to the given format.
 *
 * <pre>
 *   {{ post.published_at|date("m/d/Y") }}
 * </pre>
 *
 * @param CreasyncEnvironment             $env      A CreasyncEnvironment instance
 * @param DateTime|DateInterval|string $date     A date
 * @param string                       $format   A format
 * @param DateTimeZone|string          $timezone A timezone
 *
 * @return string The formatted date
 */
function twig_date_format_filter(CreasyncEnvironment $env, $date, $format = null, $timezone = null)
{
    if (null === $format) {
        $formats = $env->getExtension('core')->getDateFormat();
        $format = $date instanceof DateInterval ? $formats[1] : $formats[0];
    }

    if ($date instanceof DateInterval) {
        return $date->format($format);
    }

    return twig_date_converter($env, $date, $timezone)->format($format);
}

/**
 * Returns a new date object modified
 *
 * <pre>
 *   {{ post.published_at|date_modify("-1day")|date("m/d/Y") }}
 * </pre>
 *
 * @param CreasyncEnvironment  $env      A CreasyncEnvironment instance
 * @param DateTime|string   $date     A date
 * @param string            $modifier A modifier string
 *
 * @return DateTime A new date object
 */
function twig_date_modify_filter(CreasyncEnvironment $env, $date, $modifier)
{
    $date = twig_date_converter($env, $date, false);
    $date->modify($modifier);

    return $date;
}

/**
 * Converts an input to a DateTime instance.
 *
 * <pre>
 *    {% if date(user.created_at) < date('+2days') %}
 *      {# do something #}
 *    {% endif %}
 * </pre>
 *
 * @param CreasyncEnvironment    $env      A CreasyncEnvironment instance
 * @param DateTime|string     $date     A date
 * @param DateTimeZone|string $timezone A timezone
 *
 * @return DateTime A DateTime instance
 */
function twig_date_converter(CreasyncEnvironment $env, $date = null, $timezone = null)
{
    // determine the timezone
    if (!$timezone) {
        $defaultTimezone = $env->getExtension('core')->getTimezone();
    } elseif (!$timezone instanceof DateTimeZone) {
        $defaultTimezone = new DateTimeZone($timezone);
    } else {
        $defaultTimezone = $timezone;
    }

    if ($date instanceof DateTime) {
        $date = clone $date;
        if (false !== $timezone) {
            $date->setTimezone($defaultTimezone);
        }

        return $date;
    }

    $asString = (string) $date;
    if (ctype_digit($asString) || (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
        $date = '@'.$date;
    }

    $date = new DateTime($date, $defaultTimezone);
    if (false !== $timezone) {
        $date->setTimezone($defaultTimezone);
    }

    return $date;
}

/**
 * Number format filter.
 *
 * All of the formatting options can be left null, in that case the defaults will
 * be used.  Supplying any of the parameters will override the defaults set in the
 * environment object.
 *
 * @param CreasyncEnvironment    $env          A CreasyncEnvironment instance
 * @param mixed               $number       A float/int/string of the number to format
 * @param integer             $decimal      The number of decimal points to display.
 * @param string              $decimalPoint The character(s) to use for the decimal point.
 * @param string              $thousandSep  The character(s) to use for the thousands separator.
 *
 * @return string The formatted number
 */
function twig_number_format_filter(CreasyncEnvironment $env, $number, $decimal = null, $decimalPoint = null, $thousandSep = null)
{
    $defaults = $env->getExtension('core')->getNumberFormat();
    if (null === $decimal) {
        $decimal = $defaults[0];
    }

    if (null === $decimalPoint) {
        $decimalPoint = $defaults[1];
    }

    if (null === $thousandSep) {
        $thousandSep = $defaults[2];
    }

    return number_format((float) $number, $decimal, $decimalPoint, $thousandSep);
}

/**
 * URL encodes a string as a path segment or an array as a query string.
 *
 * @param string|array $url A URL or an array of query parameters
 * @param bool         $raw true to use rawurlencode() instead of urlencode
 *
 * @return string The URL encoded value
 */
function twig_urlencode_filter($url, $raw = false)
{
    if (is_array($url)) {
        return http_build_query($url, '', '&');
    }

    if ($raw) {
        return rawurlencode($url);
    }

    return urlencode($url);
}

if (version_compare(PHP_VERSION, '5.3.0', '<')) {
    /**
     * JSON encodes a variable.
     *
     * @param mixed   $value   The value to encode.
     * @param integer $options Not used on PHP 5.2.x
     *
     * @return mixed The JSON encoded value
     */
    function twig_jsonencode_filter($value, $options = 0)
    {
        if ($value instanceof CreasyncMarkup) {
            $value = (string) $value;
        } elseif (is_array($value)) {
            array_walk_recursive($value, '_twig_markup2string');
        }

        return json_encode($value);
    }
} else {
    /**
     * JSON encodes a variable.
     *
     * @param mixed   $value   The value to encode.
     * @param integer $options Bitmask consisting of JSON_HEX_QUOT, JSON_HEX_TAG, JSON_HEX_AMP, JSON_HEX_APOS, JSON_NUMERIC_CHECK, JSON_PRETTY_PRINT, JSON_UNESCAPED_SLASHES, JSON_FORCE_OBJECT
     *
     * @return mixed The JSON encoded value
     */
    function twig_jsonencode_filter($value, $options = 0)
    {
        if ($value instanceof CreasyncMarkup) {
            $value = (string) $value;
        } elseif (is_array($value)) {
            array_walk_recursive($value, '_twig_markup2string');
        }

        return json_encode($value, $options);
    }
}

function _twig_markup2string(&$value)
{
    if ($value instanceof CreasyncMarkup) {
        $value = (string) $value;
    }
}

/**
 * Merges an array with another one.
 *
 * <pre>
 *  {% set items = { 'apple': 'fruit', 'orange': 'fruit' } %}
 *
 *  {% set items = items|merge({ 'peugeot': 'car' }) %}
 *
 *  {# items now contains { 'apple': 'fruit', 'orange': 'fruit', 'peugeot': 'car' } #}
 * </pre>
 *
 * @param array $arr1 An array
 * @param array $arr2 An array
 *
 * @return array The merged array
 */
function twig_array_merge($arr1, $arr2)
{
    if (!is_array($arr1) || !is_array($arr2)) {
        throw new CreasyncError_Runtime('The merge filter only works with arrays or hashes.');
    }

    return array_merge($arr1, $arr2);
}

/**
 * Slices a variable.
 *
 * @param CreasyncEnvironment $env          A CreasyncEnvironment instance
 * @param mixed            $item         A variable
 * @param integer          $start        Start of the slice
 * @param integer          $length       Size of the slice
 * @param Boolean          $preserveKeys Whether to preserve key or not (when the input is an array)
 *
 * @return mixed The sliced variable
 */
function twig_slice(CreasyncEnvironment $env, $item, $start, $length = null, $preserveKeys = false)
{
    if ($item instanceof Traversable) {
        $item = iterator_to_array($item, false);
    }

    if (is_array($item)) {
        return array_slice($item, $start, $length, $preserveKeys);
    }

    $item = (string) $item;

    if (function_exists('mb_get_info') && null !== $charset = $env->getCharset()) {
        return mb_substr($item, $start, null === $length ? mb_strlen($item, $charset) - $start : $length, $charset);
    }

    return null === $length ? substr($item, $start) : substr($item, $start, $length);
}

/**
 * Returns the first element of the item.
 *
 * @param CreasyncEnvironment $env  A CreasyncEnvironment instance
 * @param mixed            $item A variable
 *
 * @return mixed The first element of the item
 */
function twig_first(CreasyncEnvironment $env, $item)
{
    $elements = twig_slice($env, $item, 0, 1, false);

    return is_string($elements) ? $elements[0] : current($elements);
}

/**
 * Returns the last element of the item.
 *
 * @param CreasyncEnvironment $env  A CreasyncEnvironment instance
 * @param mixed            $item A variable
 *
 * @return mixed The last element of the item
 */
function twig_last(CreasyncEnvironment $env, $item)
{
    $elements = twig_slice($env, $item, -1, 1, false);

    return is_string($elements) ? $elements[0] : current($elements);
}

/**
 * Joins the values to a string.
 *
 * The separator between elements is an empty string per default, you can define it with the optional parameter.
 *
 * <pre>
 *  {{ [1, 2, 3]|join('|') }}
 *  {# returns 1|2|3 #}
 *
 *  {{ [1, 2, 3]|join }}
 *  {# returns 123 #}
 * </pre>
 *
 * @param array  $value An array
 * @param string $glue  The separator
 *
 * @return string The concatenated string
 */
function twig_join_filter($value, $glue = '')
{
    if ($value instanceof Traversable) {
        $value = iterator_to_array($value, false);
    }

    return implode($glue, (array) $value);
}

/**
 * Splits the string into an array.
 *
 * <pre>
 *  {{ "one,two,three"|split(',') }}
 *  {# returns [one, two, three] #}
 *
 *  {{ "one,two,three,four,five"|split(',', 3) }}
 *  {# returns [one, two, "three,four,five"] #}
 *
 *  {{ "123"|split('') }}
 *  {# returns [1, 2, 3] #}
 *
 *  {{ "aabbcc"|split('', 2) }}
 *  {# returns [aa, bb, cc] #}
 * </pre>
 *
 * @param string  $value     A string
 * @param string  $delimiter The delimiter
 * @param integer $limit     The limit
 *
 * @return array The split string as an array
 */
function twig_split_filter($value, $delimiter, $limit = null)
{
    if (empty($delimiter)) {
        return str_split($value, null === $limit ? 1 : $limit);
    }

    return null === $limit ? explode($delimiter, $value) : explode($delimiter, $value, $limit);
}

// The '_default' filter is used internally to avoid using the ternary operator
// which costs a lot for big contexts (before PHP 5.4). So, on average,
// a function call is cheaper.
function _twig_default_filter($value, $default = '')
{
    if (twig_test_empty($value)) {
        return $default;
    }

    return $value;
}

/**
 * Returns the keys for the given array.
 *
 * It is useful when you want to iterate over the keys of an array:
 *
 * <pre>
 *  {% for key in array|keys %}
 *      {# ... #}
 *  {% endfor %}
 * </pre>
 *
 * @param array $array An array
 *
 * @return array The keys
 */
function twig_get_array_keys_filter($array)
{
    if (is_object($array) && $array instanceof Traversable) {
        return array_keys(iterator_to_array($array));
    }

    if (!is_array($array)) {
        return array();
    }

    return array_keys($array);
}

/**
 * Reverses a variable.
 *
 * @param CreasyncEnvironment         $env          A CreasyncEnvironment instance
 * @param array|Traversable|string $item         An array, a Traversable instance, or a string
 * @param Boolean                  $preserveKeys Whether to preserve key or not
 *
 * @return mixed The reversed input
 */
function twig_reverse_filter(CreasyncEnvironment $env, $item, $preserveKeys = false)
{
    if (is_object($item) && $item instanceof Traversable) {
        return array_reverse(iterator_to_array($item), $preserveKeys);
    }

    if (is_array($item)) {
        return array_reverse($item, $preserveKeys);
    }

    if (null !== $charset = $env->getCharset()) {
        $string = (string) $item;

        if ('UTF-8' != $charset) {
            $item = twig_convert_encoding($string, 'UTF-8', $charset);
        }

        preg_match_all('/./us', $item, $matches);

        $string = implode('', array_reverse($matches[0]));

        if ('UTF-8' != $charset) {
            $string = twig_convert_encoding($string, $charset, 'UTF-8');
        }

        return $string;
    }

    return strrev((string) $item);
}

/**
 * Sorts an array.
 *
 * @param array $array An array
 */
function twig_sort_filter($array)
{
    asort($array);

    return $array;
}

/* used internally */
function twig_in_filter($value, $compare)
{
    if (is_array($compare)) {
        return in_array($value, $compare, is_object($value));
    } elseif (is_string($compare)) {
        if (!strlen($value)) {
            return empty($compare);
        }

        return false !== strpos($compare, (string) $value);
    } elseif ($compare instanceof Traversable) {
        return in_array($value, iterator_to_array($compare, false), is_object($value));
    }

    return false;
}

/**
 * Escapes a string.
 *
 * @param CreasyncEnvironment $env        A CreasyncEnvironment instance
 * @param string           $string     The value to be escaped
 * @param string           $strategy   The escaping strategy
 * @param string           $charset    The charset
 * @param Boolean          $autoescape Whether the function is called by the auto-escaping feature (true) or by the developer (false)
 */
function twig_escape_filter(CreasyncEnvironment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
{
    if ($autoescape && is_object($string) && $string instanceof CreasyncMarkup) {
        return $string;
    }

    if (!is_string($string) && !(is_object($string) && method_exists($string, '__toString'))) {
        return $string;
    }

    if (null === $charset) {
        $charset = $env->getCharset();
    }

    $string = (string) $string;

    switch ($strategy) {
        case 'js':
            // escape all non-alphanumeric characters
            // into their \xHH or \uHHHH representations
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new CreasyncError_Runtime('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9,\._]#Su', '_twig_escape_js_callback', $string);

            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }

            return $string;

        case 'css':
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new CreasyncError_Runtime('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9]#Su', '_twig_escape_css_callback', $string);

            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }

            return $string;

        case 'html_attr':
            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, 'UTF-8', $charset);
            }

            if (0 == strlen($string) ? false : (1 == preg_match('/^./su', $string) ? false : true)) {
                throw new CreasyncError_Runtime('The string to escape is not a valid UTF-8 string.');
            }

            $string = preg_replace_callback('#[^a-zA-Z0-9,\.\-_]#Su', '_twig_escape_html_attr_callback', $string);

            if ('UTF-8' != $charset) {
                $string = twig_convert_encoding($string, $charset, 'UTF-8');
            }

            return $string;

        case 'html':
            // see http://php.net/htmlspecialchars

            // Using a static variable to avoid initializing the array
            // each time the function is called. Moving the declaration on the
            // top of the function slow downs other escaping strategies.
            static $htmlspecialcharsCharsets = array(
                'iso-8859-1' => true, 'iso8859-1' => true,
                'iso-8859-15' => true, 'iso8859-15' => true,
                'utf-8' => true,
                'cp866' => true, 'ibm866' => true, '866' => true,
                'cp1251' => true, 'windows-1251' => true, 'win-1251' => true,
                '1251' => true,
                'cp1252' => true, 'windows-1252' => true, '1252' => true,
                'koi8-r' => true, 'koi8-ru' => true, 'koi8r' => true,
                'big5' => true, '950' => true,
                'gb2312' => true, '936' => true,
                'big5-hkscs' => true,
                'shift_jis' => true, 'sjis' => true, '932' => true,
                'euc-jp' => true, 'eucjp' => true,
                'iso8859-5' => true, 'iso-8859-5' => true, 'macroman' => true,
            );

            if (isset($htmlspecialcharsCharsets[strtolower($charset)])) {
                return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
            }

            $string = twig_convert_encoding($string, 'UTF-8', $charset);
            $string = htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

            return twig_convert_encoding($string, $charset, 'UTF-8');

        case 'url':
            if (version_compare(PHP_VERSION, '5.3.0', '<')) {
                return str_replace('%7E', '~', rawurlencode($string));
            }

            return rawurlencode($string);

        default:
            throw new CreasyncError_Runtime(sprintf('Invalid escaping strategy "%s" (valid ones: html, js, url, css, and html_attr).', $strategy));
    }
}

/* used internally */
function twig_escape_filter_is_safe(CreasyncNode $filterArgs)
{
    foreach ($filterArgs as $arg) {
        if ($arg instanceof CreasyncNode_Expression_Constant) {
            return array($arg->getAttribute('value'));
        }

        return array();
    }

    return array('html');
}

if (function_exists('mb_convert_encoding')) {
    function twig_convert_encoding($string, $to, $from)
    {
        return mb_convert_encoding($string, $to, $from);
    }
} elseif (function_exists('iconv')) {
    function twig_convert_encoding($string, $to, $from)
    {
        return iconv($from, $to, $string);
    }
} else {
    function twig_convert_encoding($string, $to, $from)
    {
        throw new CreasyncError_Runtime('No suitable convert encoding function (use UTF-8 as your encoding or install the iconv or mbstring extension).');
    }
}

function _twig_escape_js_callback($matches)
{
    $char = $matches[0];

    // \xHH
    if (!isset($char[1])) {
        return '\\x'.strtoupper(substr('00'.bin2hex($char), -2));
    }

    // \uHHHH
    $char = twig_convert_encoding($char, 'UTF-16BE', 'UTF-8');

    return '\\u'.strtoupper(substr('0000'.bin2hex($char), -4));
}

function _twig_escape_css_callback($matches)
{
    $char = $matches[0];

    // \xHH
    if (!isset($char[1])) {
        $hex = ltrim(strtoupper(bin2hex($char)), '0');
        if (0 === strlen($hex)) {
            $hex = '0';
        }

        return '\\'.$hex.' ';
    }

    // \uHHHH
    $char = twig_convert_encoding($char, 'UTF-16BE', 'UTF-8');

    return '\\'.ltrim(strtoupper(bin2hex($char)), '0').' ';
}

/**
 * This function is adapted from code coming from Zend Framework.
 *
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
function _twig_escape_html_attr_callback($matches)
{
    /*
     * While HTML supports far more named entities, the lowest common denominator
     * has become HTML5's XML Serialisation which is restricted to the those named
     * entities that XML supports. Using HTML entities would result in this error:
     *     XML Parsing Error: undefined entity
     */
    static $entityMap = array(
        34 => 'quot', /* quotation mark */
        38 => 'amp',  /* ampersand */
        60 => 'lt',   /* less-than sign */
        62 => 'gt',   /* greater-than sign */
    );

    $chr = $matches[0];
    $ord = ord($chr);

    /**
     * The following replaces characters undefined in HTML with the
     * hex entity for the Unicode replacement character.
     */
    if (($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r") || ($ord >= 0x7f && $ord <= 0x9f)) {
        return '&#xFFFD;';
    }

    /**
     * Check if the current character to escape has a name entity we should
     * replace it with while grabbing the hex value of the character.
     */
    if (strlen($chr) == 1) {
        $hex = strtoupper(substr('00'.bin2hex($chr), -2));
    } else {
        $chr = twig_convert_encoding($chr, 'UTF-16BE', 'UTF-8');
        $hex = strtoupper(substr('0000'.bin2hex($chr), -4));
    }

    $int = hexdec($hex);
    if (array_key_exists($int, $entityMap)) {
        return sprintf('&%s;', $entityMap[$int]);
    }

    /**
     * Per OWASP recommendations, we'll use hex entities for any other
     * characters where a named entity does not exist.
     */

    return sprintf('&#x%s;', $hex);
}

// add multibyte extensions if possible
if (function_exists('mb_get_info')) {
    /**
     * Returns the length of a variable.
     *
     * @param CreasyncEnvironment $env   A CreasyncEnvironment instance
     * @param mixed            $thing A variable
     *
     * @return integer The length of the value
     */
    function twig_length_filter(CreasyncEnvironment $env, $thing)
    {
        return is_scalar($thing) ? mb_strlen($thing, $env->getCharset()) : count($thing);
    }

    /**
     * Converts a string to uppercase.
     *
     * @param CreasyncEnvironment $env    A CreasyncEnvironment instance
     * @param string           $string A string
     *
     * @return string The uppercased string
     */
    function twig_upper_filter(CreasyncEnvironment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtoupper($string, $charset);
        }

        return strtoupper($string);
    }

    /**
     * Converts a string to lowercase.
     *
     * @param CreasyncEnvironment $env    A CreasyncEnvironment instance
     * @param string           $string A string
     *
     * @return string The lowercased string
     */
    function twig_lower_filter(CreasyncEnvironment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtolower($string, $charset);
        }

        return strtolower($string);
    }

    /**
     * Returns a titlecased string.
     *
     * @param CreasyncEnvironment $env    A CreasyncEnvironment instance
     * @param string           $string A string
     *
     * @return string The titlecased string
     */
    function twig_title_string_filter(CreasyncEnvironment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_convert_case($string, MB_CASE_TITLE, $charset);
        }

        return ucwords(strtolower($string));
    }

    /**
     * Returns a capitalized string.
     *
     * @param CreasyncEnvironment $env    A CreasyncEnvironment instance
     * @param string           $string A string
     *
     * @return string The capitalized string
     */
    function twig_capitalize_string_filter(CreasyncEnvironment $env, $string)
    {
        if (null !== ($charset = $env->getCharset())) {
            return mb_strtoupper(mb_substr($string, 0, 1, $charset), $charset).
                         mb_strtolower(mb_substr($string, 1, mb_strlen($string, $charset), $charset), $charset);
        }

        return ucfirst(strtolower($string));
    }
}
// and byte fallback
else {
    /**
     * Returns the length of a variable.
     *
     * @param CreasyncEnvironment $env   A CreasyncEnvironment instance
     * @param mixed            $thing A variable
     *
     * @return integer The length of the value
     */
    function twig_length_filter(CreasyncEnvironment $env, $thing)
    {
        return is_scalar($thing) ? strlen($thing) : count($thing);
    }

    /**
     * Returns a titlecased string.
     *
     * @param CreasyncEnvironment $env    A CreasyncEnvironment instance
     * @param string           $string A string
     *
     * @return string The titlecased string
     */
    function twig_title_string_filter(CreasyncEnvironment $env, $string)
    {
        return ucwords(strtolower($string));
    }

    /**
     * Returns a capitalized string.
     *
     * @param CreasyncEnvironment $env    A CreasyncEnvironment instance
     * @param string           $string A string
     *
     * @return string The capitalized string
     */
    function twig_capitalize_string_filter(CreasyncEnvironment $env, $string)
    {
        return ucfirst(strtolower($string));
    }
}

/* used internally */
function twig_ensure_traversable($seq)
{
    if ($seq instanceof Traversable || is_array($seq)) {
        return $seq;
    }

    return array();
}

/**
 * Checks if a variable is empty.
 *
 * <pre>
 * {# evaluates to true if the foo variable is null, false, or the empty string #}
 * {% if foo is empty %}
 *     {# ... #}
 * {% endif %}
 * </pre>
 *
 * @param mixed $value A variable
 *
 * @return Boolean true if the value is empty, false otherwise
 */
function twig_test_empty($value)
{
    if ($value instanceof Countable) {
        return 0 == count($value);
    }

    return '' === $value || false === $value || null === $value || array() === $value;
}

/**
 * Checks if a variable is traversable.
 *
 * <pre>
 * {# evaluates to true if the foo variable is an array or a traversable object #}
 * {% if foo is traversable %}
 *     {# ... #}
 * {% endif %}
 * </pre>
 *
 * @param mixed $value A variable
 *
 * @return Boolean true if the value is traversable
 */
function twig_test_iterable($value)
{
    return $value instanceof Traversable || is_array($value);
}

/**
 * Renders a template.
 *
 * @param string  template       The template to render
 * @param array   variables      The variables to pass to the template
 * @param Boolean with_context   Whether to pass the current context variables or not
 * @param Boolean ignore_missing Whether to ignore missing templates or not
 * @param Boolean sandboxed      Whether to sandbox the template or not
 *
 * @return string The rendered template
 */
function twig_include(CreasyncEnvironment $env, $context, $template, $variables = array(), $withContext = true, $ignoreMissing = false, $sandboxed = false)
{
    if ($withContext) {
        $variables = array_merge($context, $variables);
    }

    if ($isSandboxed = $sandboxed && $env->hasExtension('sandbox')) {
        $sandbox = $env->getExtension('sandbox');
        if (!$alreadySandboxed = $sandbox->isSandboxed()) {
            $sandbox->enableSandbox();
        }
    }

    try {
        return $env->resolveTemplate($template)->display($variables);
    } catch (CreasyncError_Loader $e) {
        if (!$ignoreMissing) {
            throw $e;
        }
    }

    if ($isSandboxed && !$alreadySandboxed) {
        $sandbox->disableSandbox();
    }
}

/**
 * Provides the ability to get constants from instances as well as class/global constants.
 *
 * @param string      $constant The name of the constant
 * @param null|object $object   The object to get the constant from
 *
 * @return string
 */
function twig_constant($constant, $object = null)
{
    if (null !== $object) {
        $constant = get_class($object).'::'.$constant;
    }

    return constant($constant);
}

/**
 * Batches item.
 *
 * @param array   $items An array of items
 * @param integer $size  The size of the batch
 * @param string  $fill  A string to fill missing items
 *
 * @return array
 */
function twig_array_batch($items, $size, $fill = null)
{
    if ($items instanceof Traversable) {
        $items = iterator_to_array($items, false);
    }

    $size = ceil($size);

    $result = array_chunk($items, $size, true);

    if (null !== $fill) {
        $last = count($result) - 1;
        $result[$last] = array_merge(
            $result[$last],
            array_fill(0, $size - count($result[$last]), $fill)
        );
    }

    return $result;
}
