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
 * Imports macros.
 *
 * <pre>
 *   {% import 'forms.html' as forms %}
 * </pre>
 */
class CreasyncTokenParser_Import extends CreasyncTokenParser
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
        $this->parser->getStream()->expect('as');
        $var = new CreasyncNode_Expression_AssignName($this->parser->getStream()->expect(CreasyncToken::NAME_TYPE)->getValue(), $token->getLine());
        $this->parser->getStream()->expect(CreasyncToken::BLOCK_END_TYPE);

        $this->parser->addImportedSymbol('template', $var->getAttribute('name'));

        return new CreasyncNode_Import($macro, $var, $token->getLine(), $this->getTag());
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'import';
    }
}
