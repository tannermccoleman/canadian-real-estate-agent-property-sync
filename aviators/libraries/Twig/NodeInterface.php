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
 * Represents a node in the AST.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
interface CreasyncNodeInterface extends Countable, IteratorAggregate
{
    /**
     * Compiles the node to PHP.
     *
     * @param CreasyncCompiler A CreasyncCompiler instance
     */
    public function compile(CreasyncCompiler $compiler);

    public function getLine();

    public function getNodeTag();
}
