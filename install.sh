if [ -d ~/nosh ]
then
  printf '\033[0;33m%s\033[0m%s\n' 'You already have nosh installed.' 'You will need to remove ~/nosh if you want to install'
  exit
fi

printf '\033[0;34m%s\033[0m\n' 'Cloning nosh...'
hash git >/dev/null && /usr/bin/env git clone https://github.com/WKLive/nosh.git ~/nosh || {
  printf 'git not installed.'
  exit
}

if pushd ~/nosh > /dev/null;
then
  curl -s getcomposer.org/installer | php -d detect_unicode=Off -d date.timezone=UTC
  
  ./composer.phar install

  printf '\033[0;34m%s\033[0m\n' 'Symlinking nosh to bin'
  if [ -h /usr/bin/nosh ]
  then
    printf '\033[0;33m%s\033[0m\n' 'Existing symlink detected, please confirm removal of it'
    sudo rm /usr/bin/nosh
  fi
  sudo ln -s ~/nosh/nosh.php /usr/bin/nosh
  printf '\033[0;34m%s\033[0m\n' 'Adding nosh to your PATH...'
  export PATH="$PATH:$NOSHLOCATION/nosh:/usr/local/bin"

  popd > /dev/null;
fi

printf '\033[0;32m%s\033[0m\n' '                       __   '
printf '\033[0;32m%s\033[0m\n' '    ____  ____   _____/ /_  '
printf '\033[0;32m%s\033[0m\n' '   / __ \/ __ \ / ___/ __ \ '
printf '\033[0;32m%s\033[0m\n' '  / / / / /_/ /(__  ) / / / '
printf '\033[0;32m%s\033[0m\n' ' /_/ /_/\____/ /___/_/ /_/  '
printf '\n \033[0;32m%s\033[0m\n' '....is now installed and ready to be used.'

