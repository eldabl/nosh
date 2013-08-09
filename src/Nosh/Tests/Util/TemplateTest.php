<?php

namespace Nosh\Tests\Util;
use Nosh\Util\TemplateEngine;

/**
 * Test the templating engine for
 * nosh.
 */
class TemplateEngineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test the template engine.
     */
    function testTemplateEngine()
    {
        // You specify themes as template dirs.
        $template_dirs = array(
            'templates2' => __DIR__ . '/templates2',
            'templates' => __DIR__ . '/templates',
        );
        $template = new TemplateEngine($template_dirs);
        // One of the templates only exists in one of the template directories.
        // That template should be loaded automaticly.
        $this->assertEquals("unique file\n",
            $template->render('templates2unique'));
        // The first template directory in the array
        // takes priority, allowing overrides.
        $this->assertEquals("overridden file\n",
            $template->render('overrideme'));
        // It is possible to specify the theme to use.
        // Specifying a function to the use method will use that theme
        // for everything that is going on during the function.
        $class = $this;
        $template->useTheme('templates', function() use($class, $template) {
                $class->assertEquals("default file\n",  $template->render('overrideme'));
        });
        // After the use function has executed, we are back to normal.
        $this->assertEquals("overridden file\n",
            $template->render('overrideme'));
        // Specifying which theme to use without a function permanently sets
        // the theme that is used.
        $template->useTheme('templates');
        $this->assertEquals("default file\n", $template->render('overrideme'));

        // Use all themes again by calling the useAll() method.
        $template->useAll();

        // Add more themes.
        $template->addTheme('templates3', __DIR__ . '/templates3');
        $this->assertEquals("template 3 template\n", $template->render('templates3template'));
        // Pass variables to templates.
        $this->assertEquals("test variable\n", $template->render('vartemplate', array('var' => 'test variable')));
    }
}
