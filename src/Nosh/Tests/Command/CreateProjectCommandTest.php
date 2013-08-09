<?php
namespace Nosh\Tests\Command;

use Nosh\Command\CreateProjectCommand;
use Nosh\Command\CreateProfileCommand;
use Nosh\Command\VagrantifyCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateProjectCommandTest extends \PHPUnit_Framework_TestCase
{
    function testProjectCommand()
    {
        $application = new Application();
        $application->add(new CreateProfileCommand());
        $application->add(new VagrantifyCommand());
        $application->add(new CreateProjectCommand());
        $command = $application->find('create-project');
        $dir = __DIR__ . '/testproject';
        $arguments = array(
            'command' => 'create-project',
            'path' => $dir,
            '--no-interaction' => true,
            '--create-profile' => true,
            '--use-vagrant' => true,
            '--api' => '7.x',
            '--profile-name' => 'testprofile',
            '--profile-title' => 'Test profile',
            '--profile-description' => 'This is a test profile',
        );
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);
        // The test profile directory should exist,
        // and there should be profile files in it.
        $this->assertTrue(is_dir($dir));
        $this->assertTrue(is_file("$dir/platform.make"));
        $this->assertTrue(is_file("$dir/build"));
        $this->assertTrue(is_dir("$dir/web/profiles/testprofile"));
        $this->assertTrue(is_dir("$dir/manifests"));
        $this->assertTrue(is_file("$dir/Vagrantfile"));
    }

    /**
     * Clean up after ourselves.
     */
    function tearDown()
    {
        $dir = __DIR__ . '/testproject';
        if (is_dir($dir)) {
            exec("rm -rf $dir");
        }
    }
}