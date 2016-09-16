<?php
namespace Ocoder\Base;

use Zend\Mvc\MvcEvent;
use Zend\Mvc\Controller\PluginManager;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container;
use Zend\Db\Sql\Where;

class BaseActionController extends AbstractActionController {
    protected $_params;
    protected $userId;
    protected $_paginator = array(
        'itemCountPerPage' => 10,
        'pageRange' => 3,
    );
     public function __construct(){
        //$this->getCart();
        //$this->test();
     }
     public function setPluginManager(PluginManager $plugins) {
        // attach onInit function into Event Manager
        // 100 - set high priority for Init function (will load before controller)
        $this->getEventManager()->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onInit'), 100);
        parent::setPluginManager($plugins);
    }


    public function onInit(MvcEvent $e) {
        // GET MODULE - CONTROLLER - ACTION
        $routeMatch = $e->getRouteMatch();
        $controllerArray = explode('\\', $routeMatch->getParam('controller'));

        // SET MODULE - CONTROLLER - ACTION FOR $_PARAMS
        $this->_params['module'] = strtolower($controllerArray[0]);
        $this->_params['controller'] = strtolower($controllerArray[2]);
        $this->_params['action'] = $routeMatch->getParam('action');

        // SET MODULE - CONTROLLER - ACTION FOR VIEW
        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->module = $this->_params['module'];
        $viewModel->controller = $this->_params['controller'];
        $viewModel->action = $this->_params['action'];

        // PAGINATOR
        $this->_paginator['currentPageNumber'] = $this->params()->fromRoute('page', 1);
        $this->_params['paginator'] = $this->_paginator;

        // SET LAYOUT
        // $config = $this->getServiceLocator()->get('config');
        // $this->layout($config['module_layouts'][$controllerArray[0]]);
        // $this->layout('layout/frontend');

        $this->init();
    }

    public function init() {
        // function for controller override
       
        $oAuth = $this->getServiceLocator()->get('AuthService');
        $userInfo = $oAuth->getIdentity();
        if($userInfo!=null) {
            $this->userId = $userInfo->userId;
             $this->getCart();
        }
        
    }
 
