#!/usr/bin/env bash

apt-get update
apt-get install -y  --no-install-recommends ssl-cert
rm -r /var/lib/apt/lists/*

a2enmod ssl
a2dissite 000-default.conf
#a2ensite apache2-vhosts.conf
a2ensite default-ssl.conf

# Setup local config for wordpress
cat > /var/www/html/local-config.php <<EOF
<?php

define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
EOF

mkdir /var/www/html/wp-content/mu-plugins
find /mu-plugins -type f -exec cp {} /var/www/html/wp-content/mu-plugins \;

# finally execute default command
docker-entrypoint.sh apache2-foreground
