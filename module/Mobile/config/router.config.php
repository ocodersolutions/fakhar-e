<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonMobile for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
  'router' => array(
    'routes' => array(
      'm' => array(
        'type' => 'Hostname',
        'options' => array(
          'route' => URL_MOBILE_APPLICATION,
          'defaults' => array(
            '__NAMESPACE__' => 'Mobile\Controller',
            'controller' => 'Index',
            'action' => 'index',
          ) ,
        ) ,
        'may_terminate' => true,
        'child_routes' => array(

          // This Segment route captures the requested controller
          // and action from the URI and, through ModuleRouteListener,
          // selects the correct controller class to use

          'default' => array(
            'type' => 'Segment',
            'options' => array(
              'route' => '/[:controller[/:action[/:id[/:rand]]]][/]',
              'constraints' => array(
                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                // 'id' => '[0-9-]*',
              ) ,
              'defaults' => array() ,
            ) ,
          ) ,
          'auth' => array(
            'type' => 'Zend\Mvc\Router\Http\Literal',
            'options' => array(
              'route' => '/auth',
              'defaults' => array(
                'controller' => 'Mobile\Controller\Auth',
                'action' => 'login',
              ) ,
            ) ,
          ) ,
          // 'service' => array(
          //   'type' => 'Zend\Mvc\Router\Http\Literal',
          //   'options' => array(
          //     'route' => '/service',
          //     'defaults' => array(
          //       'controller' => 'Mobile\Controller\Service',
          //       'action' => 'index',
          //     ) ,
          //   ) ,
          //   'may_terminate' => true,
          //   'child_routes' => array(
          //     'process' => array(
          //       'type' => 'Segment',
          //       'options' => array(
          //         'route' => '/[:action][/:param1][/]',
          //         'constraints' => array(
          //           'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //           'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //         ) ,
          //         'defaults' => array(
          //           'controller' => 'Mobile\Controller\Service',
          //           'action' => 'index',
          //         ) ,
          //       ) ,
          //     ) ,
          //     'index' => array(
          //       'type' => 'Segment',
          //       'options' => array(
          //         'route' => '/index',
          //         'constraints' => array(
          //           'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //           'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //         ) ,
          //         'defaults' => array(
          //           'controller' => 'Mobile\Controller\Service',
          //           'action' => 'index',
          //         ) ,
          //       ) ,
          //     ) ,
          //     'getfeeds' => array(
          //       'type' => 'Segment',
          //       'options' => array(
          //         'route' => '/getfeeds/[:rand]',
          //         'constraints' => array(
          //           'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //           'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //         ) ,
          //         'defaults' => array(
          //           'controller' => 'Mobile\Controller\Service',
          //           'action' => 'getfeeds',
          //         ) ,
          //       ) ,
          //     ) ,
          //     'feeddatadetail' => array(
          //       'type' => 'Segment',
          //       'options' => array(
          //         'route' => '/feeddatadetail/[:feeddataid]/[:rand]',
          //         'constraints' => array(
          //           'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //           'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //         ) ,
          //         'defaults' => array(
          //           'controller' => 'Mobile\Controller\Service',
          //           'action' => 'feeddatadetail',
          //         ) ,
          //       ) ,
          //     ) ,
          //     'articlebought' => array(
          //       'type' => 'Segment',
          //       'options' => array(
          //         'route' => '/articlebought/[:feeddataid]/[:rand]',
          //         'constraints' => array(
          //           'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //           'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
          //         ) ,
          //         'defaults' => array(
          //           'controller' => 'Mobile\Controller\Service',
          //           'action' => 'articlebought',
          //         ) ,
          //       ) ,
          //     ) ,
          //   ) ,
          // ) ,
        ) ,
      ) ,
    ) ,
  ) ,
);
