<?php

/*
 * This file is part of Twig.
 *
 * (c) 2009 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * CreasyncNodeVisitor_Escaper implements output escaping.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncNodeVisitor_Escaper implements CreasyncNodeVisitorInterface
{
    protected $statusStack = array();
    protected $blocks = array();
    protected $safeAnalysis;
    protected $traverser;
    protected $defaultStrategy = false;
    protected $safeVars = array();

    public function __construct()
    {
        $this->safeAnalysis = new CreasyncNodeVisitor_SafeAnalysis();
    }

    /**
     * Called before child nodes are visited.
     *
     * @param CreasyncNodeInterface $node The node to visit
     * @param CreasyncEnvironment   $env  The Twig environment instance
     *
     * @return CreasyncNodeInterface The modified node
     */
    public function enterNode(CreasyncNodeInterface $node, CreasyncEnvironment $env)
    {
        if ($node instanceof CreasyncNode_Module) {
            if ($env->hasExtension('escaper') && $defaultStrategy = $env->getExtension('escaper')->getDefaultStrategy($node->getAttribute('filename'))) {
                $this->defaultStrategy = $defaultStrategy;
            }
            $this->safeVars = array();
        } elseif ($node instanceof CreasyncNode_AutoEscape) {
            $this->statusStack[] = $node->getAttribute('value');
        } elseif ($node instanceof CreasyncNode_Block) {
            $this->statusStack[] = isset($this->blocks[$node->getAttribute('name')]) ? $this->blocks[$node->getAttribute('name')] : $this->needEscaping($env);
        } elseif ($node instanceof CreasyncNode_Import) {
            $this->safeVars[] = $node->getNode('var')->getAttribute('name');
        }

        return $node;
    }

    /**
     * Called after child nodes are visited.
     *
     * @param CreasyncNodeInterface $node The node to visit
     * @param CreasyncEnvironment   $env  The Twig environment instance
     *
     * @return CreasyncNodeInterface The modified node
     */
    public function leaveNode(CreasyncNodeInterface $node, CreasyncEnvironment $env)
    {
        if ($node instanceof CreasyncNode_Module) {
            $this->defaultStrategy = false;
            $this->safeVars = array();
        } elseif ($node instanceof CreasyncNode_Expression_Filter) {
            return $this->preEscapeFilterNode($node, $env);
        } elseif ($node instanceof CreasyncNode_Print) {
            return $this->escapePrintNode($node, $env, $this->needEscaping($env));
        }

        if ($node instanceof CreasyncNode_AutoEscape || $node instanceof CreasyncNode_Block) {
            array_pop($this->statusStack);
        } elseif ($node instanceof CreasyncNode_BlockReference) {
            $this->blocks[$node->getAttribute('name')] = $this->needEscaping($env);
        }

        return $node;
    }

    protected function escapePrintNode(CreasyncNode_Print $node, CreasyncEnvironment $env, $type)
    {
        if (false === $type) {
            return $node;
        }

        $expression = $node->getNode('expr');

        if ($this->isSafeFor($type, $expression, $env)) {
            return $node;
        }

        $class = get_class($node);

        return new $class(
            $this->getEscaperFilter($type, $expression),
            $node->getLine()
        );
    }

    protected function preEscapeFilterNode(CreasyncNode_Expression_Filter $filter, CreasyncEnvironment $env)
    {
        $name = $filter->getNode('filter')->getAttribute('value');

        $type = $env->getFilter($name)->getPreEscape();
        if (null === $type) {
            return $filter;
        }

        $node = $filter->getNode('node');
        if ($this->isSafeFor($type, $node, $env)) {
            return $filter;
        }

        $filter->setNode('node', $this->getEscaperFilter($type, $node));

        return $filter;
    }

    protected function isSafeFor($type, CreasyncNodeInterface $expression, $env)
    {
        $safe = $this->safeAnalysis->getSafe($expression);

        if (null === $safe) {
            if (null === $this->traverser) {
                $this->traverser = new CreasyncNodeTraverser($env, array($this->safeAnalysis));
            }

            $this->safeAnalysis->setSafeVars($this->safeVars);

            $this->traverser->traverse($expression);
            $safe = $this->safeAnalysis->getSafe($expression);
        }

        return in_array($type, $safe) || in_array('all', $safe);
    }

    protected function needEscaping(CreasyncEnvironment $env)
    {
        if (count($this->statusStack)) {
            return $this->statusStack[count($this->statusStack) - 1];
        }

        return $this->defaultStrategy ? $this->defaultStrategy : false;
    }

    protected function getEscaperFilter($type, CreasyncNodeInterface $node)
    {
        $line = $node->getLine();
        $name = new CreasyncNode_Expression_Constant('escape', $line);
        $args = new CreasyncNode(array(new CreasyncNode_Expression_Constant((string) $type, $line), new CreasyncNode_Expression_Constant(null, $line), new CreasyncNode_Expression_Constant(true, $line)));

        return new CreasyncNode_Expression_Filter($node, $name, $args, $line);
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return 0;
    }
}
