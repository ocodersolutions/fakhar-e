<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Event\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class EventController extends AbstractActionController {

    public function indexAction() 
    {
        $event = new \Event\Model\EventsLog();
        $event->user_ip = '127.0.0.1';
        $event->page = 'profile-style';
        $event->product_selected = '123';
        $event->created = date("Y-m-d h:i:s");
        $event->user_id = '1';

        $sm = $this->getServiceLocator();
        $eFeeds = $sm->get('EventsLogTable');
        
        //echo '<pre>';print_r($eFeeds);die;
        $event_id = $eFeeds->saveEvent($event);

        print_r($event_id);
        return new ViewModel();
    }

    public function log($input) {
        echo '<pre>';
        print_r($input);
        echo 'test';
        die;
        return new ViewModel();
    }

}
