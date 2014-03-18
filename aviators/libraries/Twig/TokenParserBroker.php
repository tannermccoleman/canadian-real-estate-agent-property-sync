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
 * Default implementation of a token parser broker.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
class CreasyncTokenParserBroker implements CreasyncTokenParserBrokerInterface
{
    protected $parser;
    protected $parsers = array();
    protected $brokers = array();

    /**
     * Constructor.
     *
     * @param array|Traversable $parsers A Traversable of CreasyncTokenParserInterface instances
     * @param array|Traversable $brokers A Traversable of CreasyncTokenParserBrokerInterface instances
     */
    public function __construct($parsers = array(), $brokers = array())
    {
        foreach ($parsers as $parser) {
            if (!$parser instanceof CreasyncTokenParserInterface) {
                throw new LogicException('$parsers must a an array of CreasyncTokenParserInterface');
            }
            $this->parsers[$parser->getTag()] = $parser;
        }
        foreach ($brokers as $broker) {
            if (!$broker instanceof CreasyncTokenParserBrokerInterface) {
                throw new LogicException('$brokers must a an array of CreasyncTokenParserBrokerInterface');
            }
            $this->brokers[] = $broker;
        }
    }

    /**
     * Adds a TokenParser.
     *
     * @param CreasyncTokenParserInterface $parser A CreasyncTokenParserInterface instance
     */
    public function addTokenParser(CreasyncTokenParserInterface $parser)
    {
        $this->parsers[$parser->getTag()] = $parser;
    }

    /**
     * Removes a TokenParser.
     *
     * @param CreasyncTokenParserInterface $parser A CreasyncTokenParserInterface instance
     */
    public function removeTokenParser(CreasyncTokenParserInterface $parser)
    {
        $name = $parser->getTag();
        if (isset($this->parsers[$name]) && $parser === $this->parsers[$name]) {
            unset($this->parsers[$name]);
        }
    }

    /**
     * Adds a TokenParserBroker.
     *
     * @param CreasyncTokenParserBroker $broker A CreasyncTokenParserBroker instance
     */
    public function addTokenParserBroker(CreasyncTokenParserBroker $broker)
    {
        $this->brokers[] = $broker;
    }

    /**
     * Removes a TokenParserBroker.
     *
     * @param CreasyncTokenParserBroker $broker A CreasyncTokenParserBroker instance
     */
    public function removeTokenParserBroker(CreasyncTokenParserBroker $broker)
    {
        if (false !== $pos = array_search($broker, $this->brokers)) {
            unset($this->brokers[$pos]);
        }
    }

    /**
     * Gets a suitable TokenParser for a tag.
     *
     * First looks in parsers, then in brokers.
     *
     * @param string $tag A tag name
     *
     * @return null|CreasyncTokenParserInterface A CreasyncTokenParserInterface or null if no suitable TokenParser was found
     */
    public function getTokenParser($tag)
    {
        if (isset($this->parsers[$tag])) {
            return $this->parsers[$tag];
        }
        $broker = end($this->brokers);
        while (false !== $broker) {
            $parser = $broker->getTokenParser($tag);
            if (null !== $parser) {
                return $parser;
            }
            $broker = prev($this->brokers);
        }

        return null;
    }

    public function getParsers()
    {
        return $this->parsers;
    }

    public function getParser()
    {
        return $this->parser;
    }

    public function setParser(CreasyncParserInterface $parser)
    {
        $this->parser = $parser;
        foreach ($this->parsers as $tokenParser) {
            $tokenParser->setParser($parser);
        }
        foreach ($this->brokers as $broker) {
            $broker->setParser($parser);
        }
    }
}
