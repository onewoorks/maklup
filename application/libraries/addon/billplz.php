<?php

class BillPlz {

    /**
     * the billplz api server
     * @var string
     */
    private $api;

    /**
     * mode, if false will be using the sandbox api, default is true
     * @var bool
     */
    private $mode;

    /**
     * the token key
     * @var string
     */
    private $token;
    
  

    /**
     * constructor for billplz
     * @param string  $token the token of the logged user
     * @param boolean $mode  if false, it will use sandbox endpoint
     */
    function __construct($token, $mode = true) {
        $this->token = $token;
        $this->mode = $mode;
        $this->api = 'https://www.billplz.com/api/v3/';
//        $this->api = 'https://billplz-staging.herokuapp.com/api/v3/';
        if (!$mode) {
            $this->api = 'https://billplz-staging.herokuapp.com/api/v3/';
        }
    }

    /**
     * create a new collection
     * @param array $data 
     * @return mixed 
     */
    
    public function setCollection(array $data) {
        $curl_data = [
            "title" => $data['title'],
        ];
        $optional_field = [
            "logo",
            "split_payment[email]",
            "split_payment[fixed_cut]",
            "split_payment[variable_cut]"
        ];
        foreach ($optional_field as $field) {
            if (!empty($data[$field])) {
                $curl_data[$field] = $data[$field];
            }
        }

        return $this->run_curl('collections', $curl_data);
    }
    
    public function testJe(){
        $curl_data = [
            'title' => 'test je dulu'
        ];
        return $this->run_curl('collections', $curl_data);
    }

    /**
     * create a new open collection
     * @param array $data 
     * @return  mixed 
     */
    public function setOpenCollection(array $data) {
        $curl_data = [
            "title" => $data['title'],
            "description" => $data['description'],
            "amount" => $data['amount'],
        ];
        $optional_field = [
            "fixed_amount",
            "fixed_quantity",
            "payment_button",
            "reference_1_label",
            "reference_2_label",
            "email_link",
            "tax",
            "photo",
            "split_payment[email]",
            "split_payment[fixed_cut]",
            "split_payment[variable_cut]",
        ];
        foreach ($optional_field as $field) {
            if (!empty($data[$field])) {
                $curl_data[$field] = $data[$field];
            }
        }
        return $this->run_curl('open_collections', $curl_data);
    }

    /**
     * deactivate a collection
     * @param  sting $collection_id collection id
     * @return mixed      
     */
    public function deactivateCollection($collection_id) {
        return $this->run_curl('collections/' . $collection_id . '/deactivate', null, 'POST');
    }

    /**
     * activate a collection
     * @param  int $collection_id collection id
     * @return mixed   
     */
    public function activateCollection($collection_id) {
        return $this->run_curl('collections/' . $collection_id . '/activate', null, 'POST');
    }

    /**
     * create a new bill
     * @param array $data 
     * @return mixed 
     */
    public function setBill(array $data) {
        $curl_data = [
            "collection_id" => $data['collection_id'],
            "description" => $data['description'],
            "name" => $data['name'],
            "email" => $data['email'],
            "mobile" => isset($data['mobile']),
            "amount" => $data['amount'],
            "callback_url" => $data['callback_url'],
        ];
        $optional_field = [
            "due_at",
            "redirect_url" ,
            "deliver",
            "reference_1_label",
            "reference_1",
            "reference_2_label",
            "reference_2",
        ];
        
        foreach ($optional_field as $field) {
            if (!empty($data[$field])) {
                $curl_data[$field] = $data[$field];
            }
        }
        return $this->run_curl('bills', $curl_data);
    }

    /**
     * get a bill based on their id
     * @param  string $bill_id 
     * @return mixed
     */
    public function getBill($bill_id) {
        return $this->run_curl('bills/' . $bill_id);
    }

    /**
     * delete bill based on their id
     * @param  int $bill_id 
     * @return mixed         
     */
    public function deleteBill($bill_id) {
        return $this->run_curl('bills/' . $bill_id, null, 'DELETE');
    }

    /**
     * verify the current user account by using the bank account number
     * @param  int $bank_account 
     * @return mixed              
     */
    public function verifyAccount($bank_account) {
        return $this->run_curl('check/bank_account_number/' . $bank_account);
    }

    /**
     * run the curl command and get the data from the api server
     * @param  string $endpoint       
     * @param  array  $data           
     * @param  int $custom_request 
     * @return mixed             
     */
    private function run_curl($endpoint, $data = [], $custom_request = null) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api . $endpoint);
        //disable ssl for testing purpose
        if (!$this->mode) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        }
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERPWD, $this->token . ":");
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        if (count($data) > 0) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        if (!empty($custom_request)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $custom_request);
        }

        $r = curl_exec($ch);
        curl_close($ch);
        return json_decode($r, true);
    }

}

//$pay = new BillPlz('03093118-9b4a-434f-9004-9cf287d30333');
//$data = array(
//    'collection_id' => 'zjuisadg',
//    'description' => 'payment for invoice no#',
//    'name' => 'sales@onewoorks.com',
//    'email' => 'irwanbibrahim@onewoorks.com',
//    'amount' => '1',
//    'callback_url' => 'https://onewoorks-solutions.com'
//);
//$result = $pay->setBill($data);
//echo json_encode($result);

