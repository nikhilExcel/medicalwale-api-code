<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Api_2 extends CI_Controller{
    
    public function __construct($config = 'rest')
    {
        //  header('Access-Control-Allow-Origin: *'); 
        //  header("Access-Control-Allow-Credentials: true"); 
        //  header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
         
        parent::__construct($config);

        $this->load->model('MedicalMall_model_2');
       
        $this->load->model('Login_Model');
        
    }
    
    
    public function home_page(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            
		            $user_id = $this->input->post('user_id');
                    if ($user_id == "") {
                        $result = array(
                            "status" => 400,
                            "message" => "Please enter user_id"
                        );
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    } else {
                        $resp = array(
		                "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $this->MedicalMall_model_2->home_page($user_id)
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
                    }
                        
                    }
                     
		            
		        }
			}
		}
	
	
	public function get_featured_products(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $resp = array(
		                "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $this->MedicalMall_model_2->get_featured_products()
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
		
	public function get_ayurvedic_products(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $resp = array(
		                "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $this->MedicalMall_model_2->get_ayurvedic_products()
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
// 	get_fitness_products

	public function get_fitness_products(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $resp = array(
		                "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $this->MedicalMall_model_2->get_fitness_products()
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
// 	get_personal_care_products
public function get_personal_care_products(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $resp = array(
		                "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $this->MedicalMall_model_2->get_personal_care_products()
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}


	//luxurious_brand
	public function get_luxurious_brand_products(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $resp = array(
		                "status" => "true",
                        "statuspic_root_code" => "200",
                        "data" => $this->MedicalMall_model_2->get_luxurious_brand_products()
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	
	//new_arrival
	public function get_new_arrival(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $resp = array(
		                "status" => 200,
                        "message" => "success",
                        "data" => $this->MedicalMall_model_2->get_new_arrival()
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        } else {
		            $resp = array(
		                "status" => 400,
                        "message" => "failed",
                        "data" => array()
		                );
					
					    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		        }
			}
		}
	}
	

	public function place_order_post(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $data['cust_id']  = $this->input->post('cust_id');
        		    $data['products'] = $this->input->post('products');
        		    $pro = json_decode($data['products'],TRUE);
        		    $data['payment_method'] = $this->input->post('payment_method');
		          //  if($data['cust_id'] == "" || $data['products'] == "" || (json_last_error() == JSON_ERROR_SYNTAX) || $data['payment_method'] == ""){
		            if($data['cust_id'] == "" || $data['products'] == "" || $data['payment_method'] == ""){
		                $resp = array(
		                    "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
		                );
			            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		            }else{
                		$data['name']  = $this->input->post('name');
                		$data['mobileno']   = $this->input->post('mobileno');
                		$data['pincode']  = $this->input->post('pincode');
                		$data['address']   = $this->input->post('address');
                		$data['address1']   = $this->input->post('address1');
                		$data['landmark']  = $this->input->post('landmark');
                		$data['city']   = $this->input->post('city');
                		$data['state'] = $this->input->post('state');
    		
                		
                		
                // 		print_r($pro['products']);die();
                		$data['total_products'] = count($pro['products']);
                // 		print_r($data['total_products']);die;
                		for($i=0;$i<$data['total_products'];$i++){
                		    $data['product_id'.$i]   = $pro['products'][$i]['id'];
                		    $data['product_name'.$i]   = $pro['products'][$i]['name'];
                		    $data['product_qty'.$i]  = $pro['products'][$i]['quantity'];
                		    $data['product_price'.$i]   = $pro['products'][$i]['price'];
                		    $data['total_products_price'.$i]   = $pro['products'][$i]['price'] * $pro['products'][$i]['quantity'];
                		    $data['vendor_id'.$i]   = $pro['products'][$i]['v_id'];
                		    $data['vendor_name'.$i]   = $pro['products'][$i]['v_name'];
                		    $data['is_offer'.$i]   = $pro['products'][$i]['is_offer'];
                		    if($pro['products'][$i]['is_offer'] == 1){
                		        $data['offer_id'.$i]   = $pro['products'][$i]['offer_id'];
                		        $data['offer_price'.$i]   = $pro['products'][$i]['offer_price'];
                		        $data['offer_total'.$i] = $pro['products'][$i]['offer_price'] * $pro['products'][$i]['quantity'];
                		    }else{
                		        $data['offer_id'.$i]   = 0;
                		        $data['offer_price'.$i]   = $pro['products'][$i]['price'];
                		        $data['offer_total'.$i] = $data['total_products_price'.$i];
                		    }
                		}
                		$amt=0;
                		$offer_amt=0;
                		for($i=0;$i<$data['total_products'];$i++){
                		    $amt += $data['total_products_price'.$i];
                		    $offer_amt += $data['offer_total'.$i];
                		}
                		$data['amount']   = $amt;
                		$data['offer_amount'] = $offer_amt;
                		$data['savings'] = $amt - $offer_amt;
                        //print_r($data);die;
                        $resp = $this->MedicalMall_model_2->placeOrder($data);
                		return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp)); 
		            }
		        }
			}
		}
	}
	
		public function place_order1(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $data['cust_id']  = $this->input->post('cust_id');
        		    $data['orders'] = $this->input->post('products');
        		    $orders = json_decode($data['orders'],TRUE);
        		    $data['payment_method'] = $this->input->post('payment_method');
		            if($data['cust_id'] == "" || $data['orders'] == "" || (json_last_error() == JSON_ERROR_SYNTAX) || $data['payment_method'] == ""){
		                $resp = array(
		                    "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
		                );
			            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		            }else{
		                $referral_codes = $resp = $allOrders = array();
		                
		                $allowed = $this->MedicalMall_model_2->placing_order_allowed($data);
		                $referral_code['allowedArray'] = $allowed['allowedArray'];
		                $referral_code['notAllowedArray'] = $allowed['notAllowedArray'];
		                
		                if($allowed['status'] == 200){
		                    
		               
		               
                		$data['name']  = $this->input->post('name');
                		$data['mobileno']   = $this->input->post('mobileno');
                		$data['pincode']  = $this->input->post('pincode');
                		$data['address']   = $this->input->post('address');
                		$data['address1']   = $this->input->post('address1');
                		$data['landmark']  = $this->input->post('landmark');
                		$data['city']   = $this->input->post('city');
                		$data['state'] = $this->input->post('state');
    		
    		
    		                	$is_website = $this->input->post('is_website');
                		if($is_website != "" || !empty($is_website)){
                		    if($is_website == 1){
                    		    $data['is_website'] = $is_website;
                    		} else {
                    		    $data['is_website'] = $is_website;
                    		}
                		} else {
                		    $data['is_website'] = 0;
                		}
    		        
    		          	for($o=0;$o<sizeof($orders['orders']);$o++){
    		          	    
    		          	    $pro = $orders['orders'][$o];
    		                $data['products'] = $orders['orders'][$o];
                    //  		print_r($pro['products']);
                    //  		die();
                    		$data['total_products'] = count($pro['products']);
                    // 		print_r($data['total_products']);die;
                    
                    
                    // place orde
                    		for($i=0;$i<$data['total_products'];$i++){
                    		    $data['product_id'.$i]   = $pro['products'][$i]['id'];
                    		    $data['product_name'.$i]   = $pro['products'][$i]['name'];
                    		    $data['product_qty'.$i]  = $pro['products'][$i]['quantity'];
                    		    $data['product_price'.$i]   = $pro['products'][$i]['price'];
                    		    $data['total_products_price'.$i]   = $pro['products'][$i]['price'] * $pro['products'][$i]['quantity'];
                    		    $data['vendor_id'.$i]   = $pro['products'][$i]['v_id'];
                    		    $data['vendor_name'.$i]   = $pro['products'][$i]['v_name'];
                    		    $data['is_offer'.$i]   = $pro['products'][$i]['is_offer'];
                    		    if($pro['products'][$i]['is_offer'] == 1){
                    		        $data['offer_id'.$i]   = $pro['products'][$i]['offer_id'];
                    		        $data['offer_price'.$i]   = $pro['products'][$i]['offer_price'];
                    		        $data['offer_total'.$i] = $pro['products'][$i]['offer_price'] * $pro['products'][$i]['quantity'];
                    		    }else{
                    		        $data['offer_id'.$i]   = 0;
                    		      //  $data['offer_price'.$i]   = $pro['products'][$i]['price'];
                    		      //  $data['offer_total'.$i] = $data['total_products_price'.$i];
                    		      $data['offer_price'.$i]   = 0;
                    		        $data['offer_total'.$i] = 0;

                    		    }
                    		    //  referral_code
                    		    if(!empty($pro['products'][$i]['referral_code']) && $pro['products'][$i]['referral_code'] != 0){
                    		        //echo "referral code not empty its".$pro['products'][$i]['referral_code'];
                    		        $data['referral_code'.$i] = $pro['products'][$i]['referral_code'];
                    		    } else {
                    		       // echo "referral code empty";
                    		        $data['referral_code'.$i] = 0;
                    		    }
                    		 
                    		}
                    		$amt=0;
                    		$offer_amt=0;
                    		for($i=0;$i<$data['total_products'];$i++){
                    		    $amt += $data['total_products_price'.$i];
                    		    $offer_amt += $data['offer_total'.$i];
                    		}
                    		$data['amount']   = $amt;
                    		$data['offer_amount'] = $offer_amt;
                    		$data['savings'] = $amt - $offer_amt;
                    		$data['number'] = $o;
                            //print_r($data);die;
                            $result[] = $this->MedicalMall_model_2->place_order1($data, $allowed);
                            
		                }
		                $original_total_price = 0;
                        $offer_total_price = 0;
                        $saved_amount = 0;
		               $response = $orders = $order = $productDetails = $orderDetails = array();
		                for($j=0;$j<sizeof($result);$j++){
		                    if($result[$j]['status'] == 'false'){
		                        $resp = array(
                    			"status" => "false",
                    			"message" => "something went wrong"
                			);
		                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp)); 
		                    } else {
		                         $original_total_price = $result[$j]['original_total_price'] + $original_total_price;
                                 $offer_total_price = $result[$j]['offer_total_price'] + $offer_total_price;
                                 $saved_amount = $result[$j]['saved_amount'] + $saved_amount;
                                 $orderDetails = $result[$j]['order_details']; 
                                 $productDetails = $result[$j]['products'][1];
                                 
                                for($i=0;$i<sizeof($productDetails);$i++){
                                    
                                  
                                
                                
                                    $res = $this->MedicalMall_model_2->get_product_image($productDetails[$i]['id']);
                                    $productDetails[$i]['pd_photo_1'] = $res['pd_photo_1'];
                                    $reduce_product_quantity = $this->MedicalMall_model_2->quantity_bestseller($productDetails[$i]['id'],$productDetails[$i]['quantity']);
                                    
                                    
                                    
                                }
                                 
                                
                                //  die();
                                 $order = array('order_details' => $orderDetails, 'products' => $productDetails);
                                 $orders[] = $order;
		                   
		                     
		                    }
		                     
		                }
		                $response['status'] = "true";
            			$response['statuspic_root_code'] = "200";
            			$response['message'] = "Order Created Succesfully";
            			$response['original_total_price'] = $original_total_price;
            			$response['offer_total_price'] = $offer_total_price;
            			$response['saved_amount'] = $saved_amount;
            			$response['referral_codes'] = $referral_code;
            			$response['orders'] = $orders;
            			
		                
		                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response)); 
		                
		                } else {
	
        
		                    $resp = array('status'=>"false", 
		                                    'statuspic_root_code'=>"400",
		                                    'message' => "Order failed",
		                                    'original_total_price'=> $original_total_price, 
		                                    'offer_total_price' => $offer_total_price, 
		                                    'saved_amount'=>$saved_amount,
		                                    'referral_codes'=>$referral_code, 
		                                    'orders' => array()
		                                   );
		                   
		                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp)); 
		                }
		            }
		        }
			}
		}
	}
	
		public function place_order(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $data['cust_id']  = $this->input->post('cust_id');
        		    $data['orders'] = $this->input->post('products');
        		    $orders = json_decode($data['orders'],TRUE);
        		    $data['payment_method'] = $this->input->post('payment_method');
		            if($data['cust_id'] == "" || $data['orders'] == "" || $data['payment_method'] == ""){
		                $resp = array(
		                    "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
		                );
			            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		            }else{
		                $referral_codes = $resp = $allOrders = array();
		                
		                $allowed = $this->MedicalMall_model_2->placing_order_allowed($data);
		                $referral_code['allowedArray'] = $allowed['allowedArray'];
		                $referral_code['notAllowedArray'] = $allowed['notAllowedArray'];
		                
		                if($allowed['status'] == 200){
		                    
		               
		               
                		$data['name']  = $this->input->post('name');
                		$data['mobileno']   = $this->input->post('mobileno');
                		$data['pincode']  = $this->input->post('pincode');
                		$data['address']   = $this->input->post('address');
                		$data['address1']   = $this->input->post('address1');
                		$data['landmark']  = $this->input->post('landmark');
                		$data['city']   = $this->input->post('city');
                		$data['state'] = $this->input->post('state');
    		
    		
    		                	$is_website = $this->input->post('is_website');
                		if($is_website != "" || !empty($is_website)){
                		    if($is_website == 1){
                    		    $data['is_website'] = $is_website;
                    		} else {
                    		    $data['is_website'] = $is_website;
                    		}
                		} else {
                		    $data['is_website'] = 0;
                		}
    		        
    		          	for($o=0;$o<sizeof($orders['orders']);$o++){
    		          	    
    		          	    $pro = $orders['orders'][$o];
    		                $data['products'] = $orders['orders'][$o];
    		                if(!empty($orders['orders'][$o]['chc'])){
    		                    $chc = $orders['orders'][$o]['chc'];
    		                } else {
    		                    $chc = 0;
    		                }
                     	    
                     	    
                    //  	    print_r($chc);
                    // 		die();
                    		$data['total_products'] = count($pro['products']);
                    // 		print_r($data['total_products']);die;
                    
                    
                    // place orde
                    		for($i=0;$i<$data['total_products'];$i++){
                    		    $data['product_id'.$i]   = $pro['products'][$i]['id'];
                    		    $data['product_name'.$i]   = $pro['products'][$i]['name'];
                    		    $data['product_qty'.$i]  = $pro['products'][$i]['quantity'];
                    		    $data['product_price'.$i]   = $pro['products'][$i]['price'];
                    		    $data['total_products_price'.$i]   = $pro['products'][$i]['price'] * $pro['products'][$i]['quantity'];
                    		    $data['vendor_id'.$i]   = $pro['products'][$i]['v_id'];
                    		    $data['chc'.$i]   = $chc;
                    		    $data['vendor_name'.$i]   = $pro['products'][$i]['v_name'];
                    		    $data['is_offer'.$i]   = $pro['products'][$i]['is_offer'];
                    		    if($pro['products'][$i]['is_offer'] == 1){
                    		        $data['offer_id'.$i]   = $pro['products'][$i]['offer_id'];
                    		        $data['offer_price'.$i]   = $pro['products'][$i]['offer_price'];
                    		        $data['offer_total'.$i] = $pro['products'][$i]['offer_price'] * $pro['products'][$i]['quantity'];
                    		    }else{
                    		        $data['offer_id'.$i]   = 0;
                    		      //  $data['offer_price'.$i]   = $pro['products'][$i]['price'];
                    		      //  $data['offer_total'.$i] = $data['total_products_price'.$i];
                    		      $data['offer_price'.$i]   = 0;
                    		        $data['offer_total'.$i] = 0;

                    		    }
                    		    //  referral_code
                    		    if(!empty($pro['products'][$i]['referral_code']) && $pro['products'][$i]['referral_code'] != 0){
                    		        //echo "referral code not empty its".$pro['products'][$i]['referral_code'];
                    		        $data['referral_code'.$i] = $pro['products'][$i]['referral_code'];
                    		    } else {
                    		       // echo "referral code empty";
                    		        $data['referral_code'.$i] = 0;
                    		    }
                    		    
                    		    //  variable_pd_id
                    		    if(!empty($pro['products'][$i]['variable_pd_id'])){
                    		        //echo "referral code not empty its".$pro['products'][$i]['referral_code'];
                    		        $data['variable_pd_id'.$i] = $pro['products'][$i]['variable_pd_id'];
                    		    } else {
                    		       // echo "referral code empty";
                    		        $data['variable_pd_id'.$i] = 0;
                    		    }
                    		    
                    		  //  sku
                    		    if(!empty($pro['products'][$i]['sku'])){
                    		        //echo "referral code not empty its".$pro['products'][$i]['referral_code'];
                    		        $data['sku'.$i] = $pro['products'][$i]['sku'];
                    		    } else {
                    		       // echo "referral code empty";
                    		        $data['sku'.$i] = 0;
                    		    }
                    		    
                    		}
                    		$amt=0;
                    		$offer_amt=0;
                    		for($i=0;$i<$data['total_products'];$i++){
                    		    $amt += $data['total_products_price'.$i];
                    		    $offer_amt += $data['offer_total'.$i];
                    		}
                    		$data['amount']   = $amt;
                    		$data['offer_amount'] = $offer_amt;
                    		$data['savings'] = $amt - $offer_amt;
                    		$data['number'] = $o;
                            // print_r($pro);die;
                            $products = $pro;
                            $result[] = $this->MedicalMall_model_2->place_order($data, $allowed,$products);
                            
		                }
		                $original_total_price = 0;
                        $offer_total_price = 0;
                        $saved_amount = 0;
		               $response = $orders = $order = $productDetails = $orderDetails = array();
		                for($j=0;$j<sizeof($result);$j++){
		                    if($result[$j]['status'] == 'false'){
		                        $resp = array(
                    			"status" => "false",
                    			"message" => "something went wrong"
                			);
		                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp)); 
		                    } else {
		                         $original_total_price = $result[$j]['original_total_price'] + $original_total_price;
                                 $offer_total_price = $result[$j]['offer_total_price'] + $offer_total_price;
                                 $saved_amount = $result[$j]['saved_amount'] + $saved_amount;
                                 $orderDetails = $result[$j]['order_details']; 
                               //  print_r($result[$j]['products']); die();
                               $p = 2;
                                 $productDetails = $result[$j]['products'][$p];
                                 
                                for($i=0;$i<sizeof($productDetails);$i++){
                                    
                                  
                                    $res = $this->MedicalMall_model_2->get_product_image($productDetails[$i]['id']);
                                    $productDetails[$i]['pd_photo_1'] = $res['pd_photo_1'];
                                    
                                    if(array_key_exists('variable_pd_id', $productDetails[$i])  && $productDetails[$i]['variable_pd_id'] > 0){
                                    //   print_r($productDetails); die();
                                      $res = $this->MedicalMall_model_2->get_variable_product_image($productDetails[$i]['variable_pd_id']);
                                      $productDetails[$i]['pd_photo_1'] = $res['image'];
                                      $reduce_product_quantity = $this->MedicalMall_model_2->variable_quantity_bestseller($productDetails[$i]['id'],$productDetails[$i]['variable_pd_id'],$productDetails[$i]['quantity']);
                                   } else {
                                    
                                    $reduce_product_quantity = $this->MedicalMall_model_2->quantity_bestseller($productDetails[$i]['id'],$productDetails[$i]['quantity']);
                                      
                                  }
                                }
                                 
                                
                                //  die();
                                 $order = array('order_details' => $orderDetails, 'products' => $productDetails);
                                 $orders[] = $order;
		                   
		                     
		                    }
		                     
		                }
		                $response['status'] = "true";
            			$response['statuspic_root_code'] = "200";
            			$response['message'] = "Order Created Succesfully";
            			$response['original_total_price'] = $original_total_price;
            			$response['offer_total_price'] = $offer_total_price;
            			$response['saved_amount'] = $saved_amount;
            			$response['referral_codes'] = $referral_code;
            			$response['orders'] = $orders;
            			
		                
		                return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($response)); 
		                
		                } else {
	
        
		                    $resp = array('status'=>"false", 
		                                    'statuspic_root_code'=>"400",
		                                    'message' => "Order failed",
		                                    'original_total_price'=> $original_total_price, 
		                                    'offer_total_price' => $offer_total_price, 
		                                    'saved_amount'=>$saved_amount,
		                                    'referral_codes'=>$referral_code, 
		                                    'orders' => array()
		                                   );
		                   
		                    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp)); 
		                }
		            }
		        }
			}
		}
	}
		public function change_payment_status(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => 400,
                "message" => "Bad request",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $user_id  = $this->input->post('user_id');
        		    $status = $this->input->post('status');
        		    
        		   if($user_id == "" || empty($user_id) || $status == "" || empty($status))   {
        		       $resp = array(
        		        "status" => 400,
                        "message" => "please enter status and user_id",
        		        );
        		        
        		   }   else {
        		       
        		      
        		             $resp = $this->MedicalMall_model_2->change_payment_status($user_id, $status);
        		      //  if($result == 1){
        		      //      $resp = array(
            		  //      "status" => 200,
                //             "message" => "success",
                           
        	    	  //      );
        		       
        		      //  }   else {
        		      //      $resp = array(
            		  //      "status" => 500,
                //             "message" => "Something went wrong",
                           
        	    	  //      );
        		       
        		      //  }
        		          
        		       
        		       
        		   }
        		  //  $orders = json_decode($data['orders'],TRUE);
        		  //  $data['payment_method'] = $this->input->post('payment_method');
        		    
        		     
        		    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		           
		        }
			}
		}
	}
	public function get_products(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            
		            $page_no  = $this->input->post('page_no');
            		$per_page  = $this->input->post('per_page');
            		$type = $this->input->post('type');
            		
            		if($type < 0 || $type == " " || $type > 2 ){
	                   $type = 0;
	               }
		
	               if($page_no == "" || $per_page == ""){
    		           $resp = array (
	                       "status" => 400,
	                       "message" => "failed",
	                       "description" => "please enter page number(page_no) and per page(per_page) fields"
	                       
	                       );
	                   return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));   
	               } 
	               else {
	                   
	                   
	                    $exclude_page_entries = ($page_no - 1) * $per_page;
        		        $next_page_entries = $exclude_page_entries + $per_page;
        		 
        		 
        		 
        		
    		            $id = $this->input->post('id');
    		            $term = $this->input->post('term');
    		            $resp = $this->MedicalMall_model_2->get_products($type,$id,$term, $page_no, $per_page);
                    
                    	
                    	$count = $resp->num_rows();
                    	
                    	if($count > 0){
                    	    $last_page_no = $count / $per_page;
                            $last_page_no = ceil($last_page_no);
                            
                            // print_r();
                            
                            $array1 = $resp->result_array();
                            
                            $array2 = array_splice($array1, $exclude_page_entries, $per_page);
                            $data_count = sizeof($array2) ;
                           
                            
                    		$result = array(
                                "status" => "true",
                                "statuspic_root_code" => "200",
                                "last_page_no" => $last_page_no,
                                "data_count" => $data_count,
                                "current_page" => $page_no,
                                "data" => $array2
                            );     
                    	} else {
                    	    $result = array(
                				"status" => 400,
                				"message" => "Result not found"
                			);
                    	}
                        
                        
                        
                        // die();
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    	
                    	
	                   
	               }
		            
		        }
			}
		}
	}
	
	
	public function get_products_v(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            
		            $page_no  = $this->input->post('page_no');
            		$per_page  = $this->input->post('per_page');
            		$vid  = $this->input->post('vid');
            		$type = $this->input->post('type');
            		
            		if($type < 0 || $type == " " || $type > 2 ){
	                   $type = 0;
	               }
		
	               if($page_no == "" || $per_page == ""){
    		           $resp = array (
	                       "status" => "false",
	                       "message" => "failed",
	                       "description" => "please enter page number(page_no) and per page(per_page) fields"
	                       
	                       );
	                   return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));   
	               } 
	               else {
	                   
	                   
	                    $exclude_page_entries = ($page_no - 1) * $per_page;
        		        $next_page_entries = $exclude_page_entries + $per_page;
        		 
        		 
        		 
        		
    		            $id = $this->input->post('id');
    		            $term = $this->input->post('term');
    		            $resp = $this->MedicalMall_model_2->get_products_v($type,$id,$term, $page_no, $per_page, $vid);
                    
                    	
                    	$count = $resp->num_rows();
                    	
                    	if($count > 0){
                    	    $last_page_no = $count / $per_page;
                            $last_page_no = ceil($last_page_no);
                            
                            // print_r();
                            
                            $array1 = $resp->result_array();
                            
                            $array2 = array_splice($array1, $exclude_page_entries, $per_page);
                            $data_count = sizeof($array2) ;
                           
                            
                    		$result = array(
                                "status" => "true",
                                "statuspic_root_code" => "200",
                                "last_page_no" => $last_page_no,
                                "data_count" => $data_count,
                                "current_page" => $page_no,
                                "data" => $array2
                            );     
                    	} else {
                    	    $result = array(
                				"status" => "false",
                				"message" => "Result not found"
                			);
                    	}
                        
                        
                        
                        // die();
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    	
                    	
	                   
	               }
		            
		        }
			}
		}
	}
	
		public function get_products1(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            
		            $page_no  = $this->input->post('page_no');
            		$per_page  = $this->input->post('per_page');
            		$type = $this->input->post('type');
            		
            		if($type < 0 || $type == " " || $type > 2 ){
	                   $type = 0;
	               }
		
	               if($page_no == "" || $per_page == ""){
    		           $resp = array (
	                       "status" => 400,
	                       "message" => "failed",
	                       "description" => "please enter page number(page_no) and per page(per_page) fields"
	                       
	                       );
	                   return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));   
	               } 
	               else {
	                   
	                   
	                    $exclude_page_entries = ($page_no - 1) * $per_page;
        		        $next_page_entries = $exclude_page_entries + $per_page;
        		 
        		 
        		 
        		
    		            $id = $this->input->post('id');
    		            $term = $this->input->post('term');
    		            $resp = $this->MedicalMall_model_2->get_products1($type,$id,$term, $page_no, $per_page);
                    
                    	
                    	$count = $resp->num_rows();
                    	
                    	if($count > 0){
                    	    $last_page_no = $count / $per_page;
                            $last_page_no = ceil($last_page_no);
                            
                            // print_r();
                            
                            $array1 = $resp->result_array();
                            
                            $array2 = array_splice($array1, $exclude_page_entries, $per_page);
                            $data_count = sizeof($array2) ;
                           
                            
                    		$result = array(
                                "status" => "true",
                                "statuspic_root_code" => "200",
                                "last_page_no" => $last_page_no,
                                "data_count" => $data_count,
                                "current_page" => $page_no,
                                "data" => $array2
                            );     
                    	} else {
                    	    $result = array(
                				"status" => 400,
                				"message" => "Result not found"
                			);
                    	}
                        
                        
                        
                        // die();
                        
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
                    	
                    	
	                   
	               }
		            
		        }
			}
		}
	}
	
	public function place_order_ledger(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    $check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            $data['cust_id']  = $this->input->post('cust_id');
        		    $data['products'] = $this->input->post('products');
        		    $data['payment_method'] = $this->input->post('payment_method');
        		    $data['trans_id'] = $this->input->post('trans_id');
        		    //$data['trans_id'] = mt_rand(1000000000000, 9999999999999);
        		    $data['trans_type'] = $this->input->post('trans_type');
		            if($data['cust_id'] == "" || $data['products'] == "" || $data['payment_method'] == "" || $data['trans_type'] == ""){
		                $resp = array(
		                    "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
		                );
			            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		            }else{
                		$data['name']  = $this->input->post('name');
                		$data['mobileno']   = $this->input->post('mobileno');
                		$data['amount']   = $this->input->post('amount');
                		$data['pincode']  = $this->input->post('pincode');
                		$data['address']   = $this->input->post('address');
                		$data['address1']   = $this->input->post('address1');
                		$data['landmark']  = $this->input->post('landmark');
                		$data['city']   = $this->input->post('city');
                		$data['state'] = $this->input->post('state');
    		            $data['status'] = $this->input->post('status');
    		            $data['payment_type'] = $this->input->post('payment_type');
                		$pro = json_decode($data['products'],TRUE);
                		$data['total_products'] = count($pro['products']);
                // 		print_r($pro);
                // 		print_r($data['total_products']);die;
                		for($i=0;$i<$data['total_products'];$i++){
                		    $data['product_id'.$i]   = $pro['products'][$i]['id'];
                		    $data['product_name'.$i]   = $pro['products'][$i]['name'];
                		    $data['product_qty'.$i]  = $pro['products'][$i]['quantity'];
                		    $data['product_price'.$i]   = $pro['products'][$i]['price'];
                		    $data['total_products_price'.$i]   = $pro['products'][$i]['total_price'];
                		    $data['vendor_id'.$i]   = $pro['products'][$i]['v_id'];
                		    $data['vendor_name'.$i]   = $pro['products'][$i]['v_name'];
                		}
    
                        $resp = $this->MedicalMall_model_2->placeOrder_ledger($data);
                		return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp)); 
		            }
		        }
			}
		}
	}
	
