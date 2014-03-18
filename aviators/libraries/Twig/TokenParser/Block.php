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
 * Marks a section of a template as being reusable.
 *
 * <pre>
 *  {% block head %}
 *    <link rel="stylesheet" href="style.css" />
 *    <title>{% block title %}{% endblock %} - My Webpage</title>
 *  {% endblock %}
 * </pre>
 */
class CreasyncTokenParser_Block extends CreasyncTokenParser
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
        if ($this->parser->hasBlock($name)) {
            throw new CreasyncError_Syntax(sprintf("The block '$name' has already been defined line %d", $this->parser->getBlock($name)->getLine()), $stream->getCurrent()->getLine(), $stream->getFilename());
        }
        $this->parser->setBlock($name, $block = new CreasyncNode_Block($name, new CreasyncNode(array()), $lineno));
        $this->parser->pushLocalScope();
        $this->parser->pushBlockStack($name);

        if ($stream->test(CreasyncToken::BLOCK_END_TYPE)) {
            $stream->next();

            $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
            if ($stream->test(CreasyncToken::NAME_TYPE)) {
                $value = $stream->next()->getValue();

                if ($value != $name) {
                    throw new CreasyncError_Syntax(sprintf("Expected endblock for block '$name' (but %s given)", $value), $stream->getCurrent()->getLine(), $stream->getFilename());
                }
            }
        } else {
            $body = new CreasyncNode(array(
                new CreasyncNode_Print($this->parser->getExpressionParser()->parseExpression(), $lineno),
            ));
        }
        $stream->expect(CreasyncToken::BLOCK_END_TYPE);

        $block->setNode('body', $body);
        $this->parser->popBlockStack();
        $this->parser->popLocalScope();

        return new CreasyncNode_BlockReference($name, $lineno, $this->getTag());
    }

    public function decideBlockEnd(CreasyncToken $token)
    {
        return $token->test('endblock');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'block';
    }
}
