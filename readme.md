
## About this DBS application

This application was developed using the LAMP stack with Laravel being the base framework. It has been built to work under SSL and it is using a SOAP API connection to DBS that is restricted to a specific certificate and key that are not included in this repo

## Installation

This application has been tested locally using a XAMPP installation and in production using a CentOs7 server.  It is running on PHP 7.4 and MariaDB 10.4<br />
There are certain requirements that have to be satisfied before the application can run on a newly installed server, please see the installation details bellow.<br />
The application has not yet been tested on a Windows server running IIS.


## Server Dependencies

###1. install vim
```
sudo yum install vim-enhanced -y
```

###2. install Apache
```
sudo yum update httpd
sudo yum install httpd
sudo systemctl start httpd.service
sudo systemctl enable httpd.service // apache start on boot
systemctl status httpd
```

###3. add exceptions to the firewall
```
sudo firewall-cmd --permanent --add-service=http
sudo firewall-cmd --permanent --add-service=https
sudo firewall-cmd --reload
sudo setsebool -P httpd_can_network_connect on // SMTP connect
```

###4. install MySql (10.4.18-MariaDB)
[Web Guide](https://computingforgeeks.com/how-to-install-mariadb-on-centos/);
```
sudo systemctl enable --now mariadb // enable start on boot
sudo mysql_secure_installation

CREATE DATABASE dbs;
CREATE USER 'dbsuser'@localhost IDENTIFIED BY 'XXXXXXX';
GRANT ALL PRIVILEGES ON dbs.* TO 'dbsuser'@localhost;
FLUSH PRIVILEGES;

sudo setsebool -P httpd_can_network_connect_db 1
```

###5. install php (PHP 7.4)
```
sudo yum install epel-release yum-utils -y 
sudo yum install http://rpms.remirepo.net/enterprise/remi-release-7.rpm 
sudo yum-config-manager --enable remi-php74 

sudo yum install php php-common php-opcache php-mcrypt php-cli php-gd php-curl php-mysql -y 
sudo yum search php | more
php -v
```

###6. configure php.ini
```
sudo vim /etc/php.ini
	=> memory_limit = 2048M
	=> post_max_size = 15M
	=> upload_max_filesize = 15M
sudo systemctl restart httpd
```

###7. install SSL
```
sudo yum install mod_ssl
sudo yum --enablerepo=epel install perl-DateTime-TimeZone*
sudo mkdir /etc/httpd/ssl
**sudo cp /tmp/[zip name] /etc/httpd/ssl
**sudo unzip [zip name]
**sudo openssl x509 -x509toreq -in [cert_name].crt -out [same_cert_name].csr -signkey [same_cert_name].key
sudo vim /etc/httpd/conf.d/ssl.conf
** change the cert and key paths
sudo apachectl configtest
```

## App Dependencies
```
sudo yum install vim-enhanced -y
sudo yum install -y zip unzip
```

###1. composer
```
sudo yum install php-cli php-zip wget unzip
sudo php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
HASH="$(wget -q -O - https://composer.github.io/installer.sig)"
sudo php -r "if (hash_file('SHA384', 'composer-setup.php') === '$HASH') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer
```

###2. laravel requirements
ext-mbstring
```
sudo yum install php-mbstring
```
ext-dom
```
sudo yum install php-xml
```

###3. Restart Apache
```
sudo /usr/local/bin/composer update
sudo /usr/local/bin/composer update php
```

###4. install PDO
```
yum -y install php-mysqlnd php-pdo
```

###5. laravel storage and log folder access
change current folder to the web folder `cd /var/www/html`
```
sudo chmod 777 -R storage bootstrap/cache public/uploads
```
###6. link storage folder
```
sudo php artisan storage:link
```
###7. SELinux access
```
chcon -R -t httpd_sys_rw_content_t storage
```

## Configure the application
Create a database and install the database schema and minimum data found in `database-schema.sql`. Rename the `.env.sample` file to `.env` and set the app details and database logins. Configure the RO details id the settings table.

## Responsables and contacts

Support: [? ?](mailto:info@bluescreenit.co.uk')
Code maintenance and Development [Ion POPESCU](mailto:contact@startsoft.co.uk).

## Security Vulnerabilities

All security vulnerabilities have been addressed up to date. Last recurity vulnerability review was done on 16 July 2021 and the issues highlited were fixed and deployed 18 July 2021

## License

This application was built for [BluescreenIT](https://www.bluescreenit.co.uk/) and all copyrights belong to [BluescreenIT](https://www.bluescreenit.co.uk/).
