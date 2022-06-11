<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends CI_Model
{
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key       = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }

    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token    = $this->input->get_request_header('Authorizations', TRUE);
        $q        = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                ));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2018-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }
    }

    public function address_list($user_id)
    {
        $query = $this->db->query("SELECT address_id,name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' order by address_id desc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $address_id = $row['address_id'];
                $name       = $row['name'];
                $mobile     = $row['mobile'];
                $address1   = $row['address1'];
                $address2   = $row['address2'];
                $landmark   = $row['landmark'];
                $city       = $row['city'];
                $state      = $row['state'];
                $pincode    = $row['pincode'];

                $resultpost[] = array(
                    "address_id" => $address_id,
                    "name" => $name,
                    "mobile" => $mobile,
                    'address1' => $address1,
                    'address2' => $address2,
                    'landmark' => $landmark,
                    'city' => $city,
                    'state' => $state,
                    'pincode' => $pincode
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;

    }

    public function address_add($user_id, $name, $mobile, $pincode, $address1, $address2, $landmark, $city, $state)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        $address_data = array(
            'user_id' => $user_id,
            'name' => $name,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'mobile' => $mobile,
            'city' => $city,
            'state' => $state,
            'pincode' => $pincode,
            'date' => $created_at
        );
        $this->db->insert('user_address', $address_data);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function address_update($user_id, $address_id, $name, $address1, $address2, $landmark, $mobile, $city, $state, $pincode)
    {
        $query = $this->db->query("UPDATE `user_address` SET `name`='$name',`mobile`='$mobile',`address1`='$address1',`address2`='$address2',`landmark`='$landmark',`city`='$city',`state`='$state',`pincode`='$pincode' WHERE address_id='$address_id' and user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function address_delete($user_id, $address_id)
    {
        $query = $this->db->query("DELETE FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id'");
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }

    public function order_add($user_id, $listing_id, $listing_type, $listing_name, $address_id, $payment_method, $order_product_id, $order_product_price, $order_product_quantity, $order_product_name, $delivery_charge, $order_product_img, $chat_id,$product_unit,$product_unit_value,$is_night_delivery)
    {
        date_default_timezone_set('Asia/Kolkata');
        $order_date       = date('Y-m-d H:i:s');
        $invoice_no       = date("YmdHis");
        $order_status     = 'Awaiting Confirmation';
        $order_total      = '0';
        $action_by        = 'customer';
        $product_id       = explode(",", $order_product_id);
        $product_quantity = explode(",", $order_product_quantity);
        $product_price    = explode(",", $order_product_price);
        $cnt              = count($product_id);
        for ($i = 0; $i < $cnt; $i++) {
            $order_total = $order_total + ($product_price[$i] * $product_quantity[$i]);
        }

        $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        foreach ($query->result_array() as $row) {
            $name     = $row['name'];
            $mobile   = $row['mobile'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city     = $row['city'];
            $state    = $row['state'];
            $pincode  = $row['pincode'];
        }
        $user_order = array(
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'listing_name' => $listing_name,
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'payment_method' => $payment_method,
            'order_total' => $order_total,
            'delivery_charge' => $delivery_charge,
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'is_night_delivery' => $is_night_delivery
        );
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id();

        $sub_total            = '0';
        $product_status       = '';
        $product_status_type  = '';
        $product_status_value = '';
        $product_order_status = 'Awaiting Confirmation';

        $product_id       = explode(",", $order_product_id);
        $product_quantity = explode(",", $order_product_quantity);
        $product_price    = explode(",", $order_product_price);
        $product_name     = explode(",", $order_product_name);
        $product_img      = explode(",", $order_product_img);
        $product_unit      = explode(",", $product_unit);
        $product_unit_value      = explode(",", $product_unit_value);
        $cnt              = count($product_id);
        for ($i = 0; $i < $cnt; $i++) {
            $sub_total     = $product_price[$i] * $product_quantity[$i];
            $product_order = array(
                'order_id' => $order_id,
                'product_name' => $product_name[$i],
                'product_img' => str_replace('https://d2c8oti4is0ms3.cloudfront.net/images/product_images/','',$product_img[$i]),
                'product_id' => $product_id[$i],
                'product_quantity' => $product_quantity[$i],
                'product_price' => $product_price[$i],
                'sub_total' => $sub_total,
                'product_status' => $product_status,
                'product_status_type' => $product_status_type,
                'product_status_value' => $product_status_value,
                'product_unit' => $product_unit[$i],
                'product_unit_value' => $product_unit_value[$i],
                'order_status' => $product_order_status
            );

            $this->db->insert('user_order_product', $product_order);
        }
		
		
	
	define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");

	function send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$key_count,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent)
	{
        $fields = array(
            'to'                  => $reg_id ,
            'priority'            => "high",
            $agent === 'android' ? 'data' : 'notification' => array("title"=>$title,"message"=>$msg,"image"=>$img_url,"tag"=>$tag,"notification_type"=>"order","order_status"=>$order_status,"order_date"=>$order_date,"order_id"=>$order_id,"invoice_no"=>$invoice_no,"name"=>$name,"listing_name"=>$listing_name)
        );
		if($key_count=='1')
		{
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',       
			$agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
		}
		if($key_count=='2')
		{
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json', 
			$agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            
        );
		}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    if($order_id>0)
	{
	  $order_date=date('j M Y h:i A', strtotime($order_date));
	  $order_info    = $this->db->select('token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
	  $token_status  = $order_info->token_status;
	  if($token_status>0)
	  {
	    $reg_id = $order_info->token;
	    $agent = $order_info->agent;
		$msg = 'Thanks for placing order with '.$listing_name;
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
		$tag = 'text';
		$key_count='1';
		$title='Order Placed';
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$key_count,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent);
	  }

	  $partner_info    = $this->db->select('token,token_status,phone,agent')->from('users')->where('id', $listing_id)->get()->row();
	  
     $partner_token_status=$partner_info->token_status;
	  if($partner_token_status>0)
	  {	
	    $partner_phone=$partner_info->phone;
	    $reg_id = $partner_info->token;
	    $agent = $partner_info->agent;
		$msg = 'You Have Received a New General Order';
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
		$tag = 'text';
		$key_count='2';
		$title='New Order';
      		
		//web notification starts
		$pharmacy_notifications = array( 
		'listing_id' => $listing_id,
        'order_id' => $order_id, 
        'title' => $title,
        'msg' => $msg,
        'image' => $img_url,
        'notification_type' => 'order',
        'order_status' => $order_status,
        'order_date' => $order_date,           
        'invoice_no' => $invoice_no       
        );
        $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
		//web notification ends  
		
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$key_count,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent);
	  }
	  
	  $message  = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
	  $post_data = array('From' => '02233721563','To' => $partner_phone,'Body' => $message);
	  $exotel_sid = "aegishealthsolutions";
	  $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
	  $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	  $http_result = curl_exec($ch);
	  curl_close($ch);
	  
	 	   //sms same to nyla,abdul, zaheer	   
	  $message2  = 'There is new order in pharmacy store. Pharmacy Name-'.$listing_name.', Pharmacy Mobile- '.$mobile.', Order Id-'.$order_id.', Order Date-'.$order_date.'.';
	  $post_data2 = array('From' => '02233721563','To' => '9619294702,9819839008,8655328655,7506908285,8424051234','Body' => $message2);
	  $exotel_sid2 = "aegishealthsolutions";
	  $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
	  $url2 = "https://".$exotel_sid2.":".$exotel_token2."@twilix.exotel.in/v1/Accounts/".$exotel_sid2."/Sms/send";
	  $ch2 = curl_init();
      curl_setopt($ch2, CURLOPT_VERBOSE, 1);
	  curl_setopt($ch2, CURLOPT_URL, $url2);
	  curl_setopt($ch2, CURLOPT_POST, 1);
	  curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
	  curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
	  $http_result2 = curl_exec($ch2);
	  curl_close($ch2);	   
	  
	}	
		

        return array(
            'status' => 201,
            'message' => 'success',
            'order_id' => $invoice_no
        );
    }

    public function order_list($user_id, $listing_type)
    {
        if ($listing_type != '0') {
            $query = $this->db->query("select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_no,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where user_id='$user_id' and listing_type='$listing_type' order by order_id desc");
        } else {
            $query = $this->db->query("select action_by,order_id,order_type,delivery_time,listing_id,cancel_reason,action_by,delivery_charge,listing_name,listing_type,invoice_nox,chat_id,address_id,name,mobile,pincode,address1,address2,landmark,city,state,order_total,payment_method,order_date,order_status from user_order where user_id='$user_id' order by order_id desc");
        }
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_id      = $row['order_id']; 
                $order_type     = $row['order_type'];
                $delivery_time = $row['delivery_time'];
                $listing_id    = $row['listing_id'];
                $listing_name  = $row['listing_name'];
                $listing_type  = $row['listing_type'];
                $invoice_no    = $row['invoice_no'];
                $chat_id            = $row['chat_id'];
                $address_id         = $row['address_id'];
                $name               = $row['name'];
                $mobile             = $row['mobile'];
                $pincode            = $row['pincode'];
                $address1           = $row['address1'];
                $address2           = $row['address2'];
                $landmark           = $row['landmark'];
                $city               = $row['city'];
                $state              = $row['state']; 
                $action_by              = $row['action_by'];
								
                $payment_method     = $row['payment_method'];
                $order_date  = $row['order_date'];
                $order_date = date('j M Y h:i A', strtotime($order_date));
                $delivery_charge    = $row['delivery_charge'];
                $order_status       = $row['order_status'];
				$order_type  = $row['order_type'];	
				$action_by  = $row['action_by'];
				if($action_by=='vendor')
				{
				$cancel_reason  = $row['cancel_reason'];
				}
				else
				{
				$cancel_reason ='';
				}		


       $user_info      = $this->db->query("SELECT name,phone FROM users WHERE id='$user_id'");
	   $getuser_info   = $user_info->row_array();
       $user_name     = $getuser_info['name'];
       $user_mobile   = $getuser_info['phone'];

				
                $product_resultpost=array();
				$prescription_result=array();
				
				
				
				if($order_type=='order')
				{
				$order_total='0';
				$product_query      = $this->db->query("select id as product_order_id,product_unit,product_unit_value,product_id,product_name,product_img,product_quantity,product_discount,product_price,sub_total,product_status,product_status_type,product_status_value,order_status from user_order_product where order_id='$order_id' order by product_order_id asc");
                $product_count      = $product_query->num_rows();
                if($product_count > 0) {				
				foreach ($product_query->result_array() as $product_row) {
                $product_order_id  = $product_row['product_order_id'];
                $product_id  = $product_row['product_id'];
				$product_name  = $product_row['product_name'];
                $product_img  = "https://d2c8oti4is0ms3.cloudfront.net/images/product_images/".$product_row['product_img'];
                $product_quantity  = $product_row['product_quantity'];
                $product_discount  = $product_row['product_discount'];
                $product_price  = $product_row['product_price'];
                $product_unit  = $product_row['product_unit'];
                $product_unit_value  = $product_row['product_unit_value'];
                $sub_total  = $product_row['sub_total'];
                $product_status  = $product_row['product_status'];
                $product_status_type  = $product_row['product_status_type'];
                $product_status_value  = $product_row['product_status_value'];
                $product_order_status = $product_row['order_status'];
				
				$order_total=$order_total+($product_quantity*$product_price);
				
				$product_resultpost[] = array(
				"product_order_id" => $product_order_id,
                "product_id" => $product_id,
				"product_name" => $product_name,
				"product_img" => $product_img,
                "product_quantity" => $product_quantity,
                "product_price" => $product_price,
                "product_unit" => $product_unit,
                "product_unit_value" => $product_unit_value,
                "product_discount" => $product_discount,
                "sub_total" => $sub_total,
                "product_status" => $product_status,
                "product_status_type" => $product_status_type,
                "product_status_value" => $product_status_value,
                "product_order_status" => $product_order_status
				);
				}
				}
                else
                {
                    $product_resultpost=array();
                }
				}
				else
				{
				$order_total='0';
				$product_query      = $this->db->query("SELECT id as product_order_id, order_status,prescription_image FROM prescription_order_details WHERE order_id='$order_id' order by product_order_id asc");
                $product_count      = $product_query->num_rows();	
				if($product_count > 0) {				
			    foreach ($product_query->result_array() as $product_row) {
                $product_order_id  = $product_row['product_order_id'];
                $product_id  = $product_row['product_order_id'];
				$product_name  = '';
				$prescription_image='';
				$product_img  = $product_row['prescription_image'];
                $product_quantity  = '';
                $product_price  = '';
                $sub_total  = '';
                $product_status  = '';
                $product_status_type  = '';
                $product_status_value  = '';
                $product_order_status = $product_row['order_status'];
				
				$product_resultpost[] = array(
				"product_order_id" => $product_order_id,
                "product_id" => $product_id,
				"product_name" => $product_name,
				"product_img" => "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/".$product_img,
                "product_quantity" => $product_quantity,
                "product_price" => $product_price,
                "product_discount" => '0',
                "sub_total" => $sub_total,
                "product_status" => $product_status,
                "product_status_type" => $product_status_type,
                "product_status_value" => $product_status_value,
                "product_order_status" => $product_order_status
				);
				}
				
				
				$prescription_query      = $this->db->query("SELECT * FROM `prescription_order_list` where order_id='$order_id' order by id desc");
                $prescription_count      = $prescription_query->num_rows();
				if($prescription_count > 0) {				
				foreach ($prescription_query->result_array() as $prescription_row) {				
				$prescription_name  = $prescription_row['prescription_name'];
				$prescription_quantity  = $prescription_row['prescription_quantity'];
				$prescription_price  = $prescription_row['prescription_price'];
				$prescription_discount  = $prescription_row['prescription_discount'];
				$prescription_status  = $prescription_row['prescription_status'];
				
				$prescription_result[] = array(
				"prescription_name" => $prescription_name,
                "prescription_quantity" => $prescription_quantity,
				"prescription_price" => $prescription_price,
				"prescription_discount" => $prescription_discount,
                "prescription_status" => $prescription_status
				);
				}
				}
				}				
				}
				

                $resultpost[] = array(
                "order_id" => $order_id,
                "delivery_time" => str_replace('null','',$delivery_time),
                "order_type" => $order_type,
                "listing_id" => $listing_id,
                "listing_name" => $listing_name,
                "listing_type" => $listing_type,
                "invoice_no" => $invoice_no,
				"chat_id" => $chat_id,
                "address_id" => $address_id,
                "name" => $name,
                "mobile" => $mobile,
                "pincode" => $pincode,
                "address1" => $address1,
                "address2" => $address2,
                "landmark" => $landmark,
                "city" => $city,
                "state" => $state,
                "user_name" => $user_name,
                "user_mobile" => $user_mobile,
                "order_total" => $order_total,
                "payment_method" => $payment_method,
                "order_date" => $order_date,
                "order_status" => $order_status,
				"cancel_reason" => $cancel_reason,
                "delivery_charge" => $delivery_charge,
				"product_order" => $product_resultpost,
				"prescription_order" => $prescription_result,
				"action_by" => $action_by
				
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }

	public function prescription_add($user_id,$listing_id,$address_id,$listing_name,$listing_type,$chat_id,$payment_method,$delivery_charge,$is_night_delivery)
	{
	    date_default_timezone_set('Asia/Kolkata');
        $order_date       = date('Y-m-d H:i:s');
        $invoice_no       = date("YmdHis");
        $order_status     = 'Awaiting Confirmation';
        $action_by        = 'customer';
        $product_order_status = 'Awaiting Confirmation';

        $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        foreach($query->result_array() as $row) {
            $name     = $row['name'];
            $mobile   = $row['mobile'];
            $address1 = $row['address1'];
            $address2 = $row['address2'];
            $landmark = $row['landmark'];
            $city     = $row['city'];
            $state    = $row['state'];
            $pincode  = $row['pincode'];
        }
        
		$user_order = array(
            'user_id' => $user_id,
            'order_type' => 'prescription',
            'listing_id' => $listing_id,
            'listing_name' => $listing_name,
            'listing_type' => $listing_type,
            'invoice_no' => $invoice_no,
            'chat_id' => $chat_id,
            'address_id' => $address_id,
            'name' => $name,
            'mobile' => $mobile,
            'pincode' => $pincode,
            'address1' => $address1,
            'address2' => $address2,
            'landmark' => $landmark,
            'city' => $city,
            'state' => $state,
            'payment_method' => $payment_method,
            'order_total' => '0',
            'delivery_charge' => '0',
            'order_date' => $order_date,
            'order_status' => $order_status,
            'action_by' => $action_by,
            'delivery_charge' => $delivery_charge,
            'is_night_delivery' => $is_night_delivery   
        );
        $this->db->insert('user_order', $user_order);
        $order_id = $this->db->insert_id();


	define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");

	function send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$key_count,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent)
	{

        $fields = array(
            'to'                  => $reg_id ,
            'priority'            => "high",
             $agent === 'android' ? 'data' : 'notification' => array("title"=>$title,"message"=>$msg,"image"=>$img_url,"tag"=>$tag,"notification_type"=>"prescription","order_status"=>$order_status,"order_date"=>$order_date,"order_id"=>$order_id,"invoice_no"=>$invoice_no,"name"=>$name,"listing_name"=>$listing_name)
        );
		if($key_count=='1')
		{
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
             $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
		}
		if($key_count=='2')
		{
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
             $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
		}
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
    }

    if($order_id>0)
	{
	  $order_date=date('j M Y h:i A', strtotime($order_date));
	  $order_info    = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
	  $token_status  = $order_info->token_status;
	  if($token_status>0)
	  {
	    $reg_id = $order_info->token;
	    $agent = $order_info->agent;
		$msg = 'Thanks uploading your prescription with '.$listing_name;
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
		$tag = 'text';
		$key_count='1';
		$title='Order Placed';
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$key_count,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent);
	  }

	  $partner_info    = $this->db->select('token, agent, token_status,phone')->from('users')->where('id', $listing_id)->get()->row();
	  $partner_token_status=$partner_info->token_status;
	  $partner_phone=$partner_info->phone;
	  if($partner_token_status>0)
	  {
	    $reg_id = $partner_info->token;	
	    $agent = $partner_info->agent;
		$msg = 'You Have Received a New Prescription Order';
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
		$tag = 'text';
		$key_count='2';
		$title='New Order';		
		
		//web notification starts
		$pharmacy_notifications = array( 
		'listing_id' => $listing_id,
		'order_id' => $order_id, 
        'title' => $title,
        'msg' => $msg,
        'image' => $img_url,
        'notification_type' => 'prescription',
        'order_status' => $order_status,
        'order_date' => $order_date,           
        'invoice_no' => $invoice_no       
        );
        $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
		//web notification ends  
		
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$key_count,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent);
	  }
	  //sms same as order
	  
	  $message  = 'You have received an order, kindly login to https://pharmacy.medicalwale.com';
	  $post_data = array('From' => '02233721563','To' => $partner_phone,'Body' => $message);
	  $exotel_sid = "aegishealthsolutions";
	  $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
	  $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	  $http_result = curl_exec($ch);
	  curl_close($ch);
	  
	 	   //sms same to nyla,abdul, zaheer	   
	  $message2  = 'There is new order in pharmacy store. Pharmacy Name-'.$listing_name.', Pharmacy Mobile- '.$mobile.', Order Id-'.$order_id.', Order Date-'.$order_date.'.';
	  $post_data2 = array('From' => '02233721563','To' => '9619294702,9819839008,8655328655,7506908285,8424051234','Body' => $message2);
	  $exotel_sid2 = "aegishealthsolutions";
	  $exotel_token2 = "a642d2084294a21f0eed3498414496229958edc5";
	  $url2 = "https://".$exotel_sid2.":".$exotel_token2."@twilix.exotel.in/v1/Accounts/".$exotel_sid2."/Sms/send";
	  $ch2 = curl_init();
      curl_setopt($ch2, CURLOPT_VERBOSE, 1);
	  curl_setopt($ch2, CURLOPT_URL, $url2);
	  curl_setopt($ch2, CURLOPT_POST, 1);
	  curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch2, CURLOPT_FAILONERROR, 0);
	  curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($ch2, CURLOPT_POSTFIELDS, http_build_query($post_data2));
	  $http_result2 = curl_exec($ch2);
	  curl_close($ch2);	   
	  
	  
	  
	}

	return $order_id;
	}

    public function order_confirm_cancel($order_id, $type, $order_status, $cancel_reason)
    {
        date_default_timezone_set('Asia/Kolkata');     
		$date=date('Y-m-d');
		$updated_at = date('Y-m-d H:i:s');
		
	  $order_type_query   = $this->db->query("select order_type from user_order where order_id='$order_id' ");
      $get_order_info=$order_type_query->row_array();
	  $order_type=$get_order_info['order_type']; 	 
		
		
		function send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent,$order_type)
		{	           	
      if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to'                  => $reg_id ,
            'priority'             => "high",
             $agent === 'android' ? 'data' : 'notification'  => array("title" => $title, "message" => $msg, "image"=> $img_url, "tag" => $tag,"notification_type" => $order_type,"order_status"=>$order_status,"order_date"=>$order_date,"order_id"=>$order_id,"invoice_no"=>$invoice_no,"name"=>$name,"listing_name"=>$listing_name)
        );
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
             $agent === 'android' ? 'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        //echo $result;
       }	
		
	


		function send_gcm_notify_usr($title,$reg_id,$msg,$img_url,$tag,$order_status,$order_date,$order_id,$invoice_no,$name,$listing_name,$agent,$order_type)
		{
	   if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
         $fields = array(
            'to'                  => $reg_id ,
            'priority'             => "high",
             $agent === 'android' ? 'data' : 'notification'  => array("title" => $title, "message" => $msg, "image"=> $img_url, "tag" => $tag,"notification_type" => $order_type,"order_status"=>$order_status,"order_date"=>$order_date,"order_id"=>$order_id,"invoice_no"=>$invoice_no,"name"=>$name,"listing_name"=>$listing_name)
        );
		$headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
             $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        //echo $result;
       }		
		
				
	if($type=='Order Confirmed')
	{
	  $update=$this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Confirmed',`cancel_reason`='',`action_by`='customer' WHERE order_id='$order_id'");
	  $updated_at=date('j M Y h:i A', strtotime($updated_at));
	
      $res_order   = $this->db->query("select user_id,listing_id,invoice_no,name,listing_name from user_order where order_id='$order_id' limit 1");
      $order_info=$res_order->row_array();
	  $user_id=$order_info['user_id']; 	  
	  $listing_id=$order_info['listing_id'];
	  $invoice_no=$order_info['invoice_no'];
	  $name=$order_info['name'];
	  $listing_name=$order_info['listing_name'];
	  $updated_at=date('j M Y h:i A', strtotime($updated_at));
	  
	  
	  //user notify starts
	  $order_info    = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
	  $token_status  = $order_info->token_status;
	  if($token_status>0)
	  {
	    $reg_id = $order_info->token;
	    $agent = $order_info->agent;		
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
		$tag = 'text';
		$title='Order Confirmed ';
		$msg = 'Your order has been confirmed';
		send_gcm_notify_usr($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent,$order_type);
	  }	  
	  //user notify ends
	  
	   
	  
	  $res_token   = $this->db->query("select token,token_status,agent,phone from users where id='$listing_id' limit 1");
	  $token_value=$res_token->row_array();
	  $token_status=$token_value['token_status'];
	  $partner_phone=$token_value['phone'];
	  if($token_status>0)
	  {
	    $reg_id = $token_value['token'];		
	    $agent = $token_value['agent'];		
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
		$tag = 'text';
		$title='Order Confirmed';
		$msg = 'Kindly deliver the order';
		
		//web notification starts
		$pharmacy_notifications = array(
		'listing_id' => $listing_id,
		'order_id' => $order_id, 
        'title' => $title,
        'msg' => $msg,
        'image' => $img_url,
        'notification_type' => $order_type,
        'order_status' => $order_status,
        'order_date' => $updated_at,           
        'invoice_no' => $invoice_no       
        );
        $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
		//web notification ends  
		
		
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent,$order_type);
	  }
	  
	  
	  $message  = 'Order confirmed from the customer, Kindly deliver the order.';
	  $post_data = array('From' => '02233721563','To' => $partner_phone,'Body' => $message);
	  $exotel_sid = "aegishealthsolutions";
	  $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
	  $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	  $http_result = curl_exec($ch);
	  curl_close($ch);

     	return array(
        'status' => 201,
        'message' => 'Order Confirmed'
        );
}

