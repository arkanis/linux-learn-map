== TODO ==
exit status "check" scripts

apache: 
vhost change existing
logfiles & debugging
vhost create new
config file includes


tasks: localhost ersetzen durch linuxvmXXX

VirtualHost einfügen
LDAP Auth: VHOST, a2enmod authnz_ldap
apache.txt

== Setup ==

Install Server Files
$ scp -r linux-learn-map root@bongo.mi.hdm-stuttgart.de:/var/www

Install dependencies
sudo apt-get install apache2-mpm-prefork 
sudo apt-get install libapache2-mod-php5
sudo apt-get install libssh2-php:amd64

Edit php.ini 
# vi /etc/php5/apache2/php.ini 
display_errors=On

Create VirtualHost 
for DocumentRoot /var/www/linux-learn-map/public

Give read access for webserver
sudo chown -Rv root.www-data /var/www/linux-learn-map/
sudo chmod -Rv g=rx /var/www/linux-learn-map/public

Check permissions for /var/www/linux-learn-map/keys
sudo chmod 600 -v /var/www/linux-learn-map/keys/*
sudo chown -v www-data.www-data keys/*

sudo service apache2 restart

SSH Config
copy keys/test.pub to target linuxvmXXX
Check permissions of keys/*

Adjust server name
root@bongo:/var/www/linux-learn-map/include/config.php 

Prerelease: Remove resets stylesheet from index.php
	<!-- <link rel="stylesheet" href="reset.css"> -->


Markdown sytax for task.txt files
http://daringfireball.net/projects/markdown/syntax
http://michelf.ca/projects/php-markdown/extra/


Demo installation
http://events.mi.hdm-stuttgart.de/linux-learn-map/public/
