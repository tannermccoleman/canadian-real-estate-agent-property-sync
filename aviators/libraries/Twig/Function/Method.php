<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 * (c) 2010 Arnaud Le Blanc
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Represents a method template function.
 *
 * Use CreasyncSimpleFunction instead.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 * @deprecated since 1.12 (to be removed in 2.0)
 */
class CreasyncFunction_Method extends CreasyncFunction
{
    protected $extension;
    protected $method;

    public function __construct(CreasyncExtensionInterface $extension, $method, array $options = array())
    {
        $options['callable'] = array($extension, $method);

        parent::__construct($options);

        $this->extension = $extension;
        $this->method = $method;
    }

    public function compile()
    {
        return sprintf('$this->env->getExtension(\'%s\')->%s', $this->extension->getName(), $this->method);
    }
}