     protected function get_client_ip() {
   
        $ipaddress = '';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if (getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if (getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if (getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if (getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if (getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }

   

    protected function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
        $output = NULL;
        if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
            $ip = $_SERVER["REMOTE_ADDR"];
            if ($deep_detect) {
                if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
            }
        }
        $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
        $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
        $continents = array(
            "AF" => "Africa",
            "AN" => "Antarctica",
            "AS" => "Asia",
            "EU" => "Europe",
            "OC" => "Australia (Oceania)",
            "NA" => "North America",
            "SA" => "South America"
        );
        if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
            $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
            if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
                switch ($purpose) {
                    case "location":
                        $output = array(
                            "city"           => @$ipdat->geoplugin_city,
                            "state"          => @$ipdat->geoplugin_regionName,
                            "country"        => @$ipdat->geoplugin_countryName,
                            "country_code"   => @$ipdat->geoplugin_countryCode,
                            "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                            "continent_code" => @$ipdat->geoplugin_continentCode
                        );
                        break;
                    case "address":
                        $address = array($ipdat->geoplugin_countryName);
                        if (@strlen($ipdat->geoplugin_regionName) >= 1)
                            $address[] = $ipdat->geoplugin_regionName;
                        if (@strlen($ipdat->geoplugin_city) >= 1)
                            $address[] = $ipdat->geoplugin_city;
                        $output = implode(", ", array_reverse($address));
                        break;
                    case "city":
                        $output = @$ipdat->geoplugin_city;
                        break;
                    case "state":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "region":
                        $output = @$ipdat->geoplugin_regionName;
                        break;
                    case "country":
                        $output = @$ipdat->geoplugin_countryName;
                        break;
                    case "countrycode":
                        $output = @$ipdat->geoplugin_countryCode;
                        break;
                }
            }
        }
        return $output;
    }
    public function getBrowser() 
    { 
        $u_agent = $_SERVER['HTTP_USER_AGENT']; 
        $bname = 'Unknown';
        $platform = 'Unknown';
        $version= "";

        //First get the platform?
        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'linux';
        }
        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'mac';
        }
        elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'windows';
        }
        
        // Next get the name of the useragent yes seperately and for good reason
        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Internet Explorer'; 
            $ub = "MSIE"; 
        } 
        elseif(preg_match('/Firefox/i',$u_agent)) 
        { 
            $bname = 'Mozilla Firefox'; 
            $ub = "Firefox"; 
        } 
        elseif(preg_match('/Chrome/i',$u_agent)) 
        { 
            $bname = 'Google Chrome'; 
            $ub = "Chrome"; 
        } 
        elseif(preg_match('/Safari/i',$u_agent)) 
        { 
            $bname = 'Apple Safari'; 
            $ub = "Safari"; 
        } 
        elseif(preg_match('/Opera/i',$u_agent)) 
        { 
            $bname = 'Opera'; 
            $ub = "Opera"; 
        } 
        elseif(preg_match('/Netscape/i',$u_agent)) 
        { 
            $bname = 'Netscape'; 
            $ub = "Netscape"; 
        } 
        
        // finally get the correct version number
        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
        ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {
            // we have no matching number just continue
        }
        
        // see how many we have
        $i = count($matches['browser']);
        if ($i != 1) {
            //we will have two since we are not using 'other' argument yet
            //see if version is before or after the name
            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
                $version= $matches['version'][0];
            }
            else {
                $version= $matches['version'][1];
            }
        }
        else {
            $version= $matches['version'][0];
        }
        
        // check if we have a number
        if ($version==null || $version=="") {$version="?";}
        
        return array(
            'userAgent' => $u_agent,
            'name'      => $bname,
            'version'   => $version,
            'platform'  => $platform,
            'pattern'    => $pattern
        );
    }

    public function getCart(){

       
        $oAuth = $this->getServiceLocator()->get('AuthService');
        
        $userInfo = $oAuth->getIdentity();
        $userId = $userInfo->userId;

        $where = new Where();
        $where->equalTo('user_id',$userId);
        $cartTable = $this->getServiceLocator()->get('CartTable');
        $cartNumber = $cartTable->getCartNumber(['where'=>$where]);
        
        $sessionCart = new Container('cart');
        if($cartNumber > 0) {
            $sessionCart->cartNumber = $cartNumber;
        } else {
            $sessionCart->cartNumber = 0;
        }
      
        return $sessionCart->cartNumber;
    }

    public function preUrl(){
        $ssSystem = new Container('system');
        return $ssSystem->offsetGet('previous-url');
    }

    public function getItemCart(){
        $cartTable = $this->getServiceLocator()->get('CartTable');
        $where = new Where();
        $where->equalTo('user_id',$this->userId);
        $listItemCart = $cartTable->getCart(['where'=>$where]);
        $listIdCart = array();
        foreach ($listItemCart as $value) {
            $listIdCart[] = $value->feed_id;
        }
        return $listIdCart;
    }

    function convertStringToAlias($str) {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        //$str = str_replace(array(' ~', ' !', ' @', ' #', ' $', ' %', ' ^', ' &', ' *', ' (', ' )', ' +', ' |', ' \\', ' [', ' ]', ' {', ' }', ' ;', ' :', ' "', ' \'', ' <', ' >', ' ,', ' .', ' ?', ' /', ' -'), '', $str);

        $str = str_replace(array('~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '+', '|', '\\', '[', ']', '{', '}', ';', ':', '"', '\'', '<', '>', ',', '.', '?', '/', '-'), '', $str);
        $str = str_replace(array('  '), ' ', $str);
        $str = str_replace(" ", "-", str_replace("&*#39;", "", $str));

        return strtolower($str);
    }

 
 
}

