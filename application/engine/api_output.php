<?php

class ApiOutput {

    private $data = array();
    private $render = FALSE;    

    public function __construct($template) {
        $file = SERVER_ROOT . 'application/views/' . strtolower($template) . '.php';
        if (file_exists($file)) {
            $this->render = $file;
        }
    }

    public function assign($variable, $value) {
        $this->data[$variable] = $value;
    }
    
     public function __destruct() {
        $data = $this->data;
        $k = array();
            if (isset($data['content'])):
                foreach ($data['content'] as $k => $v) {
                    $$k = $v;
                }
            endif;
        include_once $this->render;
        return $k;
    }
    
    function showResponse(){
        $data = $this->data;
        include_once $this->render;
    }
    
}
