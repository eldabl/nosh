# Nosh - The Arnold way

**ALERT: For "The Arnold way" to work your workspace will be: ~/projects
That is were you will pull down your projects**

* There are som usefull scripts in this repo, check them out!

### Install Nosh
* Follow the [install instructions](https://github.com/team-arnold/nosh#nodestream-shell)

### Preinstallation for Team Arnold, if you want to contribute to [repo](https://github.com/team-arnold/nosh) If not, skip to the next step. If this step fail look at [this](http://net.tutsplus.com/tutorials/tools-and-tips/how-to-work-with-github-and-multiple-accounts/)
* We need to generate a unique SSH key for our second GitHub account.
                
                ssh-keygen -t rsa -C "team-arnold"

* Now add your key to the team-arnold account at github. Login credentials are found in our secret file at the team docs. Simply “less” the .pub-key and att it under the account settings.

* Now we need a way to specify when we wish to push to our personal account, and when we should instead push to our company account. To do so, let’s edit the config file.



                cd ~/.ssh
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

* Add that and write a funny commit message and push it like it's hot!

### Installation on a project already vagrantified

* Get a drush alias to enable drush access from your local environment by running the script  site-alias.sh found in the nosh repo and add sitenumber as first argument and sitenumber again but with a dot as second argument. Example: 

                ./site_alias.sh 257 25.7

* Run the script nosh_key_setup.sh found in this repo to download the ssh-key for vagrant
* **ALERT: By whatnot reason this key fails on my computer, I just “vagrant ssh” and add one of my pubkeys to authorized_keys and everything works dandy fine!**

* Create a directory for your projects and Clone the 257 repo for testing!
* Type:

                vagrant up

    and all the puppet modules and sweet stuff for the box will be created for you! (Ubuntu: NFS mount do not work on encrypted home folders. Set up project folder somewhere else.)
* For build simply go to where your buildscript is and:

                ./build

* We use the drush-alias because we want to do all of our work in one place, ie. not ssh in and out of the vagrant-box

* For usage of the drush-alias you simply type "drush" adding “@xxx” after drush. ex; 

                drush @257 status

* Now, try the above command!

* For install type: 
        
                drush @257 -v si habilitering --db-url=mysql://root:password@localhost/257_db1 -y
 
    (observe that we have specified a password to the root mysql user, this is standard on new mysql versions)

* To use hosts on your local machine and get to the site you need to ssh into vagrant and symlink the vhostfile to /etc/apache2/sites-available and then a2ensite loc.whatever.se and then service apache2 reload:

                vagrant ssh
                cd /etc/apache2/sites-availible
                sudo ln -s /path/to/vhost
                sudo a2ensite vhostname
                sudo service apache2 reload


* Observe that on project without installscript, such as bostadsrätterna, you will have to create the database and user in mysql on your vagrantbox

                vagrant ssh
                mysql -uroot -ppassword
                mysql> create database databasename;
                mysql> GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES ON databasename.* TO 'username'@'localhost' IDENTIFIED BY 'password';
                mysql> exit;

* Then load a database on the vagrantbox:

                mysql -uroot -ppassword databasename < database.mysql


### Use Nosh vagrantify on “non-vagrantified” projects

* Clone that project
* Use nosh vagrantify and specify everything with the different flags:

                nosh vagrantify --help

* For a team arnold member those flags would be:

                nosh vagrantify --webroot="." --nfsroot="/srv/www/xxx" --hostname="projectname" --ip="192.168.xx.x"

Where xxx represents the projectnumber

If you intend to use the script to create drushalias the webroot needs to be set to “/srv/www/xxx” where xxx is the projectnumber

* When done you will have manifests and a VagrantFile and a .vagrantfile.
* Commit that shit to the repo and the next happy user will just be able to perform the [previous instructions](https://github.com/team-arnold/nosh#installation-on-a-project-allready-vagrantified)

**Enjoy folks!**
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

* the file may end up in your home directory, if so, move it to the nosh-directory

* Install Composer

                cd ~/nosh
                ./composer.phar install


* Symlink Nosh to your bin

                sudo ln -s ~/nosh/nosh.php /usr/bin/nosh


### Caveats
* it probably not a bad idea to have run (outside the ~/nosh dir) Vagrant before testing Nosh

                vagrant box add base http://files.vagrantup.com/precise64.box
                vagrant init
                vagrant up


! don't forget to stop the initial Vagrant box and optionally destroy it

### Mac OS X Nosh install helper script 
Can be found [here](https://github.com/team-arnold/nosh/blob/master/nosh_setup.sh). This script will guide you through setting up Nosh on Mac OS X.
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

# If you prefer to make your own alias instead of using the script in Team Arnolds How To here it is, remember to check the credentials  for it in the Vagrantfile of an already vagrantified project if using it on such.

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



