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
use Zend\Db\TableGateway\TableGateway;


class AdvertiserTempTable extends BasicTableAdapter 
{
	private $oSql = null;
	
	public function __construct(TableGateway $tableGateway)
	{
		parent::__construct( $tableGateway );
		$this->oSql = new Sql( $this->getDBAdapter() );		
	}	
	
	public function getReady()
	{
	    
	}
	
	public function getProcessedData( $oNode )
	{
		
		$aData = array(
				'programname' 			=> (string)$oNode->programname,
				'programurl' 			=> (string)$oNode->programurl,
				'catalogname' 			=> (string)$oNode->catalogname,
				'lastupdated' 			=> (string)$oNode->lastupdated,
				'name' 					=> (string)$oNode->name,
				'keywords' 				=> (string)$oNode->keywords,
				'description' 			=> (string)$oNode->description,
				'sku' 					=> (string)$oNode->sku,
				'currency' 				=> (string)$oNode->currency,
				'price' 				=> (string)$oNode->price,
				'buyurl' 				=> (string)$oNode->buyurl,
				'impressionurl' 		=> (string)$oNode->impressionurl,
				'imageurl' 				=> (string)$oNode->imageurl,
				'advertisercategory' 	=> (string)$oNode->advertisercategory,
				'instock' 				=> (string)$oNode->instock,
				'condition'				=> (string)$oNode->condition,
				'format'				=> (string)$oNode->format,
				'manufacturerid' 		=> (string)$oNode->manufacturerid,
				'promotionaltext' 		=> (string)$oNode->promotionaltext,
				'retailprice' 			=> (string)$oNode->retailprice,
				'saleprice' 			=> (string)$oNode->saleprice,
				'standardshippingcost' 	=> (string)$oNode->standardshippingcost,
				'thirdpartycategory' 	=> (string)$oNode->thirdpartycategory,
				'title' 				=> (string)$oNode->title,
				'upc' 					=> (string)$oNode->upc,
				'img_program_dir'		=> preg_replace("/[^a-zA-Z0-9]/", "", ((string)$oNode->programname) )/*,
				'img_local_name' 		=> md5( (string)$oNode->imageurl ) . '.' . $aPathInfo['extension']*/
		);		
		
		$insert = $this->oSql->insert( 'temp' )->values($aData);
		$sInsertString = $insert->getSqlString($this->getDBAdapter()->getPlatform());
		$this->oSql->prepareStatementForSqlObject($insert)->execute();
		 
		return false;
	}
	
}