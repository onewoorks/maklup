<?php

class Task_Controller extends Common_Controller {
    private $uploadMobile;
    private $uploadVacantPremise;
    private $uploadNonCommercial;
    private $url_query;

    public function __construct() {
        $this->uploadMobile = new MobileUpload_Model();
        $this->uploadVacantPremise = new UploadVacantPremise_Model();
        $this->uploadNonCommercial = new UploadNonCommercial_Model();
    }

    public function main(array $getVars, array $params) {
        $request = explode('?', $this->ControllerPath());
        
        if (method_exists($this, $request[0])):
            $this->url_query = isset($request[1]) ? $this->UrlParameter($request[1])  : false;
            $method = $request[0];
	    $result = $this->$method();
//            $method_name = $this->MethodNamer($params);
//            $method_call = $this->classNamer($method_name['method']) . 'Page';
//            $this->url_query = $method_name['params'];
//            $result = $this->$method_call();
        else:
            $result = $this->ErrorMethod();
        endif;
        return $result;
    }

    protected function GetNonDomestic() {
        $result = array();
        return $result;
    }

    protected function PostNonDomestic() {
        $files = filter_var_array($_FILES);
        $transaction_code = $this->TimestampCode();
        $images = array();
        foreach ($files as $k => $file):
            $images[$k] = $this->UploadFile($file, $transaction_code);
        endforeach;
        $compiled = array(
            'transaction_code' => $this->TimestampCode(),
            'content' => filter_input_array(INPUT_POST),
            'images' => $images
        );
        return $compiled;
    }

    protected function PostTariffConfirmation() {
        $tcm = new EntryTariffConfirmation_Model();
        $files = filter_var_array($_FILES);
        $transaction_code = $this->TimestampCode();
        $images = array();
        foreach ($files as $k => $file):
            $images[$k] = $this->UploadFile($file, $transaction_code);
        endforeach;
        $compiled = array(
            'transaction_code' => $this->TimestampCode(),
            'content' => filter_input_array(INPUT_POST),
            'images' => $images
        );
        return $tcm->CreateNewEntry($compiled);
    }

    private function ReadNonDomesticForm() {
        $data = $this->uploadMobile->ReadUploadData();
        $column = array('mobile_id', 'upload_data');
        return $this->CheckReturnQuery($data, $column);
    }

    protected function GetMyTask() {
        $p = $this->url_query;
        $data['vacant_premises'] = $this->uploadVacantPremise->ReadTaskAssignToStaff($p['id']);
	$data['non_commercial'] = $this->uploadNonCommercial->ReadMyTask($p['id']);
	$data['domestic'] = array();
        $data['tariff_confirmation'] = array();
        $total = 0;
        foreach($data as $d):
            $total = $total + count($d);
        endforeach;
	$data['total_task'] = $total;;
        return $data;
    }
    
    protected function PostVacantPremise(){
        $vpm = new EntryVacantPremise_Model();
        $input = filter_input_array(INPUT_POST);
        $files = filter_var_array($_FILES);
        $transaction_code = $this->TimestampCode();
        $images = array();
        foreach ($files as $k=>$file):
            $images[$k] = $this->UploadFile($file, $transaction_code);
        endforeach;
        $data = array();

        $data['task_detail'] = json_decode($input['task_detail']);
        $data['task_perform'] = json_decode($input['task_perform']);
        $data['remarks'] = $input['remarks'];
	$data['photos'] = $images;
	$data['perform_staff'] = $input['perform_staff'];
        $vpm->CreateEntry($data);
        $status_data = array(
            'seq_id'=> $data['task_detail']->seq_id, 
            'sewacc' => $data['task_detail']->sewacc,
            'la_name'=> $data['task_detail']->la_name);
        $vpm->UpdateUploadVacantPremise($status_data, 'COMPLETED');
        $this->uploadMobile->CreateNewData($data);
        return $data;
    }
    
    protected function PostNonCommercial(){
        $uncm = new EntryNonCommercial_Model();
        $input = filter_input_array(INPUT_POST);
        $files = filter_var_array($_FILES);
        $transaction_code = $this->TimestampCode();
        $data = array();
        $data['task_detail'] = json_decode($input['task_detail']);
        $data['task_perform'] = json_decode($input['task_perform']);
	$data['perform_staff'] = $input['perform_staff'];
        $images = array();
        foreach ($files as $k=>$file):
            $images[$k] = $this->UploadFile($file, $transaction_code);
        endforeach;
        $data['photos'] = $images;
        $uncm->CreateEntry($data);
        $status_data = array('seq_id' => $data['task_detail']->id,'tabs_name' => $data['task_detail']->sheet_code);
        $uncm->UpdateUploadNonCommercial($status_data, 'COMPLETED');
        $this->uploadMobile->CreateNewData($data);
        return $data;
    }
    
    protected function GetSyncVacantPremise(){
        
    }

}
