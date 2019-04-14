<?php

class Cdm_Model extends Common_Model {
    
    private $cdm = 'cdm';
    private $columns = array();

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->cdm)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->cdm);
    }

    private function CreateTable() {
        
    }
    
    public function CreateNewData($data){
        $query = "INSERT INTO $this->cdm (cdm_id, seq_id, payment_date, payment_time, paid_amount, status, pemohon_id, cdm_data) "
                . "VALUE ("
                . "'".(int) $data['cdm_id']."', "
                . "'".(int) $data['seq_no']."', "
                . "'".$this->db->escape($data['payment_date'])."', "
                . "'".$this->db->escape($data['payment_time'])."', "
                . "'".$this->db->escape($data['paid_amount'])."', "
                . "'".$this->db->escape($data['status'])."', "
                . "'".(int) $data['pemohon_id']."', "
                . "'". $this->db->escape($data['cdm_data'])."'"
                . ")";
        $this->db->executeQuery($query);
    }
    
    public function ReadTotalCollection(array $date = array(), $status = 'jelas'){
        if(isset($date['start']) && isset($date['end'])):
            $where = "DATE(payment_date) >= '".$this->db->escape($date['start'])."' "
                . "AND DATE(payment_date) <= '".$this->db->escape($date['end'])."' "
                . "AND ";
        else:
            $where = '';
        endif;
        
        $query = "SELECT sum(paid_amount) as total "
                . "FROM $this->cdm "
                . "WHERE $where "
                . "status='".$this->db->escape($status)."'";
        $result = $this->db->executeQuery($query,'single');
        return $result['total'];
    }
    
}
