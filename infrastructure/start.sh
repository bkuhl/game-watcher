#!/bin/sh

#
#   Used by within the Dockerfile to add the passed private key to the container
#

# Set global git config
git config --global user.email "${RELEASER_EMAIL}"
git config --global user.name "${RELEASER_NAME}"

# Add private key to server
if [ "${PRIVATE_KEY}" != "**None**" ]; then
    echo "=> Found private key"
    mkdir -p /root/.ssh
    chmod 700 /root/.ssh
    echo "=> Adding private key to /root/.ssh/id_rsa"

    # private key is encoded since multiline environment variable support is janky
    echo -e "$PRIVATE_KEY" | base64 -d > /root/.ssh/id_rsa

    # avoid "The authenticity of host 'xxxxx' can't be established." when cloning
    echo -e "Host github.com\n\tStrictHostKeyChecking no\n" >> /root/.ssh/config

    chmod 600 /root/.ssh/id_rsa
else
    echo "ERROR: No private keys found in \$PRIVATE_KEY"
   exit 1
fi