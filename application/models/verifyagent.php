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
        $query = "CREATE TABLE `verify_agent` (`id` int(11) unsigned "
                . "NOT NULL AUTO_INCREMENT, "
                . "`timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP, "
                . "`pemohon_id` int(11) NOT NULL, "
                . "`verify_status` varchar(20) NOT NULL DEFAULT '', "
                . "`staff_id` int(11) NOT NULL, "
                . "`device_id` varchar(100) DEFAULT NULL, "
                . "PRIMARY KEY (`id`)) "
                . "ENGINE=InnoDB AUTO_INCREMENT=2 "
                . "DEFAULT CHARSET=utf8";
        $this->db->executeQuery($query);
    }

    public function CreateNewData($data) {
        $query = "INSERT INTO $this->verify_agent "
                . "(pemohon_id, verify_status, staff_id,device_id) "
                . "VALUE ("
                . "'" . (int) $data['pemohon_id'] . "', "
                . "'" . $this->db->escape($data['verify_status']) . "', "
                . "'" . (int) $data['staff_id'] . "', "
                . "'" . $this->db->escape($data['device_id']) . "')";
        echo $query;
        $this->db->executeQuery($query);
    }

    public function ReadVerifyData() {
        $query = "SELECT v.timestamp as tarikh_verify, "
                . "p.data_pemohon as data_pemohon,"
                . "v.verify_status as status "
                . "FROM $this->verify_agent v "
                . "LEFT JOIN pemohon p ON p.id = v.pemohon_id ";
        return $this->db->executeQuery($query);
    }

}
