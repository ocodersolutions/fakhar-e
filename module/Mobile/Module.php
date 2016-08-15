<?php
namespace Mobile;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Session\Container;
use Zend\Console\Request as ConsoleRequest;


class Module
{

    public function onBootstrap(MvcEvent $e)
    {  

        $eventManager        = $e->getApplication()->getEventManager();
        $eventManager->getSharedManager()->attach('Zend\Mvc\Controller\AbstractActionController', 'dispatch', function($e) {
            $controller      = $e->getTarget();
            $controllerClass = get_class($controller);
            $moduleNamespace = substr($controllerClass, 0, strpos($controllerClass, '\\'));
            $config          = $e->getApplication()->getServiceManager()->get('config');
            if (isset($config['module_layouts'][$moduleNamespace])) {
                $controller->layout($config['module_layouts'][$moduleNamespace]);
            }
        }, 100);
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        if( !($e->getRequest() instanceof ConsoleRequest) ) {
            // Store History URL in Session
            $helperString = new \Ocoder\Helper\String();
    
            if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
                $currentUrl = $helperString->getCurrentUrl();
                $ssSystem = new Container('system');
                $previousUrl = $ssSystem->offsetExists('current-url') ? $ssSystem->offsetGet('current-url') : URL_MOBILE_APPLICATION;
                if (trim($currentUrl) != trim($previousUrl)) {
                    $ssSystem->offsetSet('previous-url', $previousUrl);
                }
                $ssSystem->offsetSet('current-url', $currentUrl);
            }
        }        

      
        
    }

    public function loadLayout(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $controllerArr = explode('\\', $routeMatch->getParam('controller'));
        $moduleName = $controllerArr['0'];

        $config = $e->getApplication()->getServiceManager()->get('config');
      
        $controler = $e->getTarget();
        $controler->layout($config['module_layouts'][$moduleName]);
 


    }

    public function getConfig()
    {

        return array_merge(
            include __DIR__ . '/config/module.config.php', 
            include __DIR__ . '/config/router.config.php'
        );
    }

    public function getAutoloaderConfig()

    {
        return array(

            'Zend\Loader\StandardAutoloader' => array(

                'namespaces' => array(

                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,

                ),

            ),

        );

    }
    

}