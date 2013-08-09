#!/bin/bash
#
# By Daniel Blomqvist at Wunderkraut
#
# To use Nosh you need a vagrant ssh key. This
# script will fetch the key for you!


echo "Great Master, I shall now fetch your vagrant key"

cd ~/.ssh
wget https://raw.github.com/mitchellh/vagrant/master/keys/vagrant
