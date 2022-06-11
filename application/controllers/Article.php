<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Article extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
    }

	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
	
	
	
		public function article_list()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['category_id'] == "" || $params['user_id'] == ""  ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$category_id= $params['category_id'];
						$user_id= $params['user_id'];
		        		$resp = $this->ArticleModel->article_list($category_id,$user_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
	
		
		public function article_details()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == ""  ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {

						$user_id= $params['user_id'];
						$post_id= $params['post_id'];
		        		$resp = $this->ArticleModel->article_details($user_id,$post_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	

	public function article_like()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['article_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$article_id = $params['article_id'];
		        		$resp = $this->ArticleModel->article_like($user_id,$article_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	
		public function add_review()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['article_id'] == "" || $params['review'] == "" || $params['service'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$article_id = $params['article_id'];				
						$review = $params['review'];
						$service = $params['service']; 
		        		$resp = $this->ArticleModel->add_review($user_id,$article_id,$review,$service);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	public function review_list()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->ArticleModel->review_list($user_id,$post_id); 
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
		public function article_review_likes()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['comment_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$comment_id = $params['comment_id'];
		        		$resp = $this->ArticleModel->article_review_likes($user_id,$comment_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	
	
 public function article_views()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['article_id'] == "" ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
						$user_id = $params['user_id'];
						$article_id = $params['article_id']; 
		        		$resp = $this->ArticleModel->article_views($user_id,$article_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
		public function article_bookmark()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['article_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$article_id = $params['article_id'];
		        		$resp = $this->ArticleModel->article_bookmark($user_id,$article_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	
			public function related_article_list()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['article_id'] == "" || $params['category_id'] == "" || $params['user_id'] == ""  ) {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $article_id= $params['article_id'];
						$category_id= $params['category_id'];
						$user_id= $params['user_id'];
		        		$resp = $this->ArticleModel->related_article_list($article_id,$category_id,$user_id);
					}
					json_outputs($resp);
		        }
			}
		}
	}
	
	
	public function article_follow()
	{
	    $this->load->model('ArticleModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->ArticleModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->ArticleModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					if ($params['user_id'] == "" || $params['post_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter all fields');
					} else {
					    $user_id = $params['user_id'];
						$post_id = $params['post_id'];
		        		$resp = $this->ArticleModel->article_follow($user_id,$post_id);
					}
					simple_json_output($resp);
		        }
			}
		}
	}
	
	
	
}