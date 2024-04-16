chmod 777 /run/php-fpm-librenms.sock

su - librenms 
./opt/librenms/scripts/composer_wrapper.php install --no-dev 
exit

service cgproxy start
service dbus start
service mysql start
service nginx start
service procps start
service rsync start
service snmpd start

