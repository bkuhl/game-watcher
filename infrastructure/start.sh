#!/bin/sh

#
#   Used by within the Dockerfile to add the passed private key to the container
#

# Add private key to server
if [ "${PRIVATE_KEY}" != "**None**" ]; then
    echo "=> Found private key"
    mkdir -p /home/www-data/.ssh
    chmod 700 /home/www-data/.ssh
    echo "=> Adding private key to /home/www-data/.ssh/id_rsa"

    # private key is encoded since multiline environment variable support is janky
    echo -e "$PRIVATE_KEY" | base64 -d > /home/www-data/.ssh/id_rsa

    # avoid "The authenticity of host 'xxxxx' can't be established." when cloning
    echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> /home/www-data/.ssh/config

    chmod 600 /home/www-data/.ssh/id_rsa
    chown -R www-data:www-data /home/www-data
else
    echo "ERROR: No private keys found in \$PRIVATE_KEY"
   exit 1
fi