// 	public function payment_gateway(){
// // 	    $method = $_SERVER['REQUEST_METHOD'];
// // 		if($method != 'POST'){
// // 		    $resp = array(
// // 		        "status" => "Bad request",
// //                 "statuspic_root_code" => "400",
// // 		        );
// // 			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
// // 		} else {
// // 		    $check_auth_client = $this->Login_Model->check_auth_client();
// // 			if($check_auth_client == true){
// // 		        $response = $this->Login_Model->auth();
// // 		        if($response == 200){
		            
// 		            $posted['txnid'] = $this->input->post('txnid');
// 		            $posted['amount'] = $this->input->post('amount');
// 		            $posted['productinfo'] = $this->input->post('productinfo');
// 		            $posted['firstname'] = $this->input->post('firstname');
// 		            $posted['email'] = $this->input->post('email');
// 		            $posted['phone'] = $this->input->post('phone');
// 		            $posted['surl'] = 'https://sandbox.medicalwale.com/healthmall/API_2/payment_success';
// 		            $posted['furl'] = 'https://sandbox.medicalwale.com/healthmall/API_2/payment_failure';
// 		            $posted['mode'] = $this->input->post('mode');
		            
		           
		            
// 		            if(
//                     empty($posted['txnid'])
//                     || empty($posted['amount'])
//                     || empty($posted['firstname'])
//                     || empty($posted['email'])
//                     || empty($posted['phone'])
//                     || empty($posted['productinfo'])){
//                         $resp = array(
// 		                    "status" => "Bad request",
//                             "statuspic_root_code" => "400",
//                             "message" => "please enter fields"
// 		                );
// 			            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
//                     }else{
//                       // print_r($posted);die;
//                       $resp =  $this->MedicalMall_model_2->payment_gateway($posted);
//                         //return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
//                         return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
//                     }
// // 		        }
// // 			}
// // 		}
// 	}


