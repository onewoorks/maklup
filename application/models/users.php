<?php

class Users_Model extends Common_Model {

    private $table_users = 'users';
    private $columns = array();

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->table_users)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->table_users);
    }

    private function CreateTable() {
        $query = "CREATE TABLE `users` ( "
                . "`id` int(8) NOT NULL AUTO_INCREMENT, "
                . "`timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP, "
                . "`username` varchar(20) DEFAULT NULL, "
                . "`password` varchar(20) DEFAULT NULL, "
                . "`staff_detail` JSON DEFAULT NULL, "
                . "`access_level` JSON DEFAULT NULL, "
                . "`status` int(11) DEFAULT NULL, "
                . "PRIMARY KEY (`id`) "
                . ") ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8;";
        $this->db->executeQuery($query);
    }
    
    public function CheckUser($data){
        $query = "SELECT * FROM $this->table_users WHERE username = '".$data->username."'";
        return $this->db->executeQuery($query);
    }
    
    public function CheckUserLogin($data){
        $query = "SELECT * FROM $this->table_users "
                . "WHERE username = '".$data->username."' "
                . "AND password='".$data->password."' "
                . "LIMIT 1";
        return $this->db->executeQuery($query,'single');
    }
    
    public function CreateUser($data){
        $query = "INSERT INTO users (username,password,staff_detail,access_level,status) "
                . "VALUE "
                . "('".$this->db->escape($data->username)."', "
                . "'".$this->db->escape($data->password)."', "
                . "'".$this->db->escape(json_encode($data->staff_detail))."', "
                . "'".$this->db->escape(json_encode($data->access_level))."', "
                . "'".$this->db->escape($data->status)."' )";
        $this->db->executeQuery($query);
    }

}
