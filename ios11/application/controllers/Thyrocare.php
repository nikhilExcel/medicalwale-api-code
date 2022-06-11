<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Thyrocare extends CI_Controller {

    public function __construct() {
        parent::__construct();
         $this->load->model('LoginModel');
    }

    public function index() {
        json_output(400, array(
            'status' => 400,
            'message' => 'Bad request.'
        ));
    }

    public function add_order() {

        $this->load->model('ThyrocareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->ThyrocareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['lead_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {

                        $user_id = $params['user_id'];
                        $order_id = $params['reference_id'];
                        $user_name = $params['user_name'];
                        $schedule_date = $params['schedule_date'];
                        $device_type = $params['device_type'];
                        $lead_id = $params['lead_id'];

                        $data = array(
                            'user_id' => $user_id,
                            'order_id' => $order_id,
                            'user_name' => $user_name,
                            'schedule_date' => $schedule_date,
                            'device_type' => $device_type,
                            'lead_id' => $lead_id
                        );

                        $resp = $this->ThyrocareModel->add_order($data);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    
    
    
    
       public function fetch_thyrocare() {
        $this->load->model('ThyrocareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array('status' => 400, 'message' => 'Bad request.'));
        } else {
            $check_auth_client = $this->ThyrocareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array('status' => 400, 'message' => 'please enter fields');
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->ThyrocareModel->fetch_thyrocare($user_id);
                    }

                    json_outputs($resp);
                }
            }
        }
    }
    
    public function add_lab_booking_details() {

        $this->load->model('ThyrocareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->ThyrocareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['listing_id'] == "" || $params['package_id'] == "" || $params['branch_id'] == "")
                    
                    {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {

                        $user_id = $params['user_id'];
                        $patient_id = $params['patient_id'];
                        $listing_id = $params['listing_id'];
                        $vendor_type = $params['vendor_type'];
                        $branch_id = $params['branch_id'];
                        $branch_name = $params['branch_name'];
                        $at_home = $params['at_home'];
                        $address_line1 = $params['address_line1'];
                        $address_line2 = $params['address_line2'];
                        $city = $params['city'];
                        $state = $params['state'];
                        $mobile_no = $params['mobile_no'];
                        $pincode = $params['pincode'];
                        $email_id = $params['email_id'];
                        $address_id = $params['address_id'];
                        $test_id = $params['test_id'];
                        $package_id = $params['package_id'];
                        $booking_date = $params['booking_date'];
                        $booking_time = $params['booking_time'];
                        $booking_id = $params['booking_id'];

                        $data = array(
                            'user_id' => $user_id,
                            'patient_id' => $patient_id,
                            'listing_id' => $listing_id,
                            'vendor_type' => $vendor_type,
                            'branch_id' => $branch_id,
                            'branch_name' => $branch_name,
                            'at_home' => $at_home,
                            'address_line1' => $address_line1,
                            'address_line2' => $address_line2,
                            'city' => $city,
                            'state' => $state,
                            'mobile_no' => $mobile_no,
                            'pincode' => $pincode,
                            'email_id' => $email_id,
                            'address_id' => $address_id,
                            'test_id' => $test_id,
                            'package_id' => $package_id,
                            'booking_date' =>$booking_date,
                            'booking_time' => $booking_time,
                            'booking_id	' => $booking_id
                        );

                        $resp = $this->ThyrocareModel->add_lab_booking_details($data);
                    }
                    json_outputs($resp);
                }
            }
        }
    }
    
    
    

public function add_thyrocare() {

        $this->load->model('ThyrocareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->ThyrocareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['product_id'] == "" || $params['name'] == "" || $params['price'] == "")
                    
                    {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {

                        $user_id = $params['user_id'];
                        $product_id = $params['product_id'];
                        $name = $params['name'];
                        $price = $params['price'];
                        $test_name = $params['test_name'];
                        $type = $params['type'];
                        $margin = $params['margin'];
                       date_default_timezone_set('Asia/Kolkata');
                       $date = date('Y-m-d H:i:s');

                        $data = array(
                            'user_id' => $user_id,
                            'product_id' => $product_id,
                            'name' => $name,
                            'price' => $price,
                             'date' => $date,
                             'test_name'=>$test_name,
                             'type'=>$type,
                             'margin'=>$margin
                          
                        );

                        $resp = $this->ThyrocareModel->add_thyrocare($data);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }


public function delete_thyrocare_order() {

        $this->load->model('ThyrocareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->ThyrocareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" || $params['id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {

                        $user_id = $params['user_id'];
                        $id = $params['id'];
                      $resp = $this->ThyrocareModel->delete_thyrocare_order($user_id,$id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    
public function delete_thyrocare_cart() {

        $this->load->model('ThyrocareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->ThyrocareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "" ) {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {

                        $user_id = $params['user_id'];
                        
                      $resp = $this->ThyrocareModel->delete_thyrocare_cart($user_id);
                    }
                    simple_json_output($resp);
                }
            }
        }
    }
    

    public function list_orders() {

        $this->load->model('ThyrocareModel');
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            json_output(400, array(
                'status' => 400,
                'message' => 'Bad request.'
            ));
        } else {
            $check_auth_client = $this->ThyrocareModel->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->LoginModel->auth();
                if ($response['status'] == 200) {
                    $params = json_decode(file_get_contents('php://input'), TRUE);
                    if ($params['user_id'] == "") {
                        $resp = array(
                            'status' => 400,
                            'message' => 'please enter fields'
                        );
                    } else {
                        $user_id = $params['user_id'];
                        $resp = $this->ThyrocareModel->list_orders($user_id);
                    }
                    json_outputs($resp);
                }
            }
        }
    }

    
    
    public function cancel_order(){
        $this->load->model('ThyrocareModel');
        $method  = $_SERVER['REQUEST_METHOD'];
                if ($method != 'POST') {
                json_output(400, array(
                    'status' => 400,
                    'message' => 'Bad request.'
                ));
            } else {
                $check_auth_client = $this->ThyrocareModel->check_auth_client();
                if ($check_auth_client == true) {
                    $response = $this->LoginModel->auth();
                    if ($response['status'] == 200) {
                        $params = json_decode(file_get_contents('php://input'), TRUE);
                        if ($params['id'] == "") {
                            $resp = array(
                                'status' => 400,
                                'message' => 'please enter fields'
                            );
                        } else {
                            $id = $params['id'];
                            $resp = $this->ThyrocareModel->cancel_order($id);
                        }
                        simple_json_output($resp);
                    }
                }
            }

    }
    
    
}

?>