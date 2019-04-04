<?php

class Common_Model {

    public function __construct() {
        $this->db = new Mysql_Driver();
    }
   
    protected function CheckTable($table_name) {
        $query = "SHOW TABLES LIKE '$table_name'";
        $result = $this->db->executeQuery($query);
        return count($result) > 0 ? true : false;
    }

    protected function CheckColumn(array $columns, $table_name) {
        foreach ($columns as $column):
            $query = "SELECT `" . $column['column'] . "` FROM `$table_name`";
            $result = $this->db->executeQuery($query);
            if (isset($result['status']) && strtolower($result['status']) == 'error'):
                $alter = "ALTER TABLE `$table_name` ADD " . $column['column'] . " " . $column['type'] . "(".$column['length'].") DEFAULT NULL";
                $this->db->executeQuery($alter);
            endif;
        endforeach;
    }
    
    protected function PagingRange($pageNo){
        $list = 50;
        $start = ($pageNo == 1 ) ? 0 : ($pageNo - 1) * 50;
        $end  = $list;
        return "LIMIT $start, $end";
    }

}
