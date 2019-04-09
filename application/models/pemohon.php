<?php

class Pemohon_Model extends Common_Model {

    private $pemohon = 'pemohon';
    private $columns = array();

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->pemohon)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->pemohon);
    }

    private function CreateTable() {
        
    }

    public function CreateNewPemohon(array $input) {
        $query = "INSERT INTO $this->pemohon "
                . "(data_pemohon, register_id, temporary_id) VALUE ("
                . "'" . $this->db->escape($input['data_pemohon']) . "',"
                . "'" . $this->db->escape($input['register_id']) . "', "
                . "'" . $this->db->escape($input['temporary_id']) . "')";
        $this->db->executeQuery($query);
    }

    public function GetInfo($register_id, $temporary_id) {
        $query = "SELECT p.*, b.*, p.timestamp AS daftar,"
                . "p.id as id_pemohon "
                . "FROM pemohon p "
                . "LEFT JOIN billplz b ON p.id = b.pemohon_id "
                . "WHERE "
                . "p.register_id= '" . $this->db->escape($register_id) . "' "
                . "AND p.temporary_id='" . $this->db->escape($temporary_id) . "'";
        return $this->db->executeQuery($query,'single');
    }
    
    public function GetInfoById($pemohon_id) {
        $query = "SELECT p.* "
                . "FROM pemohon p "
                . "WHERE "
                . "p.id= '" . (int) $pemohon_id . "' ";
        return $this->db->executeQuery($query,'single');
    }
    
    public function GetUserTemp($email, $temporary_id){
        $query = "SELECT * FROM pemohon "
                . "WHERE data_pemohon->>'$.email' = '".$this->db->escape($email)."' "
                . "AND temporary_id='$temporary_id'";
        return $this->db->executeQuery($query, 'single');
    }
    
    public function UpdatePemohonPayment($data){
        $query = "UPDATE $this->pemohon SET "
                . "payment_status='".$this->db->escape($data['payment_status'])."', "
                . "payment_option='".$this->db->escape($data['payment_option'])."',"
                . "appointment_slot='".$this->db->escape($data['appointment_slot'])."', "
                . "appointment_session='".$this->db->escape($data['appointment_session'])."' "
                . "WHERE id = '".(int) $data['pemohon_id']."'";
        $this->db->executeQuery($query);
    }
    
    public function UpdatePemohon($data){
        $data_pemohon = json_encode($data->body->input);
        $register_id = $data->body->ref->register_id;
        $temporary_id = $data->body->ref->temporary_id;
        $query = "UPDATE pemohon SET "
                . "data_pemohon = '".$this->db->escape($data_pemohon)."' "
                . "WHERE register_id = '". $register_id ."' "
                . "AND temporary_id='".$temporary_id."'";
        return $this->db->executeQuery($query);
    }
    
    public function ReadAllPemohon(){
        $query = "SELECT p.* "
                . "FROM pemohon p";
        return $this->db->executeQuery($query);
    }

}
