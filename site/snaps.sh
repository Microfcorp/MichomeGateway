#!/bin/bash

$(wget -O /var/www/html/textultemp.txt -P /var/www/html/ "http://192.168.1.42/michome/api/getdata.php?device=192.168.1.11&cmd=textultemp" 2>/dev/null)

tempul=$(cat /var/www/html/textultemp.txt)

cd /var/www/html/site/image/graphical

$(ffmpeg -dn -an -y -f video4linux2 -i /dev/video0 -vframes 1 -vf drawtext="fontfile=/usr/share/fonts/truetype/freefont/FreeSerif.ttf: \ text='Температура на улице $(echo $tempul)': fontcolor=white: fontsize=24: box=1: boxcolor=black@0.5: \ boxborderw=5: x=(w-text_w)-10: y=10" -f image2 -q:v 5 "p-$(date +%m-%d-%Y) $(date +%H-%M).jpg")


