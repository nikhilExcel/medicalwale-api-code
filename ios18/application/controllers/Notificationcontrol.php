<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notificationcontrol extends CI_Controller
{
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('LoginModel');
        $this->load->model('NotificationcontrolModel');
        
        $check_auth_client = $this->NotificationcontrolModel->check_auth_client();
        if ($check_auth_client != true) {
            die($this->output->get_output());
        }
        
    }
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    public function update_token_firebase()
    {
        $token    = $this->input->post('token');
        $response = $this->NotificationcontrolModel->update_token_firebase($token);
        simple_json_output($response);
    }
    
    public function send_text_notification()
    {
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
                    if ($params['web_token'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $web_token = $params['web_token'];
                        $title     = $params['title'];
                        $message   = $params['message'];
                        $click_action       = $params['click_action'];
                        $resp      = $this->text_notification($title, $message, $web_token, 'https://sandboxapi.medicalwale.com/website5/assets1/icons/medicalwale.png', $click_action);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function text_notification($title, $msg, $reg_id, $img_url, $click_action)
    {
        define('API_ACCESS_KEY', 'AAAAYB0ZjOc:APA91bFbKu52dAoF-ZPaa2Bm-R6mS2lgbUDMejJKv7-bg3nZiQFlLsfJ50GAuJyPaFMOvaRCL_rDUWIk47R2n1RLjaEkufVdc6626TleJDyZh4mXvvp_TdrJBt82XGpIM-J3if9Pm2OW');
        $data = array(
            "to" => "$reg_id",
            "notification" => array(
                "title" => "$title",
                "body" => "$msg",
                "icon" => $img_url,
                "click_action" => $click_action
            )
        );
        $data_string = json_encode($data);
        $url = "https://fcm.googleapis.com/fcm/send";
        $headers = array(
            'Authorization: key=' . API_ACCESS_KEY,
            'Content-Type: application/json'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $result = curl_exec($ch);
        echo $result;
        curl_close($ch);
        
    }
    
    public function Notification_list()
    {
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
                        $resp    = $this->NotificationcontrolModel->Notification_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    
    public function Notification_update()
    {
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
                        $user_id  = $params['user_id'];
                        $category = $params['category'];
                        $status   = $params['status'];
                        $resp     = $this->NotificationcontrolModel->Notification_update($user_id, $category, $status);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_read_update()
    {
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
                        $type    = $params['type'];
                        $resp    = $this->NotificationcontrolModel->Notification_read_update($user_id, $noti_id,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_read_update_v1()
    {
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
                        $type    = $params['type'];
                        $resp    = $this->NotificationcontrolModel->Notification_read_update_v1($user_id, $noti_id, $type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_all_read()
    {
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
                        $type    = $params['type'];
                        $resp    = $this->NotificationcontrolModel->Notification_all_read($user_id, $noti_id, $type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    public function Notification_unread_count()
    {
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
                        $resp    = $this->NotificationcontrolModel->Notification_unread_count($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_All_list()
    {
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
                        if (array_key_exists("page", $params)) {
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        $resp = $this->NotificationcontrolModel->Notification_All_list($user_id, $page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_All_list_v1()
    {
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
                    if ($params['user_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        if (array_key_exists("page", $params)) {
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                        
                        $type = $params['type'];
                        $resp = $this->NotificationcontrolModel->Notification_All_list_v1($user_id, $page, $type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    // 	Web notification all for website by ghanshyam parihar starts
    public function Web_Notification_All_list()
    {
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
                        $resp    = $this->NotificationcontrolModel->Web_Notification_All_list($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    // 	Web notification all for website by ghanshyam parihar ends
    
    public function Notification_Delete_All_list()
    {
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
                        $id      = $params['id'];
                        $resp    = $this->NotificationcontrolModel->Notification_Delete_All_list($user_id, $id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function Notification_Delete_single_list()
    {
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
                    if ($params['user_id'] == "" || $params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $id      = $params['id'];
                        $resp    = $this->NotificationcontrolModel->Notification_Delete_single_list($user_id, $id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_Delete()
    {
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
                        $id      = $params['id'];
                        $type    = $params['type'];
                        $resp    = $this->NotificationcontrolModel->Notification_Delete($user_id, $id,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_Delete_v1()
    {
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
                        $id      = $params['noti_id'];
                        $type    = $params['type'];
                        $resp    = $this->NotificationcontrolModel->Notification_Delete_v1($user_id, $id, $type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function video_notification()
    {
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
                    if ($params['user_id'] == "" || $params['type'] == "" || $params['type_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter all fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $type    = $params['type'];
                        $type_id = $params['type_id'];
                        $resp    = $this->NotificationcontrolModel->video_notification($user_id, $type, $type_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
}