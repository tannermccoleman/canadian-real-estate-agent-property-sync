<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2009 Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CreasyncNode_Expression_AssignName extends CreasyncNode_Expression_Name
{
    /**
     * Compiles the node to PHP.
     *
     * @param CreasyncCompiler A CreasyncCompiler instance
     */
    public function compile(CreasyncCompiler $compiler)
    {
        $compiler
            ->raw('$context[')
            ->string($this->getAttribute('name'))
            ->raw(']')
        ;
    }
}
