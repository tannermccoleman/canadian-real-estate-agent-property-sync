<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 * (c) 2010 Arnaud Le Blanc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Interface implemented by token parser brokers.
 *
 * Token parser brokers allows to implement custom logic in the process of resolving a token parser for a given tag name.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
interface CreasyncTokenParserBrokerInterface
{
    /**
     * Gets a TokenParser suitable for a tag.
     *
     * @param string $tag A tag name
     *
     * @return null|CreasyncTokenParserInterface A CreasyncTokenParserInterface or null if no suitable TokenParser was found
     */
    public function getTokenParser($tag);

    /**
     * Calls CreasyncTokenParserInterface::setParser on all parsers the implementation knows of.
     *
     * @param CreasyncParserInterface $parser A CreasyncParserInterface interface
     */
    public function setParser(CreasyncParserInterface $parser);

    /**
     * Gets the CreasyncParserInterface.
     *
     * @return null|CreasyncParserInterface A CreasyncParserInterface instance or null
     */
    public function getParser();
}
