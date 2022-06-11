<?php

// created by swapnali

defined('BASEPATH') OR exit('No direct script access allowed');

class Ledger extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('LedgerModel');
    }
 
    public function index() { 
        json_output(400, array(
            'message' => 'Bad request.'
        ));
    }
    
    public function pharmacy_ledger() { 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if(array_key_exists('user_id',$params )){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                if ($user_id == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id'
                    );
                } else {
                   
                    $res = $this->LedgerModel->pharmacy_ledger($user_id);
                    
                    $resp = array(
                        'status' => 200,
                        'message' => 'success',
                        'data'  => $res
                    );
                }
                simple_json_output($resp);
            }
        }
    }
    
    // pharmacy_ledger_test
    
    public function get_ledger() { 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if(array_key_exists('user_id',$params )){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                
                if(array_key_exists('listing_id',$params )){
                     $listing_id = $params['listing_id'];
                } else {
                     $listing_id = "";
                }
                
                if ($user_id == "" || $listing_id == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id and listing_id'
                    );
                } else {
                   
                    $res = $this->LedgerModel->get_ledger($user_id,$listing_id);
                    if($res['status'] == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data'  => $res['data']
                        );    
                    } else {
                        $resp = array(
                            'status' => 400,
                            'message' => 'No data found'
                        );
                    }
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
    // create ledger
    
     public function create_ledger() { 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                // $user_id = ""; Req
                
                if(array_key_exists('user_id',$params )  ){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                
                if(array_key_exists('user_ledger',$params ) && ($params['user_ledger'] == 0 || $params['user_ledger'] == 1) ){
                     $user_ledger = $params['user_ledger'];
                } else {
                     $user_ledger = "";
                }
                
                // $invoice_no = "";
                
                if(array_key_exists('invoice_no',$params )){
                     $invoice_no = $params['invoice_no'];
                } else {
                     $invoice_no = "";
                }
                
                
                // $ledger_owner_type = "";
                if(array_key_exists('ledger_owner_type',$params )){
                     $ledger_owner_type = $params['ledger_owner_type'];
                } else {
                     $ledger_owner_type = "";
                }
                
                
                // $listing_id = "";
                if(array_key_exists('listing_id',$params )){
                     $listing_id = $params['listing_id'];
                } else {
                     $listing_id = "";
                }
                
                
                // $listing_id_type = "";
                if(array_key_exists('listing_id_type',$params )){
                     $listing_id_type = $params['listing_id_type'];
                } else {
                     $listing_id_type = 0;
                }
                
                
                // $credit = "";
                if(array_key_exists('credit',$params )){
                     $credit = $params['credit'];
                } else {
                     $credit = "";
                }
                
                
                // $debit = "";
                if(array_key_exists('debit',$params )){
                     $debit = $params['debit'];
                } else {
                     $debit = "";
                }
                
               
                
                
                // $payment_method = "";
                if(array_key_exists('payment_method',$params )){
                     $payment_method = $params['payment_method'];
                } else {
                     $payment_method = "";
                }
                
                
                // $user_comments = "";
                if(array_key_exists('user_comments',$params )){
                     $user_comments = $params['user_comments'];
                } else {
                     $user_comments = "";
                }
                
                
                // $mw_comments = "";
                if(array_key_exists('mw_comments',$params )){
                     $mw_comments = $params['mw_comments'];
                } else {
                     $mw_comments = "";
                }
                
                
                // $vendor_comments = "";
                if(array_key_exists('vendor_comments',$params )){
                     $vendor_comments = $params['vendor_comments'];
                } else {
                     $vendor_comments = "";
                }
                
                
                // $trans_status = "";
                if(array_key_exists('trans_status',$params )){
                     $trans_status = $params['trans_status'];
                } else {
                     $trans_status = "";
                }
                
                // order_type

                if(array_key_exists('order_type',$params )){
                     $order_type = $params['order_type'];
                } else {
                     $order_type = "";
                }
                
                
                // created_by
                if(array_key_exists('created_by',$params )){
                     $created_by = $params['created_by'];
                } else {
                     $created_by = "";
                }
                
                // modified_by
                if(array_key_exists('modified_by',$params )){
                     $modified_by = $params['modified_by'];
                } else {
                     $modified_by = "";
                }
                
                if ($user_ledger == "" || $user_id == "" || $invoice_no == "" ||  $listing_id  == "" || $listing_id_type  == "" || ($credit  == "" && $debit  == "" ) || $payment_method  == "" || $trans_status  == "" || $trans_status  > 3 || $trans_status  < -1 || $order_type == "" || $order_type > 5 || $order_type < 1 || $created_by == "" || $modified_by == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_ledger : it should be 1 or 0 if 1 means user ledger and if 0 means vendor ledger, user_id, invoice_no : it should be invoice_no from user_order table or booking_id from booking_master / doctor_booking_master / hospital_booking_master / pestcontrol_booking_master , ledger_owner_type i.e. user_id type, listing_id, listing_id_type,  either credit or debit amount ,  payment_method, trans_status should be 0: filed; 1: success ; 2 : pending , order_type should be 1 : service ; 2 : appointment ; 3 : doctor appointment; 4 : hospital appointment; 5 : pestcontrol  appointment, created_by user id who is creating this record and modified_by user id who is creating / updating this record'
                    );
                    
                } else {
                   
                    $res = $this->LedgerModel->create_ledger($user_ledger, $user_id, $invoice_no, $ledger_owner_type, $listing_id, $listing_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments, $trans_status,$order_type,$created_by,$modified_by);
                    
                    if($res['status'] == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            // 'data'  => $res['data']
                        );
                    } else if($res['status'] == 2){
                        $resp = array(
                            'status' => 201,
                            'message' => 'No order found'
                        );
                    }  else if($res['status'] == 3){
                        $resp = array(
                            'status' => 201,
                            'message' => 'Wrong payment method id '
                        );
                    } else {
                         $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong'
                        );
                    }
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
    
    // pharmacy_ledger_test
    
    public function pharmacy_ledger_options() { 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if(array_key_exists('user_id',$params )){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
               
                
                if ($user_id == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id'
                    );
                } else {
                   
                    $res = $this->LedgerModel->pharmacy_ledger_options($user_id);
                    
                    $resp = array(
                        'status' => 200,
                        'message' => 'success',
                        'data'  => $res
                    );
                }
                simple_json_output($resp);
            }
        }
    }
    
    // create user own ledger
    
     public function create_user_ledger() { 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                // user_id
                if(array_key_exists('user_id',$params )){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                
                // ledger_owner_type
                if(array_key_exists('ledger_owner_type',$params ) && gettype($params['ledger_owner_type']) == 'integer' && $params['ledger_owner_type'] >= 0 ){
                     $ledger_owner_type = $params['ledger_owner_type'];
                } else {
                     $ledger_owner_type = "";
                }
                
                // credit
                if(array_key_exists('credit',$params )){
                     $credit = $params['credit'];
                } else {
                     $credit = "";
                }
                
                // debit
                if(array_key_exists('debit',$params )){
                     $debit = $params['debit'];
                } else {
                     $debit = "";
                }
                
                // payment_method
                if(array_key_exists('payment_method',$params )){
                     $payment_method = $params['payment_method'];
                } else {
                     $payment_method = "";
                }
                
                // user_comments
                if(array_key_exists('user_comments',$params )){
                     $user_comments = $params['user_comments'];
                } else {
                     $user_comments = "";
                }
                
                // mw_comments
                if(array_key_exists('mw_comments',$params )){
                     $mw_comments = $params['mw_comments'];
                } else {
                     $mw_comments = "";
                }
                
                
                // vendor_comments
                if(array_key_exists('vendor_comments',$params )){
                     $vendor_comments = $params['vendor_comments'];
                } else {
                     $vendor_comments = "";
                }
                
              
                if ($user_id == ""  || ($credit == "" && $debit == "") || $payment_method == "" || ($user_comments == "" && $mw_comments == "" && $vendor_comments == "")) {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'Send user_id, ledger_owner_type : should be integer and greater than or equal to 0, payment_method, credit or debit, user_comments or mw_comments or vendor_comments'
                    );
                    
                } else {
                   
                    $res = $this->LedgerModel->create_user_ledger($user_id, $ledger_owner_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments);
                    
                    if($res['ledger_id'] > 0){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'ledger_id'  => $res['ledger_id']
                        );
                    }  else {
                         $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong'
                        );
                    }
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
     // update user own ledger
    
     public function update_user_ledger() { 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                // user_id
                if(array_key_exists('user_id',$params )){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                
                // $ledger_id
                if(array_key_exists('ledger_id',$params )){
                     $ledger_id = $params['ledger_id'];
                } else {
                     $ledger_id = "";
                }
                
                // $invoice_no
                if(array_key_exists('invoice_no',$params )){
                     $invoice_no = $params['invoice_no'];
                } else {
                     $invoice_no = "";
                }
                
                // $transaction_id
                if(array_key_exists('transaction_id',$params )){
                     $transaction_id = $params['transaction_id'];
                } else {
                     $transaction_id = "";
                }
                
                // $trans_status
                if(array_key_exists('trans_status',$params ) && $params['trans_status'] >= 0 && $params['trans_status'] < 4){
                     $trans_status = $params['trans_status'];
                } else {
                     $trans_status = "";
                }
              
                if ($user_id == "" || $ledger_id == "" || (($transaction_id == "" || $trans_status > 2  ) && ($trans_status != 3))) {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'send user_id, ledger_id, transaction_id ,trans_status as 1 : success ; 2 : failed ; 3 :  transaction cancel ; 0 - no response '
                    );
                    
                } else {
                   
                    $res = $this->LedgerModel->update_user_ledger($user_id, $ledger_id, $invoice_no, $transaction_id , $trans_status);
                    
                    if($res['status'] == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success'
                          
                        );
                    } else if($res['status'] == 2){
                        $resp = array(
                            'status' => 400,
                            'message' => 'No ledger found for given ledger id'
                          
                        );
                    }  else {
                         $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong'
                        );
                    }
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
     public function get_payment_methods() { 
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                
                // user_id
                if(array_key_exists('user_id',$params )){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                
                // $ledger_id
                if(array_key_exists('vendor_id',$params )){
                     $vendor_id = $params['vendor_id'];
                } else {
                     $vendor_id = "";
                }
                
               
                if ($user_id == "" || $vendor_id == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'send user_id, vendor_id'
                    );
                    
                } else {
                   
                    $res = $this->LedgerModel->get_payment_methods($user_id, $vendor_id);
                    
                    if($res['status'] == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $res['data']
                        );
                    } else if($res['status'] == 2){
                        $resp = array(
                            'status' => 400,
                            'message' => 'No payment methods found'
                          
                        );
                    }  else {
                         $resp = array(
                            'status' => 400,
                            'message' => 'Something went wrong'
                        );
                    }
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
}
?>