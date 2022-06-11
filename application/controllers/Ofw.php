<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ofw extends CI_Controller {

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
	
	public function home_remedies()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" && $params['user_id'] == "category_id") {
						$resp = array('status' => 400,'message' =>  'user id not blank');
					} else {
					    $user_id = $params['user_id'];
					    $category_id = $params['category_id'];
		        		$resp = $this->OfwModel->home_remedies($user_id,$category_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	public function home_remedies_likes()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['home_remedies_id'] == "") {
						$resp = array('status' => 400,'message' =>  'user id not blank');
					} else {
					    $user_id = $params['user_id'];
					    $home_remedies_id = $params['home_remedies_id'];
		        		$resp = $this->OfwModel->home_remedies_likes($user_id,$home_remedies_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	public function home_remedies_bookmark()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['home_remedies_id'] == "") {
						$resp = array('status' => 400,'message' =>  'user id not blank');
					} else {
					    $user_id = $params['user_id'];
					    $home_remedies_id = $params['home_remedies_id'];
		        		$resp = $this->OfwModel->home_remedies_bookmark($user_id,$home_remedies_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	
	public function ask_saheli_character()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					   
		        	$resp = $this->OfwModel->ask_saheli_character();
				    json_outputs($resp); 
		        }
			}
		}
	}
	
	
	
		
	public function ask_saheli_post_category()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					   
		        	$resp = $this->OfwModel->ask_saheli_post_category();
				    json_outputs($resp); 
		        }
			}
		}
	}
	
	public function ask_saheli_post_list()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['saheli_category'] == "" || $params['page'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$saheli_category = $params['saheli_category'];
						$page = $params['page'];
		        		$resp = $this->OfwModel->ask_saheli_post_list($user_id,$saheli_category,$page); 
					}
					json_healthwall($resp);
		        }
			}
		}
	}
	
	
	
		public function ask_saheli_post_details()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->OfwModel->ask_saheli_post_details($user_id,$post_id); 
					}
					json_healthwall($resp);
		        }
			}
		}
	}
	
	
		public function  ask_saheli_post_like()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];	
						$user_name = $params['user_name'];	
						$user_image = $params['user_image'];
                        $post_user_id = $params['post_user_id'];
						
		        		$resp = $this->OfwModel-> ask_saheli_post_like($user_id,$post_id,$user_name,$user_image,$post_user_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
		
    public function ask_saheli_post_comment()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "" || $params['comment'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
						$comment = $params['comment'];
						$user_name = $params['user_name'];	
						$user_image = $params['user_image'];
						$post_user_id = $params['post_user_id'];


		        		$resp = $this->OfwModel->ask_saheli_post_comment($user_id,$post_id,$comment,$user_name,$user_image,$post_user_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
		
	public function ask_saheli_post_comment_list()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->OfwModel->ask_saheli_post_comment_list($user_id,$post_id); 
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
	
	public function ask_saheli_post_comment_like()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
						$user_name = $params['user_name'];	
						$user_image = $params['user_image'];
						$post_id = $params['post_id'];
						$comment_user_id = $params['comment_user_id'];
					
		        		$resp = $this->OfwModel->ask_saheli_post_comment_like($user_id,$comment_id,$user_name,$user_image,$post_id,$comment_user_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	
		public function ask_saheli_add_question()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['user_name'] == "" || $params['question'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$user_name = $params['user_name'];
						$user_image = $params['user_image'];
						$question = $params['question'];
						$category = $params['category'];
						$saheli_category = $params['saheli_category'];
						$post_location = $params['post_location'];
		        		$resp = $this->OfwModel->ask_saheli_add_question($user_id,$user_name,$user_image,$question,$category,$post_location,$saheli_category);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function ask_saheli_user_like_list()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					
						$post_id = $params['post_id'];	
					
		        		$resp = $this->OfwModel->ask_saheli_user_like_list($post_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
	
		
	public function ask_saheli_video_views()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['media_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					
						$user_id = $params['user_id'];	
						$media_id = $params['media_id'];	
					
		        		$resp = $this->OfwModel->ask_saheli_video_views($user_id,$media_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
		
	public function ask_saheli_post_views()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					
						$user_id = $params['user_id'];	
						$post_id = $params['post_id'];	
					
		        		$resp = $this->OfwModel->ask_saheli_post_views($user_id,$post_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
   public function ask_saheli_follow_post()
 	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->OfwModel->ask_saheli_follow_post($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
   public function ask_saheli_user_update()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['user_name'] == "" || $params['user_image'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$user_name = $params['user_name'];
						$user_image = $params['user_image'];

		        		$resp = $this->OfwModel->ask_saheli_user_update($user_id,$user_name,$user_image);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
		
	
	   public function ask_saheli_user_check()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];

		        	$resp = $this->OfwModel->ask_saheli_user_check($user_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
		public function ask_saheli_post_hide()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->OfwModel->ask_saheli_post_hide($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
		public function ask_saheli_post_delete()
	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->OfwModel->ask_saheli_post_delete($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function ask_saheli_edit_question()	{
	    $this->load->model('OfwModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->OfwModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->OfwModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['post_id'] == "" || $params['user_id'] == "" || $params['question'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $post_id = $params['post_id'];
					    $user_id = $params['user_id'];
						$user_name = $params['user_name'];
						$user_image = $params['user_image'];
						$question = $params['question'];
						$category = $params['category'];
						$saheli_category = $params['saheli_category'];
		        		$resp = $this->OfwModel->ask_saheli_edit_question($post_id,$user_id,$user_name,$user_image,$question,$category,$saheli_category);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	

    

    
	
}
