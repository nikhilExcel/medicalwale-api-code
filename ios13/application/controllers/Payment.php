<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Payment extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
         $this->load->model('PaymentModel');
          $this->load->model('LoginModel');
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
					    	$resp           = $this->PaymentModel->insert_payment_status($user_id, $listing_id, $trans_id, $status, $type, $order_id, $order_type, $amount, $status_mesg, $discount, $discount_rupee, $payment_type,$opd);
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
	
}