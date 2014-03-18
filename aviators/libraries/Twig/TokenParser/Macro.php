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
 * Defines a macro.
 *
 * <pre>
 * {% macro input(name, value, type, size) %}
 *    <input type="{{ type|default('text') }}" name="{{ name }}" value="{{ value|e }}" size="{{ size|default(20) }}" />
 * {% endmacro %}
 * </pre>
 */
class CreasyncTokenParser_Macro extends CreasyncTokenParser
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
        $name = $stream->expect(CreasyncToken::NAME_TYPE)->getValue();

        $arguments = $this->parser->getExpressionParser()->parseArguments(true, true);

        $stream->expect(CreasyncToken::BLOCK_END_TYPE);
        $this->parser->pushLocalScope();
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        if ($stream->test(CreasyncToken::NAME_TYPE)) {
            $value = $stream->next()->getValue();

            if ($value != $name) {
                throw new CreasyncError_Syntax(sprintf("Expected endmacro for macro '$name' (but %s given)", $value), $stream->getCurrent()->getLine(), $stream->getFilename());
            }
        }
        $this->parser->popLocalScope();
        $stream->expect(CreasyncToken::BLOCK_END_TYPE);

        $this->parser->setMacro($name, new CreasyncNode_Macro($name, new CreasyncNode_Body(array($body)), $arguments, $lineno, $this->getTag()));

        return null;
    }

    public function decideBlockEnd(CreasyncToken $token)
    {
        return $token->test('endmacro');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'macro';
    }
}
