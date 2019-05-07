<?php

class Preflight_Model extends Common_Model {

    private $preflight = 'preflight';
    private $columns = array(
        array(
            'column' => 'pemohon_id',
            'type' => 'int',
            'length' => '11'
        )
    );

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->preflight)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->preflight);
    }

    private function CreateTable() {
        
    }

    public function CreateNewData($data) {
        $query = "INSERT INTO $this->preflight "
                . "(block_no, crew) "
                . "VALUE ("
                . "'". $this->db->escape($data->block_no)."', "
                . "'". $this->db->escape(json_encode($data->crew))."')";
        $this->db->executeQuery($query);
    }
    
    public function ReadBlock($id = false){
        if(!$id):
            $where = '1';
        else:
            $where = "block_no = '".$this->db->escape($id)."'";
        endif;
        
        $query = "SELECT * FROM $this->preflight "
                . "WHERE $where ";
        return $this->db->executeQuery($query);
    }
    
    public function ReadBlockSummary($id = false){
        if(!$id):
            $where = '1';
        else:
            $where = "block_no = '".$this->db->escape($id)."'";
        endif;
        
        $query = "SELECT *, count(id) as total FROM $this->preflight "
                . "WHERE $where ";
        return $this->db->executeQuery($query);
    }
    
    public function ReadCurrentBlock($block_no){
        $query = "SELECT * FROM $this->preflight "
                . "WHERE block_no = '" . $this->db->escape($block_no)."'";
        return $this->db->executeQuery($query);
    }

}
