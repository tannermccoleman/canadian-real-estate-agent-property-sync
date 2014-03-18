<?php

/*
 * This file is part of Twig.
 *
 * (c) 2012 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class CreasyncExtension_StringLoader extends CreasyncExtension
{
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new CreasyncSimpleFunction('template_from_string', 'twig_template_from_string', array('needs_environment' => true)),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'string_loader';
    }
}

/**
 * Loads a template from a string.
 *
 * <pre>
 * {% include template_from_string("Hello {{ name }}") }}
 * </pre>
 *
 * @param CreasyncEnvironment $env      A CreasyncEnvironment instance
 * @param string           $template A template as a string
 *
 * @return CreasyncTemplate A CreasyncTemplate instance
 */
function twig_template_from_string(CreasyncEnvironment $env, $template)
{
    static $loader;

    if (null === $loader) {
        $loader = new CreasyncLoader_String();
    }

    $current = $env->getLoader();
    $env->setLoader($loader);
    try {
        $template = $env->loadTemplate($template);
    } catch (Exception $e) {
        $env->setLoader($current);

        throw $e;
    }
    $env->setLoader($current);

    return $template;
}
