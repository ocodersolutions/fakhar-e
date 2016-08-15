<?php

define('PATH_APPLICATION', realpath(dirname(__DIR__)));
define('PATH_VENDOR', PATH_APPLICATION . '/vendor');
define('PATH_ZEND_CORE', PATH_VENDOR . '/zendframework/zendframework/library');
define('PATH_PUBLIC', PATH_APPLICATION . '/public');
define('PATH_TEMPLATE', PATH_PUBLIC . '/templates');


define('URL_APPLICATION', 'dev.elnove.com');
define('URL_MOBILE_APPLICATION', 'devmobile.elnove.com');
define('URL_BLOG', 'devblog.elnove.com');
define('URL_PUBLIC', 'http://' . URL_APPLICATION . '/public');
define('URL_TEMPLATE', URL_PUBLIC . '/templates');
define('URL_UPLOADS', URL_PUBLIC . '/uploads');