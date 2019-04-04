<?php

class Jualan_Model extends Common_Model {

    private $table_jualan = 'tbl_purchase';
    private $columns = array();

    public function __construct() {

        parent::__construct();
        if (!$this->CheckTable($this->table_jualan)):
            $this->CreateTable();
        endif;
        $this->CheckColumn($this->columns, $this->table_jualan);
    }

    private function CreateTable() {
        $query = "CREATE TABLE `tbl_purchase` ( "
                . "`pch_id` int(8) NOT NULL AUTO_INCREMENT, "
                . "`pch_tag` int(11) DEFAULT NULL, "
                . "`cat_id` int(11) DEFAULT NULL, "
                . "`pch_name` varchar(250) DEFAULT NULL, "
                . "`pch_date` timestamp NULL DEFAULT '0000-00-00 00:00:00', "
                . "`pch_price` double(11,2) DEFAULT NULL, "
                . "`pch_modal` double(11,2) DEFAULT NULL, "
                . "`pch_weight` double(11,2) DEFAULT NULL, "
                . "`pch_resit` int(4) DEFAULT NULL, "
                . "`cust_id` int(8) DEFAULT NULL, "
                . "`stf_id` int(8) DEFAULT NULL, "
                . "`stk_id` int(8) DEFAULT NULL, "
                . "`pch_hes` double(11,2) DEFAULT '0.00', "
                . "`komisen` double(11,2) DEFAULT NULL, "
                . "`stk_color` int(11) DEFAULT NULL, "
                . "`dlg_id` int(11) DEFAULT NULL, "
                . "`spl_id` int(11) DEFAULT NULL, "
                . "`stk_upah` float(11,2) DEFAULT NULL, "
                . "`stk_emas` float(11,2) DEFAULT NULL, "
                . "`stk_permata` float(11,2) DEFAULT NULL, "
                . "`stk_upahdisplay` float(11,2) DEFAULT NULL, "
                . "`tenant_id` int(11) DEFAULT NULL, "
                . "`tax_gst` decimal(14,2) NOT NULL, "
                . "PRIMARY KEY (`pch_id`), "
                . "KEY `FK_tbl_purchase` (`tenant_id`), "
                . "CONSTRAINT `FK_tbl_purchase` FOREIGN KEY (`tenant_id`) REFERENCES `tbl_information` (`if_id`) ON UPDATE CASCADE "
                . ") ENGINE=InnoDB AUTO_INCREMENT=19009 DEFAULT CHARSET=latin1;";
        $this->db->executeQuery($query);
    }
    
    public function ReadJualanGST(){
        $query = "SELECT j.*, g.tax_code, g.tax_group FROM tbl_gstjualan j "
                . "LEFT JOIN tbl_gstcode g ON j.tax_code = g.id "
                . "ORDER BY j.id DESC LIMIT 100 ";
        return $this->db->executeQuery($query);
    }
    
    public function ReadJualan(){
        $query = "SELECT * FROM $this->table_jualan ORDER BY pch_id DESC LIMIT 100 ";
        return $this->db->executeQuery($query);
    }

}
