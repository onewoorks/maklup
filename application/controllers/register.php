<?php

class Register_Controller extends Common_Controller {

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
    
    protected function GetInfo(){
        $pemohon = new Pemohon_Model();
        $param = $this->url_query;
        $result = $pemohon->GetInfo($param['regid'], $param['tempid']);
        $data = array(
            'id' => $result['id_pemohon'],
            'timestamp' => $result['daftar'],
            'data_pemohon' => json_decode($result['data_pemohon']),
            'register_id' => $result['register_id'],
            'temporary_id' => $result['temporary_id'],
            'billplz_id' => $result['billplz_id'],
            'billplz_data' => json_decode($result['billplz_data'])
        );
        return $data;
    }

    protected function PostNew() {
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        $register = new Pemohon_Model();
        $register_id = $this->RandomNo();
        $temporary_id = $this->RandomNo();
        $input['data_pemohon'] = json_encode($raw->body);
        $input['register_id'] = $register_id;
        $input['temporary_id'] = $temporary_id;
        $register->CreateNewPemohon($input);
        
        return array('register_id'=> $register_id, 'temporary_id'=> $temporary_id);
    }
    
    protected function GetBillPlzInfo(){
        $blpz = new Billplz_Model();
        $result = $blpz->GetBillPlzInfo($this->url_query['billplzId']);
        $data = array(
            'billplz_data' => json_decode($result['billplz_data']),
            'data_pemohon' => json_decode($result['data_pemohon']),
            'register_id' => $result['register_id'],
            'temporary_id' => $result['temporary_id'],
            'timestamp' => $result['timestamp']
        );
        return $data;
    }
    
    protected function PostBillPlz(){
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        
        $pay = new BillPlz('44ff1f75-be5f-4b73-8b48-16687ed41cef', false);
        $billplz_data = array(
            'collection_id' => 'g8hlwyda',
            'description' => 'Payment for pulkam invoice no #2019-' . $raw->body->temporary_id,
            'name' => $raw->body->nama,
            'email' => $raw->body->email,
            'amount' => (800 * 100) + 150,
            'redirect_url' => 'http://localhost:8080/callback/',
            'callback_url' => 'http://localhost/pulkam-api/register/webhook-cb/'
        );
        $result = $pay->setBill($billplz_data);
        $bplz = new Billplz_Model();
        $bplz->CreateNewData($result, $raw->body->pemohon_id);
        return $result;
    }

    protected function PostWebhook() {
        $data = file_get_contents('php://input');
        $d = explode('&', $data);
        $post = array();
        foreach ($d as $a):
            $v = explode('=', $a);
            $post[urldecode($v[0])] = urldecode($v[1]);
        endforeach;
        return $post;
    }
    
    protected function PostCompleteProcess(){
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        $token_array = array(
            'register_id'=> $raw->body->register_id,
            'temporary_id' => $raw->body->temporary_id,
            'status' => $raw->body->status,
        );
        $token = JWT::encode($token_array, TOKEN_SIGNATURE);
        return array(
            'token' => $token
        );
    }
    
    protected function PostVerifyQr(){
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        $parsed = JWT::decode($raw->body->token, TOKEN_SIGNATURE);
        $pemohon = new Pemohon_Model();
        $result = $pemohon->GetInfo($parsed->register_id, $parsed->temporary_id);
        $info = array(
            'data_pemohon' => json_decode($result['data_pemohon']),
            'status' => 'paid'
        );
        return $info;
    }

}
