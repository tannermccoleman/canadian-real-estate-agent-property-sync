<?php

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Imports blocks defined in another template into the current template.
 *
 * <pre>
 * {% extends "base.html" %}
 *
 * {% use "blocks.html" %}
 *
 * {% block title %}{% endblock %}
 * {% block content %}{% endblock %}
 * </pre>
 *
 * @see http://www.twig-project.org/doc/templates.html#horizontal-reuse for details.
 */
class CreasyncTokenParser_Use extends CreasyncTokenParser
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
        $template = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();

        if (!$template instanceof CreasyncNode_Expression_Constant) {
            throw new CreasyncError_Syntax('The template references in a "use" statement must be a string.', $stream->getCurrent()->getLine(), $stream->getFilename());
        }

        $targets = array();
        if ($stream->test('with')) {
            $stream->next();

            do {
                $name = $stream->expect(CreasyncToken::NAME_TYPE)->getValue();

                $alias = $name;
                if ($stream->test('as')) {
                    $stream->next();

                    $alias = $stream->expect(CreasyncToken::NAME_TYPE)->getValue();
                }

                $targets[$name] = new CreasyncNode_Expression_Constant($alias, -1);

                if (!$stream->test(CreasyncToken::PUNCTUATION_TYPE, ',')) {
                    break;
                }

                $stream->next();
            } while (true);
        }

        $stream->expect(CreasyncToken::BLOCK_END_TYPE);

        $this->parser->addTrait(new CreasyncNode(array('template' => $template, 'targets' => new CreasyncNode($targets))));

        return null;
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'use';
    }
}
