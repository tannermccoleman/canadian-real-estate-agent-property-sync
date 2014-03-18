<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncNode_Expression_Binary_FloorDiv extends CreasyncNode_Expression_Binary
{
    /**
     * Compiles the node to PHP.
     *
     * @param CreasyncCompiler A CreasyncCompiler instance
     */
    public function compile(CreasyncCompiler $compiler)
    {
        $compiler->raw('intval(floor(');
        parent::compile($compiler);
        $compiler->raw('))');
    }

    public function operator(CreasyncCompiler $compiler)
    {
        return $compiler->raw('/');
    }
}
