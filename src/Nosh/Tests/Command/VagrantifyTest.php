<?php
namespace Nosh\Tests\Command;

use Nosh\Command\VagrantifyCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class VagrantifyCommandTest extends \PHPUnit_Framework_TestCase
{
    function testProfileCommand()
    {
        $application = new Application();
        $application->add(new VagrantifyCommand());
        $command = $application->find('vagrantify');
        $arguments = array(
            'command' => 'vagrantify',
            '--hostname' => 'mybox.dev',
            '--webroot' => 'myroot',
            '--ip' => '192.168.40.2',
            '--boxname' => 'mybox',
            '--boxurl' => 'myboxurl',
            '--path' => __DIR__,
        );
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);
        $dir = __DIR__;
        $this->assertDir("$dir/manifests");
        $this->assertFile("$dir/manifests/manifest.pp");
        $this->assertFile("$dir/Vagrantfile");
        $this->assertDir("$dir/manifests/modules");
        // Check for hardcoded modules.
        foreach (array('systools', 'apache', 'mysql', 'php', 'postfix') as $module) {
            $this->assertDir("$dir/manifests/modules/$module");
        }
        // Check syntax for the Vagrantfile.
        $return = array();
        $statusCode = 0;
        exec("ruby -c $dir/Vagrantfile", $return, $statusCode);
        $this->assertTrue($statusCode == 0);

        $vagrantFile = file_get_contents("$dir/Vagrantfile");
        $this->assertRegExp("/config.vm.box\s=\s\"mybox\"/", $vagrantFile);
        $this->assertRegExp("/config.vm.box_url\s=\s\"myboxurl\"/", $vagrantFile);
        $this->assertRegExp("/config.vm.network\s:hostonly,\s\"192.168.40.2\"/", $vagrantFile);
        $this->assertRegExp("/\"myroot\"/", $vagrantFile);
    }

    /**
     * Clean up after ourselves.
     */
    function tearDown()
    {
        $dir = __DIR__;
        if (is_dir("$dir/manifests")) {
            exec("rm -rf $dir/manifests");
        }
        if (is_file("$dir/Vagrantfile")) {
            unlink("$dir/Vagrantfile");
        }
    }

    function assertFile($file)
    {
        $this->assertTrue(is_file($file));
    }


    function assertDir($dir)
    {
        $this->assertTrue(is_dir($dir));
    }

}
