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

    public function CreateNewData($data) {
        $query = "INSERT INTO $this->cdm (cdm_id, seq_id, payment_date, payment_time, paid_amount, status, pemohon_id, cdm_data) "
                . "VALUE ("
                . "'" . $this->db->escape($data['cdm_id']) . "', "
                . "'" . $this->db->escape($data['seq_no']) . "', "
                . "'" . $this->db->escape($data['payment_date']) . "', "
                . "'" . $this->db->escape($data['payment_time']) . "', "
                . "'" . $this->db->escape($data['paid_amount']) . "', "
                . "'" . $this->db->escape($data['status']) . "', "
                . "'" . (int) $data['pemohon_id'] . "', "
                . "'" . $this->db->escape($data['cdm_data']) . "'"
                . ")";
        $this->db->executeQuery($query);
    }

    public function ReadTotalCollection(array $date = array(), $status = 'jelas') {
        if (isset($date['start']) && isset($date['end'])):
            $where = "DATE(payment_date) >= '" . $this->db->escape($date['start']) . "' "
                    . "AND DATE(payment_date) <= '" . $this->db->escape($date['end']) . "' "
                    . "AND ";
        else:
            $where = '';
        endif;

        $query = "SELECT sum(paid_amount) as total "
                . "FROM $this->cdm "
                . "WHERE $where "
                . "status='" . $this->db->escape($status) . "'";
        $result = $this->db->executeQuery($query, 'single');
        return $result['total'];
    }

    public function ReadCdmTransaction(array $transaction_list) {
        $trans = sprintf("'%s'", implode("','", $transaction_list));
        $query = "SELECT c.*, p.data_pemohon, "
                . "CONCAT(REPLACE(c.cdm_id,'/',''), c.seq_id) as trans_ref "
                . "FROM cdm c "
                . "LEFT JOIN pemohon p ON p.id = c.pemohon_id "
                . "WHERE "
                . "CONCAT(REPLACE(c.cdm_id,'/',''), c.seq_id) IN ($trans)";
        $result = $this->db->executeQuery($query);
        return $result;
    }

    public function ReadCdmNumber(array $cdminfo) {
        $query = "SELECT * FROM $this->cdm "
                . "WHERE cdm_id = '" . $this->db->escape($cdminfo['cdm_id']) . "' "
                . "AND seq_id = '" . $this->db->escape($cdminfo['seq_id']) . "'";
        return $this->db->executeQuery($query);
    }
    
    public function ReadAllCdm($status = false){
        $where = (!$status) ? '1' : "status = '$status' ";
        $query = "SELECT c.*, p.*, c.id AS cdmid "
                . "FROM cdm c "
                . "LEFT JOIN pemohon p ON p.id = c.pemohon_id "
                . "WHERE $where ";
        return $this->db->executeQuery($query);
    }

    public function UpdateCdmStatus($input,$status){
	    $in_predicate = array();
	    foreach($input as $i):
			$in_predicate[] = "( id ='".(int) $i['cdm_id']."' AND pemohon_id='". (int) $i['pemohon_id']."')";
		endforeach;    
	$in_query = implode(' OR ', $in_predicate);
    	$query = "UPDATE $this->cdm SET "
		. "status = '". $this->db->escape($status) ."' "
		. "WHERE ($in_query) ";
	    return $this->db->executeQuery($query);
    }

    public function UpdatePemohonPayment($input,$status){
	$in_predicate = array();
	foreach($input as $i):
		$in_predicate[] = "(id='".(int) $i['pemohon_id']."' AND register_id='".$i['register_id']."' AND temporary_id = '".$i['temporary_id']."')";
	endforeach;
	$in_query = implode(' OR ', $in_predicate);
    	$query = "UPDATE pemohon SET "
		. "payment_status = '".$this->db->escape($status)."' "
		. "WHERE ($in_query) ";
	return $this->db->executeQuery($query);
    }

}
