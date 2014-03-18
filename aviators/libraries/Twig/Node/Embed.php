<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents an embed node.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncNode_Embed extends CreasyncNode_Include
{
    // we don't inject the module to avoid node visitors to traverse it twice (as it will be already visited in the main module)
    public function __construct($filename, $index, CreasyncNode_Expression $variables = null, $only = false, $ignoreMissing = false, $lineno, $tag = null)
    {
        parent::__construct(new CreasyncNode_Expression_Constant('not_used', $lineno), $variables, $only, $ignoreMissing, $lineno, $tag);

        $this->setAttribute('filename', $filename);
        $this->setAttribute('index', $index);
    }

    protected function addGetTemplate(CreasyncCompiler $compiler)
    {
        $compiler
            ->write("\$this->env->loadTemplate(")
            ->string($this->getAttribute('filename'))
            ->raw(', ')
            ->string($this->getAttribute('index'))
            ->raw(")")
        ;
    }
}
