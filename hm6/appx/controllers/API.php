<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Api extends CI_Controller
{
    
    public function __construct($config = 'rest')
    {
        
        //  header('Access-Control-Allow-Origin: *'); 
        //  header("Access-Control-Allow-Credentials: true"); 
        //  header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS'); 
        parent::__construct($config);
         
        $this->load->model('MedicalMall_model');
        $this->load->model('Login_Model');
    }
    
    //start of me
    function get_num_ratings()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $pro_id = $this->input->post('pd_id');
                    if ($pro_id == "") {
                        $res = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_num_ratings($pro_id);
                    }
                    $res = array(
                        "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $result
                    );
                    
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($res));
                }
            }
        }
    }
    
    function get_similar_items()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $pro_id = $this->input->post('pd_id');
                    if ($pro_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_similar_items($pro_id);
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    }
                }
            }
        }
    }
    
      function update_cart()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id   = $this->input->post('user_id');
                    $cart_list = $this->input->post('cart_list');
                    
                    if ($user_id == 0 || $user_id == "" || $cart_list == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id, product_id, quantity and offer_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->update_cart($user_id, $cart_list);
                    }
                    
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    //end of me
    
    function get_adv_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => 400,
                "statuspic_root_code" => "Bad request"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->getallAdvertisements();
                    $count  = sizeof($result);
                    
                    $result = array(
                        "status" => 200,
                        "statuspic_root_code" => "Success",
                        "data" => $result
                    );
                    if ($count > 0) {
                        
                        
                        // $this->response(json_encode($categories), 200); 
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                    else {
                        
                        $result = array(
                            "status" => "false",
                            "message" => "Result not found"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                }
            }
        }
        
    }
    
    // 	getallAdvertisementsWebsite for website
    function get_adv_post_web()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->getallAdvertisementsWebsite();
                    $count  = $result->num_rows();
                    
                    $result = array(
                        "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $result->result_array()
                    );
                    if ($count > 0) {
                        
                        
                        // $this->response(json_encode($categories), 200); 
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                    else {
                        
                        $result = array(
                            "status" => "false",
                            "message" => "Result not found"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                }
            }
        }
        
    }
    function get_vendre_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $v_id = $this->input->post('v_id');
                    if ($v_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getallVendors($v_id);
                        
                        $count = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $result->result_array()
                        );
                        if ($count > 0) {
                            
                            
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function vendor_product_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $v_id = $this->input->post('v_id');
                    if ($v_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getallProductsForVendor($v_id);
                        
                        $count = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $result->result_array()
                        );
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
  
     function product_details_post1()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $pd_id   = $this->input->post('pd_id');
                    $user_id = $this->input->post('user_id');
                    if ($pd_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        //	     $recently_viewed = $this->MedicalMall_model->recently_viewed($pd_id,$user_id);
                        
                        $results = $this->MedicalMall_model->getProductForVendor1($pd_id, $user_id);
                        
                        $count = sizeof($results);
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $results
                        );
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    
      function product_details_post2(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $pd_id   = $this->input->post('pd_id');
                    $user_id = $this->input->post('user_id');
                    if ($pd_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        //	     $recently_viewed = $this->MedicalMall_model->recently_viewed($pd_id,$user_id);
                        
                        $results = $this->MedicalMall_model->getProductForVendor2($pd_id, $user_id);
                        
                        $count = sizeof($results);
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $results
                        );
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function product_details_post(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $pd_id   = $this->input->post('pd_id');
                    $user_id = $this->input->post('user_id');
                    if ($pd_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        //	     $recently_viewed = $this->MedicalMall_model->recently_viewed($pd_id,$user_id);
                        
                        $results = $this->MedicalMall_model->getProductForVendor($pd_id, $user_id);
                        
                        $count = sizeof($results);
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $results
                        );
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    
    
    
    function all_cat_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    
                    $result = $this->MedicalMall_model->getallCategoriesHealthMall();
                    $count  = sizeof($result);
                    
                    $result = array(
                        "status" => "true",
                        "statuspic_root_code" => "200",
                        "message" => "Categories Found",
                        "data" => $result
                    );
                    if ($count > 0) {
                        
                        
                        // $this->response(json_encode($categories), 200); 
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                    else {
                        
                        $result = array(
                            "status" => "false",
                            "message" => "Result not found"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                }
            }
        }
        
    }
    
    function all_sub_cat_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $c_id = $this->input->post('c_id');
                    if ($c_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $res   = $this->MedicalMall_model->getallSubCategoriesHealthMall($c_id);
                        $count = sizeof($res);
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "message" => "Categories Found",
                            "data" => $res
                        );
                        if ($count > 0) {
                            
                            
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
        
    }
    
    function pro_by_cat_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['c_id'] = $this->input->post('c_id');
                    $per_page     = $this->input->post('per_page');
                    $page_no      = $this->input->post('page_no');
                    
                    
                    //	$data['pincode']  = $this->input->post('pincode');
                    if ($data['c_id'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $res = $this->MedicalMall_model->getProductByCategories($data);
                        
                        $count = $res->num_rows();
                        
                        
                        if ($count > 0) {
                            
                            if (!empty($page_no) && !empty($per_page)) {
                                $exclude_page_entries = ($page_no - 1) * $per_page;
                                $next_page_entries    = $exclude_page_entries + $per_page;
                                
                                $last_page_no = $count / $per_page;
                                $last_page_no = ceil($last_page_no);
                                
                                // print_r();
                                
                                $array1 = $res->result_array();
                                
                                $array2     = array_splice($array1, $exclude_page_entries, $per_page);
                                $data_count = sizeof($array2);
                                
                                
                                $result = array(
                                    "status" => "true",
                                    "statuspic_root_code" => "200",
                                    "last_page_no" => $last_page_no,
                                    "data_count" => $data_count,
                                    "current_page" => $page_no,
                                    "data" => $array2
                                );
                                
                                $this->output->set_content_type('Content-Type: application/json');
                                
                                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                                
                                
                                
                            } else {
                                
                                $result = array(
                                    "status" => "true",
                                    "statuspic_root_code" => "200",
                                    "last_page_no" => 1,
                                    "data_count" => $count,
                                    "current_page" => 1,
                                    "data" => $res->result_array()
                                );
                                // $this->response(json_encode($categories), 200); 
                                $this->output->set_content_type('Content-Type: application/json');
                                
                                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            }
                            
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function pro_by_subcat_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['sub_id'] = $this->input->post('sub_id');
                    //$data['pincode']  = $this->input->post('pincode');
                    if ($data['sub_id'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getProductBySubCategories($data);
                        
                        $count = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $result->result_array()
                        );
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function check_product_availability_post1()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['p_id']    = $this->input->post('p_id');
                    $data['pincode'] = $this->input->post('pincode');
                    if ($data['p_id'] == "" || $data['pincode'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->checkProductAvailibility($data);
                        $count  = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "message" => "Product Available"
                        );
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function check_product_availability_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['p_id']    = $this->input->post('p_id');
                    $data['pincode'] = $this->input->post('pincode');
                    if ($data['p_id'] == "" || $data['pincode'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result_count = $this->MedicalMall_model->checkProductAvailibility($data);
                        $count        = $result_count;
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "message" => "Product Available",
                            "p_id" => $data['p_id'],
                            "pincode" => $data['pincode']
                        );
                        if ($result_count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found",
                                "p_id" => $data['p_id'],
                                "pincode" => $data['pincode']
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function logincheck_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['email']    = $this->input->post('email');
                    $data['password'] = $this->input->post('password');
                    if ($data['email'] == "" || $data['password'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->checkLoginUser($data);
                        
                        $count = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "message" => "User Found",
                            "statuspic_root_code" => "200",
                            "data" => $result->row_array()
                        );
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function adduser_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['name']     = $this->input->post('name');
                    $data['email']    = $this->input->post('email');
                    $data['phoneno']  = $this->input->post('phoneno');
                    $data['address1'] = $this->input->post('address1');
                    $data['address2'] = $this->input->post('address2');
                    $data['city']     = $this->input->post('city');
                    $data['state']    = $this->input->post('state');
                    $data['country']  = $this->input->post('country');
                    $data['pincode']  = $this->input->post('pincode');
                    $data['lat']      = $this->input->post('lat');
                    $data['long']     = $this->input->post('long');
                    $data['password'] = $this->input->post('password');
                    if ($data['name'] == "" || $data['email'] == "" || $data['phoneno'] == "" || $data['address1'] == "" || $data['city'] == "" || $data['state'] == "" || $data['pincode'] == "" || $data['password'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->addUser($data);
                        
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    }
                }
            }
        }
    }
    
    function get_all_product_by_name_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $page_no  = $this->input->post('page_no');
                    $per_page = $this->input->post('per_page');
                    
                    if ($page_no != "" || $per_page != "") {
                        
                        
                        $data['name'] = $this->input->post('name');
                        
                        $exclude_page_entries = ($page_no - 1) * $per_page;
                        $next_page_entries    = $exclude_page_entries + $per_page;
                        
                        // 		echo $exclude_page_entries;
                        // 		echo $next_page_entries;
                        $result       = $this->MedicalMall_model->getAllProductsByName($data);
                        $count        = $result->num_rows();
                        $last_page_no = $count / $per_page;
                        $last_page_no = ceil($last_page_no);
                        // echo $last_page_no;
                        $array1       = $result->result_array();
                        
                        // code to make image links starts here
                        // $i=0;
                        // foreach($array1 as $res){
                        // $temp = $array1[$i]['pd_photo_1'];
                        // $flag = preg_match('/^http*/i',$temp);
                        // if($flag == 0){
                        // $new_link = 'https://medicalwale.com/uploads/'.$temp;
                        // $array1[$i]['pd_photo_1'] = $new_link;
                        // }
                        // $i++;
                        // }
                        // and ends here
                        
                        $array2     = array_splice($array1, $exclude_page_entries, $per_page);
                        $data_count = sizeof($array2);
                        // print_r($array2);
                        $result     = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "last_page_no" => $last_page_no,
                            "data_count" => $data_count,
                            "data" => $array2
                        );
                        //  print_r($result);
                        //  die();
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    } else {
                        $result = array(
                            "status" => "400",
                            "message" => "please enter page number and per page"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    }
                }
            }
        }
    }
    
    function get_all_countries_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->getallCountries();
                    $count  = $result->num_rows();
                    
                    $result = array(
                        "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $result->result_array()
                    );
                    if ($count > 0) {
                        
                        
                        // $this->response(json_encode($categories), 200); 
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                    else {
                        
                        $result = array(
                            "status" => "false",
                            "message" => "Result not found"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                }
            }
        }
    }
    
    function get_all_states_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $country_id = $this->input->post('country_id');
                    if ($country_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getallStates($country_id);
                        $count  = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $result->result_array()
                        );
                        if ($count > 0) {
                            
                            
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
        
    }
    
    function get_all_cities_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $state_id = $this->input->post('state_id');
                    if ($state_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getallCities($state_id);
                        $count  = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $result->result_array()
                        );
                        if ($count > 0) {
                            
                            
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function rating_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['product_id'] = $this->input->post('product_id');
                    $data['cus_id']     = $this->input->post('cus_id');
                    $data['pr_review']  = $this->input->post('pr_review');
                    $data['ratting']    = $this->input->post('ratting');
                    if ($data['product_id'] == "" || $data['cus_id'] == "" || $data['pr_review'] == "" || $data['ratting'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->setallRatings($data);
                        
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    }
                }
            }
        }
    }
    
    function getRatingProduct_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $pd_id = $this->input->post('pd_id');
                    if ($pd_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getRatingsForProduct($pd_id);
                        
                        if ($result['status']) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function all_search_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->getallSearch();
                    // 		$count = $result->num_rows();
                    $count  = sizeof($result);
                    
                    $result = array(
                        "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $result
                    );
                    if ($count > 0) {
                        
                        
                        // $this->response(json_encode($categories), 200); 
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                    else {
                        
                        $result = array(
                            "status" => "false",
                            "message" => "Result not found"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                    
                }
            }
        }
    }
    
    function getBestSellers_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->getBestSellerProducts();
                    
                    $count = sizeof($result);
                    
                    $result = array(
                        "status" => "true",
                        "statuspic_root_code" => "200",
                        "count" => $count,
                        "data" => $result
                    );
                    if ($count > 0) {
                        // $this->response(json_encode($categories), 200); 
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    } else {
                        
                        $result = array(
                            "status" => "false",
                            "message" => "Result not found"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    }
                }
            }
        }
    }
    
    function get_all_products_by_cat_subcat_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['c_id']  = $this->input->post('c_id');
                    $data['sc_id'] = $this->input->post('sc_id');
                    if ($data['c_id'] == "" || $data['sc_id'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getallProductByCatSubCat($data);
                        $count  = $result->num_rows();
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $result->result_array()
                        );
                        if ($count > 0) {
                            
                            
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function get_vendor_by_id_post()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $data['v_id'] = $this->input->post('v_id');
                    if ($data['v_id'] == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $result = $this->MedicalMall_model->getVendorById($data);
                        $count  = sizeof($result);
                        
                        $result = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "data" => $result
                        );
                        if ($count > 0) {
                            
                            
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                        
                        else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    }
                }
            }
        }
    }
    
    function get_description()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $pro_id = $this->input->post('pd_id');
                    if ($pro_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_description($pro_id);
                    }
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    function get_offers()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->get_offers();
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    function add_address()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $address['address_type'] = $this->input->post('address_type');
                    $address['user_id']      = $this->input->post('user_id');
                    $address['name']         = $this->input->post('name');
                    $address['mobile']       = $this->input->post('mobile');
                    $address['pincode']      = $this->input->post('pincode');
                    $address['address1']     = $this->input->post('address1');
                    $address['address2']     = $this->input->post('address2');
                    $address['landmark']     = $this->input->post('landmark');
                    $address['city']         = $this->input->post('city');
                    $address['state']        = $this->input->post('state');
                    // $date = $this->input->post('date');
                    $result                  = $this->MedicalMall_model->add_address($address);
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    // 	get_address
    
    function get_address()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    if ($user_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_address($user_id);
                    }
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    function get_cat_subcat()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->get_cat_subcat();
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
                
            }
            
        }
    }
    
    function get_user_orders()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    if ($user_id == "") {
                        $result = array(
                            "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_user_orders($user_id);
                    }
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    function add_to_wishlist()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id    = $this->input->post('user_id');
                    $product_id = $this->input->post('product_id');
                    
                    if ($user_id == 0 || $product_id == 0 || $user_id == "" || $product_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id and product_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->add_to_wishlist($user_id, $product_id);
                    }
                    
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    
    // By shyam
    function product_like()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id    = $this->input->post('user_id');
                    $product_id = $this->input->post('product_id');
                    
                    if ($user_id == 0 || $product_id == 0 || $user_id == "" || $product_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id and product_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->product_like($user_id, $product_id);
                    }
                    
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    
    function get_user_wishlist()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                // print_r($response); die();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    if ($user_id == 0 || $user_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id and product_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_user_wishlist($user_id);
                    }
                    // $result = $this->MedicalMall_model->get_user_wishlist($user_id, $product_id);
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    
    function get_product_like()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                // print_r($response); die();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    if ($user_id == 0 || $user_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id and product_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_product_like($user_id);
                    }
                 
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    
     function add_to_cart()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id    = $this->input->post('user_id');
                    $product_id = $this->input->post('product_id');
                    $quantity   = $this->input->post('quantity');
                    $offer_id   = $this->input->post('offer_id');
                    $referal_code   = $this->input->post('referal_code');
                    $variable_pd_id   = $this->input->post('variable_pd_id');
                    $sku   = $this->input->post('sku');
                    if(empty($referal_code)){
                        $referal_code = 0;
                    }
                    
                    if(empty($variable_pd_id)){
                        $variable_pd_id = 0;
                    }
                    
                    if(empty($sku)){
                        $sku = 0;
                    }
                    
                    if ($user_id == 0 || $product_id == 0 || $user_id == "" || $product_id == "" || $quantity == "" || $quantity == 0 || $offer_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id, product_id, quantity and offer_id"
                        );
                    } else if ($quantity < 1) {
                        $result = array(
                            "status" => 400,
                            "message" => "Quantity must be greater than 0 "
                        );
                    } else {
                        $result = $this->MedicalMall_model->add_to_cart($user_id, $product_id, $quantity, $offer_id,$referal_code,$variable_pd_id,$sku);
                    }
                    
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    function get_user_cart()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    if ($user_id == 0 || $user_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_user_cart($user_id);
                    }
                    // $result = $this->MedicalMall_model->get_user_wishlist($user_id, $product_id);
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
  
    
     function get_cart()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    if ($user_id == 0 || $user_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->get_cart($user_id);
                    }
                    // $result = $this->MedicalMall_model->get_user_wishlist($user_id, $product_id);
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                }
            }
        }
    }
    
    function remove_from_cart(){
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    $pd_id   = $this->input->post('pd_id');
                    if ($user_id == 0 || $user_id == "" || $pd_id == 0 || $pd_id == "") {
                        $res = array(
                            "status" => 400,
                            "message" => "please enter user_id and pd_id"
                        );
                    } else {
                        
                        if(!empty($this->input->post('variable_pd_id'))){
                            $variable_pd_id = $this->input->post('variable_pd_id');
                        } else {
                            $variable_pd_id = 0;
                        }
                        $result = $this->MedicalMall_model->remove_from_cart($user_id, $pd_id, $variable_pd_id);
                        if ($result == 1) {
                            $res = array(
                                "status" => 200,
                                "message" => "Successfully removed"
                            );
                        } else {
                            $res = array(
                                "status" => 400,
                                "message" => "Something went wrong"
                            );
                        }
                    }
                    // $result = $this->MedicalMall_model->get_user_wishlist($user_id, $product_id);
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($res));
                }
            }
        }
    }
    
    function remove_from_wishlist()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    $pd_id   = $this->input->post('pd_id');
                    if ($user_id == 0 || $user_id == "" || $pd_id == 0 || $pd_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter user_id and $pd_id"
                        );
                    } else {
                        $result = $this->MedicalMall_model->remove_from_wishlist($user_id, $pd_id);
                        if ($result == 1) {
                            $res = array(
                                "status" => 200,
                                "message" => "Successgully removed"
                            );
                        } else {
                            $res = array(
                                "status" => 200,
                                "message" => "Something went wrong"
                            );
                        }
                    }
                    // $result = $this->MedicalMall_model->get_user_wishlist($user_id, $product_id);
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($res));
                }
            }
        }
    }
    
    
    function get_offer_products()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $offer_id = $this->input->post('offer_id');
                    if ($offer_id == "") {
                        $res = array(
                            "status" => 400,
                            "message" => "Please enter offer_id"
                        );
                    } else {
                        
                        $result = $this->MedicalMall_model->get_offer_products($offer_id);
                        
                        $res = array(
                            "status" => 200,
                            "message" => "Success",
                            "data" => $result
                        );
                    }
                    
                }
            }
            
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($res));
        }
    }
    
    // 	get_banner_images
    function get_banner_images()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $result = $this->MedicalMall_model->get_banner_images();
                    
                    $res = array(
                        "status" => 200,
                        "message" => "Success",
                        "data" => $result
                    );
                    
                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($res));
                    
                }
            }
        }
    }
    
    function search_products_by_all()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $page_no  = $this->input->post('page_no');
                    $per_page = $this->input->post('per_page');
                    
                    if ($page_no != "" || $per_page != "") {
                        
                        
                        $data['name'] = $this->input->post('search');
                        
                        $exclude_page_entries = ($page_no - 1) * $per_page;
                        $next_page_entries    = $exclude_page_entries + $per_page;
                        
                        // 		echo $exclude_page_entries;
                        // 		echo $next_page_entries;
                        $result       = $this->MedicalMall_model->search_products_by_all($data);
                        $count        = $result->num_rows();
                        $last_page_no = $count / $per_page;
                        $last_page_no = ceil($last_page_no);
                        // echo $last_page_no;
                        $array1       = $result->result_array();
                        
                        // code to make image links starts here
                        // $i=0;
                        // foreach($array1 as $res){
                        // $temp = $array1[$i]['pd_photo_1'];
                        // $flag = preg_match('/^http*/i',$temp);
                        // if($flag == 0){
                        // $new_link = 'https://medicalwale.com/uploads/'.$temp;
                        // $array1[$i]['pd_photo_1'] = $new_link;
                        // }
                        // $i++;
                        // }
                        // and ends here
                        
                        $array2     = array_splice($array1, $exclude_page_entries, $per_page);
                        $data_count = sizeof($array2);
                        // print_r($array2);
                        $result     = array(
                            "status" => "true",
                            "statuspic_root_code" => "200",
                            "last_page_no" => $last_page_no,
                            "data_count" => $data_count,
                            "data" => $array2
                        );
                        //  print_r($result);
                        //  die();
                        if ($count > 0) {
                            // $this->response(json_encode($categories), 200); 
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        } else {
                            
                            $result = array(
                                "status" => "false",
                                "message" => "Result not found"
                            );
                            $this->output->set_content_type('Content-Type: application/json');
                            
                            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                            
                        }
                    } else {
                        $result = array(
                            "status" => "400",
                            "message" => "please enter page number and per page"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    }
                }
            }
        }
    }
    
    // 	allow rating to user
    function allow_rating()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    $pd_id   = $this->input->post('pd_id');
                    
                    if ($user_id != "" && $pd_id != "") {
                        
                        $res = $this->MedicalMall_model->allow_rating($user_id, $pd_id);
                        
                        
                        if ($res == 1) {
                            $result = array(
                                "status" => 200,
                                "message" => "sucess",
                                "user_id" => $user_id,
                                "pd_id" => $pd_id,
                                "description" => "Allow user to give ratings"
                                
                            );
                        } else {
                            $result = array(
                                "status" => 400,
                                "message" => "failed",
                                "user_id" => $user_id,
                                "pd_id" => $pd_id,
                                "description" => "Do not allow user to give ratings"
                                
                            );
                        }
                        
                        
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                    } else {
                        $result = array(
                            "status" => 400,
                            "message" => "please enter USER ID and PRODUCT ID"
                        );
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    }
                }
            }
        }
    }
    
    
    function get_recently_viewed()
    {
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method != 'POST') {
            $resp = array(
                "status" => "Bad request",
                "statuspic_root_code" => "400"
            );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        } else {
            $check_auth_client = $this->Login_Model->check_auth_client();
            if ($check_auth_client == true) {
                $response = $this->Login_Model->auth();
                if ($response == 200) {
                    $user_id = $this->input->post('user_id');
                    if ($user_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "Please enter user_id"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        
                        $results = $this->MedicalMall_model->get_recently_viewed($user_id);
                        
                        //   $count = sizeof($results);
                        
                        $result = array(
                            "status" => 200,
                            "message" => "success",
                            "data" => $results
                        );
                        // print_r($result); die();
                        // $this->response(json_encode($categories), 200); 
                        $this->output->set_content_type('Content-Type: application/json');
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                        
                        
                    }
                }
            }
        }
    }
    
    
}
?>