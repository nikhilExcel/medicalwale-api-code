<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pharmacy extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
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
	
	public function pharmacy_list()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['lat'] == "" || $params['lng'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$lat = $params['lat'];
						$lng = $params['lng'];
		        		$resp = $this->PharmacyModel->pharmacy_list($user_id,$lat,$lng);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
		
	public function pharmacy_details()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
					
		        		$resp = $this->PharmacyModel->pharmacy_details($user_id,$listing_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function category_list()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
		        	$resp = $this->PharmacyModel->category_list();
	    			json_outputs($resp);
		        }
			}
		}
	}
	
	
	
		public function sub_category()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['category_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$category_id = $params['category_id'];
		        		$resp = $this->PharmacyModel->sub_category($category_id);
					}
										
					if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
					
		        }
			}
		}
	}
	
	
		
		public function product_list()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['sub_category_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$sub_category_id= $params['sub_category_id'];
		        		$resp = $this->PharmacyModel->product_list($sub_category_id);
					}
					
					if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
		        }
			}
		}
	}
	




	public function product_search()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['keyword'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$keyword= $params['keyword'];
		        		$resp = $this->PharmacyModel->product_search($keyword);
					}
					
					if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
		        }
			}
		}
	}
	

		public function cart_order()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['address_id'] == "" || $params['medical_id'] == ""  || $params['product_id'] == ""  || $params['product_quantity'] == "" || $params['product_price'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$address_id = $params['address_id'];
						$medical_id= $params['medical_id'];
						$payType= $params['payType'];
						$product_id= $params['product_id'];
						$product_quantity=$params['product_quantity'];
						$product_price= $params['product_price'];
		        		$resp = $this->PharmacyModel->cart_order($user_id,$address_id,$medical_id,$payType,$product_id,$product_quantity,$product_price);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
		
	public function cart_order_list()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id= $params['user_id'];
		        		$resp = $this->PharmacyModel->cart_order_list($user_id);
					}
					
					if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
		        }
			}
		}
	}
	
		
		
	public function cart_order_details()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['order_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id= $params['user_id'];
						$order_id= $params['order_id'];
		        		$resp = $this->PharmacyModel->cart_order_details($user_id,$order_id);
					}
				    if($resp!='') { json_outputs($resp); }
					else { json_outputs_not_found($resp); }
		        }
			}
		}
	}
	

	

	public function add_review()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
						$rating = $params['rating'];
						$review = $params['review'];
						$service = $params['service']; 
		        		$resp = $this->PharmacyModel->add_review($user_id,$listing_id,$rating,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_list()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->PharmacyModel->review_list($user_id,$listing_id); 
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function review_comment_list()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->PharmacyModel->review_comment_list($user_id,$post_id); 
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function review_like()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->PharmacyModel->review_like($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
    public function review_comment()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
		        		$resp = $this->PharmacyModel->review_comment($user_id,$post_id,$comment);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function review_comment_like()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->PharmacyModel->review_comment_like($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	
  public function pharmacy_view()
	{
	    $this->load->model('PharmacyModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['listing_id'] == "" || $params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $listing_id = $params['listing_id'];
					    $user_id = $params['user_id'];
		        		$resp = $this->PharmacyModel->pharmacy_view($listing_id,$user_id);
					}
						 simple_json_output($resp); 
		        }
			}
		}
	}
	
	
	
	
}
