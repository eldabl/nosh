<?php
namespace Nosh\Tests\Command;

use Nosh\Command\CreateProfileCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CreateProfileCommandTest extends \PHPUnit_Framework_TestCase
{
    function testProfileCommand()
    {
        $application = new Application();
        $application->add(new CreateProfileCommand());
        $command = $application->find('create-profile');
        $dir = __DIR__ . '/testprofile';
        $arguments = array(
            'command' => 'create-profile',
            'path' => $dir,
            '--title' => 'Test profile',
            '--description' => 'This is a test profile',
        );
        $commandTester = new CommandTester($command);
        $commandTester->execute($arguments);
        // The test profile directory should exist,
        // and there should be profile files in it.
        $this->assertDir($dir);
        $this->assertFile("$dir/testprofile.info");
        $this->assertFile("$dir/testprofile.make");
        $this->assertFile("$dir/testprofile.profile");

        // The test profile file should compile.
        $return = array();
        $statusCode = 0;
        exec("php -l -f $dir/testprofile.profile", $return, $statusCode);
        $this->assertTrue($statusCode == 0);

        $infoFile = file_get_contents("$dir/testprofile.info");
        $this->assertRegExp('/name\s=\sTest profile/', $infoFile);
        $this->assertRegExp('/description\s=\sThis is a test profile/', $infoFile);
    }

    /**
     * Clean up after ourselves.
     */
    function tearDown()
    {
        $dir = __DIR__ . '/testprofile';
        if (is_dir($dir)) {
            exec("rm -rf $dir");
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