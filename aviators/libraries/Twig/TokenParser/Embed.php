<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Embeds a template.
 */
class CreasyncTokenParser_Embed extends CreasyncTokenParser_Include
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
        $stream = $this->parser->getStream();

        $parent = $this->parser->getExpressionParser()->parseExpression();

        list($variables, $only, $ignoreMissing) = $this->parseArguments();

        // inject a fake parent to make the parent() function work
        $stream->injectTokens(array(
            new CreasyncToken(CreasyncToken::BLOCK_START_TYPE, '', $token->getLine()),
            new CreasyncToken(CreasyncToken::NAME_TYPE, 'extends', $token->getLine()),
            new CreasyncToken(CreasyncToken::STRING_TYPE, '__parent__', $token->getLine()),
            new CreasyncToken(CreasyncToken::BLOCK_END_TYPE, '', $token->getLine()),
        ));

        $module = $this->parser->parse($stream, array($this, 'decideBlockEnd'), true);

        // override the parent with the correct one
        $module->setNode('parent', $parent);

        $this->parser->embedTemplate($module);

        $stream->expect(CreasyncToken::BLOCK_END_TYPE);

        return new CreasyncNode_Embed($module->getAttribute('filename'), $module->getAttribute('index'), $variables, $only, $ignoreMissing, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(CreasyncToken $token)
    {
        return $token->test('endembed');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'embed';
    }
}
