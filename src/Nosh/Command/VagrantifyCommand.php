<?php
namespace Nosh\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for creating projects (also called platforms).
 */
class VagrantifyCommand extends BaseCommand
{
    protected $variableOptions = array(
        'webroot',
        'ip',
        'hostname',
        'boxname',
        'boxurl',
        'nfsroot',
    );

    protected function configure()
    {
        $this->setName('vagrantify')
            ->setDescription('Add a vagrant configuration capable of running Drupal sites.')
            ->addOption('path', null, InputOption::VALUE_OPTIONAL, "The path to the project. The current working directory will be used if this isn't specified.", ".")
            ->addOption('hostname', null, InputOption::VALUE_OPTIONAL, "The hostname of the virtual machine. Defaults to devbox.dev", "devbox.dev")
            ->addOption('webroot', null, InputOption::VALUE_OPTIONAL, "The drupal web root. Defaults to web.", './web')
            ->addOption('ip', null, InputOption::VALUE_OPTIONAL, "IP Address of the new box. Defaults to 192.168.50.2", "192.168.50.2")
            ->addOption('boxname', null, InputOption::VALUE_OPTIONAL, "Box name. Defaults to precise64.", 'precise64')
            ->addOption('boxurl', null, InputOption::VALUE_OPTIONAL, "Box URL. Defaults to http://files.vagrantup.com/precise64.box", 'http://files.vagrantup.com/precise64.box')
            ->addOption('nfsroot', null, InputOption::VALUE_OPTIONAL, "NFS mount point on the virtual machine.", '/var/www');


    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Hardcoded modules (for now).
        $modules = array(
            'systools' => 'https://github.com/nodeone/puppet-systools.git',
            'apache' => 'https://github.com/nodeone/puppet-apache',
            'drush' => 'https://github.com/nodeone/puppet-drush.git',
            'mysql' => 'https://github.com/nodeone/puppet-mysql.git',
            'php' => 'https://github.com/nodeone/puppet-php.git',
            'postfix' => 'https://github.com/nodeone/puppet-postfix.git',
            'mongodb' => 'https://github.com/WKLive/puppet-mongodb.git',
            'bundler' => 'git@github.com:nodeone/puppet-bundler.git'
        );
        $path = $input->getOption('path');
        chdir($path);
        $dialog = $this->getHelperSet()->get('dialog');
        $twig = $this->getTwig();
        // Create a manifests folder.
        if (!is_dir("manifests")) {
            mkdir("manifests");
        }
        if (!is_dir("manifests/modules")) {
            mkdir("manifests/modules");
        }
        // Get latest version of all modules.
        foreach ($modules as $name => $module) {
            $module_path = "manifests/modules/$name";
            if (is_dir($module_path)) {
                $this->executeExternalCommand("rm -rf {$module_path}", $output);
            }
            $this->executeExternalCommand("git clone $module $module_path", $output);
            // Remove the git repository to avoid conflicts.
            $this->executeExternalCommand("rm -rf $module_path/.git", $output);
        }
        $variables = array();
        foreach ($this->variableOptions as $option) {
            $variables[$option] = $input->getOption($option);
        }
        // Generate vagrantfile and manifest.
        file_put_contents('Vagrantfile', $twig->render("vagrant/Vagrantfile", $variables));
        file_put_contents('manifests/manifest.pp', $twig->render("vagrant/manifest.pp", $variables));
    }
}