public function payment_gateway(){
// 	    $method = $_SERVER['REQUEST_METHOD'];
// 		if($method != 'POST'){
// 		    $resp = array(
// 		        "status" => "Bad request",
//                 "statuspic_root_code" => "400",
// 		        );
// 			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
// 		} else {
// 		    $check_auth_client = $this->Login_Model->check_auth_client();
// 			if($check_auth_client == true){
// 		        $response = $this->Login_Model->auth();
// 		        if($response == 200){
		            
		            $posted['txnid'] = $this->input->post('txnid');
		            $posted['amount'] = $this->input->post('amount');
		            $posted['productinfo'] = $this->input->post('productinfo');
		            $posted['firstname'] = $this->input->post('firstname');
		            $posted['email'] = $this->input->post('email');
		            $posted['phone'] = $this->input->post('phone');
		            $posted['surl'] =  $this->input->post('surl');
		            $posted['furl'] =  $this->input->post('furl');
		            $posted['mode'] = $this->input->post('mode');
		            
		           
		            
		            if(
                    empty($posted['txnid'])
                    || empty($posted['amount'])
                    || empty($posted['firstname'])
                    || empty($posted['email'])
                    || empty($posted['phone'])
                    || empty($posted['productinfo'])){
                        $resp = array(
		                    "status" => "Bad request",
                            "statuspic_root_code" => "400",
                            "message" => "please enter fields"
		                );
			            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
                    }else{
                       // print_r($posted);die;
                       $resp =  $this->MedicalMall_model_2->payment_gateway($posted);
                        //return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
                        return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
                    }
// 		        }
// 			}
// 		}
	}
	
	public function payment_success(){
	    $this->load->view('success');
	}
	
	public function payment_failure(){
	    $this->load->view('failure');
	}
	
	public function payu(){
	    $this->load->view('payubiz');
	}
	
	function cancel_order(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
	   $order_id = $this->input->post('order_id');
	   $reason = "";
	   $reason = $this->input->post('reason');
	   if($order_id == 0 || $order_id == ""){
	        $result = array(
	            "status" => 400,
	            "message" => "please enter order_id",
            );
	    } else {
	        $result = $this->MedicalMall_model_2->cancel_order($order_id, $reason);
	    }
	   // $result = $this->MedicalMall_model->get_user_wishlist($user_id, $product_id);
	    return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
	}}}
	}
	
	// 	get brand images
	function brand_images(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		             $finalData = $this->MedicalMall_model_2->brand_images();
		             $result = array(
        	            "status" => 200,
        	            "message" => "success",
        	            "data" => $finalData
                    );
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
		        }
			}
		}
	}
	
	

