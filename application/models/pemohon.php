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

}
