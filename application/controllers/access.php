<?php

class Access_Controller extends Common_Controller{
    
    public function main(){
        $data = filter_input_array(INPUT_SERVER);
        $msg = explode('/',$data['REQUEST_URI']);
        $a = array_reverse($msg);
        $access = JWT::decode($a[0],TOKEN_SIGNATURE);
        $sendback = array(
            'token' => $a[0],
            'path'  => $access->path
        );
        return $sendback;
    }
}
