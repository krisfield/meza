#!/bin/bash
#
# Setup PHP

print_title "Starting script php.sh"


# Install IUS repository
yum -y install https://centos7.iuscommunity.org/ius-release.rpm

# Install yum-plugin-replace and replace the php packages with php70u packages:
# yum install -y yum-plugin-replace
# yum -y replace --replace-with php70u php

# Install php70u packages
yum install -y \
	php70u \
	php70u-cli \
	php70u-common \
	php70u-devel \
	php70u-gd \
	php70u-pecl-memcache \
	php70u-pspell \
	php70u-snmp \
	php70u-xml \
	php70u-xmlrpc \
	php70u-mysqlnd \
	php70u-pdo \
	php70u-pear \
	php70u-pecl-jsonc \
	php70u-process \
	php70u-bcmath \
	php70u-intl \
	php70u-opcache \
	php70u-soap \
	php70u-mbstring \
	php70u-mcrypt


#
# Initiate php.ini
#
mv /etc/php.ini /etc/php.ini.default
ln -s "$m_config/core/php.ini" /etc/php.ini


#
# Start webserver service
#
chkconfig httpd on
service httpd status
service httpd restart

# # Install PEAR Mail
pear install --alldeps Mail

echo -e "\n\nPHP has been setup.\n\nPlease use the web browser on your host computer to navigate to http://192.168.56.56/info.php to verify php is being executed."
