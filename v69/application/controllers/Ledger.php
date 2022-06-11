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
    
    public function get_ledger_old() { 
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
                     $listing_id = 0;
                }
                
                // search : search_term added by swaqpnali on 22nd 2k19
                if(array_key_exists('search',$params )){
                     $search = $params['search'];
                } else {
                     $search = "";
                }
                // page_no : search_term added by swaqpnali on 22nd 2k19
                if(array_key_exists('page_no',$params )){
                     $page_no = $params['page_no'];
                } else {
                     $page_no = "";
                }
                // per_page : search_term added by swaqpnali on 22nd 2k19
                if(array_key_exists('per_page',$params )){
                     $per_page = $params['per_page'];
                } else {
                     $per_page = "";
                }
                
                if(array_key_exists('invoice_no',$params )){
                     $invoice_no = $params['invoice_no'];
                } else {
                     $invoice_no = "";
                }
                
                
                // if (($user_id == "" || $listing_id == "") && $invoice_no == "") {
                if ($user_id == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id'
                    );
                } else {
                   
                    $res = $this->LedgerModel->get_ledger_old($user_id,$listing_id,$search,$page_no,$per_page,$invoice_no);
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
                     $listing_id = 0;
                }
                
                // search : search_term added by swaqpnali on 22nd 2k19
                if(array_key_exists('search',$params )){
                     $search = $params['search'];
                } else {
                     $search = "";
                }
                // page_no : search_term added by swaqpnali on 22nd 2k19
                if(array_key_exists('page_no',$params )){
                     $page_no = $params['page_no'];
                } else {
                     $page_no = 1;
                }
                // per_page : search_term added by swaqpnali on 22nd 2k19
                if(array_key_exists('per_page',$params )){
                     $per_page = $params['per_page'];
                } else {
                     $per_page = 10000;
                }
                
                if(array_key_exists('invoice_no',$params )){
                     $invoice_no = $params['invoice_no'];
                } else {
                     $invoice_no = "";
                }
                
                
                // if (($user_id == "" || $listing_id == "") && $invoice_no == "") {
                if ($user_id == "" || $page_no == "" || $per_page == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter user_id, page_no and per_page'
                    );
                } else {
                   
                    $res = $this->LedgerModel->get_ledger($user_id,$listing_id,$search,$page_no,$per_page,$invoice_no);
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
                
                // transaction_date
                
                if(array_key_exists('transaction_date',$params )){
                     $transaction_date = $params['transaction_date'];
                } else {
                     $transaction_date = "";
                }
                
                // vendor_id
                if(array_key_exists('vendor_id',$params )){
                     $vendor_id = $params['vendor_id'];
                } else {
                     $vendor_id = $listing_id_type;
                }
                
                
                // submitted_by
                if(array_key_exists('submitted_by',$params )){
                     $submitted_by = $params['submitted_by'];
                } else {
                     $submitted_by = "";
                }
                
                // vendor_id
                if(array_key_exists('accepted_by',$params )){
                     $accepted_by = $params['accepted_by'];
                } else {
                     $accepted_by = "";
                }
                
                // $data : future use , not using yet
                $data = array();
                // added on 25th APR 2020 by swapnali waghunde
                $data['accepted_by'] = $accepted_by;
                $data['submitted_by'] = $submitted_by;
                
               if($user_id == "" || $invoice_no == "" ||   ($credit  == "" && $debit  == "" )  ||  $order_type == "" || $order_type > 9 || $order_type < 1 || $transaction_of == ""){
            
                    $resp = array(
                        'status' => 400,
                        'message' => 'Please enter user_ledger : it should be 1 or 0 if 1 means user ledger and if 0 means vendor ledger, user_id, invoice_no : it should be invoice_no from user_order table or booking_id from booking_master / doctor_booking_master / hospital_booking_master / pestcontrol_booking_master , ledger_owner_type i.e. user_id type, listing_id, listing_id_type,  either credit or debit amount ,  payment_method, trans_status should be 0: filed; 1: success ; 2 : pending , order_type should be 1 : service ; 2 : appointment ; 3 : doctor appointment; 4 : hospital appointment; 5 : pestcontrol  appointment, transaction_of where 1: package; 2: order amount; 3: delivery charges; 4: ledger balance; 5 : points '
                    );
                    
                } else {
                    $res = $this->LedgerModel->create_ledger($user_id, $invoice_no, $ledger_owner_type, $listing_id, $listing_id_type, $credit, $debit, $payment_method, $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_id,$data);
                    
                    if($res['status'] == 1){
                        $data = array(
                            'ledger_id' => $res['ledger_id'],
                            'type' => $res['type']
                        );
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
                        $description = "";
                        if($trans_status == 1){
                            $description = "SUCCESS";
                        } else if($trans_status == 2){
                            $description = "FAILURE";
                        } else if($trans_status == 3){
                            $description = "DECLINED";
                        } 
                        //EXPIRED // PAYMENT_NOT_INITIATED // DECLINED // FAILURE // SUCCESS

                        
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'description' => $description
                          
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
                     $vendor_id = 0;
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
                
                
                // aded by swapnali on 25th APR 2020 9:18PM
                if(array_key_exists('accepted_by',$params )){
                     $accepted_by = $params['accepted_by'];
                } else {
                     $accepted_by = "";
                }
                
                if(array_key_exists('submitted_by',$params )){
                     $submitted_by = $params['submitted_by'];
                } else {
                     $submitted_by = "";
                }
              
                if ($user_id == "" || $invoice_no == "" || $ledger_id == "" || (($transaction_id == "" || $trans_status > 2  ) && ($trans_status != 3)) || $type == "") {
                    
                    $resp = array(
                        'status' => 400,
                        'message' => 'send user_id, ledger_id, transaction_id, type : user or vendor (Already sent in create ledger) ,trans_status as 1 : success ; 2 : failed ; 3 :  transaction cancel ; 0 - no response '
                    );
                    
                } else {
                   
                    $res = $this->LedgerModel->update_ledger($user_id, $ledger_id, $invoice_no, $transaction_id , $trans_status,$type,$accepted_by,$submitted_by);
                    
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
    
    public function ledger_select() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        if(array_key_exists('listing_id',$params ) && $params['listing_id'] != ""){
                             $listing_id = $params['listing_id'];
                        } else {
                             $listing_id = 0;
                        }
                        
                        $resp = $this->LedgerModel->ledger_select($user_id,$listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    // add points
    public function add_points() { 
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
                
                
                if(array_key_exists('user_id',$params ) && $params['user_id'] != "" ){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                
                // $invoice_no = "";
                
                if(array_key_exists('invoice_no',$params ) && $params['invoice_no'] != ""){
                     $invoice_no = $params['invoice_no'];
                } else {
                     $invoice_no = "";
                }
                 //   transaction_date
                
                if(array_key_exists('transaction_date',$params ) && $params['transaction_date'] != ""){
                     $transaction_date = $params['transaction_date'];
                } else {
                     $transaction_date = "";
                }
                
                //   transaction_id
                
                if(array_key_exists('transaction_id',$params ) && $params['transaction_id'] != ""){
                     $transaction_id = $params['transaction_id'];
                } else {
                     $transaction_id = "";
                }
                
                // points
                if(array_key_exists('points',$params ) && $params['points'] != "" ){
                     $points = $params['points'];
                } else {
                     $points = 0;
                }
                
                // comments = "";
                if(array_key_exists('comments',$params ) && $params['comments'] != "" ){
                     $comments = $params['comments'];
                } else {
                     $comments = "";
                }
                
                // comments = "";
                if(array_key_exists('status',$params ) && $params['status'] != "" ){
                     $status = $params['status'];
                } else {
                     $status = "active";
                }
                
                // listing_type = "";
                if(array_key_exists('listing_type',$params ) && $params['listing_type'] != "" ){
                     $listing_type = $params['listing_type'];
                } else {
                     $listing_type = 0;
                }
                
                // expire_at
                if(array_key_exists('expire_at',$params ) && $params['expire_at'] != "" ){
                     $expire_at = $params['expire_at'];
                } else {
                     $expire_at = "";
                }
                
                
               if($user_id == "" ||   $points < 1 ||  $comments == "" ||   ($listing_type == "" && $listing_type < 0) ||  $expire_at == "" )  {
            
                    $resp = array(
                        'status' => 400,
                        'message' => 'Please enter user_id, invoice_no, points should be greater than 0, comments, listing_type means vendor type id from where user got pointss, expire_at and transaction_date. transaction_date is default current date'
                    );
                    
                } else {
                    $res = $this->LedgerModel->add_points($user_id,  $invoice_no, $transaction_date, $transaction_id,  $points,  $comments,  $status,  $listing_type,  $expire_at);
                    $message = $res['message'];
                    if($res['insert_id'] > 0){
                        
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            
                        );
                    } else{
                        $resp = array(
                            'status' => 200,
                            'message' => $message,
                           
                        );
                    }  
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function get_points() { 
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
                
                
                if(array_key_exists('user_id',$params ) && $params['user_id'] != "" ){
                     $user_id = $params['user_id'];
                } else {
                     $user_id = "";
                }
                
                if(array_key_exists('vendor_type',$params ) && $params['vendor_type'] != "" ){
                     $vendor_type = $params['vendor_type'];
                } else {
                     $vendor_type = 0;
                }
                
                // per_page 
                if(array_key_exists('per_page',$params )){
                     $per_page = $params['per_page'];
                } else {
                     $per_page = 0;
                }
                
                if(array_key_exists('page_no',$params )){
                     $page_no = $params['page_no'];
                } else {
                     $page_no = 0;
                }
                
                
               if($user_id == "" ||    $vendor_type < 0 || $per_page < 1 || $per_page  < 1)  {
            
                    $resp = array(
                        'status' => 400,
                        'message' => 'Please enter user_id, vendor_type, page_no and per_page'
                    );
                    
                } else {
                    $res = $this->LedgerModel->get_points($user_id, $vendor_type, $per_page, $page_no);
                    if(sizeof($res['points']) > 0){
                        
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'description' => 'status_id : 1 - active, 2 - converted, 3 - expired',
                            'data' => $res
                        );
                    } else{
                        $resp = array(
                            'status' => 400,
                            'message' => 'Points not available, please do transactions to get points',
                        );
                    }  
                    
                }
                simple_json_output($resp);
            }
        }
    }
    
    public function ledger_page_options() 
      {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   if(array_key_exists('vendor_type',$params)){
                       $vendor_type = $params['vendor_type'];
                   } else {
                       $vendor_type = 0;
                   }
                    if ($params['user_id'] == "" )
                    {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    }
                    else
                    {
                        $user_id = $params['user_id'];
                        
                        $res = $this->LedgerModel->ledger_page_options($user_id, $vendor_type);
                        // type = >1 : ledger; 2 : Bachat ; 3 : Points ; 4 : coupon wall
                        
                        if(sizeof($res) > 0){
                            $descriptition = 'For type = 1 : ledger; 2 : Bachat ; 3 : Points ; 4 : coupon wall';
                            $resp = array('status' => 200, 'message' => 'success' , 'description' => $descriptition,'data' => $res);
                        } else {
                            $msg = 'Options are not available for given vendor type';
                            $resp = array('status' => 400, 'message' => $msg);
                        }
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function mw_team_extra_points()  {
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) 
                {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                    if(array_key_exists('user_id',$params)){
                        $user_id = $params['user_id'];
                    } else {
                        $user_id ="";
                    }
                   
                    if(array_key_exists('invoice_no',$params)){
                        $invoice_no = $params['invoice_no'];
                    } else {
                        $invoice_no ="";
                    }
                    
                    if(array_key_exists('transaction_id',$params)){
                        $transaction_id = $params['transaction_id'];
                    } else {
                        $transaction_id ="";
                    }
                    
                    if(array_key_exists('points',$params)){
                        $points = $params['points'];
                    } else {
                        $points ="";
                    }
                    
                    if(array_key_exists('comments',$params)){
                        $comments = $params['comments'];
                    } else {
                        $comments ="";
                    }
                    
                    if(array_key_exists('listing_type',$params)){
                        $listing_type = $params['listing_type'];
                    } else {
                        $listing_type =0;
                    }
                
                    if ( $user_id == "" || $invoice_no == "" || $points < 1 ) {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                       
                        $res = $this->LedgerModel->mw_team_extra_points($user_id, $invoice_no, $transaction_id, $points, $comments, $listing_type );
                        if(sizeof($res) > 0){
                            $status = $res['status'];
                            if($status == 1){
                                $resp = array('status' => 200, 'message' => 'Successfully added') ;
                            } else if($status == 3){
                                $resp = array('status' => 400, 'message' => 'Can not add more points to this user');
                            } else if($status == 4){
                                $resp = array('status' => 400, 'message' => 'Already added for given order');
                            } else {
                                $resp = array('status' => 400, 'message' => 'Something went wrong');
                            }
                            
                        } else {
                            $resp = array('status' => 400, 'message' => 'Something went wrong');
                        }
                        
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function payment_method_by_vendor_type(){
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
                
                if(array_key_exists('user_id',$params)){
                    $user_id = $params['user_id'];
                } else {
                    $user_id = "";
                }
           
                if(array_key_exists('vendor_type',$params)){
                    $vendor_type = $params['vendor_type'];
                } else {
                    $vendor_type =0;
                }
                
                
                $res = $this->LedgerModel->payment_method_by_vendor_type($user_id,$vendor_type);
                
                if(sizeof($res) > 0){
                    $resp = array(
                        'status' => 200,
                        'message' => 'success',
                        'data'=>$res
                    );
                }  else {
                    $resp = array(
                        'status' => 400,
                        'message' => 'No payment methods found'
                    );
                }
           
                simple_json_output($resp);
                
                
            }
        }
    }
    
}
?>
