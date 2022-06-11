<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PharmacyAdmin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PharmacyAdminModel');
        $this->load->model('PharmacyPartnerModel');
        $this->load->model('LedgerModel');
        $this->load->model('LoginModel');
        
    }
    
    public function pharmacy_orders()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    $user_orders = array_key_exists('user_orders', $params) ? $params['user_orders'] : "" ;
                    $panel_orders = array_key_exists('panel_orders', $params) ? $params['panel_orders'] : "" ;
                    $per_page = array_key_exists('per_page', $params) ? $params['per_page'] : 10 ;
                    $page_no = array_key_exists('page_no', $params) ? $params['page_no'] : 1 ;
                    
                    
                    /*Filter added by swapnali on 29th Jun 2020*/
                    $search = array_key_exists('search', $params) ? $params['search'] : "" ;
                    $from_date = array_key_exists('from_date', $params) ? $params['from_date'] : "" ;
                    $to_date = array_key_exists('to_date', $params) ? $params['to_date'] : "" ;
                    $order_status = array_key_exists('order_status', $params) && gettype($params['order_status']) == 'array' ? $params['order_status'] : array() ;
                    
                    $resp = $this->PharmacyAdminModel->pharmacy_orders($page_no,$per_page,$user_orders,$panel_orders,$search,$from_date, $to_date, $order_status);
                   
                    $res = array("status" => 200, "message" => 'success', "data" => $resp);
                    simple_json_output($res);
                }
            }
        }
    }
}
?>
