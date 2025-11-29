#!/bin/bash
set -e

# Enable basic auth if config file exists
if [ -f /etc/apache2/conf-available/phpmyadmin-basic-auth.conf ]; then
    echo "Enabling phpMyAdmin HTTP Basic Authentication..."
    a2enconf phpmyadmin-basic-auth
fi

# Run the original phpMyAdmin entrypoint
exec /docker-entrypoint.sh apache2-foreground