// 	filters

	function get_filters(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		             $finalData = $this->MedicalMall_model_2->get_filters();
		             $result = array(
        	            "status" => 200,
        	            "message" => "success",
        	            "data" => $finalData
                    );
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
		        }
			}
		}
	}
	
	
// 		function add_filters(){
// 	    $method = $_SERVER['REQUEST_METHOD'];
// 		if($method != 'POST'){
// 		    $resp = array(
// 		        "status" => "Bad request",
//                 "statuspic_root_code" => "400",
// 		        );
// 			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
// 		} else {
		    
// 			$check_auth_client = $this->Login_Model->check_auth_client();
// 			if($check_auth_client == true){
// 		        $response = $this->Login_Model->auth();
// 		        if($response == 200){
		            
// 		            $page_no  = $this->input->post('page_no');
//             		$per_page  = $this->input->post('per_page');
            		
            		
//             		if($page_no == "" || $per_page == ""){
//     		           $resp = array (
// 	                       "status" => 400,
// 	                       "message" => "failed",
// 	                       "description" => "please enter page number(page_no) and per page(per_page) fields"
	                       
// 	                       );
// 	                   return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));   
// 	               } 
// 	               else {
	                   
	                   
// 	                    $exclude_page_entries = ($page_no - 1) * $per_page;
//         		        $next_page_entries = $exclude_page_entries + $per_page;
        		        
