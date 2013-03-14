# [Nosh - The Arnold way](https://github.com/team-arnold/nosh#nosh---the-arnold-way-1)

# NodeStream Shell

NodeStream shell is a set of tools that can be used to easily get going with developing NodeStream-based profiles and projects.

## Dependencies
* [GIT](http://git-scm.com/)
* [Virtualbox](https://www.virtualbox.org/wiki/Downloads) 
* [Vagrant](http://downloads.vagrantup.com)
* [Drush](http://drupal.org/project/drush)
* curl (on Linux <code> sudo apt-get install curl</code>)

## Installing


### Manually
* Clone the repository

                git clone git@github.com:team-arnold/nosh.git ~/nosh


* Fetch composer (sensible defaults added to command here) 

                curl -s getcomposer.org/installer | php -d detect_unicode=Off -d date.timezone=UTC


* Install Composer

                cd ~/nosh
                ./composer.phar install


* Symlink Nosh to your bin

                sudo ln -s ~/Nosh/nosh.php /usr/bin/nosh


### Caveats
* it probably not a bad idea to have run (outside the ~/nosh dir) Vagrant before testing Nosh

                vagrant box add base http://files.vagrantup.com/precise64.box
                vagrant init
                vagrant up


! don't forget to stop the initial Vagrant box and optionally destroy it

### Mac OS X Nosh install helper script
Can be found [here](https://github.com/sjugge/mac_setup/blob/master/nosh_setup.sh). This script will guide you through setting up Nosh on Mac OS X.
A more streamlined version that supports Homebrew installs of Drush and Composer, as well as a helper script for Linux is on the way...

## Access and Credentials
* Access via browser to the web root is defined in the Vagrant file: 192.168.50.2, you can add this entry to <code>/etc/hosts</code>

Mac OS X GUI hint: [Hosts.prefpane](https://github.com/specialunderwear/Hosts.prefpane)

### MySQL
* host: 192.168.50.2
* user: root
* pass: password

### SSH
* host: 192.168.50.2
* user: vagrant
* pass: vagrant

## Setting up a drush alias to work with vagrant boxes

Download the private vagrant ssh key [here](https://raw.github.com/mitchellh/vagrant/master/keys/vagrant), then create an alias in ~/.drush/aliases.drushrc.php:

    <?php
	$aliases['dev'] = array(
	  'root' => '/var/www',
	  'db-url' => 'mysql://root:password@localhost/db',
      'remote-host' => '192.168.50.2',
      'remote-user' => 'vagrant',
         'ssh-options' => '-i /path-to-your-key/vagrant',
	   );
	?>

## Setting up projects with Nosh

### New Vagrant based project

                cd ~/My-Nosh-Projects
                nosh create-project foo_bar
                cd foo_bar
                vagrant up


# Nosh - The Arnold way

**ALERT: For "The Arnold way" to work your workspace will be: /home/user/projects
That is were you will pull down your project**

### Install Nosh
* Follow the above install instructions

### Preinstallation for Team Arnold, if you want to contribute to [repo](https://github.com/team-arnold/nosh)
* We need to generate a unique SSH key for our second GitHub account.
                
                ssh-keygen -t rsa -C "team-arnold"

* Now add your key to the team-arnold account at github. Login credentials are found in our secret file at the team docs. Simply “less” the .pub-key and att it under the account settings.

* Now we need a way to specify when we wish to push to our personal account, and when we should instead push to our company account. To do so, let’s edit the config file.



                cd /home/user/.ssh
                sudo nano config

add the following:
                
                #Default Github
                Host github.com
                        Hostname github.com
                        User     git
                        IdentityFile ~/.ssh/id_dsa

                Host github-team-arnold
                        Hostname github.com
                        User     git
                        IdentityFile ~/.ssh/id_rsa_team_arnold

* Time to try it out. Clone the testdir and make a commit:

                git clone git@github.com:team-arnold/test.git
                nano README.md

* Add that and write a funny commit message and push it like its hot!

### Installation on a project allready vagrantified

* Get a drush alias to enable drush access from your local environment
* run the script nosh_key_setup.sh found in this repo to download the ssh-key for vagrant
* **ALERT: By whatnot reason this key fails on my computer, I just “vagrant ssh” and add one of my pubkeys to authorized_keys and everything works dandy fine!**
* run the script  site-alias.sh and add sitenumber as first argument and sitenumber again but with a dot as second argument. Example: 

                ./site_alias.sh 257 25.7

* You use the alias by simply adding “@xxx” after drush. ex; 

                drush @257 cc all

* Create a directory for your projects and Clone that shit!
* Type:

                vagrant up

    and all the puppet modules and sweet stuff for the box will be created for you!
* For build simply go to where your buildscript is and:

                ./build

* For install type: 
        
                drush @xxx install 

    (where xxx is whatever you set the alias to)

* **If you in your install script havent added password for mysql it wont work, then simply add that!**

* To use hosts and get to the site you need to symlink the vhostfile to /etc/apache2/sites-available and then a2ensite loc.whatever.se and then service apache2 reload

### Use Nosh vagrantify on “non-vagrantified” projects

* Clone that project
* Use nosh vagrantify and specify everything with the different flags:

                nosh vagrantify --help

IF you intend to use the script to create drushalias the webroot needs to be set to “/srv/www/xxx” where xxx is the projectnumber

* When done you will have manifests and a VagrantFile and a .vagrantfile.
* Commit that shit to the repo and the next happy user will just be able to perform the [previous instructions](https://github.com/team-arnold/nosh#installation-on-a-project-allready-vagrantified)

**Enjoy folks!**
