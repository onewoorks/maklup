<?php

class Upload_Controller extends Common_Controller {

    public function __construct() {
        
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
    
    protected function PostIndex(){
        $input = filter_input_array(INPUT_POST);
        $files = filter_var_array($_FILES);
        print_r($files);
        $values = array(
            'input' => $input,
            'files' => $files
        );
        return $values;
    }

}
