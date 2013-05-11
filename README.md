# Linux learn map

This project should allow students of a Linux curse to get a quick overview of all exercises as
well as automatic feedback for completed exercises.

The exercises are described in simple text files, one per execise. To test if an exercise is complete
a "check" script is uploaded to a server via SSH and then executed. If the return value is 0 the exercise
is complete.


## Setup

The setup is outlined for a Debian based system:

1. Install Apache 2 webserver with PHP and SSH2 PHP module:
   
	apt-get install apache2 libapache2-mod-php5 libssh2-php
   
   You can use the `apache2-mpm-prefork` package if you don't have a high load and want to save some memory.
   
   For local development it can be helpful to set `display_errors=On` in `/etc/php5/apache2/php.ini`. Please
   don't do this on a live system.
   
2. Checkout the repository:
   
	git clone https://github.com/arkanis/linux-learn-map.git
	cd linux-learn-map
   
3. Make the `public` directory accessable via the webserver. Either by setting a `DocumentRoot` to the
   public directory or by defining an alias.
   
   The webserver also needs read-only access to most directories in the repository. Easiest way:
   
	chgrp -R www-data .
   
4. Copy the `sample.htaccess` file in the public directory to `.htaccess` and configure the access control.
   For local development a local htpasswd file is useful. For productive deployment you might want to use
   LDAP authentication.
   
5. Create a private/public key pair in the `keys` directory and configure the paths in `config.php`.
   **Don't set a password for the private key!** The webserver will use the private key to open an SSH
   connection to the student VM every time a student checks the exercise.
   
   Add the public key to the `authorized_keys` file of your student virtual machines.
   
6. Thats it for now.


## Writing new exercises

By default all exercises are stored in the `exercises` directory. Each subdirectory that contains a file
named `check` is interpreted as an exercise. No matter how deeply nested the directories are.

A file named `exercise.txt` in the same directorie contains additional information about the exercise. In
the simplest form this file looks like this:

	Title: Install an Apache Webserver
	
	Description written in Markdown
	...

How to write Markdown: [Markdown introduction][md-intro], [php Markdown Extra][md-extra]

[md-intro]: http://daringfireball.net/projects/markdown/
[md-extra]: http://michelf.ca/projects/php-markdown/extra/