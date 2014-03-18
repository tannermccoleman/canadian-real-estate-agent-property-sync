<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncNode_Expression_Binary_LessEqual extends CreasyncNode_Expression_Binary
{
    public function operator(CreasyncCompiler $compiler)
    {
        return $compiler->raw('<=');
    }
}
