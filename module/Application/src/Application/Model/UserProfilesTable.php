<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Select;
use Zend\Session\SessionManager;
use Zend\Json\Json;

class UserProfilesTable extends BasicTableAdapter {

    protected $table = 'UserProfiles';

    public function updateUserProfile($iUserId, $aData) { //print_r($aData); die;

        $sql = new Sql($this->getDBAdapter());
    
        foreach ($aData as $key1 => $val1) {
            if (isset($key1) && !empty($key1)) {
                $delete = new Delete();
                $delete->from($this->table);
                $delete->where("userId = {$iUserId}");
                $delete->where("keyLevel1 = '{$key1}'");
                //echo $sql->getSqlStringForSqlObject($delete);
                $sql->prepareStatementForSqlObject($delete)->execute();
            }
            if (is_array($val1)) {
                foreach ($val1 as $key2 => $val2) {
                    if (is_array($val2)) {
                        foreach ($val2 as $key3 => $val3) {
                            $oInsert = $sql->insert($this->table)->values(array('userId' => $iUserId, 'keyLevel1' => $key1, 'keyLevel2' => $key2, 'value' => $val3));
                            $sql->prepareStatementForSqlObject($oInsert)->execute();
                        }
                    } else {
                        $oInsert = $sql->insert($this->table)->values(array('userId' => $iUserId, 'keyLevel1' => $key1, 'keyLevel2' => $key2, 'value' => $val2));
                        $sql->prepareStatementForSqlObject($oInsert)->execute();
                    }
                }
            } else {
                $oInsert = $sql->insert($this->table)->values(array('userId' => $iUserId, 'keyLevel1' => $key1, 'value' => $val1));
                $sql->prepareStatementForSqlObject($oInsert)->execute();
            }
        }
    }

    public function getUserProfile() {
        $oAuth = $this->getServiceLocator()->get('AuthService');
        if ($oAuth->hasIdentity()) {

            $sql = new Sql($this->getDBAdapter());

            $select = new Select();
            $select->from(array('up' => $this->table));
            $select->where("up.userId = {$oAuth->getIdentity()->userId}");
               //$select->where("up.userId = 10");
            $results = $sql->prepareStatementForSqlObject($select)->execute();

            $profileArr = array();
            foreach ($results as $row) {
                $profileArr[$row['keyLevel1']][$row['keyLevel2']][] = $row['value'];
            }

            $returnArr = array();
            foreach ($profileArr as $key1 => $val1) {
                if (is_array($val1)) {
                    foreach ($val1 as $key2 => $val2) {
                        if ($key1 == 'style')
                            $returnArr[$key1][$key2] = $val2;
                        else
                            $returnArr[$key1][$key2] = $val2[0];
                    }
                }
            }

            return $returnArr;
        }
    }

