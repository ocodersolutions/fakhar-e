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
use Application\Model\EngineTable;

class FeedDataTable extends BasicTableAdapter {
    protected $table = 'FeedData';
    protected $productAttributesTable = 'ProductAttributes';
    protected $images_table = 'FeedData_Images';
    protected $oFeedCatagory = null;
    protected $sDuplicateUpdateStr = '';

    public function initialize() {
        $updateAllowdItems = array('brand', 'caption', 'color', 'name', 'keywords', 'description', 'sku', 'currency', 'price', 'buyurl', 'impressionurl', 'imageurl', 'advertisercategory', 'instock', 'condition', 'format', 'manufacturerid', 'promotionaltext', 'retailprice', 'saleprice', 'standardshippingcost', 'thirdpartycategory', 'title', 'upc');
        
        $updateItems = array();
        $updateItems[] = "`item_count_temp` = `item_count_temp` + 1 ";
        foreach ($updateAllowdItems as $vItem) {
            $updateItems[] = "`$vItem` = VALUES( `$vItem` )";
        }

        /*
        $updateStr = ' ON DUPLICATE KEY UPDATE 
            `item_count`            = `item_count` + 1,
            `caption`               = VALUES(`caption`),
            `name`                  = VALUES(`name`),
            `keywords`              = VALUES(`keywords`),
            `description`           = VALUES(`description`),
            `sku`                   = VALUES(`sku`),
            `currency`              = VALUES(`currency`),
            `price`                 = VALUES(`price`),
            `buyurl`                = VALUES(`buyurl`),
            `impressionurl`         = VALUES(`impressionurl`),
            `imageurl`              = VALUES(`imageurl`),
            `advertisercategory`    = VALUES(`advertisercategory`),
            `instock`               = VALUES(`instock`),
            `condition`             = VALUES(`condition`),
            `format`                = VALUES(`format`),
            `manufacturerid`        = VALUES(`manufacturerid`),
            `promotionaltext`       = VALUES(`promotionaltext`),
            `retailprice`           = VALUES(`retailprice`),
            `saleprice`             = VALUES(`saleprice`),
            `standardshippingcost`  = VALUES(`standardshippingcost`),
            `thirdpartycategory`    = VALUES(`thirdpartycategory`),
            `title`                 = VALUES(`title`)
            '; */

        //if (count($updateItems)) 
        //{
        //    $updateStr = ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updateItems);
        //}

        $this->sDuplicateUpdateStr = ' ON DUPLICATE KEY UPDATE ' . implode(', ', $updateItems); 

        return $this;
    }

    public function insertData($aData) {
        $oAdapter = $this->getDBAdapter();
        $sql = new Sql($oAdapter);

        $oInsert = $sql->insert($this->table)->values($aData['basicData']);
        $sql->prepareStatementForSqlObject($oInsert);
        $sInsertString = $oInsert->getSqlString($oAdapter->getPlatform());
        //echo "$sInsertString \n"; exit(0);
        /* if( !empty($aInsertArray['fysCategory']) ) {
          $this->addToFeedImages( $aInsertArray['imageurl'], $this->getDBAdapter()->getDriver()->getLastGeneratedValue() );
          } */

//echo $sInsertString . $this->sDuplicateUpdateStr . "\n\n";
        $this->getDBAdapter()->query($sInsertString . $this->sDuplicateUpdateStr)->execute();
        
        $aUpdatedData = $this->getItemByUID( $aData['basicData']['uid'] );
        
        if( $aUpdatedData['productInfoAdded']  != 'yes' && @is_array($aData['attributes']) && @count($aData['attributes']) ) {
            $this->getServiceLocator()->get('ProductAttributesTable')->addProductAttributes($aData['basicData']['uid'], $aData['attributes']);
        }

        //return $this->getDBAdapter()->getDriver()->getLastGeneratedValue();
        return $aUpdatedData['uid'];
    }

    public function updateData($aData,$feedid){
      // echo '<pre style="color:blue">';
      // var_dump($aData);
      // echo '</pre>';  
      // die(__FILE__);
         $sql = new Sql($this->getDBAdapter());
         $update = new Update();
         $update->table($this->table)
                ->set($aData)
                ->where("id = '{$feedid}'");
         $sql->prepareStatementForSqlObject($update)->execute();

    }


    /*
      private function addToFeedImages( $sImage, $iProductId )
      {
      $this->getDBAdapter()
      ->query(  "INSERT INTO {$this->images_table} (`imageurl`, `productIds`)
      VALUES ('{$sImage}', '{$iProductId}')
      ON DUPLICATE KEY UPDATE `productIds` = CONCAT(`productIds`,',','{$iProductId}')"  )
      ->execute();
      }
     */

    public function truncateTables( $iFeedId ) { 
        /* $dbAdpr = $this->getDBAdapter();
          $dbAdpr->query( "TRUNCATE {$this->table};" )->execute();
          $dbAdpr->query( "TRUNCATE {$this->productAttributesTable};" )->execute(); */
        $sql = new Sql($this->getDBAdapter());

        $update = new Update();
        $update->table($this->table)->set(array('item_count_temp' => 0))->where("feedId = {$iFeedId}"); 
        $sql->prepareStatementForSqlObject($update)->execute();
        //echo $sql->getSqlstringForSqlObject($update);die(); 

        $this->getServiceLocator()->get('ProductAttributesTable')->reset( $iFeedId );
    }

    public function getImageURL($sProgramDir, $sImage) {
        $sql = new Sql($this->getDBAdapter());

        $select = new Select();
        $select->from(array('f' => $this->table))
                ->where("`img_program_dir` = '{$sProgramDir}' and `img_local_name` = '{$sImage}'");

        $data = $sql->prepareStatementForSqlObject($select)->execute()->current();
        if (isset($data['imageurl'])) {
            return $data['imageurl'];
        } else {
            return false;
        }
    }

