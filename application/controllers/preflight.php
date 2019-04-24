<?php

class Preflight_Controller extends Common_Controller {
    private $preflight;

    public function __construct() {
        $this->preflight = new Preflight_Model();
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
    
    protected function PostNewCrew(){
        $data = file_get_contents('php://input');
        $raw = json_decode($data)->body;
        $this->preflight->CreateNewData($raw);
        return $raw;
    }
    
    protected function GetBlock(){
        $params = $this->url_query;
        if(isset($params)):
            $id = $params['no'];
        else :
            $id = false;
        endif;
        $data = $this->preflight->ReadBlock($id);
        $result = array();
        foreach($data as $d):
            $d['crew'] = json_decode($d['crew']);
            $result[] = $d;
        endforeach;
        return $result;
    }
    
    protected function GetBlockSummary(){
        $params = $this->url_query;
        if(isset($params)):
            $id = $params['no'];
        else :
            $id = false;
        endif;
        $data = $this->preflight->ReadBlockSummary($id);
        $result = array();
        foreach($data as $d):
            $d['crew'] = json_decode($d['crew']);
            $result[] = $d;
        endforeach;
        return $result;
    }

}
