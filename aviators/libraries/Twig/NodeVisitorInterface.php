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
 * CreasyncNodeVisitorInterface is the interface the all node visitor classes must implement.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface CreasyncNodeVisitorInterface
{
    /**
     * Called before child nodes are visited.
     *
     * @param CreasyncNodeInterface $node The node to visit
     * @param CreasyncEnvironment   $env  The Twig environment instance
     *
     * @return CreasyncNodeInterface The modified node
     */
    public function enterNode(CreasyncNodeInterface $node, CreasyncEnvironment $env);

    /**
     * Called after child nodes are visited.
     *
     * @param CreasyncNodeInterface $node The node to visit
     * @param CreasyncEnvironment   $env  The Twig environment instance
     *
     * @return CreasyncNodeInterface|false The modified node or false if the node must be removed
     */
    public function leaveNode(CreasyncNodeInterface $node, CreasyncEnvironment $env);

    /**
     * Returns the priority for this visitor.
     *
     * Priority should be between -10 and 10 (0 is the default).
     *
     * @return integer The priority level
     */
    public function getPriority();
}