    public function getStyleQuestions() {
        $aUserProfile = $this->getUserProfile();

        $sessionManager = new SessionManager();
        $sessionid =     $sessionManager ->getId();
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
        $dataDecode = array();
        if(isset($ouserTraking->getUserTrakingInfo($sessionid,'profile')->data)) {
            $dataTracking = $ouserTraking->getUserTrakingInfo($sessionid,'profile')->data;
            $dataDecode  = json_decode($dataTracking,true);
        }
        
        $aData = is_array($aUserProfile['style']['work']) ? $aUserProfile['style']['work'] : (isset($dataDecode['profile']['work']) ? $dataDecode['profile']['work'] : array());
        
        $aStyles['work'] = array(
            array('BucketID'=>'WorkOffice','name' => 'WorkOffice', 'img' => 'Work_Box1.jpg', 'value' => in_array('WorkOffice', $aData)),
            array('BucketID'=>'WorkPreppy','name' => 'WorkPreppy', 'img' => 'Work_Box2.jpg', 'value' => in_array('WorkPreppy', $aData)),
            array('BucketID'=>'WorkHipster','name' => 'WorkHipster', 'img' => 'Work_Box3.jpg', 'value' => in_array('WorkHipster', $aData)),
            array('BucketID'=>'WorkLeisure','name' => 'WorkLeisure', 'img' => 'Work_Box4.jpg', 'value' => in_array('WorkLeisure', $aData))
        );

        $aData = is_array($aUserProfile['style']['night_out']) ? $aUserProfile['style']['night_out'] : (isset($dataDecode['profile']['night_out']) ? $dataDecode['profile']['night_out'] : array());
        $aStyles['night_out'] = array(
            array('BucketID'=>'NightOutOffice','name' => 'NightOutOffice', 'img' => 'Nightout_Box1.jpg', 'value' => in_array('NightOutOffice', $aData)),
            array('BucketID'=>'NightOutPreppy','name' => 'NightOutPreppy', 'img' => 'Nightout_Box2.jpg', 'value' => in_array('NightOutPreppy', $aData)),
            array('BucketID'=>'NightOutHipster','name' => 'NightOutHipster', 'img' => 'Nightout_Box3.jpg', 'value' => in_array('NightOutHipster', $aData)),
            array('BucketID'=>'NightOutLeisure','name' => 'NightOutLeisure', 'img' => 'Nightout_Box4.jpg', 'value' => in_array('NightOutLeisure', $aData))
        );

        $aData = is_array($aUserProfile['style']['casual']) ? $aUserProfile['style']['casual'] : (isset($dataDecode['profile']['casual']) ? $dataDecode['profile']['casual'] : array());
        $aStyles['casual'] = array(
            array('BucketID'=>'CasualOffice','name' => 'CasualOffice', 'img' => 'Casual_Box1.jpg', 'value' => in_array('CasualOffice', $aData)),
            array('BucketID'=>'CasualPreppy','name' => 'CasualPreppy', 'img' => 'Casual_Box2.jpg', 'value' => in_array('CasualPreppy', $aData)),
            array('BucketID'=>'CasualHipster','name' => 'CasualHipster', 'img' => 'Casual_Box3.jpg', 'value' => in_array('CasualHipster', $aData)),
            array('BucketID'=>'CasualLeisure','name' => 'CasualLeisure', 'img' => 'Casual_Box4.jpg', 'value' => in_array('CasualLeisure', $aData))
        );

        $aData = is_array($aUserProfile['style']['never_wear']) ? $aUserProfile['style']['never_wear'] : (isset($dataDecode['profile']['never_wear']) ? $dataDecode['profile']['never_wear'] : array());
        $aStyles['never_wear'] = array(
            array('BucketID'=>'Pink','name' => 'Pink', 'img' => 'NeverWear_Box1.jpg', 'value' => in_array('Pink', $aData)),
            array('BucketID'=>'Sporty','name' => 'Sporty', 'img' => 'NeverWear_Box2.jpg', 'value' => in_array('Sporty', $aData)),
            array('BucketID'=>'SkinnyJeans','name' => 'SkinnyJeans', 'img' => 'NeverWear_Box3.jpg', 'value' => in_array('SkinnyJeans', $aData)),
            array('BucketID'=>'FormalWear','name' => 'FormalWear', 'img' => 'NeverWear_Box4.jpg', 'value' => in_array('FormalWear', $aData))
        );

        return $aStyles;
    }

