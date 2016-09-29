<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;

class VenueStyleTable extends BasicTableAdapter {
    protected $table = 'VenueStyle';
	protected $tableGateway;

    public function __construct(TableGateway $tableGateway){
         $this->tableGateway = $tableGateway;
    }
    public function GetVenue($venueStyle){
        $rowset = $this->tableGateway->select($venueStyle);
        $row = $rowset->current();
        if (!$row) {
         $row = '';
        }
        return $row;
    }
    public function AddVenue($venueStyle){
        if ($this->GetVenue($venueStyle)) {
                $result=0;
             } else {
                $result = $this->tableGateway->insert($venueStyle);
             }
        // $result = $this->tableGateway->insert($VenueStyle);
        return $result;
    }
    public function delete($styleid,$venueid){
        $delete = $this->tableGateway->delete(array('style_id' => (int) $styleid,'venue_id' =>(int) $venueid));
       return $delete;
    }

}
