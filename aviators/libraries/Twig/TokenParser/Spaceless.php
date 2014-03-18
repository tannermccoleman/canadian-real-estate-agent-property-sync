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
 * Remove whitespaces between HTML tags.
 *
 * <pre>
 * {% spaceless %}
 *      <div>
 *          <strong>foo</strong>
 *      </div>
 * {% endspaceless %}
 *
 * {# output will be <div><strong>foo</strong></div> #}
 * </pre>
 */
class CreasyncTokenParser_Spaceless extends CreasyncTokenParser
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

        $this->parser->getStream()->expect(CreasyncToken::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideSpacelessEnd'), true);
        $this->parser->getStream()->expect(CreasyncToken::BLOCK_END_TYPE);

        return new CreasyncNode_Spaceless($body, $lineno, $this->getTag());
    }

    public function decideSpacelessEnd(CreasyncToken $token)
    {
        return $token->test('endspaceless');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'spaceless';
    }
}
