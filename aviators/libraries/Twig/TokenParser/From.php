<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Imports macros.
 *
 * <pre>
 *   {% from 'forms.html' import forms %}
 * </pre>
 */
class CreasyncTokenParser_From extends CreasyncTokenParser
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
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect('import');

        $targets = array();
        do {
            $name = $stream->expect(CreasyncToken::NAME_TYPE)->getValue();

            $alias = $name;
            if ($stream->test('as')) {
                $stream->next();

                $alias = $stream->expect(CreasyncToken::NAME_TYPE)->getValue();
            }

            $targets[$name] = $alias;

            if (!$stream->test(CreasyncToken::PUNCTUATION_TYPE, ',')) {
                break;
            }

            $stream->next();
        } while (true);

        $stream->expect(CreasyncToken::BLOCK_END_TYPE);

        $node = new CreasyncNode_Import($macro, new CreasyncNode_Expression_AssignName($this->parser->getVarName(), $token->getLine()), $token->getLine(), $this->getTag());

        foreach ($targets as $name => $alias) {
            $this->parser->addImportedSymbol('function', $alias, 'get'.$name, $node->getNode('var'));
        }

        return $node;
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'from';
    }
}
