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
 * Interface implemented by compiler classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
interface CreasyncCompilerInterface
{
    /**
     * Compiles a node.
     *
     * @param CreasyncNodeInterface $node The node to compile
     *
     * @return CreasyncCompilerInterface The current compiler instance
     */
    public function compile(CreasyncNodeInterface $node);

    /**
     * Gets the current PHP code after compilation.
     *
     * @return string The PHP code
     */
    public function getSource();
}
