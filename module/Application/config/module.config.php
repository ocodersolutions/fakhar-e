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
                        'controller' => 'Application\Controller\Service',
                        'action' => 'index',
                    ),
                ),
            ),
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/',
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
            
            'index' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/index',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/[:action[/:id]][/]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Index',
                                'action' => 'index',
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
             'mybought' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/mybought',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Mybought',
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
                                'controller' => 'Application\Controller\Mybought',
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
                                'controller' => 'Application\Controller\Mybought',
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
                                'controller' => 'Application\Controller\Mybought',
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
                                'controller' => 'Application\Controller\Mybought',
                                'action' => 'feeddatadetail',
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
                    'outfits' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/outfits[/]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Service',
                                'action' => 'outfits',
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
                    
                    'share-outfit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/share-outfit',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Service',
                                'action' => 'share-outfit',
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
                    'processcjtest' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/process-cjtest',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'processCJTest',
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
                    'articles-list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/articles-list',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'articles-list',
                            ),
                        ),
                    ), 
                    'edit-article' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit-article',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'edit-article',
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
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'getfeeds',
                            ),
                        ),
                    ), 

                    'process-invalid-outfits' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/process-invalid-outfits',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'process-invalid-outfits',
                            ),
                        ),
                    ),  
                    'generate-outfits-preview-image' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/generate-outfits-preview-image',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'generate-outfits-preview-image',
                            ),
                        ),
                    ), 
                    'process-bucket-products' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/process-bucket-products',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Feed',
                                'action' => 'process-bucket-products',
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
            'comingsoon' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/comingsoon',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Comingsoon',
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
                                'controller' => 'Application\Controller\Comingsoon',
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
                                'controller' => 'Application\Controller\Comingsoon',
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),     
            'bucket' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/bucket',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Bucket',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'index' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                 'controller' => 'Application\Controller\Bucket',
                                 'action' => 'index',
                            ),
                        ),
                    ),
                    'add' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/add',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                 'controller' => 'Application\Controller\Bucket',
                                 'action' => 'add',
                            ),
                        ),
                    ),
                    'delete' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/delete/:id',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Bucket',
                                'action' => 'delete',
                            ),
                        ),
                    ),
                    'edit' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Bucket',
                                'action' => 'edit',
                            ),
                        ),
                    ),
                    'edit-bucket-attribute' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/edit-bucket-attribute',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Bucket',
                                'action' => 'edit-bucket-attribute',
                            ),
                        ),
                    ),
                ),
            ), 
            'email' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/email',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Email',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'index' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/index',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                 'controller' => 'Application\Controller\Email',
                                 'action' => 'index',
                            ),
                        ),
                    ),
                    
                ),
            ),        
            'style' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route' => '/style',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Style',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'defination' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/defination[/:id]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id'     => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Style',
                                'action' => 'defination',
                            ),
                        ),
                    ),
                    'mystyle' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/mystyle',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'controller' => 'Application\Controller\Style',
                                'action' => 'mystyle',
                            ),
                        ),
                    ),
                ),
            ),  
            'venue' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/venue',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Venue',
                        'action'     => 'index',
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
            'Zend\Authentication\AuthenticationService' => 'AuthService',
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
             'Application\Controller\Mybought' => 'Application\Controller\MyboughtController',
            'Application\Controller\Info' => 'Application\Controller\InfoController',
            'Application\Controller\Feed' => 'Application\Controller\FeedController',
            'Application\Controller\Attributes' => 'Application\Controller\AttributesController',
            'Application\Controller\Comingsoon' => 'Application\Controller\ComingsoonController',
            'Application\Controller\Bucket' => 'Application\Controller\BucketController',
            'Application\Controller\Email' => 'Application\Controller\EmailController',
            'Application\Controller\Style' => 'Application\Controller\StyleController',
            'Application\Controller\Venue' => 'Application\Controller\VenueController',
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
            'service_outfits' => __DIR__ . '/../view/application/service/outfit_3_panel.phtml',
        ),
        'template_path_stack' => array(
            'module' => __DIR__ . '/../view',
            'partial' => __DIR__ . '/../view/partial/'
        )
    ),
    'view_helpers' => array(
        'invokables'=> array(
            'string_helper' => 'Application\View\Helper\Stringhelper',
            'percentage_helper' => 'Application\View\Helper\Percentagehelper'
        )
    ),

    'view_helper_config' => array(
        'flashmessenger' => array(
            'message_open_format'      => '<div%s><button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button><p>',
            'message_close_string'     => '</p></div>',
            'message_separator_string' => '</p><p>'
        )
    ),
    // Placeholder for console routes
    'console' => array(
        'router' => array(
            'routes' => array(
                
                'feed-processcj' => array(
                    'options' => array(
                        'route' => 'feed processcj [--verbose|-v] <feed_id>',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'processCJ'
                        )
                    )
                ),
                'feed-invalid-outfits' => array(
                    'options' => array(
                        'route' => 'feed invalidoutfits [--verbose|-v]',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'processInvalidOutfits'
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
                'feed-generateoutfitviews' => array(
                    'options' => array(
                        'route' => 'feed generateoutfitspreviewimage',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'generateOutfitsPreviewImage'
                        )
                    )
                ),                
                'feed-bucketproducts' => array(
                    'options' => array(
                        'route' => 'feed processbucketproducts',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'processBucketProducts'
                        )
                    )
                ),      
                'feed-deleteUnUsedAttributes' => array(
                    'options' => array(
                        'route' => 'feed delete-un-used-attributes',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'delete-un-used-attributes'
                        )
                    )
                ),
                'feed-globalFinalize' => array(
                    'options' => array(
                        'route' => 'feed global-finalize',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'global-finalize'
                        )
                    )
                ),   
                'feed-updateCatagoriesTable' => array(
                    'options' => array(
                        'route' => 'feed update-catagories-table',
                        'defaults' => array(
                            'controller' => 'Application\Controller\Feed',
                            'action' => 'update-catagories-table'
                        )
                    )
                ),                             
                
            ),
        ),
    ),
);