    /*
      public function assignRandomProcessIds()
      {
      $sql = new Sql($this->getDBAdapter());

      $update = new Update();
      $update->table($this->images_table);
      $update->set( array('processId'=> new Expression('(FLOOR(1 + RAND() * 10))')));
      $sql->prepareStatementForSqlObject($update)->execute();
      }

      public function getFeedImagesByProcessId( $iProcessId )
      {
      $sql = new Sql($this->getDBAdapter());

      $select = new Select();
      $select->from(array('f' => $this->images_table))
      ->where( "processId = {$iProcessId}" );

      return $sql->prepareStatementForSqlObject($select)->execute();
      }

      public function updateFeedDataWithImageInfo( $aImage, $urlInfo )
      {
      $ext = 'unk';

      if( isset($urlInfo['content_type']) ) {

      switch (@$urlInfo['content_type']) {
      case 'image/jpeg':	$ext = 'jpg';	break;
      }

      if( $ext != 'unk' && isset($urlInfo['download_content_length']) && $urlInfo['download_content_length'] > 0 ) {
      $localFileName = md5(base64_encode($aImage['imageurl'].'_'.@$urlInfo['download_content_length'])) . ".{$ext}";
      }
      else {
      $localFileName = $urlInfo['content_type'] = $urlInfo['download_content_length'] = $aImage['programname'] = '';
      }
      }
      else {
      $localFileName = $urlInfo['content_type'] = $urlInfo['download_content_length'] = $aImage['programname'] = '';
      }


      $updateArr = array(
      'img_content_type'				=>	@$urlInfo['content_type'],
      'img_download_content_length'	=>	@$urlInfo['download_content_length'],
      'img_local_name'				=>	$localFileName
      );

      $sql = new Sql($this->getDBAdapter());
      $update = new Update();
      $update->table($this->table)->set( $updateArr )->where( "id in ({$aImage['productIds']})" );
      $sql->prepareStatementForSqlObject($update)->execute();
      }
     */
    /* public function getFeedData()
      {
      $sql = new Sql($this->getDBAdapter());

      $select = new Select();
      $select->from(array('f' => $this->table))
      ->where('fysCategory != "" and fysCategory is not null');//->limit(8);

      $paginator = new Paginator(new paginatorIterator($albums));
      return $paginator;

      //return $sql->prepareStatementForSqlObject($select)->execute();
      } */

    public function getFeedData($aPostParams = array(), $paginated = false, $userId = null, $bought = null, $service = null) {
        
        // $sWhere = ' 1 ';
        // $oEngine = new EngineTable( $this->getTableGateway() );
        // $oEngine->setServiceLocator( $this->getServiceLocator() );
        
        // if( isset($service) && !empty($service) ) {

        //     $sWhere .= ' AND ' . $oEngine->profileBasedProductsQuery();
        //     $sWhere .= ' AND item_count > 0 '; 
            
        // }
         
        // if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'yes') {
        //     $sWhere .= " AND `productInfoAdded` = 'yes' ";

        //     if (isset($aPostParams['start_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['start_date'])) {
        //         $sWhere .= " AND `productInfoAddDate` >= '{$aPostParams['start_date']}' ";
        //     }

        //     if (isset($aPostParams['end_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['end_date'])) {
        //         $sWhere .= " AND `productInfoAddDate` <= '{$aPostParams['end_date']}' ";
        //     }
        // } else if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'no') {
        //     $sWhere .= " AND `productInfoAdded` = 'no' ";
        // }

        // if (isset($aPostParams['category']) && $aPostParams['category'] != 'all') {
        //     $sWhere .= " AND `advertisercategory` = '{$aPostParams['category']}' ";
        // }

        if ($paginated) {
            $feedmapbrandJoin = false;
            $prodattJoin = false;

            $sWhere = ' 1 ';
            $oEngine = new EngineTable( $this->getTableGateway() );
            $oEngine->setServiceLocator( $this->getServiceLocator() );
        if( isset($aPostParams['profileBasePrices']) && $aPostParams['profileBasePrices'] ==1 ) {
            
            $sProfileBaseQuery = $oEngine->profileBasedProductsQuery();
            $sWhere = $sProfileBaseQuery;
        }
            
        $sWhere .= ' AND item_count > 0 ';
        if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'yes') {
            $sWhere .= " AND `productInfoAdded` = 'yes' ";

            if (isset($aPostParams['start_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['start_date'])) {
                $sWhere .= " AND `productInfoAddDate` >= '{$aPostParams['start_date']}' ";
            }

            if (isset($aPostParams['end_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['end_date'])) {
                $sWhere .= " AND `productInfoAddDate` <= '{$aPostParams['end_date']}' ";
            }
        } else if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'no') {
            $sWhere .= " AND `productInfoAdded` = 'no' ";
        }

        if (isset($aPostParams['category']) && $aPostParams['category'] != 'all') {
            $sWhere .= " AND `advertisercategory` = '{$aPostParams['category']}' ";
        }

        if (!empty($aPostParams['catids'])) {
            $sWhere .= " AND (prodatt.type = 'categories' AND prodatt.value IN (" . $aPostParams['catids'] . ") )";
            $prodattJoin = true;
        }

        if (!empty($aPostParams['colors'])) {
            //$sWhere.= " AND FIND_IN_SET(feed.color,'{$aPostParams['colors']}')";
            $sWhere .= " AND (prodatt.type = 'color' AND prodatt.value IN ('" . str_replace(',', "','", $aPostParams['colors']) . "') )"; 
            $prodattJoin = true;            
        }
        if (!empty($aPostParams['stores'])) {
            // $sWhere .= " AND FIND_IN_SET(feedmapbrand.id,'{$aPostParams['brands']}')";
            /* $allStores=  explode(',',$aPostParams['stores']);
              if(!empty($allStores))
              {
              $sWhere.= " AND FIND_IN_SET(feed.id,feedmapstore.value)";
              } */
            $sWhere.=" AND feedmapstore.id IN ({$aPostParams['stores']})";
            $feedmapbrandJoin = true;
        }
        if (!empty($aPostParams['deals'])) {
            
        }

        if (!empty($aPostParams['infoadded'])) {
             $sWhere.=" AND productInfoAdded = '{$aPostParams['infoadded']}'";
        }

        if (!empty($aPostParams['checknocolor']) && ($aPostParams['checknocolor']=='yes')) {
            //$sWhere.=" AND (prodatt.type ='color' AND prodatt.value='')";
            $sWhere.=" AND 'Color' NOT IN (Select distinct type from ProductAttributes where productUID=prodatt.productUID)";
            $prodattJoin = true; 
        }

        // if( !isset($aPostParams['profileBasePrices']) || $aPostParams['profileBasePrices'] != 1 ) {
            if( isset($aPostParams['minamount']) && $aPostParams['minamount'] > 0 ) {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) > {$aPostParams['minamount']} ";
            } else {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) > 0 ";
            }
            if( isset($aPostParams['maxamount']) && $aPostParams['maxamount'] < 1000 && $aPostParams['maxamount'] > 0 ) {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) < {$aPostParams['maxamount']} ";
            }
        // }

        if (!empty($aPostParams['duration'])) {
            
        }

        if (!empty($aPostParams['onsale'])) {
            $sWhere.=" AND feed.onsale='Y'";
        }
        if (!empty($aPostParams['todaynew'])) {
            $sWhere.=" AND feed.dateadded=CURRENT_DATE";
        }
        if (!empty($aPostParams['newthisweek'])) {
            $sWhere.=" AND feed.dateadded BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL (SELECT DAYOFWEEK(NOW())+1) DAY) AND CURRENT_DATE";
        }
        if (!empty($aPostParams['freeshipping'])) {
            $sWhere.=" AND feed.freeshipping='Y'";
        }
        if (!empty($aPostParams['specialoffer'])) {
            $sWhere.=" AND feed.specialoffer='Y'";
        }
        if (!empty($aPostParams['discountcode'])) {
            $sWhere.="  AND feed.onsale='Y' AND ((feed.price-feed.saleprice)/feed.price*100)>={$aPostParams['minduration']} AND ((feed.price-feed.saleprice)/feed.price*100)<={$aPostParams['maxduration']} ";
        }

        if (!empty($aPostParams['brands'])) {
            $feedmapbrandJoin = true;
            $sWhere .= " AND FIND_IN_SET(feedmapbrand.id,'{$aPostParams['brands']}')";
        }
        
        if (!empty($aPostParams['filter'])) {
          $sWhere.=" AND (feed.description like '%{$aPostParams['filter']}%' OR feed.caption like '%{$aPostParams['filter']}%' OR feed.name like '%{$aPostParams['filter']}%' OR feed.keywords like '%{$aPostParams['filter']}%' OR feed.sku = '{$aPostParams['filter']}' OR feed.upc= '{$aPostParams['filter']}')" ;
        }

       // $offSet = (isset($aPostParams['offset'])) ? intval($aPostParams['offset']) : 0;
        //$limit = (isset($aPostParams['limit'])) ? intval($aPostParams['limit']) : PRODUCTS_PER_PAGE;
        
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('feed' => $this->table)); 
        $select->columns(array('feeddataid' => 'id', 'feedId', 'uid', 'caption' => 'caption', 'color' => 'color', 'name' => 'name', 'sku' => 'sku', 'currency', 'price', 'buyurl', 'impressionurl', 'onsale', 'saleprice', 'img_local_name', 'feedimageurl' => 'imageurl','productInfoAdded'));
        if( isset($service) && !empty($service) ) {
            $oEngine->styleBasedProductsQuery( $select );
        }        
        $select->join(array("Al" => 'ArticleCloseset'), new Expression("(Al.feeddataid = feed.id)"), array('likesCount' => new Expression('COUNT(Al.closesetid)')), 'left')
                ->where($sWhere)
                ->group('feed.id');
                //->limit($limit)
                //->offset($offSet);

