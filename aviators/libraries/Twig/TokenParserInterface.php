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
 * Interface implemented by token parsers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface CreasyncTokenParserInterface
{
    /**
     * Sets the parser associated with this token parser
     *
     * @param $parser A CreasyncParser instance
     */
    public function setParser(CreasyncParser $parser);

    /**
     * Parses a token and returns a node.
     *
     * @param CreasyncToken $token A CreasyncToken instance
     *
     * @return CreasyncNodeInterface A CreasyncNodeInterface instance
     */
    public function parse(CreasyncToken $token);

    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string The tag name
     */
    public function getTag();
}
