<?php

require_once './application/libraries/addon/jwt.php';

class Verify_Controller extends Common_Controller {

    private $verify_agent;
    private $pemohon;

    public function __construct() {
        $this->verify_agent = new VerifyAgent_Model();
        $this->pemohon = new Pemohon_Model();
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

    protected function PostNewVerify() {
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        $jwt = JWT::decode($raw->token, TOKEN_SIGNATURE);
        $pemohon = $this->pemohon->GetInfo($jwt->register_id, $jwt->temporary_id);
        $input = array(
            "pemohon_id" => $pemohon['id_pemohon'],
            "verify_status" => $raw->verify,
            "staff_id" => $raw->staff_id,
            "device_id" => $raw->device_info
        );
        $this->verify_agent->CreateNewData($input);
        return $jwt;
    }
    
    protected function GetDataVerify(){
        $data = $this->verify_agent->ReadVerifyData();
        $result = array();
        foreach($data as $r):
            $r['data_pemohon'] = json_decode($r['data_pemohon']);
            $r['tarikh_verify'] = date('d-m-Y H:i:s', strtotime($r['tarikh_verify']));
            $result[] = $r;
        endforeach;
        
        return $result;
    }

}