        if(isset($service) && !empty($service)){
            $select->join(array("All" => 'ArticleCloseset'), new Expression("(`All`.userId = $userId AND `All`.feeddataid = feed.id)"), array('mylike' => new Expression('COUNT(All.closesetid)')), 'left');
        }
        if (isset($bought) && !empty($bought) && $bought) {
            $select->join(array("Ab" => 'ArticleBought'), new Expression("(Ab.feeddataid = feed.id)"), array(), 'left');
            $select->where(array('Ab.userId' => $userId));
        }
        if ($prodattJoin) {
            $select->join(array("prodatt" => 'ProductAttributes'), new Expression("(feed.uid=prodatt.productUID) and `attribute_count` > 0")) 
                   ->group('prodatt.productUID');
        }
        if ($feedmapbrandJoin) {
            $select->join(array("feedmapbrand" => 'FeedsMapping'), new Expression("(feedmapbrand.title=feed.brand AND feedmapbrand.mappingType='brand')"), array());
        }
        if (!empty($aPostParams['stores'])) {
            $select->join(array("feedmapstore" => 'FeedsMapping'), new Expression("(feedmapstore.value=feed.feedId AND feedmapstore.mappingType='store')"), array());
        }
        if (isset($userId) && !empty($userId) && empty($bought) && empty($service)) {
            $select->where(array('Al.userId' => $userId));
        }

        $orderBy = 'feed.sortOrder desc';
        if (!empty($aPostParams['orderBy'])) {
          switch ($aPostParams['orderBy']) {
            case 'lowest':
              $orderBy = 'feed.saleprice asc';
              break;
            case 'highest':
              $orderBy = 'feed.saleprice desc';
              break;
            case 'newest':
              $orderBy = 'feed.id desc';
              break;
          }
        }
        $select->order('feed.id asc');