    public function getAboutYouQuestions() {

        $sessionManager = new SessionManager();
        $sessionid =     $sessionManager ->getId();
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
         $dataDecode = array();
        if(isset($ouserTraking->getUserTrakingInfo($sessionid,'you')->data)) {
            $dataTracking = $ouserTraking->getUserTrakingInfo($sessionid,'you')->data;
            $dataDecode  = json_decode($dataTracking,true);
        }
   
        $aUserProfile = $this->getUserProfile();

        //$sData = (isset($aUserProfile['you']['body_shape']) ? $aUserProfile['you']['body_shape'] : '');
        $sData = isset($aUserProfile['you']['body_shape']) ? $aUserProfile['you']['body_shape'] : (isset($dataDecode['you']['body_shape']) ? $dataDecode['you']['body_shape'] : '');
        
        $aStyles['body_shape'] = array(
            array('name' => 'Oval', 'img' => 'BodyShape_Oval.png', 'value' => ($sData == 'Oval')),
            array('name' => 'Circle', 'img' => 'BodyShape_Circle.png', 'value' => ($sData == 'Circle')),
            array('name' => 'Square', 'img' => 'BodyShape_Square.png', 'value' => ($sData == 'Square')),
            array('name' => 'Rectangle', 'img' => 'BodyShape_Rectangle.png', 'value' => ($sData == 'Rectangle')),
            array('name' => 'Triangle', 'img' => 'BodyShape_Triangle.png', 'value' => ($sData == 'Triangle')),
            array('name' => 'Inverted_Triangle', 'img' => 'BodyShape_InvertedTriangle.png', 'value' => ($sData == 'Inverted_Triangle')),
        );
  

        //$sData = (isset($aUserProfile['you']['skin_tone']) ? $aUserProfile['you']['skin_tone'] : '');
        $sData = isset($aUserProfile['you']['skin_tone']) ? $aUserProfile['you']['skin_tone'] : (isset($dataDecode['you']['skin_tone']) ? $dataDecode['you']['skin_tone'] : '');
        $aStyles['skin_tone'] = array(
            array('name' => 'Box1', 'img' => 'SkinTone_Box1.png', 'value' => ($sData == 'Box1')),
            array('name' => 'Box2', 'img' => 'SkinTone_Box2.png', 'value' => ($sData == 'Box2')),
            array('name' => 'Box3', 'img' => 'SkinTone_Box3.png', 'value' => ($sData == 'Box3')),
            array('name' => 'Box4', 'img' => 'SkinTone_Box4.png', 'value' => ($sData == 'Box4')),
            array('name' => 'Box5', 'img' => 'SkinTone_Box5.png', 'value' => ($sData == 'Box5')),
            array('name' => 'Box6', 'img' => 'SkinTone_Box6.png', 'value' => ($sData == 'Box6')),
        );

        // $aStyles['weight'] = array(
        //     'name' => 'Weight',
        //     'min' => '100',
        //     'max' => '350',
        //     'step' => '5',
        //     'value' => (isset($aUserProfile['you']['weight']) ? $aUserProfile['you']['weight'] : '-1')
        // );

         $aStyles['weight'] = array(
            'name' => 'Weight',
            'min' => '100',
            'max' => '350',
            'step' => '5',
            'value' => (isset($aUserProfile['you']['weight']) ? $aUserProfile['you']['weight'] : (isset($dataDecode['you']['weight']) ? $dataDecode['you']['weight'] : '0'))
        );

        $aStyles['height'] = array(
            'name' => 'Height',
            'min' => '5',
            'max' => '7',
            'step' => '.1',
            'value' => (isset($aUserProfile['you']['height']) ? $aUserProfile['you']['height'] : (isset($dataDecode['you']['height']) ? $dataDecode['you']['height'] : '0'))
        );

        return $aStyles;
    }

