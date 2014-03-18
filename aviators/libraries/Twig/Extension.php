<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class CreasyncExtension implements CreasyncExtensionInterface
{
    /**
     * Initializes the runtime environment.
     *
     * This is where you can load some file that contains filter functions for instance.
     *
     * @param CreasyncEnvironment $environment The current CreasyncEnvironment instance
     */
    public function initRuntime(CreasyncEnvironment $environment)
    {
    }

    /**
     * Returns the token parser instances to add to the existing list.
     *
     * @return array An array of CreasyncTokenParserInterface or CreasyncTokenParserBrokerInterface instances
     */
    public function getTokenParsers()
    {
        return array();
    }

    /**
     * Returns the node visitor instances to add to the existing list.
     *
     * @return array An array of CreasyncNodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        return array();
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return array An array of filters
     */
    public function getFilters()
    {
        return array();
    }

    /**
     * Returns a list of tests to add to the existing list.
     *
     * @return array An array of tests
     */
    public function getTests()
    {
        return array();
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
    public function getFunctions()
    {
        return array();
    }

    /**
     * Returns a list of operators to add to the existing list.
     *
     * @return array An array of operators
     */
    public function getOperators()
    {
        return array();
    }

    /**
     * Returns a list of global variables to add to the existing list.
     *
     * @return array An array of global variables
     */
    public function getGlobals()
    {
        return array();
    }
}
