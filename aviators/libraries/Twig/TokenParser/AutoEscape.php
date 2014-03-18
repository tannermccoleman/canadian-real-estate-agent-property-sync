<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Marks a section of a template to be escaped or not.
 *
 * <pre>
 * {% autoescape true %}
 *   Everything will be automatically escaped in this block
 * {% endautoescape %}
 *
 * {% autoescape false %}
 *   Everything will be outputed as is in this block
 * {% endautoescape %}
 *
 * {% autoescape true js %}
 *   Everything will be automatically escaped in this block
 *   using the js escaping strategy
 * {% endautoescape %}
 * </pre>
 */
class CreasyncTokenParser_AutoEscape extends CreasyncTokenParser
{
    /**
     * Parses a token and returns a node.
     *
     * @param CreasyncToken $token A CreasyncToken instance
     *
     * @return CreasyncNodeInterface A CreasyncNodeInterface instance
     */
    public function parse(CreasyncToken $token)
    {
        $lineno = $token->getLine();
        $stream = $this->parser->getStream();

        if ($stream->test(CreasyncToken::BLOCK_END_TYPE)) {
            $value = 'html';
        } else {
            $expr = $this->parser->getExpressionParser()->parseExpression();
            if (!$expr instanceof CreasyncNode_Expression_Constant) {
                throw new CreasyncError_Syntax('An escaping strategy must be a string or a Boolean.', $stream->getCurrent()->getLine(), $stream->getFilename());
            }
            $value = $expr->getAttribute('value');

            $compat = true === $value || false === $value;

            if (true === $value) {
                $value = 'html';
            }

            if ($compat && $stream->test(CreasyncToken::NAME_TYPE)) {
                if (false === $value) {
                    throw new CreasyncError_Syntax('Unexpected escaping strategy as you set autoescaping to false.', $stream->getCurrent()->getLine(), $stream->getFilename());
                }

                $value = $stream->next()->getValue();
            }
        }

        $stream->expect(CreasyncToken::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $stream->expect(CreasyncToken::BLOCK_END_TYPE);

        return new CreasyncNode_AutoEscape($value, $body, $lineno, $this->getTag());
    }

    public function decideBlockEnd(CreasyncToken $token)
    {
        return $token->test('endautoescape');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'autoescape';
    }
}
