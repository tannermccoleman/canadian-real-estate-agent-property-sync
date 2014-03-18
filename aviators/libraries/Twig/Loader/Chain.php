<?php

/*
 * This file is part of Twig.
 *
 * (c) 2011 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Loads templates from other loaders.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncLoader_Chain implements CreasyncLoaderInterface, CreasyncExistsLoaderInterface
{
    private $hasSourceCache = array();
    protected $loaders;

    /**
     * Constructor.
     *
     * @param CreasyncLoaderInterface[] $loaders An array of loader instances
     */
    public function __construct(array $loaders = array())
    {
        $this->loaders = array();
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * Adds a loader instance.
     *
     * @param CreasyncLoaderInterface $loader A Loader instance
     */
    public function addLoader(CreasyncLoaderInterface $loader)
    {
        $this->loaders[] = $loader;
        $this->hasSourceCache = array();
    }

    /**
     * {@inheritdoc}
     */
    public function getSource($name)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof CreasyncExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->getSource($name);
            } catch (CreasyncError_Loader $e) {
                $exceptions[] = $e->getMessage();
            }
        }

        throw new CreasyncError_Loader(sprintf('Template "%s" is not defined (%s).', $name, implode(', ', $exceptions)));
    }

    /**
     * {@inheritdoc}
     */
    public function exists($name)
    {
        $name = (string) $name;

        if (isset($this->hasSourceCache[$name])) {
            return $this->hasSourceCache[$name];
        }

        foreach ($this->loaders as $loader) {
            if ($loader instanceof CreasyncExistsLoaderInterface && $loader->exists($name)) {
                return $this->hasSourceCache[$name] = true;
            }

            try {
                $loader->getSource($name);

                return $this->hasSourceCache[$name] = true;
            } catch (CreasyncError_Loader $e) {
            }
        }

        return $this->hasSourceCache[$name] = false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey($name)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof CreasyncExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->getCacheKey($name);
            } catch (CreasyncError_Loader $e) {
                $exceptions[] = get_class($loader).': '.$e->getMessage();
            }
        }

        throw new CreasyncError_Loader(sprintf('Template "%s" is not defined (%s).', $name, implode(' ', $exceptions)));
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($name, $time)
    {
        $exceptions = array();
        foreach ($this->loaders as $loader) {
            if ($loader instanceof CreasyncExistsLoaderInterface && !$loader->exists($name)) {
                continue;
            }

            try {
                return $loader->isFresh($name, $time);
            } catch (CreasyncError_Loader $e) {
                $exceptions[] = get_class($loader).': '.$e->getMessage();
            }
        }

        throw new CreasyncError_Loader(sprintf('Template "%s" is not defined (%s).', $name, implode(' ', $exceptions)));
    }
}
