<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as DbTableAuthAdapter;
use Application\Service\StorageService;
use Zend\Console\Request as ConsoleRequest;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface; 
use Application\Service\EmailService as EmailService;

class Module implements
    AutoloaderProviderInterface, 
    ConfigProviderInterface{

    public function onBootstrap(MvcEvent $e) {
        
        include __DIR__ . '/config/module.defines.php';
        
        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);

        $eventManager = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        $eventManager->attach('route', array($this, 'addUserInfo'));

        /* if( !($e->getRequest() instanceof ConsoleRequest) ) {
          $eventManager->attach('route', array($this, 'checkAcl'));
          } */
    }

    public function getConfig() {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig() {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getViewHelperConfig() {
        return array(
            'invokables' => array(
                'cmsLinkAdmin'       => '\Ocoder\View\Helper\CmsLinkAdmin',
            ),
            'factories' => array(
                'Params' => function ($serviceManager) {
                    $services = $serviceManager->getServiceLocator();
                    $app = $services->get('Application');
                    return new View\Helper\Params($app->getRequest(), $app->getMvcEvent());
                },
            ),
        );
    }
    
    public function getServiceConfig() {
        return array(
            'factories' => array(
                'StorageService' => function($oSM) {
                    return new \Application\Service\StorageService('StitchStyleSolutions');
                },
                'AuthService' => function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    //$dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'Users', 'email', 'password', 'md5(?)');
                    $dbTableAuthAdapter = new DbTableAuthAdapter($dbAdapter, 'Users', 'email', 'password', '?');
                    $authService = new AuthenticationService();
                    $authService->setAdapter($dbTableAuthAdapter)->setStorage($sm->get('StorageService'));
                    //->getAdapter()->setTableName('Users');
                    return $authService;
                },
                'Application\Service\EmailService' => function($sm){
                    return new EmailService();
                },                
                'db' => function ($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    return $dbAdapter;
                },
                        
                'FeedsMappingTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('FeedsMappingTableGateway');
                    $table = new Model\FeedMappingTable($tableGateway);
                    return $table;
                },
                'FeedsMappingTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\FeedsMapping($dbAdapter));
                    return new TableGateway('FeedsMapping', $dbAdapter, null, $resultSetPrototype);
                },
                'OutfitsProductsTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('OutfitsProductsTableGateway');
                    $table = new Model\OutfitsProductsTable($tableGateway);
                    return $table;
                },
                'OutfitsProductsTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\OutfitsProducts($dbAdapter));
                    return new TableGateway('outfits_products', $dbAdapter, null, $resultSetPrototype);
                },        
                'OutfitsFeedsMappingTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('OutfitsFeedsMappingTableGateway');
                    $table = new Model\OutfitsFeedsMappingTable($tableGateway);
                    return $table;
                },
                'OutfitsFeedsMappingTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\OutfitsFeedsMapping($dbAdapter));
                    return new TableGateway('outfits_feeds_mapping', $dbAdapter, null, $resultSetPrototype);
                },

                'BucketDefinitionTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('BucketDefinitionTableGateway');
                    $table = new Model\BucketDefinitionTable($tableGateway);
                    return $table;
                },
                'BucketDefinitionTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\BucketDefinition($dbAdapter));
                    return new TableGateway('bucketdefinitions', $dbAdapter, null, $resultSetPrototype);
                },

                'BucketAttributeTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('BucketAttributeTableGateway');
                    $table = new Model\BucketAttributeTable($tableGateway);
                    return $table;
                },
                'BucketAttributeTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\BucketAttribute($dbAdapter));
                    return new TableGateway('bucketattributes', $dbAdapter, null, $resultSetPrototype);
                },

                'ConfigTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('ConfigTableGateway');
                    $table = new Model\ConfigTable($tableGateway);
                    return $table;
                },
                'ConfigTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Config($dbAdapter));
                    return new TableGateway('configs', $dbAdapter, null, $resultSetPrototype);
                },

                'LogTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('LogTableGateway');
                    $table = new Model\LogTable($tableGateway);
                    return $table;
                },
                'LogTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Log($dbAdapter));
                    return new TableGateway('logs', $dbAdapter, null, $resultSetPrototype);
                },

                'LogRedirectTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('LogRedirectTableGateway');
                    $table = new Model\LogRedirectTable($tableGateway);
                    return $table;
                },
                'LogRedirectTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\LogRedirect($dbAdapter));
                    return new TableGateway('log_redirects', $dbAdapter, null, $resultSetPrototype);
                },

                'CartTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('CartTableGateway');
                    $table = new Model\CartTable($tableGateway);
                    return $table;
                },
                'CartTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Cart($dbAdapter));
                    return new TableGateway('carts', $dbAdapter, null, $resultSetPrototype);
                },
                'ArticleAlertTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('ArticleAlertTableGateway');
                    $table = new Model\ArticleAlertTable($tableGateway);
                    return $table;
                },
                'ArticleAlertTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\ArticleAlert($dbAdapter));
                    return new TableGateway('articlealert', $dbAdapter, null, $resultSetPrototype);
                },
                'StyleListTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('StyleListTableGateway');
                    $table = new Model\StyleListTable($tableGateway);
                    return $table;
                },
                'StyleListTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\StyleList($dbAdapter));
                    return new TableGateway('style', $dbAdapter, null, $resultSetPrototype);
                },
                'AttributeListTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('AttributeListTableGateway');
                    $table = new Model\AttributeListTable($tableGateway);
                    return $table;
                },
                'AttributeListTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\AttributeList($dbAdapter));
                    return new TableGateway('attributeslist', $dbAdapter, null, $resultSetPrototype);
                },    
                'VenueTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('VenueTableGateway');
                    $table = new Model\VenueTable($tableGateway);
                    return $table;
                },
                'VenueTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\Venue($dbAdapter));
                    return new TableGateway('venue', $dbAdapter, null, $resultSetPrototype);
                },
                 'StyleDefinationTable' => function($serviceManager) {
                    $tableGateway = $serviceManager->get('StyleDefinationTableGateway');
                    $table = new Model\StyleDefinationTable($tableGateway);
                    return $table;
                },
                'StyleDefinationTableGateway' => function ($serviceManager) {
                    $dbAdapter = $serviceManager->get('Zend\Db\Adapter\Adapter');
                    $resultSetPrototype = new ResultSet();
                    $resultSetPrototype->setArrayObjectPrototype(new Model\StyleDefination($dbAdapter));
                    return new TableGateway('styledefination', $dbAdapter, null, $resultSetPrototype);
                },
            ),
            'abstract_factories' => array(
                'Application\Service\CommonTableAbstractFactory'
            ),
        );
    }

    public function addUserInfo(MvcEvent $e) {
    
        $app = $e->getApplication();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();
        $auth = $sm->get('AuthService');

       //you set your role
        if ($auth->hasIdentity()) {
    
            $viewModel = $app->getMvcEvent()->getViewModel();
            $viewModel->userData = $auth->getIdentity();
        }
    }
    
    public function checkAcl(MvcEvent $e) {

        $app = $e->getApplication();
        $em = $app->getEventManager();
        $sm = $app->getServiceManager();
        $auth = $sm->get('AuthService');

        $response = $e->getResponse();

        $route = $e->getRouteMatch()->getMatchedRouteName();
        //echo  $e->getRouteMatch()->getParam('controller', 'NA').'/'.$e->getRouteMatch()->getParam('action', 'NA');exit(0);
        //echo $route;exit(0);

        if (!in_array($route, array('home', 'auth', 'auth/default', 'profile', 'profile/default')) && !$auth->hasIdentity()) {
            $response->getHeaders()->addHeaderLine('Location', '/auth/login');
            $response->setStatusCode(302);
            $response->sendHeaders();
            exit;
        }

        //you set your role
        if ($auth->hasIdentity()) {

            if (!($e->getRouteMatch()->getParam('controller', 'NA') == 'Application\Controller\Auth' && $e->getRouteMatch()->getParam('action', 'NA') == 'logout')) {
                $lastAccessTime = $auth->getStorage()->get('lastAccessTime');
                if (isset($lastAccessTime) && !empty($lastAccessTime) && strtotime($lastAccessTime) < strtotime('-30 minutes')) {
                    $response->getHeaders()->addHeaderLine('Location', '/auth/logout');
                    $response->setStatusCode(302);
                    $response->sendHeaders();
                    exit;
                }
            }

            $auth->getStorage()->set('lastAccessTime', date('Y-m-d H:i:s'));
        }
    }

}
