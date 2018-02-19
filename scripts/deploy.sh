#!/bin/bash
echo 'Deploying Vault'
cd /tmp
tar xzf package.tgz
cd /srv
mv /tmp/tmp/build ./new
sudo rm -rf vault_old
mv vault vault_old
mv new vault
cd /srv/vault
ln -s /srv/vault/env/vault.env .env
touch /srv/vault/storage/logs/lumen.log

# sensitive files
rm -rf scripts
rm -rf node_modules
rm -rf scripts
rm -rf tests
rm -rf .git
rm -f .gitignore
rm -f .gitattributes
rm -f composer.*
rm -f .travis.yml
rm -f coverage.xml
rm -f phpunit.xml
rm -f README.md
rm -f auth.json
rm -f mysql-apt-config_*

# owners and permissions
sudo chown -R vault:www-data /srv/vault
sudo chown -R www-data:www-data /srv/vault/storage/logs
sudo chown -R www-data:www-data /srv/vault/storage/framework/views
sudo chown -R www-data:www-data /srv/vault/storage/framework/cache
sudo chmod 775 /srv/vault/storage/logs
sudo chmod 777 /srv/vault/storage/logs/lumen.log
sudo chmod 775 /srv/vault/storage/framework/views
sudo chmod 775 /srv/vault/storage/framework/cache

# go
php artisan migrate
php artisan db:seed
