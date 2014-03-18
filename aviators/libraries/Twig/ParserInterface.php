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
 * Interface implemented by parser classes.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
interface CreasyncParserInterface
{
    /**
     * Converts a token stream to a node tree.
     *
     * @param CreasyncTokenStream $stream A token stream instance
     *
     * @return CreasyncNode_Module A node tree
     */
    public function parse(CreasyncTokenStream $stream);
}
