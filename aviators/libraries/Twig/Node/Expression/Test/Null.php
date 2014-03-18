<?php

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Checks that a variable is null.
 *
 * <pre>
 *  {{ var is none }}
 * </pre>
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncNode_Expression_Test_Null extends CreasyncNode_Expression_Test
{
    public function compile(CreasyncCompiler $compiler)
    {
        $compiler
            ->raw('(null === ')
            ->subcompile($this->getNode('node'))
            ->raw(')')
        ;
    }
}
