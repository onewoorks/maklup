<?php

class Billplz_Model extends Common_Model {

    private $billplz = 'billplz';
    private $columns = array();

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->billplz)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->billplz);
    }

    private function CreateTable() {
        
    }

    public function CreateNewData($result, $pemohon_id) {
        $query = "INSERT INTO $this->billplz "
                . "(pemohon_id, billplz_id, billplz_data) VALUE ("
                . "'" . (int) $pemohon_id . "',"
                . "'" . $this->db->escape($result['id']) . "', "
                . "'" . $this->db->escape(json_encode($result)) . "')";
        $this->db->executeQuery($query);
    }
    
    public function GetBillPlzInfo($billplz_id){
        $query = "SELECT b.*, p.* FROM $this->billplz b "
                . "LEFT JOIN pemohon p ON p.id = b.pemohon_id "
                . "WHERE b.billplz_id = '".$this->db->escape($billplz_id)."' ";
        return $this->db->executeQuery($query,'single');
    }
    
    public function UpdatePaymentInfo($payment_data){
        $query = "UPDATE $this->billplz SET "
                . "billplz_data = "
                . "JSON_SET("
                . "billplz_data, "
                . "\"$.paid\", '".$this->db->escape($payment_data['paid_status'])."', "
                . "\"$.paid_at\", '".$this->db->escape($payment_data['paid_at'])."') "
                . "WHERE billplz_id = '".$this->db->escape($payment_data['billplz_id'])."'";
        $this->db->executeQuery($query);
    }
    
    public function ReadTotalCollection(array $date = array(), $status = 'true'){
        if(isset($date['start']) && isset($date['end'])):
            $where = "";
        else:
            $where = "";
        endif;
        $query = "SELECT sum(billplz_data->>'$.amount') as total "
                . "FROM billplz WHERE billplz_data->>'$.paid' = '$status' "
                . "$where ";
        $result = $this->db->executeQuery($query,'single'); 
        return $result['total']/100;
    }

}
