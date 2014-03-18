<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
abstract class CreasyncTest_NodeTestCase extends PHPUnit_Framework_TestCase
{
    abstract public function getTests();

    /**
     * @dataProvider getTests
     */
    public function testCompile($node, $source, $environment = null)
    {
        $this->assertNodeCompilation($source, $node, $environment);
    }

    public function assertNodeCompilation($source, CreasyncNode $node, CreasyncEnvironment $environment = null)
    {
        $compiler = $this->getCompiler($environment);
        $compiler->compile($node);

        $this->assertEquals($source, trim($compiler->getSource()));
    }

    protected function getCompiler(CreasyncEnvironment $environment = null)
    {
        return new CreasyncCompiler(null === $environment ? $this->getEnvironment() : $environment);
    }

    protected function getEnvironment()
    {
        return new CreasyncEnvironment();
    }

    protected function getVariableGetter($name)
    {
        if (version_compare(phpversion(), '5.4.0RC1', '>=')) {
            return sprintf('(isset($context["%s"]) ? $context["%s"] : null)', $name, $name);
        }

        return sprintf('$this->getContext($context, "%s")', $name);
    }

    protected function getAttributeGetter()
    {
        if (function_exists('twig_template_get_attributes')) {
            return 'twig_template_get_attributes($this, ';
        }

        return '$this->getAttribute(';
    }
}
