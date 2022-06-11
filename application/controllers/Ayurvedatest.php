<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ayurvedatest extends CI_Controller {

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
	
	
	public function ayurveda_list()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					 
		        		$resp = $this->Ayurvedatestmodel->ayurveda_list();
					    json_outputs($resp);
				
		        }
			}
		}
	}
	
	
	
	//listing_id is ayurveda's user id
		
	public function ayurveda_home()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $listing_id= $params['listing_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_home($listing_id);
					}
					    json_outputs($resp);
				
		        }
			}
		}
	}
	
	
	
	

	public function ayurveda_about_us()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
		            $params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "" ) {
						$resp = array('status' => 400,'message' =>  'User Id cant empty');
					} else {
					    $user_id = $params['user_id'];					
					    $listing_id = $params['listing_id'];	
		        	$resp = $this->Ayurvedatestmodel->ayurveda_about_us($user_id,$listing_id);
					}
	    			json_outputs($resp); 
		        }
			}
		}
	}
	
	public function ayurveda_contact_us()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['listing_id'] == "" || $params['user_id'] == "" || $params['name'] == "" || $params['message'] == "" || $params['mobile'] == "" ) {
						$resp = array('status' => 400,'message' =>  'User Id cant empty');
					} else {
					    $listing_id = $params['listing_id'];
					    $user_id = $params['user_id'];
					    $name = $params['name'];					
					    $message = $params['message'];					
					    $mobile = $params['mobile'];
					    
		        		$resp = $this->Ayurvedatestmodel->ayurveda_contact_us($listing_id,$user_id,$name,$message,$mobile);
					}
              simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function ayurveda_category()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
		        $params = json_decode(file_get_contents('php://input'), TRUE);
				if ($params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					$listing_id= $params['listing_id'];
		        	$resp = $this->Ayurvedatestmodel->ayurveda_category($listing_id);
	    	
		        }
		        json_outputs($resp);      
		        }
		       
			}
		}
	}
	public function ayurveda_subcategory()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['category_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $category_id = $params['category_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_subcategory($category_id);
					}
					    json_outputs($resp);
				
		        }
			}
		}
	}
	
	
	public function ayurveda_products()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['listing_id'] == "" || $params['category_id'] == "" || $params['sub_category_id'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $listing_id= $params['listing_id'];
					    $category_id = $params['category_id'];
					    $sub_category_id = $params['sub_category_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_products($listing_id,$category_id,$sub_category_id);
					}
					    json_outputs($resp);
				
		        }
			}
		}
	}
	
		public function ayurveda_related_products()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['product_id'] == "" || $params['listing_id'] == "" || $params['category_id'] == "" || $params['sub_category_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $product_id = $params['product_id'];
					    $listing_id= $params['listing_id'];
					    $category_id = $params['category_id'];
					    $sub_category_id = $params['sub_category_id'];

		        		$resp = $this->Ayurvedatestmodel->ayurveda_related_products($product_id,$listing_id,$category_id,$sub_category_id);
					}
					    json_outputs($resp);
				
		        }
			}
		}
	}

	public function ayurveda_get_quotes()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['pincode'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    	
					    $listing_id = $params['listing_id'];
					    $pincode = $params['pincode'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_get_quotes($listing_id,$pincode);
					}
              simple_json_output($resp);
		        }
			}
		}
	}
	
	public function ayurveda_pincode_check()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['pincode'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    	
					    $listing_id = $params['listing_id'];
					    $pincode = $params['pincode'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_pincode_check($listing_id,$pincode);
					}
              simple_json_output($resp);
		        }
			}
		}
	}
	



	public function ayurveda_product_review()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['listing_id'] == "" || $params['user_id'] == "" || $params['product_id'] == "" || $params['rating'] == "" || $params['review'] == "" || $params['service'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$listing_id = $params['listing_id'];
						$user_id = $params['user_id'];
						$product_id = $params['product_id'];
						$rating = $params['rating'];
						$review = $params['review'];
						$service = $params['service']; 
		        		$resp = $this->Ayurvedatestmodel->ayurveda_product_review($listing_id,$user_id,$product_id,$rating,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function ayurveda_product_review_list()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['product_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
						$product_id = $params['product_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_product_review_list($user_id,$product_id ); 
					}
					
					 json_outputs($resp);
		        }
			}
		}
	}
	
	public function ayurveda_product_review_likes()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_product_review_likes($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function ayurveda_product_review_comment()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_product_review_comment($user_id,$post_id,$comment);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function ayurveda_product_review_comment_like()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_product_review_comment_like($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function ayurveda_product_review_comment_list()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
					    $post_id = $params['post_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_product_review_comment_list($user_id,$post_id);
					}
						 json_outputs($resp); 
		        }
			}
		}
	}
	
	
	public function ayurveda_review()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
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
		        		$resp = $this->Ayurvedatestmodel->ayurveda_review($user_id,$listing_id,$rating,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function ayurveda_review_list()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['listing_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$listing_id = $params['listing_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_review_list($user_id,$listing_id); 
					}
					
					 json_outputs($resp);
		        }
			}
		}
	}
	
	public function ayurveda_review_likes()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_review_likes($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	 public function ayurveda_review_comment()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_review_comment($user_id,$post_id,$comment);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function ayurveda_review_comment_like()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_review_comment_like($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function ayurveda_review_comment_list()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
					    $post_id = $params['post_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_review_comment_list($user_id,$post_id);
					}
						 json_outputs($resp); 
		        }
			}
		}
	}
	
	
		public function ayurveda_view()
	{
	    $this->load->model('Ayurvedatestmodel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->Ayurvedatestmodel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Ayurvedatestmodel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['listing_id'] == "" || $params['user_id'] == "" || $params['product_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $ayurveda_id = $params['listing_id'];
					    $user_id = $params['user_id'];
					    $product_id = $params['product_id'];
		        		$resp = $this->Ayurvedatestmodel->ayurveda_view($ayurveda_id,$user_id,$product_id);
					}
						 simple_json_output($resp); 
		        }
			}
		}
	}
	
	
}