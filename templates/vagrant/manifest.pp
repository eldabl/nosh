class { 'systools': }
class { 'apache': }
class { 'php':
  development => true
}
class { 'drush': }
class { 'postfix': }
class { 'mongodb': }
class { 'bundler': }


class { 'mysql':
  local_only     => true,
  hostname => "{{ hostname }}"
}

{% for host in vhost %}
file { "/etc/apache2/sites-enabled/{{ host.basename }}": ensure => 'link', target => "{{nfsroot}}/{{ host.path }}" }
{% else %}
{% endfor %}