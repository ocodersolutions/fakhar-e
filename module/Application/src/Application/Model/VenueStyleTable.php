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
        $oVenueList = $this->getServiceLocator()->get('VenueTable');
        $getChild = $oVenueList->getchildrentId($venueStyle['venue_id']);

         if ($this->GetVenue($venueStyle)) {
            $result=0;
         } else {
            $result = $this->tableGateway->insert($venueStyle);
         }  


        if (!empty($getChild)) {

            //loop 1
            foreach ($getChild as $key) {
                 $venueStyle['venue_id'] = $key['id'];
                 $getChild_lv2 = $oVenueList->getchildrentId($key['id']);

                 if ($this->GetVenue($venueStyle)) {
                        $result = 0;
                     } else {
                        $result = $this->tableGateway->insert($venueStyle);
                }

                 if (!empty($getChild_lv2)) {
                        foreach ($getChild_lv2 as $key2) {
                            $venueStyle['venue_id'] = $key2['id'];
                            // check exist
                             if ($this->GetVenue($venueStyle)) {
                                $result = 0;
                             } else {
                                $result = $this->tableGateway->insert($venueStyle);
                             }
                        }
                 }

            }
            
        }
                // die;

                


             
        return $result;
    }
    public function delete($styleid,$venueid){
        $delete = $this->tableGateway->delete(array('style_id' => (int) $styleid,'venue_id' =>(int) $venueid));
       return $delete;
    }
    public function deleterbystyleid($styleid){
        $delete = $this->tableGateway->delete(array('style_id' => (int) $styleid));
       return $delete;
    }
}
