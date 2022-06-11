<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OrderModel extends CI_Model
{
    public function order_add($user_id,$cart_data,$address_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date_created = date('Y-m-d H:i');
        $grand_total = 0;
        $cart_arr=array();
        if ($user_id>0) {
            foreach ($cart_data as $list) {
                $grand_total=(float)$grand_total+(float)$list['price'];
            }
            $invoice_no=date('YmdHis');
            $order_type = "Bulkorder";
            $order_date = date('Y-m-d H:i:s');
            $email_id = '';
            $lat = '';
            $lng = '';
            $address1 = '';
            $address2 = '';
            $landmark = '';
            $city = '';
            $state = '';
            
            $chat_id = "user".$user_id;
            
            if($address_id>0){
                $query1 = $this->db->query("SELECT address1,address2,landmark,city,state FROM user_address WHERE id='$address_id'");
                if ($query1->num_rows()>0) {
                   $row2   = $query1->row_array();
                   $address1 = $row2['address1'];
                   $address2 = $row2['address2'];
                   $landmark = $row2['landmark'];
                   $city = $row2['city'];
                   $state = $row2['state'];
                }
            }
            $address1 = $cart_data[0]['address1'];
            if ($address1!="") {
                $address1 = $cart_data[0]['address1'];
                $address2 = $cart_data[0]['address2'];
                $city_id  = $cart_data[0]['city'];
                $state_id = $cart_data[0]['state'];
                $landmark = $cart_data[0]['landmark'];
                
                $query1 = $this->db->query("SELECT name FROM city_list WHERE id='$city_id' limit 1");
                if ($query1->num_rows()>0) {
                   $row2   = $query1->row_array();
                   $city = $row2['name'];
                }

                $query1 = $this->db->query("SELECT name FROM state_list WHERE id='$state_id' limit 1");
                if ($query1->num_rows()>0) {
                   $row2   = $query1->row_array();
                   $state = $row2['name'];
                }
                
                $location = $address1 . " " . $address2 . " " . $city . " " . $state . " ";
                $latlong  = $this->get_lat_lng($location);
                
                $lat = $latlong['customer_lat'];
                $lng = $latlong['customer_lng'];
                
                if (empty($address_id)) {
                    $name    = $cart_data[0]['name'];
                    $phone   = $cart_data[0]['phone'];
                    $pincode  = $cart_data[0]['pincode'];
                    $landmark = $cart_data[0]['landmark'];
                    $full_address = $address1.",".$address2.",".$city.",".$state;
                    $address_data = array(
                        'user_id' => $user_id,
                        'name' => $name,
                        'address1' => $address1,
                        'address2' => $address2,
                        'landmark' => $landmark,
                        'mobile' => $phone,
                        'city' => $city,
                        'state' => $state,
                        'pincode' => $pincode,
                        'date' => $order_date,
                        'full_address' => $full_address,
                        'lat' => $lat,
                        'lng' => $lng
                    );
                    $this->db->insert('user_address', $address_data);
                    $address_id = $this->db->insert_id();
                }
            }
            
            $query1 = $this->db->query("SELECT email,name,phone FROM users WHERE id='$user_id' limit 1");
            if ($query1->num_rows()>0) {
               $row2   = $query1->row_array();
               $email_id = $row2['email'];
               $name_ = $row2['name'];
               $phone_ = $row2['phone'];
            }
            
            $gst_no = '';
            
            foreach ($cart_data as $list) {
                if (!in_array($list['user_id'], $cart_arr)) {
                    $cart_arr[]     = $list['user_id'];
                    $delivery_type = $list['delivery_type'];
                    if($delivery_type=='delivery'){
                        $delivery_type ='Self';
                        $address1 = $list['address1'];
                        $name     = $list['name'];
                        $phone    = $list['phone'];
                    }
                    else{
                        $delivery_type ='Pickup';
                        $address1 ='Self Pickup' ;
                        $name =$name_;
                        $phone =$phone_;
                    }
                    $order_data  = array(
                        'invoice_no' => $invoice_no,
                        'order_type' => $order_type,
                        'order_status' => "Confirmation",
                        'listing_id' => $list['user_id'],
                        'listing_name' => $list['distributor'],
                        'email' => $email_id,
                        'listing_type' => $list['vendor_type'],
                        'user_id' => $user_id,
                        'lat' => $lat,
                        'lng' => $lng,
                        'address_id' => $address_id,
                        'name' => $name,
                        'mobile' => $phone,
                        'pincode' => $list['pincode'],
                        'chat_id' => $chat_id,
                        'address1' => $address1,
                        'address2' => $list['address2'],
                        'landmark' => $list['landmark'],
                        'order_total' => $grand_total,
                        'city' => $city,
                        'state' => $state,
                        'gst_no' => '',
                        'order_date' => $order_date,
                        'action_by' => '',
                        'order_generate_from' => "Supplymarket.in",
                        'order_deliver_by' => $delivery_type,
                        'created_by' => $user_id,
                        'staff_user_id' => 0,
                        'change_user_id' => 0
                    );
                    $check_order = $this->db->insert('inven_distrub_po', $order_data);
                    $order_id    = $this->db->insert_id();
                    
                    foreach ($cart_data as $product_list) {
                        if ($product_list['user_id']==$list['user_id']) {
                            $product_data = array(
                                'user_id' => $user_id,
                                'order_id' => $order_id,
                                'product_id' =>  $product_list['id'],
                                'product_qty' => $product_list['quantity'],
                                'vendor_id' => $product_list['user_id'],
                                'remark' => '',
                                'price'=> $product_list['price'],
                                'product_mrp' => $product_list['mrp'],
                                'sku' => $product_list['sku_code'],
                            );
                            $this->db->insert('inven_distrub_po_details', $product_data);
                            $this->db->insert_id(); 
                        }
                    }
                    $distributor_name=$list['distributor'];
                    $duser_id     = $list['user_id'];
                    
                    $query2 = $this->db->query("SELECT web_token FROM `users` WHERE `id` = '$duser_id' limit 1");
                    $row2   = $query2->row_array();
                    $fcm_token  = $row2['web_token'];
                    
                    $title='Order Placed';
                    $msg='Order #'.$invoice_id.' has been placed successfully to '.$distributor_name.'.';
                    $agent='web';
                    $token=$fcm_token;
                    //$res=$this->common_model->send_notification_to_delivery($title, $token, $msg, $agent);
                }
                
                if ($order_id) {
                    $resultpost = array(
                        "status" => 200,
                        "message" => 'success'
                    );
                }
                else{
                    $resultpost = array(
                        "status" => 400,
                        "message" => 'failure',
                    );
                }
            }
        } else {
            $resultpost = array(
                "status" => 400,
                "message" => 'failure',
            );
        }
        return $resultpost;
    }
    
    public function get_lat_lng($address)
    {
        // default eco space 
        $lat     = "19.126979";
        $long    = "72.849979";
        $pincode = "400053";
        
        $address = str_replace(" ", "+", $address);
        
        $json = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=$address&key=AIzaSyCMHN__LczEziaYuKXQ-SKdiMfx5AiY66o");
        
        
        $json = json_decode($json);
        
        if (sizeof($json->results) > 0) {
            $lat  = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lat'};
            $long = $json->{'results'}[0]->{'geometry'}->{'location'}->{'lng'};
            
            if (sizeof($json->results) > 1) {
                $address_components = $json->{'results'}[1]->{'address_components'};
                foreach ($address_components as $ac) {
                    if (sizeof($ac->{'types'}) > 0 && $ac->{'types'}[0] == 'postal_code') {
                        $pincode = $ac->{'short_name'};
                    }
                }
            }
            
        }
        $data['customer_lat']     = $lat;
        $data['customer_lng']     = $long;
        $data['customer_pincode'] = $pincode;
        return $data;
    }
    
    

    public function order_list($user_id,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT order_id,invoice_no,order_status,listing_id,listing_name,order_deliver_by,order_date FROM `inven_distrub_po` where user_id='$user_id' order by order_id desc  LIMIT $start, $limit");
        
        $count_query = $this->db->query("SELECT order_id FROM `inven_distrub_po` where user_id='$user_id'");
        
        $count = $count_query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $order_id  = $row['order_id'];
            $invoice_no  = $row['invoice_no'];
            $order_status  = $row['order_status'];
            $listing_id  = $row['listing_id'];
           // $listing_name  = $row['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($row['listing_id']);
            $order_total  = '';
            $delivery_type  = $row['order_deliver_by'];
            $order_date  = $row['order_date'];
            
            $total_items=0;
            $c_query = $this->db->query("SELECT id FROM `inven_distrub_po_details` where order_id='$order_id' and user_id='$user_id'");
            $total_items = $c_query->num_rows();
            
            $data[] = array(
                "order_id" => $order_id,
                "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_id" => $listing_id,
                "total_items" => $total_items,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'count' => $count
        );
        return $resultpost;
    }
    
    public function order_list2($user_id,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT order_id,invoice_no,order_status,listing_id,listing_name,order_total,delivery_type,order_date FROM `inven_distrub_invoice` where user_id='$user_id' order by order_id desc  LIMIT $start, $limit");
        
        $count_query = $this->db->query("SELECT order_id FROM `inven_distrub_invoice` where user_id='$user_id'");
        
        $count = $count_query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $order_id  = $row['order_id'];
            $invoice_no  = $row['invoice_no'];
            $order_status  = $row['order_status'];
            $listing_id  = $row['listing_id'];
           // $listing_name  = $row['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($row['listing_id']);
            $order_total  = $row['order_total'];
            $delivery_type  = $row['delivery_type'];
            $order_date  = $row['order_date'];
            
            $total_items=0;
            $c_query = $this->db->query("SELECT id FROM `inven_distrub_invoice_details` where order_id='$order_id' and user_id='$user_id'");
            $total_items = $c_query->num_rows();
            
            $data[] = array(
                "order_id" => $order_id,
                "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_id" => $listing_id,
                "total_items" => $total_items,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'count' => $count
        );
        return $resultpost;
    }
    

    public function order_details($user_id,$order_id)
    {
        $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,order_total,state,order_id,invoice_no,order_status,listing_id,listing_name,order_deliver_by,order_date FROM `inven_distrub_po` where user_id='$user_id' and order_id='$order_id' limit 1");
        $data  = array();
        
        if($query->num_rows()>0){
            $row=$query->row_array();
            $order_id  = $row['order_id'];
            $invoice_no  = $row['invoice_no'];
            $order_status  = $row['order_status'];
            $listing_id  = $row['listing_id'];
           // $listing_name  = $row['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($row['listing_id']);
            $order_total  = $row['order_total'];
            $delivery_type  = $row['order_deliver_by'];
            $order_date  = $row['order_date'];
            
            $name  = $row['name'];
            $mobile  = $row['mobile'];
            $pincode  = $row['pincode'];
            $address1  = $row['address1'];
            $address2  = $row['address2'];
            $landmark  = $row['landmark'];
            $city_id  = $row['city'];
            $state_id  = $row['state'];
            
            $c_query = $this->db->query("SELECT state_name FROM `states` where state_id='$state_id' limit 1");
            $prow    = $c_query->row_array();
            $state   = $prow['state_name'];
            
            $c_query = $this->db->query("SELECT city_name FROM `cities` where city_id='$city_id' limit 1");
            $prow    = $c_query->row_array();
            $city    = $prow['city_name'];
            
            $p_query = $this->db->query("SELECT id,product_id,product_qty,price,product_mrp FROM `inven_distrub_po_details` where user_id='$user_id' and order_id='$order_id'");
            $p_array = array();
            if($p_query->num_rows()>0){
                foreach ($p_query->result_array() as $rows) {
                    $id  = $rows['id'];
                    $product_id  = $rows['product_id'];
                    $product_qty  = $rows['product_qty'];
                    $price  = $rows['price'];
                    $product_mrp  = $rows['product_mrp'];
                   
                    $table_='dstock_'.$listing_id;
                    $c_query = $this->db->query("SELECT product_name,hsncode FROM $table_ where product_id='$product_id' limit 1");
                    $prow   = $c_query->row_array();
                    $product_name  = $prow['product_name'];
                    $product_hsn  = $prow['hsncode'];
                    
                    $p_array[] = array(
                        "id" => $id,
                        "product_id" => $product_id,
                        "product_qty" => $product_qty,
                        "price" => $price,
                        "product_mrp" => $product_mrp,
                        "product_name" => $product_name,
                    );
                }
            }
            
            $data[] = array(
                "order_id" => $order_id,
                "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_id" => $listing_id,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date,
                "name" => $name,
                "mobile" => $mobile,
                "pincode" => $pincode,
                "address1" => $address1,
                "address2" => $address2,
                "landmark" => $landmark,
                "city" => $city,
                "state" => $state,
                "product_list" => $p_array
            );
        }
        
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    
    
    
    
    
    
public function export_order_list($user_id,$filter)
    {
        
        $query = $this->db->query("SELECT order_id,name,mobile,pincode,address1,address2,landmark,order_total,state,order_id,invoice_no,order_status,listing_id,listing_name,order_deliver_by,order_date FROM `inven_distrub_po` WHERE user_id='$user_id' order by order_id desc");
         
        $resultpost        = array();
        if($query->num_rows()>0){
        foreach ($query->result_array() as $order) {
			$order_id  = $order['order_id'];
			$invoice_no  = $order['invoice_no'];
            $order_status  = $order['order_status'];
            $listing_id  = $order['listing_id'];
            //$listing_name  = $order['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($order['listing_id']);
            $order_total  = $order['order_total'];
            $delivery_type  = $order['order_deliver_by'];
            $order_date  = $order['order_date'];
            
            $name  = $order['name'];
            $mobile  = $order['mobile'];
            $pincode  = $order['pincode'];
            $address1  = $order['address1'];
            $address2  = $order['address2'];
            $landmark  = $order['landmark'];
            $city_id  = $order['city'];
            $state_id  = $order['state'];
            
            $c_query1 = $this->db->query("SELECT state_name FROM `states` where state_id='$state_id' limit 1");
            $prow    = $c_query1->row_array();
            $state   = $prow['state_name'];
            
            $c_query2 = $this->db->query("SELECT city_name FROM `cities` where city_id='$city_id' limit 1");
            $prow    = $c_query2->row_array();
            $city    = $prow['city_name'];
            
           $c_query = $this->db->query("SELECT user_id,order_id,product_id,product_qty,price,product_mrp FROM `inven_distrub_po_details` WHERE order_id='$order_id'");
       
	   
          foreach ($c_query->result_array() as $row) {  
  		    $product_id     = $row['product_id'];
            $product_qty    = $row['product_qty'];
            $product_price  = $row['price']; 

			$c_sql = $this->db->query("SELECT product_name FROM `product` where id='$product_id' limit 1");
			$prod   = $c_sql->row_array();
			$product_name  = $prod['product_name'];  
			
            $resultpost[] = array(
		        "order_id" => $order_id,
		        "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date,
                "name" => $name,
                "mobile" => $mobile,
                "pincode" => $pincode,
                "address1" => $address1,
                "address2" => $address2,
                "landmark" => $landmark,
                "state" => $state,
                "city" => $city,
                "product_name" => $product_name ,
                "product_qty" => $product_qty,
                "product_price" => $product_price,
             );
		   }			
         }
        }

        return $resultpost;
    }
    
    
  

    public function order_invoice_list($user_id,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT order_id,invoice_no,invoice_type,order_status,listing_id,listing_name,order_deliver_by,order_date FROM `inven_distrub_invoice` where user_id='$user_id' order by order_id desc  LIMIT $start, $limit");
        
        $count_query = $this->db->query("SELECT order_id FROM `inven_distrub_invoice` where user_id='$user_id'");
        
        $count = $count_query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $order_id  = $row['order_id'];
            $invoice_no  = $row['invoice_no'];
            $order_status  = $row['order_status'];
            $listing_id  = $row['listing_id'];
            $invoice_type  = $row['invoice_type'];
            //$listing_name  = $row['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($row['listing_id']);
            $order_total  = '';
            $delivery_type  = $row['order_deliver_by'];
            $order_date  = $row['order_date'];
            
            $total_items=0;
            $c_query = $this->db->query("SELECT id FROM `inven_distrub_invoice_details` where order_id='$order_id' and user_id='$user_id'");
            $total_items = $c_query->num_rows();
            
            $data[] = array(
                "order_id" => $order_id,
                "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_id" => $listing_id,
                "invoice_type" => $invoice_type,
                "total_items" => $total_items,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'count' => $count
        );
        return $resultpost;
    }
     

    public function order_invoice_details($user_id,$order_id)
    {
        $query = $this->db->query("SELECT name,invoice_type,mobile,pincode,address1,address2,landmark,order_total,city,state,order_id,invoice_no,order_status,listing_id,listing_name,order_deliver_by,order_date FROM `inven_distrub_invoice` where user_id='$user_id' and order_id='$order_id' limit 1");
        $data  = array();
        
        if($query->num_rows()>0){
            $row=$query->row_array();
            $order_id  = $row['order_id'];
            $invoice_no  = $row['invoice_no'];
            $order_status  = $row['order_status'];
            $listing_id  = $row['listing_id'];
            $invoice_type  = $row['invoice_type'];
            //$listing_name  = $row['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($row['listing_id']);
            $order_total  = $row['order_total'];
            $delivery_type  = $row['order_deliver_by'];
            $order_date  = $row['order_date'];
            
            $name  = $row['name'];
            $mobile  = $row['mobile'];
            $pincode  = $row['pincode'];
            $address1  = $row['address1'];
            $address2  = $row['address2'];
            $landmark  = $row['landmark'];
            $city_id  = $row['city'];
            $state_id  = $row['state'];
            
            $c_query = $this->db->query("SELECT state_name FROM `states` where state_id='$state_id' limit 1");
            $prow    = $c_query->row_array();
            $state   = $prow['state_name'];
            
            $c_query = $this->db->query("SELECT city_name FROM `cities` where city_id='$city_id' limit 1");
            $prow    = $c_query->row_array();
            $city    = $prow['city_name'];
            
            $p_query = $this->db->query("SELECT id,product_id,product_qty,price,product_mrp,gst,discount,calculateby,gst_amount FROM `inven_distrub_invoice_details` where user_id='$user_id' and order_id='$order_id'");
            $p_array = array();
            if($p_query->num_rows()>0){
                $gross = 0;
                $total_discount = 0;
                $tax_amount = 0;
                $product_value = 0;
                $discount_rupees = 0;
                $price_after_discount = 0;
                $gst_rupees = 0;
                foreach ($p_query->result_array() as $rows) {
                    $id  = $rows['id'];
                    $product_id  = $rows['product_id'];
                    $product_qty  = $rows['product_qty'];
                    $price  = $rows['price'];
                    $product_mrp  = $rows['product_mrp'];
		
		    //new logic added by nikhil - STARTS HERE	
	            if($rows['calculateby'] == 'PTR'){
		  	$item_rate = $rows['price']/$rows['product_qty'];
            	    }else {
		   	$item_rate = $rows['gst_amount']; 
            	    }		
		    $product_value = round($rows['product_qty']*$item_rate,2);
		    $gross += $product_value;
		    $discount_rupees = $product_value*round(($rows['discount']/100),2);	
                    $total_discount += $discount_rupees;
		    $price_after_discount = $product_value - $discount_rupees;	
		    $gst_rupees = round((($product_value-$discount_rupees)*$rows['gst'])/100,2);
		    $tax_amount +=$gst_rupees;	
		    //new logic added by nikhil - ENDS HERE	
			
                    /* old logic
		    $product_value = round($rows['product_qty']*($rows['product_mrp']/(1+ (0.01 *$rows['gst']))),2);
                    $gross += $product_value;
                    $discount_rupees = round($rows['product_qty']*($rows['product_mrp']/(1+ (0.01 *$rows['gst']))),2)*round(($rows['discount']/100),2);
                    $total_discount += $discount_rupees;
                    $price_after_discount = $product_value - $discount_rupees;
                    $gst_rupees = round((($product_value-$discount_rupees)*$rows['gst'])/100,2);
                    $tax_amount +=$gst_rupees;
                    */
                    
                   
                    $table_='dstock_'.$listing_id;
                    $c_query = $this->db->query("SELECT product_name,hsncode FROM $table_ where product_id='$product_id' limit 1");
                    $prow   = $c_query->row_array();
                    $product_name  = $prow['product_name'];
                    $product_hsn  = $prow['hsncode'];
                            
                    $p_array[] = array(
                        "id" => $id,
                        "product_id" => $product_id,
                        "product_qty" => $product_qty,
                        "price" => $price,
                        "product_mrp" => $product_mrp,
                        "product_name" => $product_name,
                    );
                }
                $grand_total = round($gross-$total_discount+$tax_amount,2);
            }
            
            $data[] = array(
                "order_id" => $order_id,
                "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_id" => $listing_id,
                "invoice_type" => $invoice_type,
                "listing_name" => $listing_name,
                "order_total" => $grand_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date,
                "name" => $name,
                "mobile" => $mobile,
                "pincode" => $pincode,
                "address1" => $address1,
                "address2" => $address2,
                "landmark" => $landmark,
                "city" => $city,
                "state" => $state,
                "product_list" => $p_array
            );
        }
        
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    
   public function order_invoice_return($user_id,$order_id,$id,$product)
    {
        
	   date_default_timezone_set('Asia/Kolkata');
        
        $check = $this->db->query("SELECT id FROM inven_distrub_returned WHERE order_id='$order_id' AND user_id='$user_id' LIMIT 1");
      
        if($check->num_rows()==0){ 

			$sql = $this->db->query("SELECT * FROM inven_distrub_invoice WHERE order_id='$order_id' AND user_id='$user_id' LIMIT 1"); 
		    $row=$sql->row_array();
            $data['order_id']       	= $row['order_id'];
            $data['invoice_no']         = $row['invoice_no'];
            $data['invoice_type']       = $row['invoice_type'];
            $data['invoice_type_old']   = $row['invoice_type_old'];
            $data['buyer']        		= $row['buyer'];
            $data['order_type']         = $row['order_type'];
            $data['delivery_type']      = $row['delivery_type'];
            $data['updated_at']         = $row['updated_at'];
            $data['order_status']       = $row['order_status'];
            $data['listing_id']         = $row['listing_id'];
            $data['listing_name']       = $row['listing_name'];
            $data['listing_type']       = $row['listing_type'];
            $data['user_id']            = $row['user_id'];
            $data['actual_cost']        = $row['actual_cost'];
            $data['order_total']        = $row['order_total'];
            $data['discount']           = $row['discount'];
            $data['delivery']           = $row['delivery'];
            $data['delivery_charges_by_customer']   = $row['delivery_charges_by_customer'];
            $data['delivery_charges_by_vendor']     = $row['delivery_charges_by_vendor'];
            $data['delivery_charges_by_mw']         = $row['delivery_charges_by_mw'];
            $data['delivery_charges_to_mno']        = $row['delivery_charges_to_mno'];
            $data['mno_service_charge_percent']     = $row['mno_service_charge_percent'];
            $data['mno_service_charge_rupees']      = $row['mno_service_charge_rupees'];
            $data['bill_url']           = $row['bill_url'];
            $data['mno_bill_no']        = $row['mno_bill_no'];
            $data['gst']        		= $row['gst'];
            $data['gst_no']         	= $row['gst_no'];
            $data['chc']        		= $row['chc'];
            $data['email']        		= $row['email'];
            $data['gste_type']          = $row['gste_type'];
            $data['payment_method']     = $row['payment_method'];
            $data['lat']        		= $row['lat'];
            $data['lng']         		= $row['lng'];
            $data['address_id']         = $row['address_id'];
            $data['name']        		= $row['name'];
            $data['mobile']        		= $row['mobile'];
            $data['pincode']       	    = $row['pincode'];
            $data['chat_id']            = $row['chat_id'];
            $data['address1']           = $row['address1'];
            $data['address2']           = $row['address2'];
            $data['landmark']           = $row['landmark'];
            $data['city']         		= $row['city'];
            $data['state']        		= $row['state'];
            $data['order_date']         = $row['order_date'];
            $data['cancel_reason']      = $row['cancel_reason'];
            $data['cancel_date']        = $row['cancel_date'];
            $data['action_by']          = $row['action_by'];
            $data['order_generate_from']= $row['order_generate_from'];
            $data['order_deliver_by']   = $row['order_deliver_by'];
            $data['created_by']         = $row['created_by'];
            $data['inven_order_id']     = $row['inven_order_id'];
            $data['status']         	= $row['status'];
            $data['staff_user_id']      = $row['staff_user_id'];
            $data['change_user_id']     = $row['change_user_id'];
            $data['cancel_by']          = $row['cancel_by'];
            $data['final_invoice_by']   = $row['final_invoice_by'];
            $data['invoice_date']       = $row['invoice_date'];
            $data['invoice_closed_date']= $row['invoice_closed_date'];
            $data['mno_assign_status']  = $row['mno_assign_status'];
            $data['delivered_sign']     = $row['delivered_sign'];
            $data['order_received_by']  = $row['order_received_by'];
            $data['delete_status']      = $row['delete_status'];
            $data['added_date']      = date('Y-m-d H:i:s');
            $this->db->insert('inven_distrub_returned', $data);
			$returned_id=$this->db->insert_id();
					
			$grand_total=0;		
            if(!empty($product)){
            foreach ($product as $ret_product) {			
				$ret_id=$ret_product['product_id'];
				$ret_qty=$ret_product['qty'];
				$query = $this->db->query("SELECT id, user_id, order_id, product_id, product_qty, discount, discount_rupess, remark, price, gst, gst_amount, gst_type, product_mrp, vendor_id, sku FROM inven_distrub_invoice_details WHERE id='$ret_id'");			
				$info=$query->row_array();
				$data2['product_return_qty'] = $ret_qty;
				$data2['user_id']    		 = $info['user_id'];
				$data2['order_id']           = $info['order_id'];
				$data2['product_id']         = $info['product_id'];
				$data2['product_qty']   	 = $info['product_qty'];
				$data2['discount']      	 = $info['discount'];
				$data2['discount_rupess']	 = $info['discount_rupess'];
				$data2['remark'] 			 = $info['remark'];
				$data2['price']    		     = $info['price'];
				$data2['gst']  				 = $info['gst'];
				$data2['gst_amount']         = $info['gst_amount'];
				$data2['gst_type']     		 = $info['gst_type'];
				$data2['product_mrp']        = $info['product_mrp'];
				$data2['vendor_id']     	 = $info['vendor_id'];
				$data2['sku']     			 = $info['sku'];
				$this->db->insert('inven_distrub_returned_details', $data2);
			    $grand_total=(float)$grand_total+(float)$info['price']*$ret_qty;	
			 }
			}
			
		      
		      
		       $amount_data_1 = array(
                   'order_total'=>$grand_total,
                    
                );
                $this->db->where('id', $returned_id);
                $this->db->UPDATE('inven_distrub_returned', $amount_data_1);	
			
            
            $resultpost = array(
                'status' => 200,
                'message' => "success"
            );
         
		
		}
		else{  
     		$resultpost = array(
                'status' => 400,
                'message' => "Your returned request already in process!"
            );
		}
        return $resultpost;	
		
    }   
     
     
     
     
    public function pending_returned_invoice_list($user_id,$status,$page)
    {
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start = ($page - 1) * $limit;
        
        $query = $this->db->query("SELECT order_id,invoice_no,order_status,listing_id,listing_name,order_deliver_by,order_date,added_date,updated_at FROM `inven_distrub_returned` where user_id='$user_id' AND return_status='$status' order by order_id desc  LIMIT $start, $limit");
        
        $count_query = $this->db->query("SELECT order_id FROM `inven_distrub_returned` where user_id='$user_id' AND return_status='$status' ");
        
        $count = $count_query->num_rows();
        $data        = array();
        
        $count_array = array();
        foreach ($query->result_array() as $row) {
            $order_id  = $row['order_id'];
            $invoice_no  = $row['invoice_no'];
            $order_status  = $row['order_status'];
            $listing_id  = $row['listing_id'];
            //$listing_name  = $row['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($row['listing_id']);
            $order_total  = '';
            $delivery_type  = $row['order_deliver_by'];
            $order_date  = $row['order_date'];
            $added_date  = $row['added_date'];
            $updated_at  = $row['updated_at'];
            
            $total_items=0;
            $c_query = $this->db->query("SELECT id FROM `inven_distrub_returned_details` where order_id='$order_id' and user_id='$user_id'");
            $total_items = $c_query->num_rows();
            
            $data[] = array(
                "order_id" => $order_id,
                "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_id" => $listing_id,
                "total_items" => $total_items,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date,
                "added_date" => $added_date,
                "updated_at" => $updated_at
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data,
            'count' => $count
        );
        return $resultpost;
    }
     
      public function pending_returned_invoice_details($user_id,$order_id)
    {
        $query = $this->db->query("SELECT name,mobile,pincode,address1,address2,landmark,order_total,city,state,order_id,invoice_no,order_status,listing_id,listing_name,order_deliver_by,order_date FROM `inven_distrub_returned` where user_id='$user_id' and order_id='$order_id' limit 1");
        $data  = array();
        
        if($query->num_rows()>0){
            $row=$query->row_array();
            $order_id  = $row['order_id'];
            $invoice_no  = $row['invoice_no'];
            $order_status  = $row['order_status'];
            $listing_id  = $row['listing_id'];
            //$listing_name  = $row['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($row['listing_id']);
            $order_total  = $row['order_total'];
            $delivery_type  = $row['order_deliver_by'];
            $order_date  = $row['order_date'];
            
            $name  = $row['name'];
            $mobile  = $row['mobile'];
            $pincode  = $row['pincode'];
            $address1  = $row['address1'];
            $address2  = $row['address2'];
            $landmark  = $row['landmark'];
            $city_id  = $row['city'];
            $state_id  = $row['state'];
            
            $c_query = $this->db->query("SELECT state_name FROM `states` where state_id='$state_id' limit 1");
            $prow    = $c_query->row_array();
            $state   = $prow['state_name'];
            
            $c_query = $this->db->query("SELECT city_name FROM `cities` where city_id='$city_id' limit 1");
            $prow    = $c_query->row_array();
            $city    = $prow['city_name'];
            
            $p_query = $this->db->query("SELECT id,product_id,product_qty,price,product_mrp,product_return_qty FROM `inven_distrub_returned_details` where user_id='$user_id' and order_id='$order_id'");
            $p_array = array();
            if($p_query->num_rows()>0){
                foreach ($p_query->result_array() as $rows) {
                    $id  = $rows['id'];
                    $product_id  = $rows['product_id'];
                    $product_qty  = $rows['product_qty'];
                    $price  = $rows['price'];
                    $product_mrp  = $rows['product_mrp'];
                   
                    $table_='dstock_'.$listing_id;
                    $c_query = $this->db->query("SELECT product_name,hsncode FROM $table_ where product_id='$product_id' limit 1");
                    $prow   = $c_query->row_array();
                    $product_name  = $prow['product_name'];
                    $product_hsn  = $prow['hsncode'];
                    
                    $p_array[] = array(
                        "id" => $id,
                        "product_id" => $product_id,
                        "product_qty" => $product_qty,
                        "price" => $price,
                        "product_mrp" => $product_mrp,
                        "product_name" => $product_name,
                        "product_return_qty" => $rows['product_return_qty'],
                    );
                }
            }
            
            $data[] = array(
                "order_id" => $order_id,
                "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_id" => $listing_id,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date,
                "name" => $name,
                "mobile" => $mobile,
                "pincode" => $pincode,
                "address1" => $address1,
                "address2" => $address2,
                "landmark" => $landmark,
                "city" => $city,
                "state" => $state,
                "product_list" => $p_array
            );
        }
        
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
      
     
     
    
    
public function export_order_invoice_list($user_id,$filter)
    {
        
        $query = $this->db->query("SELECT order_id,name,mobile,pincode,address1,address2,landmark,order_total,state,order_id,invoice_no,order_status,listing_id,listing_name,order_deliver_by,order_date FROM `inven_distrub_invoice` WHERE user_id='$user_id' order by order_id desc");
         
        $resultpost        = array();
        if($query->num_rows()>0){
        foreach ($query->result_array() as $order) {
			$order_id  = $order['order_id'];
			$invoice_no  = $order['invoice_no'];
            $order_status  = $order['order_status'];
            $listing_id  = $order['listing_id'];
            //$listing_name  = $order['listing_name'];
            $listing_name  = $this->auth_model->get_listing_name($order['listing_id']);
            $order_total  = $order['order_total'];
            $delivery_type  = $order['order_deliver_by'];
            $order_date  = $order['order_date'];
            
            $name  = $order['name'];
            $mobile  = $order['mobile'];
            $pincode  = $order['pincode'];
            $address1  = $order['address1'];
            $address2  = $order['address2'];
            $landmark  = $order['landmark'];
            $city_id  = $order['city'];
            $state_id  = $order['state'];
            
            $c_query1 = $this->db->query("SELECT state_name FROM `states` where state_id='$state_id' limit 1");
            $prow    = $c_query1->row_array();
            $state   = $prow['state_name'];
            
            $c_query2 = $this->db->query("SELECT city_name FROM `cities` where city_id='$city_id' limit 1");
            $prow    = $c_query2->row_array();
            $city    = $prow['city_name'];
            
           $c_query = $this->db->query("SELECT user_id,order_id,product_id,product_qty,price,product_mrp FROM `inven_distrub_invoice_details` WHERE order_id='$order_id'");
       
	   
          foreach ($c_query->result_array() as $row) {  
  		    $product_id     = $row['product_id'];
            $product_qty    = $row['product_qty'];
            $product_price  = $row['price']; 

            $table_='dstock_'.$listing_id;
            $c_query = $this->db->query("SELECT product_name,hsncode FROM $table_ where product_id='$product_id' limit 1");
            $prow   = $c_query->row_array();
            $product_name  = $prow['product_name'];
            $product_hsn  = $prow['hsncode'];
			
            $resultpost[] = array(
		        "order_id" => $order_id,
		        "invoice_no" => $invoice_no,
                "order_status" => $order_status,
                "listing_name" => $listing_name,
                "order_total" => $order_total,
                "delivery_type" => $delivery_type,
                "order_date" => $order_date,
                "name" => $name,
                "mobile" => $mobile,
                "pincode" => $pincode,
                "address1" => $address1,
                "address2" => $address2,
                "landmark" => $landmark,
                "state" => $state,
                "city" => $city,
                "product_name" => $product_name ,
                "product_qty" => $product_qty,
                "product_price" => $product_price,
             );
		   }			
         }
        }

        return $resultpost;
    }
    
    
     
  public function export_invoice_details($user_id,$order_id)
    {
        $query = $this->db->query("SELECT listing_id FROM `inven_distrub_invoice` WHERE user_id='$user_id' and order_id='$order_id' limit 1");
        $resultpost  = array();
        
        if($query->num_rows()>0){
          $row=$query->row_array();
          $listing_id  = $row['listing_id'];
         $table_='dstock_'.$listing_id;
			 
        $sql_distributor = $this->db->query("SELECT name FROM `users` WHERE id='$listing_id' limit 1");
		if($sql_distributor->num_rows()>0){
		 $get_dis=$sql_distributor->row_array();
		 $distributor_name=$get_dis['name'];
		}	
		else{
		    $distributor_name='-';
		}
			            
$p_query = $this->db->query("SELECT
inven_distrub_invoice_details.discount as discount_per,
inven_distrub_invoice.user_id,
inven_distrub_invoice.listing_id,
inven_distrub_invoice.name as username,
inven_distrub_invoice.landmark as area,
inven_distrub_invoice.city as city,
inven_distrub_invoice.state as state,
inven_distrub_invoice.pincode as pincode,
inven_distrub_invoice.order_date,
inven_distrub_invoice.invoice_no,
inven_distrub_invoice.invoice_type,
inven_distrub_invoice.invoice_date,
inven_distrub_invoice.invoice_type,
inven_distrub_invoice.name,
inven_distrub_invoice.gst_no as CGSTIN,
inven_distrub_invoice_details.product_id,
inven_distrub_invoice_details.product_qty,
inven_distrub_invoice_details.gst_amount,
inven_distrub_invoice_details.product_mrp,
inven_distrub_invoice_details.price,
inven_distrub_invoice_details.remark,
round(((inven_distrub_invoice_details.discount*0.01)*inven_distrub_invoice_details.product_qty*inven_distrub_invoice_details.product_mrp),2) as Discount,
inven_distrub_invoice_details.gst,
round((inven_distrub_invoice_details.gst/2),2) AS CGST,
round((inven_distrub_invoice_details.gst/2),2) AS SGST,
round(((inven_distrub_invoice_details.price-((inven_distrub_invoice_details.discount*0.01)*inven_distrub_invoice_details.product_qty*inven_distrub_invoice_details.product_mrp))/(1+(0.01*inven_distrub_invoice_details.gst))),2) as Taxable_Value,
round(((inven_distrub_invoice_details.price-((inven_distrub_invoice_details.discount*0.01)*inven_distrub_invoice_details.product_qty*inven_distrub_invoice_details.product_mrp))/(1+(0.01*inven_distrub_invoice_details.gst))),2)*(0.01*inven_distrub_invoice_details.gst) as GST2,
round(((inven_distrub_invoice_details.price-((inven_distrub_invoice_details.discount*0.01)*inven_distrub_invoice_details.product_qty*inven_distrub_invoice_details.product_mrp))/(1+(0.01*inven_distrub_invoice_details.gst))),2)*(0.01*inven_distrub_invoice_details.gst)/2 as SGST2,
round(((inven_distrub_invoice_details.price-((inven_distrub_invoice_details.discount*0.01)*inven_distrub_invoice_details.product_qty*inven_distrub_invoice_details.product_mrp))/(1+(0.01*inven_distrub_invoice_details.gst))),2)*(0.01*inven_distrub_invoice_details.gst)/2 as CGST2
FROM `inven_distrub_invoice`
LEFT JOIN inven_distrub_invoice_details
ON inven_distrub_invoice.order_id = inven_distrub_invoice_details.order_id
 WHERE inven_distrub_invoice.order_id='$order_id'");

		 $p_array = array();
		   if($p_query->num_rows()>0){
                foreach ($p_query->result_array() as $rows) {    
                    $inven_brand=$rows['inven_brand'];
                    $product_id=$rows['product_id'];
                    $listing_id=$rows['listing_id'];
					

					$this->db->select('*');
					$this->db->from($table_);
					$this->db->where('product_id', $product_id);
					$dstock = $this->db->get()->row_array();			
			  
                   $bsql = $this->db->query("SELECT `id`, `v_name` as name FROM `inven_brand` WHERE (id='$inven_brand')  LIMIT 1");
                    if($bsql->num_rows()>0){
                       $gbrand=$bsql->row_array();
                       $brand_name=$gbrand['name'];
                       $brand_short=get_short_name($gbrand['name']);
                    }
                    else{
                     $brand_name='NA';  
                     $brand_short='NA';  
                    }
                 
                   $product_name= ($dstock['product_name']!=''? $dstock['product_name']:'NA');
                 
                    $psql = $this->db->query("SELECT product_description FROM `product` WHERE (id='$product_id')  LIMIT 1");
                    if($psql->num_rows()>0){
                       $prod=$psql->row_array();
                       $product_description=str_replace(',', ' ',trim($prod['product_description']));
                    }
                    else{
                     $product_description=$product_name;  
                    }
                    
                     $vsql = $this->db->query(" SELECT gst_number FROM `distributor` WHERE user_id='$listing_id' and is_staff='0' limit 1");
                    if($vsql->num_rows()>0){
                       $vgst=$vsql->row_array();
                       $VGSTIN=$vgst['gst_number'];
                    }
                    else{
                     $VGSTIN='';  
                    }
              
             if($rows['order_date']!='' && $rows['order_date']!='0000-00-00 00:00:00'){
                 $order_date= date("d-m-Y", strtotime($rows['order_date']));  
              }
              else{
                $order_date= '';   
              }
             
              
              
              if($rows['invoice_date']!='' && $rows['invoice_date']!='0000-00-00 00:00:00'){
                  $invoice_date= date("d-m-Y", strtotime($rows['invoice_date']));
              }
              else{
                  $invoice_date=$order_date;
              }  
              
            $user_id=$rows['user_id'];
            $gst_sql = $this->db->query("SELECT state FROM `medical_stores` WHERE `user_id` = '$user_id' LIMIT 1");
            if($gst_sql->num_rows()>0){
               $ggst=$gst_sql->row_array();
               $state=$ggst['state'];
            }
            else{
             $state='';  
            } 
              
              if($state=='Maharashtra'){
        		$gst  = 0;
			    $GST2 = 0;
			    $CGST = $rows['CGST'];
			    $SGST = $rows['SGST'];
			    $SGST2 = $rows['SGST2'];
			    $CGST2 = $rows['CGST2']; 
              }
              else{
        		$gst  = $rows['gst'];
			    $GST2 = $rows['GST2'];
			    $CGST = 0;
			    $SGST = 0;
			    $SGST2= 0;
			    $CGST2= 0; 
              }

                 
                    $resultpost[] = array(      
					    "product_name" => $product_name,
					    "hsncode" => $dstock['hsncode'],
					    "order_date" => $order_date,
					    "invoice_no" => $rows['invoice_no'],
					    "invoice_type" => $rows['invoice_type'],
					    "name" => $distributor_name,
					    "gst_no" => $rows['gst_no'],
					    "product_id" => $rows['product_id'],
					    "product_qty" => $rows['product_qty'],
					    "gst_amount" => $rows['gst_amount'],
					    "product_mrp" => $rows['product_mrp'],
					    "product_gross" => $rows['gst_amount']*$rows['product_qty'],
					    "price" => $rows['price'],
					    "Discount" => $rows['Discount'],
					    "gst" => $gst,
					    "CGST" => $CGST,
					    "SGST" => $SGST,
					    "Taxable_Value" => $rows['Taxable_Value'],
					    "GST2" => $GST2,
					    "SGST2" => $SGST2,
					    "CGST2" => $CGST2,
					    "username" => $rows['username'],
					    "area" => str_replace(',', ' ', $rows['area']),
					    "city" => $rows['city'],
					    "invoice_date" => $invoice_date,
					    "pincode" => $rows['pincode'],
					    "brand_name" => ($brand_name!=''? $brand_name:'NA'),
					    "brand_short" => ($brand_short!=''? $brand_short:'NA'),
					    "product_description" => ($product_description!=''? $product_description:$product_name),
					    "pack" => ($dstock['pack']!=''? $dstock['pack']:'NA'),
					    "batch_no" => $dstock['batch_no'],
					    "expiry_date" => $dstock['expiry_date'],
					    "ptr" => $dstock['ptr'],
					    "remark" => ($rows['remark']!=''? $rows['remark']:'DMDelivery'),
					    "barcode" => ($dstock['barcode']!=''? $dstock['barcode']:'0'),
					    "CGSTIN" => $rows['CGSTIN'],
					    "VGSTIN" => $VGSTIN,
					    "listing_id" => $listing_id, 
					    "discount_per" => $rows['discount_per'],
                    );
                }
            }            

        }
        
    
        return $resultpost;
    }
     
}
