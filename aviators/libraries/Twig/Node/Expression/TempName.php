<?php

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncNode_Expression_TempName extends CreasyncNode_Expression
{
    public function __construct($name, $lineno)
    {
        parent::__construct(array(), array('name' => $name), $lineno);
    }

    public function compile(CreasyncCompiler $compiler)
    {
        $compiler
            ->raw('$_')
            ->raw($this->getAttribute('name'))
            ->raw('_')
        ;
    }
}
