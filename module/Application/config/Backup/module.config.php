<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                    'index' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
            'auth' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/auth',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Auth',
                        'action' => 'login',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Auth',
                                'action' => 'login',
                            ),
                        ),
                    ),
                ),
            ),
            'user' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/user',
                    'defaults' => array(
                        'controller' => 'Application\Controller\User',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\User',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'index' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\User',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'changepassword' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action][/:param1][/]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\User',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
            'profile' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/profile',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Profile',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Profile',
                                'action' => 'index',
                            ),
                        ),
                    ),
//             				'index' => array(
//             						'type'    => 'Segment',
//             						'options' => array(
//             								'route'    => '/index',
//             								'constraints' => array(
//             										'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
//             										'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
//             								),
//             								'defaults' => array(
//             										'controller'    => 'Application\Controller\Profile',
//             										'action'        => 'index',
//             								),
//             						),
//             				),
                ),
            ),
            'mycloset' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/mycloset',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Mycloset',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action][/:param1][/]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Mycloset',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'index' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Mycloset',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'getfeeds' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/getfeeds/[:rand]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Mycloset',
                                'action' => 'getfeeds',
                            ),
                        ),
                    ),
                    'feeddatadetail' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/feeddatadetail/[:feeddataid]/[:rand]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Mycloset',
                                'action' => 'feeddatadetail',
                            ),
                        ),
                    ),
                      'articlebought' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/articlebought/[:feeddataid]/[:rand]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Mycloset',
                                'action' => 'articlebought',
                            ),
                        ),
                    ),
                ),
            ),
            'service' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/service',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Service',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action][/:param1][/]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Service',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'index' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Service',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'getfeeds' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/getfeeds/[:rand]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Service',
                                'action' => 'getfeeds',
                            ),
                        ),
                    ),
                    'feeddatadetail' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/feeddatadetail/[:feeddataid]/[:rand]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Service',
                                'action' => 'feeddatadetail',
                            ),
                        ),
                    ),
                      'articlebought' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/articlebought/[:feeddataid]/[:rand]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Service',
                                'action' => 'articlebought',
                            ),
                        ),
                    ),
                ),
            ),
            'feed' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/feed',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Feed',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:pname]/[:image]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'processcj' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/process-cj',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'processCJ',
                            ),
                        ),
                    ),
                    'addinfo' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/add-info',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'addInfo',
                            ),
                        ),
                    ),
                    'list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/list',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'list',
                            ),
                        ),
                    ),
                ),
            ),
            'info' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/info',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Info',
                        'action' => 'how-it-works',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Info',
                                'action' => 'how-it-works',
                            ),
                        ),
                    )
                ),
            ),
            'attributes' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/attributes',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Attributes',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'process' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Attributes',
                                'action' => 'index',
                            ),
                        ),
                    ),
                    'index' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Attributes',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),
            /* 'auth' => array(
              'type' => 'Zend\Mvc\Router\Http\Literal',
              'options' => array(
              'route'    => '/',
              'defaults' => array(
              'controller' => 'Application\Controller\Auth',
              'action'     => 'index',
              ),

              ),
              ), */
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type' => 'Literal',
                'options' => array(
                    'route' => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller' => 'Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type' => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern' => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController',
            'Application\Controller\Auth' => 'Application\Controller\AuthController',
            'Application\Controller\User' => 'Application\Controller\UserController',
            'Application\Controller\Profile' => 'Application\Controller\ProfileController',
            'Application\Controller\Service' => 'Application\Controller\ServiceController',
            'Application\Controller\Mycloset' => 'Application\Controller\MyclosetController',
            'Application\Controller\Info' => 'Application\Controller\InfoController',
            'Application\Controller\Feed' => 'Application\Controller\FeedController',
            'Application\Controller\Attributes' => 'Application\Controller\AttributesController'
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'ReadXMLByNodePlugin' => 'Application\Controller\Plugin\ReadXMLByNodePlugin',
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions' => true,
        'doctype' => 'HTML5',
        'not_found_template' => 'error/404',
        'exception_template' => 'error/index',
        'template_map' => array(
            'layout/layout' => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404' => __DIR__ . '/../view/error/404.phtml',
            'error/index' => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                'feed-processcj' => array(
                    'options' => array(
                        'route' => 'feed processcj',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'processCJ'
                        )
                    )
                ),
                'feed-processimages' => array(
                    'options' => array(
                        'route' => 'feed processimages [--verbose|-v] <processId>',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'processImages'
                        )
                    )
                ),
            ),
        ),
    ),
);
