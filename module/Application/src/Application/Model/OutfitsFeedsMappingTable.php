<?php

namespace Application\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Delete;
use Zend\Db\Sql\Update;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Paginator;
use Zend\Db\ResultSet\ResultSet;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Paginator\Adapter\DbSelect;

class OutfitsFeedsMappingTable extends BasicTableAdapter {

    public function getOutfitsMapping($conditions = array(), $withFeed = null, $remdom=null) {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('ofm' => 'outfits_feeds_mapping'));
        if($withFeed) {
            $select->join(array("fd" => 'FeedData'), new Expression("ofm.feeds_uid = fd.uid"), array('*'), 'inner')
                   ->join(array("op" => 'outfits_products'), new Expression("op.outfit_id  = ofm.outfit_id"), array('outfittitle'=>'title'), 'inner')
                   ->join(array("Al" => 'ArticleCloseset'), new Expression("(Al.feeddataid = fd.id)"),array('likesCount' => new Expression('COUNT(Al.closesetid)')), 'left');
            $select->group('fd.id');
        }
        if(!empty($conditions)) {
            foreach($conditions as $colom=>$value) {
                $select->where(array('ofm.'.$colom => $value));
            }
        }
        if($remdom) {
            $select->order(new Expression("RAND()"))->limit(6);
        } else {
            $select->order('feed_position');
        }
        //echo $sql->getSqlStringForSqlObject($select); die;

        $resultSet = array();
        
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }    

    public function listItem($where){
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('op' => 'outfits_feeds_mapping'))
                      ->where($where);
        $resultSet = array();
        //echo $sql->getSqlstringForSqlObject($select);die();
        //echo $sql->prepareStatementForSqlObject($select); die();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        return $resultSet;
    }
    
    public function save($data = array()){
        $id = null;
        if(!isset($data['id']) || empty($data['id'])) {
            $id = $this->tableGateway->insert($data);
        } else {
            $id = $data['id'];
            unset($data['id']);
            $this->tableGateway->update($data, array('id' => $id));
        }
        return $id;
    }
    
    public function remove($id)
    {
        $this->tableGateway->delete(array('id' => (int) $id));
    }
     public function removeByCondition($where = null){
        if(isset($where)) {
             $this->tableGateway->delete($where);
        }
     }

    public function generateOutfitImage($iOutfitId)
    {
        $mapping = $this->getOutfitsMapping(array('outfit_id' => $iOutfitId), true);
        
        $sCMD = ''; 
        $sWorkingDir = PUBLIC_PATH . '/feed/outfits/';
        $aResizeImages = array(); 
        $aCombineImages = array();
        $aFilesToDelete = array();
        $i = 0;
        
        if( isset($mapping[$i]['imageurl']) && !empty($mapping[$i]['imageurl']) ) {
            $aResizeImages[] = "wget -q '{$mapping[$i]['imageurl']}' -O {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aResizeImages[] = "convert '{$sWorkingDir}{$iOutfitId}_{$i}.jpg' -resize 752x307\> -size 752x307 xc:white +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aCombineImages[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg -geometry +2+2  -composite ";
            $aFilesToDelete[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg ";
        } $i++;
        if( isset($mapping[$i]['imageurl']) && !empty($mapping[$i]['imageurl']) ) {
            $aResizeImages[] = "wget -q '{$mapping[$i]['imageurl']}' -O {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aResizeImages[] = "convert '{$sWorkingDir}{$iOutfitId}_{$i}.jpg' -resize 375x307\> -size 375x307 xc:white +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aCombineImages[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg -geometry +2+311  -composite ";
            $aFilesToDelete[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg ";
        } $i++;
        if( isset($mapping[$i]['imageurl']) && !empty($mapping[$i]['imageurl']) ) {
            $aResizeImages[] = "wget -q '{$mapping[$i]['imageurl']}' -O {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aResizeImages[] = "convert '{$sWorkingDir}{$iOutfitId}_{$i}.jpg' -resize 375x307\> -size 375x307 xc:white +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aCombineImages[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg -geometry +379+311  -composite ";
            $aFilesToDelete[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg ";
        } $i++;
        if( isset($mapping[$i]['imageurl']) && !empty($mapping[$i]['imageurl']) ) {
            $aResizeImages[] = "wget -q '{$mapping[$i]['imageurl']}' -O {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aResizeImages[] = "convert '{$sWorkingDir}{$iOutfitId}_{$i}.jpg' -resize 375x307\> -size 375x307 xc:white +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aCombineImages[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg -geometry +756+2  -composite ";
            $aFilesToDelete[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg ";
        } $i++;
        if( isset($mapping[$i]['imageurl']) && !empty($mapping[$i]['imageurl']) ) {
            $aResizeImages[] = "wget -q '{$mapping[$i]['imageurl']}' -O {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aResizeImages[] = "convert '{$sWorkingDir}{$iOutfitId}_{$i}.jpg' -resize 375x307\> -size 375x307 xc:white +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aCombineImages[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg -geometry +1133+2  -composite ";
            $aFilesToDelete[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg ";
        } $i++;
        if( isset($mapping[$i]['imageurl']) && !empty($mapping[$i]['imageurl']) ) {
            $aResizeImages[] = "wget -q '{$mapping[$i]['imageurl']}' -O {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aResizeImages[] = "convert '{$sWorkingDir}{$iOutfitId}_{$i}.jpg' -resize 752x307\> -size 752x307 xc:white +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}_{$i}.jpg";
            $aCombineImages[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg -geometry +756+311  -composite ";
            $aFilesToDelete[] = " {$sWorkingDir}{$iOutfitId}_{$i}.jpg ";
        } $i++;
        
//         echo implode(' && ', $aResizeImages) . ' && convert -size 1510x620 xc:grey ' . implode(' ', $aCombineImages) . " {$sWorkingDir}{$iOutfitId}.jpg && convert {$sWorkingDir}{$iOutfitId}.jpg -resize 1200x630 -size 1200x630 xc:#FFF +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}.jpg && rm -f " .  implode(' ', $aFilesToDelete);
//         die; 
        exec( implode(' && ', $aResizeImages) . ' && convert -size 1510x620 xc:grey ' . implode(' ', $aCombineImages) . " {$sWorkingDir}{$iOutfitId}.jpg && convert {$sWorkingDir}{$iOutfitId}.jpg -resize 1200x630 -size 1200x630 xc:#FFF +swap -gravity center  -composite {$sWorkingDir}{$iOutfitId}.jpg && rm -f " .  implode(' ', $aFilesToDelete) ); 

    }    
}
