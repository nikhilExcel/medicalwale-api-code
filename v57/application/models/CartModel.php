<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class CartModel extends CI_Model
{

    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    public function check_auth_client() {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }

    public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
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
                $expired_at = '2030-11-12 08:57:58';
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

    public function encrypt($str) {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }

    public function decrypt($str) {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv = hash('MD5', 'mdwale8655328655', true);
        $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str = substr($str, 0, strlen($str) - $slast);
        return $str;
    }

    public function cart_add($user_id,$listing_id,$product_id,$ipaddress,$product_name,$product_image,$product_price,$product_type,$quantity,$medicalname,$product_unit)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $query = $this->db->query("SELECT product_id,ipaddress FROM `cart_session` WHERE ipaddress='$ipaddress' AND product_id='$product_id'");
        if($query->num_rows() ==0)
        {
            $cart_add_array = array(
                'user_id' =>$user_id ,
                'listing_id' => $listing_id,
                'product_id' => $product_id,
                'quantity' => $quantity,
                'product_name' => $product_name,
                'product_image' => $product_image,
                'product_price' => $product_price,
                'product_type' => $product_type,
                'listing_name' => $medicalname,
                'product_unit' => $product_unit,
                'created_at' => $date,
                'updated_at' => $date,
                'ipaddress' => $ipaddress
            );

            $this->db->insert('cart_session', $cart_add_array);
             $cart_view = $this->db->select('id')->from('cart_session')->where('ipaddress', $ipaddress)->get()->num_rows();

            return array(
                'status' => 200,
                'message' => 'success',
                'cart_view' => $cart_view
            );
        }
        else
        {
             return array(
                'status' => 400,
                'message' => 'Failed'
            );
        }
    }

    public function cart_details($ipaddress) {
        $resultpost =array();
        $query = $this->db->query("SELECT * FROM `cart_session` WHERE ipaddress='$ipaddress'");
        foreach ($query->result_array() as $row) {

            $resultpost[] = array
                (
                   "id" => $row['id'],
                   "user_id" => $row['user_id'],
                   "listing_id" => $row['listing_id'],
                   "product_id" => $row['product_id'],
                   "quantity" => $row['quantity'],
                   "product_name" => $row['product_name'],
                   "product_image" => $row['product_image'],
                   "product_price" => $row['product_price'],
                   "product_type" => $row['product_type'],
                   "listing_name" => $row['listing_name'],
                   "product_unit" => $row['product_unit'],

                );
        }
        return $resultpost;
    }

    public function remove_cart($ipaddress,$product_id,$user_id) {
        $resultpost =array();
        $query = $this->db->query("SELECT * FROM `cart_session` WHERE ipaddress='$ipaddress' AND product_id='$product_id'");
        if($query->num_rows()>0)
        {
            $this->db->where('ipaddress',$ipaddress);
            $this->db->where('product_id',$product_id);
            if(!empty($user_id)){
                $this->db->where('user_id',$user_id);
            }
            $this->db->delete('cart_session');
            $resultpost = array
                (
                    'status' => 200,
                    'message' => 'success',
                );
        }
        else
        {
            $resultpost = array
                (
                    'status' => 400,
                    'message' => 'Failed',
                );
        }
        return $resultpost;
    }
    public function update_quantity($ipaddress,$product_id,$quantity,$user_id) {
        $resultpost =array();
        $update= array('quantity' => $quantity);
        $query = $this->db->query("SELECT * FROM `cart_session` WHERE ipaddress='$ipaddress' AND product_id='$product_id'");
        if($query->num_rows()>0)
        {
            $this->db->where('ipaddress',$ipaddress);
            $this->db->where('product_id',$product_id);
            if(!empty($user_id)){
                $this->db->where('user_id',$user_id);
            }
            $this->db->update('cart_session',$update);
            $resultpost = array
                (
                    'status' => 200,
                    'message' => 'success',
                );
        }
        else
        {
            $resultpost = array
                (
                    'status' => 400,
                    'message' => 'Failed',
                );
        }
        return $resultpost;
    }


    public function remove_cart_alls($ipaddress,$user_id) {
        $resultpost =array();
        $query = $this->db->query("SELECT * FROM `cart_session` WHERE ipaddress='$ipaddress' AND user_id='$user_id'");
        if($query->num_rows()>0)
        {
            $this->db->where('ipaddress',$ipaddress);
            $this->db->where('user_id',$user_id);

            $this->db->delete('cart_session');
            $resultpost = array
                (
                    'status' => 200,
                    'message' => 'success',
                );
        }
        else
        {
            $resultpost = array
                (
                    'status' => 400,
                    'message' => 'Failed',
                );
        }
        return $resultpost;
    }
    
    
    public function order_add_cart($user_id,$cart_details,$description)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $cart = json_decode($cart_details,TRUE);
        $count = count($cart['list']);
      //echo $count;
         $desc = array();
                if(!empty($description))
                {
                    $description = $description.'$';
                    $desc = explode("$",$description);
                }
             $actual_image_path = '';
             if(!empty($_FILES["image"]["name"]))
            {
                
                 if (!empty($_FILES["image"]["name"])) {
                        $image = count($_FILES['image']['name']);
                        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
                        include('s3_config.php');
                        date_default_timezone_set('Asia/Calcutta');
                       // $invoice_no = date("YmdHis");
                      //  $order_status = 'Awaiting Confirmation';
                    }
                    if ($image > 0) {
                         $i=0;
                        
                        foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                            $img_name = $key . $_FILES['image']['name'][$key];
                            $img_size = $_FILES['image']['size'][$key];
                            $img_tmp = $_FILES['image']['tmp_name'][$key];
                            $ext = getExtension($img_name);
                            if (strlen($img_name) > 0) {
                                if ($img_size < (50000 * 50000)) {
                                    if (in_array($ext, $img_format)) {
                                        $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                        $actual_image_path = 'images/prescription_images/' . $actual_image_name;
                                        if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) {
                                         //  $p = 'pre';
                                           $p = md5(uniqid(rand(), true));
                                            if(!empty($desc))
                                        {
                                            $d = $desc[$i];
                                        }
                                        else
                                        {
                                            $d = "";
                                        }
                                    
                                            //$this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$actual_image_name','$order_status', '$invoice_no')");
                                         $p_results = $this->db->query("INSERT INTO `user_card_order` (`user_id`, `listing_id`, `listing_type`,`product_id`,`offline_prescription`,`date`,`is_prescription`,`description`) VALUES ('$user_id', '0', '13','$p','$actual_image_path','$date','YES','$d')");
                                        }
                                         $i++;
                                    }
                                }
                            }
                        }
                    }
                
            }
        
        if($count > 0)
        {
        for($i = 0; $i < $count; $i++) {
          // echo 'enter in loop'.$i;
            $product_id = $cart['list'][$i]['product_id'];
            $product_qty = $cart['list'][$i]['quantity'];
             
       
            $prescrition_type = $cart['list'][$i]['prescrition_type'];
            if($prescrition_type == 'E-pre')
            {
               $product_id =  md5(uniqid(rand(), true));
               $is_prescription = 'YES';
            }
            else
            {
                $is_prescription = 'NO';
            }
            
            $listing_type = $cart['list'][$i]['listing_type'];
            
            if(!empty($cart['list'][$i]['prescription_id'])){
                $prescription_id = $cart['list'][$i]['prescription_id'];   
            } else {
                $prescription_id = 0;
            }
            $prescription_url = $cart['list'][$i]['prescription_url'];
             if(!empty($cart['list'][$i]['prescription_url'])){
                $prescription_url = $cart['list'][$i]['prescription_url'];   
            } else {
                $prescription_url = '';
            }
            if(!empty($cart['list'][$i]['total_product_price'])){
                $total_product_price = $cart['list'][$i]['total_product_price'];   
            } else {
                $total_product_price = 0;
            }
            $checkAvailable = $this->db->query("SELECT * FROM `user_card_order` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
        $count1 = $checkAvailable->num_rows();
        if($count1 != 0){
            $this->db->query("DELETE FROM `user_card_order` WHERE `product_id` = '$product_id' AND `user_id` = '$user_id'");
            $results = $this->db->query("INSERT INTO `user_card_order` (`user_id`, `listing_id`, `listing_type`, `product_id`,  `quantity`, `prescription_id`, `prescription_url`,`total_product_price`,`date`,`is_prescription`) VALUES ('$user_id', '0', '$listing_type', '$product_id',  '$product_qty', '$prescription_id','$prescription_url','$total_product_price','$date','$is_prescription')");
    	    $insert_id = $this->db->insert_id();
    	    if($insert_id){
    	           $checkAvailablec = $this->db->query("SELECT 'user_id' FROM `user_card_order` WHERE `user_id` = '$user_id'");
                  $stack_count = $checkAvailablec->num_rows();
    	        
    	        return array(
                    "status" => 200,
                    "message" => "success",
                    "stack_count" => $stack_count
                );
    	    } else {
    	        return array(
                    "status" => 400,
                    "message" => "fail"
                );
                
    	    }
            
        } 
        else
        {
             $results = $this->db->query("INSERT INTO `user_card_order` (`user_id`, `listing_id`, `listing_type`, `product_id`,  `quantity`, `prescription_id`, `prescription_url`,`total_product_price`,`date`,`is_prescription`) VALUES ('$user_id', '0', '$listing_type', '$product_id',  '$product_qty', '$prescription_id','$prescription_url',$total_product_price,'$date','$is_prescription')");
    	    $insert_id = $this->db->insert_id();
    	    if($insert_id){
    	        
    	         $checkAvailablec = $this->db->query("SELECT 'user_id' FROM `user_card_order` WHERE `user_id` = '$user_id'");
                  $stack_count = $checkAvailablec->num_rows();
    	        
    	        return array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to Stack",
                    "stack_count" => $stack_count
                );
    	    }
    	    else
    	    {
    	        return array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong, please try again",
                );
             
    	    }
        }
            
            
    }
    
        }
        else
        {
            if($actual_image_path !== '')
            {
                
                
                   $checkAvailablec = $this->db->query("SELECT 'user_id' FROM `user_card_order` WHERE `user_id` = '$user_id'");
                  $stack_count = $checkAvailablec->num_rows();
                  return array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to Stack",
                    "stack_count" => $stack_count
                );
            }
            else
            {
                 return array(
                   "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong, please try again",
                );
            }
        }
    
    
    }
    
     public function order_add_cart_web($user_id,$cart_details,$description,$image)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        
        $cart = json_decode($cart_details,TRUE);
        $count = count($cart['list']);
         $desc = array();
                if(!empty($description))
                {
                    $description = $description.'$';
                    $desc = explode("$",$description);
                }
             $actual_image_path = '';
             if(!empty($image))
            {
                 $imagearray=explode(',', $image);
                  $image = count($imagearray);
                    if ($image > 0) {
                         $i=0;
                        foreach ($imagearray as $key => $tmp_name) {
                                 $d = "";
                                 $p= md5(uniqid(rand(), true));         
                                     if(!empty($desc))
                                        {
                                            $d = $desc[$i];
                                        }
                                        else
                                        {
                                            $d = "";
                                        }
                             //$this->db->query("INSERT INTO `prescription_order_details`(`order_id`, `prescription_image`,`order_status`, `uni_id`) VALUES ('$order_id', '$actual_image_name','$order_status', '$invoice_no')");
                             $p_results = $this->db->query("INSERT INTO `user_card_order` (`user_id`, `listing_id`, `listing_type`,`product_id`,`offline_prescription`,`date`,`is_prescription`,`description`) VALUES ('$user_id', '0', '13','$p','$tmp_name','$date','YES','$d')");
                        $i++;
                        }
                    }
                
            }
        
        if($count > 0)
        {
        for($i = 0; $i < $count; $i++) {
          // echo 'enter in loop'.$i;
            $product_id = $cart['list'][$i]['product_id'];
            $product_qty = $cart['list'][$i]['quantity'];
             
       
            $prescrition_type = $cart['list'][$i]['prescrition_type'];
            if($prescrition_type == 'E-pre')
            {
               $product_id =  md5(uniqid(rand(), true));
               $is_prescription = 'YES';
            }
            else
            {
                $is_prescription = 'NO';
            }
            $listing_id = $cart['list'][$i]['listing_id'];
            $listing_type = $cart['list'][$i]['listing_type'];
            
            if(!empty($cart['list'][$i]['prescription_id'])){
                $prescription_id = $cart['list'][$i]['prescription_id'];   
            } else {
                $prescription_id = 0;
            }
            $prescription_url = $cart['list'][$i]['prescription_url'];
             if(!empty($cart['list'][$i]['prescription_url'])){
                $prescription_url = $cart['list'][$i]['prescription_url'];   
            } else {
                $prescription_url = '';
            }
            if(!empty($cart['list'][$i]['total_product_price'])){
                $total_product_price = $cart['list'][$i]['total_product_price'];   
            } else {
                $total_product_price = 0;
            }
            $checkAvailable = $this->db->query("SELECT * FROM `user_card_order` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id'");
        $count1 = $checkAvailable->num_rows();
        if($count1 != 0){
            $this->db->query("DELETE FROM `user_card_order` WHERE `product_id` = '$product_id' AND `user_id` = '$user_id'");
            $results = $this->db->query("INSERT INTO `user_card_order` (`user_id`, `listing_id`, `listing_type`, `product_id`,  `quantity`, `prescription_id`, `prescription_url`,`total_product_price`,`date`,`is_prescription`) VALUES ('$user_id', '$listing_id', '$listing_type', '$product_id',  '$product_qty', '$prescription_id','$prescription_url','$total_product_price','$date','$is_prescription')");
    	    $insert_id = $this->db->insert_id();
    	    if($insert_id){
    	           $checkAvailablec = $this->db->query("SELECT 'user_id' FROM `user_card_order` WHERE `user_id` = '$user_id'");
                  $stack_count = $checkAvailablec->num_rows();
    	        
    	        return array(
                    "status" => 200,
                    "message" => "success",
                    "stack_count" => $stack_count
                );
    	    } else {
    	        return array(
                    "status" => 400,
                    "message" => "fail"
                );
                
    	    }
            
        } 
        else
        {
             $results = $this->db->query("INSERT INTO `user_card_order` (`user_id`, `listing_id`, `listing_type`, `product_id`,  `quantity`, `prescription_id`, `prescription_url`,`total_product_price`,`date`,`is_prescription`) VALUES ('$user_id', '$listing_id', '$listing_type', '$product_id',  '$product_qty', '$prescription_id','$prescription_url',$total_product_price,'$date','$is_prescription')");
    	    $insert_id = $this->db->insert_id();
    	    if($insert_id){
    	        
    	         $checkAvailablec = $this->db->query("SELECT 'user_id' FROM `user_card_order` WHERE `user_id` = '$user_id'");
                  $stack_count = $checkAvailablec->num_rows();
    	        
    	        return array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to Stack",
                    "stack_count" => $stack_count
                );
    	    }
    	    else
    	    {
    	        return array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong, please try again",
                );
             
    	    }
        }
            
            
    }
    
        }
        else
        {
            if($tmp_name !== '')
            {
                
                
                   $checkAvailablec = $this->db->query("SELECT 'user_id' FROM `user_card_order` WHERE `user_id` = '$user_id'");
                  $stack_count = $checkAvailablec->num_rows();
                  return array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to Stack",
                    "stack_count" => $stack_count
                );
            }
            else
            {
                 return array(
                   "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong, please try again",
                );
            }
        }
    
    
    }
    
    
    

    public function remove_user_cart($user_id,$product_id,$listing_type)
    {
         $checkAvailable = $this->db->query("SELECT `user_id` FROM `user_card_order` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id' AND `listing_type` = '$listing_type'");
       //  echo "SELECT `user_id` FROM `user_card_order` WHERE `user_id` = '$user_id' AND `product_id` = '$product_id' AND `listing_type` = '$listing_type'";
        $count = $checkAvailable->num_rows();
        if($count != 0){
            $this->db->query("DELETE FROM `user_card_order` WHERE `product_id` = '$product_id' AND `user_id` = '$user_id' AND `listing_type` = '$listing_type'");
              return array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully Deleted from Stack"
                );
        }
        else
        {
            return array(
                   "status" => 400,
                    "message" => "fail",
                    "description" => "No cart available to delete",
                );
        }
    }
    
    
    public function all_user_cart_list($user_id)
    {
        $this->load->model('UserstackModel');
        
        $checkAvailable = $this->db->query("SELECT * FROM `user_card_order` WHERE `user_id` = '$user_id' AND is_prescription = 'YES'");
        $count = $checkAvailable->num_rows();
        if($count > 0){
           
          foreach ($checkAvailable->result_array() as $row) {
              
              if($row['prescription_url'] == '')
              {
                  $prescription_type = 'O-pre';
              }
              else
              {
                  $prescription_type = 'E-pre';
              }
              if(empty($row['description']))
              {
                  $desc='';
              }
              else
              {
                  $desc=$row['description'];
              }
              
              
               if (strpos($row['offline_prescription'], '/') == true) {
                                $images_1 = "https://d2c8oti4is0ms3.cloudfront.net/" . $row['offline_prescription'];
                           }
                           else
                           {
                               $images_1= "https://d2c8oti4is0ms3.cloudfront.net/images/prescription_images/" . $row['offline_prescription'];
                           }
            $prescription_array[] = array
                (
                   "id" => $row['id'],
                   "user_id" => $row['user_id'],
                   "listing_id" => $row['listing_id'],
                   "listing_type" => $row['listing_type'],
                   "product_id" => $row['product_id'],
                   "prescription_url" => $row['prescription_url'],
                   "prescription_id" => $row['prescription_id'],
                   "offline_prescription" => $images_1,
                   "product_quantity" => $row['quantity'],
                   "product_name" => "",
                 //$row['product_image']
                 "product_image" => 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/',
                   "product_price" => $row['total_product_price'],
                   "date"         => $row['date'],
                   "prescription_description"  =>$desc ,
                   "prescription_type"  => $prescription_type
                );
        }
           
        }
        else
        {
             $prescription_array = array();
        }
        
       
        $checkAvailable1 = $this->db->query("SELECT * FROM `user_card_order` WHERE `user_id` = '$user_id' AND is_prescription = 'NO'");
        $count1 = $checkAvailable1->num_rows();
        if($count1 > 0){
           
          foreach ($checkAvailable1->result_array() as $row) {
               $pro_id = $row['product_id'];
                $query = $this->db->query("SELECT product_name,product_price,image FROM `product` WHERE id='$pro_id'");
               foreach ($query->result_array() as $mrow) {
                         
                       
                           $product_price = $mrow['product_price'];
                           $product_name = $mrow['product_name'];
                          // echo 'name is ='.$mrow['product_name'];
                           $product_img = $mrow['image'];
        
               }

            $Medicine_array[] = array
                (
                   "id" => $row['id'],
                   "user_id" => $row['user_id'],
                   "listing_id" => $row['listing_id'],
                   "listing_type" => $row['listing_type'],
                   "product_id" => $row['product_id'],
                   "prescription_url" => 'https://d2c8oti4is0ms3.cloudfront.net/'.$row['prescription_url'],
                   "prescription_id" => $row['prescription_id'],
                   "offline_prescription" => 'https://d2c8oti4is0ms3.cloudfront.net/'.$row['offline_prescription'],
                   "product_quantity" => $row['quantity'],
                   "product_name" => $product_name,
                  "product_image" => 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/'.$product_img,
                   "product_price" => $row['total_product_price'],
                   "date"         => $row['date'],
                   "prescription_type"  => ""
                );
        }
           
        }
        else
        {
             $Medicine_array = array();
        }
        
        
        $labs_stack = $this->UserstackModel->stack_list($user_id);
        $healthmall_stack = $this->CartModel->get_cart($user_id);
        
        return array(
                    'prescrption_data' => $prescription_array,
                    'medicine_product_data' => $Medicine_array,
                    'labs' => $labs_stack,
                    'healthmall' => $healthmall_stack
            );
    }
    
    // healthmall cart aade by swapnali on 20th march 2019 for all_user_cart_list function
    
    
    public function get_cart($user_id){
        $oldId = 0;
        $referal_code = $prod_data_all = $cart_data_all = $availableCartFull = $availableCart = $cart = $vendor = array();
        $cartQuantity = $delvChargeOld = 0;
        // $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `customer_id` = '$user_id'");
        //$checkAvailable = $this->db->query("SELECT uc.`id`,uc.`customer_id`, uc.`product_id`,uc.`quantity`, uc.`offer_id`, uc.`referal_code`,pd.pd_added_v_id,pd.pd_vendor_price FROM `user_cart` uc JOIN product_details_hm pd ON uc.product_id = pd.pd_id WHERE uc.`customer_id` = '$user_id' ORDER BY pd.pd_added_v_id ASC");
        $checkAvailable = $this->db->query("SELECT uc.*,pd.pd_added_v_id,pd.pd_vendor_price FROM `user_cart` uc JOIN product_details_hm pd ON uc.product_id = pd.pd_id WHERE uc.`customer_id` = '$user_id' ORDER BY pd.pd_added_v_id ASC");
        $count = $checkAvailable->num_rows();
        $oldVid =  0;
        
        if($count > 0){
            
            foreach($checkAvailable->result_array() as $prodInfo)
            {
              
                // $availableCart[] = $prodInfo['pd_added_v_id'];
                $referal_code = $prodInfo['referal_code'];
            
                if($prodInfo['pd_added_v_id'] != $oldId){
                    $availableCartFull[] = $availableCart;
                    $availableCart['v_id'] = $prodInfo['pd_added_v_id'];
                    $availableCart['product_id'] = array();
                    $availableCart['referal_code'] = array();
                    $availableCart['offer_id'] = array();
                    $availableCart['quantity'] = array();
                    $availableCart['variable_pd_id'] = array();
                    $availableCart['sku'] = array();
                    $availableCart['product_id'][] = $prodInfo['product_id'];
                    $availableCart['referal_code'][] = $referal_code;
                     $availableCart['offer_id'][] = $prodInfo['offer_id'];
                     $availableCart['quantity'][] =$prodInfo['quantity'];
                    $availableCart['variable_pd_id'][] =$prodInfo['variable_pd_id'];
                    $availableCart['sku'][] =$prodInfo['sku'];
                    
                    
                } else {
                    $availableCart['product_id'][] = $prodInfo['product_id'];
                    $availableCart['referal_code'][] = $referal_code;
                     $availableCart['offer_id'][] = $prodInfo['offer_id'];
                     $availableCart['quantity'][] =$prodInfo['quantity'];
                    $availableCart['variable_pd_id'][] =$prodInfo['variable_pd_id'];
                    $availableCart['sku'][] =$prodInfo['sku'];
                    
                    //  $availableCartFull[] = $availableCart;
                }
                
                
                
                $oldId = $prodInfo['pd_added_v_id'];
            }
            $availableCartFull[] = $availableCart;
            //  print_r($availableCartFull); die();               
            for($i=0;$i<sizeof($availableCartFull);$i++){
                 $oldCost = $finalCost = 0;
                if(sizeof($availableCartFull[$i]) > 0){
                    
                $v_id = $availableCartFull[$i]['v_id'];
                    $prod_data = array();
                    for($j=0;$j<sizeof($availableCartFull[$i]['product_id']);$j++){
               
                $product = array();
                $quantity = $availableCartFull[$i]['quantity'][$j];
                $referal_code_id = $availableCartFull[$i]['referal_code'][$j];
                $offer_id = $availableCartFull[$i]['offer_id'][$j];
                $variable_pd_id = $availableCartFull[$i]['variable_pd_id'][$j];
                $sku = $availableCartFull[$i]['sku'][$j];
                // print_r($availableCartFull[$i]['offer_id'][$j]); die();
                
                 
                // $product_id = $prodInfo['product_id'];
                $product_id = $availableCartFull[$i]['product_id'][$j];
                $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_min_order,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,vd.cap_available,vd.cap_charge,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
                //echo $stmt."<br>";
                $prod_dataAll = $this->db->query($stmt);
                // $prod_data = $prod_dataAll->result_array();
                $prod_num = $prod_dataAll->num_rows();
                // print_r($prod_data); die();
                if($prod_num > 0){
                    
                    $offers = $this->CartModel->get_product_offer($product_id);
                
                    
                    if(empty($referal_code_id)){
                        $referal_code = (object)[];
                    } else {
                        $referal_code = $this->CartModel->get_referal_code($product_id,$referal_code_id);
                        // $referal_code = $referal_code_id;
                    }
                   
                   //echo sizeof($prod_data); 
                    foreach($prod_dataAll->result_array() as $prod){
                        $product = $prod;
                       
                       $variableProduct = array();
                        if($variable_pd_id > 0){
                            $variableProds = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = '$variable_pd_id'"); 
                            foreach($variableProds->result_array() as $variableProd){
                               //  print_r($variableProd['id']); die();
                                $colorId = $variableProd['color'];
                                $sizeId = $variableProd['size'];
                                
                                $color = $this->CartModel->get_color_by_id($colorId);
                                $size = $this->CartModel->get_size_by_id($sizeId);
                                
                                $variableProduct['id'] = $variableProd['id'];
                                $variableProduct['pd_id'] = $variableProd['pd_id'];
                                $variableProduct['sku'] = $variableProd['sku'];
                                $variableProduct['quantity'] = $variableProd['quantity'];
                                $variableProduct['price'] = $variableProd['price'];
                                $variableProduct['vendor_price'] = $variableProd['vendor_price'];
                                $variableProduct['image'] = $variableProd['image'];
                                $variableProduct['color'] = $color;
                                $variableProduct['size'] = $size;
                              //  print_r($variableProduct); die();
      
                            }
                        } else {
                            $variableProduct = (object)[];
                        }
                         
                        //   $oldCost $finalCost
                        $productCost = 0;
                        if(!empty($offers)){
                            $productCost = $offers[0]['offer_best_price'] * $quantity;
                        }  else {
                            $productCost = $prod['pd_vendor_price'] * $quantity;
                        }  
                        
                        $finalCost = $finalCost + $productCost;
                         
                        // $oldCost = $productCost;
                            
                        $cartQuantity = $cartQuantity + $quantity;
                        $product['quantity'] = $quantity;
                        $product['product_cost'] = $productCost;
                        $product['referal_code_id'] = $referal_code_id;
                        $product['referal_code'] = $referal_code;
                        $product['offer_id'] = $offer_id;
                        $product['variable_pd_id'] = $variable_pd_id;
                        $product['sku'] = $sku;
                        $product['variable_product'] = $variableProduct;
                        if($offer_id != 0){
                            foreach($offers as $off){
                                if($off['id'] == $offer_id){
                                    $product['offer_price'] = $off['offer_mrp'];
                                    break;
                                }
                            }
                        }
                        $product['offers'] = $offers;
                        if($variable_pd_id > 0){
                            if(!empty($variableProduct['image'])){
                                 $product['pd_photo_1'] = $variableProduct['image'];
                            }
                            if(!empty($variableProduct['price'])){
                                 $product['pd_mrp_price'] = $variableProduct['price'];
                            }
                            if(!empty($variableProduct['vendor_price'])){
                                 $product['pd_vendor_price'] = $variableProduct['vendor_price'];
                            }
                        }
                        //  print_r($variableProduct['image']);
                        //  print_r($product);
                        //  die();
                        $del['pd_id'] = $prod['pd_id'];
                        $del['v_id'] = $prod['v_id'];
                        $del['v_delivery_charge'] = $prod['v_delivery_charge'] ;
                        $del['v_min_order'] = $prod['v_min_order'] ;
                        $del['pd_vendor_price'] = $prod['pd_vendor_price'] ;
                        $del['quantity'] = $quantity;
                       
                        $delivery[] = $del;
                    }
                }
                
               
                // deliveryCharges begins 
                
                    $prod_data[] = $product;
                    
                    //   print_r($chc); die();
                     
                    }
                    
                   $handlingCharge = $charge = 0;
                    $chcs = $this->db->query("SELECT * FROM `cash_handling_charges` WHERE `v_id` = '$v_id'")->result_array();
                    foreach($chcs as $chc){
                        $start_limit = $chc['start_limit'];
                        $end_limit = $chc['end_limit'];
                        $chargesType =$chc['charges_type'];
                       $cashHandlingChargeRow = $chc['chc'];
                        
                    //   $chc
                        if($chargesType  == 'rupee'){
                            if($finalCost <= $end_limit &&  $finalCost >= $start_limit){
                                $charge = $cashHandlingChargeRow;
                            }
                        } else {
                            if($finalCost <= $end_limit &&  $finalCost >= $start_limit){
                               
                                $handlingChargePercent = ($finalCost * $cashHandlingChargeRow) / 100;
                                $charge = $handlingChargePercent;
                        
                            }    
                        }
                        
                        
                        
                        
                        // print_r($chc['start_limit']); die();
                    }
                    $ven_data['v_id'] = $prod['v_id'];
                    $ven_data['v_delivery_charge'] = $prod['v_delivery_charge'] ;
                    $ven_data['cash_handling_charges'] = $charge ;
                    $ven_data['vendor_cost'] = $finalCost ;
                    $ven_data['v_min_order'] = $prod['v_min_order'] ;
                    $ven_data['v_name'] = $prod['v_name'] ;
                    
                    $prod_data_all['vendor'] = $ven_data;
                    $prod_data_all['product'] = $prod_data;
                    
                    // print_r($availableCartFull[$i]);
                        $cart_data_all[] = $prod_data_all;
                }
                
                 
            }      
            
        
           
            $oldDel = array();
            $i=0;
            $totalProd =0 ;
            $oldProdPrice = 0;
            $oldPrice = 0;
            $sameVenProd = 0;
            foreach($delivery as $delvry){
                $currentDel = $delvry;
                $qty = $delvry['quantity'];
                $price = $delvry['pd_vendor_price'];
                $prodPrice = $qty * $price;
                 //print_r($delvry);
                if($i>0){
                    // print_r($delvry);
                    if($delvry['v_id'] == $oldDel['v_id']){
                        $sameVenProd = $sameVenProd + $prodPrice;
                        //$totalProd=$sameVenProd;
                    } else {
                        if($oldDel['v_min_order'] > $sameVenProd) {
                            $totalProd = $totalProd + $oldDel['v_delivery_charge'];
                        }
                        //$oldProdPrice = $oldProdPrice + $sameVenProd + $prodPrice ;
                        $sameVenProd = $prodPrice;
                    }
                } else {
                    $sameVenProd = $prodPrice;
                    $oldDel = $delvry;
                    //$totalProd = $prodPrice;
                    $oldProdPrice = $prodPrice;
                    $delvChargeOld = $delvry['v_delivery_charge'];
                }
             

                $oldDel = $delvry;
                $i++;
            }
            if($delvry['v_id'] == $oldDel['v_id'] &&  $oldDel['v_min_order']>$sameVenProd) {
                $totalProd = $totalProd + $oldDel['v_delivery_charge'];
            }
                $prod_data_final['total_delivery_charges'] = $totalProd;
                // $prod_data_final['cart'] = $prod_data_all;
                $prod_data_final['cart'] = $cart_data_all;
                $prod_data_final['product_quantity'] = $cartQuantity;
               
                $res = $prod_data_final;
            
        } else {
            $res =(object)[];

        }
        
	    return $res;
	}
	
	
	public function get_product_offer($pd_id){
  	
  	$offers = array();
  	$query = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` = '$pd_id'");
		
		foreach($query->result_array() as $query1 ){
		    
	        $prod_mrp = $query1['pd_mrp_price'];
	        $pd_vendor_price = $query1['pd_vendor_price'];
	        
	        $catIds = $query1['pd_pc_id'];
	            $subCatIds = $query1['pd_psc_id'];
	            
	            $vendor_id = 	$query1['pd_added_v_id'];
		} 
		
	$offersProd = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND vendor_id = '$vendor_id'");
	         date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
	    foreach ($offersProd->result_array() as $offer){
	        
	   //     $offersProd = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` = '$pd_id'");
	       
	   //     $pd_Info = $offersProd->result_array();
	   //     foreach($pd_Info as $cats ){
    	         
    // 		} 
	       
	     
	        $str = $offer['offer_on_ids'];
	        $afterstr =  (explode(",",$str));
	        
	    if($offer['offer_on'] == '1' ){
	            
	             
            for($i=0;$i<count($afterstr);$i++){
                if($afterstr[$i] == $catIds){
                    // offer
                    if($offer['end_date']){
                        if($today > $offer['end_date']){
                            $offer['expired'] = 1;
                        } else {
                            $offer['expired'] = 0;
                        }    
                    } else {
                        $offer['expired'] = 0;
                    }
                    
                    // mrp
                    $offer['offer_mrp'] = $prod_mrp;
                    if($prod_mrp > $pd_vendor_price){
        	            $prod_mrp = $pd_vendor_price;
        	        } else {
        	            $prod_mrp = $prod_mrp;
        	        }
                    $min_amount = $offer['min_amount'];
                        
                        if($min_amount <= $prod_mrp){
                            $offer['message'] = "Offer available"  ;
                             if($offer['save_type'] == 'rupee'){
                                 // max_discound
                                $dis =  $offer['price'];
                                if($offer['max_discound'] != 0 && $dis > $offer['max_discound']){
                                    $dis = $offer['max_discound'];
                                } else {
                                    $dis = $dis;
                                }
                                
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                // if($dis > $offer['max_discound']){
                                //     $prod_best_price = $offer['max_discound'];
                                // } else {
                                //     $prod_best_price = $prod_best_price;
                                // }
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                $offer['offer_best_price'] = $prod_best_price;
                            } else {
                                
                            $dis =   $prod_mrp * ( $offer['price'] / 100 ) ;
                                
                                if($offer['max_discound'] != 0 && $dis > $offer['max_discound']){
                                    $dis = $offer['max_discound'];
                                } else {
                                    $dis = $dis;
                                }
                                
                                
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                             
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                
                                $offer['offer_best_price'] = $prod_best_price;
                               
                            }
                        } else {
                            $offer['message'] = "Minimum amount must be $min_amount rupees"  ;
                            $offer['offer_best_price'] = null;
                        }
                           
                        
                    
                    $off = $offer;
                    $offers[] = $off;
                }
                }
	            
	        }
	    if($offer['offer_on'] == '2' ){
	            
	             
            for($i=0;$i<count($afterstr);$i++){
                if($afterstr[$i] == $subCatIds){
                    // offer
                    if($offer['end_date']){
                        if($today > $offer['end_date']){
                            $offer['expired'] = 1;
                        } else {
                            $offer['expired'] = 0;
                        }    
                    } else {
                        $offer['expired'] = 0;
                    }
                    
                    // mrp
                    $offer['offer_mrp'] = $prod_mrp;
                    if($prod_mrp > $pd_vendor_price){
        	            $prod_mrp = $pd_vendor_price;
        	        } else {
        	            $prod_mrp = $prod_mrp;
        	        }
                    $min_amount = $offer['min_amount'];
                        
                        if($min_amount <= $prod_mrp){
                            $offer['message'] = "Offer available"  ;
                             if($offer['save_type'] == 'rupee'){
                                 
                                 // max_discound
                                 
                                $dis =  $offer['price'];
                                
                                if($offer['max_discound'] != 0 && $dis > $offer['max_discound']){
                                    $dis = $offer['max_discound'];
                                } else {
                                    $dis = $dis;
                                }
                                
                                
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                
                                 $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                $offer['offer_best_price'] = $prod_best_price;
                            } else {
                                
                              $dis =   $prod_mrp  * ( $offer['price'] / 100 ) ;
                                
                                if($dis > $offer['max_discound']){
                                    $dis = $offer['max_discound'];
                                } else {
                                    $dis = $dis;
                                }
                                
                                
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                
                                $offer['offer_best_price'] = $prod_best_price;
                               
                            }
                        } else {
                            $offer['message'] = "Minimum amount must be $min_amount rupees"  ;
                            $offer['offer_best_price'] = null;
                        }
                           
                        
                    
                    $off = $offer;
                    $offers[] = $off;
                }
                }
	            
	        }
	    if($offer['offer_on'] == '3' ){
	            // print_r (explode(",",$str));
            for($i=0;$i<count($afterstr);$i++){
                if($afterstr[$i] == $pd_id){
                    // offer
                    if($offer['end_date']){
                        if($today > $offer['end_date']){
                            $offer['expired'] = 1;
                        } else {
                            $offer['expired'] = 0;
                        }    
                    } else {
                        $offer['expired'] = 0;
                    }
                    
                    // mrp
                    $offer['offer_mrp'] = $prod_mrp;
                    if($prod_mrp > $pd_vendor_price){
        	            $prod_mrp = $pd_vendor_price;
        	        } else {
        	            $prod_mrp = $prod_mrp;
        	        }
                    $min_amount = $offer['min_amount'];
                        
                        if($min_amount <= $prod_mrp){
                            $offer['message'] = "Offer available"  ;
                             if($offer['save_type'] == 'rupee'){
                                 
                                 // max_discound
                                 
                                $dis =  $offer['price'];
                                
                                if($offer['max_discound'] != 0 &&  $dis > $offer['max_discound']){
                                    $dis = $offer['max_discound'];
                                } else {
                                    $dis = $dis;
                                }
                                
                                
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                
                                 $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                $offer['offer_best_price'] = $prod_best_price;
                            } else {
                                
                             $dis =   $prod_mrp  * ( $offer['price'] / 100 ) ;
                                
                                if($offer['max_discound'] != 0 && $dis > $offer['max_discound']){
                                    $dis = $offer['max_discound'];
                                } else {
                                    $dis = $dis;
                                }
                                
                                
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                               
                                  
                                
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                
                                $offer['offer_best_price'] = $prod_best_price;
                               
                            }
                        } else {
                            $offer['message'] = "Minimum amount must be $min_amount rupees"  ;
                            $offer['offer_best_price'] = null;
                        }
                           
                        
                    
                    $off = $offer;
                    $offers[] = $off;
                }
                }
	            
	        }
	        
	    }
	    return $offers;
 }
 
  public function get_referal_code($product_id,$referal_code_id){
        $data = array();
        $referalCode = $this->db->query("SELECT * FROM `refer_product_hm` WHERE `id` = '$referal_code_id'")->row_array();
        $data['referal_code_id'] = $referalCode['id'];
        $data['code'] = $referalCode['code'];
        // print_r($data); die();
        return $data; 
    }
	
	
	public function get_color_by_id($colorId){
        
        $get_color_by_id = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$colorId'")->result_array();
        if(sizeof($get_color_by_id) > 0){
            return $get_color_by_id;
        } else {
            $get_color_by_id = array();
            return $get_color_by_id;
        }
    }
    
    
    public function get_size_by_id($sizeId){
        $get_size_by_id = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$sizeId'")->result_array();
        if(sizeof($get_size_by_id) > 0){
            return $get_size_by_id;
        } else {
            $get_size_by_id = array();
            return $get_size_by_id;
        }
       
    }
// 	added by swapnali ended 
	
	

}
?>
