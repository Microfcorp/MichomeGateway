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
	lighttpd=$(which /opt/sbin/lighttpd)
else
	MICHOMEWEB="/var/www/html/michome/"
	MICHOMESITE="/var/www/html/site/"
	WWWINDEX="/var/www/html/index.php"
	CRONTAB="/etc/crontab"
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
	echo "Remove old files"
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

echo "Installing Michome web-server"
if [[ -d $MICHOMEWEB ]]; then
	$CP -r ./MichomeGateway/michome/* $MICHOMEWEB
else
	echo "MichomeGateway is not install"
	return 1
fi;
if [[ ! -f $WWWINDEX ]]; then
	echo "Install www index redirect for michome"
	echo "<html><head><meta http-equiv='refresh' content='0;URL=/michome'/></head></html>" > $WWWINDEX	
fi;

echo "Update Michome is Success"
$CD ../
$RM -r $TMPDIR