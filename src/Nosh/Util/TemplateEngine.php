<?php

namespace Nosh\Util;

class TemplateEngine
{
    protected $loaders = array();
    protected $currentLoader;
    protected $environment;
    protected $chainedLoader;

    function __construct($themes)
    {
        // We use an array of loaders rather than
        // putting all paths into Twig_Loader_Filesystem.
        // this allows us to use a particular loader more easily.
        foreach ($themes as $name => $path) {
            $this->loaders[$name] = new \Twig_Loader_Filesystem($path);
        }
        // Consolidate all loaders into one when doing lookups.
        $this->currentLoader = $this->chainedLoader = new \Twig_Loader_Chain($this->loaders);
        $this->environment = new \Twig_Environment($this->currentLoader);
    }

    function useTheme($theme, $fn = null)
    {
        if (!isset($this->loaders[$theme])) {
            throw new \Exception('Theme doesn\'t exist.');
        }
        $old_loader = $this->currentLoader;
        $this->currentLoader = $this->loaders[$theme];
        $this->environment->setLoader($this->currentLoader);
        if (isset($fn)) {
            $this->theme = $theme;
            $fn();
            $this->currentLoader = $old_loader;
            $this->environment->setLoader($this->currentLoader);
        }
    }

    function useAll()
    {
        $this->currentLoader = $this->chainedLoader;
        $this->environment->setLoader($this->currentLoader);
    }

    function render($template, $variables = array())
    {
      return $this->environment->render($template, $variables);
    }

    function addTheme($name, $path)
    {
      $this->loaders[$name] = new \Twig_Loader_Filesystem($path);
      $this->chainedLoader->addLoader($this->loaders[$name]);
    }
}
