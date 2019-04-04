<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Webhook_Controller {
    
    public function main(array $getVars, array $params){
        echo shell_exec("git pull https://user:password@repoline.org/user/repo.git ");
//        return 'i will git pull for you '. $dir;
    }
}