// 		             $filter['category_ids'] = $this->input->post('category_ids');
// 		             $filter['brands'] = $this->input->post('brands');
// 		             $filter['in_stock'] = $this->input->post('in_stock');
// 		             $filter['offers'] = $this->input->post('offers');
// 		             $filter['price_low_high'] = $this->input->post('price_low_high');
// 		             $filter['sort_a_z'] = $this->input->post('sort_a_z');
		             
// 		             $finalData = $this->MedicalMall_model_2->add_filters($filter);
		             
		             
// 		             $count = sizeof($finalData);
//                     // 	print_r($count); 
//                     // 	die();
//                     	if($count > 0){
//                     	    $last_page_no = $count / $per_page;
//                             $last_page_no = ceil($last_page_no);
                            
//                             // print_r();
                            
//                             $array1 = $finalData;
                            
//                             $array2 = array_splice($array1, $exclude_page_entries, $per_page);
//                             $data_count = sizeof($array2) ;
                           
                            
//                     		$result = array(
//                                 "status" => "true",
//                                 "statuspic_root_code" => "200",
//                                 "last_page_no" => $last_page_no,
//                                 "data_count" => $data_count,
//                                 "current_page" => $page_no,
//                                 "data" => $array2
//                             );     
//                     	} else {
//                     	    $result = array(
//                 				"status" => 400,
//                 				"message" => "Result not found"
//                 			);
//                     	}	
                			
