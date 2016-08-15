<?php

return array(
     'module_layouts' => array(
        'Mobile' => 'layout/mobile',
    ),
     
    'controllers' => array(
        'invokables' => array(
            'Mobile\Controller\Index' => 'Mobile\Controller\IndexController',
            'Mobile\Controller\Auth' => 'Mobile\Controller\AuthController',
            'Mobile\Controller\User' => 'Mobile\Controller\UserController',
            'Mobile\Controller\Profile' => 'Mobile\Controller\ProfileController',
            'Mobile\Controller\Service' => 'Mobile\Controller\ServiceController',
            'Mobile\Controller\Mycloset' => 'Mobile\Controller\MyclosetController',
            'Mobile\Controller\Mybought' => 'Mobile\Controller\MyboughtController',
            'Mobile\Controller\Info' => 'Mobile\Controller\InfoController',
            'Mobile\Controller\Feed' => 'Mobile\Controller\FeedController',
            'Mobile\Controller\Attributes' => 'Mobile\Controller\AttributesController'
        ),
    ),
 
    'view_manager' => array(
   
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            //'layout/mobile' => PATH_TEMPLATE.'/mobile/layout.phtml',
            'layout/mobile' => __DIR__ . '/../view/layout/layout.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
   
);