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
 * CreasyncNodeTraverser is a node traverser.
 *
 * It visits all nodes and their children and call the given visitor for each.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncNodeTraverser
{
    protected $env;
    protected $visitors;

    /**
     * Constructor.
     *
     * @param CreasyncEnvironment $env      A CreasyncEnvironment instance
     * @param array            $visitors An array of CreasyncNodeVisitorInterface instances
     */
    public function __construct(CreasyncEnvironment $env, array $visitors = array())
    {
        $this->env = $env;
        $this->visitors = array();
        foreach ($visitors as $visitor) {
            $this->addVisitor($visitor);
        }
    }

    /**
     * Adds a visitor.
     *
     * @param CreasyncNodeVisitorInterface $visitor A CreasyncNodeVisitorInterface instance
     */
    public function addVisitor(CreasyncNodeVisitorInterface $visitor)
    {
        if (!isset($this->visitors[$visitor->getPriority()])) {
            $this->visitors[$visitor->getPriority()] = array();
        }

        $this->visitors[$visitor->getPriority()][] = $visitor;
    }

    /**
     * Traverses a node and calls the registered visitors.
     *
     * @param CreasyncNodeInterface $node A CreasyncNodeInterface instance
     */
    public function traverse(CreasyncNodeInterface $node)
    {
        ksort($this->visitors);
        foreach ($this->visitors as $visitors) {
            foreach ($visitors as $visitor) {
                $node = $this->traverseForVisitor($visitor, $node);
            }
        }

        return $node;
    }

    protected function traverseForVisitor(CreasyncNodeVisitorInterface $visitor, CreasyncNodeInterface $node = null)
    {
        if (null === $node) {
            return null;
        }

        $node = $visitor->enterNode($node, $this->env);

        foreach ($node as $k => $n) {
            if (false !== $n = $this->traverseForVisitor($visitor, $n)) {
                $node->setNode($k, $n);
            } else {
                $node->removeNode($k);
            }
        }

        return $visitor->leaveNode($node, $this->env);
    }
}
