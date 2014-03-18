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
class CreasyncNode_Expression_Conditional extends CreasyncNode_Expression
{
    public function __construct(CreasyncNode_Expression $expr1, CreasyncNode_Expression $expr2, CreasyncNode_Expression $expr3, $lineno)
    {
        parent::__construct(array('expr1' => $expr1, 'expr2' => $expr2, 'expr3' => $expr3), array(), $lineno);
    }

    public function compile(CreasyncCompiler $compiler)
    {
        $compiler
            ->raw('((')
            ->subcompile($this->getNode('expr1'))
            ->raw(') ? (')
            ->subcompile($this->getNode('expr2'))
            ->raw(') : (')
            ->subcompile($this->getNode('expr3'))
            ->raw('))')
        ;
    }
}
