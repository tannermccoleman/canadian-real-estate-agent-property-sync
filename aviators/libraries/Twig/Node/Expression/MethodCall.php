<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncNode_Expression_MethodCall extends CreasyncNode_Expression
{
    public function __construct(CreasyncNode_Expression $node, $method, CreasyncNode_Expression_Array $arguments, $lineno)
    {
        parent::__construct(array('node' => $node, 'arguments' => $arguments), array('method' => $method, 'safe' => false), $lineno);

        if ($node instanceof CreasyncNode_Expression_Name) {
            $node->setAttribute('always_defined', true);
        }
    }

    public function compile(CreasyncCompiler $compiler)
    {
        $compiler
            ->subcompile($this->getNode('node'))
            ->raw('->')
            ->raw($this->getAttribute('method'))
            ->raw('(')
        ;
        $first = true;
        foreach ($this->getNode('arguments')->getKeyValuePairs() as $pair) {
            if (!$first) {
                $compiler->raw(', ');
            }
            $first = false;

            $compiler->subcompile($pair['value']);
        }
        $compiler->raw(')');
    }
}
