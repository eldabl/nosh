#!/bin/bash
#
# By Daniel Blomqvist at Wunderkraut
#
# To use Nosh you need a drush alias. This
# script will create the alias for you! It also changes the last number in the ip
# address to you projectnumber. As ip write the projectnumnber as xx.x ie; 25.7



project=${1}
ip=${2}


# Make sure we have required parameter.
if [[ -z "$project"  ]]; then
  echo "I am so sorry my Commander, you forgot to enter the projectnumber! Exiting."
  exit 1
fi
if [[ -z "$ip"  ]]; then
  echo "I am so sorry my Commander, you forgot to enter the projectnumber! Exiting."
  exit 1
fi


cd ~/.drush

echo "My Excellence, if you wish to use the drush alias just type 'drush @'projectnumber' cc all"

echo "<?php
\$aliases['${project}'] = array(
  'root' => '/srv/www/${project}/web',
  'db-url' => 'mysql://root:password@localhost/db',
  'remote-host' => '192.168.${ip}',
  'remote-user' => 'vagrant',
  'ssh-options' => '-i ~/.ssh/vagrant',
);" > "${project}".aliases.drushrc.php

