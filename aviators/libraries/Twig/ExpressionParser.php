<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Parses expressions.
 *
 * This parser implements a "Precedence climbing" algorithm.
 *
 * @see http://www.engr.mun.ca/~theo/Misc/exp_parsing.htm
 * @see http://en.wikipedia.org/wiki/Operator-precedence_parser
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncExpressionParser
{
    const OPERATOR_LEFT = 1;
    const OPERATOR_RIGHT = 2;

    protected $parser;
    protected $unaryOperators;
    protected $binaryOperators;

    public function __construct(CreasyncParser $parser, array $unaryOperators, array $binaryOperators)
    {
        $this->parser = $parser;
        $this->unaryOperators = $unaryOperators;
        $this->binaryOperators = $binaryOperators;
    }

    public function parseExpression($precedence = 0)
    {
        $expr = $this->getPrimary();
        $token = $this->parser->getCurrentToken();
        while ($this->isBinary($token) && $this->binaryOperators[$token->getValue()]['precedence'] >= $precedence) {
            $op = $this->binaryOperators[$token->getValue()];
            $this->parser->getStream()->next();

            if (isset($op['callable'])) {
                $expr = call_user_func($op['callable'], $this->parser, $expr);
            } else {
                $expr1 = $this->parseExpression(self::OPERATOR_LEFT === $op['associativity'] ? $op['precedence'] + 1 : $op['precedence']);
                $class = $op['class'];
                $expr = new $class($expr, $expr1, $token->getLine());
            }

            $token = $this->parser->getCurrentToken();
        }

        if (0 === $precedence) {
            return $this->parseConditionalExpression($expr);
        }

        return $expr;
    }

    protected function getPrimary()
    {
        $token = $this->parser->getCurrentToken();

        if ($this->isUnary($token)) {
            $operator = $this->unaryOperators[$token->getValue()];
            $this->parser->getStream()->next();
            $expr = $this->parseExpression($operator['precedence']);
            $class = $operator['class'];

            return $this->parsePostfixExpression(new $class($expr, $token->getLine()));
        } elseif ($token->test(CreasyncToken::PUNCTUATION_TYPE, '(')) {
            $this->parser->getStream()->next();
            $expr = $this->parseExpression();
            $this->parser->getStream()->expect(CreasyncToken::PUNCTUATION_TYPE, ')', 'An opened parenthesis is not properly closed');

            return $this->parsePostfixExpression($expr);
        }

        return $this->parsePrimaryExpression();
    }

    protected function parseConditionalExpression($expr)
    {
        while ($this->parser->getStream()->test(CreasyncToken::PUNCTUATION_TYPE, '?')) {
            $this->parser->getStream()->next();
            if (!$this->parser->getStream()->test(CreasyncToken::PUNCTUATION_TYPE, ':')) {
                $expr2 = $this->parseExpression();
                if ($this->parser->getStream()->test(CreasyncToken::PUNCTUATION_TYPE, ':')) {
                    $this->parser->getStream()->next();
                    $expr3 = $this->parseExpression();
                } else {
                    $expr3 = new CreasyncNode_Expression_Constant('', $this->parser->getCurrentToken()->getLine());
                }
            } else {
                $this->parser->getStream()->next();
                $expr2 = $expr;
                $expr3 = $this->parseExpression();
            }

            $expr = new CreasyncNode_Expression_Conditional($expr, $expr2, $expr3, $this->parser->getCurrentToken()->getLine());
        }

        return $expr;
    }

    protected function isUnary(CreasyncToken $token)
    {
        return $token->test(CreasyncToken::OPERATOR_TYPE) && isset($this->unaryOperators[$token->getValue()]);
    }

    protected function isBinary(CreasyncToken $token)
    {
        return $token->test(CreasyncToken::OPERATOR_TYPE) && isset($this->binaryOperators[$token->getValue()]);
    }

    public function parsePrimaryExpression()
    {
        $token = $this->parser->getCurrentToken();
        switch ($token->getType()) {
            case CreasyncToken::NAME_TYPE:
                $this->parser->getStream()->next();
                switch ($token->getValue()) {
                    case 'true':
                    case 'TRUE':
                        $node = new CreasyncNode_Expression_Constant(true, $token->getLine());
                        break;

                    case 'false':
                    case 'FALSE':
                        $node = new CreasyncNode_Expression_Constant(false, $token->getLine());
                        break;

                    case 'none':
                    case 'NONE':
                    case 'null':
                    case 'NULL':
                        $node = new CreasyncNode_Expression_Constant(null, $token->getLine());
                        break;

                    default:
                        if ('(' === $this->parser->getCurrentToken()->getValue()) {
                            $node = $this->getFunctionNode($token->getValue(), $token->getLine());
                        } else {
                            $node = new CreasyncNode_Expression_Name($token->getValue(), $token->getLine());
                        }
                }
                break;

            case CreasyncToken::NUMBER_TYPE:
                $this->parser->getStream()->next();
                $node = new CreasyncNode_Expression_Constant($token->getValue(), $token->getLine());
                break;

            case CreasyncToken::STRING_TYPE:
            case CreasyncToken::INTERPOLATION_START_TYPE:
                $node = $this->parseStringExpression();
                break;

            default:
                if ($token->test(CreasyncToken::PUNCTUATION_TYPE, '[')) {
                    $node = $this->parseArrayExpression();
                } elseif ($token->test(CreasyncToken::PUNCTUATION_TYPE, '{')) {
                    $node = $this->parseHashExpression();
                } else {
                    throw new CreasyncError_Syntax(sprintf('Unexpected token "%s" of value "%s"', CreasyncToken::typeToEnglish($token->getType(), $token->getLine()), $token->getValue()), $token->getLine(), $this->parser->getFilename());
                }
        }

        return $this->parsePostfixExpression($node);
    }

    public function parseStringExpression()
    {
        $stream = $this->parser->getStream();

        $nodes = array();
        // a string cannot be followed by another string in a single expression
        $nextCanBeString = true;
        while (true) {
            if ($stream->test(CreasyncToken::STRING_TYPE) && $nextCanBeString) {
                $token = $stream->next();
                $nodes[] = new CreasyncNode_Expression_Constant($token->getValue(), $token->getLine());
                $nextCanBeString = false;
            } elseif ($stream->test(CreasyncToken::INTERPOLATION_START_TYPE)) {
                $stream->next();
                $nodes[] = $this->parseExpression();
                $stream->expect(CreasyncToken::INTERPOLATION_END_TYPE);
                $nextCanBeString = true;
            } else {
                break;
            }
        }

        $expr = array_shift($nodes);
        foreach ($nodes as $node) {
            $expr = new CreasyncNode_Expression_Binary_Concat($expr, $node, $node->getLine());
        }

        return $expr;
    }

    public function parseArrayExpression()
    {
        $stream = $this->parser->getStream();
        $stream->expect(CreasyncToken::PUNCTUATION_TYPE, '[', 'An array element was expected');

        $node = new CreasyncNode_Expression_Array(array(), $stream->getCurrent()->getLine());
        $first = true;
        while (!$stream->test(CreasyncToken::PUNCTUATION_TYPE, ']')) {
            if (!$first) {
                $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ',', 'An array element must be followed by a comma');

                // trailing ,?
                if ($stream->test(CreasyncToken::PUNCTUATION_TYPE, ']')) {
                    break;
                }
            }
            $first = false;

            $node->addElement($this->parseExpression());
        }
        $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ']', 'An opened array is not properly closed');

        return $node;
    }

    public function parseHashExpression()
    {
        $stream = $this->parser->getStream();
        $stream->expect(CreasyncToken::PUNCTUATION_TYPE, '{', 'A hash element was expected');

        $node = new CreasyncNode_Expression_Array(array(), $stream->getCurrent()->getLine());
        $first = true;
        while (!$stream->test(CreasyncToken::PUNCTUATION_TYPE, '}')) {
            if (!$first) {
                $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ',', 'A hash value must be followed by a comma');

                // trailing ,?
                if ($stream->test(CreasyncToken::PUNCTUATION_TYPE, '}')) {
                    break;
                }
            }
            $first = false;

            // a hash key can be:
            //
            //  * a number -- 12
            //  * a string -- 'a'
            //  * a name, which is equivalent to a string -- a
            //  * an expression, which must be enclosed in parentheses -- (1 + 2)
            if ($stream->test(CreasyncToken::STRING_TYPE) || $stream->test(CreasyncToken::NAME_TYPE) || $stream->test(CreasyncToken::NUMBER_TYPE)) {
                $token = $stream->next();
                $key = new CreasyncNode_Expression_Constant($token->getValue(), $token->getLine());
            } elseif ($stream->test(CreasyncToken::PUNCTUATION_TYPE, '(')) {
                $key = $this->parseExpression();
            } else {
                $current = $stream->getCurrent();

                throw new CreasyncError_Syntax(sprintf('A hash key must be a quoted string, a number, a name, or an expression enclosed in parentheses (unexpected token "%s" of value "%s"', CreasyncToken::typeToEnglish($current->getType(), $current->getLine()), $current->getValue()), $current->getLine(), $this->parser->getFilename());
            }

            $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ':', 'A hash key must be followed by a colon (:)');
            $value = $this->parseExpression();

            $node->addElement($value, $key);
        }
        $stream->expect(CreasyncToken::PUNCTUATION_TYPE, '}', 'An opened hash is not properly closed');

        return $node;
    }

    public function parsePostfixExpression($node)
    {
        while (true) {
            $token = $this->parser->getCurrentToken();
            if ($token->getType() == CreasyncToken::PUNCTUATION_TYPE) {
                if ('.' == $token->getValue() || '[' == $token->getValue()) {
                    $node = $this->parseSubscriptExpression($node);
                } elseif ('|' == $token->getValue()) {
                    $node = $this->parseFilterExpression($node);
                } else {
                    break;
                }
            } else {
                break;
            }
        }

        return $node;
    }

    public function getFunctionNode($name, $line)
    {
        switch ($name) {
            case 'parent':
                $args = $this->parseArguments();
                if (!count($this->parser->getBlockStack())) {
                    throw new CreasyncError_Syntax('Calling "parent" outside a block is forbidden', $line, $this->parser->getFilename());
                }

                if (!$this->parser->getParent() && !$this->parser->hasTraits()) {
                    throw new CreasyncError_Syntax('Calling "parent" on a template that does not extend nor "use" another template is forbidden', $line, $this->parser->getFilename());
                }

                return new CreasyncNode_Expression_Parent($this->parser->peekBlockStack(), $line);
            case 'block':
                return new CreasyncNode_Expression_BlockReference($this->parseArguments()->getNode(0), false, $line);
            case 'attribute':
                $args = $this->parseArguments();
                if (count($args) < 2) {
                    throw new CreasyncError_Syntax('The "attribute" function takes at least two arguments (the variable and the attributes)', $line, $this->parser->getFilename());
                }

                return new CreasyncNode_Expression_GetAttr($args->getNode(0), $args->getNode(1), count($args) > 2 ? $args->getNode(2) : new CreasyncNode_Expression_Array(array(), $line), CreasyncTemplateInterface::ANY_CALL, $line);
            default:
                if (null !== $alias = $this->parser->getImportedSymbol('function', $name)) {
                    $arguments = new CreasyncNode_Expression_Array(array(), $line);
                    foreach ($this->parseArguments() as $n) {
                        $arguments->addElement($n);
                    }

                    $node = new CreasyncNode_Expression_MethodCall($alias['node'], $alias['name'], $arguments, $line);
                    $node->setAttribute('safe', true);

                    return $node;
                }

                $args = $this->parseArguments(true);
                $class = $this->getFunctionNodeClass($name, $line);

                return new $class($name, $args, $line);
        }
    }

    public function parseSubscriptExpression($node)
    {
        $stream = $this->parser->getStream();
        $token = $stream->next();
        $lineno = $token->getLine();
        $arguments = new CreasyncNode_Expression_Array(array(), $lineno);
        $type = CreasyncTemplateInterface::ANY_CALL;
        if ($token->getValue() == '.') {
            $token = $stream->next();
            if (
                $token->getType() == CreasyncToken::NAME_TYPE
                ||
                $token->getType() == CreasyncToken::NUMBER_TYPE
                ||
                ($token->getType() == CreasyncToken::OPERATOR_TYPE && preg_match(CreasyncLexer::REGEX_NAME, $token->getValue()))
            ) {
                $arg = new CreasyncNode_Expression_Constant($token->getValue(), $lineno);

                if ($stream->test(CreasyncToken::PUNCTUATION_TYPE, '(')) {
                    $type = CreasyncTemplateInterface::METHOD_CALL;
                    foreach ($this->parseArguments() as $n) {
                        $arguments->addElement($n);
                    }
                }
            } else {
                throw new CreasyncError_Syntax('Expected name or number', $lineno, $this->parser->getFilename());
            }

            if ($node instanceof CreasyncNode_Expression_Name && null !== $alias = $this->parser->getImportedSymbol('template', $node->getAttribute('name'))) {
                if (!$arg instanceof CreasyncNode_Expression_Constant) {
                    throw new CreasyncError_Syntax(sprintf('Dynamic macro names are not supported (called on "%s")', $node->getAttribute('name')), $token->getLine(), $this->parser->getFilename());
                }

                $node = new CreasyncNode_Expression_MethodCall($node, 'get'.$arg->getAttribute('value'), $arguments, $lineno);
                $node->setAttribute('safe', true);

                return $node;
            }
        } else {
            $type = CreasyncTemplateInterface::ARRAY_CALL;

            // slice?
            $slice = false;
            if ($stream->test(CreasyncToken::PUNCTUATION_TYPE, ':')) {
                $slice = true;
                $arg = new CreasyncNode_Expression_Constant(0, $token->getLine());
            } else {
                $arg = $this->parseExpression();
            }

            if ($stream->test(CreasyncToken::PUNCTUATION_TYPE, ':')) {
                $slice = true;
                $stream->next();
            }

            if ($slice) {
                if ($stream->test(CreasyncToken::PUNCTUATION_TYPE, ']')) {
                    $length = new CreasyncNode_Expression_Constant(null, $token->getLine());
                } else {
                    $length = $this->parseExpression();
                }

                $class = $this->getFilterNodeClass('slice', $token->getLine());
                $arguments = new CreasyncNode(array($arg, $length));
                $filter = new $class($node, new CreasyncNode_Expression_Constant('slice', $token->getLine()), $arguments, $token->getLine());

                $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ']');

                return $filter;
            }

            $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ']');
        }

        return new CreasyncNode_Expression_GetAttr($node, $arg, $arguments, $type, $lineno);
    }

    public function parseFilterExpression($node)
    {
        $this->parser->getStream()->next();

        return $this->parseFilterExpressionRaw($node);
    }

    public function parseFilterExpressionRaw($node, $tag = null)
    {
        while (true) {
            $token = $this->parser->getStream()->expect(CreasyncToken::NAME_TYPE);

            $name = new CreasyncNode_Expression_Constant($token->getValue(), $token->getLine());
            if (!$this->parser->getStream()->test(CreasyncToken::PUNCTUATION_TYPE, '(')) {
                $arguments = new CreasyncNode();
            } else {
                $arguments = $this->parseArguments(true);
            }

            $class = $this->getFilterNodeClass($name->getAttribute('value'), $token->getLine());

            $node = new $class($node, $name, $arguments, $token->getLine(), $tag);

            if (!$this->parser->getStream()->test(CreasyncToken::PUNCTUATION_TYPE, '|')) {
                break;
            }

            $this->parser->getStream()->next();
        }

        return $node;
    }

    /**
     * Parses arguments.
     *
     * @param Boolean $namedArguments Whether to allow named arguments or not
     * @param Boolean $definition     Whether we are parsing arguments for a function definition
     */
    public function parseArguments($namedArguments = false, $definition = false)
    {
        $args = array();
        $stream = $this->parser->getStream();

        $stream->expect(CreasyncToken::PUNCTUATION_TYPE, '(', 'A list of arguments must begin with an opening parenthesis');
        while (!$stream->test(CreasyncToken::PUNCTUATION_TYPE, ')')) {
            if (!empty($args)) {
                $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ',', 'Arguments must be separated by a comma');
            }

            if ($definition) {
                $token = $stream->expect(CreasyncToken::NAME_TYPE, null, 'An argument must be a name');
                $value = new CreasyncNode_Expression_Name($token->getValue(), $this->parser->getCurrentToken()->getLine());
            } else {
                $value = $this->parseExpression();
            }

            $name = null;
            if ($namedArguments && $stream->test(CreasyncToken::OPERATOR_TYPE, '=')) {
                $token = $stream->next();
                if (!$value instanceof CreasyncNode_Expression_Name) {
                    throw new CreasyncError_Syntax(sprintf('A parameter name must be a string, "%s" given', get_class($value)), $token->getLine(), $this->parser->getFilename());
                }
                $name = $value->getAttribute('name');

                if ($definition) {
                    $value = $this->parsePrimaryExpression();

                    if (!$this->checkConstantExpression($value)) {
                        throw new CreasyncError_Syntax(sprintf('A default value for an argument must be a constant (a boolean, a string, a number, or an array).'), $token->getLine(), $this->parser->getFilename());
                    }
                } else {
                    $value = $this->parseExpression();
                }
            }

            if ($definition) {
                if (null === $name) {
                    $name = $value->getAttribute('name');
                    $value = new CreasyncNode_Expression_Constant(null, $this->parser->getCurrentToken()->getLine());
                }
                $args[$name] = $value;
            } else {
                if (null === $name) {
                    $args[] = $value;
                } else {
                    $args[$name] = $value;
                }
            }
        }
        $stream->expect(CreasyncToken::PUNCTUATION_TYPE, ')', 'A list of arguments must be closed by a parenthesis');

        return new CreasyncNode($args);
    }

    public function parseAssignmentExpression()
    {
        $targets = array();
        while (true) {
            $token = $this->parser->getStream()->expect(CreasyncToken::NAME_TYPE, null, 'Only variables can be assigned to');
            if (in_array($token->getValue(), array('true', 'false', 'none'))) {
                throw new CreasyncError_Syntax(sprintf('You cannot assign a value to "%s"', $token->getValue()), $token->getLine(), $this->parser->getFilename());
            }
            $targets[] = new CreasyncNode_Expression_AssignName($token->getValue(), $token->getLine());

            if (!$this->parser->getStream()->test(CreasyncToken::PUNCTUATION_TYPE, ',')) {
                break;
            }
            $this->parser->getStream()->next();
        }

        return new CreasyncNode($targets);
    }

    public function parseMultitargetExpression()
    {
        $targets = array();
        while (true) {
            $targets[] = $this->parseExpression();
            if (!$this->parser->getStream()->test(CreasyncToken::PUNCTUATION_TYPE, ',')) {
                break;
            }
            $this->parser->getStream()->next();
        }

        return new CreasyncNode($targets);
    }

    protected function getFunctionNodeClass($name, $line)
    {
        $env = $this->parser->getEnvironment();

        if (false === $function = $env->getFunction($name)) {
            $message = sprintf('The function "%s" does not exist', $name);
            if ($alternatives = $env->computeAlternatives($name, array_keys($env->getFunctions()))) {
                $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
            }

            throw new CreasyncError_Syntax($message, $line, $this->parser->getFilename());
        }

        if ($function instanceof CreasyncSimpleFunction) {
            return $function->getNodeClass();
        }

        return $function instanceof CreasyncFunction_Node ? $function->getClass() : 'CreasyncNode_Expression_Function';
    }

    protected function getFilterNodeClass($name, $line)
    {
        $env = $this->parser->getEnvironment();

        if (false === $filter = $env->getFilter($name)) {
            $message = sprintf('The filter "%s" does not exist', $name);
            if ($alternatives = $env->computeAlternatives($name, array_keys($env->getFilters()))) {
                $message = sprintf('%s. Did you mean "%s"', $message, implode('", "', $alternatives));
            }

            throw new CreasyncError_Syntax($message, $line, $this->parser->getFilename());
        }

        if ($filter instanceof CreasyncSimpleFilter) {
            return $filter->getNodeClass();
        }

        return $filter instanceof CreasyncFilter_Node ? $filter->getClass() : 'CreasyncNode_Expression_Filter';
    }

    // checks that the node only contains "constant" elements
    protected function checkConstantExpression(CreasyncNodeInterface $node)
    {
        if (!($node instanceof CreasyncNode_Expression_Constant || $node instanceof CreasyncNode_Expression_Array)) {
            return false;
        }

        foreach ($node as $n) {
            if (!$this->checkConstantExpression($n)) {
                return false;
            }
        }

        return true;
    }
}