            //$paginator = $sql->prepareStatementForSqlObject($select)->execute();
            //echo $sql->getSqlStringForSqlObject($select); die; 
            $resultSetPrototype = new ResultSet();
            $resultSetPrototype->setArrayObjectPrototype(new FeedData());
            $paginatorAdapter = new DbSelect(
                    $select, $this->tableGateway->getAdapter(), $resultSetPrototype
            );
            $paginator = new Paginator($paginatorAdapter);
            
        
            return $paginator;
        }
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }

    public function getItem($iPID) {
        $sql = new Sql($this->getDBAdapter());

        $select = new Select();
        $select->from(array('t' => $this->table))
                ->join(array("f" => 'Feeds'), new Expression("(t.feedId=f.id)"), array('programname', 'programurl'), 'left')
                ->join(array("Al" => 'ArticleCloseset'), new Expression("(Al.feeddataid = t.id)"), array('likesCount' => new Expression('COUNT(distinct Al.closesetid)')), 'left')
                ->where("t.id = {$iPID}");
        //echo $sql->getSqlStringForSqlObject($select); die;
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }

    public function getItemByUID($sUID) {
        $sql = new Sql($this->getDBAdapter());
    
        $select = new Select();
        $select->from(array('t' => $this->table))->where("t.uid = '{$sUID}'");
        //echo $sql->getSqlStringForSqlObject($select); die;
        return $sql->prepareStatementForSqlObject($select)->execute()->current();
    }
        
    
    public function getAdvertiserCaegoryList() {
        $sql = new Sql($this->getDBAdapter());

        $select = new Select();
        $select->from(array('t' => $this->table))
                ->columns(array('advertisercategory'))
                ->group('advertisercategory')
                ->order('advertisercategory');

        return $sql->prepareStatementForSqlObject($select)->execute();
    }

    public function finalize( $iFeedId ) {
      
          
    }
    
    public function globalFinalize() {
        $sql = new Sql($this->getDBAdapter());
    
        $update = new Update();
        $update->table($this->table)->set(array('img_local_name' => new Expression('id')))->where("(img_local_name is null or img_local_name = '')");
        $sql->prepareStatementForSqlObject($update)->execute();
    
        $update = new Update();
        $update->table($this->table)
                    ->set(array(
                        'item_count' => new Expression('item_count_temp'),
                        'onsale' => new Expression("(if(saleprice!='' and saleprice is not null and saleprice > 0 and price > saleprice , 'Y', 'N'))"),
                        'caption' => new Expression("(if(caption='' or caption is null, name, caption))")
                    ));
        $sql->prepareStatementForSqlObject($update)->execute();
         
        $this->getDBAdapter()->query("update FeedData set sortOrder = NULL;")->execute();
        $this->getDBAdapter()->query("update FeedData set sortOrder = FLOOR(RAND()*(50000-35000)+35000) where sortOrder is null and  uid in ( select distinct productUID from ProductAttributes where type = 'categories' and value in (6,7,36,37) );")->execute();
        $this->getDBAdapter()->query("update FeedData set sortOrder = FLOOR(RAND()*(50000-30000)+30000) where sortOrder is null and   uid in ( select distinct productUID from ProductAttributes where type = 'categories' and value in (4) );")->execute();
        $this->getDBAdapter()->query("update FeedData set sortOrder = FLOOR(RAND()*(40000-1)+100) where sortOrder is null and   uid in ( select distinct productUID from ProductAttributes where type = 'categories' and value in (68,63) );")->execute();
    
    }    

    public function updateLocalImage($iPID, $sFileName) {
        $sql = new Sql($this->getDBAdapter()); 
        $update = new Update();
        $update->table($this->table)
                ->set(array('img_local_name' => $sFileName))
                ->where("id = {$iPID}");
        $sql->prepareStatementForSqlObject($update)->execute();
    }

    public function getFilterFeedData($aPostParams = array(), $paginated = false, $userId = null, $bought = null,$service = null, $returnArr = true) {
        $feedmapbrandJoin = false;
        $prodattJoin = false;

        $sWhere = ' 1 ';
        $oEngine = new EngineTable( $this->getTableGateway() );
        $oEngine->setServiceLocator( $this->getServiceLocator() );
        if($userId != 0){
          if( isset($aPostParams['profileBasePrices']) && $aPostParams['profileBasePrices'] != 1 ) {
              $sProfileBaseQuery = $oEngine->profileBasedProductsQuery();
              $sWhere = $sProfileBaseQuery;
          }
        }

        $sWhere .= ' AND item_count > 0 ';
        if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'yes') {
            $sWhere .= " AND `productInfoAdded` = 'yes' ";

            if (isset($aPostParams['start_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['start_date'])) {
                $sWhere .= " AND `productInfoAddDate` >= '{$aPostParams['start_date']}' ";
            }

            if (isset($aPostParams['end_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['end_date'])) {
                $sWhere .= " AND `productInfoAddDate` <= '{$aPostParams['end_date']}' ";
            }
        } else if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'no') {
            $sWhere .= " AND `productInfoAdded` = 'no' ";
        }

        if (isset($aPostParams['category']) && $aPostParams['category'] != 'all') {
            $sWhere .= " AND `advertisercategory` = '{$aPostParams['category']}' ";
        }

        if (isset($aPostParams['searchArticle']) && !empty($aPostParams['searchArticle']) ) {
            
            $searcharray = explode(",",$aPostParams['searchArticle']);
            foreach ($searcharray as $key => $value) {
              $sWhere .= " AND `feed`.`name` LIKE '%".$value."%' ";
            }
        }

        if (isset($aPostParams['searchVenue']) && !empty($aPostParams['searchVenue']) ) {
            
            $searcharray = explode(",",$aPostParams['searchVenue']);
            foreach ($searcharray as $key => $value) {
              $sWhere .= " AND `feed`.`name` LIKE '%".$value."%' ";
            }
        }
        
        if (!empty($aPostParams['catids'])) {
            $sWhere .= " AND `feed`.`uid` IN (select distinct productUID from ProductCategories where value IN (" . $aPostParams['catids'] . ") )";
            //$prodattJoin = true;
        }

        if (!empty($aPostParams['colors'])) {
            //$sWhere.= " AND FIND_IN_SET(feed.color,'{$aPostParams['colors']}')";
            $sWhere .= " AND `feed`.`uid` IN (select distinct productUID from ProductAttributes where type = 'color' AND value IN ('" . str_replace(',', "','", $aPostParams['colors']) . "') )"; 
            //$prodattJoin = true;            
        }
        if (!empty($aPostParams['stores'])) {
            // $sWhere .= " AND FIND_IN_SET(feedmapbrand.id,'{$aPostParams['brands']}')";
            /* $allStores=  explode(',',$aPostParams['stores']);
              if(!empty($allStores))
              {
              $sWhere.= " AND FIND_IN_SET(feed.id,feedmapstore.value)";
              } */
            $sWhere.=" AND feedmapstore.id IN ({$aPostParams['stores']})";
            $feedmapbrandJoin = true;
        }
        if (!empty($aPostParams['deals'])) {
            
        }

        if (!empty($aPostParams['infoadded'])) {
             $sWhere.=" AND productInfoAdded = '{$aPostParams['infoadded']}'";
        }

        if (!empty($aPostParams['checknocolor']) && ($aPostParams['checknocolor']=='yes')) {
            //$sWhere.=" AND (prodatt.type ='color' AND prodatt.value='')";
            //$sWhere.=" AND 'Color' NOT IN (Select distinct type from ProductAttributes where productUID=prodatt.productUID)";
            $sWhere.=" AND `feed`.`uid` IN (select distinct productUID from ProductAttributes where productUID not in (select distinct productUID from ProductAttributes where type = 'Color')) ";
            $prodattJoin = true; 
        }
        if( !isset($aPostParams['profileBasePrices']) || $aPostParams['profileBasePrices'] == 1 ) {
            if( isset($aPostParams['minamount']) && $aPostParams['minamount'] > 0 ) {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) > {$aPostParams['minamount']} ";
            } else {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) > 0 ";
            }

            if( isset($aPostParams['maxamount']) && $aPostParams['maxamount'] < 1000 && $aPostParams['maxamount'] > 0 ) {
              
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) < {$aPostParams['maxamount']} ";
              

            }
        }
        if (!empty($aPostParams['duration'])) {
            
        }

        if (!empty($aPostParams['onsale'])) {
            $sWhere.=" AND feed.onsale='Y'";
        }
        if (!empty($aPostParams['todaynew'])) {
            $sWhere.=" AND feed.dateadded=CURRENT_DATE";
        }
        if (!empty($aPostParams['newthisweek'])) {
            $sWhere.=" AND feed.dateadded BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL (SELECT DAYOFWEEK(NOW())+1) DAY) AND CURRENT_DATE";
        }
        if (!empty($aPostParams['freeshipping'])) {
            $sWhere.=" AND feed.freeshipping='Y'";
        }
        if (!empty($aPostParams['specialoffer'])) {
            $sWhere.=" AND feed.specialoffer='Y'";
        }
        if (!empty($aPostParams['discountcode'])) {
            $sWhere.="  AND feed.onsale='Y' AND ((feed.price-feed.saleprice)/feed.price*100)>={$aPostParams['minduration']} AND ((feed.price-feed.saleprice)/feed.price*100)<={$aPostParams['maxduration']} ";
        }

        if (!empty($aPostParams['brands'])) {
            $feedmapbrandJoin = true;
            $sWhere .= " AND FIND_IN_SET(feedmapbrand.id,'{$aPostParams['brands']}')";
        }
        
        if (!empty($aPostParams['filter'])) {
          $sWhere.=" AND (feed.description like '%{$aPostParams['filter']}%' OR feed.caption like '%{$aPostParams['filter']}%' OR feed.name like '%{$aPostParams['filter']}%' OR feed.keywords like '%{$aPostParams['filter']}%' OR feed.sku = '{$aPostParams['filter']}' OR feed.upc= '{$aPostParams['filter']}')" ;
        }

        $offSet = (isset($aPostParams['offset'])) ? intval($aPostParams['offset']) : 0;
        $limit = (isset($aPostParams['limit'])) ? intval($aPostParams['limit']) : PRODUCTS_PER_PAGE;
        
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('feed' => $this->table));
        $select->columns(array('feeddataid' => 'id', 'feedId', 'uid', 'caption' => 'caption', 'color' => 'color', 'name' => 'name', 'sku' => 'sku', 'currency', 'price', 'buyurl', 'impressionurl', 'onsale', 'saleprice', 'img_local_name', 'feedimageurl' => 'imageurl','productInfoAdded'));
        if( isset($service) && !empty($service) ) {
            $oEngine->styleBasedProductsQuery( $select ); 
        }        
        $select->join(array("Al" => 'ArticleCloseset'), new Expression("(Al.feeddataid = feed.id)"), array('likesCount' => new Expression('COUNT(Al.closesetid)')), 'left')
                ->where($sWhere)
                ->group('feed.id')
                ->limit($limit)
                ->offset($offSet);

        if(isset($service) && !empty($service)){
            $select->join(array("All" => 'ArticleCloseset'), new Expression("(`All`.userId = $userId AND `All`.feeddataid = feed.id)"), array('mylike' => new Expression('COUNT(All.closesetid)')), 'left');
        }
        if (isset($bought) && !empty($bought) && $bought) {
            $select->join(array("Ab" => 'ArticleBought'), new Expression("(Ab.feeddataid = feed.id)"), array(), 'left');
            $select->where(array('Ab.userId' => $userId));
        }
        if(isset($aPostParams['filterTypeMain']) && $aPostParams['filterTypeMain'] == 'saveitem'){
            $select->join(array("Ab" => 'ArticleCloseset'), new Expression("(Ab.feeddataid = feed.id)"), array(), 'left');
            $select->where(array('Ab.userId' => $userId)); 
        }
        if(isset($aPostParams['filterTypeMain']) && $aPostParams['filterTypeMain'] == 'sale'){
            $select->join(array("Ab" => 'ArticleBought'), new Expression("(Ab.feeddataid = feed.id)"), array(), 'left');
            $select->where(array('Ab.userId' => $userId)); 
        }
        
        if ($prodattJoin) {
            $select->join(array("prodatt" => 'ProductAttributes'), new Expression("(feed.uid=prodatt.productUID) and `attribute_count` > 0")) 
                   ->group('prodatt.productUID');
        }
        if ($feedmapbrandJoin) {
            $select->join(array("feedmapbrand" => 'FeedsMapping'), new Expression("(feedmapbrand.title=feed.brand AND feedmapbrand.mappingType='brand')"), array());
        }
        if (!empty($aPostParams['stores'])) {
            $select->join(array("feedmapstore" => 'FeedsMapping'), new Expression("(feedmapstore.value=feed.feedId AND feedmapstore.mappingType='store')"), array());
        }
        if (isset($userId) && !empty($userId) && empty($bought) && empty($service)) {

            $select->where(array('Al.userId' => $userId));
        }
        
        // $select->order('feed.sortOrder desc');
        $orderBy = 'feed.sortOrder desc';
        if (!empty($aPostParams['orderBy'])) {
          switch ($aPostParams['orderBy']) {
            case 'lowest':
              $orderBy = 'feed.saleprice asc';
              break;
            case 'highest':
              $orderBy = 'feed.saleprice desc';
              break;
            case 'newest':
              $orderBy = 'feed.id desc';
              break;
          }
        }
        $select->order($orderBy);
        
        $resultSet = array();
        //echo $sql->getSqlStringForSqlObject($select); die;
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        if($returnArr == true) {
          $resultSet = $resultSet->toArray();
        }
        return $resultSet;
    }
    public function countFilterFeedData($aPostParams = array(), $paginated = false, $userId = null, $bought = null,$service = null) {

        $feedmapbrandJoin = false;
        $prodattJoin = false;

        $sWhere = ' 1 ';
        $oEngine = new EngineTable( $this->getTableGateway() );
        $oEngine->setServiceLocator( $this->getServiceLocator() );
        if( isset($aPostParams['profileBasePrices']) && $aPostParams['profileBasePrices'] ==1 ) {
            $sProfileBaseQuery = $oEngine->profileBasedProductsQuery();
            $sWhere = $sProfileBaseQuery;
        }
            
        $sWhere .= ' AND item_count > 0 ';
        if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'yes') {
            $sWhere .= " AND `productInfoAdded` = 'yes' ";

            if (isset($aPostParams['start_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['start_date'])) {
                $sWhere .= " AND `productInfoAddDate` >= '{$aPostParams['start_date']}' ";
            }

            if (isset($aPostParams['end_date']) && preg_match("/^\d{4}-\d{1,2}-\d{1,2}/", $aPostParams['end_date'])) {
                $sWhere .= " AND `productInfoAddDate` <= '{$aPostParams['end_date']}' ";
            }
        } else if (isset($aPostParams['infoStatus']) && $aPostParams['infoStatus'] == 'no') {
            $sWhere .= " AND `productInfoAdded` = 'no' ";
        }

        if (isset($aPostParams['category']) && $aPostParams['category'] != 'all') {
            $sWhere .= " AND `advertisercategory` = '{$aPostParams['category']}' ";
        }

        if (!empty($aPostParams['catids'])) {
            $sWhere .= " AND `feed`.`uid` IN (select distinct productUID from ProductAttributes where type = 'categories' AND value IN (" . $aPostParams['catids'] . ") )";
            //$prodattJoin = true;
        }

        if (!empty($aPostParams['colors'])) {
            //$sWhere.= " AND FIND_IN_SET(feed.color,'{$aPostParams['colors']}')";
            $sWhere .= " AND `feed`.`uid` IN (select distinct productUID from ProductAttributes where type = 'color' AND value IN ('" . str_replace(',', "','", $aPostParams['colors']) . "') )"; 
            //$prodattJoin = true;            
        }
        if (!empty($aPostParams['stores'])) {
            // $sWhere .= " AND FIND_IN_SET(feedmapbrand.id,'{$aPostParams['brands']}')";
            /* $allStores=  explode(',',$aPostParams['stores']);
              if(!empty($allStores))
              {
              $sWhere.= " AND FIND_IN_SET(feed.id,feedmapstore.value)";
              } */
            $sWhere.=" AND feedmapstore.id IN ({$aPostParams['stores']})";
            $feedmapbrandJoin = true;
        }
        if (!empty($aPostParams['deals'])) {
            
        }

        if (!empty($aPostParams['infoadded'])) {
             $sWhere.=" AND productInfoAdded = '{$aPostParams['infoadded']}'";
        }

        if (!empty($aPostParams['checknocolor']) && ($aPostParams['checknocolor']=='yes')) {
            //$sWhere.=" AND (prodatt.type ='color' AND prodatt.value='')";
            //$sWhere.=" AND 'Color' NOT IN (Select distinct type from ProductAttributes where productUID=prodatt.productUID)";
            $sWhere.=" AND `feed`.`uid` IN (select distinct productUID from ProductAttributes where productUID not in (select distinct productUID from ProductAttributes where type = 'Color')) ";
            $prodattJoin = true; 
        }

        // if( !isset($aPostParams['profileBasePrices']) || $aPostParams['profileBasePrices'] != 1 ) {
            if( isset($aPostParams['minamount']) && $aPostParams['minamount'] > 0 ) {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) > {$aPostParams['minamount']} ";
            } else {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) > 0 ";
            }
            if( isset($aPostParams['maxamount']) && $aPostParams['maxamount'] < 1000 && $aPostParams['maxamount'] > 0 ) {
                $sWhere .= " AND IF(feed.onsale='Y',feed.saleprice,feed.price) < {$aPostParams['maxamount']} ";
            }
        // }

        if (!empty($aPostParams['duration'])) {
            
        }

        if (!empty($aPostParams['onsale'])) {
            $sWhere.=" AND feed.onsale='Y'";
        }
        if (!empty($aPostParams['todaynew'])) {
            $sWhere.=" AND feed.dateadded=CURRENT_DATE";
        }
        if (!empty($aPostParams['newthisweek'])) {
            $sWhere.=" AND feed.dateadded BETWEEN DATE_SUB(CURRENT_DATE, INTERVAL (SELECT DAYOFWEEK(NOW())+1) DAY) AND CURRENT_DATE";
        }
        if (!empty($aPostParams['freeshipping'])) {
            $sWhere.=" AND feed.freeshipping='Y'";
        }
        if (!empty($aPostParams['specialoffer'])) {
            $sWhere.=" AND feed.specialoffer='Y'";
        }
        if (!empty($aPostParams['discountcode'])) {
            $sWhere.="  AND feed.onsale='Y' AND ((feed.price-feed.saleprice)/feed.price*100)>={$aPostParams['minduration']} AND ((feed.price-feed.saleprice)/feed.price*100)<={$aPostParams['maxduration']} ";
        }

        if (!empty($aPostParams['brands'])) {
            $feedmapbrandJoin = true;
            $sWhere .= " AND FIND_IN_SET(feedmapbrand.id,'{$aPostParams['brands']}')";
        }
        
        if (!empty($aPostParams['filter'])) {
          $sWhere.=" AND (feed.description like '%{$aPostParams['filter']}%' OR feed.caption like '%{$aPostParams['filter']}%' OR feed.name like '%{$aPostParams['filter']}%' OR feed.keywords like '%{$aPostParams['filter']}%' OR feed.sku = '{$aPostParams['filter']}' OR feed.upc= '{$aPostParams['filter']}')" ;
        }

        //$offSet = (isset($aPostParams['offset'])) ? intval($aPostParams['offset']) : 0;
        //$limit = (isset($aPostParams['limit'])) ? intval($aPostParams['limit']) : PRODUCTS_PER_PAGE;
        
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('feed' => $this->table));
        $select->columns(array('feeddataid' => 'id', 'feedId', 'uid', 'caption' => 'caption', 'color' => 'color', 'name' => 'name', 'sku' => 'sku', 'currency', 'price', 'buyurl', 'impressionurl', 'onsale', 'saleprice', 'img_local_name', 'feedimageurl' => 'imageurl','productInfoAdded'));
        if( isset($service) && !empty($service) ) {
            $oEngine->styleBasedProductsQuery( $select );
        }        
        $select->join(array("Al" => 'ArticleCloseset'), new Expression("(Al.feeddataid = feed.id)"), array('likesCount' => new Expression('COUNT(Al.closesetid)')), 'left')
                ->where($sWhere)
                ->group('feed.id');
                // ->limit($limit)
                // ->offset($offSet);

        if(isset($service) && !empty($service)){
            $select->join(array("All" => 'ArticleCloseset'), new Expression("(`All`.userId = $userId AND `All`.feeddataid = feed.id)"), array('mylike' => new Expression('COUNT(All.closesetid)')), 'left');
        }
        if (isset($bought) && !empty($bought) && $bought) {
            $select->join(array("Ab" => 'ArticleBought'), new Expression("(Ab.feeddataid = feed.id)"), array(), 'left');
            $select->where(array('Ab.userId' => $userId));
        }
        if ($prodattJoin) {
            $select->join(array("prodatt" => 'ProductAttributes'), new Expression("(feed.uid=prodatt.productUID) and `attribute_count` > 0")) 
                   ->group('prodatt.productUID');
        }
        if ($feedmapbrandJoin) {
            $select->join(array("feedmapbrand" => 'FeedsMapping'), new Expression("(feedmapbrand.title=feed.brand AND feedmapbrand.mappingType='brand')"), array());
        }
        if (!empty($aPostParams['stores'])) {
            $select->join(array("feedmapstore" => 'FeedsMapping'), new Expression("(feedmapstore.value=feed.feedId AND feedmapstore.mappingType='store')"), array());
        }
        if (isset($userId) && !empty($userId) && empty($bought) && empty($service)) {

            $select->where(array('Al.userId' => $userId));
        }
        
        $select->order('feed.sortOrder desc');

        $resultSet = array();
        //echo $sql->getSqlStringForSqlObject($select); die('abc');
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        return  $resultSet->count();
       // $resultSet = $resultSet->toArray();
       
       // return $resultSet;
    }

    public function getMaxRangePrice() {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('feed' => $this->table));
        $select->columns(array('MAXPRICE' => new Expression('MAX(price)')));
        $resultSet = array();
        $results = $sql->prepareStatementForSqlObject($select)->execute();
        $resultSet = new \Zend\Db\ResultSet\ResultSet();
        $resultSet->initialize($results);
        $resultSet = $resultSet->toArray();
        $maxPrice = number_format($resultSet[0]['MAXPRICE'], 0);
        $priceLen = strlen($maxPrice) - 1;
        $maxPrice = substr($maxPrice, 0, 1) + 1;
        $maxPrice = $maxPrice * ( pow(10, $priceLen));
        return $maxPrice;
    }

    public function getProductsForArticleManagement($conditions = array() ) {
        $sql = new Sql($this->getServiceLocator()->get('db'));
        $select = $sql->select(array('p' => $this->table));
        $select->columns(array('feeddataid' => 'id', 'feedId', 'uid', 'caption' => 'caption', 'color' => 'color', 'name' => 'name', 'sku' => 'sku', 'currency', 'price', 'saleprice', 'buyurl', 'impressionurl', 'onsale', 'img_local_name', 'feedimageurl' => 'imageurl', 'productInfoAdded'));
        $select->join(array("prodatt" => 'ProductAttributes'), new Expression("(p.uid=prodatt.productUID)"));
        $select->group('p.uid');        
        
        $select->where('p.item_count > 0');
        
        if( !isset($conditions['post']) ) {
            $select->where(" (prodatt.type = 'categories' AND prodatt.value IN (2) )");
        }
        else {
            
            if( isset($conditions['category']) && !empty($conditions['category']) ) {
                $select->where(" (prodatt.type = 'categories' AND prodatt.value IN (".implode(',', $conditions['category']).") )");
            }
            if( isset($conditions['infoAdded']) && !empty($conditions['infoAdded']) ) {
                $select->where("p.productInfoAdded ='{$conditions['infoAdded']}'");
            }
            
        }
        
        //echo $sql->getSqlstringForSqlObject($select); die;
        
        // create a new result set based on the Album entity
        $resultSetPrototype = new ResultSet();
        $resultSetPrototype->setArrayObjectPrototype(new FeedData());
        // create a new pagination adapter object
        $paginatorAdapter = new DbSelect(
            // our configured select object
            $select,
            // the adapter to run it against
            $this->tableGateway->getAdapter(),
            // the result set to hydrate
            $resultSetPrototype
        );
        $paginator = new Paginator($paginatorAdapter);
        return $paginator;
    }    
    
    public function test()
    {

        function fix($brand) {
            $arr = array(
               "180'S", 'ADIDAS', 'ALFRED SUNG', 'ANNICK GOUTAL', 'ARMANI EXCHANGE', 'ARMANI JEANS', 'BEN SHERMAN', 'BILLIONAIRE BOYS CLUB', 'BLACK BROWN 1826',
                'BOND NO 9', 'BOSS', 'BRUUN AND STENGADE', 'BURBERRY', 'CALVIN KLEIN', 'CAROLEE', 'CASIO', 'CHANEL ALLURE HOMME', 'CHANEL BLEU DE CHANEL', 'CUFFWEAR', 'DECIEM',
                'DIESEL', 'DIOR', 'DOCKERS', 'DOLCE & GABBANA', 'EMPORIO ARMANI', 'FASHION FAIR', 'G-STAR', 'GIORGIO ARMANI', 'GUCCI', 'HAGGAR', 'HAIGHT AND ASHBURY', 'HANAE MORI PERFUMES',
                'HERSCHEL SUPPLY CO', "HUDSON'S BAY COMPANY", 'HUGO', 'ISSEY MIYAKE', 'JACK BLACK', 'JOCKEY', 'JOE BOXER', 'KENNETH COLE REACTION', 'LAB SERIES', 'LACOSTE',
                "LEVI'S", 'LISE WATIER', 'MCGREGOR', 'NEW BALANCE', 'PKG LB04 DRI', 'POLO RALPH LAUREN', 'POLO SPORT', 'PUMA', 'RED CANOE', 'REEBOK', 'ROGER & GALLET', 'ROGUE STATE',
                'SATURDAYS SURF', 'SEIKO', 'SELECTED HOMME', 'SPEEDO', 'SPERRY', "STANFIELD'S", 'STRELLSON', 'TAG HEUER', 'THIERRY MUGLER', 'TOMMY BAHAMA', 'TOMMY HILFIGER', 'TOPMAN', 'UNDER ARMOUR', 'VICTORINOX',
                'VICTORINOX SWISS ARMY', 'VILLEROY & BOCH', 'YVES SAINT LAURENT', 'UNDER ARMOUR', 'CARRERA', 'DKNY JEANS', 'DR. HAUSCHKA', 'ENGLISH LAUNDRY', 'FILIPPA K',
                'FOSSIL', 'FULTON', 'GUERLAIN', 'JACK OF ALL TRADES', 'KITCHENAID', 'LINEA IN', 'MOVADO', 'NIKE', 'OBEY', 'OPI', 'ORIGINS', 'SHISEIDO', 'SUPERDRY', 'TED BAKER', 'TIMEX'
            );
            
            foreach($arr as $v) {
                if (stripos($brand, $v) !== false) {
                    return $v;
                }
            }
            
            return $brand;
        }
        
        $sQuery = 'SELECT keywords FROM FeedData where feedId = 3;';
        $statement = $this->getDBAdapter()->query($sQuery);
        $oMapping = $statement->execute();
        
        $aBrand = array();
        
        foreach($oMapping as $vMap) {
            $aTemp = array();
            foreach(explode(' ', $vMap['keywords']) as $v) {
                if(preg_match('/[a-z,]/', $v)) break;
                else $aTemp[] = $v;
            }
            @$aBrand[ fix(implode(' ', $aTemp)) ]++;
        }        
        
        $aBrand = array_merge($aBrand, array_flip(array("3.1 Phillip Lim","A.P.C.","Acne Studios","Adieu","Alexander McQueen","AMI Alexandre Mattiussi","Ann Demeulemeester","Arc'teryx Veilance","Balenciaga","Balmain","Bao Bao Issey Miyake","Belstaff","Black Limited Edition","Boris Bidjan Saberi","Burberry Brit","Burberry London","Burberry Prorsum","Calvin Klein Underwear","Canada Goose","Carven","Christopher Kane","Comme des Garçons Play","Comme des Garçons Wallets","Common Projects","Côte & Ciel","Cottweiler","Craig Green","Diemme","Diesel","Diesel Black Gold","Dior Homme","Dita","Dolce & Gabbana","Dsquared2","Eytys","Feit","Fendi","Giuseppe Zanotti","Givenchy","Golden Goose","Grenson","H by Hudson","Haerfest","Helmut Lang","Homme Plissé Issey Miyake","Issey Miyake Men","J.W.Anderson","Jil Sander","Jimmy Choo","John Undercover","Julius","Juun.J","Kenzo","Lanvin","Le Gramme","Loewe","Maison Margiela","Marc Jacobs","Marcelo Burlon County of Milan","Markus Lupfer","Marni","Marsèll","Marsèll Gomma","Master & Dynamic","McQ Alexander Mcqueen","Moncler","Moncler A","Mostly Heard Rarely Seen","Naked & Famous Denim","Neil Barrett","Noon Goons","Nudie Jeans","OAMC","Off-White","Officine Creative","Palm Angels","Paul Smith","Paul Smith Jeans","Paul Smith London","PB 0110","Pearls Before Swine","Phoebe English","Pierre Balmain","Pierre Hardy","Pigalle","Porter","PS by Paul Smith","Public School","R13","Rag & Bone","Ray-Ban","Reebok Classics","Rick Owens","Robert Clergerie","Sacai","Saint Laurent","Sasquatchfabrix","Super","T by Alexander Wang","Thamanyah","Thom Browne","Tiger of Sweden","Tom Ford","Underground","Valentino","Versace","Versace Underwear","Versus","Visvim","Wales Bonner","Want Les Essentiels de la Vie","Wooyoungmi","Y-3","Yang Li","Season 1")));
        
        foreach($aBrand as $k => $v) {
            $v = strtoupper($v);
            echo 'INSERT INTO `development`.`FeedsMapping`(`feedId`,`mappingType`,`title`,`top`) VALUES (0, "brand", "'.$k.'", 0); <br />';
        }
        
        die;
        echo '<pre>';
        echo count($aBrand) . '<br />';
        ksort($aBrand);
        print_r( $aBrand );
        echo '</pre>';
        
        echo '"' . implode('","', array_keys($aBrand)) . '"';
        die;
        
        echo '<table border="1">';
        foreach($oMapping as $vMap) {
            echo '<tr>';
            echo  '<td>' . $vMap['keywords'] . '<td>';
            $aTemp = array();
            foreach(explode(' ', $vMap['keywords']) as $v) {
                if(preg_match('/[a-z,]/', $v)) break;
                else $aTemp[] = $v;
            }
            echo  '<td>' . implode(' ', $aTemp) . '<td>';
            echo '</tr>';
        }
        echo '</table>';
        die;
    }
    
}