    public function getSizeQuestions() {

           $sessionManager = new SessionManager();
        $sessionid =     $sessionManager ->getId();
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
           $dataDecode = array();
        if(isset($ouserTraking->getUserTrakingInfo($sessionid,'size')->data)) {
            $dataTracking = $ouserTraking->getUserTrakingInfo($sessionid,'size')->data;
            $dataDecode  = json_decode($dataTracking,true);
        }


       
        $aUserProfile = $this->getUserProfile();

        $aStyles['waist'] = array(
            'name' => 'waist',
            'list' => array(),
            // 'value' => (isset($aUserProfile['size']['waist']) ? $aUserProfile['size']['waist'] : '')
            'value' => isset($aUserProfile['size']['waist']) ? $aUserProfile['size']['waist'] :  (isset($dataDecode['size']['waist']) ? $dataDecode['size']['waist'] :'')
        );

        $aStyles['waist']['list'][''] = 'Waist';
        $aStyles['waist']['list']['dont_know'] = 'Don&rsquo;t Know';
        $aStyles['waist']['list']['other'] = 'Other';
        for ($i = 28; $i <= 44; $i++)
            $aStyles['waist']['list'][$i] = $i;
        

        $aStyles['inseam'] = array(
            'name' => 'inseam',
            'list' => array(),
           // 'value' => (isset($aUserProfile['size']['inseam']) ? $aUserProfile['size']['inseam'] : '')
            'value' => isset($aUserProfile['size']['inseam']) ? $aUserProfile['size']['inseam'] : (isset($dataDecode['size']['inseam']) ? $dataDecode['size']['inseam'] :'')
        );
        $aStyles['inseam']['list'][''] = 'Inseam';
        $aStyles['inseam']['list']['dont_know'] = 'Don&rsquo;t Know';
        $aStyles['inseam']['list']['other'] = 'Other';
        for ($i = 28; $i <= 44; $i+=2)
            $aStyles['inseam']['list'][$i] = $i;

        
        $aStyles['shoeSize'] = array(
            'name' => 'shoeSize',
            'list' => array(),
            //'value' => (isset($aUserProfile['size']['shoeSize']) ? $aUserProfile['size']['shoeSize'] : '')
            'value' => isset($aUserProfile['size']['shoeSize']) ? $aUserProfile['size']['shoeSize'] : (isset($dataDecode['size']['shoeSize']) ? $dataDecode['size']['shoeSize'] :'')
        );
        $aStyles['shoeSize']['list'][''] = 'Shoe Size';

        // foreach (range(7, 15, 0.5) as $i) {
        //    $aStyles['shoeSize']['list'][$i] = $i;
        // }
      
        $aStyles['shoeSize']['list']['dont_know'] = 'Don&rsquo;t Know';
        $aStyles['shoeSize']['list']['other'] = 'Other';
        for ($i = 14; $i <= 30; $i++)
            $aStyles['shoeSize']['list'][$i] = $i/2;


        
        $aStyles['jacketSize'] = array(
            'name' => 'jacketSize',
            'list' => array(),
            //'value' => (isset($aUserProfile['size']['jacketSize']) ? $aUserProfile['size']['jacketSize'] : '')
            'value' => isset($aUserProfile['size']['jacketSize']) ? $aUserProfile['size']['jacketSize'] : (isset($dataDecode['size']['jacketSize']) ? $dataDecode['size']['jacketSize'] :'')
        );
        $aStyles['jacketSize']['list'][''] = 'Jacket Size';
        $aStyles['jacketSize']['list']['dont_know'] = 'Don&rsquo;t Know';
        $aStyles['jacketSize']['list']['other'] = 'Other';
        for ($i = 34; $i <= 50; $i+=2)
            $aStyles['jacketSize']['list'][$i] = $i;

        
        $aStyles['shirtSize'] = array(
            'name' => 'shirtSize',
            'list' => array(''=>'Shirt Size', 'dont_know'=>'Don&rsquo;t Know', 'XS'=>'XS', 'S'=>'S', 'M'=>'M', 'L'=>'L', 'XL'=>'XL', '2XL'=>'2XL', '3XL'=>'3XL'),
            //'value' => (isset($aUserProfile['size']['shirtSize']) ? $aUserProfile['size']['shirtSize'] : '')
            'value' => isset($aUserProfile['size']['shirtSize']) ? $aUserProfile['size']['shirtSize'] : (isset($dataDecode['size']['shirtSize']) ? $dataDecode['size']['shirtSize'] :'')
        );

        if($aUserProfile['size']['waistOther']=='') {
            if(isset($dataDecode['size']['waistOther'])) {
                $aStyles['waistOther'] = $dataDecode['size']['waistOther'];
            } 
            else $aStyles['waistOther'] ='';
        } 
        else {
            $aStyles['waistOther'] = $aUserProfile['size']['waistOther'];
        }

         if($aUserProfile['size']['inseamOther']=='') {
            if(isset($dataDecode['size']['inseamOther'])) {
                $aStyles['inseamOther'] = $dataDecode['size']['inseamOther'];
            } 
            else $aStyles['inseamOther'] ='';
        } 
        else {
            $aStyles['inseamOther'] = $aUserProfile['size']['inseamOther'];
        }

        if($aUserProfile['size']['shoeSizeOther']=='') {
            if(isset($dataDecode['size']['shoeSizeOther'])) {
                $aStyles['shoeSizeOther'] = $dataDecode['size']['shoeSizeOther'];
            } 
            else $aStyles['shoeSizeOther'] ='';
        } 
        else {
            $aStyles['shoeSizeOther'] = $aUserProfile['size']['shoeSizeOther'];
        }

        if($aUserProfile['size']['jacketSizeOther']=='') {
            if(isset($dataDecode['size']['jacketSizeOther'])) {
                $aStyles['jacketSizeOther'] = $dataDecode['size']['jacketSizeOther'];
            } 
            else $aStyles['jacketSizeOther'] ='';
        } 
        else {
            $aStyles['jacketSizeOther'] = $aUserProfile['size']['jacketSizeOther'];
        }
        
        if($aUserProfile['size']['shirtSizeOther']=='') {
            if(isset($dataDecode['size']['shirtSizeOther'])) {
                $aStyles['shirtSizeOther'] = $dataDecode['size']['shirtSizeOther'];
            } 
            else $aStyles['shirtSizeOther'] ='';
        } 
        else {
            $aStyles['shirtSizeOther'] = $aUserProfile['size']['shirtSizeOther'];
        }

        //$aStyles['waistOther'] = $aUserProfile['size']['waistOther'];
        //$aStyles['inseamOther'] = $aUserProfile['size']['inseamOther'];
        //$aStyles['shoeSizeOther'] = $aUserProfile['size']['shoeSizeOther'];
        //$aStyles['jacketSizeOther'] = $aUserProfile['size']['jacketSizeOther'];
        //$aStyles['shirtSizeOther'] = $aUserProfile['size']['shirtSizeOther']; 



    
        return $aStyles;
    }

