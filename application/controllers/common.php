<?php

class Common_Controller {
    
    protected function JWT_verify(){
        print_r(getallheaders()['token']);
    }

    private function ColumnLoop($source, $data) {
        $final = array();
        foreach ($data as $col):
            $final[$col] = $this->JsonCheck($source[$col]) ? json_decode($source[$col]) : $source[$col];
        endforeach;
        return $final;
    }

    private function ClassMethodDefine($name) {
        $toClean = preg_split('/-|_/', strtolower($name));
        $final = '';
        foreach ($toClean as $clean):
            $final .= ucfirst($clean);
        endforeach;
        $finalCall = ($final == '') ? 'Index' : $final;
        $method = ucfirst(strtolower(filter_input(INPUT_SERVER, 'REQUEST_METHOD')));
        return $method . $finalCall;
    }

    protected function TimestampCode() {
        $d = new DateTime();
        return $d->getTimestamp();
    }

    protected function GenerateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    protected function ErrorMethod() {
        $response = array(
            'status' => 'Error',
            'message' => 'Method is not defined'
        );
        return $response;
    }

    public static function MethodNamer(array $params_urls) {
        $result = array();
        $naming = '';
        foreach ($params_urls as $key => $paging):
            if ($key >= URL_ARRAY):
                $clean_name = explode('?', $paging);
                $naming .= ucwords(strtolower($clean_name[0]));
            endif;
        endforeach;
        $params = explode('?', $paging);
        $result['method'] = classNamer($naming);
        $result['params'] = (isset($params[1])) ? self::ParamSplitter($params[1]) : false;
        return $result;
    }

    private static function ParamSplitter($params) {
        $p = explode('&', $params);
        $result = array();
        foreach ($p as $key):
            $d = explode('=', $key);
            $result[$d[0]] = $d[1];
        endforeach;
        return $result;
    }

    protected function ControllerPath($position = 1) {
        $server = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $serverArray = explode('/', $server);
        $classMethod = $serverArray[URL_ARRAY + $position];
        return $this->ClassMethodDefine($classMethod);
    }

    protected function JsonCheck($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    protected function CheckReturnQuery($data, array $column) {
        $final = array();
        if (!isset($data['status'])):
            foreach ($data as $d):
                $final[] = $this->ColumnLoop($d, $column);
            endforeach;
        else:
            $final = $data;
        endif;
        return $final;
    }

    protected function UploadFile($fileData, $transactionCode) {
        $extension = FALSE;
        $filename = FALSE;
        switch ($fileData['type']):
            case 'image/png':
                $extension = '.png';
                break;
            case 'image/jpeg':
            case 'image/jpg':
                $extension = '.jpg';
                break;
            default:
                $extension = '.jpg';
                break;
        endswitch;
        if ($extension):
//            $filename = $transactionCode . '_' . $this->GenerateRandomString() . $extension;
            $filename = $transactionCode . '_' . $this->GenerateRandomString() . '_' . $fileData['name'];
            move_uploaded_file($fileData['tmp_name'], UPLOAD_DIR . $filename);
        endif;
        return $filename;
    }

    protected function classNamer($filter) {
        $names = explode('-', strtolower($filter));
        $clean = array();
        foreach ($names as $name):
            $clean[] = $name;
        endforeach;
        $final = implode('', $clean);
        return $final;
    }

    public function UrlParameter($params) {
        $result = self::ParamSplitter($params);
        return $result;
    }
    
    protected function NumberFormat($number){
        return number_format($number,2,'.',',');
    }
    
    protected function NumberPadding($number, $padding = 7 ){
        $len = strlen($number);
        $pad = '';
        for($i=$len; $i<= $padding; $i++):
            $pad = $pad . '0'; 
        endfor;
        return $pad . $number;
    }
    
    protected function DateFormat($date){
        $d = explode('-', $date);
        return $d[2] . '/' . $d[1] . '/' . $d[0];
    }
    
    protected function RandomNo( $len = 9 ) {
    $rand   = '';
    while( !( isset( $rand[$len-1] ) ) ) {
        $rand   .= mt_rand( );
    }
    return substr( $rand , 0 , $len );
}

}
