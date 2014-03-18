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
abstract class CreasyncNode_Expression_Unary extends CreasyncNode_Expression
{
    public function __construct(CreasyncNodeInterface $node, $lineno)
    {
        parent::__construct(array('node' => $node), array(), $lineno);
    }

    public function compile(CreasyncCompiler $compiler)
    {
        $compiler->raw('(');
        $this->operator($compiler);
        $compiler
            ->subcompile($this->getNode('node'))
            ->raw(')')
        ;
    }

    abstract public function operator(CreasyncCompiler $compiler);
}