// 		          //   $result = array(
//         	   //         "status" => 200,
//         	   //         "message" => "success",
//         	   //         "data" => $finalData
//             //         );
// 		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
// 	               }
// 		        }
// 			}
// 		}
// 	}


	function add_filters(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
		    
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		            
		            $page_no  = $this->input->post('page_no');
            		$per_page  = $this->input->post('per_page');
            		
            		
            		if($page_no == "" || $per_page == ""){
    		           $resp = array (
	                       "status" => 400,
	                       "message" => "failed",
	                       "description" => "please enter page number(page_no) and per page(per_page) fields"
	                       
	                       );
	                   return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));   
	               } 
	               else {
	                   
	                   
	                    $exclude_page_entries = ($page_no - 1) * $per_page;
        		        $next_page_entries = $exclude_page_entries + $per_page;
        		        
		             $filter['category_ids'] = $this->input->post('category_ids');
		             $filter['brands'] = $this->input->post('brands');
		             $filter['in_stock'] = $this->input->post('in_stock');
		             $filter['is_bestseller'] = $this->input->post('is_bestseller');
		             $filter['offers'] = $this->input->post('offers');
		             $filter['price_low_high'] = $this->input->post('price_low_high');
		             $filter['sort_a_z'] = $this->input->post('sort_a_z');
		             $filter['price_min'] = $this->input->post('price_min');
		             $filter['price_max'] = $this->input->post('price_max');
		             
		             
		             $finalData = $this->MedicalMall_model_2->add_filters($filter);
		             
		             
		             $count = sizeof($finalData);
                    // 	print_r($count); 
                    // 	die();
                    	if($count > 0){
                    	    $last_page_no = $count / $per_page;
                            $last_page_no = ceil($last_page_no);
                            
                            // print_r();
                            
                            $array1 = $finalData;
                            $total_count = sizeof($finalData);
                            $array2 = array_splice($array1, $exclude_page_entries, $per_page);
                            $data_count = sizeof($array2) ;
                           
                            
                    		$result = array(
                                "status" => "true",
                                "statuspic_root_code" => "200",
                                "last_page_no" => $last_page_no,
                                "data_count" => $data_count,
                                "total_count" => $total_count,
                                "current_page" => $page_no,
                                "data" => $array2
                            );     
                    	} else {
                    	    $result = array(
                				"status" => 400,
                				"message" => "Result not found"
                			);
                    	}	
                			
		          //   $result = array(
        	   //         "status" => 200,
        	   //         "message" => "success",
        	   //         "data" => $finalData
            //         );
		            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
	               }
		        }
			}
		}
	}
	
		// 	get brand categories
	function brand_categories(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		             $brand_id = $this->input->post('brand_id');
		             if($brand_id == ""){
		                $result = array(
            	            "status" => 404,
            	            "message" => "Please add brand_id",
            	           // "data" => $finalData
                        );       

		             } else {
		                $finalData = $this->MedicalMall_model_2->brand_categories($brand_id);
		                
		                 $result = array(
            	            "status" => 200,
            	            "message" => "Success",
            	            "data" => $finalData
                        );    
		             }
		          
		           
		        } else {
		            $result = array(
        	            "status" => 400,
        	            "message" => "Authentication failed",
        	          
                    );
		        }
		         return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
			}
		}
	}
	
	
	
	function brand_categories1(){
	    $method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    $resp = array(
		        "status" => "Bad request",
                "statuspic_root_code" => "400",
		        );
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		             $brand_id = $this->input->post('brand_id');
		             if($brand_id == ""){
		                $result = array(
            	            "status" => 404,
            	            "message" => "Please add brand_id",
            	           // "data" => $finalData
                        );       

		             } else {
		                $finalData = $this->MedicalMall_model_2->brand_categories1($brand_id);
		                
		                 $result = array(
            	            "status" => 200,
            	            "message" => "Success",
            	            "data" => $finalData
                        );    
		             }
		          
		           
		        } else {
		            $result = array(
        	            "status" => 400,
        	            "message" => "Authentication failed",
        	          
                    );
		        }
		         return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
			}
		}
	}

	
	
