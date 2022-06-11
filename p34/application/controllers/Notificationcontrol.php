 
 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificationcontrol extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
       
         $this->load->model('NotificationcontrolModel');
        
        $check_auth_client = $this->NotificationcontrolModel->check_auth_client();
		if($check_auth_client != true){
			die($this->output->get_output());
		}
		
    }
	public function index()
	{
	    json_output(400,array('status' => 400,'message' => 'Bad request.'));
	}
    public function Notification_All_list() {
        $this->load->model('NotificationcontrolModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->NotificationcontrolModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        $resp = $this->NotificationcontrolModel->Notification_All_list($user_id,$page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_unread_count() {
        $this->load->model('NotificationcontrolModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->NotificationcontrolModel->check_auth_client();
            if ($check_auth_client == true) {
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->NotificationcontrolModel->Notification_unread_count($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
     public function Notification_read_update() {
        $this->load->model('NotificationcontrolModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->NotificationcontrolModel->check_auth_client();
            if ($check_auth_client == true) {
               $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['noti_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $noti_id = $params['noti_id'];
                        $resp = $this->NotificationcontrolModel->Notification_read_update($user_id,$noti_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
}