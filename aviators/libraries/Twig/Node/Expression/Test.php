<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncNode_Expression_Test extends CreasyncNode_Expression_Call
{
    public function __construct(CreasyncNodeInterface $node, $name, CreasyncNodeInterface $arguments = null, $lineno)
    {
        parent::__construct(array('node' => $node, 'arguments' => $arguments), array('name' => $name), $lineno);
    }

    public function compile(CreasyncCompiler $compiler)
    {
        $name = $this->getAttribute('name');
        $test = $compiler->getEnvironment()->getTest($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'test');
        $this->setAttribute('thing', $test);
        if ($test instanceof CreasyncTestCallableInterface || $test instanceof CreasyncSimpleTest) {
            $this->setAttribute('callable', $test->getCallable());
        }

        $this->compileCallable($compiler);
    }
}
