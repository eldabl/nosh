#!/bin/bash

# Nosh setup script for Mac OS X

# This script offers a menu with all the steps that are required to get Nosh up and running on a clean Mac OS X
# Some Dependencies may be skipped if they are already met. Note that it's recommended to run Vagrant before using Nosh.
# A reboot after completing the install is also recommended.

# Last updated: 2012/11/10
# sjugge@heretiksambrosia.net

# looking good
clear
# go home
cd

echo "###########################################"
echo "# Nosh install helper script for MAC OS X #"
echo "###########################################"
echo ""
# Set Nosh build location
echo "# Required setup variable"
echo "In which existing directory do you want to build Nosh?"
echo "e.g. Workspace"
read -e NOSHLOCATION
echo ""  
echo "In order to get Nosh up and running, some dependencies need to be met."
echo "If these are already met, you can proceed with getting Nosh & Composer and the install."
echo ""

# Log select menu
showMenu () {
    echo "# Dependencies"
    echo "1 Virtualbox"
    echo "2 Vagrant"
    echo "3 Apple Command Line Tools"
    echo "4 Drush"
    echo ""
    echo "# Get Nosh and Composer"
    echo "5 get Nosh"
    echo "6 get Composer"
    echo ""
    echo "# Install"
    echo "7 install"
    echo ""
    echo "0 exit"
}

while [ 1 ]
do
        showMenu
        read CHOICE
        case "$CHOICE" in
            "1")  echo "1 Virtualbox"
                  echo "Nosh requires Virtualbox, a browser tab will be opened for you to select your download."
                  echo "Please complete the download and Virtualbox install."
                  sleep 8
                  open https://www.virtualbox.org/wiki/Downloads
                  echo "Then move on with the next step of this script."
                  echo ""
                  ;;
            "2")  echo "2 Vagrant"
                  echo "Nosh requires Vagrant, a browser tab will be opened for you to select your download."
                  echo "Please complete the download and Vagrant install."
                  sleep 8
                  open http://downloads.vagrantup.com
                  echo "Then move on with the next step of this script."
                  echo ""
                  ;;
            "3")  echo "3 Apple's Command Line Tools"
                  echo "Nosh has a dependency on GIT, this is fixed by installing the Command Line Tools for Mac OS X."
                  echo "Either install these manually through Xcode or via the Developer downloads provide on developer.apple.com."
                  echo "Then move on with the next step of this script."
                  echo ""
                  ;;
            "4")  echo "4 Drush"
                  echo "Nosh requires Drush to be installed."
                  echo "Note that in order to install Drush, you'll need to have Apple's Command Line Tools installed"
                  # Set Drush build location
                  echo "Where do you want to build Drush?"
                  echo "e.g. ~/Workspace"
                  read -e DRUSHLOCATION
                  # go to Workspace
                  cd $DRUSHLOCATION
                  wait
                  # clone drush
                  echo "Cloning Drush..."
                  git clone --recursive --branch 7.x-5.x http://git.drupal.org/project/drush.git
                  wait
                  # make drush executable
                  echo "Making Drush executable..."
                  cd
                  chmod u+x ~/$DRUSHLOCATION/drush/drush
                  # symlink drush to your bin
                  echo "Symlinking drush to /usr/bin/drush"
                  sudo ln -s ~/$DRUSHLOCATION/drush/drush /usr/bin/drush
                  # add drush to your PATH
                  echo "Adding drush to your PATH..."
                  export PATH="$PATH:$DRUSHLOCATION/drush:/usr/local/bin"
                  # round up
                  echo "Drush install completed"
                  echo "Test Drush, then move on with the next step of this script."
                  echo ""
                  ;;
            "5")  echo "5 get Nosh"
                  cd
                  wait
                  cd $NOSHLOCATION
                  wait
                  echo "cloning Nosh..."
                  git clone git@github.com:team-arnold/nosh.git
                  wait
                  echo ""
                  ;;
            "6")  echo "6 get Composer"
                  echo "Downloading the Composer PHP dependency management tool..."
                  cd
                  wait
                  cd $NOSHLOCATION/nosh
                  wait
                  curl -s getcomposer.org/installer | php -d detect_unicode=Off -d date.timezone=UTC
                  wait
                  echo ""
                  ;;
            "7")  echo "7 install"
                  cd
                  wait
                  cd $NOSHLOCATION/nosh
                  echo "Installing Composer..."
                  ./composer.phar install
                  wait
                  # symlink Nosh to bin
                  wait
                  cd
                  wait
                  echo "Symlinking Nosh to bin..."
                  sudo ln -s ~/$NOSHLOCATION/nosh/nosh.php /usr/bin/nosh
                  echo "Adding nosh to your PATH..."
                  export PATH="$PATH:$NOSHLOCATION/nosh:/usr/local/bin"
                  ;;
             "0") echo ""
                  echo "# Note"
                  echo "Make sure Vagrant has run at least once before testing Nosh:"
                  echo ""
                  echo "$ vagrant box add base http://files.vagrantup.com/precise64.box"
                  echo "$ vagrant init"
                  echo "$ vagrant up"
                  echo ""
                  echo "Then make sure to Power off the Vagrant VM instance."
                  echo "Reboot your machine and you should be good to go."
                  echo ""
                  exit
                  ;;
        esac
done