<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;

class EngineTable extends BasicTableAdapter {

    

    public function profileBasedProductsQuery() 
    {
        //return ' (1) ';
        $oUserProfile = new UserProfilesTable( $this->getTableGateway() );
        $oUserProfile->setServiceLocator( $this->getServiceLocator() );
        $aUserProfile = $oUserProfile->getUserProfile();
        
        $sQuery = ' ( ';
            
        # outerwear = jackets
        if( isset($aUserProfile['prices']['jacket']) ) {
            $sPriceFilter = (( $aUserProfile['prices']['jacket'] < 1500 ) ? " and `feed`.`price` <= {$aUserProfile['prices']['jacket']} " : "");
            $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 6) {$sPriceFilter} ) or ";
        }
        
        # top = dress shirts
        if( isset($aUserProfile['prices']['shirt']) ) {
            $sPriceFilter = (( $aUserProfile['prices']['shirt'] < 500 ) ? " and `feed`.`price` <= {$aUserProfile['prices']['shirt']} " : "");
            $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 7) {$sPriceFilter} ) or ";
        }

        # suits = suits                 # pants = 40% of suits              # shorts = 40% of suits
        if( isset($aUserProfile['prices']['suit']) ) {
            $sPriceFilter = (( $aUserProfile['prices']['suit'] < 2000 ) ? " and `feed`.`price` <= {$aUserProfile['prices']['suit']} " : "");
            $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 36) {$sPriceFilter} ) or ";
            
            $sPriceFilter = (( $aUserProfile['prices']['suit'] < 2000 ) ? " and `feed`.`price` <= ".($aUserProfile['prices']['suit'] * 0.4)." " : "");
            $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 37)  {$sPriceFilter} ) or ";
            $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 54)  {$sPriceFilter} ) or ";
        }

        # Footwear = shoe
        if( isset($aUserProfile['prices']['shoes']) ) {
            $sPriceFilter = (( $aUserProfile['prices']['shoes'] < 1000 ) ? " and `feed`.`price` <= {$aUserProfile['prices']['shoes']} " : "");
            $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 4)  {$sPriceFilter} ) or ";
        }        
        
        # Accessories = watch
        if( isset($aUserProfile['prices']['watch']) ) {
            $sPriceFilter = (( $aUserProfile['prices']['watch'] < 2000 ) ? " and `feed`.`price` <= {$aUserProfile['prices']['watch']} " : "");
            $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 63)  {$sPriceFilter} ) or ";
        }   

        # underwears etc
        $sQuery .= " (`feed`.`uid` in (select distinct productUID from ProductAttributes where type = 'categories' and value = 68) ) ";
        
        $sQuery .= ' ) ';
         
        //echo $sQuery;   die;
        //echo '<pre>'; print_r($aUserProfile); exit(0);        
      
        return $sQuery;
    }
    
    public function styleBasedProductsQuery( &$oSelect )
    {
        return true;

        $oUserProfile = new UserProfilesTable( $this->getTableGateway() );
        $oUserProfile->setServiceLocator( $this->getServiceLocator() );
        $aUserProfile = $oUserProfile->getUserProfile();
        
        $aStyles = array();
        foreach($aUserProfile['style'] as $k1=>$v1 ) {
            
            foreach($v1 as $k2=>$v2 ) {
                        
                if (!in_array($k1.'__'.$v2, array('casual__CasualHipster','casual__CasualLeisure','casual__CasualOffice','casual__CasualPreppy','night_out__NightOutHipster','night_out__NightOutLeisure','night_out__NightOutOffice','night_out__NightOutPreppy','work__WorkHipster','work__WorkLeisure','work__WorkOffice','work__WorkPreppy','never_wear__FormalWear','never_wear__Pink','never_wear__SkinnyJeans','never_wear__Sporty'))){
                    continue;
                }
                
                if (in_array($k1.'__'.$v2, array('never_wear__FormalWear','never_wear__Pink','never_wear__SkinnyJeans','never_wear__Sporty'))){
                    $aNegativeStyles[] = "{$k1}__{$v2} = 0";
                }
                else {                 
                    $aStyles[] = "{$k1}__{$v2} = 1";
                }
            }
        }

        $sNegativeQuery = ( isset($aNegativeStyles) && count($aNegativeStyles) ? "AND (".implode(" AND ", $aNegativeStyles).")" : "");
        //return " (`feed`.`uid` in (select distinct productUID from bucketProducts where  bucketID IN ('".implode("','", $aStyles)."') ) ) ";
        if(count($aStyles) > 0) {
            $oSelect->join(array("bp" => 'bucketProducts'), new Expression("(feed.uid = bp.productUID AND (".implode(" OR ", $aStyles).")  {$sNegativeQuery} )"), array());
        } else {
            $oSelect->join(array("bp" => 'bucketProducts'), new Expression("(feed.uid = bp.productUID  {$sNegativeQuery} )"), array());
        }
        
        
        //print_r($aStyles); die;
        
        
    }
}