if($type=='Order Cancelled')
{
$res_status  =$this->db->query("select order_status from user_order where order_id='$order_id' limit 1"); 
$o_status=$res_status->row_array();
$check_status=$o_status['order_status'];
if($check_status=='Order Delivered')
{
return array(
        'status' => 201,
       'message' => 'Order Delivered'
	   );
}
else
{
$update= $this->db->query("UPDATE `user_order` SET `updated_at`='$updated_at',`order_status`='Order Cancelled',`cancel_reason`='$cancel_reason',`action_by`='customer' WHERE order_id='$order_id'");

if($update)
{
	   
      $res_order   = $this->db->query("select listing_id,user_id,invoice_no,name,listing_name from user_order where order_id='$order_id' limit 1");  
	  $order_info=$res_order->row_array();
	  $user_id=$order_info['user_id']; 
	  $listing_id=$order_info['listing_id'];
	  $invoice_no=$order_info['invoice_no'];
	  $name=$order_info['name'];
	  $listing_name=$order_info['listing_name'];
      $updated_at=date('j M Y h:i A', strtotime($updated_at));
	  
		  
	  //user notify starts
	  $order_info    = $this->db->select('token, agent, token_status')->from('users')->where('id', $user_id)->get()->row();
	  $token_status  = $order_info->token_status;
	  if($token_status>0)
	  {
	    $reg_id = $order_info->token;
	    $agent = $order_info->agent;		
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
		$tag = 'text';
		$title='Order Cancelled ';
		$msg = 'Your order has been cancelled';
		send_gcm_notify_usr($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent,$order_type);
	  }	  
	  //user notify ends
	  	  
	  
	  
	  $res_token   =$this->db->query("select token,token_status,agent,phone from users where id='$listing_id' limit 1"); 
	  $token_value=$res_token->row_array();
	  $token_status=$token_value['token_status'];
	  $partner_phone=$token_value['phone'];
	  if($token_status>0)
	  {
	    $reg_id = $token_value['token'];		
	    $agent = $token_value['agent'];		
		$img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
		$tag = 'text';
		$title='Order Cancelled';
		$msg = 'You order has been cancelled';
		
		
		//web notification starts
		$pharmacy_notifications = array( 
		'listing_id' => $listing_id,
		'order_id' => $order_id, 
        'title' => $title,
        'msg' => $msg,
        'image' => $img_url,
        'notification_type' => $order_type,
        'order_status' => $order_status,
        'order_date' => $updated_at,           
        'invoice_no' => $invoice_no       
        );
        $this->db->insert('pharmacy_notifications', $pharmacy_notifications);
		//web notification ends  
		
		send_gcm_notify($title,$reg_id,$msg,$img_url,$tag,$order_status,$updated_at,$order_id,$invoice_no,$name,$listing_name,$agent,$order_type);

	  }
	  
	  
	  $message  = 'Order cancelled, You order has been cancelled.';
	  $post_data = array('From' => '02233721563','To' => $partner_phone,'Body' => $message);
	  $exotel_sid = "aegishealthsolutions";
	  $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
	  $url = "https://".$exotel_sid.":".$exotel_token."@twilix.exotel.in/v1/Accounts/".$exotel_sid."/Sms/send";
	  $ch = curl_init();
      curl_setopt($ch, CURLOPT_VERBOSE, 1);
	  curl_setopt($ch, CURLOPT_URL, $url);
	  curl_setopt($ch, CURLOPT_POST, 1);
	  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	  curl_setopt($ch, CURLOPT_FAILONERROR, 0);
	  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
	  $http_result = curl_exec($ch);
	  curl_close($ch);
	  
return array(
        'status' => 201,
       'message' => 'Order Cancelled'
	   );	  
	  
	  
}
else
{
return array(
        'status' => 201,
       'message' => 'failed'
	   );
}
}
}
}
    
    
}
