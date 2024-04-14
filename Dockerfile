FROM php:5.6-fpm

# Following LibreNMS Linux Server instructions

# fix apt problem
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list
RUN sed -i '/stretch-updates/d' /etc/apt/sources.list
RUN sed -i '/stretch\/updates/d' /etc/apt/sources.list 

# Update and install dependencies
RUN apt-get update
RUN apt-get install -y acl curl dbus fping git graphviz imagemagick libcurl4-gnutls-dev libgmp-dev libgmp3-dev libpng-dev libsnmp-dev libxml2-dev mariadb-client mariadb-server mtr-tiny nginx-full nmap rrdtool snmp snmpd systemd unzip python3-pymysql python3-redis python3-setuptools python3-systemd python3-pip wget whois traceroute
# this line is added to help find gmp.h
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h
RUN docker-php-ext-install curl gd gmp json mbstring mysql snmp xml zip
RUN pip3 install python-dotenv

# Add librenms user
RUN useradd librenms -d /opt/librenms -M -r -s "$(which bash)"

# Copy Exploit DB files over
COPY librenms-1.46 /opt/librenms

# Set permissions
RUN chown -R librenms:librenms /opt/librenms
RUN chmod 771 /opt/librenms
RUN setfacl -d -m g::rwx /opt/librenms/rrd /opt/librenms/logs /opt/librenms/bootstrap/cache/ /opt/librenms/storage/
RUN setfacl -R -m g::rwx /opt/librenms/rrd /opt/librenms/logs /opt/librenms/bootstrap/cache/ /opt/librenms/storage/

# Install PHP dependencies
RUN wget https://getcomposer.org/composer-stable.phar
RUN mv composer-stable.phar /usr/bin/composer
RUN chmod +x /usr/bin/composer

# Set timezone - difficulties with timedatectl so skipping that for now
# Import configured php.ini
COPY php.ini "$PHP_INI_DIR/php.ini"

# Configure MariaDB
COPY 50-server.cnf /etc/mysql/mariadb.conf.d/
COPY setup.sql setup.sql
RUN systemctl enable mariadb
RUN service mysql restart && mysql -u root < setup.sql

# Configure PHP-FPM
COPY fpm.conf /usr/local/etc/php-fpm.d/librenms.conf
COPY nginx.conf /etc/nginx/conf.d/librenms.conf
RUN rm /etc/nginx/sites-enabled/default
RUN service nginx restart

# Configure snmpd
RUN cp /opt/librenms/snmpd.conf.example /etc/snmp/snmpd.conf
# leaving community string as default

