#!/bin/bash
/usr/local/zend/bin/php /var/www/lnove.com/public/index.php feed processcj 6 > /dev/null &
/usr/local/zend/bin/php /var/www/lnove.com/public/index.php feed processcj 3 > /dev/null &
wait
/usr/local/zend/bin/php /var/www/lnove.com/public/index.php feed invalidoutfits
