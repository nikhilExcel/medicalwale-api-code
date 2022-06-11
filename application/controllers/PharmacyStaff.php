<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PharmacyStaff extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PharmacyStaffModel');
        $this->load->model('LedgerModel');
    }   
    
    // staff listing by ghanshyam parihar 18 Feb 2020
    public function attendanceView()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_id'] == "" || $params['month'] == "" || $params['year'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_id   = $params['staff_id'];
                        $month   = $params['month'];
                        $year   = $params['year'];
                       
                        $resp         = $this->PharmacyStaffModel->attendanceView($user_id,$staff_id,$month,$year);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function addAttendanceSave()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['staff_id'] == "" || $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $staff_id           = $params['staff_id'];
                        $user_id            = $params['user_id'];
                        $month              = $params['month'];
                        $year               = $params['year'];
                        $date       = $params['date'];
                        
                        // $d1 = date("Y-m-d", $date); 
                        $check_in = $params['check_in'];
                        $check_out = $params['check_out'];
                        $duration = $params['duration'];
                        // print_r($duration); die;
                        $resp         = $this->PharmacyStaffModel->addAttendanceSave($staff_id,$user_id,$month,$year,$date,$check_in,$check_out,$duration);
                    }
                    otp_json_output($resp);
                }
            }
        }
    }
    
    public function searchattendanceList()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['id'] == "" || $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $id         = $params['id'];
                        $user_id    = $params['user_id'];
                        $month      = $params['month'];
                        $year       = $params['year'];
                        $date       = $params['date'];
                       
                        $resp         = $this->PharmacyStaffModel->searchattendanceList($id,$user_id,$month,$year,$date);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function deleteAttendance()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $id = $params['id'];
                       
                        $resp         = $this->PharmacyStaffModel->deleteAttendance($id);
                    }
                    otp_json_output($resp);
                }
            }
        }
    }
    
    public function checkout()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $id = $params['id'];
                       
                        $resp         = $this->PharmacyStaffModel->checkout($id);
                    }
                    otp_json_output($resp);
                }
            }
        }
    }
    
    public function checkin()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['staff_id'] == "" || $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $staff_id = $params['staff_id'];
                        $user_id = $params['user_id'];
                       
                        $resp         = $this->PharmacyStaffModel->checkin($staff_id,$user_id);
                    }
                    otp_json_output($resp);
                }
            }
        }
    }
    
    public function editAttendanceSave()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['check_in'] == "" || $params['check_out'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $id   = $params['id'];
                        $check_in   = $params['check_in'];
                        $check_out   = $params['check_out'];
                        $duration   = $params['duration'];
                        
                       
                        $resp         = $this->PharmacyStaffModel->editAttendanceSave($id,$check_in,$check_out,$duration);
                    }
                    otp_json_output($resp);
                }
            }
        }
    }
    
    public function attendanceList()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_id   = $params['staff_id'];
                       
                        $resp         = $this->PharmacyStaffModel->attendanceList($user_id,$staff_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function salaryList()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['month'] == "" || $params['year'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $month      = $params['month'];
                        $year       = $params['year'];
                       
                        $resp         = $this->PharmacyStaffModel->salaryList($user_id,$month,$year);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function staffList()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->staffList($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    // staff listing by ghanshyam parihar 18 Feb 2020
    
    public function index()
    {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }
    
    public function signup()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['category'] == "" || $params['name'] == "" || $params['email'] == "" || $params['phone'] == "" || $params['city'] == "") { 
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $category = $params['category'];
                        $name     = $params['name'];
                        $email    = $params['email'];
                        $city     = $params['city'];
                        $phone    = $params['phone'];
                        $token    = $params['token'];
                        $agent    = $params['agent'];
                        $resp     = $this->PharmacyPartnerModel->signup($category, $name, $email, $city, $phone, $token, $agent);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function sendotp()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter fields'
                    );
                } else {
                    $phone = $params['phone'];
                    $resp  = $this->PharmacyPartnerModel->sendotp($phone);
                }
                otp_json_output($resp);
            }
        }
    }
    
    public function login()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $phone = $params['phone'];
                    $token = $params['token'];
                    $agent = $params['agent'];
                   
                    $res   = $this->PharmacyPartnerModel->login($phone, $token, $agent);
                }
                simple_json_output($res);
            }
        }
    }   
    public function login_v1()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $phone = $params['phone'];
                    $token = $params['token'];
                    $agent = $params['agent'];
                    $password = $params['password'];
                    $res   = $this->PharmacyPartnerModel->login_v2($phone, $token, $agent,$password);
                }
                simple_json_output($res);
            }
        }
    }
    
    
      public function set_password()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "" ) {
                    $res = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                }
                elseif ($params['password'] == "" ) {
                    $res = array(
                        'status' => 400,
                        'message' => 'please enter password'
                    );
                }else {
                    
                    $phone = $params['phone'];
                    $password = $params['password'];
                    $cpassword = $params['cpassword'];
                    if($password==$cpassword)
                    {
                    $res   = $this->PharmacyPartnerModel->set_password($phone, $password,$cpassword);
                    }
                    else
                    {
                        $res = array(
                        'status' => 400,
                        'message' => 'Password Not Match'
                    );
                    }
                }
                simple_json_output($res);
            }
        }
    } 
     public function forget_sendotp()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['phone'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter fields'
                    );
                } else {
                    $phone = $params['phone'];
                    $resp  = $this->PharmacyPartnerModel->forget_sendotp($phone);
                }
                otp_json_output($resp);
            }
        }
    }
    public function update_registration_token()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['listing_id'] == "" || $params['token'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $listing_id = $params['listing_id'];
                    $token      = $params['token'];
                    $res        = $this->PharmacyPartnerModel->update_registration_token($listing_id, $token);
                }
                simple_json_output($res);
            }
        }
    }
    
    public function order_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $listing_id   = $params['listing_id'];
                        $listing_type = $params['listing_type'];
                        $order_type   = $params['order_type'];
                        
                        if(array_key_exists('order_type',$params)){
                            $order_type   = $params['order_type'];
                        } else {
                            $order_type="";
                        }
                        
                        if(isset($params['status']))
                        {
                            $status = $params['status'];
                        }
                        else
                        {
                            $status= "";
                        }
                        
                        if(isset($params['page']))
                        {
                            $page   = $params['page'];
                        }
                        else
                        {
                            $page= "";
                        }
                        
                        if(isset($params['find']))
                        {
                            $find   = $params['find'];
                        }
                        else
                        {
                            $find="";
                        }
                        
                        if(isset($params['date_from']))
                        {
                            $date_from = $params['date_from'];
                        }
                        else
                        {
                            $date_from="";
                        }
                        
                        if(isset($params['date_to']))
                        {
                            $date_to = $params['date_to'];
                        }
                        else
                        {
                            $date_to="";
                        }
                        
                       /// echo 'check';
                        $resp         = $this->PharmacyPartnerModel->order_list($status, $listing_id, $listing_type, $order_type, $page, $find, $date_from, $date_to);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function order_status_count()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ( $params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                       // $status    = $params['status'];
                        $user_id   = $params['user_id'];
                       /// echo 'check';
                        $resp         = $this->PharmacyPartnerModel->order_status_count($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
      public function order_list_search()
    {
        $this->load->model('PharmacyPartnerModel');
        $this->load->library('ElasticSearch');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        $listing_id   = $params['listing_id'];
                        $listing_type = $params['listing_type'];
                        $order_type   = $params['order_type'];
                        
                        
                        
                        
                        if(isset($params['find']))
                        {
                            $find   = $params['find'];
                        }
                        else
                        {
                            $find="";
                        }
                        
                      
                        
                       /// echo 'check';
                        $resp         = $this->PharmacyPartnerModel->order_list_search( $listing_id, $listing_type, $order_type, $find);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    public function order_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['invoice_no'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $invoice_no   = $params['invoice_no'];
                        $resp         = $this->PharmacyPartnerModel->order_details($invoice_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function order_list_v2()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id   = $params['listing_id'];
                        $listing_type = $params['listing_type'];
                        $order_type   = $params['order_type'];
                        $page         = $params['page'];
                        $keyword      = $params['keyword']; 
                        
                       /// echo 'check';
                        $resp         = $this->PharmacyPartnerModel->order_list_v2($listing_id, $listing_type, $order_type,$page,$keyword);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function awaiting_order_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['listing_type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id   = $params['listing_id'];
                        $listing_type = $params['listing_type'];
                        $order_type   = $params['order_type'];
                       /// echo 'check';
                        $resp         = $this->PharmacyPartnerModel->awaiting_order_list($listing_id, $listing_type, $order_type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
  /*  public function order_status()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $order_   = json_decode(file_get_contents('php://input'), TRUE);
                    
                 
               
                     if(count($order_['order']) > 0 &&  count($order_['prescription']) > 0)
                    {
                           $orders[] = $order_['order'];
                           foreach ($orders as $order_array) 
                           {
                            $order_id      = $order_array['order_id'];
                           }
                           
                           $orders1[] = $order_['prescription'];
                           foreach ($orders1 as $order_array1) 
                           {
                            $order_id1= $order_array1['order_id'];
                           }
                          if($order_id == "" && $order_id1== "") 
                          {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                          } 
                         else 
                          {
                            $resp = $this->PharmacyPartnerModel->order_status_common($orders, $orders1,$order_id,$order_id1);
                            simple_json_output($resp);
                          } 
                    
                    }
                    else if(count($order_['order']) > 0 &&  count($order_['prescription']) == 0)
                    {
                       $orders[] = $order_['order'];
                       foreach ($orders as $order_array) 
                       {
                            $order_id      = $order_array['order_id'];
                            $delivery_time = $order_array['delivery_time'];
                            $order_status  = $order_array['order_status'];
                            $listing_id    = $order_array['listing_id'];
                            $listing_type  = $order_array['listing_type'];
                            $order_data    = $order_array['product_order'];
                        }
                      if ($order_id == "") 
                       {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                       } 
                     else 
                      {
                        $resp = $this->PharmacyPartnerModel->order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data);
                        simple_json_output($resp);
                          
                      } 
                    }
                    else if (count($order_['order']) == 0 &&  count($order_['prescription']) > 0)
                    {
                        $orders[] = $order_['prescription'];
                        foreach ($orders as $order_array) 
                        {
                            $order_id           = $order_array['order_id'];
                            $order_status       = $order_array['order_status'];
                            $delivery_time      = $order_array['delivery_time'];
                            $prescription_order = $order_array['prescription_order'];
                        }
                        if ($order_id == "" && $order_status == "") 
                        {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                        } 
                        else 
                       {
                        $resp = $this->PharmacyPartnerModel->prescription_status($order_id, $order_status, $prescription_order, $delivery_time);
                        simple_json_output($resp);
                       } 
                    }
                  
                    
                    
                }
            }
        }
    }
    */
    
   public function order_status(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                  //  $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($this->input->post('user_id') == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $this->input->post('user_id');
                        $order_details = $this->input->post('order');
                       
                        $prescription_details = $this->input->post('prescription');
                        $delivery_charges = $this->input->post('delivery_charges');
                        $delivery_charges_by_customer = $this->input->post('delivery_charges_by_customer');
                        
                        $sub_total = $this->input->post('sub_total');
                        $discount = $this->input->post('discount');
                        $gst      = $this->input->post('gst');
                        $order_deliver_by      = $this->input->post('order_deliver_by');
                        if($order_deliver_by == ""){
                            $order_deliver_by == 'pharmacy';
                        } 
                        
                        if($delivery_charges == ""){
                            $delivery_charges = 0;
                        }
                        if($delivery_charges_by_customer == ""){
                            $delivery_charges_by_customer = 0;
                        }
                        
                         $product_details_new = json_decode($order_details,TRUE);
                         $prescription_details_new = json_decode($prescription_details,TRUE);
 
                       
                       if(!empty($product_details_new)){
                           $product_data_count = 1;
                       } else {
                           $product_data_count = 0;
                       }
                       
                       if(!empty($prescription_details_new)){
                           $prescription_data_count = 1;
                       } else {
                           $prescription_data_count = 0;
                       }
                       
                        // Both prescription and  general
                        /*  if( $order_details !="" &&  $prescription_details!="")*/
                        if($product_data_count > 0  && $prescription_data_count > 0 )
                             {
                                
                                 $order_id_data = $product_details_new['order_id']; 
                                 
                                
                                 $prescription_id_data = $prescription_details_new['order_id'];  
                          
                                  if($order_id_data == "" && $prescription_id_data== "") 
                                  {
                                    $resp = array(
                                        'status' => 400,
                                        'message' => 'please enter Order ID' 
                                    );
                                    simple_json_output($resp);
                                  }  
                                 else 
                                  {
                                    $resp = $this->PharmacyPartnerModel->order_status_common($order_id_data, $prescription_id_data,$order_details,$prescription_details,$delivery_charges,$delivery_charges_by_customer,$sub_total,$discount,$gst,$order_deliver_by);
                                    simple_json_output($resp);
                                  } 
                    
                        }
                    // Only general
                          else 
                          /*if($order_details !="" && $prescription_details=="")*/
                           if($product_data_count > 0  && $prescription_data_count == 0 )
                             {
                                 $product_details_new = json_decode($order_details,TRUE);
                                 $order_id      = $product_details_new['order_id'];
                                 $delivery_time = $product_details_new['delivery_time'];
                                 $order_status  = $product_details_new['order_status'];
                                 $listing_id    = $product_details_new['listing_id'];
                                 $listing_type  = $product_details_new['listing_type'];
                                 $order_data    = $product_details_new['product_order'];
                       
                      if ($order_id == "") 
                       {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                       } 
                     else 
                      {
                        $resp = $this->PharmacyPartnerModel->order_status($order_id, $delivery_time, $order_status, $listing_id, $listing_type, $order_data,$delivery_charges,$delivery_charges_by_customer,$sub_total,$discount,$gst,$order_deliver_by);
                        simple_json_output($resp);
                          
                      } 
                    }
                    // Only prescription
                    else
                    /*if ($order_details =="" && $prescription_details !="")*/
                     if($product_data_count == 0  && $prescription_data_count > 0 )
                    {
                        
                        $prescription_details_new = json_decode($prescription_details,TRUE);
                        // print_r($prescription_details_new);
                        // die;
                        $order_id           = $prescription_details_new['order_id'];
                        $order_status       = $prescription_details_new['order_status'];
                        $delivery_time      = $prescription_details_new['delivery_time'];
                        $prescription_order = $prescription_details_new['prescription_order'];
                        $listing_id         = $prescription_details_new['listing_id'];
                        if ($order_id == "") 
                        {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                            simple_json_output($resp);
                        } 
                        else 
                       {
                        $resp = $this->PharmacyPartnerModel->prescription_status($order_id, $order_status, $prescription_order, $delivery_time,$listing_id,$delivery_charges,$delivery_charges_by_customer,$sub_total,$discount,$gst,$order_deliver_by);
                        simple_json_output($resp);
                       } 
                    } 
                    }
                   
                  
                }
            }
        }
    }  
    
    
    
    
     public function add_address()
     { 
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
               
                if ($response['status'] == 200) {
                  
                  //  $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($this->input->post('name') == "" || $this->input->post('mobile') == "" || $this->input->post('listing_id')=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_name = $this->input->post('name');
                        $user_mobile = $this->input->post('mobile');
                        $dob = $this->input->post('dob');
                        $email = $this->input->post('email');
                        $gender = $this->input->post('gender');
                        $blood = $this->input->post('blood');
                        $address_details = $this->input->post('address');
                        $type=$this->input->post('type');
                        $listing_id=$this->input->post('listing_id');
                        $user_id=$this->input->post('user_id');
                        $resp = $this->PharmacyPartnerModel->add_address($user_name,$user_mobile,$dob,$email,$gender,$blood,$address_details,$type,$listing_id,$user_id);
                        simple_json_output($resp);
                          
                     
                   
                    }
                   
                  
                }
            }
        }
    }  
    
    public function prescription_status()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $order_   = json_decode(file_get_contents('php://input'), TRUE);
                    $orders[] = $order_['prescription'];
                    foreach ($orders as $order_array) {
                        $order_id           = $order_array['order_id'];
                        $order_status       = $order_array['order_status'];
                        $delivery_time      = $order_array['delivery_time'];
                        $prescription_order = $order_array['prescription_order'];
                    }
                    if ($order_id == "" && $order_status == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->prescription_status($order_id, $order_status, $prescription_order, $delivery_time);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
   public function list_address()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id   = $params['listing_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->list_address($listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function delete_address()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id   = $params['listing_id'];
                        $user_id   = $params['user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->delete_address($listing_id,$user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
  /* 
  old file 
  public function order_deliver_cancel()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $cancel_order      = json_decode(file_get_contents('php://input'), TRUE);
                    $order_id          = $cancel_order['order_id'];
                    $type              = $cancel_order['type'];
                    $notification_type = $cancel_order['notification_type'];
                    $cancel_reason     = $cancel_order['cancel_reason'];
                    if ($order_id == "" && $cancel_reason == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->order_deliver_cancel($order_id, $cancel_reason, $type, $notification_type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }*/

    
    //new file
     public function order_deliver_cancel()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $cancel_order      = json_decode(file_get_contents('php://input'), TRUE);
                    $invoice_id          = $cancel_order['invoice_id'];
                    $type              = $cancel_order['type'];
                    $notification_type = $cancel_order['notification_type'];
                    $cancel_reason     = $cancel_order['cancel_reason'];
                    if ($invoice_id == "" && $cancel_reason == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        
                        if (array_key_exists("user_id",$cancel_order))
                                   {
                                      $user_id    = $cancel_order['user_id'];
                                   }else
                                   {
                                   $user_id    = "";
                                    }
                        $resp = $this->PharmacyPartnerModel->order_deliver_cancel($invoice_id, $cancel_reason, $type, $notification_type,$user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function add_pharmacy()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $store_name         = $list['store_name'];
                    $store_manager_name = $list['store_manager_name'];
                    $store_since        = $list['store_since'];
                    $address_line1      = $list['address_line1'];
                    $address_line2      = $list['address_line2'];
                    $state              = $list['state'];
                    $city               = $list['city'];
                    $pincode            = $list['pincode'];
                    $latitude           = $list['latitude'];
                    $longitude          = $list['longitude'];
                    $listing_id         = $list['listing_id'];
                    
                    if ($store_name == "" && $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_pharmacy($store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $latitude, $longitude, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $resp       = $this->PharmacyPartnerModel->pharmacy_details($listing_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function partner_statistics()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "" || $params['type'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $listing_id = $params['listing_id'];
                        $type       = $params['type'];
                        $resp       = $this->PharmacyPartnerModel->partner_statistics($listing_id, $type);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function pharmacy_license_no()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list       = json_decode(file_get_contents('php://input'), TRUE);
                    $license_no = $list['license_no'];
                    $listing_id = $list['listing_id'];
                    if ($license_no == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_license_no($license_no, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_delivery_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $reach_area         = $list['reach_area'];
                    $day_night_delivery = $list['day_night_delivery'];
                    $free_start_time    = $list['free_start_time'];
                    $free_end_time      = $list['free_end_time'];
                    $days_closed        = $list['days_closed'];
                    $store_open         = $list['store_open'];
                    $store_close        = $list['store_close'];
                    $listing_id         = $list['listing_id'];
                    $is_24hrs_available = $list['is_24hrs_available'];
                    
                    if ($reach_area == "" || $free_start_time == "" || $free_end_time == "" ||  $is_24hrs_available == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_delivery_details($reach_area, $day_night_delivery, $free_start_time, $free_end_time, $days_closed, $store_open, $store_close, $is_24hrs_available, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_delivery_charges()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                      = json_decode(file_get_contents('php://input'), TRUE);
                    $min_order                 = $list['min_order'];  
                    $is_min_order_delivery     = $list['is_min_order_delivery'];
                    $min_order_delivery_charge = $list['min_order_delivery_charge'];
                    $night_delivery_charge     = $list['night_delivery_charge'];
                    $listing_id                = $list['listing_id'];
                    if ($min_order == "" || $min_order_delivery_charge == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_delivery_charges($min_order,$is_min_order_delivery, $min_order_delivery_charge, $night_delivery_charge, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_payment_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list         = json_decode(file_get_contents('php://input'), TRUE);
                    $payment_type = $list['payment_type'];
                    $listing_id   = $list['listing_id'];
                    if ($payment_type == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_payment_details($payment_type, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_partner_profile_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list       = json_decode(file_get_contents('php://input'), TRUE);
                    $listing_id = $list['listing_id'];
                    if ($listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_partner_profile_list($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_licence_pic()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $listing_id = $this->input->post('listing_id');  
            $license_no = $this->input->post('license_no');
            if ($license_no == "" || $listing_id == "" ) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {  
                
                $licence_pic_file = "";
                //unlink images
                
                if(!empty($_FILES["licence_pic"]["name"]))  {
                $file_query = $this->db->query("SELECT licence_pic FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();
                
                if ($get_file) {
                    $licence_pic = $get_file->licence_pic;
                        $file = "images/pharmacy_images/" . $licence_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                }
                //unlink images ends
            
                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['licence_pic']['name'];
                $img_size = $_FILES['licence_pic']['size'];
                $img_tmp  = $_FILES['licence_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $licence_pic_file  = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/pharmacy_images/' . $licence_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);                            
                        }
                    }
                } 
                }
                $resp = $this->PharmacyPartnerModel->pharmacy_licence_pic($listing_id, $license_no, $licence_pic_file);
            }            
            simple_json_output($resp);
        }
    }
    
     public function pharmacy_logo()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $listing_id = $this->input->post('listing_id');            
            if ($listing_id == "" || empty($_FILES["logo"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {                
                //unlink images
                $file_query = $this->db->query("SELECT logo FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();
                
                if ($get_file) {
                    $licence_pic = $get_file->logo;
                        $file = "images/pharmacy_images/" . $licence_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                }
                //unlink images ends

                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['logo']['name'];
                $img_size = $_FILES['logo']['size'];
                $img_tmp  = $_FILES['logo']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $licence_pic_file  = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/pharmacy_images/' . $licence_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);                            
                        }
                    }
                }   
                $resp = $this->PharmacyPartnerModel->pharmacy_logo($listing_id, $licence_pic_file);
            }            
            simple_json_output($resp);
        }
    }
    public function pharmacy_shop_establish_pic()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            
            $listing_id = $this->input->post('listing_id');
            
            if ($listing_id == "" || empty($_FILES["shop_establish_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {                
                //unlink images
                $file_query = $this->db->query("SELECT shop_establish_pic FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();                
                if ($get_file) {
                    $shop_establish_pic = $get_file->shop_establish_pic;
                        $file = "images/pharmacy_images/" . $shop_establish_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                }
                //unlink images ends    
                 $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                
                $img_name = $_FILES['shop_establish_pic']['name'];
                $img_size = $_FILES['shop_establish_pic']['size'];
                $img_tmp  = $_FILES['shop_establish_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $shop_establish_pic_file = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/pharmacy_images/' . $shop_establish_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            
                        }
                    }
                } 
                $resp = $this->PharmacyPartnerModel->pharmacy_shop_establish_pic($listing_id, $shop_establish_pic_file);
            }
            
            simple_json_output($resp);
        }
    }
    
    public function update_pharmacy()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $mobile             = $list['mobile'];
                    $store_name         = $list['store_name'];
                    $store_manager_name = $list['store_manager_name'];
                    $store_since        = $list['store_since'];
                    $address_line1      = $list['address_line1'];
                    $address_line2      = $list['address_line2'];
                    $state              = $list['state'];
                    $city               = $list['city'];
                    $pincode            = $list['pincode'];
                    $listing_id         = $list['listing_id'];
                    if ($mobile == "" && $store_name == "" && $store_manager_name == "" && $store_since == "" && $address_line1 == "" && $address_line2 == "" && $state == "" && $city == "" && $pincode == "" && $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->update_pharmacy($mobile, $store_name, $store_manager_name, $store_since, $address_line1, $address_line2, $state, $city, $pincode, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    
    public function pharmacy_profile_pic()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $listing_id = $this->input->post('listing_id');            
            if ($listing_id == "" || empty($_FILES["profile_pic"]["name"])) {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {
                //unlink images
                $file_query = $this->db->query("SELECT profile_pic FROM `medical_stores` WHERE  user_id='$listing_id'");
                $get_file   = $file_query->row();
                
                if ($get_file) {
                    $profile_pic = $get_file->profile_pic;                    
                        $file = "images/healthwall_avatar/".$profile_pic;
						@unlink(trim($file));
						DeleteFromToS3($file);
                    
                }                
                //unlink images ends  
       
                $img_format = array("jpg","png","gif","bmp","jpeg","PNG","JPG","JPEG","GIF","BMP");
                include('s3_config.php');
                $img_name = $_FILES['profile_pic']['name'];
                $img_size = $_FILES['profile_pic']['size'];
                $img_tmp  = $_FILES['profile_pic']['tmp_name'];
                $ext      = getExtension($img_name);
                if (strlen($img_name) > 0) {
                    if ($img_size < (50000 * 50000)) {
                        if (in_array($ext, $img_format)) {
                            $profile_pic_file  = uniqid() . date("YmdHis") . "." . $ext;
                            $actual_image_path = 'images/healthwall_avatar/' . $profile_pic_file;
                            $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                            
                        }
                    }
                }    
                $resp = $this->PharmacyPartnerModel->pharmacy_profile_pic($listing_id, $profile_pic_file);
            }
            
            simple_json_output($resp);
        }
    }
    
    public function pharmacy_is_approval()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list = json_decode(file_get_contents('php://input'), TRUE);
                    
                    $listing_id = $list['listing_id'];
                    if ($listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_is_approval($listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function pharmacy_lat_log()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list       = json_decode(file_get_contents('php://input'), TRUE);
                    $latitude   = $list['latitude'];
                    $longitude  = $list['longitude'];
                    $listing_id = $list['listing_id'];
                    if ($latitude == "" || $longitude == "" || $listing_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_lat_log($latitude, $longitude, $listing_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function partner_subcategory()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $category = $list['category'];
                    if ($category == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->partner_subcategory($category);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    //**************************************************Login Staff System***************************************************
    public function add_staff_member()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id            = $list['user_id'];
                    $mobile             = $list['mobile'];
                    $staff_name         = $list['staff_name'];
                    $staff_email        = $list['staff_email'];
                   
                    if ($mobile == "" && $staff_name == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_staff_member($user_id,$mobile, $staff_name, $staff_email);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function add_staff_member_v1()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $user_id        = $this->input->post('user_id');            
            $mobile         = $this->input->post('mobile');            
            $staff_name     = $this->input->post('staff_name');            
            $staff_email    = $this->input->post('staff_email');            
            
            if ($user_id == "" || $mobile == "" && $staff_name == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {      
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
               if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
               
                $resp = $this->PharmacyPartnerModel->add_staff_member_v1($user_id,$mobile, $staff_name, $staff_email, $profile, $media_id);
            }            
            simple_json_output($resp);
        }
    }
    
    //By Ghanshyam Parihar 17 Feb 2020 Starts
    
     public function add_staff_attendance()
    {
        
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $user_id        = $this->input->post('user_id');            
            $mobile         = $this->input->post('mobile');            
            $staff_name     = $this->input->post('staff_name');            
            $staff_email    = $this->input->post('staff_email');            
            
            $salary        = $this->input->post('salary');            
            $check_in         = $this->input->post('check_in');            
            $check_out     = $this->input->post('check_out');            
            $working_hours    = $this->input->post('working_hours'); 
            $weekly_off    = $this->input->post('weekly_off');
            
            if ($user_id == "" || $mobile == "" && $staff_name == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {      
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
               if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
               
                        $data = array(
                            'first_name' => $staff_name,
                            'email_id' => $staff_email,
                            'profie_picture'=>$profile,
                            'mobile_number'=>$mobile,
                            'date_of_joining' =>curr_date(),
                            'user_id'=>$user_id,
                            'is_active'=>1,
                            'salary' => $salary,
                            'check_in' => $check_in,
                            'check_out' => $check_out,
                            'working_hours' => $working_hours,
                            'weekly_off' => $weekly_off
                        );
                        
                           
               
                $resp = $this->PharmacyPartnerModel->add_staff_attendance($user_id,$mobile, $staff_name, $staff_email, $profile, $media_id,$data);
            }            
            simple_json_output($resp);
        }
    }
    
    //By Ghanshyam Parihar 17 Feb 2020 Ends
    
    public function edit_staff_member()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                   
                    $staff_id           = $list['staff_id'];
                    $mobile             = $list['mobile'];
                    $staff_name         = $list['staff_name'];
                    $staff_email        = $list['staff_email'];
                   
                    if ($mobile == "" && $staff_name == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->edit_staff_member($staff_id, $mobile, $staff_name, $staff_email);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function edit_staff_member_v1()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $user_id        = $this->input->post('staff_id');            
            $mobile         = $this->input->post('mobile');            
            $staff_name     = $this->input->post('staff_name');            
            $staff_email    = $this->input->post('staff_email');            
            
            if ($user_id == "" || $mobile == "" && $staff_name == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {      
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
               if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $query = $this->db->query("SELECT * FROM users WHERE id='$user_id'");
                            $count = $query->num_rows();
                            if ($count > 0) {
                                $media_id = $query->row()->avatar_id;
                            }
                            
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
               
                $resp = $this->PharmacyPartnerModel->edit_staff_member_v1($user_id,$mobile, $staff_name, $staff_email, $profile, $media_id);
            }            
            simple_json_output($resp);
        }
    }
    public function staff_member_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->staff_member_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function delete_staff_member()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $staff_user_id            = $list['staff_user_id'];
                  
                    if ($staff_user_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->delete_staff_member($staff_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    //end Login Flow
    public function add_admin_member_v1()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $user_id        = $this->input->post('user_id');            
            $mobile         = $this->input->post('mobile');            
            $staff_name     = $this->input->post('staff_name');            
            $staff_email    = $this->input->post('staff_email');            
            
            if ($user_id == "" || $mobile == "" && $staff_name == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {      
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
               if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $media_id = '0';
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
               
                $resp = $this->PharmacyPartnerModel->add_admin_member_v1($user_id,$mobile, $staff_name, $staff_email, $profile, $media_id);
            }            
            simple_json_output($resp);
        }
    }
    public function edit_admin_member_v1()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {            
            $user_id        = $this->input->post('staff_id');            
            $mobile         = $this->input->post('mobile');            
            $staff_name     = $this->input->post('staff_name');            
            $staff_email    = $this->input->post('staff_email');            
            
            if ($user_id == "" || $mobile == "" && $staff_name == "") {
                $resp = array(
                    'status' => 400,
                    'message' => 'please enter fields'
                );
            } else {      
                date_default_timezone_set('Asia/Kolkata');
                $updated_at = date('Y-m-d H:i:s');
               if (isset($_FILES["image"]) AND ! empty($_FILES["image"]["name"])) {
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        $img_name = $_FILES['image']['name'];
                        $img_size = $_FILES['image']['size'];
                        $img_tmp = $_FILES['image']['tmp_name'];
                        $ext = getExtension($img_name);
                        if (strlen($img_name) > 0) {
                            if ($img_size < (50000 * 50000)) {
                                if (in_array($ext, $img_format)) {
                                    $image = uniqid() . date("YmdHis") . "." . $ext;
                                    $actual_image_path = 'images/healthwall_avatar/' . $image;
                                    $s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ);
                                }
                            }
                        }
                    } else {

                        $image = '';
                    }
                    if ($image != '') {
                            $type = 'image';
                            $image_data = array(
                                'title' => $image,
                                'type' => $type,
                                'source' => $image,
                                'created_at' => $updated_at,
                                'updated_at' => $updated_at
                            );

                            $this->db->insert('media', $image_data);
                            $media_id = $this->db->insert_id();
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                        } else {
                            $query = $this->db->query("SELECT * FROM users WHERE id='$user_id'");
                            $count = $query->num_rows();
                            if ($count > 0) {
                                $media_id = $query->row()->avatar_id;
                            }
                            
                            $profile = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                        }
               
                $resp = $this->PharmacyPartnerModel->edit_admin_member_v1($user_id,$mobile, $staff_name, $staff_email, $profile, $media_id);
            }            
            simple_json_output($resp);
        }
    }
    public function admin_member_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->admin_member_list($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function delete_admin_member()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list               = json_decode(file_get_contents('php://input'), TRUE);
                    $staff_user_id            = $list['staff_user_id'];
                  
                    if ($staff_user_id == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->delete_admin_member($staff_user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     //**************************************************Login Staff System***************************************************
    public function inventory_product_type_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_product_type_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     public function inventory_category_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_category_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    public function inventory_distributor_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_distributor_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function inventory_rack_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_rack_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function add_inventory_distributor()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      = $list['user_id'];
                    $staff_user_id                = $list['staff_user_id'];
                    $distributor_name             =   $list['distributor_name'];
        	        $manufacturer_name            =   $list['manufacturer_name'];
        	        $distributor_phone            =   $list['distributor_phone'];
        	        $manufacturer_phone           =   $list['manufacturer_phone'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $distributor_name == "" && $distributor_phone =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_inventory_distributor($user_id,$staff_user_id, $distributor_name, $manufacturer_name , $distributor_phone, $manufacturer_phone, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_inventory_distributor()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      =   $list['user_id'];
                    $staff_user_id                =   $list['staff_user_id'];
                    $distributor_id               =   $list['distributor_id'];
                    $distributor_name             =   $list['distributor_name'];
        	        $manufacturer_name            =   $list['manufacturer_name'];
        	        $distributor_phone            =   $list['distributor_phone'];
        	        $manufacturer_phone           =   $list['manufacturer_phone'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $distributor_name == "" && $distributor_phone =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->edit_inventory_distributor($user_id,$staff_user_id,$distributor_id, $distributor_name, $manufacturer_name , $distributor_phone, $manufacturer_phone, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function inventory_warehouse_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                       
                        $resp         = $this->PharmacyPartnerModel->inventory_warehouse_list($user_id,$staff_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_inventory_warehouse()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      = $list['user_id'];
                    $staff_user_id                = $list['staff_user_id'];
                    $wname                        =   $list['wname'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $wname == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->add_inventory_warehouse($user_id,$staff_user_id, $wname, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function edit_inventory_warehouse()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list                         = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id                      =   $list['user_id'];
                    $staff_user_id                =   $list['staff_user_id'];
                    $warehouse_id                 =   $list['warehouse_id'];
                    $wname                        =   $list['wname'];
        	        $map_location                 =   $list['map_location'];
        	        $lat                          =   $list['lat'];
        	        $lng                          =   $list['lng'];
                    if ($user_id == "" && $staff_user_id == "" && $wname == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->edit_inventory_warehouse($user_id,$staff_user_id, $warehouse_id, $wname, $map_location, $lat, $lng);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
  
    //**************************************************Billing System***************************************************
    public function product_barcode_scanner()
    {
        // $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $barcode        = $list['barcode'];
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($barcode == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_barcode_scanner_v2($user_id,$hub_user_id,$barcode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function product_barcode_scanner_user_order()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $barcode        = $list['barcode'];
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($barcode == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_barcode_scanner_user_order($user_id,$hub_user_id,$barcode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function stock_availability()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $product_id        = $list['product_id'];
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    $quantity = $list['quantity'];
                    if ($product_id == "" || $user_id == "" || $quantity == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->stock_availability_v1($user_id,$hub_user_id,$product_id,$quantity);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    public function product_inventory_bill()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $this->input->post('user_id');
                    $hub_user_id    = $this->input->post('staff_user_id');
                    $date           = $this->input->post('date');
                    $invoice_no     = $this->input->post('invoice_no');
                    $product_details= $this->input->post('product_details');
                    $total_quantity = $this->input->post('total_quantity');
                    $total_price    = $this->input->post('total_price');
                    $discount       = $this->input->post('discount');
                    $tax            = $this->input->post('tax');
                    $net_amount     = $this->input->post('net_amount');
                    $payment_method = $this->input->post('payment_method');
                    $customer_name  = $this->input->post('customer_name');
                    $customer_phone = $this->input->post('customer_phone');
                    $customer_email = $this->input->post('customer_email');
                    $customer_address = $this->input->post('customer_address');
                    $doctor_name     = $this->input->post('doctor_name');
                    $bhc_no         = $this->input->post('bhc_no');
                    //count($product_details) <=0
                    
                if ($user_id == "" || $hub_user_id == "" || $date == "" || $invoice_no == ""   || $total_quantity == "" || $total_price == "" || $net_amount == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_inventory_bill($user_id,$hub_user_id,$date,$invoice_no,$product_details,$total_quantity,$total_price,$discount,$tax,$net_amount,$payment_method,$customer_name,$customer_phone,$customer_email,$customer_address,$doctor_name,$bhc_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
   
   
    public function upload_product_csv()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($this->input->post('user_id') == "" || $this->input->post('staff_user_id') == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $this->input->post('user_id');
                        $staff_user_id = $this->input->post('staff_user_id');
                        $file1 = $this->input->post('files');
                        $file   = $_FILES['files']['tmp_name'];
                        $resp         = $this->PharmacyPartnerModel->upload_product_csv($user_id,$staff_user_id,$file);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    public function order_on_call()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $delivery_by            = $this->input->post('delivery_by');
                    $service                = $this->input->post('service');
                    $delivery_time          = $this->input->post('delivery_time');
                    $delivery_charge        = $this->input->post('delivery_charge');
                    $listing_id             = $this->input->post('listing_id');
                    $listing_name           = $this->input->post('listing_name');
                    $staff_user_id          = $this->input->post('staff_user_id');
                    if($this->input->post('map_location')!=""){
                         $map_location           = $this->input->post('map_location');
                    }else{
                         $map_location           = "";
                    }
                   
                    
                    $product_details        = $this->input->post('product_details');
                    $total_quantity         = $this->input->post('total_quantity');
                    $total_price            = $this->input->post('total_price');
                    $discount               = $this->input->post('discount');
                    $net_amount             = $this->input->post('net_amount');
                    $gst                    = $this->input->post('gst');
                    
                    $payment_method         = $this->input->post('payment_id');
                    
                    if($this->input->post('address_id')!=""){
                         $address_id             = $this->input->post('address_id');
                    }else{
                         $address_id             = "";
                    }
                   
                    $customer_name          = $this->input->post('name');
                    $customer_phone         = $this->input->post('mobile');
                    $customer_email         = $this->input->post('email');
                    if($this->input->post('address1')!=""){
                        $address1               = $this->input->post('address1');
                    }
                    else{
                        $address1               = "";
                    }
                    if($this->input->post('address1')!=""){
                        $address2               = $this->input->post('address2');
                    }
                    else{
                        $address2               = "";
                    }
                    if($this->input->post('city')!=""){
                        $city                   = $this->input->post('city');
                    }else{
                        $city                   = "";
                    }
                   if($this->input->post('state')!=""){
                       $state                  = $this->input->post('state');
                   }else{
                       $state                  = "";
                   }
                    if($this->input->post('pincode')!=""){
                        $pincode                = $this->input->post('pincode');
                    }else{
                        $pincode                = "";
                    }
                    if($this->input->post('lat')!=""){
                    
                    $lat                    = $this->input->post('lat');
                    $lng                    = $this->input->post('lng');
                    }else{
                         $lat                    = "";
                    $lng                    = "";
                    }
                    $delivery_charges_by_customer = $this->input->post('delivery_charges_by_customer');
                    $doctor_name = $this->input->post('doctor_name');
                    //count($product_details) <=0
                    
                if ($delivery_by == "" || $listing_id == ""  || $customer_phone =="" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                       
                        $resp = $this->PharmacyPartnerModel->order_on_call($delivery_by,$service,$delivery_time,$delivery_charge,$listing_id,$listing_name,$staff_user_id,$product_details,$total_quantity,$total_price,$discount,$net_amount,$payment_method,$gst,$customer_name,$customer_phone,$customer_email,$address1,$address2,$city,$state,$pincode,$lat,$lng,$map_location,$address_id,$delivery_charges_by_customer,$doctor_name);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
  
    public function callNightOwls()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    // $delivery_by            = $this->input->post('delivery_by');
                    // $service                = $this->input->post('service');
                    // $delivery_time          = $this->input->post('delivery_time');
                    // $delivery_charge        = $this->input->post('delivery_charge');
                    // $listing_id             = $this->input->post('listing_id');
                    // $listing_name           = $this->input->post('listing_name');
                    // $staff_user_id          = $this->input->post('staff_user_id');
                    // $map_location           = $this->input->post('map_location');
                    
                    // $product_details        = $this->input->post('product_details');
                    // $total_quantity         = $this->input->post('total_quantity');
                    // $total_price            = $this->input->post('total_price');
                    // $discount               = $this->input->post('discount');
                    // $net_amount             = $this->input->post('net_amount');
                    // $gst                    = $this->input->post('gst');
                    
                    // $payment_method         = '';
                    
                    // $customer_name          = $this->input->post('name');
                    // $customer_phone         = $this->input->post('mobile');
                    // $customer_email         = $this->input->post('email');
                    // $address1               = $this->input->post('address1');
                    // $address2               = $this->input->post('address2');
                    // $city                   = $this->input->post('city');
                    // $state                  = $this->input->post('state');
                    // $pincode                = $this->input->post('pincode');
                    $order_id   = $this->input->post('order_id');
                    // $lat                    = $this->input->post('lat');
                    // $lng                    = $this->input->post('lng');
                    
                    //count($product_details) <=0
                    
                if ($order_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                       
                        $resp = $this->PharmacyPartnerModel->callNightOwls($order_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function monthly_report()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($user_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->monthly_report_v2($user_id,$hub_user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function get_customer_detail_mobile()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $mobile        = $list['mobile'];
                    //$hub_user_id    = $list['staff_user_id'];
                    if ($mobile == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->get_customer_detail_mobile($mobile);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function product_find()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    $find        = $list['find'];
                   
                    if ($user_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_find($user_id,$hub_user_id,$find);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    public function inventory_dashboard()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $from_date        = $list['from_date'];
                    $to_date          = $list['to_date'];
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($user_id == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->inventory_dashboard_v2($user_id,$hub_user_id,$from_date,$to_date);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function stock_inventory_dashboard()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    $date_from        = $list['date_from'];
                    $date_to        = $list['date_to'];
                    $page = $list['page'];
                    if ($user_id == "" || $date_from =="" || $date_to == "" || $page =="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->stock_inventory_dashboard_v1($user_id,$hub_user_id,$page,$date_from,$date_to);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function check_json()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                  //  $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $product_details        = $this->input->post('product_details');
                  
                   
                        $resp = $this->PharmacyPartnerModel->check_json($product_details);
                    
                   // json_outputs($resp);
                }
            }
        }
    }
     public function check_notification()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $list['user_id'];
                    $title          = $list['title'];
                    $msg            = $list['msg'];
                
                    if ($user_id == "" || $title =="" || $msg == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->check_notification($user_id,$title,$msg);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function booking_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['invoice_no'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id    = $params['user_id'];
                        $invoice_no = $params['invoice_no'];
                          
                        $resp       = $this->PharmacyPartnerModel->booking_details($user_id,$invoice_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function pharmacy_feedback()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $feedback        = $list['feedback'];
                    $email          = $list['email'];
                    $user_id        = $list['user_id'];
                 
                    if ($user_id == "" || $feedback=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->pharmacy_feedback($user_id,$feedback,$email); 
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
        public function How_to_use() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    // if ($params['user_id'] == "") {
                    //     $resp = array(
                    //         'status' => 400,
                    //         'message' => 'please enter fields'
                    //     );
                    // } else {
                    $user_id = $params['user_id'];
                    $resp = $this->PharmacyPartnerModel->How_to_use($user_id);
                    //  }
                    json_outputs($resp);
                }
            }
        }
    }
    public function get_user_ledger_details(){
	    
	  $this->load->model('PharmacyPartnerModel');
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
			json_output(400,array('status' => 400,'message' => 'Bad request.'));
		} else {
			$check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->PharmacyPartnerModel->auth();
		        if($response['status'] == 200){
					$params = json_decode(file_get_contents('php://input'), TRUE);
					//print_r($params);
					if ($params['user_id'] == "") {
						$resp = array('status' => 400,'message' =>  'please enter fields');
					} else {
					    $user_id        = $params['user_id'];

		        		$ledger_details           = $this->PharmacyPartnerModel->get_user_ledger_details($user_id);
					}
					    json_outputs($ledger_details);
				
		        }
			}
		}
	    
	}
	/* public function all_order_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['filter'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                        $date_from = $params['from_date'];
                        $date_to = $params['to_date'];
                        $filter =  $params['filter'];  // all, online,offline
                      
                        $amount_from = $params['amount_from'];
                        $amount_to = $params['amount_to'];
                        
                        $discount_from = $params['discount_from'];
                        $discount_to = $params['discount_to'];
                        $prescription = $params['prescription']; //order,prescription
                     
                        $resp         = $this->PharmacyPartnerModel->all_order_list($user_id,$staff_user_id,$date_from,$date_to,$filter,$amount_from,$amount_to,$discount_from,$discount_to,$prescription);
                    }
                    json_outputs($resp);
                }
            }
        }
    }*/
     public function inventory_stock_report_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "" || $params['filter']=="" || $params['page']=="") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $page = $params['page'];
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                        //------------------------------
                        //$find = $params['find']; 
                         if(array_key_exists("find",$params)){
                            $find= $params['find'];
                        } else {
                            $find= '';
                        }
                        //-----------------------------
                        $filter = $params['filter'];
                        $brand = $params['brand'];
                        $purchase_order = $params['purchase_order'];
                        $expiry_date_from = $params['expiry_date_from'];
                        $expiry_date_to = $params['expiry_date_to'];
                        $stock_limit = "";
                        $warehouse_id = $params['warehouse_id'];
                        $pid_sort = $params['pid_sort'];
                        $pname_sort = $params['pname_sort'];
                        
                        if($filter == "surplus" || $filter == "lowstock")
                        {
                            $stock_limit = $params['stock_limit'];
                            if($stock_limit == "")
                            {
                                 $resp = array(
                                        'status' => 400,
                                        'message' => 'please stock limit field'
                                    );
                            }
                            else
                            {
                                $resp        = $this->PharmacyPartnerModel->inventory_stock_report_list_v1($page,$user_id,$staff_user_id,$find,$filter,$stock_limit,$brand,$purchase_order,$expiry_date_from,$expiry_date_to,$warehouse_id,$pid_sort,$pname_sort);
                            }
                        }
                        else
                        {
                             $resp         = $this->PharmacyPartnerModel->inventory_stock_report_list_v1($page,$user_id,$staff_user_id,$find,$filter,$stock_limit,$brand,$purchase_order,$expiry_date_from,$expiry_date_to,$warehouse_id,$pid_sort,$pname_sort);
                        }
                        
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
     public function inventory_manufacturer_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['staff_user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id   = $params['user_id'];
                        $staff_user_id = $params['staff_user_id'];
                        $page = $params['page'];
                        $resp         = $this->PharmacyPartnerModel->inventory_manufacturer_list($user_id,$staff_user_id,$page);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
     public function buy_pbioms()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['user_id'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $user_id = $params['user_id'];
                    $admin  = $params['total_admin'];
                    $staff  = $params['total_staff'];
                    $total_amount = $params['total_amount'];
                    $gst = $params['gst'];
                    $res    = $this->PharmacyPartnerModel->buy_pbioms($user_id,$admin,$staff,$total_amount,$gst);
                }
                simple_json_output($res);
            }
        }
    }
    public function pbioms_booking_details()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $params = json_decode(file_get_contents('php://input'), TRUE);
                if ($params['user_id'] == "") {
                    $resp = array(
                        'status' => 400,
                        'message' => 'please enter phone no'
                    );
                } else {
                    $user_id = $params['user_id'];
                  
                    $res    = $this->PharmacyPartnerModel->pbioms_booking_details($user_id);
                }
                simple_json_output($res);
            }
        }
    }
     public function Notification_All_list() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
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
                        if(array_key_exists("page",$params)){
                            $page = $params['page'];
                        } else {
                            $page = '';
                        }
                       
                        $resp = $this->PharmacyPartnerModel->Notification_All_list($user_id,$page);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function Notification_All_list_v1() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['type']=="") {
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
                       
                        $type = $params['type'];
                        $resp = $this->PharmacyPartnerModel->Notification_All_list_v1($user_id,$page,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
     public function Notification_read_update() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
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
                        $resp = $this->PharmacyPartnerModel->Notification_read_update($user_id,$noti_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_read_update_v1() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
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
                        $type = $params['type'];
                        $resp = $this->PharmacyPartnerModel->Notification_read_update_v1($user_id,$noti_id,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
      public function Notification_Delete() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
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
                        $id  = $params['id'];
                        $resp = $this->PharmacyPartnerModel->Notification_Delete($user_id,$id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    public function Notification_Delete_v1() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
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
                        $id  = $params['noti_id'];
                        $type  = $params['type'];
                        $resp = $this->PharmacyPartnerModel->Notification_Delete_v1($user_id,$id,$type);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    // Added by swapnali on 16th aug 2019
    // check contact number or backtcard num,ber is present in system
    // if present then send user_details and address of user
    
    
    public function check_contact_barcode() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['number'] ==  ""){
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user_id and number'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $number  = $params['number'];
                        $res = $this->PharmacyPartnerModel->check_contact_barcode($user_id,$number);
                        
                        if($res['status'] == 1){
                            $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'data' =>  $res['data']
                            );
                        
                        }
                        else {
                           
                            $resp = array(
                                'status' => 400,
                                'message' => 'New user will be created'
                            );
                            
                        }
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    // add_order_info
    public function add_order_info() {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['number'] ==  ""){
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter user_id and number'
                        );
                    } else {
                        $vendor_id = $params['vendor_id'];
                        $user_id = $params['user_id'];
                        $name  = $params['name'];
                        $phone = $params['phone'];
                        $email = $params['email'];
                        $address1 = $params['address1'];
                        $address2  = $params['address2'];
                         $landmark = $params['landmark'];
                         $city = $params['city'];
                          $state  = $params['state'];
                         $pincode  = $params['pincode'];
                        $lat  = $params['lat'];
                        $lng = $params['lng'];
                        $order_type = $params['order_type'];
                        $medicine_count  = $params['medicine_count'];
                        $order_cost = $params['order_cost'];
                        
                        
                        
                        
                        
                        
                        $res = $this->PharmacyPartnerModel->add_order_info($vendor_id , $user_id , $name , $phone , $email , $address1 , $address2 , $landmark , $city , $state , $pincode , $lat , $lng , $order_type , $medicine_count , $order_cost);
                        
                        if($res['status'] == 1){
                            $resp = array(
                                'status' => 200,
                                'message' => 'success',
                                'data' =>  $res['data']
                            );
                        
                        }
                        else {
                           
                            $resp = array(
                                'status' => 400,
                                'message' => 'No user found'
                            );
                            
                        }
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    // Added by swapnali on 17th aug 2019
    //  some changes in product_inventory_bill api
    public function product_inventory_bill_v1(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   // $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $user_id        = $this->input->post('user_id');
                    $hub_user_id    = $this->input->post('staff_user_id');
                    $date           = $this->input->post('date');
                    $invoice_no     = $this->input->post('invoice_no');
                    $product_details= $this->input->post('product_details');
                    // $total_quantity = $this->input->post('total_quantity');
                    // $total_price    = $this->input->post('total_price');
                    $discount       = $this->input->post('discount');
                    // $tax            = $this->input->post('tax');
                    // $net_amount     = $this->input->post('net_amount');
                    $payment_method = $this->input->post('payment_method');
                    $customer_name  = $this->input->post('customer_name');
                    $customer_phone = $this->input->post('customer_phone');
                    $customer_email = $this->input->post('customer_email');
                    $customer_lat = $this->input->post('customer_lat');
                    $customer_lng = $this->input->post('customer_lng');
                    $customer_address = $this->input->post('customer_address');
                    $doctor_name     = $this->input->post('doctor_name');
                    $bhc_no         = $this->input->post('bhc_no');
                    //count($product_details) <=0
                    $product_details_new = json_decode($product_details,TRUE);
                    $total_products = count($product_details_new['product']);
                if ($user_id == "" || $hub_user_id == "" ||   $total_products < 1  || $payment_method == "" || $customer_name == "" || $customer_phone == "" ||$customer_lat == "" || $customer_lng == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                            'description' =>   'required fields are pharmacy id in user_id , statff user id in staff_user_id ,  in product_details add atleast 1 product under product array, payment_method, either add customer_name and  customer_phone or bhc_no, customer_lat , customer_lng'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_inventory_bill_v1($user_id,$hub_user_id,$date,$invoice_no,$product_details,$discount,$payment_method,$customer_name,$customer_phone,$customer_email,$customer_address,$doctor_name,$bhc_no,$customer_lat,$customer_lng);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    // book_mno
  
    // 21st aug 2019 by swapnali
    // estimate cost
    
      public function estimate_cost_mno(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                   
              //     print_r($params); die();
                  if(array_key_exists('user_id',$params)){
                      $user_id = $params['user_id'] ;
                  } else {
                      $user_id = "";
                  }
                   
                   if(array_key_exists('invoice_no',$params)){
                       $invoice_no = $params['invoice_no'];
                   } else {
                       $invoice_no = "";
                   }
                //   only_view : if 1 then dont updater user order else update in user order
                   if(array_key_exists('only_view',$params)){
                       $only_view = $params['only_view'];
                   } else {
                       $only_view = "";
                   }
                   
                    if ($user_id == "" || $invoice_no == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                            'description' =>   'Required fields are pharmacy id in user_id ,  invoice no in invoice_no '
                        );
                    } else {
                        $res = $this->PharmacyPartnerModel->estimate_cost_mno($user_id,$invoice_no,$bill="",$only_view);
                        $resp = array(
                            'status' => 200,
                            'message' => 'success',
                            'data' =>   $res
                        );
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    // 21st aug 2019 by swapnali
    // confirm cost
    
    public function responce_user_behalf(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                   if(array_key_exists('user_id',$params)){
                         $user_id = $params['user_id'];
                    } else {
                        $user_id = "";
                    }
                   
                    if(array_key_exists('invoice_no',$params)){
                         $invoice_no = $params['invoice_no'];
                    } else {
                        $invoice_no = "";
                    }
                   
                    if(array_key_exists('order_status',$params)){
                        $order_status = $type = $params['order_status'];
                    } else {
                        $order_status = $type = "";
                    }
                    
                    if(array_key_exists('cancel_reason',$params)){
                         $cancel_reason = $params['cancel_reason'];
                    } else {
                        $cancel_reason = "";
                    }
                    
                    if(array_key_exists('payment_method',$params)){
                         $payment_method = $params['payment_method'];
                    } else {
                        $payment_method = "";
                    }
                    
                    
                    if ($user_id == "" || $invoice_no == "" || $order_status == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                            'description' =>   'required fields are pharmacy id in user_id order_status and invoice_no'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->responce_user_behalf($invoice_no, $order_status, $cancel_reason, $payment_method);
                    }
                    simple_json_output($resp);
                }
            }
        }
    } 
    
    
      // 21st aug 2019 by swapnali
    // call nightowl
      public function call_mno(){
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                   $params = json_decode(file_get_contents('php://input'), TRUE);
                   
                   
                   print_r($params); die();
                   
                   
                   
                    $product_details_new = json_decode($product_details,TRUE);
                    $total_products = count($product_details_new['product']);
                if ($user_id == "" || $hub_user_id == "" ||  $invoice_no == ""  || $total_products < 1  || $payment_method == "" || (($customer_name == "" || $customer_phone == "") && $bhc_no == "" ||$customer_lat == "" || $customer_lng == "")) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields',
                            'description' =>   'required fields are pharmacy id in user_id , statff user id in staff_user_id , invoice no in invoice_no , in product_details add atleast 1 product under product array, payment_method, either add customer_name and  customer_phone or bhc_no, customer_lat , customer_lng'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->call_mno($user_id,$hub_user_id,$date,$invoice_no,$product_details,$discount,$payment_method,$customer_name,$customer_phone,$customer_email,$customer_address,$doctor_name,$bhc_no,$customer_lat,$customer_lng);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
      
    
    
     // END : Added by swapnali
     
     
     
     // CashCheque 27-08-2019 Start Dhaval
      public function cashcheque()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['amount'] == "" || $params['vendor_id'] == "" || $params['vendor_name'] == "" || $params['max_usage_day'] == "" ) { 
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $amount        = $params['amount'];
                        $vendor_id     = $params['vendor_id'];
                        $vendor_name   = $params['vendor_name'];
                        $max_usage_day = $params['max_usage_day'];
                        $min_order     = $params['min_order'];
                        $max_order     = $params['max_order'];
                        $expiry_day    = $params['expiry_day'];
                        $first_txn     = $params['first_txn'];
                        $save_type     = $params['save_type'];
                        $type          = $params['type'];
                        $id            = $params['id'];
                        $resp          = $this->PharmacyPartnerModel->cashcheque($amount, $vendor_id, $vendor_name, $max_usage_day, $min_order, $max_order, $expiry_day,$first_txn,$save_type,$type,$id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
      public function cashcheque_list()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['listing_id'] == "") { 
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $vendor_id     = $params['listing_id'];
                       
                        $resp          = $this->PharmacyPartnerModel->cashcheque_list($vendor_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
     
     
       public function cashcheque_delete()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['id'] == "" || $params['listing_id'] == "" ) { 
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                      
                        $vendor_id     = $params['listing_id'];
                        $id            = $params['id'];
                        $resp          = $this->PharmacyPartnerModel->cashcheque_delete($vendor_id,$id);
                    }
                    simple_json_output($resp);
                }
            }
        } 
    }
     
     // CashCheque 27-08-2019 End Dhaval
     
    //  order_tracking_mno by swapnali on 13th sept 2k19
    
     public function order_tracking_mno() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['invoice_no'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter user_id and invoice_no');
                    } else {
                        $user_id = $params['user_id']; 
                        $invoice_no = $params['invoice_no']; 
                        
                        $data =  array();
                        $res = $this->PharmacyPartnerModel->order_tracking_mno($user_id, $invoice_no);
                        $data = $res['data'];
                        if($res['status'] == 1){
                            $totalMsgs = sizeof($data['tracker']);
                            $latestMsg = $data['tracker'][$totalMsgs - 1];
                            $msg = $latestMsg['message'];
                            $resp = array('status' => 200, 'message' => $msg,  'data' => $data);
                        } else if($res['status'] == 2){
                            $resp = array('status' => 400, 'message' => 'No order found');
                        } else if($res['status'] == 3){
                            $resp = array('status' => 200, 'message' => 'Waiting for night owl to accept the order', 'data' => $data);
                        } else {
                            $resp = array('status' => 400, 'message' => 'something went wrong');
                        }
                    }
                   simple_json_output($resp);
                }
            }
        }
    }
    
    //  order_tracking_mno by swapnali on 13th sept 2k19 end 
    
    
    // get branches by pharmacy id -added by swapnali on 27th sept 2k19
    
    public function get_branches() {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->LoginModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    
                    if (array_key_exists('user_id',$params) != 1 || $params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter user_id');
                    } else {
                        $user_id = $params['user_id']; 
                        $data =  array();
                        $res = $this->PharmacyPartnerModel->get_branches($user_id);
                        if($res['status'] == 1){
                            $data = $res['data'];
                            $resp = array('status' => 200, 'message' => 'success',  'data' => $data);
                        } else  if($res['status'] == 2){
                            $resp = array('status' => 201, 'message' => 'No branches found');
                        }  else {
                            $resp = array('status' => 201, 'message' => 'something went wrong');
                        }
                    }
                   simple_json_output($resp);
                }
            }
        }
    }
    
    
    // payment_accepted
    public function payment_accepted() {
        
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
                
             //   print_r($params); die();
                if(array_key_exists('user_id',$params)){
                    $user_id = $params['user_id'];
                } else {
                    $user_id = "";
                }
                if(array_key_exists('amount',$params)){
                    $amount = $params['amount'];
                } else {
                    $amount = 0;
                }
                
                if(array_key_exists('invoice_no',$params)){
                    $invoice_no = $params['invoice_no'];
                } else {
                    $invoice_no = "";
                }
                
                if(array_key_exists('payment_id',$params)){
                    $payment_id = $params['payment_id'];
                } else {
                    $payment_id = "";
                }
                
                 if ($user_id == "" || $invoice_no == "") {
                    $response = array(
                        'status' => 400,
                        'message' => 'please enter user_id,  payment_id , amount and invoice_no '
                    );
                } else {
                    
                    $res = $this->PharmacyPartnerModel->payment_accepted($user_id,$invoice_no, $payment_id, $amount);
                    // print_r($res); die();
                    if($res['status'] == 1){
                        $response = array(
                            'status' => 200,
                            'message' => 'success'
                        );
                    }else if($res['status'] == 2){
                        $response = array(
                            'status' => 400,
                            'message' => 'Order not found'
                        );
                    } else if($res['status'] == 4){
                        $response = array(
                            'status' => 201,
                            'message' => 'please send sub_type id of payment method'
                        );
                    } else if($res['status'] == 5){
                        $response = array(
                            'status' => 400,
                            'message' => 'No payment method found'
                        );
                    } else {
                        $response = array(
                            'status' => 400,
                            'message' => 'Something went wrong, please try again'
                        );
                    }
                }
                simple_json_output($response);
            }
        }
    }
    
    public function generate_invoice()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['invoice_no'] == "" || $params['order_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $invoice_no   = $params['invoice_no'];
                        $order_id   = $params['order_id'];
                       /// echo 'check';
                        $resp         = $this->PharmacyPartnerModel->generate_invoice_v1($order_id,$invoice_no);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    public function product_barcode_scanner_v2()
    {
        $this->load->model('PharmacyPartnerModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $list     = json_decode(file_get_contents('php://input'), TRUE);
                    $barcode        = $list['barcode'];
                    $user_id        = $list['user_id'];
                    $hub_user_id    = $list['staff_user_id'];
                    if ($barcode == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $resp = $this->PharmacyPartnerModel->product_barcode_scanner_v2($user_id,$hub_user_id,$barcode);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
    
    public function product_list()
    {
        $this->load->model('PartnermnoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $resp = (object)[];
                    $params     = json_decode(file_get_contents('php://input'), TRUE);
                   if($params == ""){
                        $resp = array(
                            'status' => 400,
                            'message' => 'please send fields'
                        );
                        // simple_json_output($resp);
                   } else {
                        if(array_key_exists('user_id',$params)){
                                $user_id = $params['user_id'];
                            } else {
                               $user_id = '';
                            }
                        
                        if(array_key_exists('page_no',$params)){
                                $page_no = $params['page_no'];
                            } else {
                               $page_no = 1;
                            }
                            
                            
                        if(array_key_exists('per_page',$params)){
                                $per_page = $params['per_page'];
                            } else {
                               $per_page = 10;
                            }
                            
                        if(array_key_exists('search',$params)){
                            $search = $params['search'];
                        } else {
                           $search = "";
                        }    
                        
                        if ($user_id == "" || $page_no == "" || $per_page == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            $resp = $this->PharmacyPartnerModel->product_list($user_id,$page_no,$per_page,$search);
                            if($resp['status'] == 1){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success',
                                    'data' => $resp['products']
                                );
                            }  else if($resp['status'] == 2){
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'No product found'
                                );
                            } else if($resp['status'] == 3){
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'First add products'
                                );
                            } else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Something went wrong'
                                );
                            }
                        }
                    // simple_json_output($resp);
                   }
                    simple_json_output($resp);
                    
                }
            }
        }
    }

    public function edit_product()
    {
        $this->load->model('PartnermnoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $resp = (object)[];
                    $params     = json_decode(file_get_contents('php://input'), TRUE);
                   if($params == ""){
                        $resp = array(
                            'status' => 400,
                            'message' => 'please send fields'
                        );
                        // simple_json_output($resp);
                   } else {
                        if(array_key_exists('user_id',$params)){
                            $user_id = $params['user_id'];
                        } else {
                           $user_id = '';
                        }
                        
                        if(array_key_exists('product_id',$params)){
                            $product_id = $params['product_id'];
                        } else {
                           $product_id = '';
                        }
                        if(array_key_exists('expiry_date',$params)){
                            $expiry_date = $params['expiry_date'];
                        } else {
                           $expiry_date = '';
                        }
                        // optional fields
                        
                        if(array_key_exists('size',$params)){
                            $size = $params['size'];
                        } else {
                           $size = '';
                        }
                        
                        if(array_key_exists('ptr',$params)){
                            $ptr = $params['ptr'];
                        } else {
                           $ptr = '';
                        }
                        
                        if(array_key_exists('mrp',$params)){
                            $mrp = $params['mrp'];
                        } else {
                           $mrp = '';
                        }
                        
                        if(array_key_exists('selling_price',$params)){
                            $selling_price = $params['selling_price'];
                        } else {
                            $selling_price = '';
                        }
                        
                        if(array_key_exists('distributor_id',$params)){
                            $distributor_id = $params['distributor_id'];
                        } else {
                           $distributor_id = '';
                        }
                        
                        if(array_key_exists('status',$params)){
                            $status = $params['status'];
                        } else {
                           $status = '';
                        }
                        
                        if(array_key_exists('rack_id',$params)){
                            $rack_id = $params['rack_id'];
                        } else {
                           $rack_id = '';
                        }
                        
                        if(array_key_exists('quantity',$params)){
                            $quantity = $params['quantity'];
                        } else {
                           $quantity = '';
                        }
                        
                        if ($user_id == "" || $product_id == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter user_id and product_id'
                            );
                        } else {
                            $resp = $this->PharmacyPartnerModel->edit_product($user_id,$product_id, $size, $ptr, $mrp, $selling_price, $distributor_id, $status, $rack_id,$quantity,$expiry_date);
                            if($resp['status'] == 1){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'Successfully updated'
                                );
                            }  else if($resp['status'] == 2){
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Nothing to update'
                                );
                            }else if($resp['status'] == 3){
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'No product found'
                                );
                            } else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Something went wrong'
                                );
                            }
                        }
                    // simple_json_output($resp);
                   }
                    simple_json_output($resp);
                    
                }
            }
        }
    }     
    
    public function select_payment_options(){
        $this->load->model('PartnermnoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $resp = (object)[];
                    $params     = json_decode(file_get_contents('php://input'), TRUE);
                   if($params == ""){
                        $resp = array(
                            'status' => 400,
                            'message' => 'please send fields'
                        );
                        // simple_json_output($resp);
                   } else {
                        if(array_key_exists('user_id',$params)){
                            $user_id = $params['user_id'];
                        } else {
                           $user_id = '';
                        }
                        
                        if(array_key_exists('vendor_type',$params)){
                            $vendor_type = $params['vendor_type'];
                        } else {
                           $vendor_type = 13;
                        }
                       
                        
                        if ($user_id == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            $resp = $this->PharmacyPartnerModel->select_payment_options($user_id,$vendor_type);
                            if(sizeof($resp['data']) > 0){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success',
                                    'data' => $resp['data']
                                );
                            } else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Check internet connection'
                                );
                            }
                        }
                    // simple_json_output($resp);
                   }
                    simple_json_output($resp);
                    
                }
            }
        }
    }
    
    public function get_payment_info_by_id(){
        $this->load->model('PartnermnoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $resp = (object)[];
                    $params     = json_decode(file_get_contents('php://input'), TRUE);
                   if($params == ""){
                        $resp = array(
                            'status' => 400,
                            'message' => 'please send fields'
                        );
                        // simple_json_output($resp);
                   } else {
                        if(array_key_exists('user_id',$params)){
                            $user_id = $params['user_id'];
                        } else {
                           $user_id = '';
                        }
                       
                        if(array_key_exists('payment_id',$params)){
                            $payment_id = $params['payment_id'];
                        } else {
                           $payment_id = '';
                        }
                        
                        if ($user_id == "" || $payment_id == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            $res = $this->PharmacyPartnerModel->get_payment_info_by_id($user_id, $payment_id);
                            if($res['status'] == 1){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success',
                                    'data' => $res['data']
                                );
                            } else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'This payment method is not added yet'
                                );
                            }
                        }
                    // simple_json_output($resp);
                   }
                    simple_json_output($resp);
                    
                }
            }
        }
    }
    
    public function add_edit_payment_method(){
        $this->load->model('PartnermnoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $resp = (object)[];
                    // $params     = json_decode(file_get_contents('php://input'), TRUE);
                   
                        $payment_id = $this->input->post('payment_id');
                        $phone = $this->input->post('phone');
                        $user_id = $this->input->post('user_id');
                        
                        if ($user_id == "" || $payment_id == "" ){
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            $resp = $this->PharmacyPartnerModel->add_edit_payment_method($payment_id, $phone, $user_id);
                            if($resp['status'] == 1 ){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success'
                                );
                            }  else if($resp['status'] == 2){
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Please send child payment method'
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
    }
    
    public function create_new_table(){
        $this->load->model('PartnermnoModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->PharmacyPartnerModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->PharmacyPartnerModel->auth();
                if ($response['status'] == 200) {
                    $resp = (object)[];
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        if(array_key_exists('user_id',$params)){
                            $user_id = $params['user_id'];
                        } else {
                            $user_id = "";
                        }
                        
                        
                        if ($user_id == ""){
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            $resp = $this->PharmacyPartnerModel->create_new_table($user_id);
                            if($resp['status'] == 1 ){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success',
                                    'description' => 'Already exists',
                                    'table_name' => $resp['table_name']
                                    
                                );
                            }  else if($resp['status'] == 2){
                                $resp = array(
                                    'status' => 200,
                                    'message' => 'success',
                                    'description' => 'Created new table',
                                    'table_name' => $resp['table_name']
                                );
                            }  else {
                                $resp = array(
                                    'status' => 400,
                                    'message' => 'Could not create table'
                                );
                            }
                        }
                   
                    simple_json_output($resp);
                }
            }
        }
    }
}
