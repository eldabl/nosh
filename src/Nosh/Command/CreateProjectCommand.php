<?php
/**
 * @file
 * Contains the CreateProjectCommand.
 */
namespace Nosh\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Fabsor\DrupalReleaseApi\HTTPReleaseFetcher;

/**
 * Command for creating projects (also called platforms).
 */
class CreateProjectCommand extends BaseCommand
{
    var $options = array(
        'profile-name' => 'The name of a profile to create',
        'profile-title' => 'The title of a profile',
        'profile-description' => 'A profile description',
    );

    protected function configure()
    {
        $this->setName('create-project')
            ->setDescription('Create a NodeStream project (platform) with Drupal core and potentially an installation profile.')
            ->addArgument('path', InputArgument::REQUIRED, 'The path to the project');

        foreach ($this->options as $option => $description) {
            $this->addOption($option, null, InputOption::VALUE_OPTIONAL, $description, false);
        }
        $this->addOption('use-vagrant', null, InputOption::VALUE_NONE, "Use vagrant for this project");
        $this->addOption('create-profile', null, InputOption::VALUE_NONE, "Create an installation profile");
        $this->addOption('build-profile', null, InputOption::VALUE_NONE, "Build the installation profile");
        $this->addOption('api', null, InputOption::VALUE_OPTIONAL, 'API version. Defaults to 7.x', '7.x');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $path = $input->getArgument('path');
        $quiet = $input->getOption('no-interaction');
        if (is_dir($path)) {
            throw new \Exception("The directory already exists.");
        }
        mkdir($path);
        $dialog = $this->getHelperSet()->get('dialog');
        $twig = $this->getTwig();
        // Fetch Drupal.
        $output->writeln("Fetching Drupal");
        $fetcher = new HTTPReleaseFetcher();
        $api = $input->getOption('api');
        $release = $fetcher->getReleaseInfo('drupal', $api)->getCurrentRelease();
        $drupalIdentifier = "drupal-{$release['version']}";
        $this->executeExternalCommand("drush dl $drupalIdentifier --destination={$path}", $output);
        $this->executeExternalCommand("mv {$path}/{$drupalIdentifier} {$path}/web", $output);
        if ($api == '7.x' && ($input->getOption('create-profile') || (!$quiet && $dialog->askConfirmation($output, '<question>Do you want to create an installation profile?</question> ')))) {
            $arguments = array(
                'command' => 'create-profile',
                '--title' => $input->getOption('profile-title'),
                '--description' => $input->getOption('profile-description'),
            );
            $profile_name = $input->getOption('profile-name');
            if (empty($profile_name)) {
                $profile_name = $dialog->ask($output, '<question>Enter the name of the profile:</question> ');
            }
            $arguments['path'] = $profile_path = $path . '/web/profiles/' . $profile_name;
            $command = $this->getApplication()->find('create-profile');
            $cmdInput = new ArrayInput($arguments);
            $returnCode = $command->run($cmdInput, $output);
            if ($input->getOption('build-profile') || (!$quiet && $dialog->askConfirmation($output, '<question>Do you want to build your profile now?</question> '))) {
                $output->writeln("Building installation profile...");
                $this->executeExternalCommand("drush make -y --no-core --contrib-destination={$profile_path} {$profile_path}/{$profile_name}.make", $output);
            }
        }
        $variables = array('core_version' => '7.15');
        file_put_contents($path . '/' . 'platform.make', $twig->render('project/platform.make', $variables));
        file_put_contents($path . '/' . '.gitignore', $twig->render('project/gitignore', $variables));
        file_put_contents($path . '/' . 'build', $twig->render('project/build', $variables));
        $this->executeExternalCommand('chmod +x ' . $path . '/' . 'build', $output);
        if ($input->getOption('use-vagrant') || (!$quiet && $dialog->askConfirmation($output, '<question>Do you want to use vagrant for this project?</question> '))) {
            $command = $this->getApplication()->find('vagrantify');
            $arguments = array(
                'command' => 'create-profile',
                '--path' => $path,
            );
            $input = new ArrayInput($arguments);
            $returnCode = $command->run($input, $output);
        }
    }
}
