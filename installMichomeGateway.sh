#!/bin/sh

TMPDIR="./michomeInstall"

MKDIR=$(which mkdir)
RM=$(which rm)
CP=$(which cp)
CD=cd
GIT=$(which git)
OPKG=$(which opkg)
APTGET=$(which apt-get)
MARIADB=$(which mariadb)
MYSQL=$(which mysql)
MYSQLADMIN=$(which mysqladmin)
PHP=$(which php8-fcgi)
CRON=$(which cron)
if [[ ! -z $OPKG ]]; then
	MICHOMEWEB="/opt/share/www/michome/"
	MICHOMESITE="/opt/share/www/site/"
	WWWINDEX="/opt/share/www/index.php"
	CRONTAB="/opt/etc/crontab"
	SCRIPTSPATH="/opt/usr/scripts/"
	lighttpd=$(which /opt/sbin/lighttpd)
else
	MICHOMEWEB="/var/www/html/michome/"
	MICHOMESITE="/var/www/html/site/"
	WWWINDEX="/var/www/html/index.php"
	CRONTAB="/etc/crontab"
	SCRIPTSPATH="/usr/scripts/"
	lighttpd=$(which lighttpd)
fi;

installPackage() {
    if [ ! -z $OPKG ]; then
		echo "Instalation $1 from OPKG"
		$OPKG install $1 > /dev/null
		return 0
	fi;
	
	if [ ! -z $APTGET ]; then
		echo "Instalation $1 from apt-get"
		$APTGET install $1
		return 0
	fi;
	return 1
}

if [[ -d $TMPDIR ]]; then
	$RM -f -r $TMPDIR
fi;

$MKDIR -p $TMPDIR

if [ ! -z $OPKG ]; then
	$OPKG update
fi;
if [ ! -z $APTGET ]; then
	$APTGET update
fi;

if [[ -z $GIT ]]; then
	echo "Git not installed"
	installPackage git
	installPackage git-http
fi;

echo "Download Michome package from git..."
$CD $TMPDIR
$GIT clone --quiet https://github.com/Microfcorp/MichomeGateway.git
echo "Michome is downloading"
echo ""

echo "Configuring lighttpd server"
if [[ -z $lighttpd ]]; then
	echo "Lighhtpd web-server not installed"
	installPackage lighttpd
	installPackage lighttpd-mod-fastcgi
	installPackage lighttpd-mod-proxy
	installPackage lighttpd-mod-redirect
	installPackage lighttpd-mod-rewrite
	installPackage lighttpd-mod-alias
	echo "Lighhtpd success installed"
fi;

if [ ! -z $OPKG ]; then
	echo "Stop lighttpd server..."
	/opt/etc/init.d/S80lighttpd stop > /dev/null
	echo "Copy lighhttpd params for OPKG system"
	$CP -r ./MichomeGateway/lighttpd/opkg/* /opt/etc/lighttpd
fi;
if [ ! -z $APTGET ]; then
	echo "Stop lighttpd server..."
    /etc/init.d/lighttpd stop > /dev/null
	echo "Copy lighhttpd params for apt system"
	$CP -r ./MichomeGateway/lighttpd/apt/* /etc/lighttpd
fi;

echo ""
echo "Configuring PHP (version 8)"
if [[ -z $PHP ]]; then
	echo "PHP fastcgi not installed"
	installPackage php8
	installPackage php8-fastcgi
	installPackage php8-cli
	installPackage php8-mod-curl
	installPackage php8-mod-fileinfo
	installPackage php8-mod-gd
	installPackage php8-mod-mbstring
	installPackage php8-mod-mysqli
	installPackage php8-mod-phar
	installPackage php8-mod-session
	installPackage php8-mod-simplexml
	installPackage php8-mod-zip
	installPackage php8-mod-xml
	installPackage php8-mod-xmlreader
	installPackage php8-mod-xmlwriter
	installPackage php8-mod-simplexml
	echo "PHP fastcgi success installed"
fi;

if [ ! -z $OPKG ]; then
	echo "Copy php.ini for OPKG system"
	$CP ./MichomeGateway/php/opkg/php.ini /opt/etc/php.ini
fi;
if [ ! -z $APTGET ]; then
	echo "Copy php.ini for apt system"
	$CP -r ./MichomeGateway/php/apt/php.ini /etc/php.ini
fi;

echo ""
echo "Installing Michome web-server"
if [[ -d $MICHOMEWEB ]]; then
	echo "Michome web-server is already install"
	echo "Please run cp -r $TMPDIR/MichomeGateway/michome $MICHOMEWEB"
else
	$CP -r ./MichomeGateway/michome/* $MICHOMEWEB
fi;
if [[ -d $MICHOMESITE ]]; then
	echo "Michome (site) web-server is already install"
	echo "Please run cp -r $TMPDIR/MichomeGateway/site $MICHOMESITE"
else
	$CP -r ./MichomeGateway/site $MICHOMESITE/../
fi;
if [[ ! -f $WWWINDEX ]]; then
	echo "Install www index redirect for michome"
	echo "<html><head><meta http-equiv='refresh' content='0;URL=/michome'/></head></html>" > $WWWINDEX	
fi;

echo ""
echo "Configuring MariaDB"
if [[ -z $MARIADB ]]; then
	echo "MariaDB server not installed"
	installPackage mariadb-client-extra
	installPackage mariadb-server
	installPackage mariadb-server-plugin-locales
	mysql_install_db
	echo "MariaDB server success installed"
fi;
if [[ -z $MYSQL ]]; then
	echo "MariaDB client not installed"
	installPackage mariadb-client
	echo "MariaDB client success installed"
fi;

echo "Install MariaDB database michome"

if [ ! -z $OPKG ]; then
	/opt/etc/init.d/S70mysqld start
fi;
if [ ! -z $APTGET ]; then
    /etc/init.d/mysqld start
fi;

$MYSQLADMIN -u root password "MICHOMEBD2022"
$MYSQL -u root < ./MichomeGateway/sql/michome.sql

echo "Start lighttpd server..."
if [ ! -z $OPKG ]; then
	/opt/etc/init.d/S80lighttpd start
fi;
if [ ! -z $APTGET ]; then
    /etc/init.d/lighttpd start
fi;

if [[ -d $TMPDIR ]]; then
	$RM -f -r $TMPDIR
fi;

echo "Install auto update scripts..."
$MKDIR -p $SCRIPTSPATH
$CP ./MichomeGateway/scripts/updateMichomeGateway.sh $SCRIPTSPATH/updateMichomeGateway.sh
echo "Install auto update success"

echo ""
echo "Installing cron..."
if [[ -z $CRON ]]; then
	echo "Cron not installed. Install"
	installPackage cron
	echo "Cron success installed"
	
	echo "Start cron configuring..."
	PHPCLI=$(which php)
	echo "*/1   * * * *   root    $PHPCLI $MICHOMEWEB/cron.php 1" >> $CRONTAB
	echo "*/5   * * * *   root    $PHPCLI $MICHOMEWEB/cron.php 5" >> $CRONTAB
	echo "*/10  * * * *   root    $PHPCLI $MICHOMEWEB/cron.php 10" >> $CRONTAB
	echo "10    6 * * 5   root    sh      $SCRIPTSPATH/updateMichomeGateway.sh" >> $CRONTAB
fi;

#*/1 * * * * root /bin/php8.1 /var/www/html/michome/cron.php

echo "Install Complete!"
