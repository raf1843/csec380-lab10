FROM php:5.6-fpm

# Following LibreNMS Linux Server instructions

# fix apt problem
RUN sed -i 's/deb.debian.org/archive.debian.org/g' /etc/apt/sources.list
RUN sed -i '/stretch-updates/d' /etc/apt/sources.list
RUN sed -i '/stretch\/updates/d' /etc/apt/sources.list 

# Update and install dependencies
RUN apt-get update
RUN apt-get install -y acl curl fping git graphviz imagemagick libcurl4-gnutls-dev libgmp-dev libgmp3-dev libpng-dev libsnmp-dev libxml2-dev mariadb-client mariadb-server mtr-tiny nginx-full nmap rrdtool snmp snmpd unzip python3-pymysql python3-redis python3-setuptools python3-systemd python3-pip whois traceroute
# this line is added to help find gmp.h
RUN ln -s /usr/include/x86_64-linux-gnu/gmp.h /usr/include/gmp.h
RUN docker-php-ext-install curl gd gmp json mbstring mysql snmp xml zip
RUN pip3 install python3-dotenv

# Add librenms user
RUN useradd librenms -d /opt/librenms -M -r -s "$(which bash)"

# Copy Exploit DB files over
COPY librenms-1.46 /opt/librenms-1.46

# Set permissions
RUN chown -R librenms:librenms /opt/librenms
RUN chmod 771 /opt/librenms
RUN setfacl -d -m g::rwx /opt/librenms/rrd /opt/librenms/logs /opt/librenms/bootstrap/cache/ /opt/librenms/storage/
RUN setfacl -R -m g::rwx /opt/librenms/rrd /opt/librenms/logs /opt/librenms/bootstrap/cache/ /opt/librenms/storage/

# Install PHP dependencies
RUN su - librenms; ./opt/librenms/scripts/composer_wrapper.php install --no-dev; exit

# Set timezone

