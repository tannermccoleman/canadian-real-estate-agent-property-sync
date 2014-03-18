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
 * Stores the Twig configuration.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class CreasyncEnvironment
{
    const VERSION = '1.12.3';

    protected $charset;
    protected $loader;
    protected $debug;
    protected $autoReload;
    protected $cache;
    protected $lexer;
    protected $parser;
    protected $compiler;
    protected $baseTemplateClass;
    protected $extensions;
    protected $parsers;
    protected $visitors;
    protected $filters;
    protected $tests;
    protected $functions;
    protected $globals;
    protected $runtimeInitialized;
    protected $extensionInitialized;
    protected $loadedTemplates;
    protected $strictVariables;
    protected $unaryOperators;
    protected $binaryOperators;
    protected $templateClassPrefix = '__TwigTemplate_';
    protected $functionCallbacks;
    protected $filterCallbacks;
    protected $staging;

    /**
     * Constructor.
     *
     * Available options:
     *
     *  * debug: When set to true, it automatically set "auto_reload" to true as
     *           well (default to false).
     *
     *  * charset: The charset used by the templates (default to utf-8).
     *
     *  * base_template_class: The base template class to use for generated
     *                         templates (default to CreasyncTemplate).
     *
     *  * cache: An absolute path where to store the compiled templates, or
     *           false to disable compilation cache (default).
     *
     *  * auto_reload: Whether to reload the template is the original source changed.
     *                 If you don't provide the auto_reload option, it will be
     *                 determined automatically base on the debug value.
     *
     *  * strict_variables: Whether to ignore invalid variables in templates
     *                      (default to false).
     *
     *  * autoescape: Whether to enable auto-escaping (default to html):
     *                  * false: disable auto-escaping
     *                  * true: equivalent to html
     *                  * html, js: set the autoescaping to one of the supported strategies
     *                  * PHP callback: a PHP callback that returns an escaping strategy based on the template "filename"
     *
     *  * optimizations: A flag that indicates which optimizations to apply
     *                   (default to -1 which means that all optimizations are enabled;
     *                   set it to 0 to disable).
     *
     * @param CreasyncLoaderInterface $loader  A CreasyncLoaderInterface instance
     * @param array                $options An array of options
     */
    public function __construct(CreasyncLoaderInterface $loader = null, $options = array())
    {
        if (null !== $loader) {
            $this->setLoader($loader);
        }

        $options = array_merge(array(
            'debug'               => false,
            'charset'             => 'UTF-8',
            'base_template_class' => 'CreasyncTemplate',
            'strict_variables'    => false,
            'autoescape'          => 'html',
            'cache'               => false,
            'auto_reload'         => null,
            'optimizations'       => -1,
        ), $options);

        $this->debug              = (bool) $options['debug'];
        $this->charset            = $options['charset'];
        $this->baseTemplateClass  = $options['base_template_class'];
        $this->autoReload         = null === $options['auto_reload'] ? $this->debug : (bool) $options['auto_reload'];
        $this->strictVariables    = (bool) $options['strict_variables'];
        $this->runtimeInitialized = false;
        $this->setCache($options['cache']);
        $this->functionCallbacks = array();
        $this->filterCallbacks = array();

        $this->addExtension(new CreasyncExtension_Core());
        $this->addExtension(new CreasyncExtension_Escaper($options['autoescape']));
        $this->addExtension(new CreasyncExtension_Optimizer($options['optimizations']));
        $this->extensionInitialized = false;
        $this->staging = new CreasyncExtension_Staging();
    }

    /**
     * Gets the base template class for compiled templates.
     *
     * @return string The base template class name
     */
    public function getBaseTemplateClass()
    {
        return $this->baseTemplateClass;
    }

    /**
     * Sets the base template class for compiled templates.
     *
     * @param string $class The base template class name
     */
    public function setBaseTemplateClass($class)
    {
        $this->baseTemplateClass = $class;
    }

    /**
     * Enables debugging mode.
     */
    public function enableDebug()
    {
        $this->debug = true;
    }

    /**
     * Disables debugging mode.
     */
    public function disableDebug()
    {
        $this->debug = false;
    }

    /**
     * Checks if debug mode is enabled.
     *
     * @return Boolean true if debug mode is enabled, false otherwise
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * Enables the auto_reload option.
     */
    public function enableAutoReload()
    {
        $this->autoReload = true;
    }

    /**
     * Disables the auto_reload option.
     */
    public function disableAutoReload()
    {
        $this->autoReload = false;
    }

    /**
     * Checks if the auto_reload option is enabled.
     *
     * @return Boolean true if auto_reload is enabled, false otherwise
     */
    public function isAutoReload()
    {
        return $this->autoReload;
    }

    /**
     * Enables the strict_variables option.
     */
    public function enableStrictVariables()
    {
        $this->strictVariables = true;
    }

    /**
     * Disables the strict_variables option.
     */
    public function disableStrictVariables()
    {
        $this->strictVariables = false;
    }

    /**
     * Checks if the strict_variables option is enabled.
     *
     * @return Boolean true if strict_variables is enabled, false otherwise
     */
    public function isStrictVariables()
    {
        return $this->strictVariables;
    }

    /**
     * Gets the cache directory or false if cache is disabled.
     *
     * @return string|false
     */
    public function getCache()
    {
        return $this->cache;
    }

     /**
      * Sets the cache directory or false if cache is disabled.
      *
      * @param string|false $cache The absolute path to the compiled templates,
      *                            or false to disable cache
      */
    public function setCache($cache)
    {
        $this->cache = $cache ? $cache : false;
    }

    /**
     * Gets the cache filename for a given template.
     *
     * @param string $name The template name
     *
     * @return string The cache file name
     */
    public function getCacheFilename($name)
    {
        if (false === $this->cache) {
            return false;
        }

        $class = substr($this->getTemplateClass($name), strlen($this->templateClassPrefix));

        return $this->getCache().'/'.substr($class, 0, 2).'/'.substr($class, 2, 2).'/'.substr($class, 4).'.php';
    }

    /**
     * Gets the template class associated with the given string.
     *
     * @param string  $name  The name for which to calculate the template class name
     * @param integer $index The index if it is an embedded template
     *
     * @return string The template class name
     */
    public function getTemplateClass($name, $index = null)
    {
        return $this->templateClassPrefix.md5($this->getLoader()->getCacheKey($name)).(null === $index ? '' : '_'.$index);
    }

    /**
     * Gets the template class prefix.
     *
     * @return string The template class prefix
     */
    public function getTemplateClassPrefix()
    {
        return $this->templateClassPrefix;
    }

    /**
     * Renders a template.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    public function render($name, array $context = array())
    {
        return $this->loadTemplate($name)->render($context);
    }

    /**
     * Displays a template.
     *
     * @param string $name    The template name
     * @param array  $context An array of parameters to pass to the template
     */
    public function display($name, array $context = array())
    {
        $this->loadTemplate($name)->display($context);
    }

    /**
     * Loads a template by name.
     *
     * @param string  $name  The template name
     * @param integer $index The index if it is an embedded template
     *
     * @return CreasyncTemplateInterface A template instance representing the given template name
     */
    public function loadTemplate($name, $index = null)
    {
        $cls = $this->getTemplateClass($name, $index);

        if (isset($this->loadedTemplates[$cls])) {
            return $this->loadedTemplates[$cls];
        }

        if (!class_exists($cls, false)) {
            if (false === $cache = $this->getCacheFilename($name)) {
                eval('?>'.$this->compileSource($this->getLoader()->getSource($name), $name));
            } else {
                if (!is_file($cache) || ($this->isAutoReload() && !$this->isTemplateFresh($name, filemtime($cache)))) {
                    $this->writeCacheFile($cache, $this->compileSource($this->getLoader()->getSource($name), $name));
                }

                require_once $cache;
            }
        }

        if (!$this->runtimeInitialized) {
            $this->initRuntime();
        }

        return $this->loadedTemplates[$cls] = new $cls($this);
    }

    /**
     * Returns true if the template is still fresh.
     *
     * Besides checking the loader for freshness information,
     * this method also checks if the enabled extensions have
     * not changed.
     *
     * @param string    $name The template name
     * @param timestamp $time The last modification time of the cached template
     *
     * @return Boolean true if the template is fresh, false otherwise
     */
    public function isTemplateFresh($name, $time)
    {
        foreach ($this->extensions as $extension) {
            $r = new ReflectionObject($extension);
            if (filemtime($r->getFileName()) > $time) {
                return false;
            }
        }

        return $this->getLoader()->isFresh($name, $time);
    }

    public function resolveTemplate($names)
    {
        if (!is_array($names)) {
            $names = array($names);
        }

        foreach ($names as $name) {
            if ($name instanceof CreasyncTemplate) {
                return $name;
            }

            try {
                return $this->loadTemplate($name);
            } catch (CreasyncError_Loader $e) {
            }
        }

        if (1 === count($names)) {
            throw $e;
        }

        throw new CreasyncError_Loader(sprintf('Unable to find one of the following templates: "%s".', implode('", "', $names)));
    }

    /**
     * Clears the internal template cache.
     */
    public function clearTemplateCache()
    {
        $this->loadedTemplates = array();
    }

    /**
     * Clears the template cache files on the filesystem.
     */
    public function clearCacheFiles()
    {
        if (false === $this->cache) {
            return;
        }

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->cache), RecursiveIteratorIterator::LEAVES_ONLY) as $file) {
            if ($file->isFile()) {
                @unlink($file->getPathname());
            }
        }
    }

    /**
     * Gets the Lexer instance.
     *
     * @return CreasyncLexerInterface A CreasyncLexerInterface instance
     */
    public function getLexer()
    {
        if (null === $this->lexer) {
            $this->lexer = new CreasyncLexer($this);
        }

        return $this->lexer;
    }

    /**
     * Sets the Lexer instance.
     *
     * @param CreasyncLexerInterface A CreasyncLexerInterface instance
     */
    public function setLexer(CreasyncLexerInterface $lexer)
    {
        $this->lexer = $lexer;
    }

    /**
     * Tokenizes a source code.
     *
     * @param string $source The template source code
     * @param string $name   The template name
     *
     * @return CreasyncTokenStream A CreasyncTokenStream instance
     */
    public function tokenize($source, $name = null)
    {
        return $this->getLexer()->tokenize($source, $name);
    }

    /**
     * Gets the Parser instance.
     *
     * @return CreasyncParserInterface A CreasyncParserInterface instance
     */
    public function getParser()
    {
        if (null === $this->parser) {
            $this->parser = new CreasyncParser($this);
        }

        return $this->parser;
    }

    /**
     * Sets the Parser instance.
     *
     * @param CreasyncParserInterface A CreasyncParserInterface instance
     */
    public function setParser(CreasyncParserInterface $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Parses a token stream.
     *
     * @param CreasyncTokenStream $tokens A CreasyncTokenStream instance
     *
     * @return CreasyncNode_Module A Node tree
     */
    public function parse(CreasyncTokenStream $tokens)
    {
        return $this->getParser()->parse($tokens);
    }

    /**
     * Gets the Compiler instance.
     *
     * @return CreasyncCompilerInterface A CreasyncCompilerInterface instance
     */
    public function getCompiler()
    {
        if (null === $this->compiler) {
            $this->compiler = new CreasyncCompiler($this);
        }

        return $this->compiler;
    }

    /**
     * Sets the Compiler instance.
     *
     * @param CreasyncCompilerInterface $compiler A CreasyncCompilerInterface instance
     */
    public function setCompiler(CreasyncCompilerInterface $compiler)
    {
        $this->compiler = $compiler;
    }

    /**
     * Compiles a Node.
     *
     * @param CreasyncNodeInterface $node A CreasyncNodeInterface instance
     *
     * @return string The compiled PHP source code
     */
    public function compile(CreasyncNodeInterface $node)
    {
        return $this->getCompiler()->compile($node)->getSource();
    }

    /**
     * Compiles a template source code.
     *
     * @param string $source The template source code
     * @param string $name   The template name
     *
     * @return string The compiled PHP source code
     */
    public function compileSource($source, $name = null)
    {
        try {
            return $this->compile($this->parse($this->tokenize($source, $name)));
        } catch (CreasyncError $e) {
            $e->setTemplateFile($name);
            throw $e;
        } catch (Exception $e) {
            throw new CreasyncError_Runtime(sprintf('An exception has been thrown during the compilation of a template ("%s").', $e->getMessage()), -1, $name, $e);
        }
    }

    /**
     * Sets the Loader instance.
     *
     * @param CreasyncLoaderInterface $loader A CreasyncLoaderInterface instance
     */
    public function setLoader(CreasyncLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Gets the Loader instance.
     *
     * @return CreasyncLoaderInterface A CreasyncLoaderInterface instance
     */
    public function getLoader()
    {
        if (null === $this->loader) {
            throw new LogicException('You must set a loader first.');
        }

        return $this->loader;
    }

    /**
     * Sets the default template charset.
     *
     * @param string $charset The default charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    /**
     * Gets the default template charset.
     *
     * @return string The default charset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Initializes the runtime environment.
     */
    public function initRuntime()
    {
        $this->runtimeInitialized = true;

        foreach ($this->getExtensions() as $extension) {
            $extension->initRuntime($this);
        }
    }

    /**
     * Returns true if the given extension is registered.
     *
     * @param string $name The extension name
     *
     * @return Boolean Whether the extension is registered or not
     */
    public function hasExtension($name)
    {
        return isset($this->extensions[$name]);
    }

    /**
     * Gets an extension by name.
     *
     * @param string $name The extension name
     *
     * @return CreasyncExtensionInterface A CreasyncExtensionInterface instance
     */
    public function getExtension($name)
    {
        if (!isset($this->extensions[$name])) {
            throw new CreasyncError_Runtime(sprintf('The "%s" extension is not enabled.', $name));
        }

        return $this->extensions[$name];
    }

    /**
     * Registers an extension.
     *
     * @param CreasyncExtensionInterface $extension A CreasyncExtensionInterface instance
     */
    public function addExtension(CreasyncExtensionInterface $extension)
    {
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to register extension "%s" as extensions have already been initialized.', $extension->getName()));
        }

        $this->extensions[$extension->getName()] = $extension;
    }

    /**
     * Removes an extension by name.
     *
     * This method is deprecated and you should not use it.
     *
     * @param string $name The extension name
     *
     * @deprecated since 1.12 (to be removed in 2.0)
     */
    public function removeExtension($name)
    {
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to remove extension "%s" as extensions have already been initialized.', $name));
        }

        unset($this->extensions[$name]);
    }

    /**
     * Registers an array of extensions.
     *
     * @param array $extensions An array of extensions
     */
    public function setExtensions(array $extensions)
    {
        foreach ($extensions as $extension) {
            $this->addExtension($extension);
        }
    }

    /**
     * Returns all registered extensions.
     *
     * @return array An array of extensions
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * Registers a Token Parser.
     *
     * @param CreasyncTokenParserInterface $parser A CreasyncTokenParserInterface instance
     */
    public function addTokenParser(CreasyncTokenParserInterface $parser)
    {
        if ($this->extensionInitialized) {
            throw new LogicException('Unable to add a token parser as extensions have already been initialized.');
        }

        $this->staging->addTokenParser($parser);
    }

    /**
     * Gets the registered Token Parsers.
     *
     * @return CreasyncTokenParserBrokerInterface A broker containing token parsers
     */
    public function getTokenParsers()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        return $this->parsers;
    }

    /**
     * Gets registered tags.
     *
     * Be warned that this method cannot return tags defined by CreasyncTokenParserBrokerInterface classes.
     *
     * @return CreasyncTokenParserInterface[] An array of CreasyncTokenParserInterface instances
     */
    public function getTags()
    {
        $tags = array();
        foreach ($this->getTokenParsers()->getParsers() as $parser) {
            if ($parser instanceof CreasyncTokenParserInterface) {
                $tags[$parser->getTag()] = $parser;
            }
        }

        return $tags;
    }

    /**
     * Registers a Node Visitor.
     *
     * @param CreasyncNodeVisitorInterface $visitor A CreasyncNodeVisitorInterface instance
     */
    public function addNodeVisitor(CreasyncNodeVisitorInterface $visitor)
    {
        if ($this->extensionInitialized) {
            throw new LogicException('Unable to add a node visitor as extensions have already been initialized.', $extension->getName());
        }

        $this->staging->addNodeVisitor($visitor);
    }

    /**
     * Gets the registered Node Visitors.
     *
     * @return CreasyncNodeVisitorInterface[] An array of CreasyncNodeVisitorInterface instances
     */
    public function getNodeVisitors()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        return $this->visitors;
    }

    /**
     * Registers a Filter.
     *
     * @param string|CreasyncSimpleFilter               $name   The filter name or a CreasyncSimpleFilter instance
     * @param CreasyncFilterInterface|CreasyncSimpleFilter $filter A CreasyncFilterInterface instance or a CreasyncSimpleFilter instance
     */
    public function addFilter($name, $filter = null)
    {
        if (!$name instanceof CreasyncSimpleFilter && !($filter instanceof CreasyncSimpleFilter || $filter instanceof CreasyncFilterInterface)) {
            throw new LogicException('A filter must be an instance of CreasyncFilterInterface or CreasyncSimpleFilter');
        }

        if ($name instanceof CreasyncSimpleFilter) {
            $filter = $name;
            $name = $filter->getName();
        }
        
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to add filter "%s" as extensions have already been initialized.', $name));
        }
        
        $this->staging->addFilter($name, $filter);
    }

    /**
     * Get a filter by name.
     *
     * Subclasses may override this method and load filters differently;
     * so no list of filters is available.
     *
     * @param string $name The filter name
     *
     * @return CreasyncFilter|false A CreasyncFilter instance or false if the filter does not exist
     */
    public function getFilter($name)
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        if (isset($this->filters[$name])) {
            return $this->filters[$name];
        }

        foreach ($this->filters as $pattern => $filter) {
            $pattern = str_replace('\\*', '(.*?)', preg_quote($pattern, '#'), $count);

            if ($count) {
                if (preg_match('#^'.$pattern.'$#', $name, $matches)) {
                    array_shift($matches);
                    $filter->setArguments($matches);

                    return $filter;
                }
            }
        }

        foreach ($this->filterCallbacks as $callback) {
            if (false !== $filter = call_user_func($callback, $name)) {
                return $filter;
            }
        }

        return false;
    }

    public function registerUndefinedFilterCallback($callable)
    {
        $this->filterCallbacks[] = $callable;
    }

    /**
     * Gets the registered Filters.
     *
     * Be warned that this method cannot return filters defined with registerUndefinedFunctionCallback.
     *
     * @return CreasyncFilterInterface[] An array of CreasyncFilterInterface instances
     *
     * @see registerUndefinedFilterCallback
     */
    public function getFilters()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        return $this->filters;
    }

    /**
     * Registers a Test.
     *
     * @param string|CreasyncSimpleTest             $name The test name or a CreasyncSimpleTest instance
     * @param CreasyncTestInterface|CreasyncSimpleTest $test A CreasyncTestInterface instance or a CreasyncSimpleTest instance
     */
    public function addTest($name, $test = null)
    {
        if (!$name instanceof CreasyncSimpleTest && !($test instanceof CreasyncSimpleTest || $test instanceof CreasyncTestInterface)) {
            throw new LogicException('A test must be an instance of CreasyncTestInterface or CreasyncSimpleTest');
        }

        if ($name instanceof CreasyncSimpleTest) {
            $test = $name;
            $name = $test->getName();
        }
        
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to add test "%s" as extensions have already been initialized.', $name));
        }

        $this->staging->addTest($name, $test);
    }

    /**
     * Gets the registered Tests.
     *
     * @return CreasyncTestInterface[] An array of CreasyncTestInterface instances
     */
    public function getTests()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        return $this->tests;
    }

    /**
     * Gets a test by name.
     *
     * @param string $name The test name
     *
     * @return CreasyncTest|false A CreasyncTest instance or false if the test does not exist
     */
    public function getTest($name)
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        if (isset($this->tests[$name])) {
            return $this->tests[$name];
        }

        return false;
    }

    /**
     * Registers a Function.
     *
     * @param string|CreasyncSimpleFunction                 $name     The function name or a CreasyncSimpleFunction instance
     * @param CreasyncFunctionInterface|CreasyncSimpleFunction $function A CreasyncFunctionInterface instance or a CreasyncSimpleFunction instance
     */
    public function addFunction($name, $function = null)
    {
        if (!$name instanceof CreasyncSimpleFunction && !($function instanceof CreasyncSimpleFunction || $function instanceof CreasyncFunctionInterface)) {
            throw new LogicException('A function must be an instance of CreasyncFunctionInterface or CreasyncSimpleFunction');
        }

        if ($name instanceof CreasyncSimpleFunction) {
            $function = $name;
            $name = $function->getName();
        }
        
        if ($this->extensionInitialized) {
            throw new LogicException(sprintf('Unable to add function "%s" as extensions have already been initialized.', $name));
        }
        
        $this->staging->addFunction($name, $function);
    }

    /**
     * Get a function by name.
     *
     * Subclasses may override this method and load functions differently;
     * so no list of functions is available.
     *
     * @param string $name function name
     *
     * @return CreasyncFunction|false A CreasyncFunction instance or false if the function does not exist
     */
    public function getFunction($name)
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        if (isset($this->functions[$name])) {
            return $this->functions[$name];
        }

        foreach ($this->functions as $pattern => $function) {
            $pattern = str_replace('\\*', '(.*?)', preg_quote($pattern, '#'), $count);

            if ($count) {
                if (preg_match('#^'.$pattern.'$#', $name, $matches)) {
                    array_shift($matches);
                    $function->setArguments($matches);

                    return $function;
                }
            }
        }

        foreach ($this->functionCallbacks as $callback) {
            if (false !== $function = call_user_func($callback, $name)) {
                return $function;
            }
        }

        return false;
    }

    public function registerUndefinedFunctionCallback($callable)
    {
        $this->functionCallbacks[] = $callable;
    }

    /**
     * Gets registered functions.
     *
     * Be warned that this method cannot return functions defined with registerUndefinedFunctionCallback.
     *
     * @return CreasyncFunctionInterface[] An array of CreasyncFunctionInterface instances
     *
     * @see registerUndefinedFunctionCallback
     */
    public function getFunctions()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        return $this->functions;
    }

    /**
     * Registers a Global.
     *
     * New globals can be added before compiling or rendering a template;
     * but after, you can only update existing globals.
     *
     * @param string $name  The global name
     * @param mixed  $value The global value
     */
    public function addGlobal($name, $value)
    {
        if ($this->extensionInitialized || $this->runtimeInitialized) {
            if (null === $this->globals) {
                $this->globals = $this->initGlobals();
            }

            /* This condition must be uncommented in Twig 2.0
            if (!array_key_exists($name, $this->globals)) {
                throw new LogicException(sprintf('Unable to add global "%s" as the runtime or the extensions have already been initialized.', $name));
            }
            */
        }

        if ($this->extensionInitialized || $this->runtimeInitialized) {
            // update the value
            $this->globals[$name] = $value;
        } else {
            $this->staging->addGlobal($name, $value);
        }
    }

    /**
     * Gets the registered Globals.
     *
     * @return array An array of globals
     */
    public function getGlobals()
    {
        if (!$this->runtimeInitialized && !$this->extensionInitialized) {
            return $this->initGlobals();
        }

        if (null === $this->globals) {
            $this->globals = $this->initGlobals();
        }

        return $this->globals;
    }

    /**
     * Merges a context with the defined globals.
     *
     * @param array $context An array representing the context
     *
     * @return array The context merged with the globals
     */
    public function mergeGlobals(array $context)
    {
        // we don't use array_merge as the context being generally
        // bigger than globals, this code is faster.
        foreach ($this->getGlobals() as $key => $value) {
            if (!array_key_exists($key, $context)) {
                $context[$key] = $value;
            }
        }

        return $context;
    }

    /**
     * Gets the registered unary Operators.
     *
     * @return array An array of unary operators
     */
    public function getUnaryOperators()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        return $this->unaryOperators;
    }

    /**
     * Gets the registered binary Operators.
     *
     * @return array An array of binary operators
     */
    public function getBinaryOperators()
    {
        if (!$this->extensionInitialized) {
            $this->initExtensions();
        }

        return $this->binaryOperators;
    }

    public function computeAlternatives($name, $items)
    {
        $alternatives = array();
        foreach ($items as $item) {
            $lev = levenshtein($name, $item);
            if ($lev <= strlen($name) / 3 || false !== strpos($item, $name)) {
                $alternatives[$item] = $lev;
            }
        }
        asort($alternatives);

        return array_keys($alternatives);
    }

    protected function initGlobals()
    {
        $globals = array();
        foreach ($this->extensions as $extension) {
            $globals = array_merge($globals, $extension->getGlobals());
        }

        return array_merge($globals, $this->staging->getGlobals());
    }

    protected function initExtensions()
    {
        if ($this->extensionInitialized) {
            return;
        }

        $this->extensionInitialized = true;
        $this->parsers = new CreasyncTokenParserBroker();
        $this->filters = array();
        $this->functions = array();
        $this->tests = array();
        $this->visitors = array();
        $this->unaryOperators = array();
        $this->binaryOperators = array();

        foreach ($this->extensions as $extension) {
            $this->initExtension($extension);
        }
        $this->initExtension($this->staging);
    }

    protected function initExtension(CreasyncExtensionInterface $extension)
    {
        // filters
        foreach ($extension->getFilters() as $name => $filter) {
            if ($name instanceof CreasyncSimpleFilter) {
                $filter = $name;
                $name = $filter->getName();
            } elseif ($filter instanceof CreasyncSimpleFilter) {
                $name = $filter->getName();
            }

            $this->filters[$name] = $filter;
        }

        // functions
        foreach ($extension->getFunctions() as $name => $function) {
            if ($name instanceof CreasyncSimpleFunction) {
                $function = $name;
                $name = $function->getName();
            } elseif ($function instanceof CreasyncSimpleFunction) {
                $name = $function->getName();
            }

            $this->functions[$name] = $function;
        }

        // tests
        foreach ($extension->getTests() as $name => $test) {
            if ($name instanceof CreasyncSimpleTest) {
                $test = $name;
                $name = $test->getName();
            } elseif ($test instanceof CreasyncSimpleTest) {
                $name = $test->getName();
            }

            $this->tests[$name] = $test;
        }

        // token parsers
        foreach ($extension->getTokenParsers() as $parser) {
            if ($parser instanceof CreasyncTokenParserInterface) {
                $this->parsers->addTokenParser($parser);
            } elseif ($parser instanceof CreasyncTokenParserBrokerInterface) {
                $this->parsers->addTokenParserBroker($parser);
            } else {
                throw new LogicException('getTokenParsers() must return an array of CreasyncTokenParserInterface or CreasyncTokenParserBrokerInterface instances');
            }
        }

        // node visitors
        foreach ($extension->getNodeVisitors() as $visitor) {
            $this->visitors[] = $visitor;
        }

        // operators
        if ($operators = $extension->getOperators()) {
            if (2 !== count($operators)) {
                throw new InvalidArgumentException(sprintf('"%s::getOperators()" does not return a valid operators array.', get_class($extension)));
            }

            $this->unaryOperators = array_merge($this->unaryOperators, $operators[0]);
            $this->binaryOperators = array_merge($this->binaryOperators, $operators[1]);
        }
    }

    protected function writeCacheFile($file, $content)
    {
        $dir = dirname($file);
        if (!is_dir($dir)) {
            if (false === @mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new RuntimeException(sprintf("Unable to create the cache directory (%s).", $dir));
            }
        } elseif (!is_writable($dir)) {
            throw new RuntimeException(sprintf("Unable to write in the cache directory (%s).", $dir));
        }

        $tmpFile = tempnam(dirname($file), basename($file));
        if (false !== @file_put_contents($tmpFile, $content)) {
            // rename does not work on Win32 before 5.2.6
            if (@rename($tmpFile, $file) || (@copy($tmpFile, $file) && unlink($tmpFile))) {
                @chmod($file, 0666 & ~umask());

                return;
            }
        }

        throw new RuntimeException(sprintf('Failed to write cache file "%s".', $file));
    }
}
