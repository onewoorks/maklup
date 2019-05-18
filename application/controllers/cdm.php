<?php

class Cdm_Controller extends Common_Controller {

    private $cdm;

    public function __construct() {
        $this->cdm = new Cdm_Model();
    }

    public function main(array $getVars, array $params) {
        $request = explode('?', $this->ControllerPath());
        if (method_exists($this, $request[0])):
            $this->url_query = isset($request[1]) ? $this->UrlParameter($request[1]) : false;
            $method = $request[0];
            $result = $this->$method();
        else:
            $result = $this->ErrorMethod();
        endif;
        return $result;
    }

    protected function GetAllCdm() {
        $status = isset($this->url_query['status']) ? $this->url_query['status'] : false;
        $data = $this->cdm->ReadAllCdm($status);
        $result = array();
        foreach($data as $d):
            $d['cdm_data'] = json_decode($d['cdm_data']);
            $d['data_pemohon'] = json_decode($d['data_pemohon']);
            $result[] = $d;
        endforeach;
        return $result;
    }
    
    protected function PostApproveList(){
        $data = file_get_contents('php://input');
	$raw = json_decode($data);
	$update_info = array();
        foreach($raw as $r):
		$update_info[] = array(
			"cdm_id" => $r->cdm_data->id,
			"pemohon_id" => $r->cdm_data->pemohon_id
		);
	endforeach;
	$result = $this->cdm->UpdateCdmStatus($update_info,'semakankedua');
        return true;
    }

    protected function PostConfirmApproved(){
    	$data = file_get_contents('php://input');
	$raw = json_decode($data);
	$update_info = array();
	foreach($raw as $r):
		$update_info[] = array(
			"cdm_id" => $r->cdmid,
			"pemohon_id" => $r->pemohon_id,
			"register_id" => $r->register_id,
			"temporary_id" => $r->temporary_id
		);
	endforeach;
	$this->cdm->UpdateCdmStatus($update_info,'jelas');
	$this->cdm->UpdatePemohonPayment($update_info, 'paid');
	return true;

    }

}
