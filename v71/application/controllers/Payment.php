<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
         $this->load->model('PaymentModel'); 
          $this->load->model('LoginModel');
          $this->load->model('LedgerModel');
        /*
        $check_auth_client = $this->SexeducationModel->check_auth_client();
		if($check_auth_client != true){
			die($this->output->get_output());
		}
		*/
    }
	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	public function paypal_create_order()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                
                //$username = 'AT51txGdbOcn60Y96XFHRpmrNmXHtPvtaful9WUfpdqP2ytadEkNQx5monoLFTNvDFL-3O4cYiAa6IXO'; //live
                //$password = 'ENzQ-Q59UBffAk1ZEt4cuUlyGpUkKIIENicsFPwxa4Zz4R8Yngr5WARKYB0MbTvosP_oB1MQ7gAmuRMS'; //live
                
                $username = 'AUf6Jh8viomIa90m8KMFndz2iIwKkIcpzZHTUmKY1f8M9J7uDeQ1zO7d-lTb85AU4oiBHBlb2mBZ9g9_'; //sandbox
                $password = 'EAnj3FLOR6FENgUKjBvkrB-m8mNIJMxNHycWdz7DuMpL_8T6dRmAV1MEfExXJTY139AXuZ0TKdTUcZQJ'; //sandbox
                $headers = array(
                    'Authorization:Basic '.base64_encode("$username:$password"),
                    'Content-Type:application/x-www-form-urlencoded',
                    'Accept:application/json',
                    'Accept-Language:en_US'
                );
                $url="https://api.sandbox.paypal.com/v1/oauth2/token";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch); 
                $res=json_decode($result,true);
                $access_token =  $res['access_token'];
                
                date_default_timezone_set('Asia/Kolkata');
                $uni_id = date("YmdHis");
                $first_name = $params['first_name'];
                $last_name = $params['last_name'];
                $national_number = $params['phone'];
                $email = $params['email'];
                $grand_amount = $params['amount'];
                $user_id = $params['user_id'];
                $shipping_full_name = $params['shipping_full_name'];
                $shipping_pincode = $params['shipping_pincode'];
                $shipping_address1 = $params['shipping_address1'];
                $shipping_address2 = $params['shipping_address2'];
                $shipping_city = $params['shipping_city'];
                $shipping_state = $params['shipping_state'];
                $device_type = $params['device_type'];
                $contexts=$this->paypal_transaction_contexts($uni_id,$email,$first_name,$last_name,$access_token,$user_id,$national_number);
                if($contexts==''){
                    $invoice_id = date("YmdHis");
                    $payment_method = array(
                       "payer_selected"=>"PAYPAL"
                    );
                    if($device_type=='app'){
                        $return_url=base_url().'payment/payment_success';
                        $cancel_url=base_url().'payment/payment_cancel';
                    }
                    else{
                        $return_url='http://sandboxstg.medicalwale.com/website5/healthmall/payment_success';
                        $cancel_url='http://sandboxstg.medicalwale.com/website5/healthmall/payment_cancel';
                    }
                    $application_context= array(
                       "brand_name"=>"Medicalwale",
                       "locale"=>"en-IN",
                       "shipping_preference"=>"NO_SHIPPING",
                       "user_action"=>"PAY_NOW",
                       "return_url"=>$return_url,
                       "cancel_url"=>$cancel_url,
                       "payment_method" => $payment_method
                    );
                    $name = array(
                       "given_name"=>$first_name,
                       "surname"=>$last_name   
                    );
                    if(empty($email)){
                        $email='info@medicalwale.com';
                    }
                    if(empty($national_number)){
                        $national_number='022 68 44 33 22';
                    }
                    $phone_number = array(
                       "national_number"=>$national_number
                    );
                    $phone = array(
                       "phone_number"=>$phone_number
                    );
                    $payer = array(
                       'name'=> $name,
                       'email_address'=>$email,
                       'phone' => $phone
                    );
                    $item_total = array(
                       "currency_code" => "INR",
                       "value" => $grand_amount
                    );
                    $shipping = array(
                       "currency_code" => "INR",
                       "value" => "00.00"
                    );
                    $handling = array(
                       "currency_code" => "INR",
                       "value" => "00.00"
                    );
                    $tax_total = array(
                       "currency_code" => "INR",
                       "value" => "00.00"
                    );
                    $breakdown = array(
                       "item_total"=>$item_total,
                       //"shipping"=>$shipping,
                       "handling"=>$handling,
                       "tax_total"=>$tax_total
                    );
                    $amount = array(
                       "currency_code"=> "INR",
                       "value"=> $grand_amount,
                       "breakdown" => $breakdown
                    );
                    $unit_amount = array(
                       "currency_code"=> "INR",
                       "value"=> $grand_amount
                    );
                    $items[] = array(
                        "name" => "Medicalwale Order Id – ".$invoice_id,
                        "description" => "Medicalwale Order Id – ".$invoice_id,
                        "quantity" => "1", 
                        "unit_amount" => $unit_amount, 
                        "category" => "PHYSICAL_GOODS"
                    );
                    
                    $shipping_name = array(
                        "full_name"=> $shipping_full_name
                    );
                    $shipping_address = array(
                        "address_line_1"=> $shipping_address1,
                        "address_line_2"=> $shipping_address2,
                        "admin_area_2"=> $shipping_city,
                        "admin_area_1"=> $shipping_state,
                        "country_code"=> "IN",
                        "postal_code"=> $shipping_pincode
                    );
                    $shipping_info = array(
                        "name"=> $shipping_name,
                        "address"=> $shipping_address
                    );
                    $purchase_units[] = array(
                       'amount'=> $amount,
                       'items' => $items,
                       'shipping'=>$shipping_info,
                       'soft_descriptor'=>'Medicalwale',
                       'custom_id'=>$user_id,
                       'invoice_id'=>$invoice_id
                    );
                    $request_array= array(
                       'intent' => 'CAPTURE',
                       'application_context' =>$application_context,
                       'payer' => $payer,
                       'purchase_units' => $purchase_units
                    );
                    $headers = array(
                       'Authorization:Bearer '.$access_token,
                       'Content-Type:application/json',
                       'Prefer:return=representation',
                       'PayPal-Client-Metadata-Id:'.$uni_id
                    );
                    $url="https://api.sandbox.paypal.com/v2/checkout/orders";
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request_array));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch); 
                    $res=json_decode($result,true);
                    $payment_id=$res['id'];
                    if($payment_id!=''){
                        $insert = $this->PaymentModel->insert_paypal_token_payment_id($payment_id,$access_token,$uni_id,$invoice_id);
                        simple_json_output($insert);
                    }
                    else{
                        $resp = array('status' => 400,'message' =>  'Order Creation Failed');
                        simple_json_output($resp);
                    }
                }
                else{
                    $resp = array('status' => 400,'message' =>  'STC API ERROR');
                    simple_json_output($resp);
                }
                
            }
        }
    }
    
    public function paypal_transaction_contexts($uni_id,$email,$first_name,$last_name,$access_token,$user_id,$national_number)
    {
        $headers = array(
            'Authorization:Bearer '.$access_token,
            'Content-Type:application/json'
        );
        $additional_data[] = array(
            'key' => 'sender_account_id',
            'value' => $user_id
        );
        $additional_data[] = array(
            'key' => 'sender_first_name',
            'value' => $first_name
        );
        $additional_data[] = array(
            'key' => 'sender_last_name',
            'value' => $last_name
        );
        $additional_data[] = array(
            'key' => 'sender_email',
            'value' => $email
        );
        $additional_data[] = array(
            'key' => 'sender_phone',
            'value' => $national_number
        );
        $additional_data[] = array(
            'key' => 'sender_country_code',
            'value' => 'IN'
        );
        date_default_timezone_set('Asia/Kolkata');
        $date = date("Y-m-d H:i:s");
        $additional_data[] = array(
            'key' => 'sender_create_date',
            'value' => $date
        );
        $contexts_array= array(
           'tracking_id' => $uni_id,
           'additional_data' =>$additional_data
        );
        $url="https://api.sandbox.paypal.com/v1/risk/transaction-contexts/XXFM9V5P9GFRE/".$uni_id;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($contexts_array));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch); 
        return json_decode($result,true);
    }
    
    public function paypal_check_status()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                
                //$username = 'AT51txGdbOcn60Y96XFHRpmrNmXHtPvtaful9WUfpdqP2ytadEkNQx5monoLFTNvDFL-3O4cYiAa6IXO'; //live
                //$password = 'ENzQ-Q59UBffAk1ZEt4cuUlyGpUkKIIENicsFPwxa4Zz4R8Yngr5WARKYB0MbTvosP_oB1MQ7gAmuRMS'; //live
                
                $username = 'AUf6Jh8viomIa90m8KMFndz2iIwKkIcpzZHTUmKY1f8M9J7uDeQ1zO7d-lTb85AU4oiBHBlb2mBZ9g9_'; //sandbox
                $password = 'EAnj3FLOR6FENgUKjBvkrB-m8mNIJMxNHycWdz7DuMpL_8T6dRmAV1MEfExXJTY139AXuZ0TKdTUcZQJ'; //sandbox
                $headers = array(
                    'Authorization:Basic '.base64_encode("$username:$password"),
                    'Content-Type:application/x-www-form-urlencoded',
                    'Accept:application/json',
                    'Accept-Language:en_US'
                );
                $url="https://api.sandbox.paypal.com/v1/oauth2/token";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch); 
                $res=json_decode($result,true);
                $access_token = $res['access_token'];
                $payment_id = $params['payment_id'];
                $headers      = array(
                   'Authorization:Bearer '.$access_token
                );
                $url = "https://api.sandbox.paypal.com/v2/checkout/orders/".$payment_id;
                $ch  = curl_init($url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch);
                $res=json_decode($result,true);
                $payment_status=$res['status'];
                if($payment_status=='CREATED'){
                    $payment_status='PENDING';
                }
                else if($payment_status=='APPROVED'){
                    $payment_status='PROCESSING';
                }
                else if($payment_status=='COMPLETED'){
                    $payment_status='SUCCESS';
                }
                $resp = array(
                    "status" => 200,
                    "message" => "success",
                    "payment_id" => $payment_id,
                    "payment_status" => $payment_status
                );
                simple_json_output($resp);
            }
        }
    }
    
    public function payment_success()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $payment_id=$_GET['token'];
            $payment = $this->PaymentModel->paypal_capture($payment_id); 
            simple_json_output($payment);
        }
    }
    
    public function payment_cancel()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'GET') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $resp = array('status' => 400,'message' =>  'Payment Cancelled');
            simple_json_output($resp);
        }
    }
	
	public function razorpay_capture()
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
                $params   = json_decode(file_get_contents('php://input'), TRUE);
                $payment_id = $params['payment_id'];
                $amount = $params['amount'];
                
                
                $username = 'rzp_test_vsRubUcePYzPG8';
                $password = 'J2dVsROks8y7E8sN9TYXod3X';
                $headers = array(
                    'Authorization: Basic '. base64_encode("$username:$password")
                );
                $data_fields = array(
                    'amount' => $amount
                );
                $url="https://api.razorpay.com/v1/payments/".$payment_id."/capture";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                curl_close($ch); 
                echo $result;
            }
        }
    }
	
    public function generate_razorpay_payment_id()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->PaymentModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['amount']=="") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $amount = $params['amount'];
                        $user_id = $params['user_id'];
                        $resp = $this->PaymentModel->generate_razorpay_payment_id($user_id,$amount);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
	
    public function add_payment_status()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['trans_id'] == "" || $params['orderid_bookingid']  == ""|| $params['order_type'] == "" || $params['status'] == "" || $params['type'] == "" || $params['amount'] == "" || $params['status_mesg'] == "" || $params['payment_type'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					 //   echo 'kadakkkkk'.$params['payment_type'];
					    $user_id        = $params['user_id'];
					    $listing_id     = $params['listing_id'];
					    $trans_id       = $params['trans_id'];
					    $status         = $params['status'];
					    $type           = $params['type'];
					    $order_id       = $params['orderid_bookingid'];
					    $order_type     = $params['order_type'];
					    $amount         = $params['amount'];
					    $status_mesg    = $params['status_mesg'];
					    $discount       = $params['discount'];
					    $discount_rupee = $params['cashback'];
					    $payment_type   = $params['payment_type'];
					   // $vendor_category = $params['vendor_category'];
					    
		        	//	$resp           = $this->PaymentModel->insert_payment_status($user_id, $listing_id, $vendor_category, $trans_id, $status, $type, $order_id, $amount, $status_mesg, $discount, $discount_rupee, $payment_type);
					 if(empty($params['opd']))
                        {
                            $opd="";
                        }
                        else
                        {
                        $opd = $params['opd'];
                        }
                        
                         if(array_key_exists('card_type',$params))
                           {
                                $card_type = $params['card_type'];
                            } else {
                                $card_type = 0;
                            }
                        
                        
					    	$resp           = $this->PaymentModel->insert_payment_status_v1($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type, $amount, $status_mesg, $discount, $discount_rupee, $payment_type,$opd,$card_type);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	 public function add_payment_status_amb()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['trans_id'] == "" || $params['orderid_bookingid']  == ""|| $params['order_type'] == "" || $params['status'] == "" || $params['type'] == "" || $params['amount'] == "" || $params['status_mesg'] == "" || $params['payment_type'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					 //   echo 'kadakkkkk'.$params['payment_type'];
					    $user_id        = $params['user_id'];
					    $listing_id     = $params['listing_id'];
					    $trans_id       = $params['trans_id'];
					    $status         = $params['status'];
					    $type           = $params['type'];
					    $order_id       = $params['orderid_bookingid'];
					    $order_type     = $params['order_type'];
					    $amount         = $params['amount'];
					    $status_mesg    = $params['status_mesg'];
					    $discount       = $params['discount'];
					    $discount_rupee = $params['cashback'];
					    $payment_type   = $params['payment_type'];
				
					    	$resp           = $this->PaymentModel->insert_payment_status_amb($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type, $amount, $status_mesg, $discount, $discount_rupee, $payment_type,$opd);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
    public function add_both_ledger()
	{
	   /* echo "dasd";
	    print_r($_POST);
	    print_r($_GET);
	    die();*/
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(200,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					//die();
					if ($params['order_id'] == "" || $params['mode'] == "" ) {
						$comp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $order_id             = $params['order_id'];
					    $mode             = $params['mode'];
			            $comp           = $this->PaymentModel->get_payment_status($order_id,$mode);       
			         }
					    simple_json_output($comp);
				
		        }
			}
		}
	}	
	
	
	/// added for medicalwale admin for approval
	
	 public function add_both_ledger_for_admin()
	{
	    //print($_POST);
	   // die();
	    //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PaymentModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
				//	die();
					if ($params['id'] == "" || $params['mode'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $id             = $params['id'];
					    $mode             = $params['mode'];
			            $comp           = $this->PaymentModel->add_both_ledger_for_admin($id,$mode);       
			         }
					 simple_json_output($comp);
		        }
			}
		}
	}	
	 
	public function get_user_ledger_details(){
	    
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id        = $params['user_id'];

		        		$ledger_details           = $this->PaymentModel->get_user_ledger_details($user_id);
					}
					    json_outputs($ledger_details);
				
		        }
			}
		}
	    
	}
	
	
		public function get_vendor_ledger_detail(){
	    
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id        = $params['user_id'];

		        		$ledger_details           = $this->PaymentModel->get_vendor_ledger_detail($user_id);
					}
					    json_outputs($ledger_details);
				
		        }
			}
		}
	    
	}
	
	
	//adding pagination in user_ledger table 
	
	public function get_user_ledger_details_pagination(){
	    
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id = $params['user_id'];
					    $page = $params['page_no'];

		 	$ledger_details  = $this->PaymentModel->get_user_ledger_details_pagination($user_id,$page);
					}
					    json_outputs($ledger_details);
				
		        }
			}
		}
	    
	}
	
	public function get_vendor_ledger_details(){
	    
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id   = $params['user_id'];

		        		$ledger_details   = $this->PaymentModel->get_vendor_ledger_details($user_id);
					}
					    json_outputs($ledger_details);
				
		        }
			}
		}
	    
	}
	
	public function get_ledgerBal_Points(){
	    
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id   = $params['user_id'];

		        		$ledger_details   = $this->PaymentModel->get_ledgerBal_Points($user_id);
					}
					    simple_json_output($ledger_details);
				
		        }
			}
		}
	    
	}
	
	
	public function user_points_redeem(){
	    
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "" || $params['redeem'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id        = $params['user_id'];
					    $redeem        = $params['redeem'];

		        		$ledger_details           = $this->PaymentModel->convert_redeem_points($user_id, $redeem);
					}
					    simple_json_output($ledger_details);
				
		        }
			}
		}
	    
	}	
	
	public function convert_user_points_amount(){
	   $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id        = $params['user_id'];
					   $ledger_          = $this->PaymentModel->convert_user_points_to_amount($user_id);
					}
					   simple_json_output($ledger_);
				}
			}
		} 
	}
	
	public function add_cart(){
    
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['product_id'] == "" || $params['quantity'] == "" || $params['sub_category'] == "" || $params['product_type'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id        = $params['user_id'];
					    $listing_id     = $params['listing_id'];
					    $product_id     = $params['product_id'];
					    $quantity       = $params['quantity'];
					    $sub_category   = $params['sub_category'];
					    $product_type   = $params['product_type'];
					    $status         = 'created';
					    
					    $ledger_         = $this->PaymentModel->insert_add_cart($user_id, $listing_id, $product_id, $quantity, $sub_category, $product_type, $status);
					}
					   simple_json_output($ledger_);
				}
			}
		} 
            
    }    
	
	public function delete_cart(){
    
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['cart_id'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $cart_id        = $params['cart_id'];
					   
					    
					    $ledger_         = $this->PaymentModel->delete_cart($cart_id);
					}
					   simple_json_output($ledger_);
				}
			}
		} 
            
    } 
    
    public function update_cart(){
    
        $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['cart_id'] == "" || $params['quantity'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $cart_id        = $params['cart_id'];
					    $quantity       = $params['quantity'];
					   
					    
					    $ledger_         = $this->PaymentModel->update_cart($cart_id, $quantity);
					}
					   simple_json_output($ledger_);
				}
			}
		} 
            
    } 
    
    //added by jakir for card details 
   
     public function get_cart_details_list(){
        $this->load->model('DoctorModel');
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
                    $params             = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id          = $params['user_id'];
                   
                    if ($user_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user id'
                        );
                    } else {
                        $data = $this->DoctorModel->get_cart_details_list($user_id);
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' => $data
                        );
                    }
                }
                simple_json_output($resp);
            }
        }
    }    
    
    //----------------------INVOICE DATA INSERT-----------------
    
    public function add_invoice()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					//$params = json_decode(file_get_contents('php://input'), TRUE);
			/*	print_r($_POST);
				print_r($_FILES);*/
				//die();
					if ($_POST['invoice_number'] == "" || $_POST['invoice_date'] == "" || $_POST['order_id'] == "" || $_POST['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $invoice_file = "";
					    $user_id               = $_POST['user_id'];
					    $invoice_number        = $_POST['invoice_number'];
					    $order_id              = $_POST['order_id'];
					    $invoice_date          = $_POST['invoice_date'];
					    $comment               = $_POST['comment'];
					    $cny = count($_FILES);
					    if($cny > 0){
					    $invoice_file          = $_FILES['invoice_file']['name'];
					    }
					    
					    $resp           = $this->PaymentModel->add_invoice($user_id, $invoice_number, $order_id, $invoice_date, $comment, $invoice_file);
					    
					    
					    
                        
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
    
    //----------------------INVOICE DATA INSERT END-------------
     //----------------------INVOICE DATA INSERT-----------------
    
    public function add_invoice_web()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					//$params = json_decode(file_get_contents('php://input'), TRUE);
			/*	print_r($_POST);
				print_r($_FILES);*/
				//die();
					if ($_POST['invoice_number'] == "" || $_POST['invoice_date'] == "" || $_POST['order_id'] == "" || $_POST['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					 
					    $user_id               = $_POST['user_id'];
					    $invoice_number        = $_POST['invoice_number'];
					    $order_id              = $_POST['order_id'];
					    $invoice_date          = $_POST['invoice_date'];
					    $comment               = $_POST['comment'];
					    
					    $invoice_file          = $_POST['invoice_file'];
					    
					    
					    $resp           = $this->PaymentModel->add_invoice_web($user_id, $invoice_number, $order_id, $invoice_date, $comment, $invoice_file);
					    
					    
					    
                        
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
    
    //----------------------INVOICE DATA INSERT END-------------


    //VIEW INVOICE
    
    public function view_invoice()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
				/*print_r($_POST);
				print_r($_FILES);*/
				//die();
					if ($params['order_id'] == "" || $params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id               = $params['user_id'];
					    $order_id        = $params['order_id'];
					    
					    $resp           = $this->PaymentModel->view_invoice($user_id, $order_id);
					    
					    
					    
                        
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}    
    
    //VIEW INVOICE END
    
    //DELETE INVOICE IMAGES
    
    public function delete_invoice_attachment()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
				/*print_r($_POST);
				print_r($_FILES);*/
				//die();
					if ($params['image_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $image_id               = $params['image_id'];
					    
					    $resp           = $this->PaymentModel->delete_invoice_attachment($image_id);
					    
					    
					    
                        
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}    
    
    //DELETE INVOICE IMAGES
	
	
	
	
	 //DELETE FULL INVOICE
    
    public function delete_invoice()
	{
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PaymentModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
			/*	print_r($_POST);
				print_r($_FILES);
				die();*/
					if ($params['order_id'] == "" || $params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					   $user_id         = $params['user_id'];
					   $order_id        = $params['order_id'];
					   $resp           = $this->PaymentModel->delete_invoice($user_id, $order_id);
					   
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}    
    
    //DELETE FULL INVOICE
    
    // add delivery charges in different conditions 
    // : added by swapnali on 17th sept 2019
    public function add_delivery_charges(){
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
			
					   if(array_key_exists('delivery_charges',$params)){
					        $delivery_charges         = $params['delivery_charges'];
					   }    else {
					        $delivery_charges         = 0;
					   }
					       
					   if(array_key_exists('invoice_no',$params)){
					        $invoice_no        = $params['invoice_no'];
					   }    else {
					        $invoice_no        = "";
					   }
					   
					   if(array_key_exists('delivery_charges_by_customer',$params)){
					       $delivery_charges_by_customer = $params['delivery_charges_by_customer']; 
					   }    else {
					       $delivery_charges_by_customer = 0; 
					   }    
					   
					   if(array_key_exists('offer_id',$params)){
					        $offer_id = $params['offer_id'];
					   }    else {
					        $offer_id = 0;
					   }
					   
					   
					   if($delivery_charges < 1 || $invoice_no == ""){
					       $resp = array('status' => 400,'message' =>  'Delivery charges should be greater than 0 and also send invoice no');
					   } else {
					        $res  = $this->PaymentModel->add_delivery_charges($delivery_charges, $invoice_no, $delivery_charges_by_customer, $offer_id);
					        if($res['status'] == true){
					            $resp = array('status' => 200,'message' =>  'success', 'data' => $res['data']);
					        } else {
					            $resp = array('status' => 400,'message' =>  'fail');    
					        }
					        
					   }
					   
					
					    simple_json_output($resp);
				
		        }
			}
		}
	}    
	
// 	get invoice costing

public function get_invoice_costing(){
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
			
					   if(array_key_exists('invoice_no',$params)){
					        $invoice_no         = $params['invoice_no'];
					   }    else {
					        $invoice_no         = 0;
					   }
					       
					   if($invoice_no == 0 || $invoice_no == "" || $invoice_no == null){
					       $resp = array('status' => 400,'message' =>  'Send invoice no');
					   } else {
					        $res  = $this->PaymentModel->get_invoice_costing($invoice_no);
					        if($res['order_found'] == 1){
					            $resp = array('status' => 200,'message' =>  'success', 'data' => $res['data']);
					        } else {
					            $resp = array('status' => 400,'message' =>  'Order not found');    
					        }
					        
					   }
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
	
	
// 	Gpay Section Starts
   public function send_gpay_request(){
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
            			  $user_id        = $params['user_id'];
            			  $mobile         = $params['mobile'];
            			  $amount         = $params['amount'];
            			  $details        = $params['details'];
					   if($user_id=='' || $mobile=='' || $amount=='' || $details==''){
					       $resp = array('status' => 400,'message' =>  'Send All Fields!');
					   } else {
					        $accessToken = $this->getAccessToken();
					        $resp  =$this->initatePaymentRequest($accessToken, $mobile, $amount, $details);
					   }
					   simple_json_output($resp);
				
		        }
			}
		}
	}
	
	
    public function payment_response_check(){
	  //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->LoginModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
            			  $user_id       = $params['user_id'];
            			  $txn_id        = $params['txn_id'];
				
					       
					   if($user_id=='' || $txn_id==''){
					       $resp = array('status' => 400,'message' =>  'Send All Fields!');
					   } else {
                        $url     = 'https://nbupayments.googleapis.com/v1/merchantTransactions:get';
                        $payLoad = array(
                            'merchantInfo' => array(
                                'googleMerchantId' => 'BCR2DN6TWWILL3AC'
                            ),
                            'transactionIdentifier' => array(
                                'merchantTransactionId' => $txn_id
                            )
                        );
                        
                        $accessToken = $this->getAccessToken();
                        $options     = array(
                            'http' => array(
                                'header' => "Content-Type: application/json\r\n" . "Authorization: Bearer " . $accessToken,
                                'method' => 'POST',
                                'content' => json_encode($payLoad)
                            )
                        );
                        $context     = stream_context_create($options);
                        $result      = file_get_contents($url, false, $context);
                        $data = json_decode($result, true);
                        
                        $status = $data["transactionStatus"]["status"];
                        $resp = array('status' => 200,'message' =>  'success', 'status' => $status);
					   }
					  simple_json_output($resp);
		        }
			}
		}
	}
	

	
	    
    public function getAccessToken()
    {
        /*
         *  Read the JSON credential file service-account.json downloaded from Google
         */
        $private_key_file = base_url() . "assets/gpay/service-account.json";
        $json_file        = file_get_contents($private_key_file);
        $info             = json_decode($json_file);
        $private_key      = $info->{'private_key'};
        
        // Get JWT Token for given service account without using any library
        $jwtAssertion = $this->getJWTToken($private_key, $info);
        /*
         *  Post call to www.googleapis.com/oauth2/v4/token to get access token
         */
        
        $url     = 'https://www.googleapis.com/oauth2/v4/token';
        $data    = array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion' => $jwtAssertion
        );
        $options = array(
            'http' => array(
                'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            )
        );
        
        $context     = stream_context_create($options);
        $result      = file_get_contents($url, false, $context);
        $data        = json_decode($result, true);
        $accessToken = $data['access_token'];
        // echo "Access Token for given payload: ".$accessToken;
        return $accessToken;
    }
	
    
    public function initatePaymentRequest($accessToken, $mobile, $amount, $details)
    {
        $payLoad = $this->getRequestPayload($accessToken, $mobile, $amount, $details);
        $result  = $this->doPostRequest($accessToken, $payLoad);
        
        $txt_id = $payLoad["merchantTransactionDetails"]["transactionId"];
        if ($result == "{}\n") {
            $result_res = 'success';
        } else {
            $result_res = json_encode($result);
        }
        
        $resultpost = array(
            'txt_id' => $txt_id,
            'accessToken' => $accessToken,
            'result' => $result_res,
        );
        
 
        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $resultpost
        );
    }
    
        public function getJWTToken($private_key, $info)
    {
        /*
         *  Base64url encoded JSON header
         */
        $jwtHeader = $this->base64url_encode(json_encode(array(
            "alg" => "RS256",
            "typ" => "JWT"
        )));
        
        /*
         *  Base64url encoded JSON claim set
         */
        $now      = time();
        $jwtClaim = $this->base64url_encode(json_encode(array(
            "iss" => $info->{'client_email'},
            "scope" => "https://www.googleapis.com/auth/nbupaymentsmerchants",
            "aud" => "https://www.googleapis.com/oauth2/v4/token",
            "exp" => $now + 3600,
            "iat" => $now
        )));
        
        $data = $jwtHeader . "." . $jwtClaim;
        //echo $data . "\n\n\n DATA";
        /*
         *  Generating JWT siganture using openssl_sign
         */
        $Sig  = '';
        openssl_sign($data, $Sig, $private_key, 'SHA256');
        $jwtSign      = $this->base64url_encode($Sig);
        $jwtAssertion = $data . "." . $jwtSign;
        //echo "\n\njwt\n".$jwtAssertion."\n\n";
        return $jwtAssertion;
    }
    
    
    public function getRequestPayload($accessToken, $mobile, $amount, $details)
    {
        date_default_timezone_set('Asia/Calcutta');
        $datetime = strtotime('+5 minutes', strtotime(date("Y-m-d h:i:sa")));
        $datetime = date("c", $datetime);
        $txn      = time() . mt_rand();
        $payLoad  = array(
            'merchantInfo' => array(
                'googleMerchantId' => 'BCR2DN6TWWILL3AC'
            ),
            'userInfo' => array(
                'phoneNumber' => '+91' . $mobile
            ),
            'merchantTransactionDetails' => array(
                'transactionId' => $txn,
                'amountPayable' => array(
                    'currencyCode' => 'INR',
                    'units' => $amount,
                    'nanos' => 0
                ),
                'description' => "$details"
            ),
            'originatingPlatform' => 'DESKTOP',
            'expiryTime' => $datetime
        );
        //echo json_encode($payLoad);
        return $payLoad;
    }
    
    
        /*
     * Make initiate payment post request using Access Token and payload.
     * */
    public function doPostRequest($accessToken, $payLoad)
    {
        $url     = 'https://nbupayments.googleapis.com/v1/merchantPayments:initiate';
        $options = array(
            'http' => array(
                'header' => "Content-Type: application/json\r\n" . "Authorization: Bearer " . $accessToken,
                'method' => 'POST',
                'content' => json_encode($payLoad)
            )
        );
        $context = stream_context_create($options);
        $result  = file_get_contents($url, false, $context);
        //echo "\nRESULTT\n\n".$result;
        return $result;
    }
    
    /*
     * Validate response of initiate payment. Successful response will be "{}" . User will get notification on his mobile number.
     * After certain time interval, do Get Request api call to get payment status.
     * */
    public function validateResponse($result)
    {
        /*  if($result == "{}\n")
        echo '\nSuccessful initiated payment with response '.$result;
        else
        echo '\nPayment request failed '.json_encode($result);*/
        
        echo json_encode(array(
            'status' => 200,
            'message' => 'success',
            'data' => $result
        ));
        
    }
    
    
    public function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
    
    
    public function generate_bhc() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_user = $this->LoginModel->check_auth_client();
            if ($check_auth_user == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                $user_id = $params['user_id'];
                if(array_key_exists('coupon_id', $params)){
                    $coupon_id = $params['coupon_id'];
                } else {
                    $coupon_id = 0;
                }
                
                // $amount
                if(array_key_exists('amount', $params)){
                    $amount = $params['amount'];
                } else {
                    $amount = 0;
                }
                
                
                // $payment_method
                if(array_key_exists('payment_method', $params)){
                    $payment_method = $params['payment_method'];
                } else {
                    $payment_method = "";
                }
                
                // $transaction_id
                if(array_key_exists('transaction_id', $params)){
                    $transaction_id = $params['transaction_id'];
                } else {
                    $transaction_id = "";
                }
                
                // $transaction_status
                if(array_key_exists('transaction_status', $params)){
                    $transaction_status = $params['transaction_status'];
                } else {
                    $transaction_status = "";
                }
                
                
                if($user_id == ""  ){
                    $response = array('status' => 400 , 'message' => 'please send all fields', 'description' => 'required fields are user_id, amount, payment_method, transaction_id if payment is not CAP, transaction status'); 
                } else {
                    $resp = $this->PaymentModel->generate_bhc($user_id,$coupon_id, $amount, $payment_method, $transaction_id, $transaction_status);
                    if($resp['status'] == 200 ){
                        $card_no = $resp['card_no'];
                        $card_type = $resp['card_type'];
                        $msg = "";    
                        $data = array();
                        if($resp['card_status'] == 1 ){
                           $msg = "Card generated successfully"; 
                            
                        } else {
                            $msg = "Card already generated"; 
                        }
                        $data = array(
                            'card_no' => $card_no, 
                            'card_type' => $card_type
                            );
                        $response = array('status' => 200, 'message' => $msg, 'data' => $data);
                    } else if($resp['status'] == 201){
                        $response = array('status' => 201, 'message' => 'No user found');
                    }else if($resp['status'] == 204){
                        $response = array('status' => 201, 'message' => 'Payment failed, please try again');
                    } else {
                        $response = array('status' => 400, 'message' => 'Something went wrong');
                    }
                    
                }
                simple_json_output($response);
                
            }
        }
    }
    
    
}
