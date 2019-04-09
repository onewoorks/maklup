<?php

require_once './application/libraries/addon/onewaysms.php';

class Register_Controller extends Common_Controller {

    private $pemohon;
    private $sms_message_register = "Maklumat akaun anda telah didaftarkan, sila gunakan id %s ini sekiranya ingin kemaskini akaun semula.";

    public function __construct() {
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

    protected function GetInfo() {
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
            'billplz_data' => json_decode($result['billplz_data']),
            'payment' => array(
                'status' => $result['payment_status'],
                'option' => $result['payment_option']
            ),
            'appointment' => array(
                'slot' => date('d F Y', strtotime($result['appointment_slot'])),
                'session' => $result['appointment_session'])
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
        $sms = Onewaysms::SendSMS('60196693481', sprintf($this->sms_message_register, $temporary_id));
        return array('register_id' => $register_id, 'temporary_id' => $temporary_id, 'sms_info' => '');
    }

    protected function PostUpdateInfo() {
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        $pemohon = new Pemohon_Model();
        $pemohon->UpdatePemohon($raw);
        $latest_info = $pemohon->GetInfo($raw->body->ref->register_id, $raw->body->ref->temporary_id);
        return array(
            'register_id' => $raw->body->ref->register_id,
            'temporary_id' => $raw->body->ref->temporary_id,
            'payment' => array(
                'status' => $latest_info['payment_status'],
                'option' => $latest_info['payment_option']
        ));
    }

    protected function OptionsNew() {
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        $register = new Pemohon_Model();
        $register_id = $this->RandomNo();
        $temporary_id = $this->RandomNo();
        $input['data_pemohon'] = json_encode($raw->body);
        $input['register_id'] = $register_id;
        $input['temporary_id'] = $temporary_id;
        $register->CreateNewPemohon($input);

        return array('register_id' => $register_id, 'temporary_id' => $temporary_id);
    }

    protected function GetBillPlzInfo() {
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

    protected function PostBillPlz() {
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

    protected function PostCompleteProcess() {
        $data = file_get_contents('php://input');
        $raw = json_decode($data);
        $token_array = array(
            'register_id' => $raw->body->register_id,
            'temporary_id' => $raw->body->temporary_id,
            'status' => $raw->body->status,
        );
        $token = JWT::encode($token_array, TOKEN_SIGNATURE);
        return array(
            'token' => $token
        );
    }

    protected function PostVerifyQr() {
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

    protected function GetReopen() {
        $p = new Pemohon_Model();
        $params = $this->url_query;
        $data = $this->PemohonOutput($p->GetUserTemp($params['email'], $params['code']));
        return $data;
    }

    private function PemohonOutput($data) {
        if ($data):
            $data['data_pemohon'] = json_decode($data['data_pemohon']);
        endif;
        return (!$data) ? false : $data;
    }

    protected function GetSemuaPemohon() {
        $pemohon = new Pemohon_Model();
        $result = $pemohon->ReadAllPemohon();
        $filter = array();
        foreach ($result as $r):
            $r['data_pemohon'] = json_decode($r['data_pemohon']);
            $r['appointment_slot'] = ($r['payment_status'] == null) ? '' : date('j F Y', strtotime($r['appointment_slot']));
            $r['timestamp'] = date('d-m-Y H:i:s', strtotime($r['timestamp']));
            $r['payment_status'] = ($r['payment_status'] == null) ? "not paid" : $r['payment_status'];
            $filter[] = $r;
        endforeach;
        return $filter;
    }

    protected function PostUpdateCdm() {
        $data = file_get_contents('php://input');
        $raw = json_decode($data)->body;
        $pemohon = new Pemohon_Model();
        $pemohon_id = $pemohon->GetInfo($raw->register_id, $raw->temporary_id);
        $cdm = new Cdm_Model();
        $input['pemohon_id'] = $pemohon_id['id_pemohon'];
        $input['cdm_id'] = $raw->cdm_id;
        $input['seq_no'] = $raw->no_seq;
        $input['payment_date'] = date('Y-m-d', strtotime($raw->tarikh_bayaran));
        $input['payment_time'] = date('H:i:s', strtotime($raw->tarikh_bayaran));
        $input['paid_amount'] = 800.00;
        $input['status'] = 'jelas';
        $cdm->CreateNewData($input);
        $this->UpdatePaymentStatus($input['pemohon_id'], 'paid', 'cdm');
    }

    protected function PostValidate() {
        $content = file_get_contents('php://input');
        return $content;
    }

    private function UpdatePaymentStatus($pemohon_id, $payment_status, $payment_option) {
        $pay['pemohon_id'] = $pemohon_id;
        $pay['payment_status'] = $payment_status;
        $pay['payment_option'] = $payment_option;
        $appt_time = $this->AddReserveSlotA(3);
        $pay['appointment_slot'] = $appt_time['slot'];
        $pay['appointment_session'] = ($appt_time['session'] == 1) ? 'pagi' : 'petang';
        $this->pemohon->UpdatePemohonPayment($pay);
    }

    private function AddReserveSlotA($days) {
        $today = date('Y-m-d');
        $appointment_date = date('Y-m-d', strtotime($today . " + $days days"));
        return array('slot' => $appointment_date, 'session' => rand(1, 2));
    }

}
