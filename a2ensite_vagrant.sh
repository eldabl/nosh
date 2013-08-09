#!/bin/bash 
#
# By Daniel Blomqvist at Wunderkraut
#
# enable vhost on vagrant script


project=${1}

if [[ -z "$project" ]]; then
  echo "Woops, you did not enter the number of the project! Please do that first. Exiting."
  exit 1
fi

ip=$(cd ~/projects/${project}; less Vagrantfile | egrep -o "([0-9][0-9][0-9].[0-9][0-9][0-9].[0-9][0-9].[0-9])")
vhosts=$(ssh vagrant@${ip} "cd /srv/www/*/vhosts/; ls")

echo "# Required setup variable"
echo "Which vhost file do you want to enable?"
echo "Simply type the one you want and press 'enter'"
echo "e.g. loc.anysite.se"
echo "The following are available:"
echo "${vhosts}"
read -e VHOSTFILE

ssh vagrant@${ip} "cd /etc/apache2/sites-available/; sudo ln -s /srv/www/${project}/vhosts/${VHOSTFILE}"
if ssh vagrant@${ip} "sudo a2ensite ${VHOSTFILE}; sudo service apache2 reload"
then
	echo "${VHOSTFILE} enabled and apache reloaded"
else
	echo "Things did not go so well, perhaps ypu miss spelled the vhostfile, talk to yor system administrator or do it the hard way"
fi

