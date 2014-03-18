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
abstract class CreasyncNode_Expression_Binary extends CreasyncNode_Expression
{
    public function __construct(CreasyncNodeInterface $left, CreasyncNodeInterface $right, $lineno)
    {
        parent::__construct(array('left' => $left, 'right' => $right), array(), $lineno);
    }

    /**
     * Compiles the node to PHP.
     *
     * @param CreasyncCompiler A CreasyncCompiler instance
     */
    public function compile(CreasyncCompiler $compiler)
    {
        $compiler
            ->raw('(')
            ->subcompile($this->getNode('left'))
            ->raw(' ')
        ;
        $this->operator($compiler);
        $compiler
            ->raw(' ')
            ->subcompile($this->getNode('right'))
            ->raw(')')
        ;
    }

    abstract public function operator(CreasyncCompiler $compiler);
}
