#!/bin/sh

# This file will be run as the entrypoint and install the GPG-Key to the `www-data` users Keyring

# Clear all existing gpg keys, in keys there are some leftover
rm -rf /var/www/.gnupg

if [ ! -f "/opt/pgp-key.private" ]; then
    echo "Could not open '/opt/pgp-key.private' file does not exist!"
    echo "Make sure to add your Repositories Private-Key to the container"
    exit 1
fi

su www-data -s /bin/sh -c "cat /opt/pgp-key.private | gpg --import"

# Execute the default entrypoint
php-fpm
