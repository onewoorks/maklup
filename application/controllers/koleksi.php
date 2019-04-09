<?php

class Koleksi_Controller extends Common_Controller {

    protected $cdm;
    protected $billplz;
    protected $pemohon;

    public function __construct() {
        $this->cdm = new Cdm_Model();
        $this->billplz = new Billplz_Model();
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
    

    protected function PostBillplzPaymentInfo() {
        $data = file_get_contents('php://input');
        $raw = json_decode($data)->body;
        $bl = $this->billplz->GetBillPlzInfo($raw->billplz_id);
        if (json_decode($bl['billplz_data'])->paid == FALSE):
            $payinfo = array(
                "billplz_id" => $raw->billplz_id,
                "paid_status" => $raw->paid_status,
                "paid_at" => date('Y-m-d')
            );
            $this->billplz->UpdatePaymentInfo($payinfo);
            $this->UpdatePaymentStatus($bl['pemohon_id'], 'paid', 'billplz');
        endif;
        $pemohon = $this->pemohon->GetInfoById($bl['pemohon_id']);
        $pemohon['data_pemohon'] = json_decode($pemohon['data_pemohon']);
        $pemohon['appointment_slot'] = date('d F Y', strtotime($pemohon['appointment_slot']));
        return $pemohon;
    }

    protected function GetDashboard() {
        $result = array();
        $cdm  = $this->cdm->ReadTotalCollection();
        $billplz = $this->billplz->ReadTotalCollection();
        $total = $cdm + $billplz;
        $result['total_bayar']['cdm'] = $this->NumberFormat($cdm);
        $result['total_bayar']['billplz'] = $this->NumberFormat($billplz);
        $result['total_bayar']['semua'] = $this->NumberFormat($total);
        return $result;
    }

    private function UpdatePaymentStatus($pemohon_id, $payment_status, $payment_option) {
        $pay['pemohon_id'] = $pemohon_id;
        $pay['payment_status'] = $payment_status;
        $pay['payment_option'] = $payment_option;
        $appt_time = $this->AddReserveSlot(3);
        $pay['appointment_slot'] = $appt_time['slot'];
        $pay['appointment_session'] = ($appt_time['session'] == 1) ? 'pagi' : 'petang';
        $this->pemohon->UpdatePemohonPayment($pay);
    }

}