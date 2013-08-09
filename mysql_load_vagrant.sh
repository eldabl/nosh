#!/bin/bash 
#
# By Daniel Blomqvist at Wunderkraut
#
# Create database and user and load database
#
# For project without installscripts which create database credentials
#

project=${1}

if [[ -z "$project" ]]; then
  echo "Woops, you did not enter the number of the project! Please do that first. Exiting."
  exit 1
fi

ip=$(cd ~/projects/${project}; less Vagrantfile | egrep -o "([0-9][0-9][0-9].[0-9][0-9][0-9].[0-9][0-9].[0-9])")
mysql_password=$(cd ~/projects/${project}/web/sites/default; php -r 'include "settings.php"; echo $databases["default"]["default"]["password"];')
#echo "${mysql_password}"

 
ssh vagrant@${ip} "mysql -uroot -ppassword -e 'create database ${project}_db1'"
ssh vagrant@${ip} "mysql -uroot -ppassword -e 'GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER, CREATE TEMPORARY TABLES, LOCK TABLES ON ${project}_db1.* TO '${project}_u1'@'localhost' IDENTIFIED BY '\''${mysql_password}'\'';'"
