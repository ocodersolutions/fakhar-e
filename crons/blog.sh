#!/bin/bash

if [ "$1" = "open" ]; then
	chown -R www-data:www-data /var/www/lnove.com/devblog/
	echo "Blog is writeable now ..."
elif [ "$1" = "close" ]; then
	chown -R root:root /var/www/lnove.com/devblog/
        echo "Blog is write protected ..."
elif [ "$1" = "status" ]; then
       uname2="$(stat --format '%U' /var/www/lnove.com/devblog)" 
	if [ "${uname2}" = "root" ]; then
    		echo "Blog is write protected ..."
	else
    		echo "Blog is writeable ..."
	fi
else
	echo "Invalid Option ..."
fi
