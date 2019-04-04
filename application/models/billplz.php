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

}
