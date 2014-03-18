<?php

/*
 * This file is part of Twig.
 *
 * (c) 2010 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncNode_Expression_Function extends CreasyncNode_Expression_Call
{
    public function __construct($name, CreasyncNodeInterface $arguments, $lineno)
    {
        parent::__construct(array('arguments' => $arguments), array('name' => $name), $lineno);
    }

    public function compile(CreasyncCompiler $compiler)
    {
        $name = $this->getAttribute('name');
        $function = $compiler->getEnvironment()->getFunction($name);

        $this->setAttribute('name', $name);
        $this->setAttribute('type', 'function');
        $this->setAttribute('thing', $function);
        $this->setAttribute('needs_environment', $function->needsEnvironment());
        $this->setAttribute('needs_context', $function->needsContext());
        $this->setAttribute('arguments', $function->getArguments());
        if ($function instanceof CreasyncFunctionCallableInterface || $function instanceof CreasyncSimpleFunction) {
            $this->setAttribute('callable', $function->getCallable());
        }

        $this->compileCallable($compiler);
    }
}
