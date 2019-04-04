<?php

class Information_Model extends Common_Model {

    private $table_information = 'tbl_information';
    private $columns = array();

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->table_information)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->table_information);
    }

    private function CreateTable() {}
    
    public function ReadInformation(){
        $query = "SELECT * FROM $this->table_information WHERE tenant_id = '". $this->db->tenant_id ."' ";
        return $this->db->executeQuery($query,'single');
    }

}
