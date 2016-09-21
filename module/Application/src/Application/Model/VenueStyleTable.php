<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class VenueStyleTable extends BasicTableAdapter {
	protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }
    public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->styleid = (!empty($data['styleid'])) ? $data['styleid'] : null;
         $this->venueid  = (!empty($data['venueid'])) ? $data['venueid'] : null;
     }

   public function savevenuestyle(VenueStyle $Postparam)
     {
         $data = array(
             'style_id' => $Postparam->styleid,
             'venue_id'  => $Postparam->venueid,
         );

         // $id = (int) $Postparam->id;
        $this->tableGateway->insert($data);
     }

	// public function insert(){
	// 	// $data = array(
 //  //           'id' => '',
 //  //           'style_id' => $styleid,
 //  //           'venue_id' => $venueid,
 //  //       );
 //        $result = $this->tableGateway->insert($data);
 //        if($result){
 //            $result = $this->tableGateway->lastInsertValue;
 //        }
 //        return $result;
	// }
	
	
}