    public function getBrandQuestions() {

        $sessionManager = new SessionManager();
        $sessionid =     $sessionManager ->getId();
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
           $dataDecode = array();
        if(isset($ouserTraking->getUserTrakingInfo($sessionid,'brands')->data)) {
            $dataTracking = $ouserTraking->getUserTrakingInfo($sessionid,'brands')->data;
            $dataDecode  = json_decode($dataTracking,true);
        }

        $aUserProfile = $this->getUserProfile();

        $aBrands = array();
        for ($i = 1; $i <= 12; $i++) {
            $condition = (isset($aUserProfile['brands']) && in_array($i, $aUserProfile['brands'])) || (isset( $dataDecode['brands']) && in_array($i, $dataDecode['brands']));
            //$aBrands[] = array('name' => "brand{$i}", 'img' => 'project-2.jpg', 'value' => (isset($aUserProfile['brands']) && in_array($i, $aUserProfile['brands'])));
            $aBrands[] = array('name' => "brand{$i}", 'img' => 'project-2.jpg', 'value' =>  $condition);
        }
    
        return $aBrands;
    }

    public function getPriceQuestions() {

             $sessionManager = new SessionManager();
        $sessionid =     $sessionManager ->getId();
        $oService = $this->getServiceLocator();
        $ouserTraking = $oService->get('UserTrakingTable');
           $dataDecode = array();
        if(isset($ouserTraking->getUserTrakingInfo($sessionid,'price')->data)) {
            $dataTracking = $ouserTraking->getUserTrakingInfo($sessionid,'price')->data;
            $dataDecode  = json_decode($dataTracking,true);
        }

        // echo '<pre style="color:blue">';
        // var_dump($dataDecode);
        // echo '</pre>';    
        // die(__FILE__);


        $aUserProfile = $this->getUserProfile();

        $aStyles = array(
            array(
                'title' => 'On A Dress Shirt',
                'name' => 'shirt',
                'minPrice' => 25,
                'maxPrice' => 500,
                'step' => 5,
                // 'value' => (isset($aUserProfile['prices']['shirt']) ? $aUserProfile['prices']['shirt'] : '250')
                'value' => isset($aUserProfile['prices']['shirt']) ? $aUserProfile['prices']['shirt'] : (isset($dataDecode['price']['shirt']) ? $dataDecode['price']['shirt'] : '0')
            ),
            array(
                'title' => 'On A Pair Of Shoes',
                'name' => 'shoes',
                'minPrice' => 60,
                'maxPrice' => 1000,
                'step' => 5,
                //'value' => (isset($aUserProfile['prices']['shoes']) ? $aUserProfile['prices']['shoes'] : '350')
                'value' => isset($aUserProfile['prices']['shoes']) ? $aUserProfile['prices']['shoes'] : (isset($dataDecode['price']['shoes']) ? $dataDecode['price']['shoes'] : '0')
            ),
            array(
                'title' => 'On A Suit',
                'name' => 'suit',
                'minPrice' => 200,
                'maxPrice' => 2000,
                'step' => 50,
                //'value' => (isset($aUserProfile['prices']['suit']) ? $aUserProfile['prices']['suit'] : '1500')
                'value' => isset($aUserProfile['prices']['suit']) ? $aUserProfile['prices']['suit'] : (isset($dataDecode['price']['suit']) ? $dataDecode['price']['suit'] : '0')
            ),
            array(
                'title' => 'On A Watch',
                'name' => 'watch',
                'minPrice' => 50,
                'maxPrice' => 2000,
                'step' => 5,
                //'value' => (isset($aUserProfile['prices']['watch']) ? $aUserProfile['prices']['watch'] : '350')
                 'value' => isset($aUserProfile['prices']['watch']) ? $aUserProfile['prices']['watch'] : (isset($dataDecode['price']['watch']) ? $dataDecode['price']['watch'] : '0')
            ),
            array(
                'title' => 'On a Jacket',
                'name' => 'jacket',
                'minPrice' => 200,
                'maxPrice' => 1500,
                'step' => 5, 
                //'value' => (isset($aUserProfile['prices']['watch']) ? $aUserProfile['prices']['watch'] : '350')
                 'value' => isset($aUserProfile['prices']['jacket']) ? $aUserProfile['prices']['jacket'] : (isset($dataDecode['price']['jacket']) ? $dataDecode['price']['jacket'] : '0')
            ),
        );

        return $aStyles;
    }

}
