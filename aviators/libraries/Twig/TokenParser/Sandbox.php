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
 * Marks a section of a template as untrusted code that must be evaluated in the sandbox mode.
 *
 * <pre>
 * {% sandbox %}
 *     {% include 'user.html' %}
 * {% endsandbox %}
 * </pre>
 *
 * @see http://www.twig-project.org/doc/api.html#sandbox-extension for details
 */
class CreasyncTokenParser_Sandbox extends CreasyncTokenParser
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
        $this->parser->getStream()->expect(CreasyncToken::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideBlockEnd'), true);
        $this->parser->getStream()->expect(CreasyncToken::BLOCK_END_TYPE);

        // in a sandbox tag, only include tags are allowed
        if (!$body instanceof CreasyncNode_Include) {
            foreach ($body as $node) {
                if ($node instanceof CreasyncNode_Text && ctype_space($node->getAttribute('data'))) {
                    continue;
                }

                if (!$node instanceof CreasyncNode_Include) {
                    throw new CreasyncError_Syntax('Only "include" tags are allowed within a "sandbox" section', $node->getLine(), $this->parser->getFilename());
                }
            }
        }

        return new CreasyncNode_Sandbox($body, $token->getLine(), $this->getTag());
    }

    public function decideBlockEnd(CreasyncToken $token)
    {
        return $token->test('endsandbox');
    }

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag()
    {
        return 'sandbox';
    }
}
