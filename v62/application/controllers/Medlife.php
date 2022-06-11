<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Medlife extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
         $this->load->model('MedlifeModel');
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
	
	
	public function medlife_order_callback1()
	{
	  
	    	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->MedlifeModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->LoginModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['orderId'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $orderId        = $params['orderId'];
					    $orderState     = $params['orderState'];
					    $estimatedDeliveryDate = $params['estimatedDeliveryDate'];
					    $totalAmount         = $params['totalAmount'];
					    $payableAmount  = $params['payableAmount'];
					    $discountAmount      = $params['discountAmount'];
					    $createdTime     = $params['createdTime'];
					    $prepayments    = $params['prepayments'];
					    $settlements    = $params['settlements'];
					    $orderItems       = $params['orderItems'];
					    $deliveryCharge = $params['deliveryCharge'];
					   
					 $resp  = $this->MedlifeModel->medlife_order_callback($orderId, $orderState, $estimatedDeliveryDate, $totalAmount, $payableAmount, $discountAmount, $createdTime, $prepayments, $settlements, $orderItems, $deliveryCharge);
					}
					    simple_json_output($resp);
				
		        }
			}
		}
	}
	
		public function medlife_order_callback()
	{
	  
	    	$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
		//	$check_auth_client = $this->MedlifeModel->check_auth_client();
		//	if($check_auth_client == true){
		     //   $response = $this->LoginModel->auth();
		       // if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					/*print_r($params);
					die();*/
					if ($params['id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $orderId        = $params['id'];
					    $orderState     = $params['status'];
					    $estimatedDeliveryDate = $params['estimatedDeliveryDate'];
					    $totalAmount         = $params['totalAmount'];
					    $payableAmount  = $params['payableAmount'];
					    $discountAmount      = $params['discountAmount'];
					    $createdTime     = $params['createdTime'];
					     $deliveryCharge = $params['deliveryCharge'];
					    $prepayments    = $params['prepayments'];
					    $settlements    = $params['settlements'];
					    $orderItems       = $params['items'];
					    $rxId           = $params['rxId'];
					    $customerId     = $params['customerId'];
					    $deliveryTime   = $params['deliveryTime'];
					   // echo 'order items';
					   // print_r ($orderItems);
					    foreach ($orderItems as $row)
					    {
					        $quantity = $row['quantity'];
					        $amount   = $row['amount'];
					        $product  = $row['product'];
					        $mrp      = $row['mrp'];
					   
					    }
					   // echo 'quantity'.$quantity;
					   // echo 'amount'.$amount;
					   // echo 'product';
					   // print_r($product);
					   // echo 'mrp'.$mrp;
					   // echo 'rxid'.$rxId;
					   
					 $resp  = $this->MedlifeModel->medlife_order_callback($orderId, $orderState, $estimatedDeliveryDate, $totalAmount, $payableAmount, $discountAmount, $createdTime, $prepayments, $settlements, $orderItems, $deliveryCharge,$rxId,$deliveryTime);
					}
					    simple_json_output($resp);
				
		        //}
			}
		
	}
	
}