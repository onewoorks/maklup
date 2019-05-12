<?php

class users_Controller extends Common_Controller {

    private $users;

    public function __construct() {
        $this->users = new Users_Model();
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

    protected function PostCheckUser() {
        $data = file_get_contents('php://input');
        $input = json_decode($data);
        $user = $this->users->CheckUserLogin($input);
        if ($user):
            $user['staff_detail'] = json_decode($user['staff_detail']);
            $user['token'] = $this->ValidUser($user);
            $user['access_level'] = $this->UserModuleAccess($user['access_level'], $input->module);
            if (isset($user['access_level']->error)):
                $user['error'] = $user['access_level']->error;
            endif;
        else:
            $user['error'] = "The username and / or password is incorrect";
        endif;

        return $user;
    }

    protected function PostCreateUser() {
        $data = file_get_contents('php://input');
        $input = json_decode($data);
        $result = array();
        if (count($this->users->CheckUser($input)) == 0):
            $this->users->CreateUser($input);
            $result['status'] = "User created";
        else:
            $result['status'] = "User already existed";
        endif;
        return $result;
    }

    private function UserModuleAccess($data, $module) {
        $raw = json_decode($data);
        $result = new stdClass();
        if (isset($raw->$module) && $raw->$module):
            $result = $raw->$module;
        else:
            $result->error = "user is not authorized for this module";
        endif;
        return $result;
    }

}
