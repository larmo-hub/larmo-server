#!/usr/bin/env bash

VAGRANT_IP="192.168.33.10"
LOG="track_bootstrap_file.txt"
DEFAULT_WEBROOT="\/var\/www\/html"
LARMO_WEBROOT="\/var\/www\/public"
OVERRIDE_NONE="AllowOverride None"
OVERRIDE_ALL="AllowOverride All"

touch $LOG
echo -e "Larmo Hub - Server"

apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 7F0CEB10
echo "deb http://repo.mongodb.org/apt/ubuntu trusty/mongodb-org/3.0 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.0.list
echo "> added key and repo for mongodb" >> $LOG

apt-get update
echo "> packages updated" >> $LOG

apt-get install -y mongodb-org
service mongod start
echo "> installed mongodb server" >> $LOG

apt-get install -y curl
echo "> curl installed" >> $LOG

apt-get install -y apache2 apache2-mpm-prefork apache2.2-bin
echo "> apache server installed" >> $LOG

apt-get install -y php5 php5-common php5-cli php5-mongo php5-curl php5-gd libapache2-mod-php5
echo "> php5 installed" >> $LOG

a2enmod rewrite
echo "> enable apache module - rewrite" >> $LOG

service apache2 restart
echo "> restart services" >> $LOG

sed -i "s/$DEFAULT_WEBROOT/$LARMO_WEBROOT/g" /etc/apache2/sites-available/000-default.conf
sed -i "s/$OVERRIDE_NONE/$OVERRIDE_ALL/g" /etc/apache2/apache2.conf
echo "ServerName $VAGRANT_IP" > /etc/apache2/httpd.conf
echo "> changed 'Webroot', 'ServerName' and 'Override' option" >> $LOG

rm /var/www/index.html
rm -rf /var/www/html
echo "> removed unnecessary Apache files" >> $LOG

service apache2 restart
echo "> restart services" >> $LOG