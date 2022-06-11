<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class exotel extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
      public function customer_call() {
        $this->load->model('exotelModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->exotelModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $to_number = $params['call_to'];
                    $from_number = $params['call_from'];
                $exotel_sid = "aegishealthsolutions";
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
                $post_data = array(
                    
                    // 'From' => "<First-phone-number-to-call (Your agent's number)>",
                    // 'To' => "<Second-phone-number-to-call (Your customer's number)>",
                    // 'CallerId' => "<Your-Exotel-virtual-number>",
                    // 'TimeLimit' => "<time-in-seconds> (optional)",
                    // 'TimeOut' => "<time-in-seconds (optional)>",
                    // 'CallType' => "promo" //Can be "trans" for transactional and "promo" for promotional content
                    // 'From' => "9833381096",
                    // 'To' => "9820782743",
                     'From' => $from_number,
                    'To' => $to_number,
                    'CallerId' => "02233721563",
                    // 'TimeLimit' => " ",
                    // 'TimeOut' => "",
                    'CallType' => "trans" //Can be "trans" for transactional and "promo" for promotional content
                );
                
                print_r($post_data); die();
                 
                $exotel_sid = "aegishealthsolutions"; // Your Exotel SID - Get it from here: http://my.exotel.in/settings/site#api-settings
                $exotel_token = "a642d2084294a21f0eed3498414496229958edc5"; // Your exotel token - Get it from here: http://my.exotel.in/settings/site#api-settings
                 
                $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Calls/connect";
                 
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, 1);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_FAILONERROR, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                 
                $http_result = curl_exec($ch);
                $error = curl_error($ch);
                $http_code = curl_getinfo($ch ,CURLINFO_HTTP_CODE);
                 
                curl_close($ch);
                 
                print "Response = ".print_r($http_result); 
           
                }
            }
        }
     
    }
    
}
?>