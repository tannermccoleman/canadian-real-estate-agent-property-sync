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
 * Filters a section of a template by applying filters.
 *
 * <pre>
 * {% filter upper %}
 *  This text becomes uppercase
 * {% endfilter %}
 * </pre>
 */
class CreasyncTokenParser_Filter extends CreasyncTokenParser
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
        $name = $this->parser->getVarName();
        $ref = new CreasyncNode_Expression_BlockReference(new CreasyncNode_Expression_Constant($name, $token->getLine()), true, $token->getLine(), $this->getTag());

        $filter = $this->parser->getExpressionParser()->parseFilterExpressionRaw($ref, $this->getTag());
        $this->parser->getStream()->expect(CreasyncToken::BLOCK_END_TYPE);

        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(CreasyncToken::BLOCK_END_TYPE);

        $block = new CreasyncNode_Block($name, $body, $token->getLine());
        $this->parser->setBlock($name, $block);

        return new CreasyncNode_Print($filter, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(CreasyncToken $token)
    {
        return $token->test('endfilter');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'filter';
    }
}
