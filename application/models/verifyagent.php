<?php

class VerifyAgent_Model extends Common_Model {

    private $verify_agent = 'verify_agent';
    private $columns = array();

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->verify_agent)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->verify_agent);
    }

    private function CreateTable() {
        
    }
    
    public function CreateNewData($data){
        $query = "INSERT INTO $this->verify_agent "
                . "(pemohon_id, verify_status, staff_id,device_id) "
                . "VALUE ("
                . "'". (int) $data['pemohon_id']."', "
                . "'". $this->db->escape($data['verify_status'])."', "
                . "'".(int) $data['staff_id']."', "
                . "'".$this->db->escape($data['device_id'])."')";
        echo $query;
        $this->db->executeQuery($query);
    }
    
    public function ReadVerifyData(){
        $query = "SELECT v.timestamp as tarikh_verify, "
                . "p.data_pemohon as data_pemohon,"
                . "v.verify_status as status "
                . "FROM $this->verify_agent v "
                . "LEFT JOIN pemohon p ON p.id = v.pemohon_id ";
        return $this->db->executeQuery($query);
    }

}
