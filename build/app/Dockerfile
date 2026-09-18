FROM centos:7.9.2009

WORKDIR /var/www/html

ADD ./composer.json composer.json
ADD ./build/app/php.ini /etc/php.ini
ADD .env.sample .env
ADD package.json phpunit.xml server.php webpack.mix.js ./docker_specific/entrypoint.py ./

RUN yum -y install https://dl.fedoraproject.org/pub/epel/epel-release-latest-7.noarch.rpm && \
  yum -y install https://rpms.remirepo.net/enterprise/remi-release-7.rpm && \
  yum -y install yum-utils && \
  yum-config-manager --enable remi-php74 && \
  yum install -y httpd firewalld epel-release zip unzip \
    php php-common php-opcache php-mcrypt php-cli php-gd php-curl php-mysqlnd \
    mod_ssl php-cli php-zip wget unzip php-mbstring php-xml php-pdo mariadb-server \
    policycoreutils python3 && \
  yum --enablerepo=epel install -y perl-DateTime-TimeZone* && \
  mkdir /etc/httpd/ssl && \
  php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
  
ADD ./app app
ADD ./bootstrap bootstrap
ADD ./config config
ADD ./database database
ADD ./public public
ADD ./resources resources
ADD ./routes routes
ADD ./storage storage
ADD ./artisan artisan

RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
  /usr/local/bin/composer update && \
  /usr/local/bin/composer update php && \
  chmod 777 -R storage bootstrap/cache public/uploads && \
  php artisan storage:link

ADD ./docker_specific/httpd.conf /etc/httpd/conf 
#needs to be below httpd install or else it gets overwritten
EXPOSE 80

CMD ["python3", "/var/www/html/entrypoint.py"]
