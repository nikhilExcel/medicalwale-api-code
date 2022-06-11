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
    
        public function get_all_ledger() { 
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
               
                
             
                   
                    $res = $this->LedgerModel->get_all_ledger();
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
                
               
                
                // order_type

                if(array_key_exists('order_type',$params )){
                     $order_type = $params['order_type'];
                } else {
                     $order_type = "";
                }
                
                // transaction_of
                if(array_key_exists('transaction_of',$params )){
                     $transaction_of = $params['transaction_of'];
                } else {
                     $transaction_of = "";
                }
                
                //   trans_status
                
                if(array_key_exists('trans_status',$params )){
                     $trans_status = $params['trans_status'];
                } else {
                     $trans_status = "";
                }
                
                 //   transaction_id
                
                if(array_key_exists('transaction_id',$params )){
                     $transaction_id = $params['transaction_id'];
                } else {
                     $transaction_id = "";
                }
                
                if ($user_id == "" || $invoice_no == "" ||  ($credit  == "" && $debit  == "" ) || $payment_method  == "" ||  $order_type == "" || $order_type > 5 || $order_type < 1 || $transaction_of == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_ledger : it should be 1 or 0 if 1 means user ledger and if 0 means vendor ledger, user_id, invoice_no : it should be invoice_no from user_order table or booking_id from booking_master / doctor_booking_master / hospital_booking_master / pestcontrol_booking_master , ledger_owner_type i.e. user_id type, listing_id, listing_id_type,  either credit or debit amount ,  payment_method, trans_status should be 0: filed; 1: success ; 2 : pending , order_type should be 1 : service ; 2 : appointment ; 3 : doctor appointment; 4 : hospital appointment; 5 : pestcontrol  appointment, transaction_of where 1: package; 2: order amount; 3: delivery charges; 4: ledger balance; 5 : points '
                    );
                    
                } else {
                    $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $ledger_owner_type, $listing_id, $listing_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status);
                    $data = array(
                            'ledger_id' => $res['ledger_id'],
                            'type' => $res['type']
                        );
                    if($res['status'] == 1){
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data'  => $data
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
                    } else if($res['status'] == 4){
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user_ledger : it should be 1 or 0 if 1 means user ledger and if 0 means vendor ledger, user_id, invoice_no : it should be invoice_no from user_order table or booking_id from booking_master / doctor_booking_master / hospital_booking_master / pestcontrol_booking_master , ledger_owner_type i.e. user_id type, listing_id, listing_id_type,  either credit or debit amount ,  payment_method, trans_status should be 0: filed; 1: success ; 2 : pending , order_type should be 1 : service ; 2 : appointment ; 3 : doctor appointment; 4 : hospital appointment; 5 : pestcontrol  appointment,  transaction_of where 1: package; 2: order amount; 3: delivery charges; 4: ledger balance; 5 : points '
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
                
               
                if ($user_id == "") {
                    
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
    
    // settle_ledger
     public function settle_ledger() { 
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
                
                // listing_type
                if(array_key_exists('listing_type',$params )){
                     $listing_type = $params['listing_type'];
                } else {
                     $listing_type = "";
                }
                
                
                // 
                if(array_key_exists('invoice_no',$params )){
                     $invoice_no = $params['invoice_no'];
                } else {
                     $invoice_no = "";
                }
                
               
                if ($user_id == "" || $listing_type < 0 || $invoice_no == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'send user_id, listing_type, invoice_no'
                    );
                    
                } else {
                   
                    $res = $this->LedgerModel->settle_ledger($user_id,$listing_type, $invoice_no);
                    // print_r($res['status']); die();
                    
                    if($res['status'] == 1){
                       $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $res['message']
                        );
                    }  else if($res['status'] == 2){
                        $resp = array(
                            'status' => 400,
                            'message' =>  $res['message']
                          
                        );
                    }else  if($res['status'] == 3){
                        $resp = array(
                            'status' => 400,
                            'message' =>  $res['message']
                           
                        );
                    }  else {
                         $resp = array(
                            'status' => 400,
                            'message' => '--Something went wrong'
                        );
                    }
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
    // use to update created api using create_ledger
    // update_ledger
    public function update_ledger() { 
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
                
                // $type
                if(array_key_exists('type',$params )){
                     $type = $params['type'];
                } else {
                     $type = "";
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
              
                if ($user_id == "" || $invoice_no == "" || $ledger_id == "" || (($transaction_id == "" || $trans_status > 2  ) && ($trans_status != 3)) || $type == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'send user_id, ledger_id, transaction_id, type : user or vendor (Already sent in create ledger) ,trans_status as 1 : success ; 2 : failed ; 3 :  transaction cancel ; 0 - no response '
                    );
                    
                } else {
                   
                    $res = $this->LedgerModel->update_ledger($user_id, $ledger_id, $invoice_no, $transaction_id , $trans_status,$type);
                    
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
                    } else if($res['status'] == 3){
                        $resp = array(
                            'status' => 400,
                            'message' => 'No type found for given ledger id : it should be either user or vendor'
                          
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