// 	get ledger

	public function get_user_ledger_details(){
	    
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    
			$resp = array('status' => 400,'message' => 'Bad request.');
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
			
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		             $user_id = $this->input->post('user_id');
		             if($user_id == "" || empty($user_id)){
		                $result = array(
            	            "status" => 404,
            	            "message" => "Please add user_id",
            	           // "data" => $finalData
                        );       

		             } else {
		                $finalData = $this->MedicalMall_model_2->get_user_ledger_details($user_id);
		                
		                 $result = array(
            	            "status" => 200,
            	            "message" => "Success",
            	            "data" => $finalData
                        );    
		             }
		          
		           
		        } else {
		            $result = array(
        	            "status" => 400,
        	            "message" => "Authentication failed",
        	          
                    );
		        }
		         return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
			}
		}	

	    
	}
	
	
		public function get_size_chart(){
	     //echo "dasd";
		$method = $_SERVER['REQUEST_METHOD'];
		if($method != 'POST'){
		    
			$resp = array('status' => 400,'message' => 'Bad request.');
			return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
			
		} else {
			$check_auth_client = $this->Login_Model->check_auth_client();
			if($check_auth_client == true){
		        $response = $this->Login_Model->auth();
		        if($response == 200){
		             $size_id = $this->input->post('size_id');
		             if($size_id == ""){
		                $result = array(
            	            "status" => 404,
            	            "message" => "Please add size_id",
            	           // "data" => $finalData
                        );       

		             } else {
		                $finalData = $this->MedicalMall_model_2->get_size_chart($size_id);
		                
		                 $result = array(
            	            "status" => 200,
            	            "message" => "Success",
            	            "data" => $finalData
                        );    
		             }
		          
		           
		        } else {
		            $result = array(
        	            "status" => 400,
        	            "message" => "Authentication failed",
        	          
                    );
		        }
		         return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($result));
			}
		}	

	    
	}
	
}