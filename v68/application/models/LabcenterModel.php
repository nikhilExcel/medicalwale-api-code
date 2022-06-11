<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LabcenterModel extends CI_Model
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
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
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
               // echo $this->db->last_query();
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }
    }
    public function encrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $block = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $pad   = $block - (strlen($str) % $block);
        $str .= str_repeat(chr($pad), $pad);
        $encrypted = mcrypt_generic($module, $str);
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        return base64_encode($encrypted);
    }
    
    public function decrypt($str)
    {
        $this->key = hash('MD5', '8655328655mdwale', true);
        $this->iv  = hash('MD5', 'mdwale8655328655', true);
        $module    = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($module, $this->key, $this->iv);
        $str = mdecrypt_generic($module, base64_decode($str));
        mcrypt_generic_deinit($module);
        mcrypt_module_close($module);
        $slast = ord(substr($str, -1));
        $str   = substr($str, 0, strlen($str) - $slast);
        return $str;
    } 
    
    public function add_prescription($user_id,$comment,$mobile,$address_id,$member_id)
    {
        $ledger_id='0';
        $ledger_type='user';
        $prescription_image='';
        date_default_timezone_set('Asia/Kolkata');
        $invoice_no = date("YmdHis");
        $image = count($_FILES['image']['name']);
        $img_format = array("jpg", "png", "gif", "bmp", "jpeg", "PNG", "JPG", "JPEG", "GIF", "BMP");
        include('s3_config.php');
        date_default_timezone_set('Asia/Calcutta');
        $date = date('Y-m-d H:i:s');
        $p_booking_time = date('h:i A', strtotime($date));
        
        $status='5';
        
        if($user_id>0){
            if($member_id>0){
                $user = $this->db->query("SELECT name,email,phone,gender from users where id='$member_id' limit 1")->row_array();
            }else{
                $user = $this->db->query("SELECT name,email,phone,gender from users where id='$user_id' limit 1")->row_array();
                $member_id=$user_id;
            }
            $prescription_order = array(
                'booking_id' => $invoice_no,
                'user_id' => $user_id,
                'listing_id' => 0,
                'vendor_id' => 10,
                'booking_date' => $date,
                'booking_time' => $p_booking_time,
                'patient_id' => $member_id,
                'payment_mode' => '',
                'user_name' => $user['name'],
                'user_mobile' => $user['phone'],
                'user_email' => $user['email'],
                'user_gender' => $user['gender'],
                'status' => $status
            );
            $this->db->insert('booking_master', $prescription_order);
            $adress_list = $this->db->query("SELECT address1,address2,mobile,email,city,state,pincode from user_address where user_id='$user_id' limit 1")->row_array();
            if (!empty($_FILES["image"]["name"])) {
                foreach ($_FILES['image']['tmp_name'] as $key => $tmp_name) {
                    $img_name = $key . $_FILES['image']['name'][$key];
                    $img_size = $_FILES['image']['size'][$key];
                    $img_tmp = $_FILES['image']['tmp_name'][$key];
                    $ext = getExtension($img_name);
                    if (strlen($img_name) > 0) {
                        if ($img_size < (50000 * 50000)) 
                        {
                            if (in_array($ext, $img_format)) 
                            {
                                $actual_image_name = uniqid() . date("YmdHis") . "." . $ext;
                                $actual_image_path = 'images/lab_prescription/' . $actual_image_name;
                                if ($s3->putObjectFile($img_tmp, $bucket, $actual_image_path, S3::ACL_PUBLIC_READ)) 
                                {
                                    $prescription_image.=$actual_image_name.',';
                                }
                            }
                        }
                    }
                }
            }
            $prescription_image=rtrim($prescription_image, ',');
            $lab_test_order_details = array(
                'booking_id' => $invoice_no,
                'user_id' => $user_id,
                'listing_id' => 0,
                'vendor_type' => 10,
                'test_id' => 0,
                'package_id' => 0,
                'prescriptions' => $prescription_image,
                'amount' => 0,
                'total_cost' => 0,
                'total_discount' => 0,
                'booking_date' => $date,
                'booking_time' => $p_booking_time,
                'address_id' => $address_id,
                'address_line1' => $adress_list['address1'],
                'address_line2' => $adress_list['address2'],
                'city' => $adress_list['city'],
                'state' => $adress_list['state'],
                'pincode' => $adress_list['pincode'],
                'mobile_no' => $adress_list['mobile'],
                'email_id' => $adress_list['email']
            );
            $this->db->insert('lab_booking_details', $lab_test_order_details);
            $response[] = array(
                'booking_id' => $invoice_no,
                'ledger_id' => $ledger_id,
                'ledger_type' => $ledger_type
            );
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $response
            );
        }
        
        
    }  
    
    
    public function package_details($package_id,$vendor_id){

        $test_list = array();
        $package_list = array();
        $query      = $this->db->query("SELECT lab_package_master.*,users.name as vendor_name from lab_package_master INNER JOIN users on users.id=lab_package_master.user_id where lab_package_master.user_id='$vendor_id' and lab_package_master.id='$package_id'");
        $package_count = $query->num_rows();
        if ($package_count > 0) {
            $packagelist = $query->result();
            foreach ($packagelist as $package) {
                $package_id = $package->id;
                $code = $package->code;
                $vendor_id = $package->user_id;
                $feature = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
                $certification = $feature['feature'];
                $vendor_name = $package->vendor_name;
                
                $rating = '4.5';
                $report_availability = '1';
                $home_collection_charges = $package->home_collection_charges;
                $price = $package->price;
                $discounted_price = $package->discounted_price;
                if($discounted_price==0){
                    $discounted_price = $package->price;
                }
                $discount = $package->discount;
                $medicalwale_discount = $package->medicalwale_discount;
                $home_delivery = '1';
                $package_name = $package->name;
                
                $test_query = $this->db->query("SELECT id as test_id,code,group_name,name as test_name,type FROM `lab_package_test_master` where package_id='$package_id'");
                $num_count  = $test_query->num_rows();
                if ($num_count > 0) {
                    $rows_list  = $test_query->result();
                    foreach ($rows_list as $row) {
                        $test_id    = $row->test_id;
                        $test_code  = $row->code;
                        $group_name = $row->group_name;
                        $test_name  = $row->test_name;
                        $type       = $row->type;
                        $test_list[] = array(
                            'test_id' => $test_id,
                            'code' => $test_code,
                            'name' => $test_name,
                            'group_name' => $group_name,
                            'aliasname' => '',
                            'description' => '',
                            'instructions' => '',
                            'components' => '',
                            'fasting' => '',
                            'type' => $type
                        );
                    }
                }
                $package_list[] = array(
                    'package_id' => $package_id,
                    'code' => $code,
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $vendor_name,
                    'certification' => $certification,
                    'rating' => $rating,
                    'report_availability' => $report_availability,
                    'home_collection_charges' => $home_collection_charges,
                    'price' => $price,
                    'discounted_price' => $discounted_price,
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'home_delivery' => $home_delivery,
                    'package_name' => $package_name,
                    'test' => $test_list
                );
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'data_count' => $package_count,
                'data' => $package_list
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'No data found!'
            );
        }
    }
    
    public function test_details($test_id,$vendor_id)
    {
		$available_list=array();
		
		if($vendor_id>0){
        $query      = $this->db->query("SELECT lab_test_master_details.price,lab_test_master_details.discounted_price,lab_test_master.id as test_id,lab_test_master.name,lab_test_master.aliasname,lab_test_master.test_result_mean,lab_test_master.test_preparation_needed,lab_test_master.components,lab_test_master.fasting,lab_test_master.why_get_tested,lab_test_master.when_to_get_tested,lab_test_master.sample_required,lab_test_master.what_is_being_tested,lab_test_master.how_is_it_used,lab_test_master.when_is_it_ordered,lab_test_master.anything_else,lab_center.lab_name as vendor_name FROM `lab_test_master` INNER JOIN lab_test_master_details on lab_test_master_details.test_id=lab_test_master.id INNER JOIN lab_center on lab_center.user_id=lab_test_master_details.user_id where lab_test_master.id='$test_id' and lab_test_master_details.user_id='$vendor_id'");
        $count = $query->num_rows();
        if ($count > 0) {
			$row=$query->row_array();			
			$test_id   = $row["test_id"];
            $test_name           = $row["name"];
            $aliasname          = $row["aliasname"];
            $description = $row["test_result_mean"];
            $instructions  = $row["test_preparation_needed"];
            $components  = $row["components"];
            $fasting  = $row["fasting"];
            $why_get_tested  = $row["why_get_tested"];
            $when_to_get_tested  = $row["when_to_get_tested"];
            $sample_required  = $row["sample_required"];
            $what_is_being_tested  = $row["what_is_being_tested"];
            $how_is_it_used  = $row["how_is_it_used"];
            $when_is_it_ordered  = $row["when_is_it_ordered"];
            $anything_else  = $row["anything_else"];
            $vendor_name  = $row["vendor_name"];
            $price  = $row["price"];
            $discounted_price  = $row["discounted_price"];
			$testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
            $certification=$testInfo['feature'];

            $test_query = $this->db->query("select lab_test_master_details.user_id as vendor_id,lab_center.lab_name as vendor_name,lab_center.profile_pic,lab_test_master_details.price,lab_test_master_details.discounted_price from lab_test_master_details INNER JOIN lab_center on lab_center.user_id=lab_test_master_details.user_id where lab_test_master_details.test_id='$test_id' and lab_test_master_details.user_id<>'$vendor_id'");
            $num_count  = $test_query->num_rows();
            if ($num_count > 0) {
                $row_list  = $test_query->result();
                foreach ($row_list as $test_row) {
                    $vendorid  = $test_row->vendor_id;	
                    $vendorname    = $test_row->vendor_name;
                    $price          = $test_row->price;
                    $discounted_price = $test_row->discounted_price;
                    if($discounted_price==0){
                        $discounted_price = $test_row->price;
                    }
                    $image = $test_row->profile_pic;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
					$testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendorid'")->row_array();
					$certification=$testInfo['feature'];
                    $available_list[] = array(
                        'vendor_id' => $vendorid,
                        'vendor_name' => $vendorname,
                        'image' => $image,
                        'rating' => 4,
                        'test_id' => $test_id,
                        'test_name' => $test_name,
                        'price' => $price,
                        'discounted_price' => $discounted_price,
                        'certification' => $certification
                   );
                }
            }
			$testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
			$certification=$testInfo['feature'];
			$test_list[] = array(
                'vendor_id' => $vendor_id,
                'vendor_name' => $vendor_name,
                'rating' => 4,
                'certification' => $certification,
                'test_id' => $test_id,
                'test_name' => $test_name,
                'aliasname' => $aliasname,
                'description' => $description,
                'instructions' => $instructions,
                'components' => $components,
                'fasting' => $fasting,
                'why_get_tested' => $why_get_tested,
                'when_to_get_tested' => $when_to_get_tested,
                'sample_required' => $sample_required,
                'what_is_being_tested' => $what_is_being_tested,
                'how_is_it_used' => $how_is_it_used,
                'when_is_it_ordered' => $when_is_it_ordered,
                'anything_else' => $anything_else,
                'price' => $price,
                'discounted_price' => $discounted_price,
                'available_list' => $available_list
            );
			
            return array(
                'status' => 200,
                'message' => 'success',
                'data_count' => $count,
                'data' => $test_list
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'No data found!'
            );
        }
		}else{
		    
		    
		$query      = $this->db->query("SELECT lab_test_master.id as test_id,lab_test_master.name,lab_test_master.aliasname,lab_test_master.test_result_mean,lab_test_master.test_preparation_needed,lab_test_master.components,lab_test_master.fasting,lab_test_master.why_get_tested,lab_test_master.when_to_get_tested,lab_test_master.sample_required,lab_test_master.what_is_being_tested,lab_test_master.how_is_it_used,lab_test_master.when_is_it_ordered,lab_test_master.anything_else FROM `lab_test_master` where lab_test_master.id='$test_id' limit 1");
        $count = $query->num_rows();
        if ($count > 0) {
			$row=$query->row_array();			
			$test_id   = $row["test_id"];
            $test_name           = $row["name"];
            $aliasname          = $row["aliasname"];
            $description = $row["test_result_mean"];
            $instructions  = $row["test_preparation_needed"];
            $components  = $row["components"];
            $fasting  = $row["fasting"];
            $why_get_tested  = $row["why_get_tested"];
            $when_to_get_tested  = $row["when_to_get_tested"];
            $sample_required  = $row["sample_required"];
            $what_is_being_tested  = $row["what_is_being_tested"];
            $how_is_it_used  = $row["how_is_it_used"];
            $when_is_it_ordered  = $row["when_is_it_ordered"];
            $anything_else  = $row["anything_else"];

            $test_query = $this->db->query("select lab_test_master_details.user_id as vendor_id,lab_center.lab_name as vendor_name,lab_center.profile_pic,lab_test_master_details.price,lab_test_master_details.discounted_price from lab_test_master_details INNER JOIN lab_center on lab_center.user_id=lab_test_master_details.user_id where lab_test_master_details.test_id='$test_id'");
            $num_count  = $test_query->num_rows();
            if ($num_count > 0) {
                $row_list  = $test_query->result();
                foreach ($row_list as $test_row) {
                    $vendorid  = $test_row->vendor_id;	
                    $vendorname    = $test_row->vendor_name;
                    $price          = $test_row->price;
                    $discounted_price = $test_row->discounted_price;
                    if($discounted_price==0){
                        $discounted_price = $test_row->price;
                    }
                    $image = $test_row->profile_pic;
                    $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
					$testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendorid'")->row_array();
					$certification=$testInfo['feature'];
                    $available_list[] = array(
                        'vendor_id' => $vendorid,
                        'vendor_name' => $vendorname,
                        'image' => $image,
                        'rating' => 4,
                        'test_id' => $test_id,
                        'test_name' => $test_name,
                        'price' => $price,
                        'discounted_price' => $discounted_price,
                        'certification' => $certification
                   );
                }
            }
			$testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
			$certification=$testInfo['feature'];
			$test_list[] = array(
                'test_id' => $test_id,
                'test_name' => $test_name,
                'aliasname' => $aliasname,
                'description' => $description,
                'instructions' => $instructions,
                'components' => $components,
                'fasting' => $fasting,
                'why_get_tested' => $why_get_tested,
                'when_to_get_tested' => $when_to_get_tested,
                'sample_required' => $sample_required,
                'what_is_being_tested' => $what_is_being_tested,
                'how_is_it_used' => $how_is_it_used,
                'when_is_it_ordered' => $when_is_it_ordered,
                'anything_else' => $anything_else,
                'price' => $price,
                'discounted_price' => $discounted_price,
                'available_list' => $available_list
            );
			
            return array(
                'status' => 200,
                'message' => 'success',
                'data_count' => $count,
                'data' => $test_list
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'No data found!'
            );
        }
		    
		    
		}
        
        
    }
    
    public function vendor_details($vendor_id,$user_id)
    {
        $sql = sprintf("SELECT id,user_id,profile_pic,lab_name,features,address1,address2,pincode,city,state,contact_no,whatsapp_no,email FROM lab_center where user_id='$vendor_id'");
        $query = $this->db->query($sql);
                $count = $query->num_rows();
                if ($count > 0) {
                    $row=$query->row_array();
                        $id = $row['id'];
                        $labcenter_user_id = $row['user_id'];
                        $lab_name = $row['lab_name'];
                        $features = $row['features'];
                        $address1          = $row['address1'];
                        $address2          = $row['address2'];
                        $pincode           = $row['pincode'];
                        $city              = $row['city'];
                        $state             = $row['state'];
                        $contact_no        = $row['contact_no'];
                        $whatsapp_no       = $row['whatsapp_no'];
                        $email             = $row['email'];
                        $rating = '4.0';
                        $profile_views = '1548';
                        $reviews = '1000';
                        $image = $row['profile_pic'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;

                        $testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
                        $certification=$testInfo['feature'];

                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $vendor_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $vendor_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $vendor_id)->get()->num_rows();
                       
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }

                $package_list = array();
                $package_query = $this->db->query("SELECT lm.id,lm.code,lm.name,lm.home_collection_charges,lm.price,lm.discounted_price,count(pd.id) as test_count FROM lab_package_master lm LEFT JOIN lab_package_test_master pd on pd.package_id=lm.id WHERE lm.user_id='$vendor_id' GROUP By lm.id");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $code = $package_row['code'];
                        $name = $package_row['name'];
                        $home_collection_charges = $package_row['home_collection_charges'];
                        $price = $package_row['price'];
                        $discounted_price = $package_row['discounted_price'];
                        if($discounted_price==0){
                            $discounted_price = $package_row['price'];
                        }
                        $test_count = $package_row['test_count'];
                        $package_list[] = array(
                            'id' => $package_id,
                            'code' => $code,
                            'name' => $name,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'test_count' => $test_count
                        );
                    }
                } else {
                    $package_list = array();
                }
                

                $test_list = array();
                $test_query       = $this->db->query("SELECT lt.id,lt.name,ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount from lab_test_master_details ld INNER JOIN lab_test_master lt on lt.id=ld.test_id where ld.user_id='$vendor_id'");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $test_id        = $test_row['id'];
                        $name        = $test_row['name'];
                        $code           = $test_row['code'];
                        $report_availability        = $test_row['report_availability'];
                        $home_collection_charges          = $test_row['home_collection_charges'];
                        $price          = $test_row['price'];
                        $discounted_price = $test_row['discounted_price'];
                        if($discounted_price==0){
                            $discounted_price = $test_row['price'];
                        }
                        $discount  = $test_row['discount'];
                        $medicalwale_discount  = $test_row['medicalwale_discount'];
                        $test_list[] = array(
                            'test_id' => $test_id,
                            'name' => $name,
                            'code' => $code,
                            'report_availability' => $report_availability,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount
                        );
                    }
                } else {
                    $test_list  = array();
                }
                
                $branch_list  = array();
                $branch_query = $this->db->query("SELECT lc.*,u.multi_user_type FROM `lab_center` as lc left join users as u on(lc.`branch_user_id` = u.id) WHERE lc.user_id='$labcenter_user_id' AND lc.`is_active` = 1 AND lc.branch_user_id<>'0' order by id asc");
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['branch_user_id'];
                        $branch_lab_branch_name                 = $branch_row['lab_name'];
                        $branch_store_manager                   = $branch_row['store_manager'];
                        $branch_latitude                        = $branch_row['latitude'];
                        $branch_map_location                    = $branch_row['map_location'];
                        $branch_longitude                       = $branch_row['longitude'];
                        $branch_address1                        = $branch_row['address1'];
                        $branch_address2                        = $branch_row['address2'];
                        $branch_pincode                         = $branch_row['pincode'];
                        $branch_features                        = $branch_row['features'];
                        $branch_city                            = $branch_row['city'];
                        $branch_state                           = $branch_row['state'];
                        $branch_contact_no                      = $branch_row['contact_no'];
                        $branch_whatsapp_no                     = $branch_row['whatsapp_no'];
                        $branch_email                           = $branch_row['email'];
                        $branch_profile_pic                     = $branch_row['profile_pic'];
                        $branch_about_us                        = $branch_row['about_us'];
                        $branch_opening_hours           = $branch_row['opening_hours'];
                        $branch_profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $branch_profile_pic;
                        
                        $branch_features_array                  = array();
                        $feature_query                          = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
                            $feature                 = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
                        
                        $branch_final_Day = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[] = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                            $current_time = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day = "";
                        
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' => $branch_store_manager,
                            'branch_latitude' => $branch_latitude,
                            'branch_map_location' => $branch_map_location,
                            'branch_longitude' => $branch_longitude,
                            'branch_address1' => $branch_address1,
                            'branch_address2' => $branch_address2,
                            'branch_pincode' => $branch_pincode,
                            'branch_features' => $branch_features_array,
                            'branch_city' => $branch_city,
                            'branch_state' => $branch_state,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_contact_no' => $branch_contact_no,
                            'branch_whatsapp_no' => $branch_whatsapp_no,
                            'branch_email' => $branch_email,
                            'branch_profile_pic' => $branch_profile_pic
                        );
                    }
                } else {
                    $branch_list = array();
                }
                
                $resultpost[] = array(
                    "id" => $vendor_id,
                    "name" => $lab_name,
                    "certification" => $certification,
                    "address1" => $address1,
                    "address2" => $address2,
                    "pincode" => $pincode,
                    "city" => $city,
                    "exotel_no" => '02233721563',
                    "state" => $state,
                    "contact_no" => $contact_no,
                    "whatsapp_no" => $whatsapp_no,
                    "email" => $email,
                    "rating" => $rating,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $profile_views,
                    "reviews" => $reviews,
                    "is_follow" => $is_follow,
                    "image" => $image,
                    "package_list"=> $package_list,
                    "test_list"=>$test_list,
                    "branch_list" => $branch_list
                );
        
                } else {
                    $resultpost = array();
                }
            return $resultpost;
    }
    
    public function booking_summary($booking_id)
    {
        $query = $this->db->query("SELECT lt.booking_date,lt.booking_time,lt.coupon_id,lt.coupon_discount,lt.listing_id as vendor_id,lt.id,lab_booking_details.address_id,lab_booking_details.created_at,lt.patient_id as member_id,lt.user_id as parent_id,lt.booking_time,ud.address2,ud.address_type,ud.city,ud.address1,ud.full_address,ud.landmark,ud.lat,ud.lng,ud.mobile,ud.name,ud.pincode,ud.relation_ship as relation,ud.state,pm.payment_method,pm.icon,pm.id as payment_id,lab_booking_details.test_id,lab_booking_details.total_cost,lab_booking_details.total_discount,lab_booking_details.amount from booking_master lt LEFT JOIN lab_booking_details on lab_booking_details.booking_id=lt.booking_id LEFT JOIN user_address ud on ud.address_id=lab_booking_details.address_id LEFT JOIN payment_method pm on pm.id=lt.payment_mode where lt.booking_id='$booking_id' limit 1");
		$num_count  = $query->num_rows();
        if ($num_count > 0) {
        $list = $query->row_array();
        $vendor_id=$list['vendor_id'];
        $booking_date=date("D jS M,Y", strtotime($list['booking_date']));
        $booking_time=$list['booking_time'];
        $test_array=array();
        $package_array=array();
        $total_amount=0;
        $test_query = $this->db->query("SELECT ld.home_delivery,lab_test_master.id,ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,lab_test_master.name from lab_test_master_details ld INNER JOIN lab_test_master on lab_test_master.id=ld.test_id INNER JOIN lab_booking_details on FIND_IN_SET(lab_test_master.id,lab_booking_details.test_id) where lab_booking_details.booking_id='$booking_id' and ld.user_id='$vendor_id' group by lab_test_master.id");
        $test_count  = $test_query->num_rows();
                if ($test_count > 0) {
                    $test_row = $test_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = $test_list->report_availability;
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price==0){
                            $discounted_price = $test_list->price;    
                        }
                        $total_amount=$total_amount+$discounted_price;
                        $discount  = $test_list->discount;
                        $home_delivery  = $test_list->home_delivery;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $test_array[] = array(
                            'type' => 'TEST',
                            'test_id' => $testid,
                            'test_name' => $test_name,
                            'code' => $code,
                            'report_availability' => '',
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => $home_delivery
                        );
                    }
                }
                
        $test_query = $this->db->query("SELECT ld.id,ld.code,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,ld.name from lab_package_master ld INNER JOIN lab_booking_details on FIND_IN_SET(ld.id,lab_booking_details.package_id) where lab_booking_details.booking_id='$booking_id' group by ld.id");
        $test_count  = $test_query->num_rows();
                if ($test_count > 0) {
                    $test_row = $test_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = '';
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price==0){
                            $discounted_price = $test_list->price;    
                        }
                        $total_amount=$total_amount+$discounted_price;
                        $discount  = $test_list->discount;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $test_array[] = array(
                            'type' => 'PACKAGE',
                            'test_id' => $testid,
                            'test_name' => $test_name,
                            'package_id' => $testid,
                            'name' => $test_name,
                            'code' => $code,
                            'report_availability' => '',
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => "0"
                        );
                    }
                }
        
        $vendor_id=$list['vendor_id'];
        $feature = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
        $certification = $feature['feature'];
        
        $vendor_detail = $this->db->query("SELECT lab_name FROM `lab_center` where user_id='$vendor_id'")->row_array();
        $vendor_name   = $vendor_detail['lab_name'];
        
        $ledger_detail = $this->db->query("SELECT ledger_id FROM `user_vendor_ledger` where invoice_no='$booking_id'")->row_array();
        $ledger_id     = $ledger_detail['ledger_id'];
        if(empty($ledger_id)){
            $ledger_id='0';
        }
        $coupon_id=$list['coupon_id'];
        $coupon_discount=$list['coupon_discount'];

        $details[] = array(
            'address1' => $list['address1'],
            'address2' => $list['address2'],
            'address_id' => $list['address_id'],
            'address_type' => $list['address_type'],
            'city' => $list['city'],
            'date' => $booking_date.' '.$booking_time,
            'full_address' => $list['full_address'],
            'landmark' => $list['landmark'],
            'lat' => $list['lat'],
            'lng' => $list['lng'],
            'member_id' => $list['member_id'],
            'mobile' => $list['mobile'],
            'name' => $list['name'],
            'parent_id' => $list['parent_id'],
            'icon' => $list['icon'],
            'payment_id' => $list['payment_id'],
            'payment_method' => $list['payment_method'],
            'pincode' => $list['pincode'],
            'relation' => $list['relation'],
            'state' => $list['state'],
            'time' => $list['booking_time'],
            'vendor_id' => $vendor_id,
            'vendor_name' => $vendor_name,
            'certification' => $certification,
            'rating' => 0,
            'ledger_id' => $ledger_id,
            'coupon_id' => $coupon_id,
            'coupon_discount' => $coupon_discount,
            'test_list' => $test_array
        );
		return array(
            'status' => 200,
            'message' => 'success',
            'data' => $details
        );
		}
		else{
			$details=array();
			return array(
				'status' => 200,
				'message' => 'success',
				'data' => $details
			);
		}
    }
        
    public function update_payment_method($booking_id,$ledger_id,$payment_type)
    {
		$ledger_type='user';
        $test_query = $this->db->query("SELECT * from user_vendor_ledger where ledger_id='$ledger_id' and invoice_no='$booking_id'");
        $num_count  = $test_query->num_rows();
        if($num_count>0 && $ledger_id>0){
            $list = $test_query->row_array();
            $user_id = $list['user_id'];
            $invoice_no = $list['invoice_no'];
            $ledger_owner_type = $list['ledger_owner_type'];
            $transaction_id = '';
            $listing_id = $list['listing_id'];
            $listing_id_type = $list['listing_id_type'];
            $credit = $list['credit'];
            $debit = $list['debit'];
            $balance = $list['balance'];
            $payment_method = $list['payment_method'];
            $transaction_of = $list['transaction_of'];
            $trans_status = "";
            $order_type = $list['order_type'];
            $user_comments = $list['user_comments'];
            $mw_comments = $list['mw_comments'];
            $vendor_comments = $list['vendor_comments'];
            $ledger_type='user';
            $payment_status='3';
            $update_data = array(
                'payment_mode' => $payment_type
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $update_data);
            $res = $this->LedgerModel->update_ledger($user_id,$ledger_id,$booking_id,$transaction_id,$payment_status,$ledger_type);
            $transaction_date='';
            $data=array();
            $vendor_type='10';
            $res = $this->LedgerModel->create_ledger($user_id,$invoice_no,  $ledger_owner_type, $listing_id, $listing_id_type, $credit, $debit, $payment_type,   $user_comments, $mw_comments, $vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_type,$data);
			$ledger_id   = $res['ledger_id'];
			$ledger_type = $res['type'];
			
        }
        if($ledger_id=='0' && $payment_type!='3'){
            $test_query = $this->db->query("SELECT booking_master.user_id,booking_master.listing_id,lab_booking_details.test_id,lab_booking_details.package_id from booking_master INNER JOIN lab_booking_details on lab_booking_details.booking_id=booking_master.booking_id where booking_master.booking_id='$booking_id'");
            $num_count  = $test_query->num_rows();
            if($num_count>0){
                $list = $test_query->row_array();
                $user_id = $list['user_id'];
                $vendor_id = $list['listing_id'];
                $test_id = $list['test_id'];
                $package_id = $list['package_id'];
                
                $test_id = rtrim($test_id, ',');
                $test_list = explode(',',$test_id);
			    $debit=0;
			    $total_discount=0;
			    $total_cost=0;
			    $test_list_array='';
                foreach ($test_list as $tid) {
                    if($tid>0){
                        $test_query = $this->db->query("SELECT home_collection_charges,price,discounted_price,discount,medicalwale_discount from lab_test_master_details where test_id='$tid' and user_id='$vendor_id' limit 1");
                        $test_list = $test_query->row_array();
					    $debit+=$test_list['home_collection_charges']+$test_list['discounted_price'];
					    $total_discount+=$total_discount+($test_list['price']-$test_list['discounted_price']);
			            $total_cost+=$test_list['home_collection_charges']+$test_list['price'];
                    }
                }
                $package_id = rtrim($package_id, ',');
                $package_list = explode(',',$package_id);
                foreach ($package_list as $pid) {
                    if($pid>0){
                        $package_query = $this->db->query("SELECT home_collection_charges,price,discounted_price,discount,medicalwale_discount from lab_package_master where id='$pid' and user_id='$vendor_id' limit 1");
                        $package_list = $package_query->row_array();
					    $debit+=$package_list['home_collection_charges']+$package_list['discounted_price'];
					    $total_discount+=$total_discount+($package_list['price']-$package_list['discounted_price']);
			            $total_cost+=$package_list['home_collection_charges']+$package_list['price'];
                    }
                }
                
                $ledger_owner_type='0';
				$listing_id_type='31';
				$credit='';
				$user_comments='';
				$mw_comments='';
				$vendor_comments='';
				$order_type='2';
				$transaction_of='3';
				$transaction_id='';
				$trans_status='';
				
                $transaction_date='';
                $data=array();
                $vendor_type='10';
                $res=$this->LedgerModel->create_ledger($user_id, $booking_id, $ledger_owner_type, $vendor_id, $listing_id_type, $credit, $debit, $payment_type, $user_comments, $mw_comments,$vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_type,$data);
				$ledger_id=$res['ledger_id'];
				$ledger_type=$res['type'];
            }
        }
			
        $response[] = array(
            'booking_id' => $booking_id,
            'ledger_id' => $ledger_id,
            'ledger_type' => $ledger_type
        );
        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $response
        );
    }
    
    public function remove_test_cart($user_id,$test_id)
    {
        $this->db->query("DELETE FROM `lab_test_cart` WHERE user_id='$user_id' and test_id='$test_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function remove_vendor_cart($user_id,$test_id,$package_id,$vendor_id)
    {
        if($test_id>0){
            $this->db->query("DELETE FROM `lab_test_cart` WHERE user_id='$user_id' and test_id='$test_id'");
            $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id' and test_id='$test_id' and vendor_id='$vendor_id'");
        }
        if($package_id>0){
            $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id' and package_id='$package_id' and vendor_id='$vendor_id'");
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function update_order($booking_id,$ledger_id,$transaction_id,$payment_status,$ledger_type)
    {
        if($payment_status=='1')     { $p_status='SUCCESS';}
        elseif($payment_status=='2') { $p_status='FAILURE';}
        elseif($payment_status=='3') { $p_status='DECLINED';}
        if($ledger_id==0){
            //$payment_status='4';
        }
        $update_data = array(
            'status' => $payment_status
        );
        $this->db->where('booking_id', $booking_id);
        $rst = $this->db->update('booking_master', $update_data);
        $user = $this->db->query("SELECT user_id from booking_master where booking_id='$booking_id' limit 1")->row_array();
        $user_id = $user['user_id'];
        $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id'");
        $this->db->query("DELETE FROM `lab_test_cart` WHERE user_id='$user_id'");
        if($ledger_id>0){
            $res = $this->LedgerModel->update_ledger($user_id,$ledger_id,$booking_id,$transaction_id,$payment_status,$ledger_type);
        }
        return array(
            'status' => 200,
            'message' => 'success',
            'description' => $p_status
            
        );
    }

    
    public function add_order($user_id,$vendor_id,$address_id,$vendor_type,$booking_date,$booking_time,$package_id,$test_id,$member_id,$payment_type,$coupon_id)
    {
        
        $status='0';
	    $ledger_id=0;
		$ledger_type=0;
		$coupon_discount=0;
        date_default_timezone_set('Asia/Kolkata');
        $invoice_no = date("YmdHis");
        if(count($test_id>0) || count($package_id>0)){
            if($member_id>0){
                $user = $this->db->query("SELECT name,email,phone,gender from users where id='$member_id' limit 1")->row_array();
            }else{
                $user = $this->db->query("SELECT name,email,phone,gender from users where id='$user_id' limit 1")->row_array();
            }
            $user_email=$user['email'];

            $test_id = rtrim($test_id, ',');
            $test_list = explode(',',$test_id);
			$debit=0;
			$total_discount=0;
			$total_cost=0;
			$test_list_array='';
            foreach ($test_list as $tid) {
                if($tid>0){
                    $test_query = $this->db->query("SELECT home_collection_charges,price,discounted_price,discount,medicalwale_discount from lab_test_master_details where test_id='$tid' and user_id='$vendor_id' limit 1");
                    $test_list = $test_query->row_array();
					$debit+=$test_list['home_collection_charges']+$test_list['discounted_price'];
					$total_discount+=$total_discount+($test_list['price']-$test_list['discounted_price']);
			        $total_cost+=$test_list['home_collection_charges']+$test_list['price'];
                }
            }
            $package_id = rtrim($package_id, ',');
            $package_list = explode(',',$package_id);
            foreach ($package_list as $pid) {
                if($pid>0){
                    $package_query = $this->db->query("SELECT home_collection_charges,price,discounted_price,discount,medicalwale_discount from lab_package_master where id='$pid' and user_id='$vendor_id' limit 1");
                    $package_list = $package_query->row_array();
					$debit+=$package_list['home_collection_charges']+$package_list['discounted_price'];
					$total_discount+=$total_discount+($package_list['price']-$package_list['discounted_price']);
			        $total_cost+=$package_list['home_collection_charges']+$package_list['price'];
                }
            }
            $cres='';
            if($coupon_id>0){
                $listing_type='10';
                $cuser = $this->db->query("SELECT name from vendor_offers where id='$coupon_id' limit 1")->row_array();
                $coupon=$cuser['name'];
                $card_type='';
                $cres=$this->LoginModel->coupon_code($user_id,$coupon,$vendor_id,$listing_type,$debit,$card_type);
                if($cres['message']=='Success'){
                    $debit = $cres['subtotal'];
                    $total_discount = $total_discount+$cres['discount'];
                    $coupon_discount = $cres['discount'];
                }
                else{
                    $coupon_id=0;
                }
            }
            
            $lab_test_order = array(
                'booking_id' => $invoice_no,
                'user_id' => $user_id,
                'coupon_id' => $coupon_id,
                'coupon_discount' => $coupon_discount,
                'listing_id' => $vendor_id,
                'vendor_id' => $vendor_type,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time,
                'patient_id' => $member_id,
                'payment_mode' => $payment_type,
                'user_name' => $user['name'],
                'user_mobile' => $user['phone'],
                'user_email' => $user['email'],
                'user_gender' => $user['gender'],
                'status' => $status
            );
            $this->db->insert('booking_master', $lab_test_order);
            
            $adress_list = $this->db->query("SELECT address1,address2,mobile,email,city,state,pincode from user_address where user_id='$user_id' limit 1")->row_array();
            $lab_test_order_details = array(
                'booking_id' => $invoice_no,
                'user_id' => $user_id,
                'listing_id' => $vendor_id,
                'vendor_type' => $vendor_type,
                'test_id' => $test_id,
                'package_id' => $package_id,
                'amount' => $debit,
                'total_cost' => $total_cost,
                'total_discount' => $total_discount,
                'booking_date' => $booking_date,
                'booking_time' => $booking_time,
                'address_id' => $address_id,
                'address_line1' => $adress_list['address1'],
                'address_line2' => $adress_list['address2'],
                'city' => $adress_list['city'],
                'state' => $adress_list['state'],
                'pincode' => $adress_list['pincode'],
                'mobile_no' => $adress_list['mobile'],
                'email_id' => $user_email
            );
            
            $ledger_id='0';
            $ledger_type='user';
            $this->db->insert('lab_booking_details', $lab_test_order_details);
            if($invoice_no>0 && $payment_type!='3'){
				$ledger_owner_type='0';
				$listing_id_type='31';
				$credit='';
				$user_comments='';
				$mw_comments='';
				$vendor_comments='';
				$order_type='2';
				$transaction_of='3';
				$transaction_id='';
				$trans_status='';
				$transaction_date='';
				
                $data=array();
                $vendor_type='10';
				$res=$this->LedgerModel->create_ledger($user_id, $invoice_no, $ledger_owner_type, $vendor_id, $listing_id_type, $credit, $debit, $payment_type, $user_comments, $mw_comments,$vendor_comments,$order_type,$transaction_of,$transaction_id,$trans_status,$transaction_date,$vendor_type,$data);
				$ledger_id=$res['ledger_id'];
				$ledger_type=$res['type'];
            }
			
            $response[] = array(
                'booking_id' => $invoice_no,
                'ledger_id' => $ledger_id,
                'ledger_type' => $ledger_type
            );
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $response
            );
        }
    }
    
    public function addtocart($user_id,$test_id,$vendor_id,$package_id)
    {
        if(count($test_id)>0){
            //$this->db->query("DELETE FROM `lab_test_cart` WHERE user_id='$user_id'");
            $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id' and test_id>0");
            $test_id = rtrim($test_id, ',');
            $test_list = explode(',',$test_id);
            foreach ($test_list as $tid) {
                $cart_query = $this->db->query("SELECT id from lab_vendor_cart where test_id='$tid' and user_id='$user_id' and vendor_id='$vendor_id'");
                $count  = $cart_query->num_rows();
                if($tid>0 && $count==0){
                    $test_cart = array(
                        'user_id' => $user_id,
                        'vendor_id' => $vendor_id,
                        'test_id' => $tid
                    );
                    $this->db->insert('lab_vendor_cart', $test_cart);
                }
            }
        }
        if(count($package_id)>0){
            $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id' and package_id>0");
            $package_id = rtrim($package_id, ',');
            $package_list = explode(',',$package_id);
            foreach ($package_list as $pid) {
                $cart_query = $this->db->query("SELECT id from lab_vendor_cart where package_id='$pid' and user_id='$user_id' and vendor_id='$vendor_id'");
                $count  = $cart_query->num_rows();
                if($pid>0 && $count==0){
                    $package_cart = array(
                        'user_id' => $user_id,
                        'vendor_id' => $vendor_id,
                        'package_id' => $pid
                    );
                    $this->db->insert('lab_vendor_cart', $package_cart);
                }
            }
        }
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function add_package($user_id,$package_id,$vendor_id,$clear_all)
    {
        if($package_id==0){
            $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id' and package_id>0");
        }
        if($clear_all=='1'){
            $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id'");
            $this->db->query("DELETE FROM `lab_test_cart` WHERE user_id='$user_id'");
            $package_id = rtrim($package_id, ',');
                        $package_list = explode(',',$package_id);
                        foreach ($package_list as $pid) {
                            $cart_query = $this->db->query("SELECT id from lab_vendor_cart where package_id='$pid' and user_id='$user_id' and vendor_id='$vendor_id'");
                            $count  = $cart_query->num_rows();
                            if($pid>0 && $count==0){
                                $package_cart = array(
                                    'user_id' => $user_id,
                                    'vendor_id' => $vendor_id,
                                    'package_id' => $pid
                                );
                                $cart_query = $this->db->query("SELECT id from lab_vendor_cart where package_id='$pid' and user_id='$user_id'");
                                $count  = $cart_query->num_rows();
                                if($count==0){
                                    $this->db->insert('lab_vendor_cart', $package_cart);
                                }
                                
                                
                            }
                        }
            
            return array(
                'status' => 200,
                'message' => 'success'
            );
            
        }
        else{
             if($vendor_id>0){
                $flag=0;
                $test_query = $this->db->query("SELECT vendor_id from lab_vendor_cart where user_id='$user_id'");
                $num_count  = $test_query->num_rows();
                if ($num_count > 0) {
                    $rows_list  = $test_query->result();
                    foreach ($rows_list as $vrow) {
                        if($vrow->vendor_id!=$vendor_id){
                            $flag=1;
                            break;
                        }
                    }
                }
                if($flag>0){
                    return array(
                        'status' => 200,
                        'message' => 'success',
                        'is_vendor'=> 0
                    );
                }
                else{
                    if(count($package_id)>0){
                        $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id' and package_id>0");
                        $package_id = rtrim($package_id, ',');
                        $package_list = explode(',',$package_id);
                        foreach ($package_list as $pid) {
                            $cart_query = $this->db->query("SELECT id from lab_vendor_cart where package_id='$pid' and user_id='$user_id' and vendor_id='$vendor_id'");
                            $count  = $cart_query->num_rows();
                            if($pid>0 && $count==0){
                                $package_cart = array(
                                    'user_id' => $user_id,
                                    'vendor_id' => $vendor_id,
                                    'package_id' => $pid
                                );
                                $cart_query = $this->db->query("SELECT id from lab_vendor_cart where package_id='$pid' and user_id='$user_id'");
                                $count  = $cart_query->num_rows();
                                if($count==0){
                                    $this->db->insert('lab_vendor_cart', $package_cart);
                                }
                                
                                
                            }
                        }
                    }
                    return array(
                        'status' => 200,
                        'message' => 'success',
                        'is_vendor'=> 1
                    );
                }
            }  
        }
            

        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
    
    public function addtestcart($user_id,$test_id,$vendor_id,$clear_all)
    {
        $test_id = rtrim($test_id, ',');
        $test_list = explode(',',$test_id);
        
        if($clear_all=='1'){
            $this->db->query("DELETE FROM `lab_vendor_cart` WHERE user_id='$user_id'");
            $this->db->query("DELETE FROM `lab_test_cart` WHERE user_id='$user_id'");
            if($vendor_id>0){
                foreach ($test_list as $tid) {
                    if($tid>0){
                        $test_cart = array(
                            'user_id' => $user_id,
                            'test_id' => $tid,
                            'vendor_id'=>$vendor_id
                        );
                        $cart_query = $this->db->query("SELECT id from lab_vendor_cart where test_id='$tid' and user_id='$user_id'");
                        $count  = $cart_query->num_rows();
                        if($count==0){
                            $this->db->insert('lab_vendor_cart', $test_cart);
                        } 
                    }
                }
            }
            else{
                foreach ($test_list as $tid) {
                if($tid>0){
                    $test_cart = array(
                        'user_id' => $user_id,
                        'test_id' => $tid
                    );
                    $cart_query = $this->db->query("SELECT id from lab_test_cart where test_id='$tid' and user_id='$user_id'");
                    $count  = $cart_query->num_rows();
                    if($count==0){
                        $this->db->insert('lab_test_cart', $test_cart);
                    }
                }
            }
            }
            
            
            return array(
                    'status' => 200,
                    'message' => 'success'
                );
        }
        else{
            if($vendor_id>0){
                $flag=0;
                foreach ($test_list as $tid) {
                    $cart_query = $this->db->query("SELECT id from lab_test_master_details where test_id='$tid' and user_id='$vendor_id'");
                    $count  = $cart_query->num_rows();
                    if ($count==0) {
                        $flag=1;
                        break;
                    }
                }
                if($flag>0){
                    return array(
                        'status' => 200,
                        'message' => 'success',
                        'is_vendor'=> 0
                    );
                }
                else{
                    foreach ($test_list as $tid) {
                        if($tid>0){
                            $test_cart = array(
                                'user_id' => $user_id,
                                'test_id' => $tid,
                                'vendor_id'=>$vendor_id
                            );
                            $cart_query = $this->db->query("SELECT id from lab_vendor_cart where test_id='$tid' and user_id='$user_id'");
                            $count  = $cart_query->num_rows();
                            if($count==0){
                                $this->db->insert('lab_vendor_cart', $test_cart);
                            }
                            
                        }
                    }
                    return array(
                        'status' => 200,
                        'message' => 'success',
                        'is_vendor'=> 1
                    );
                }
            }
            else{
                $this->db->query("DELETE FROM `lab_test_cart` WHERE user_id='$user_id'");
                foreach ($test_list as $tid) {
                    if($tid>0){
                        $test_cart = array(
                            'user_id' => $user_id,
                            'test_id' => $tid
                        );
                        $cart_query = $this->db->query("SELECT id from lab_test_cart where test_id='$tid' and user_id='$user_id'");
                        $count  = $cart_query->num_rows();
                        if($count==0){
                            $this->db->insert('lab_test_cart', $test_cart);
                        }
                    }
                }
                return array(
                    'status' => 200,
                    'message' => 'success'
                );
            }
        }
    }
    
    public function showtestcart($user_id){
        $packagetest_list=array();
        $test_cart=array();
        $package_cart=array();
        $vendor_cart=array();
        $cart_array=array();
        $vendor_id=0;
        $vendor_name='';
        $package_test_list=array();
        $test_query = $this->db->query("SELECT lab_test_cart.test_id,lab_test_master.name from lab_test_cart INNER JOIN lab_test_master on lab_test_master.id=lab_test_cart.test_id where lab_test_cart.user_id='$user_id'");
        $num_count  = $test_query->num_rows();
        if ($num_count > 0) {
            $rows_list  = $test_query->result();
            foreach ($rows_list as $vrow) {
                $test_id  = $vrow->test_id;
                $test_name  = $vrow->name;
                $test_cart[] = array(
                    'type' => 'TEST',
                    'test_id' => $test_id,
                    'test_id_id' => $test_id,
                    'test_name' => $test_name
                );
            }
        }

        
        $query = $this->db->query("SELECT DISTINCT lab_vendor_cart.test_id,lab_vendor_cart.vendor_id,lab_center.lab_name as name,lab_center.profile_pic from lab_vendor_cart INNER JOIN lab_center on lab_center.user_id=lab_vendor_cart.vendor_id where lab_vendor_cart.user_id='$user_id' GROUP by lab_vendor_cart.test_id");
        $tnum_count  = $query->num_rows();
        if ($tnum_count > 0) {
            $row_list  = $query->result();
            foreach ($row_list as $row) {
                $test_id  = $row->test_id;
                $vendor_id = $row->vendor_id;
                $vendor_name = $row->name;
                $vendor_name = $row->name;
                $vimage = $row->profile_pic;
                $vimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $vimage;
                $cart_query = $this->db->query("SELECT ld.home_delivery,ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,lab_test_master.name,lab_test_master.id from lab_test_master_details ld INNER JOIN lab_test_master on lab_test_master.id=ld.test_id where ld.test_id='$test_id' and ld.user_id='$vendor_id' group by ld.user_id");
                $cart_count  = $cart_query->num_rows();
                if ($cart_count > 0) {
                    $test_row = $cart_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = $test_list->report_availability;
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price==0){
                            $discounted_price = $test_list->price;    
                        }
                        $discount  = $test_list->discount;
                        $home_delivery  = $test_list->home_delivery;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $cart_array[] = array(
							'type' => 'TEST',
                            'test_id' => $testid,
                            'test_id_id' => $test_id,
                            'test_name' => $test_name,
                            'code' => $code,
                            'report_availability' => $report_availability,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => $home_delivery
                        );
                    }
                }
            }
        }
		$packagequery = $this->db->query("SELECT pk.id,pk.code,pk.name,pk.description,pk.home_collection_charges,pk.price,pk.discounted_price,lab_center.lab_name as vendor_name,lab_vendor_cart.vendor_id from lab_vendor_cart INNER JOIN lab_package_master pk on pk.id=lab_vendor_cart.package_id INNER JOIN lab_center on lab_center.user_id=lab_vendor_cart.vendor_id where lab_vendor_cart.user_id='$user_id' GROUP by pk.id");
        $pnum_count  = $packagequery->num_rows();
        if ($pnum_count > 0) {
            $rowslist  = $packagequery->result();
            foreach ($rowslist as $vrow) {
				$vendor_id = $vrow->vendor_id;
                $vendor_name = $vrow->vendor_name;
                $package_id  = $vrow->id;
                $code = $vrow->code;
                $pname = $vrow->name;
                $description = $vrow->description;
                $home_collection_charges = $vrow->home_collection_charges;
                $price = $vrow->price;
                $discounted_price = $vrow->discounted_price;    
                if($discounted_price==0){
                    $discounted_price = $vrow->price;    
                }
                $testquery = $this->db->query("SELECT code,name from lab_package_test_master where package_id='$package_id'");
                $num_count  = $testquery->num_rows();
                if ($num_count > 0) {
                    $rowlist  = $testquery->result();
                    foreach ($rowlist as $row) {
                        $code = $row->code;
                        $name = $row->name;
                        $packagetest_list[] = array(
                            'test_code' => $code,
                            'test_name' => $name
                        );
                    }
                }                
                $cart_array[] = array(
					'type' => 'PACKAGE',
                    'test_id' => $package_id,
                    'test_name' => $pname,
                    'package_id' => $package_id,
                    'code' => $code,
                    'name' => $pname,
                    'description' => $description,
                    'home_collection_charges' => $home_collection_charges,
                    'price' => $price,
                    'discounted_price' => $discounted_price,
                    'test_list' => $packagetest_list
                );
            }
        }
		
		if($tnum_count>0 || $pnum_count>0){
		    $vendor_cart[] =array(
            'vendor_id' => $vendor_id,
            'vendor_name' => $vendor_name,
            'image' => $vimage,
            'test_list' => $cart_array
        );
        $test_cart=array();
		}
        
        $cart_list= array(
            'test_cart' => $test_cart,
            'vendor_cart' => $vendor_cart
        );
        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $cart_list
        );
    }
    
    public function all_lab_package($page,$category,$most_common_risk,$sort_type,$home_delivery,$vendor_id,$featured,$recommended,$keyword,$per_page){
        if(!empty($per_page)){
            $limit = $per_page;
        }
        else{
            $limit = 10;
        }
        
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start      = ($page - 1) * $limit;

        $filter_sort='';
        $test_list = array();
        $package_list = array();
        $keyword_search='';
        $vendor_id_filter='';
        if($vendor_id>0)
        {
            $vendor_id_filter = ' and lab_package_master.user_id='.$vendor_id;
        }
        
        if($featured == 1){
            $featuredWhere = "AND lab_package_master.is_featured = 1"; 
        } else {
            $featuredWhere = ""; 
        }
        if($recommended == 1){
            $recommendedWhere = "AND lab_package_master.recommended = 1"; 
        } else {
            $recommendedWhere = ""; 
        } 
        
        if(!empty($keyword))
        {
            $keyword_search = " and lab_package_master.name like '%$keyword%' ";
        }

        if(!empty($sort_type))
        {
            if ($sort_type == '1'){
                $filter_sort = " order by lab_package_master.name asc";
            }
            elseif ($sort_type == '2'){
                $filter_sort = " order by lab_package_master.name desc";
            }
            elseif ($sort_type == '3'){
                $filter_sort = " order by lab_package_master.price asc";
            }
            elseif ($sort_type == '4'){
                $filter_sort = " order by lab_package_master.price desc";
            }
            else{
                $filter_sort = " order by lab_package_master.name asc";
            }
        }
        
        $query      = $this->db->query("SELECT lab_package_master.*,lab_center.lab_name as vendor_name from lab_package_master INNER JOIN lab_center on lab_center.user_id=lab_package_master.user_id where lab_package_master.user_id>0 $featuredWhere $recommendedWhere $keyword_search $vendor_id_filter group by lab_package_master.id $filter_sort limit $start,$limit");
       // echo "SELECT lab_package_master.*,lab_center.lab_name as vendor_name from lab_package_master INNER JOIN lab_center on lab_center.user_id=lab_package_master.user_id where lab_package_master.user_id>0 $featuredWhere $recommendedWhere $keyword_search $vendor_id_filter $filter_sort";
        $package_count = $query->num_rows();
        if ($package_count > 0) {
            $packagelist = $query->result();
            foreach ($packagelist as $package) {
                $package_id = $package->id;
                $code = $package->code;
                $vendor_id = $package->user_id;
                $feature = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
                $certification = $feature['feature'];
                $vendor_name = $package->vendor_name;
                
                $rating = '4.5';
                $report_availability = '1';
                $home_collection_charges = $package->home_collection_charges;
                $price = $package->price;
                $discounted_price = $package->discounted_price;
                if($discounted_price==0){
                    $discounted_price = $package->price;
                }
                $discount = $package->discount;
                $medicalwale_discount = $package->medicalwale_discount;
                $home_delivery = '1';
                $package_name = $package->name;
                
                $test_query = $this->db->query("SELECT id as test_id,code,group_name,name as test_name,type FROM `lab_package_test_master` where package_id='$package_id'");
                $num_count  = $test_query->num_rows();
                if ($num_count > 0) {
                    $rows_list  = $test_query->result();
                    foreach ($rows_list as $row) {
                        $test_id    = $row->test_id;
                        $test_code  = $row->code;
                        $group_name = $row->group_name;
                        $test_name  = $row->test_name;
                        $type       = $row->type;
                        $test_list[] = array(
                            'test_id' => $test_id,
                            'code' => $test_code,
                            'name' => $test_name,
                            'group_name' => $group_name,
                            'aliasname' => '',
                            'description' => '',
                            'instructions' => '',
                            'components' => '',
                            'fasting' => '',
                            'type' => $type
                        );
                    }
                }
                $package_list[] = array(
                    'package_id' => $package_id,
                    'code' => $code,
                    'vendor_id' => $vendor_id,
                    'vendor_name' => $vendor_name,
                    'certification' => $certification,
                    'rating' => $rating,
                    'report_availability' => $report_availability,
                    'home_collection_charges' => $home_collection_charges,
                    'price' => $price,
                    'discounted_price' => $discounted_price,
                    'discount' => $discount,
                    'medicalwale_discount' => $medicalwale_discount,
                    'home_delivery' => $home_delivery,
                    'package_name' => $package_name,
                    'test' => $test_list
                );
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'data_count' => $package_count,
                'data' => $package_list
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'No data found!'
            );
        }
    }
    
    public function all_lab_test($page,$category,$most_common_risk,$sort_type,$home_delivery,$vendor_id,$keyword,$per_page,$type){
        
        if(!empty($per_page)){
            $limit = $per_page;
        }
        else{
            $limit = 10;
        }
        
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $test_list = array();
        $start      = ($page - 1) * $limit;
        $category_filter='';
        $most_common_risk_filter='';
        $home_delivery_filter='';
        $vendor_id_filter='';
        $filter_sort='';
        $keyword_search='';
        $type_filter='';
        
        if($type>0){
            $type_filter = ' and lab_test_master.category_id='.$type;
        }
        if($category>0){
            $category_filter = ' and FIND_IN_SET(lab_test_master.test_category, '.$category.')';
        }
        if(!empty($most_common_risk))
        {
            $most_common_risk_filter = ' and FIND_IN_SET(lab_test_master.most_common_factor, '.$most_common_risk.')';
        }
        if($home_delivery=='1' || $home_delivery=='0')
        {
            $home_delivery_filter = ' and lab_test_master_details.home_delivery='.$home_delivery;
        }
        if($vendor_id>0)
        {
            $vendor_id_filter = ' and lab_test_master_details.user_id='.$vendor_id;
        }
        if(!empty($keyword))
        {
            $keyword_search = " and lab_test_master.name like '%$keyword%' ";
        }

        if(!empty($sort_type))
        {
            if ($sort_type == '1'){
                $filter_sort = " order by lab_test_master.name asc";
            }
            elseif ($sort_type == '2'){
                $filter_sort = " order by lab_test_master.name desc";
            }
            elseif ($sort_type == '3'){
                $filter_sort = " order by lab_test_master_details.price asc";
            }
            elseif ($sort_type == '4'){
                $filter_sort = " order by lab_test_master_details.price desc";
            }
            else{
                $filter_sort ='order by lab_test_master.name asc';
            }
        }
        
        $query      = $this->db->query("SELECT lab_test_master.id as test_id,lab_test_master.name,lab_test_master.aliasname,lab_test_master.description,lab_test_master.instructions,lab_test_master.components,lab_test_master.fasting FROM `lab_test_master` INNER JOIN lab_test_master_details on lab_test_master_details.test_id=lab_test_master.id where lab_test_master.name<>'' $type_filter $category_filter $most_common_risk_filter $home_delivery_filter $vendor_id_filter $keyword_search group by lab_test_master_details.test_id");
        $vendor_list='';
        $total_test = $query->num_rows();
        if ($total_test > 0) {
            $test_query = $this->db->query("SELECT lab_test_master.id as test_id,lab_test_master.name,lab_test_master.aliasname,lab_test_master.description,lab_test_master.instructions,lab_test_master.components,lab_test_master.fasting,lab_test_master_details.type,lab_test_master_details.home_delivery,lab_test_master_details.code,lab_test_master_details.report_availability,lab_test_master_details.home_collection_charges,lab_test_master_details.price,lab_test_master_details.discounted_price,lab_test_master_details.discount,lab_test_master_details.medicalwale_discount FROM `lab_test_master` INNER JOIN lab_test_master_details on lab_test_master_details.test_id=lab_test_master.id where lab_test_master.name<>'' $type_filter $category_filter $most_common_risk_filter $home_delivery_filter $vendor_id_filter $keyword_search group by lab_test_master_details.test_id $filter_sort limit $start,$limit");
            $num_count  = $test_query->num_rows();
            if ($num_count > 0) {
                $rows_list  = $test_query->result();
                $price=0;
                $discounted_price=0;
                $discount=0;
                $medicalwale_discount=0;
                $report_availability=0;
                $home_collection_charges=0;
                $homedelivery  = '';
                $code  = '';
                foreach ($rows_list as $row) {
                    $test_id        = $row->test_id;
                    $name           = $row->name;
                    $aliasname          = $row->aliasname;
                    $description = $row->description;
                    $instructions  = $row->instructions;
                    $components  = $row->components;
                    $fasting  = $row->fasting;
                    $type  = $row->type;
                    
                    if($vendor_id>0)
                    {
                    $price  = $price+$row->price;
                    if($row->discounted_price=='0'){
                        $discounted_price  = $discounted_price+$row->price;
                    }
                    else{
                        $discounted_price  = $discounted_price+$row->discounted_price;
                    }
                    $discount  = $row->discount;
                    $medicalwale_discount  = $row->medicalwale_discount;
                    $report_availability  = $row->report_availability;
                    $home_collection_charges  = $row->home_collection_charges;
                    $code  = $row->code;
                    $homedelivery  = $row->home_delivery;
                    }
                    
                    
                    $test_list[] = array(
                        'test_id' => $test_id,
                        'name' => $name,
                        'aliasname' => $aliasname,
                        'description' => $description,
                        'instructions' => $instructions,
                        'components' => $components,
                        'fasting' => $fasting,
                        'type' => $type,
                        'code' => $code,
                        'report_availability' => $report_availability,
                        'home_collection_charges' => $home_collection_charges,
                        'price' => $price,
                        'discounted_price' => $discounted_price,
                        'discount' => $discount,
                        'medicalwale_discount' => $medicalwale_discount,
                        'home_delivery' => $homedelivery
                   );
                }
            }
            return array(
                'status' => 200,
                'message' => 'success',
                'data_count' => $num_count,
                'data' => $test_list
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'No data found!'
            );
        }
    }
    
    public function test_list_by_vendor($user_id,$lat,$lng,$page,$home_delivery,$sort_type)
    {
        $filter='';
        $pre='and ';
        $vendor_list=array();
        $user_list='';
        $filter_sort = " ORDER BY distance asc";
        $home_delivery_filter='';
        $featuredWhere = ""; 
        $recommendedWhere = ""; 
        
        if($home_delivery=='1' || $home_delivery=='0')
        {
            $home_delivery_filter = ' and lab_test_master_details.home_delivery='.$home_delivery;
        }
        
        if(!empty($sort_type))
        {
            if ($sort_type == '1'){
                $filter_sort = " ORDER BY vendor_name,distance asc";
            }
            elseif ($sort_type == '2'){
                $filter_sort = " ORDER BY vendor_name,distance desc";
            }
            elseif($sort_type == '3'){
                $featuredWhere = "AND lab_center.is_featured = 1"; 
            }
            elseif($sort_type == '4'){
                $recommendedWhere = "AND lab_center.recommended = 1"; 
            }
        }
        
        
        $test_query = $this->db->query("SELECT test_id from lab_test_cart where user_id='$user_id'");
        $numcount  = $test_query->num_rows();
        $querys = $this->db->query("SELECT lab_test_master_details.user_id,count(DISTINCT lab_test_cart.test_id) as test_count FROM lab_test_master_details INNER JOIN lab_test_cart on FIND_IN_SET(lab_test_master_details.test_id,lab_test_cart.test_id) where lab_test_cart.user_id='$user_id' $home_delivery_filter GROUP BY lab_test_master_details.user_id");
        $rowlist  = $querys->result();
        foreach ($rowlist as $row) {
            $userid  = $row->user_id;
            $test_count  = $row->test_count;
            if($test_count==$numcount){
                $filter.=$pre." ld.user_id='$userid'";
                $pre='or';
            }
        }
        
        $limit = 10;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $start      = ($page - 1) * $limit;


        $certification='';
		$homedelivery='';
		$radius = '5000';
        $sql    = sprintf("SELECT ld.home_delivery,lab_center.user_id as vendor_id,lab_center.lab_name as vendor_name,lab_center.address1,lab_center.address2,lab_center.pincode,lab_center.city,lab_center.state,lab_center.contact_no,lab_center.latitude,lab_center.profile_pic,lab_center.longitude, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_test_master_details ld INNER JOIN lab_test_cart on lab_test_cart.test_id=ld.test_id INNER JOIN lab_center on lab_center.user_id=ld.user_id where lab_test_cart.user_id='$user_id' $featuredWhere $recommendedWhere $filter  group by ld.user_id HAVING distance < '%s'  $filter_sort limit $start,$limit", ($lat), ($lng), ($lat), ($radius));
		$image='';
        //$test_query = $this->db->query("SELECT lab_center.user_id as vendor_id,lab_center.lab_name as vendor_name from lab_test_master_details ld INNER JOIN lab_test_cart on lab_test_cart.test_id=ld.test_id INNER JOIN lab_center on lab_center.user_id=ld.user_id where lab_test_cart.user_id='$user_id' $filter group by ld.user_id limit $start,$limit");
		
		$test_query  = $this->db->query($sql);
        $num_count   = $test_query->num_rows(); 
            if ($num_count > 0) {
                foreach ($test_query->result() as $vrow) {
                        $vendor_name = $vrow->vendor_name;
                        $vendor_id = $vrow->vendor_id;
                        if(!empty($vrow->profile_pic)){
                            $image='https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$vrow->profile_pic;
                        }
                        $price=0;
                        $discounted_price=0;
                        $discount=0;
                        $medicalwale_discount=0;
                        $report_availability=0;
                        $home_collection_charges=0;
                        $price_query = $this->db->query("SELECT lab_test_master_details.home_delivery,lab_test_master_details.code,lab_test_master_details.report_availability,lab_test_master_details.home_collection_charges,lab_test_master_details.price,lab_test_master_details.discounted_price,lab_test_master_details.discount,lab_test_master_details.medicalwale_discount FROM lab_test_master_details INNER JOIN lab_test_cart on lab_test_cart.test_id=lab_test_master_details.test_id where lab_test_cart.user_id='$user_id' and lab_test_master_details.user_id='$vendor_id'");
                        $price_list  = $price_query->result();
                        foreach ($price_list as $prow) {
                            $price  = $price+$prow->price;
                            if($prow->discounted_price=='0'){
                                $discounted_price  = $discounted_price+$prow->price;
                            }
                            else{
                                $discounted_price  = $discounted_price+$prow->discounted_price;
                            }
                            $discount  = $prow->discount;
                            
                            $medicalwale_discount  = $prow->medicalwale_discount;
                            $report_availability  = $prow->report_availability;
                            $home_collection_charges  = $prow->home_collection_charges;
                            $code  = $prow->code;
                        }
                        
                        $address1=$vrow->address1;
                        $address2=$vrow->address2;
                        $pincode=$vrow->pincode;
                        $state=$vrow->state;
                        $city=$vrow->city;
                        $contact_no=$vrow->contact_no;
                        $lat=$vrow->latitude;
                        $lng=$vrow->longitude;
                        $homedelivery  = $prow->home_delivery;
                        $testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
                        $certification=$testInfo['feature'];
                  
                        $vendor_list[] = array(
                            'vendor_id' => $vendor_id,
                            'vendor_name' => $vendor_name,
                            'image' => $image,
                            'address1' => $address1,
                            'address2' => $address2,
                            'pincode' => $pincode,
                            'state' => $state,
                            'city' => $city,
                            'contact_no' => $contact_no,
                            'lat' => $lat,
                            'lng' => $lng,
                            'certification' => $certification,
                            'rating' => '4.5',
                            'code' => $code,
                            'report_availability' => $report_availability,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => $homedelivery
                        );
                        
                        
                    }
            }
            
            
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $vendor_list
            );
    }
    
    public function vendor_cart_list($user_id)
    {
        $query = $this->db->query("SELECT lab_vendor_cart.test_id,lab_vendor_cart.vendor_id,lab_center.lab_name from lab_vendor_cart INNER JOIN lab_center on lab_center.user_id=lab_vendor_cart.vendor_id where lab_vendor_cart.user_id='$user_id'");
        $num_count  = $query->num_rows();
        if ($num_count > 0) {
            $row_list  = $query->result();
            foreach ($row_list as $row) {
                $test_id  = $row->test_id;
                $vendor_id = $row->vendor_id;
                $vendor_name = $row->name;
                
                $test_query = $this->db->query("SELECT ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount,lab_test_master.name,lab_test_master.id from lab_test_master_details ld INNER JOIN lab_test_master on lab_test_master.id=ld.test_id where ld.test_id='$test_id' and ld.user_id='$vendor_id'");
                $test_count  = $test_query->num_rows();
                if ($test_count > 0) {
                    $test_row = $test_query->result();
                    foreach ($test_row as $test_list) {
                        $testid  = $test_list->id;
                        $test_name  = $test_list->name;
                        $code  = $test_list->code;
                        $report_availability  = $test_list->report_availability;
                        $home_collection_charges  = $test_list->home_collection_charges;
                        $price  = $test_list->price;
                        $discounted_price  = $test_list->discounted_price;
                        if($discounted_price=='0'){
                            $discounted_price  = $test_list->price;
                        }
                        $discount  = $test_list->discount;
                        $medicalwale_discount  = $test_list->medicalwale_discount;
                        $test_array[] = array(
                            'test_id' => $testid,
                            'test_name' => $test_name,
                            'code' => $code,
                            'report_availability' => $report_availability,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount,
                            'home_delivery' => "0"
                        );
                    }
                }
            }
            $vendor_list[] =array(
                'vendor_id' => $vendor_id,
                'vendor_name' => $vendor_name,
                'test_list' => $test_array
            );
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $vendor_list
            );
        }
        else{
            $vendor_list=array();
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $vendor_list
            );
        }

    }
    
    public function lab_home($user_id,$lat,$lng,$page){
        $banner_list=array();
        $feature_list=array();
        $recommeded_list=array();
        $near_by_you=array();
        $query  = $this->db->query("SELECT banner_image,v_id FROM `banner_images` WHERE vendor_type='10' and status='1' order by id DESC");
        $rows_list     = $query->result();
        foreach ($rows_list as $row) {
            $vendor_id  = $row->v_id;
            $image  = $row->banner_image;
            $banner_list[] = array(
                'vendor_id' => $vendor_id,
                'image' => $image
            );
        }
        
        $query  = $this->db->query("SELECT id,name,image FROM `lab_test_category` where is_active='1' order by sort asc");
        $rows_list     = $query->result();
        foreach ($rows_list as $row) {
            $id  = $row->id;
            $name  = $row->name;
            $image  = $row->image;
            $category_list[] = array(
                'id' => $id,
                'name' => $name,
                'image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/Labs/Lab_category/'.$image
            );
        }
        
        $query  = $this->db->query("SELECT id,name,image FROM `lab_test_most_common_factor` where is_active='1' order by sort asc");
        $rows_list     = $query->result();
        foreach ($rows_list as $row) {
            $id  = $row->id;
            $name  = $row->name;
            $image  = $row->image;
            $common_factor[] = array(
                'id' => $id,
                'name' => $name,
                'image' => 'https://d2c8oti4is0ms3.cloudfront.net/images/CommonRiskArea/'.$image
            );
        }
        $category_id='';
        $hospital_type='';
        $per_page='10';
        $featured='1';
        $recommended='0';
        $certified='';
        $mba='';
        
        //$feature_list=$this->LabcenterModel->labcenter_home_list($lat, $lng, $user_id, $category_id, $hospital_type,$per_page,$page,$featured,$recommended,$certified,$mba);
        $featured='0';
        $recommended='1';
        //$recommeded_list=$this->LabcenterModel->labcenter_home_list($lat, $lng, $user_id, $category_id, $hospital_type,$per_page,$page,$featured,$recommended,$certified,$mba);

        $array_list[] = array(
            'banner_list' => $banner_list,
            'category_list' => $category_list,
            'common_factor'=>$common_factor,
            'featured'=>$feature_list,
            'recommended'=>$recommeded_list,
            'near_by_you'=>$feature_list
        );
        return array(
            'status' => 200,
            'message' => 'success',
            'data' => $array_list
        );
    }
    
    
    public function lab_search($user_id,$keyword,$page,$lat,$lng){
        $test_list=array();
        $package_list=array();
        $center_list=array();
        $per_page='5';
        $category='';
        $most_common_risk='';
        $sort_type='';
        $home_delivery='';
        $vendor_id='';
        $featured='';
        $recommended='';
        $type='0';
        $center_list  = $this->LabcenterModel->search_list($user_id,$keyword,$page,$lat,$lng,$per_page);
        $test_list    = $this->LabcenterModel->all_lab_test($page,$category,$most_common_risk,$sort_type,$home_delivery,$vendor_id,$keyword,$per_page,$type);
        $package_list = $this->LabcenterModel->all_lab_package($page,$category,$most_common_risk,$sort_type,$home_delivery,$vendor_id,$featured,$recommended,$keyword,$per_page);
        
        if($package_list['status']!='201'){
            $package_list=$package_list['data'];
        }else{
            $package_list=array();
        }
        
        if($test_list['status']!='201'){
            $test_list=$test_list['data'];
        }else{
            $test_list=array();
        }

        $array_list[] = array(
            'test_list' => $test_list,
            'package_list' => $package_list,
            'center_list'=>$center_list
        );
        return $array_list;
    }
    
    public function search_list($user_id,$keyword,$page_no,$lat,$lng,$per_page)
    {
        $is_thyrocare  = $dataTotalCount = 0;
        $todayDay = date("l");
        $recommendedWhere = $certifiedWhere = $mbaWhere = $featuredWhere = $catWhere = $store_open = "";
        $store_close = "";
        $open_close_status = "";

        if($per_page != "" && $page_no != ""){
            $offset = $per_page*($page_no - 1);
            
            $wherePagination  = "LIMIT $per_page OFFSET $offset";
        } else {
            $wherePagination = "";
            $page_no = 1;
        }
        $radius = '5';
        $sql    = sprintf("SELECT lab_center.id,lab_center.user_id,lab_center.cat_id,lab_center.profile_pic,lab_center.lab_name,lab_center.address1,lab_center.address2,lab_center.pincode, lab_center.city, lab_center.state, lab_center.contact_no,lab_center.whatsapp_no, lab_center.email, lab_center.opening_hours, lab_center.latitude, lab_center.longitude, lab_center.reg_date,lab_center.features,lab_center.home_delivery,lab_center.user_discount, IFNULL(lab_center.delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lab_center.latitude) ) * cos( radians( lab_center.longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lab_center.latitude) ) ) ) AS distance FROM lab_center INNER JOIN lab_test_master_details on lab_test_master_details.user_id=lab_center.user_id WHERE  lab_center.is_active = 1  and (lab_center.latitude<>'' and lab_center.longitude<>'') GROUP by lab_test_master_details.user_id  HAVING distance < '%s' ORDER BY distance $wherePagination", ($lat), ($lng), ($lat), ($radius));
        $query  = $this->db->query($sql);
        $count  = $query->num_rows(); 
        $dataTotalCount = $count;
        
        
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                = $row['id'];
                $labcenter_user_id = $row['user_id'];
                $lab_name          = $row['lab_name'];
                $features          = $row['features'];
                $home_delivery     = $row['home_delivery'];
                $delivery_charges  = $row['delivery_charges'];
                $address1          = $row['address1'];
                $address2          = $row['address2'];
                $pincode           = $row['pincode'];
                $city              = $row['city'];
                $state             = $row['state'];
                $contact_no        = $row['contact_no'];
                $whatsapp_no       = $row['whatsapp_no'];
                $email             = $row['email'];
                $opening_hours     = $row['opening_hours'];
                $lat               = $row['latitude'];
                $lng               = $row['longitude'];
                $listing_type      = '10';
                $rating            = '4.0';
                $profile_views     = '';
                $reviews           = '1000';
                $user_discount     = $row['user_discount'];
                $image             = $row['profile_pic'];
                
                
                // if lab is thyrocare then call thyrocare APIs 
                if($labcenter_user_id == 4684){
                    $is_thyrocare = 1;
                }else {
                    $is_thyrocare = 0;
                }
                
               // $image             = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
               $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                $features_array    = array();
                $feature_query     = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                foreach ($feature_query->result_array() as $get_list) {
                    $feature          = $get_list['feature'];
                    $features_array[] = array(
                        "name" => $feature
                    );
                }
                $final_Day      = array();
                $day_array_list = explode('|', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time       = array();
                                $open_close = array();
                                for ($l = 0; $l < count($time_list1); $l++) {
                                    $tl1 = $time_list1[$l];
                                    $tl2 = $time_list2[$l];
                                    $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                    $time[]            = str_replace('close-close', 'close', $time_check);
                                    $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                    $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                    $current_time      = date('H.i');
                                    if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                        $ocs = $open_close[] = 'open';
                                        
                                    } else {
                                        $ocs = $open_close[] = 'close';
                                        
                                    }
                                }
                            }
                        }
                        $final_Day[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                        
                        if($todayDay == $day_list[0]){
                            $store_open = $tl1;
                            $store_close = $tl2;
                            $open_close_status = $ocs;
                        }
                        
                    }
                } else {
                    $final_Day[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }
                $current_day = "";
                $followers   = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                $following   = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                $is_follow   = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
               
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $package_list  = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages1`  WHERE  `v_id` = 10 AND user_id='$labcenter_user_id' and branch_id ='' order by id asc");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id      = $package_row['id'];
                        $package_name    = $package_row['package_name'];
                        $package_details = $package_row['package_details'];
                        $price           = $package_row['Price'];
                        $image           = $package_row['image'];
                        $home_delivery   = $package_row['home_delivery'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        
                        $Price = $package_row['Price'];
                        $discount_type = $package_row['discount_type'];
                        $discount = $package_row['discount'];
                        $full_body_checkup  = $package_row['full_body_checkup'];
                         if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                        } else {
                            $discounted_price = strval($Price); 
                        }
                        
                        $package_list[]  = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount_type' => $discount_type,
                            'discount' => $discount,
                            'full_body_checkup' => $full_body_checkup,
                            'home_delivery' => $home_delivery
                        );
                    }
                } else {
                    $package_list = array();
                }
				
				
				
				$package_list = array();
                $package_query = $this->db->query("SELECT lm.id,lm.code,lm.name,lm.home_collection_charges,lm.price,lm.discounted_price,count(pd.id) as test_count FROM lab_package_master lm INNER JOIN lab_package_test_master pd on pd.package_id=lm.id  WHERE lm.user_id='$labcenter_user_id' GROUP BY pd.package_id");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $code = $package_row['code'];
                        $name = $package_row['name'];
                        $home_collection_charges = $package_row['home_collection_charges'];
                        $price = $package_row['price'];
                        $discounted_price = $package_row['discounted_price'];
                        if($discounted_price==0){
                            $discounted_price = $package_row['price'];
                        }
                        $test_count = $package_row['test_count'];
                        $package_list[] = array(
                            'id' => $package_id,
                            'code' => $code,
                            'name' => $name,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'test_count' => $test_count
                        );
                    }
                } else {
                    $package_list = array();
                }            
				
				$test_list = array();
                $test_query       = $this->db->query("SELECT lt.id,lt.name,ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount from lab_test_master_details ld INNER JOIN lab_test_master lt on lt.id=ld.test_id where ld.user_id='$labcenter_user_id'");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $test_id        = $test_row['id'];
                        $name        = $test_row['name'];
                        $code           = $test_row['code'];
                        $report_availability        = $test_row['report_availability'];
                        $home_collection_charges          = $test_row['home_collection_charges'];
                        $price          = $test_row['price'];
                        $discounted_price = $test_row['discounted_price'];
                        if($discounted_price==0){
                            $discounted_price = $test_row['price'];
                        }
                        $discount  = $test_row['discount'];
                        $medicalwale_discount  = $test_row['medicalwale_discount'];
                        $test_list[] = array(
                            'test_id' => $test_id,
                            'name' => $name,
                            'code' => $code,
                            'report_availability' => $report_availability,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount
                        );
                    }
                } else {
                    $test_list  = array();
                }
                
				
                $branch_list  = array();
                // $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc");
                $branch_query = $this->db->query("SELECT lc.*,u.multi_user_type FROM `lab_center` as lc left join users as u on(lc.`branch_user_id` = u.id) WHERE lc.user_id='$labcenter_user_id' AND lc.`is_active` = 1 AND u.multi_user_type = 'branch' order by id asc");
                // echo "SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc"; die();
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['branch_user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        $branch_details                         = $branch_row['fnac_pick_up'];
                        /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        $branch_reg_date                        = $branch_row['reg_date'];
                        $branch_lab_branch_name                 = $branch_row['lab_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager                   = $branch_row['store_manager'];
                        $branch_latitude                        = $branch_row['latitude'];
                        $branch_map_location                    = $branch_row['map_location'];
                        $branch_longitude                       = $branch_row['longitude'];
                        $branch_address1                        = $branch_row['address1'];
                        $branch_address2                        = $branch_row['address2'];
                        $branch_pincode                         = $branch_row['pincode'];
                        $branch_features                        = $branch_row['features'];
                        $branch_city                            = $branch_row['city'];
                        $branch_state                           = $branch_row['state'];
                        $branch_contact_no                      = $branch_row['contact_no'];
                        $branch_whatsapp_no                     = $branch_row['whatsapp_no'];
                        $branch_email                           = $branch_row['email'];
                        $branch_profile_pic                     = $branch_row['profile_pic'];
                        $branch_about_us                        = $branch_row['about_us'];
                        $branch_reach_area                      = $branch_row['reach_area'];
                        $branch_distance                        = $branch_row['distance'];
                        $branch_store_since                     = $branch_row['store_since'];
                        $branch_opening_hours                   = $branch_row['opening_hours'];
                        $branch_home_deliverys                  = $branch_row['home_delivery'];
                        $branch_branch_profile                  = $branch_row['profile_pic'];
                        $branch_delivery_charges                = $branch_row['delivery_charges'];
                        $branch_payment_type                    = $branch_row['payment_type'];
                        $branch_discount                        = $branch_row['discount'];
                        $branch_services                        = $branch_row['terms_condition'];
                        $branch_home_visit                      = $branch_row['home_delivery'];
                        $branch_features_array                  = array();
                        $feature_query                          = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
                            $feature                 = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
                        $branch_final_Day      = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time       = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[]            = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                            $current_time      = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day         = "";
                        $package_branch_list = array();
                        $package_query       = $this->db->query("SELECT * FROM `lab_packages1`  WHERE `v_id` = 10 AND user_id='$labcenter_user_id' and branch_id='$branch_id' order by id asc");
                        $package_count       = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                // print_r($package_row); die();
                                $package_id            = $package_row['id'];
                                $package_name          = $package_row['package_name'];
                                $package_details       = $package_row['package_details'];
                                $price                 = $package_row['Price'];
                                $image                 = $package_row['image'];
                                $home_delivery         = $package_row['home_delivery'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'home_delivery' => $home_delivery
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' => $branch_store_manager,
                            'branch_latitude' => $branch_latitude,
                            'branch_map_location' => $branch_map_location,
                            'branch_longitude' => $branch_longitude,
                            'branch_address1' => $branch_address1,
                            'branch_address2' => $branch_address2,
                            'branch_pincode' => $branch_pincode,
                            'branch_features' => $branch_features_array,
                            'branch_city' => $branch_city,
                            'branch_state' => $branch_state,
                            'branch_contact_no' => $branch_contact_no,
                            'branch_whatsapp_no' => $branch_whatsapp_no,
                            'branch_email' => $branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic' => $branch_profile_pic,
                            'branch_about_us' => $branch_about_us,
                            'branch_reach_area' => $branch_reach_area,
                            'branch_distance' => $branch_distance,
                            'branch_store_since' => $branch_store_since,
                            'branch_package' => $package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                $test_in_lab_list = array();
                $test_in_home_list = array();
                // $test_query       = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' order by id asc");
                // $test_query       = $this->db->query("SELECT * FROM `lab_test_details1` WHERE user_id='$labcenter_user_id' order by id asc");
                $test_query       = $this->db->query("SELECT ltd.*, lat.test FROM `lab_test_details1` as ltd left join lab_all_test1 as lat on (ltd.test_id = lat.id) WHERE user_id='$labcenter_user_id' order by id asc");
                
                // echo "SELECT * FROM `lab_test_details1` WHERE user_id='$labcenter_user_id' order by id asc"; die();
                

                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        // print_r($test_row); die();
                        $user_id        = $test_row['user_id'];
                        $test           = $test_row['test'];
                        $test_id        = $test_row['test_id'];
                        $price          = $test_row['price'];
                        $offer          = $test_row['offer'];
                        $executive_rate = $test_row['discounted_price'];
                        $home_delivery  = $test_row['home_delivery'];
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test,
                                'test' => $test_id,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                    }
                } else {
                    $test_in_lab_list  = array();
                    $test_in_home_list = array();
                }
                
                 $query_count = $this->db->query("SELECT count(user_id) as total_view FROM profile_views_master WHERE listing_id='$labcenter_user_id' ");
                    $TTL_View = $query_count->row()->total_view;
        
        
                //Review Count
                //echo "SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'";
                //$Review_query = $this->db->query("SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'");
                $Review_query = $this->db->query("SELECT labcenter_review.id FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$id'");
                $Review_count = $Review_query->num_rows();
        
                $resultpost[] = array(
                    "id" => $id,
                    "is_thyrocare" => $is_thyrocare,
                    "lab_user_id" => $labcenter_user_id,
                    "name" => $lab_name,
                    "listing_type" => '10',
                    "features" => $features_array,
                    "home_delivery" => $home_delivery,
                    "delivery_charges" => $delivery_charges,
                    "address1" => $address1,
                    "address2" => $address2,
                    "pincode" => $pincode,
                    "city" => $city,
                    "exotel_no" => '02233721563',
                    "state" => $state,
                    "contact_no" => $contact_no,
                    "whatsapp_no" => $whatsapp_no,
                    "email" => $email,
                    "rating" => $rating,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $TTL_View,
                    "reviews" => $Review_count,
                    "is_follow" => $is_follow,
                    "lat" => $lat,
                    "lng" => $lng,
                    'opening_day' => $final_Day,
                    "distance" => round($row['distance'],2),
                    "store_open" => $store_open,
                    "store_close" => $store_close,
                    "open_close_status" => $open_close_status,
                    "image" => $image1,
                    "user_discount" => $user_discount,
                    "package_list" => $package_list,
                    "test_list" => $test_list,
                    "branch_list" => $branch_list,
                    "home_test_done" => $test_in_home_list,
                    "lab_test_done" => $test_in_lab_list
                );
            }
        } else {
            $resultpost = array();
        }
        
        
        if($per_page == "" || $page_no == "" || $per_page == 0 || $page_no == 0){
             $page_no = 1;
            $per_page = $count;
        } 
        
        $last_page = ceil($dataTotalCount/$per_page);

        return $resultpost;
    }
    
    public function labcenter_home_list($lat, $lng, $user_id, $category_id, $hospital_type,$per_page,$page_no,$featured,$recommended,$certified,$mba)
    {
        $catWhere = "";        
        if($featured == 1){
            $featuredWhere = "AND `is_featured` = 1"; 
        } else {
            $featuredWhere = ""; 
        }
        if($recommended == 1){
            $recommendedWhere = "AND `recommended` = 1"; 
        } else {
            $recommendedWhere = ""; 
        }        
        $certifiedWhere = "";         
        $mbaWhere = ""; 

        if($per_page != "" && $page_no != ""){
            $offset = $per_page*($page_no - 1);
            $wherePagination  = "LIMIT $per_page OFFSET $offset";
        } else {
            $wherePagination = "";
            $page_no = 1;
        }
        
        if($hospital_type == ''){
            $radius = '5';
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  `is_active` = 1  $catWhere $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere ORDER BY distance $wherePagination", ($lat), ($lng), ($lat));
            
            $query  = $this->db->query($sql);
            $count  = $query->num_rows();
            $getDataCount    = sprintf("SELECT `id`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  `is_active` = 1  $catWhere $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere ORDER BY distance", ($lat), ($lng), ($lat));
            $queryCount  = $this->db->query($getDataCount)->result_array();
            $dataTotalCount = sizeof($queryCount);
            
        }else{
            $radius = '5000';
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE  `is_active` = 1 AND is_vendor = $hospital_type $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows(); 
            $dataTotalCount = $count;
        }
       
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
				$certification='';
                $vendor_id = $row['user_id'];
                $lab_name = $row['lab_name'];
                        $features = $row['features'];
                        $rating = '4.0';
                        $profile_views = '1548';
                        $reviews = '1000';
                        $image = $row['profile_pic'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;

                        $testInfo = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$vendor_id'")->row_array();
                        $certification=$testInfo['feature'];

                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $vendor_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $vendor_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $vendor_id)->get()->num_rows();
                       
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }

                $package_list = array();
                $package_query = $this->db->query("SELECT lm.id,lm.code,lm.name,lm.home_collection_charges,lm.price,lm.discounted_price,count(pd.id) as test_count FROM lab_package_master lm INNER JOIN lab_package_test_master pd on pd.package_id=lm.id  WHERE lm.user_id='$vendor_id' GROUP BY pd.package_id");
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $code = $package_row['code'];
                        $name = $package_row['name'];
                        $home_collection_charges = $package_row['home_collection_charges'];
                        $price = $package_row['price'];
                        $discounted_price = $package_row['discounted_price'];
                        if($discounted_price==0){
                            $discounted_price = $package_row['price'];
                        }
                        $test_count = $package_row['test_count'];
                        $package_list[] = array(
                            'id' => $package_id,
                            'code' => $code,
                            'name' => $name,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'test_count' => $test_count
                        );
                    }
                } else {
                    $package_list = array();
                }               

                $test_list = array();
                $test_query       = $this->db->query("SELECT lt.id,lt.name,ld.code,ld.report_availability,ld.home_collection_charges,ld.price,ld.discounted_price,ld.discount,ld.medicalwale_discount from lab_test_master_details ld INNER JOIN lab_test_master lt on lt.id=ld.test_id where ld.user_id='$vendor_id'");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $test_id        = $test_row['id'];
                        $name        = $test_row['name'];
                        $code           = $test_row['code'];
                        $report_availability        = $test_row['report_availability'];
                        $home_collection_charges          = $test_row['home_collection_charges'];
                        $price          = $test_row['price'];
                        $discounted_price = $test_row['discounted_price'];
                        if($discounted_price==0){
                            $discounted_price = $test_row['price'];
                        }
                        $discount  = $test_row['discount'];
                        $medicalwale_discount  = $test_row['medicalwale_discount'];
                        $test_list[] = array(
                            'test_id' => $test_id,
                            'name' => $name,
                            'code' => $code,
                            'report_availability' => $report_availability,
                            'home_collection_charges' => $home_collection_charges,
                            'price' => $price,
                            'discounted_price' => $discounted_price,
                            'discount' => $discount,
                            'medicalwale_discount' => $medicalwale_discount
                        );
                    }
                } else {
                    $test_list  = array();
                }
                
                $resultpost[] = array(
                    "id" => $vendor_id,
                    "name" => $lab_name,
                    "listing_type" => 10,
                    "certification" => $certification,
                    "rating" => $rating,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $profile_views,
                    "reviews" => $reviews,
                    "is_follow" => $is_follow,
                    "image" => $image,
                    "package_list"=> $package_list,
                    "test_list"=>$test_list
                );
				
				
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    public function labcenter_list($lat, $lng, $user_id, $category_id, $hospital_type,$per_page,$page_no,$featured,$recommended,$certified,$mba)
    {
        $is_thyrocare  = $dataTotalCount = 0;
        $todayDay = date("l");
        $recommendedWhere = $certifiedWhere = $mbaWhere = $featuredWhere = $catWhere = $store_open = "";
        $store_close = "";
        $open_close_status = "";
        if($category_id != ""){
            $catWhere = "AND FIND_IN_SET($category_id, lab_center.cat_id)";
        } else {
            $catWhere = "";
        }
        if($featured == 1){
            $featuredWhere = "AND lab_center.is_featured = 1"; 
        } else {
            $featuredWhere = ""; 
        }
        if($recommended == 1){
            $recommendedWhere = "AND lab_center.recommended = 1"; 
        } else {
            $recommendedWhere = ""; 
        }
        if($certified == 1){
            $certifiedWhere = "AND lab_center.certified = 1"; 
        } else {
            $certifiedWhere = ""; 
        }
        if($mba == 1){
            $mbaWhere = "AND lab_center.mba = 1"; 
        } else {
            $mbaWhere = ""; 
        }
        if($per_page != "" && $page_no != ""){
            $offset = $per_page*($page_no - 1);
            $wherePagination  = "LIMIT $per_page OFFSET $offset";
        } else {
            $wherePagination = "";
            $page_no = 1;
        }
        if($hospital_type == ''){
            $radius = '5000';
            $sql    = sprintf("SELECT lab_center.id,lab_center.user_id,lab_center.cat_id,lab_center.profile_pic, lab_center.lab_name,lab_center.address1,lab_center.address2, lab_center.pincode, lab_center.city, lab_center.state, lab_center.contact_no,lab_center.whatsapp_no, lab_center.email, lab_center.opening_hours, lab_center.latitude, lab_center.longitude, lab_center.reg_date,lab_center.features,lab_center.home_delivery,lab_center.user_discount, IFNULL(lab_center.delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lab_center.latitude) ) * cos( radians( lab_center.longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lab_center.latitude) ) ) ) AS distance FROM lab_center INNER JOIN lab_test_master_details on lab_test_master_details.user_id=lab_center.user_id WHERE lab_center.is_active = 1  $catWhere $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere and (lab_center.latitude<>'' and lab_center.longitude<>'') GROUP by lab_test_master_details.user_id ORDER BY distance $wherePagination", ($lat), ($lng), ($lat));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows();
            $getDataCount    = sprintf("SELECT lab_center.id, IFNULL(lab_center.delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lab_center.latitude) ) * cos( radians( lab_center.longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lab_center.latitude) ) ) ) AS distance FROM lab_center INNER JOIN lab_test_master_details on lab_test_master_details.user_id=lab_center.user_id WHERE  lab_center.is_active = 1 and (lab_center.latitude<>'' and lab_center.longitude<>'') $catWhere $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere GROUP by lab_test_master_details.user_id ORDER BY distance", ($lat), ($lng), ($lat));
            $queryCount  = $this->db->query($getDataCount)->result_array();
            $dataTotalCount = sizeof($queryCount);
        }else{
            $radius = '5000';
            $sql    = sprintf("SELECT lab_center.id,lab_center.user_id,lab_center.cat_id,lab_center.profile_pic, lab_center.lab_name,lab_center.address1,lab_center.address2, lab_center.pincode, lab_center.city, lab_center.state, lab_center.contact_no,lab_center.whatsapp_no, lab_center.email, lab_center.opening_hours, lab_center.latitude, lab_center.longitude, lab_center.reg_date,lab_center.features,lab_center.home_delivery,lab_center.user_discount, IFNULL(lab_center.delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lab_center.latitude) ) * cos( radians( lab_center.longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lab_center.latitude) ) ) ) AS distance FROM lab_center INNER JOIN lab_test_master_details on lab_test_master_details.user_id=lab_center.user_id WHERE lab_center.is_active = 1 AND lab_center.is_vendor = $hospital_type $featuredWhere $recommendedWhere $certifiedWhere $mbaWhere and (lab_center.latitude<>'' and lab_center.longitude<>'') GROUP by lab_test_master_details.user_id  HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows(); 
            $dataTotalCount = $count;
        }
       
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                = $row['id'];
                $labcenter_user_id = $row['user_id'];
                
                
                $lab_name          = $row['lab_name'];
                $features          = $row['features'];
                $home_delivery     = $row['home_delivery'];
                $delivery_charges  = $row['delivery_charges'];
                $address1          = $row['address1'];
                $address2          = $row['address2'];
                $pincode           = $row['pincode'];
                $city              = $row['city'];
                $state             = $row['state'];
                $contact_no        = $row['contact_no'];
                $whatsapp_no       = $row['whatsapp_no'];
                $email             = $row['email'];
                $opening_hours     = $row['opening_hours'];
                $lat               = $row['latitude'];
                $lng               = $row['longitude'];
                $listing_type      = '10';
                $rating            = '4.0';
                $profile_views     = '';
                $reviews           = '1000';
                $user_discount     = $row['user_discount'];
                $image             = $row['profile_pic'];
                
                
                // if lab is thyrocare then call thyrocare APIs 
                if($labcenter_user_id == 4684){
                    $is_thyrocare = 1;
                }else {
                    $is_thyrocare = 0;
                }
                
               // $image            = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                $features_array    = array();
                $feature_query     = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                foreach ($feature_query->result_array() as $get_list) {
                    $feature          = $get_list['feature'];
                    $features_array[] = array(
                        "name" => $feature
                    );
                }
                $certification='';
                $feature = $this->db->query("SELECT GROUP_CONCAT(feature) as feature FROM `lab_features` INNER JOIN lab_center on FIND_IN_SET(lab_features.id, lab_center.features) where lab_center.user_id='$labcenter_user_id'")->row_array();
                $certification = $feature['feature'];
                $final_Day      = array();
                $day_array_list = explode('|', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time       = array();
                                $open_close = array();
                                for ($l = 0; $l < count($time_list1); $l++) {
                                    $tl1 = $time_list1[$l];
                                    $tl2 = $time_list2[$l];
                                    $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                    $time[]            = str_replace('close-close', 'close', $time_check);
                                    $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                    $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                    $current_time      = date('H.i');
                                    if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                        $ocs = $open_close[] = 'open';
                                    } else {
                                        $ocs = $open_close[] = 'close';
                                    }
                                }
                            }
                        }
                        $final_Day[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                        
                        if($todayDay == $day_list[0]){
                            $store_open = $tl1;
                            $store_close = $tl2;
                            $open_close_status = $ocs;
                        }
                        
                    }
                } else {
                    $final_Day[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }
                $current_day = "";
                $followers   = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                $following   = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                $is_follow   = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
               
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }

                
                 $query_count = $this->db->query("SELECT count(user_id) as total_view FROM profile_views_master WHERE listing_id='$labcenter_user_id' ");
                    $TTL_View = $query_count->row()->total_view;
        
        
                //Review Count
                //echo "SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'";
                //$Review_query = $this->db->query("SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'");
                $Review_query = $this->db->query("SELECT labcenter_review.id FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$id'");
                $Review_count = $Review_query->num_rows();
        	$km=$row['distance'];
                $meter = '0';
                $distance = $km * 1000;
                $store_distance = round($distance, 2);
                if ($distance > 999) {
                    $distance = $distance / 1000;
                    $meter = round($distance, 2) . ' km';
                } else {
                    $meter = round($distance) . ' meters away';
                }
                $resultpost[] = array(
                    "id" => $id,
                    "is_thyrocare" => $is_thyrocare,
                    "lab_user_id" => $labcenter_user_id,
                    "name" => $lab_name,
                    "listing_type" => '10',
                    "features" => $features_array,
                    'certification' => $certification,
                    "home_delivery" => $home_delivery,
                    "delivery_charges" => $delivery_charges,
                    "address1" => $address1,
                    "address2" => $address2,
                    "pincode" => $pincode,
                    "city" => $city,
                    "exotel_no" => '02233721563',
                    "state" => $state,
                    "contact_no" => $contact_no,
                    "whatsapp_no" => $whatsapp_no,
                    "email" => $email,
                    "rating" => $rating,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $TTL_View,
                    "reviews" => $Review_count,
                    "is_follow" => $is_follow,
                    "lat" => $lat,
                    "lng" => $lng,
                    'opening_day' => $final_Day,
                    "distance" => $meter,
                    "store_open" => $store_open,
                    "store_close" => $store_close,
                    "open_close_status" => $open_close_status,
                    "image" => $image1,
                    "user_discount" => $user_discount
                );
            }
        } else {
            $resultpost = array();
        }
        
        
        if($per_page == "" || $page_no == "" || $per_page == 0 || $page_no == 0){
             $page_no = 1;
            $per_page = $count;
        } 
        
        $last_page = ceil($dataTotalCount/$per_page);
        
        $resData['total_count'] = $dataTotalCount;
        $resData['page_no'] = $page_no;
        $resData['current_page_count'] = $per_page;
        $resData['last_page'] = $last_page;
        $resData['center_list'] = $resultpost;
        return $resData;
    }
    
        public function get_disounted_rate($Price,$discount_type,$discount){
        $discounted_price = 0;
        if($discount_type == 'rupee'){
            $discounted_price = $Price - $discount;
        } else {
            $totalDiscount = ( $Price * ($discount / 100));
            $discounted_price = $Price - $totalDiscount;
        }
        
        if($discounted_price < 0){
            $discounted_price = 0;
        }
        return ceil($discounted_price);
    }
    
    public function labcenter_list2($lat, $lng, $user_id, $category_id, $hospital_type)
    {
        if($hospital_type == ''){
            $radius = '5';
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1 AND FIND_IN_SET($category_id, cat_id) HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows();
        }else{
            $radius = '5000';
            $sql    = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_name`,`address1`,`address2`, `pincode`, `city`, `state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`,`user_discount`, IFNULL(delivery_charges,'') AS delivery_charges, ( 6371 * acos( cos( radians('%s') ) * cos( radians( latitude) ) * cos( radians( longitude) - radians('%s') ) + sin( radians('%s') ) * sin( radians( latitude) ) ) ) AS distance FROM lab_center  WHERE `is_active` = 1 AND is_vendor = $hospital_type HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($lat), ($lng), ($lat), ($radius));
            $query  = $this->db->query($sql);
            $count  = $query->num_rows(); 
        }
       
        
        
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id                = $row['id'];
                $labcenter_user_id = $row['user_id'];
                $lab_name          = $row['lab_name'];
                $features          = $row['features'];
                $home_delivery     = $row['home_delivery'];
                $delivery_charges  = $row['delivery_charges'];
                $address1          = $row['address1'];
                $address2          = $row['address2'];
                $pincode           = $row['pincode'];
                $city              = $row['city'];
                $state             = $row['state'];
                $contact_no        = $row['contact_no'];
                $whatsapp_no       = $row['whatsapp_no'];
                $email             = $row['email'];
                $opening_hours     = $row['opening_hours'];
                $lat               = $row['latitude'];
                $lng               = $row['longitude'];
                $listing_type      = '10';
                //$rating            = '4.0';
                $profile_views     = '';
                $reviews           = '1000';
                $user_discount     = $row['user_discount'];
                $image             = $row['profile_pic'];
               // $image             = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
                $features_array    = array();
                $feature_query     = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                foreach ($feature_query->result_array() as $get_list) {
                    $feature          = $get_list['feature'];
                    $features_array[] = array(
                        "name" => $feature
                    );
                }
                $final_Day      = array();
                $day_array_list = explode('|', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time       = array();
                                $open_close = array();
                                for ($l = 0; $l < count($time_list1); $l++) {
                                    $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                    $time[]            = str_replace('close-close', 'close', $time_check);
                                    $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                    $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                    $current_time      = date('H.i');
                                    if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                        $open_close[] = 'open';
                                    } else {
                                        $open_close[] = 'close';
                                    }
                                }
                            }
                        }
                        $final_Day[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                    }
                } else {
                    $final_Day[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }
                $current_day = "";
                $followers   = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                $following   = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                $is_follow   = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
               
                if ($is_follow > 0) {
                    $is_follow = 'Yes';
                } else {
                    $is_follow = 'No';
                }
                $package_list  = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id ='' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id      = $package_row['id'];
                        $package_name    = $package_row['lab_name'];
                        $package_details = $package_row['lab_details'];
                        $price           = $package_row['Price'];
                        $image           = $package_row['image'];
                        $home_delivery   = $package_row['home_delivery'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $package_list[]  = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price,
                            'home_delivery' => $home_delivery
                        );
                    }
                } else {
                    $package_list = array();
                }
                $branch_list  = array();
                $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        $branch_details                         = $branch_row['fnac_pick_up'];
                        /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        $branch_reg_date                        = $branch_row['reg_date'];
                        $branch_lab_branch_name                 = $branch_row['lab_branch_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager                   = $branch_row['store_manager'];
                        $branch_latitude                        = $branch_row['latitude'];
                        $branch_map_location                    = $branch_row['map_location'];
                        $branch_longitude                       = $branch_row['longitude'];
                        $branch_address1                        = $branch_row['address1'];
                        $branch_address2                        = $branch_row['address2'];
                        $branch_pincode                         = $branch_row['pincode'];
                        $branch_features                        = $branch_row['features'];
                        $branch_city                            = $branch_row['city'];
                        $branch_state                           = $branch_row['state'];
                        $branch_contact_no                      = $branch_row['contact_no'];
                        $branch_whatsapp_no                     = $branch_row['whatsapp_no'];
                        $branch_email                           = $branch_row['email'];
                        $branch_profile_pic                     = $branch_row['profile_pic'];
                        $branch_about_us                        = $branch_row['about_us'];
                        $branch_reach_area                      = $branch_row['reach_area'];
                        $branch_distance                        = $branch_row['distance'];
                        $branch_store_since                     = $branch_row['store_since'];
                        $branch_opening_hours                   = $branch_row['opening_hours'];
                        $branch_home_deliverys                  = $branch_row['home_delivery'];
                        $branch_branch_profile                  = $branch_row['branch_profile'];
                        $branch_delivery_charges                = $branch_row['delivery_charges'];
                        $branch_payment_type                    = $branch_row['payment_type'];
                        $branch_discount                        = $branch_row['discount'];
                        $branch_services                        = $branch_row['services'];
                        $branch_home_visit                      = $branch_row['home_visit'];
                        $branch_features_array                  = array();
                        $feature_query                          = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
                            $feature                 = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
                        $branch_final_Day      = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time       = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[]            = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time   = date("H.i", strtotime($time_list2[$l]));
                                            $current_time      = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day         = "";
                        $package_branch_list = array();
                        $package_query       = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id='$branch_id' order by id asc");
                        //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                        $package_count       = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_id            = $package_row['id'];
                                $package_name          = $package_row['lab_name'];
                                $package_details       = $package_row['lab_details'];
                                $price                 = $package_row['Price'];
                                $image                 = $package_row['image'];
                                $home_delivery         = $package_row['home_delivery'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price,
                                    'home_delivery' => $home_delivery
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' => $branch_store_manager,
                            'branch_latitude' => $branch_latitude,
                            'branch_map_location' => $branch_map_location,
                            'branch_longitude' => $branch_longitude,
                            'branch_address1' => $branch_address1,
                            'branch_address2' => $branch_address2,
                            'branch_pincode' => $branch_pincode,
                            'branch_features' => $branch_features_array,
                            'branch_city' => $branch_city,
                            'branch_state' => $branch_state,
                            'branch_contact_no' => $branch_contact_no,
                            'branch_whatsapp_no' => $branch_whatsapp_no,
                            'branch_email' => $branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic' => $branch_profile_pic,
                            'branch_about_us' => $branch_about_us,
                            'branch_reach_area' => $branch_reach_area,
                            'branch_distance' => $branch_distance,
                            'branch_store_since' => $branch_store_since,
                            'branch_package' => $package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                $test_in_lab_list = array();
                $test_in_home_list = array();
                $test_query       = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' order by id asc");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $user_id        = $test_row['user_id'];
                        $test           = $test_row['test'];
                        $test_id        = $test_row['test_id'];
                        $price          = $test_row['price'];
                        $offer          = $test_row['offer'];
                        $executive_rate = $test_row['executive_rate'];
                        $home_delivery  = $test_row['home_delivery'];
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test,
                                'test' => $test_id,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                    }
                } else {
                    $test_in_lab_list  = array();
                    $test_in_home_list = array();
                }
                
                 $query_count = $this->db->query("SELECT count(user_id) as total_view FROM profile_views_master WHERE listing_id='$labcenter_user_id' ");
                    $TTL_View = $query_count->row()->total_view;
        
        
                //Review Count
                //echo "SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'";
                //$Review_query = $this->db->query("SELECT * FROM `labcenter_review` WHERE labcenter_id='$id'");
                $Review_query = $this->db->query("SELECT labcenter_review.id FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$id'");
                $Review_count = $Review_query->num_rows();
                
                $rating_query = $this->db->query("SELECT sum(labcenter_review.rating) as rating, count(labcenter_review.rating) as total_view FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$id'");
                $rating_coun = $rating_query->num_rows();
                if($rating_coun > 0)
                {
                $rating_counting = $rating_query->row()->total_view;
                $rating_view = $rating_query->row()->rating;
                $rating_count=$rating_view/$rating_counting;
                }
                else
                {
                   $rating_count=0; 
                }
                $resultpost[] = array(
                    "id" => $id,
                    "lab_user_id" => $labcenter_user_id,
                    "name" => $lab_name,
                    "listing_type" => '10',
                    "features" => $features_array,
                    "home_delivery" => $home_delivery,
                    "delivery_charges" => $delivery_charges,
                    "address1" => $address1,
                    "address2" => $address2,
                    "pincode" => $pincode,
                    "city" => $city,
                    "exotel_no" => '02233721563',
                    "state" => $state,
                    "contact_no" => $contact_no,
                    "whatsapp_no" => $whatsapp_no,
                    "email" => $email,
                    "rating" => $rating_count,
                    "followers" => $followers,
                    "following" => $following,
                    "profile_views" => $TTL_View,
                    "reviews" => $Review_count,
                    "is_follow" => $is_follow,
                    "lat" => $lat,
                    "lng" => $lng,
                    'opening_day' => $final_Day,
                    "image" => $image1,
                    "user_discount" => $user_discount,
                    "package_list" => $package_list,
                    "branch_list" => $branch_list,
                    "home_test_done" => $test_in_home_list,
                    "lab_test_done" => $test_in_lab_list
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    //added by zak for lab details 
    public function labcenter_details($user_id, $listing_id)
    {
        
                // Labs
               $sql = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `discount`,`lab_name`,`address1`,`address2`, `pincode`, `city`, `user_discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center where `is_active` = 1 AND user_id='$listing_id'");
               // $sql = sprintf("SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_branch_name`,`address1`,`address2`, `pincode`, `city`, `discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center_branch where user_id='$listing_id'");
                //echo "SELECT `id`,`user_id`,`cat_id`,`profile_pic`, `lab_branch_name`,`address1`,`address2`, `pincode`, `city`, `discount`,`state`, `contact_no`,`whatsapp_no`, `email`, `opening_hours`, `latitude`, `longitude`, `reg_date`,`features`,`home_delivery`, IFNULL(delivery_charges,'') AS delivery_charges FROM lab_center_branch where user_id='$listing_id'";
                
                $query = $this->db->query($sql);
                $count = $query->num_rows();
          
                if ($count > 0) {
                    foreach ($query->result_array() as $row) {
                        $id = $row['id'];
                        $labcenter_user_id = $row['user_id'];
                        $lab_name = $row['lab_name'];
                        $features = $row['features'];
                        $home_delivery = $row['home_delivery'];
                        $delivery_charges = $row['delivery_charges'];
                        $address1 = $row['address1'];
                        $address2 = $row['address2'];
                        $pincode = $row['pincode'];
                        $city = $row['city'];
                        $state = $row['state'];
                        $contact_no = $row['contact_no'];
                        $whatsapp_no = $row['whatsapp_no'];
                        $email = $row['email'];
                        $opening_hours = $row['opening_hours'];
                        $lat = $row['latitude'];
                        $lng = $row['longitude'];
                        $rating = '4.0';
                        $profile_views = '1548';
                        $reviews = '1000';
                        $user_discount = $row['discount'];
                        $image = $row['profile_pic'];
                        $image = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;

                        $features_array = array();
                        if($features != ''){
                            $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $features . "')");
                            foreach ($feature_query->result_array() as $get_list) {
    
                                $feature = $get_list['feature'];
                                $features_array[] = array(
                                    "name" => $feature
                                );
                            }
                        }

                        $final_Day = array();
                        $day_array_list = explode('|', $opening_hours);
                        if (count($day_array_list) > 1) {
                            for ($i = 0; $i < count($day_array_list); $i++) {
                                $day_list = explode('>', $day_array_list[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[] = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                            $current_time = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day = "";



                        $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $labcenter_user_id)->get()->num_rows();
                        $following = $this->db->select('id')->from('follow_user')->where('user_id', $labcenter_user_id)->get()->num_rows();
                        $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $labcenter_user_id)->get()->num_rows();
                       
                        if ($is_follow > 0) {
                            $is_follow = 'Yes';
                        } else {
                            $is_follow = 'No';
                        }

 //********************************************added by jakir on 3 aug 2018 for lab changes*************************************************** 
                  $package_list = array();
                $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id ='' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $package_count = $package_query->num_rows();
                if ($package_count > 0) {
                    foreach ($package_query->result_array() as $package_row) {
                        $package_id = $package_row['id'];
                        $package_name = $package_row['lab_name'];
                        $package_details = $package_row['lab_details'];
                        $price = $package_row['Price'];
                        $image = $package_row['image'];
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $package_list[] = array(
                            'package_id' => $package_id,
                            'package_name' => $package_name,
                            'package_details' => $package_details,
                            'price' => $price
                        );
                    }
                } else {
                    $package_list = array();
                }
                
                $branch_list = array();
                $branch_id ="";
                $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$labcenter_user_id' order by id asc");
                //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                $branch_count = $branch_query->num_rows();
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        
                        $branch_details                         = $branch_row['fnac_pick_up'];
                       /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        
                        $branch_reg_date                = $branch_row['reg_date'];
                        $branch_lab_branch_name         = $branch_row['lab_branch_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager           = $branch_row['store_manager'];
                        $branch_latitude                = $branch_row['latitude'];
                        $branch_map_location            = $branch_row['map_location'];
                        $branch_longitude               = $branch_row['longitude'];
                        $branch_address1                = $branch_row['address1'];
                        $branch_address2                = $branch_row['address2'];
                        $branch_pincode                 = $branch_row['pincode'];
                        $branch_features                = $branch_row['features'];
                        $branch_city                    = $branch_row['city'];
                        $branch_state                   = $branch_row['state'];
                        $branch_contact_no              = $branch_row['contact_no'];
                        $branch_whatsapp_no             = $branch_row['whatsapp_no'];
                        $branch_email                   = $branch_row['email'];
                        $branch_profile_pic             = $branch_row['profile_pic'];
                        $branch_about_us                = $branch_row['about_us'];
                        $branch_reach_area              = $branch_row['reach_area'];
                        $branch_distance                = $branch_row['distance'];
                        $branch_store_since             = $branch_row['store_since'];
                        $branch_opening_hours           = $branch_row['opening_hours'];
                        $branch_home_deliverys          = $branch_row['home_delivery'];
                        $branch_branch_profile          = $branch_row['branch_profile'];
                        $branch_delivery_charges        = $branch_row['delivery_charges'];
                        $branch_payment_type            = $branch_row['payment_type'];
                        $branch_discount                = $branch_row['discount'];
                        $branch_services                = $branch_row['services'];
                        $branch_home_visit              = $branch_row['home_visit'];
                        
                        
                        $branch_features_array = array();

                        $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
        
                            $feature = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
        
                        $branch_final_Day = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[] = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                            $current_time = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day = "";
                        
                        
                        
                        $package_branch_list = array();
                        $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' and branch_id='$branch_id' order by id asc");
                        //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                        $package_count = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_id = $package_row['id'];
                                $package_name = $package_row['lab_name'];
                                $package_details = $package_row['lab_details'];
                                $price = $package_row['Price'];
                                $image = $package_row['image'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        
                        
                        
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' =>$branch_store_manager, 
                            'branch_latitude'=>$branch_latitude,
                            'branch_map_location'=>$branch_map_location,
                            'branch_longitude'=>$branch_longitude,
                            'branch_address1'=>$branch_address1,
                            'branch_address2'=>$branch_address2,
                            'branch_pincode'=>$branch_pincode,
                            'branch_features'=>$branch_features_array,
                            'branch_city'=>$branch_city,
                            'branch_state'=>$branch_state,
                            'branch_contact_no'=>$branch_contact_no,
                            'branch_whatsapp_no'=>$branch_whatsapp_no,
                            'branch_email'=>$branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic'=>$branch_profile_pic,
                            'branch_about_us'=>$branch_about_us,
                            'branch_reach_area'=>$branch_reach_area,
                            'branch_distance'=>$branch_distance,
                            'branch_store_since'=>$branch_store_since,
                            'branch_package'=>$package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                
                
                ///Lab test added by ghanshyam parihar starts
                
                // $test_branch_list = array();
                // $test_query = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' or branch_id='$branch_id' order by id asc");
                // //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                // $test_count = $test_query->num_rows();
                // if ($test_count > 0) {
                //     foreach ($test_query->result_array() as $test_row) {
                //         $test_id = $test_row['id'];
                //         $test_name = $test_row['test'];
                //         $price = $test_row['price'];
                //         $discount = $test_row['discount'];
                //         $offer = $test_row['offer'];
                //         $executive_rate = $test_row['executive_rate'];
                //         //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                //         $test_branch_list[] = array(
                //             'test_id' => $test_id,
                //             'test_name' => $test_name,
                //             'price' => $price,
                //             'offer' => $offer,
                //             'executive_rate' => $executive_rate,
                //             'discount' => $discount
                //         );
                //     }
                // } else {
                //     $test_branch_list = array();
                // }
                
                $test_in_lab_list = array();
                 $test_in_home_list = array();
                $test_query       = $this->db->query("SELECT * FROM `lab_test_details` WHERE user_id='$labcenter_user_id' order by id asc");
                $test_count       = $test_query->num_rows();
                if ($test_count > 0) {
                    foreach ($test_query->result_array() as $test_row) {
                        $user_id        = $test_row['user_id'];
                        $test           = $test_row['test'];
                        $test_id        = $test_row['test_id'];
                        $price          = $test_row['price'];
                        $offer          = $test_row['offer'];
                        $executive_rate = $test_row['executive_rate'];
                        $home_delivery  = $test_row['home_delivery'];
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test,
                                'test' => $test_id,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                    }
                } else {
                    $test_in_lab_list  = array();
                    $test_in_home_list = array();
                }
                
                ///Lab test added by ghanshyam parihar ends

//********************************************************************************End by Jakir *******************************************
                        $resultpost[] = array(
                            "id" => $id,
                            "lab_user_id" => $labcenter_user_id,
                            "name" => $lab_name,
                            "features" => $features_array,
                            "home_delivery" => $home_delivery,
                            "delivery_charges" => $delivery_charges,
                            "address1" => $address1,
                            "address2" => $address2,
                            "pincode" => $pincode,
                            "city" => $city,
                            "state" => $state,
                            "contact_no" => $contact_no,
                            "whatsapp_no" => $whatsapp_no,
                            "exotel_no" => '02233721563',
                            "email" => $email,
                            "rating" => $rating,
                            "followers" => $followers,
                            "following" => $following,
                            "profile_views" => $profile_views,
                            "reviews" => $reviews,
                            "is_follow" => $is_follow,
                            "lat" => $lat,
                            "lng" => $lng,
                            'opening_day' => $final_Day,
                            "image" => $image,
                     "user_discount" => $user_discount,
                    "package_list"=> $package_list,
                    "branch_list"=>$branch_list,
                   // "test_list"=>$test_branch_list
                    "home_test_done" => $test_in_home_list,
                    "lab_test_done" => $test_in_lab_list
                        );
                    }
                } else {
                    $resultpost = array();
                }
            return $resultpost;
    }
    
    
    //added by zak for lab branch center 
    public function labcenter_branches_details($user_id, $listing_id,$branch_id)
    {
           $branch_list = array();
                $branch_query = $this->db->query("SELECT * FROM `lab_center_branch` WHERE user_id='$listing_id' AND id='$branch_id' order by id asc");
             //   echo "SELECT * FROM `lab_center_branch` WHERE user_id='$listing_id' AND id='$branch_id' order by id asc";
                $branch_count = $branch_query->num_rows();
            //    echo $branch_count;
                if ($branch_count > 0) {
                    foreach ($branch_query->result_array() as $branch_row) {
                        $branch_id                              = $branch_row['id'];
                        $branch_listing_id                      = $branch_row['user_id'];
                        $branch_path_pick_up                    = $branch_row['path_pick_up'];
                        $branch_path_sample_pickup_price_option = $branch_row['path_sample_pickup_price_option'];
                        $branch_path_sample_pickup_price        = $branch_row['path_sample_pickup_price'];
                        $branch_path_delivery_charge            = $branch_row['path_delivery_charge'];
                        $branch_path_delivery_price_options     = $branch_row['path_delivery_price_option'];
                        $branch_path_sample_delivery_price      = $branch_row['path_sample_delivery_price'];
                        
                        $branch_diag_delivery_charge            = $branch_row['diag_delivery_charge'];
                        $branch_diag_delivery_price_option      = $branch_row['diag_delivery_price_option'];
                        $branch_diag_sample_delivery_price      = $branch_row['diag_sample_delivery_price'];
                        
                        $branch_details                         = $branch_row['fnac_pick_up'];
                       /* $branch_details = $branch_row['fnac_sample_pickup_price_option '];
                        $branch_details = $branch_row['fnac_sample_pickup_price '];
                        $branch_details = $branch_row['fnac_delivery_charge'];
                        $branch_details = $branch_row['fnac_sample_delivery_price_option'];
                        $branch_details = $branch_row['fnac_sample_delivery_price '];*/
                        
                        $branch_reg_date                = $branch_row['reg_date'];
                        $branch_lab_branch_name         = $branch_row['lab_branch_name'];
                        /*$branch_packages = $branch_row['packages'];*/
                        $branch_store_manager           = $branch_row['store_manager'];
                        $branch_latitude                = $branch_row['latitude'];
                        $branch_map_location            = $branch_row['map_location'];
                        $branch_longitude               = $branch_row['longitude'];
                        $branch_address1                = $branch_row['address1'];
                        $branch_address2                = $branch_row['address2'];
                        $branch_pincode                 = $branch_row['pincode'];
                        $branch_features                = $branch_row['features'];
                        $branch_city                    = $branch_row['city'];
                        $branch_state                   = $branch_row['state'];
                        $branch_contact_no              = $branch_row['contact_no'];
                        $branch_whatsapp_no             = $branch_row['whatsapp_no'];
                        $branch_email                   = $branch_row['email'];
                        $branch_profile_pic             = $branch_row['profile_pic'];
                        $branch_about_us                = $branch_row['about_us'];
                        $branch_reach_area              = $branch_row['reach_area'];
                        $branch_distance                = $branch_row['distance'];
                        $branch_store_since             = $branch_row['store_since'];
                        $branch_opening_hours           = $branch_row['opening_hours'];
                        $branch_home_deliverys          = $branch_row['home_delivery'];
                        $branch_branch_profile          = $branch_row['branch_profile'];
                        $branch_delivery_charges        = $branch_row['delivery_charges'];
                        $branch_payment_type            = $branch_row['payment_type'];
                        $branch_discount                = $branch_row['discount'];
                        $branch_services                = $branch_row['services'];
                        $branch_home_visit              = $branch_row['home_visit'];
                        
                        
                        $branch_features_array = array();

                        $feature_query = $this->db->query("SELECT feature FROM `lab_features` WHERE FIND_IN_SET(id,'" . $branch_features . "')");
                        foreach ($feature_query->result_array() as $get_list) {
        
                            $feature = $get_list['feature'];
                            $branch_features_array[] = array(
                                "name" => $feature
                            );
                        }
        
                        $branch_final_Day = array();
                        $day_array_list_branch = explode('|', $branch_opening_hours);
                        if (count($day_array_list_branch) > 1) {
                            for ($i = 0; $i < count($day_array_list_branch); $i++) {
                                $day_list = explode('>', $day_array_list_branch[$i]);
                                for ($j = 0; $j < count($day_list); $j++) {
                                    $day_time_list = explode('-', $day_list[$j]);
                                    for ($k = 1; $k < count($day_time_list); $k++) {
                                        $time_list1 = explode(',', $day_time_list[0]);
                                        $time_list2 = explode(',', $day_time_list[1]);
                                        $time = array();
                                        $open_close = array();
                                        for ($l = 0; $l < count($time_list1); $l++) {
                                            $time_check = $time_list1[$l] . '-' . $time_list2[$l];
                                            $time[] = str_replace('close-close', 'close', $time_check);
                                            $system_start_time = date("H.i", strtotime($time_list1[$l]));
                                            $system_end_time = date("H.i", strtotime($time_list2[$l]));
                                            $current_time = date('H.i');
                                            if ($current_time > $system_start_time && $current_time < $system_end_time) {
                                                $open_close[] = 'open';
                                            } else {
                                                $open_close[] = 'close';
                                            }
                                        }
                                    }
                                }
                                $branch_final_Day[] = array(
                                    'day' => $day_list[0],
                                    'time' => $time,
                                    'status' => $open_close
                                );
                            }
                        } else {
                            $branch_final_Day[] = array(
                                'day' => 'close',
                                'time' => array(),
                                'status' => array()
                            );
                        }
                        $current_day = "";
                        
                        
                        
                        $package_branch_list = array();
                        $package_query = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='$listing_id' and branch_id='$branch_id' order by id asc");
                        //echo "SELECT * FROM `lab_packages` WHERE user_id='$labcenter_user_id' order by id asc";
                        $package_count = $package_query->num_rows();
                        if ($package_count > 0) {
                            foreach ($package_query->result_array() as $package_row) {
                                $package_id = $package_row['id'];
                                $package_name = $package_row['lab_name'];
                                $package_details = $package_row['lab_details'];
                                $price = $package_row['Price'];
                                $image = $package_row['image'];
                                //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                                $package_branch_list[] = array(
                                    'package_id' => $package_id,
                                    'package_name' => $package_name,
                                    'package_details' => $package_details,
                                    'price' => $price
                                );
                            }
                        } else {
                            $package_branch_list = array();
                        }
                        
                        
                        
                        //$image = 'https://d2c8oti4is0ms3.cloudfront.net/images/fitness_center/' . $image;
                        $branch_list[] = array(
                            'branch_id' => $branch_id,
                            'branch_path_pick_up' => $branch_path_pick_up,
                            'branch_path_sample_pickup_price_option' => $branch_path_sample_pickup_price_option,
                            'branch_path_sample_pickup_price' => $branch_path_sample_pickup_price,
                            'branch_path_delivery_charge' => $branch_path_delivery_charge,
                            'branch_path_delivery_price_options' => $branch_path_delivery_price_options,
                            'branch_path_sample_delivery_price' => $branch_path_sample_delivery_price,
                            'branch_diag_delivery_charge' => $branch_diag_delivery_charge,
                            'branch_diag_delivery_price_option' => $branch_diag_delivery_price_option,
                            'branch_diag_sample_delivery_price' => $branch_diag_sample_delivery_price,
                            'branch_details' => $branch_details,
                            'branch_reg_date' => $branch_reg_date,
                            'branch_lab_branch_name' => $branch_lab_branch_name,
                            'branch_store_manager' =>$branch_store_manager, 
                            'branch_latitude'=>$branch_latitude,
                            'branch_map_location'=>$branch_map_location,
                            'branch_longitude'=>$branch_longitude,
                            'branch_address1'=>$branch_address1,
                            'branch_address2'=>$branch_address2,
                            'branch_pincode'=>$branch_pincode,
                            'branch_features'=>$branch_features_array,
                            'branch_city'=>$branch_city,
                            'branch_state'=>$branch_state,
                            'branch_contact_no'=>$branch_contact_no,
                            'branch_whatsapp_no'=>$branch_whatsapp_no,
                            'branch_email'=>$branch_email,
                            'branch_opening_day' => $branch_final_Day,
                            'branch_profile_pic'=>$branch_profile_pic,
                            'branch_about_us'=>$branch_about_us,
                            'branch_reach_area'=>$branch_reach_area,
                            'branch_distance'=>$branch_distance,
                            'branch_store_since'=>$branch_store_since,
                            'branch_package'=>$package_branch_list
                        );
                    }
                } else {
                    $branch_list = array();
                }
                return $branch_list;
    }
    //end 
    
    public function labcenter_packages($labcenter_id)
    {
        $query = $this->db->query("SELECT id,packages,user_id FROM `lab_center` WHERE `is_active` = 1 AND id='$labcenter_id'");
        $count = $query->num_rows();
        //print_r($query->result_array());
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $packages           = $row['user_id'];
                //$labcenter_packages = $this->db->query("SELECT * FROM `lab_packages` WHERE FIND_IN_SET(lab_pack_name,'" . $packages . "')");
                $labcenter_packages = $this->db->query("SELECT * FROM `lab_packages` WHERE user_id='" . $packages . "'");
                foreach ($labcenter_packages->result_array() as $get_list) {
                    $package_id      = $get_list['id'];
                    $package_name    = $get_list['lab_pack_name'];
                    $package_details = $get_list['lab_pack_details'];
                    $price           = $get_list['price'];
                    $image           = $get_list['image'];
                    $image           = 'https://d2c8oti4is0ms3.cloudfront.net/images/labcenter_images/' . $image;
                    $resultpost[]    = array(
                        "package_id" => $package_id,
                        "package_name" => $package_name,
                        "package_details" => $package_details,
                        'price' => $price,
                        'image' => $image
                    );
                }
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function lab_test_search($keyword, $category_id, $lab_user_id)
    {
        $resultpost = array();
        $query      = $this->db->query("SELECT id,user_id,cat_id,test,price FROM lab_test_details WHERE test LIKE '%$keyword%' AND cat_id='$category_id' AND user_id='$lab_user_id' limit 15");
        $count      = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $test_id       = $row['id'];
                $lab_id        = $row['user_id'];
                $test_name     = $row['test'];
                $product_price = $row['price'];
                $resultpost[]  = array(
                    "lab_test_id" => $test_id,
                    "test_name" => $test_name,
                    "test_price" => $product_price
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function add_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'labcenter_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('labcenter_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
      public function edit_review($review_id,$user_id, $listing_id, $rating, $review, $service) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $review_array = array(
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
        );
           $this->db->where('id',$review_id);
           $this->db->where('user_id',$user_id);
           $this->db->update('labcenter_review',$review_array);
        //$this->db->insert('fitness_center_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    public function review_list($user_id, $listing_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }
        $review_count = $this->db->select('id')->from('labcenter_review')->where('labcenter_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT labcenter_review.id,labcenter_review.user_id,labcenter_review.labcenter_id,labcenter_review.rating,labcenter_review.review, labcenter_review.service,labcenter_review.date as review_date,users.id as user_id,users.name as firstname FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$listing_id' order by labcenter_review.id desc");
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $user_id       = $row['user_id'];
                $username = $row['firstname'];
                $rating   = $row['rating'];
                $review   = $row['review'];
                $review   = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '9') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service      = $row['service'];
                $review_date  = $row['review_date'];
                $review_date  = get_time_difference_php($review_date);
                $like_count   = $this->db->select('id')->from('labcenter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count   = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('labcenter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                $resultpost[] = array(
                    'id' => $id,
                    'user_id'=>$user_id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
     public function review_with_comment($user_id, $listing_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }
        $review_count = $this->db->select('id')->from('labcenter_review')->where('labcenter_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT labcenter_review.id,labcenter_review.user_id,labcenter_review.labcenter_id,labcenter_review.rating,labcenter_review.review, labcenter_review.service,labcenter_review.date as review_date,users.id as user_id,users.name as firstname FROM `labcenter_review` INNER JOIN `users` ON labcenter_review.user_id=users.id WHERE labcenter_review.labcenter_id='$listing_id' order by labcenter_review.id desc");
            foreach ($query->result_array() as $row) {
                $id       = $row['id'];
                $username = $row['firstname'];
                $rating   = $row['rating'];
                $review   = $row['review'];
                $review   = preg_replace('~[\r\n]+~', '', $review);
                if ($id > '9') {
                    $decrypt = $this->decrypt($review);
                    $encrypt = $this->encrypt($decrypt);
                    if ($encrypt == $review) {
                        $review = $decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($review)) === $review) {
                        $review = base64_decode($review);
                    }
                }
                $service      = $row['service'];
                $review_date  = $row['review_date'];
                $review_date  = get_time_difference_php($review_date);
                $like_count   = $this->db->select('id')->from('labcenter_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count   = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('labcenter_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                        $review_list_count = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $id)->get()->num_rows();
                         if ($review_list_count) {
                              $resultcomment = array();
                        $querycomment = $this->db->query("SELECT labcenter_review_comment.id,labcenter_review_comment.post_id,labcenter_review_comment.comment as comment,labcenter_review_comment.date,users.name,labcenter_review_comment.user_id as post_user_id FROM labcenter_review_comment INNER JOIN users on users.id=labcenter_review_comment.user_id WHERE labcenter_review_comment.post_id='$id' order by labcenter_review_comment.id asc");
                        
                        foreach ($querycomment->result_array() as $rowc) {
                            $comment_id      = $rowc['id'];
                            $post_id = $rowc['post_id'];
                            $comment = $rowc['comment'];
                            $comment = preg_replace('~[\r\n]+~', '', $comment);
                           
                                $usernamec     = $rowc['name'];
                                $date         = $rowc['date'];
                                $post_user_id = $rowc['post_user_id'];
                                $like_countc   = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $comment_id)->get()->num_rows();
                                $like_yes_noc  = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $comment_id)->where('user_id', $user_id)->get()->num_rows();
                                $comment_date = get_time_difference_php($date);
                                $img_count    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                                if ($img_count > 0) {
                                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                                    $img_file      = $profile_query->source;
                                    $userimagec     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                                } else {
                                    $userimagec = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                                }
                                $date         = get_time_difference_php($date);
                                $resultcomment[] = array(
                                    'id' => $comment_id,
                                    'username' => $usernamec,
                                    'userimage' => $userimagec,
                                    'like_count' => $like_countc,
                                    'like_yes_no' => $like_yes_noc,
                                    'post_id' => $post_id,
                                    'comment' => $comment,
                                    'comment_date' => $comment_date
                                );
                            }
                        } else {
                            $resultcomment = array();
                        }
                
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count,
                       'comments'=>$resultcomment
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    public function review_like($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from labcenter_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `labcenter_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from labcenter_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $labcenter_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('labcenter_review_likes', $labcenter_review_likes);
            $like_query = $this->db->query("SELECT id from labcenter_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    public function review_comment($user_id, $post_id, $comment)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at               = date('Y-m-d H:i:s');
        $labcenter_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('labcenter_review_comment', $labcenter_review_comment);
        $labcenter_review_comment_query = $this->db->query("SELECT id from labcenter_review_comment WHERE post_id='$post_id'");
        $total_comment                  = $labcenter_review_comment_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }
    public function review_comment_like($user_id, $comment_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from labcenter_review_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `labcenter_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id from labcenter_review_comment_like where comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $labcenter_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('labcenter_review_comment_like', $labcenter_review_comment_like);
            $comment_query      = $this->db->query("SELECT id from labcenter_review_comment_like where comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }
    public function review_comment_list($user_id, $post_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . ' yrs ago';
            } elseif (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . ' yr ago';
            } elseif (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . ' months ago';
            } elseif (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . ' month ago';
            } elseif (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . ' days ago';
            } elseif (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . ' day ago';
            } elseif (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . ' hrs ago';
            } elseif (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . ' hr ago';
            } elseif (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . ' sec ago';
            } else {
                return 'few seconds ago';
            }
        }
        $review_list_count = $this->db->select('id')->from('labcenter_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT labcenter_review_comment.id,labcenter_review_comment.post_id,labcenter_review_comment.comment as comment,labcenter_review_comment.date,users.name,labcenter_review_comment.user_id as post_user_id FROM labcenter_review_comment INNER JOIN users on users.id=labcenter_review_comment.user_id WHERE labcenter_review_comment.post_id='$post_id' order by labcenter_review_comment.id asc");
            foreach ($query->result_array() as $row) {
                $id      = $row['id'];
                $post_id = $row['post_id'];
                $comment = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if ($id > '3') {
                    $comment_decrypt = $this->decrypt($comment);
                    $comment_encrypt = $this->encrypt($comment_decrypt);
                    if ($comment_encrypt == $comment) {
                        $comment = $comment_decrypt;
                    }
                } else {
                    if (base64_encode(base64_decode($comment)) === $comment) {
                        $comment = base64_decode($comment);
                    }
                }
                $username     = $row['name'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];
                $like_count   = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('labcenter_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);
                $img_count    = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file      = $profile_query->source;
                    $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
                }
                $date         = get_time_difference_php($date);
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    public function lab_booking($user_id, $listing_id, $address_line1, $address_line2, $package_id, $user_name, $mobile, $email, $gender, $branch_id, $branch_name, $vendor_id, $status, $payment_mode, $trail_booking_date, $trail_booking_time, $joining_date, $booking_location, $booking_address, $booking_mobile,$test_ids, $patient_id, $at_home, $city, $state, $pincode, $address_id,$booking_id){
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
        //$joining_date = date('Y-m-d');
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id'");
        $count        = $query->num_rows();
        if ($count > 0) {
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id' => $patient_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'listing_id' => $listing_id,
                'user_name' => $user_name,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'user_gender' => $gender,
                'branch_id' => $branch_id,
                'vendor_id' => $vendor_id,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $joining_date,//$trail_booking_date,
                'trail_booking_time' => $trail_booking_time,
                'payment_mode' => $payment_mode,
                'joining_date' => $joining_date,
                'booking_location' => $booking_location,
                'booking_address' => $booking_address,
                'booking_mobile' => $booking_mobile
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
           
            if($package_id == ''){
         
                if ($test_ids != '') {
                    $Testids = explode(',', $test_ids);
                    foreach ($Testids as $tid) {
                        $test_data = array(
                            'booking_id' => $booking_id,
                            'test_id' => $tid
                        );
                        $this->db->where('booking_id', $booking_id);
                        $this->db->where('test_id', $tid);
                        $rst       = $this->db->update('booking_test', $test_data);
                    }
                }
            }
            
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'patient_id'=> $patient_id,
                'listing_id'=> $listing_id,
                'vendor_type'=> $vendor_id,
                'branch_id'=> $branch_id,
                'branch_name'=> $branch_name,
                'at_home'=> $at_home,
                'address_line1'=> $address_line1,
                'address_line2'=> $address_line2,
                'city'=> $city,
                'state'=> $state,
                'pincode'=> $pincode,
                'mobile_no'=>$mobile ,
                'email_id'=> $email,
                'address_id'=> $address_id,
                'test_id'=> $test_ids,
                'package_id'=> $package_id,
                'booking_date'=> $joining_date,
                'booking_time'=> $trail_booking_time,
                'booking_id'=> $booking_id 
                );
            
            $this->db->where('booking_id', $booking_id);
            $rst_comp         = $this->db->update('lab_booking_details', $insertTolabBookingDetails);    
            
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ', Your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent,web_token')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Your lab package has been booked by patient ' . $user_name .' successfully and Booking Id is ' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            
            // For lab center vendor website firebase push notification by ghanshyam parihar starts :: Added date: 05 Feb 2019
                $web_token      = $vendor_info->web_token;
                $title_web      = $user_name.' has booked an package from '.$vendor_name;
                $img_url        = $userimage;
                // $img_url      = 'https://medicalwale.s3.amazonaws.com/images/img/medical_logo.png';
                $tag            = 'text';
                $agent          = $vendor_info->agent;;
                $connect_type   = 'lab_booking';
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
                $click_action = 'https://labs.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/labs/booking_controller/booking_appointment/'.$listing_id;
                $this->send_gcm_web_notify($title_web, $message, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
            // For lab center vendor website firebase push notification by ghanshyam parihar ends :: Added date: 05 Feb 2019
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
            }
        } else {
            $lab_booking = array(
                'user_id' => $user_id,
                'patient_id' => $patient_id,
                'booking_id' => $booking_id,
                'package_id' => $package_id,
                'listing_id' => $listing_id,
                'user_name' => $user_name,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'user_gender' => $gender,
                'branch_id' => $branch_id,
                'vendor_id' => $vendor_id,
                'booking_date' => $created_at,
                'status' => $status,
                'trail_booking_date' => $trail_booking_date,
                'trail_booking_time' => $trail_booking_time,
                'payment_mode' => $payment_mode,
                'joining_date' => $joining_date,
                'booking_location' => $booking_location,
                'booking_address' => $booking_address,
                'booking_mobile' => $booking_mobile
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            $appointment_id = $this->db->insert_id();
            if($package_id == ''){
                if ($test_ids != '') {
                    $Testids = explode(',', $test_ids);
                    foreach ($Testids as $tid) {
                        $test_data = array(
                            'booking_id' => $booking_id,
                            'test_id' => $tid
                        );
                        $rst       = $this->db->insert('booking_test', $test_data);
                    }
                }
            }
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'patient_id'=> $patient_id,
                'listing_id'=> $listing_id,
                'vendor_type'=> $vendor_id,
                'branch_id'=> $branch_id,
                'branch_name'=> $branch_name,
                'at_home'=> $at_home,
                'address_line1'=> $address_line1,
                'address_line2'=> $address_line2,
                'city'=> $city,
                'state'=> $state,
                'pincode'=> $pincode,
                'mobile_no'=>$mobile ,
                'email_id'=> $email,
                'address_id'=> $address_id,
                'test_id'=> $test_ids,
                'package_id'=> $package_id,
                'booking_date'=> $trail_booking_date,
                'booking_time'=> $trail_booking_time,
                'booking_id'=> $booking_id 
                );
        
            $rst_comp         = $this->db->insert('lab_booking_details', $insertTolabBookingDetails);  
            $this->insert_notification_post_question($user_id, $appointment_id,$listing_id);
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            /////////*******************************************excotel text message vendor ***************************************************
            $vendor_info  = $this->db->select('phone,name,token,token_status,agent,web_token')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ', Your lab package has been booked by patient ' . $user_name . ' successfully and Booking Id is ' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            
            
            // For lab center vendor website firebase push notification by ghanshyam parihar starts date: 05 Feb 2019
                $web_token      = $vendor_info->web_token;
                $title_web      = $user_name.' has booked an package from '.$vendor_name;
                $img_url        = $userimage;
                // $img_url      = 'https://medicalwale.s3.amazonaws.com/images/img/medical_logo.png';
                $tag            = 'text';
                $agent          = $vendor_info->agent;;
                $connect_type   = 'lab_booking';
                
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019
                $click_action = 'https://labs.medicalwale.com/booking_controller/booking_appointment/'.$listing_id;
                // $click_action = 'https://vendor.sandbox.medicalwale.com/labs/booking_controller/booking_appointment/'.$listing_id;
                $this->send_gcm_web_notify($title_web, $message, $web_token, $img_url, $tag, $agent, $connect_type,$click_action);
                // Web notification FCM function by ghanshyam parihar date:4 Feb 2019 Ends
            
            // For lab center vendor website firebase push notification by ghanshyam parihar ends date: 05 Feb 2019
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id'=>$booking_id
                );
            }
        }
    }
    
    // Web notification FCM function by ghanshyam parihar date:16 Jan 2019
    function send_gcm_web_notify($title, $msg, $reg_id, $img_url, $tag, $agent, $type,$click_action) {
            date_default_timezone_set('Asia/Kolkata');
            $date = date('j M Y h:i A');
            if (!defined("GOOGLE_GCM_URL"))
                define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                $agent === 'android' ? 'notification' : 'notification' => array(
                     "title"=> $title,
                     "body" => $msg,
                     "click_action"=> $click_action,
                     "icon"=> $img_url
                )
            );
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                $agent === 'android' ? 'Authorization: key=AAAASZs-aSM:APA91bEQ0SZFS7YnRRK95O2Jp_PdzVC8t_osgDK0UdWXeQRo06fEedknj2b26yfX_YPkLq85h0lc3hUBlnSSPD1qvfikzFYiHd8tlt7BeuOg3f5WRwQxmz19WbEGbZEX894zZEF5iRO5' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
            $result = curl_exec($ch);
            //print_r($result);
            if ($result === FALSE) {
                die('Problem occurred: ' . curl_error($ch));
            }
            curl_close($ch);
    }
    // Web notification FCM function by ghanshyam parihar date:16 Jan 2019 Ends
    
   public function insert_notification_post_question($user_id, $booking_id, $doctor_id){
      $data = array(
                           'user_id'        => $user_id,
                           'post_id'         => $booking_id,
                           'timeline_id'   => $user_id,
                           'type'          => 'Appointment',
                           'seen'          => '1',
                           'notified_by'   => $doctor_id,
                           'description'   => 'Appointment Booking',
                           'created_at'    => curr_date(),
                           'updated_at'    => curr_date()
                           
                   );
       //print_r($data);
       $this->db->insert("notifications", $data);        if($this->db->affected_rows() > 0)
       {
           
           return true; // to the controller
       }
       else{
           return false;
       }
   }  
    
    
    
    
    
     public function tyrocare_lab_booking($vendor_type, $user_id, $address, $amount, $report_code, $pincode, $bencount, $mobile, $email, $order_by, $service_type, $hc, $ref_code, $reports, $bendataxml, $booking_date, $booking_time, $payment_method, $product, $booking_id, $status,$reference_id,$leadId,$passon){
    
        date_default_timezone_set('Asia/Kolkata');
        $created_at   = date('Y-m-d H:i:s');
       /* echo"SELECT * FROM booking_master WHERE booking_id = '$booking_id'";*/
        $query        = $this->db->query("SELECT * FROM booking_master WHERE booking_id = '$booking_id'");
        $count        = $query->num_rows();
        if ($count > 0) {
            $lab_booking = array(
                'user_id' => $user_id,
                'booking_id' => $booking_id,
                'user_name' => $order_by,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'booking_date' => $created_at,
                'status' => $status,
                'vendor_id' =>$vendor_type,
                'payment_mode' => $payment_method,
                'joining_date' => $booking_date
                
            );
            $this->db->where('booking_id', $booking_id);
            $rst = $this->db->update('booking_master', $lab_booking);
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'amount'=> $amount,
                'report_code'=> $report_code,
                'becount'=> $bencount,
                'hc'=> $hc,
                'ref_code'=> $ref_code,
                'reports'=> $reports,
                'bendataxml'=> $bendataxml,
                'product'=> $product,
                'vendor_type'=> $vendor_type,
                'address_line1'=> $address,
                'address_line2'=>$address ,
                'pincode'=> $pincode,
                'mobile_no'=> $mobile,
                'email_id'=> $email,
                'booking_date'=> $booking_date,
                'booking_time'=> $booking_time,
                'booking_id'=> $booking_id,
                'reference_id' => $reference_id,
                'lead_id' => $leadId,
                'passon'=>$passon
                );
            
            $this->db->where('booking_id', $booking_id);
            $rst_comp         = $this->db->update('lab_booking_details', $insertTolabBookingDetails);    
            
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
            
            if($reference_id != '')
          {
            
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
            
          }
            /////////*******************************************excotel text message vendor ***************************************************
           /* $vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ',your  lab package has been booked by patient :' . $user_id . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);*/
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id' => $booking_id
                );
            }
        } else {
            $lab_booking = array(
                'user_id' => $user_id,
                'booking_id' => $booking_id,
                'user_name' => $order_by,
                'user_email' => $email,
                'user_mobile' => $mobile,
                'booking_date' => $created_at,
                'status' => $status,
                'vendor_id' =>$vendor_type,
                'payment_mode' => $payment_method,
                'joining_date' => $booking_date
            );
            $rst         = $this->db->insert('booking_master', $lab_booking);
            
           
            
            $insertTolabBookingDetails = array(
                'user_id'=> $user_id,
                'amount'=> $amount,
                'report_code'=> $report_code,
                'becount'=> $bencount,
                'hc'=> $hc,
                'ref_code'=> $ref_code,
                'reports'=> $reports,
                'bendataxml'=> $bendataxml,
                'product'=> $product,
                'vendor_type'=> $vendor_type,
                'address_line1'=> $address,
                'address_line2'=>$address ,
                'pincode'=> $pincode,
                'mobile_no'=> $mobile,
                'email_id'=> $email,
                'booking_date'=> $booking_date,
                'booking_time'=> $booking_time,
                'booking_id'=> $booking_id,
                'reference_id' => $reference_id,
                'lead_id' => $leadId,
                'passon'=>$passon
                );
        
            $rst_comp         = $this->db->insert('lab_booking_details', $insertTolabBookingDetails);    
            //////// notification for gcm ***********************************************************************
            //added by jakir on 01-june-2018 for notification on activation 
          if($reference_id != '')
          {
            $user_plike = $this->db->query("SELECT name FROM users WHERE id='$user_id'");
            $img_count  = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->num_rows();
            if ($img_count > 0) {
                $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $user_id)->get()->row();
                $img_file      = $profile_query->source;
                $userimage     = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $img_file;
            } else {
                $userimage = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_user.jpg';
            }
            $getusr               = $user_plike->row_array();
            $usr_name             = $getusr['name'];
            $msg                  = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token       = $this->db->query("SELECT token,agent, token_status FROM users WHERE id = '$user_id'");
            $title                = $order_by . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $customer_token_count = $customer_token->num_rows();
            if ($customer_token_count > 0) {
                $token_status = $customer_token->row_array();
                $agent        = $token_status['agent'];
                $reg_id       = $token_status['token'];
                $img_url      = $userimage;
                $tag          = 'text';
                $key_count    = '1';
                $this->send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent);
            }
            //end
            ////////////////////***********************************excotel text message user****************************************** 
            $user_info    = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $user_id)->get()->row();
            $token_status = $user_info->token_status;
            $user_phone   = $user_info->phone;
            $user_name    = $user_info->name;
            $message      = $user_name . ',your package has been booked successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $user_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);
          }
            /////////*******************************************excotel text message vendor ***************************************************
            /*$vendor_info  = $this->db->select('phone,name,token,token_status,agent')->from('users')->where('id', $listing_id)->get()->row();
            $token_status = $vendor_info->token_status;
            $vendor_phone = $vendor_info->phone;
            $vendor_name  = $vendor_info->name;
            $message      = $vendor_name . ',your  lab package has been booked by patient :' . $user_id . 'successfully and Booking Id :' . $booking_id . ' for future reference.';
            $post_data    = array(
                'From' => '02233721563',
                'To' => $vendor_phone,
                'Body' => $message
            );
            $exotel_sid   = "aegishealthsolutions";
            $exotel_token = "a642d2084294a21f0eed3498414496229958edc5";
            $url          = "https://" . $exotel_sid . ":" . $exotel_token . "@twilix.exotel.in/v1/Accounts/" . $exotel_sid . "/Sms/send";
            $ch           = curl_init();
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_FAILONERROR, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
            $http_result = curl_exec($ch);
            curl_close($ch);*/
            
            
            if ($rst) {
                return array(
                    'status' => 201,
                    'message' => 'success',
                    'booking_id' => $booking_id
                );
            }
        }
    }
    
    
    public function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $agent){
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        if (!defined("GOOGLE_GCM_URL"))
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields  = array(
            'to' => $reg_id,
            'priority' => "high",
            $agent === 'android' ? 'data' : 'notification' => array(
                "title" => $title,
                "message" => $msg,
                "notification_image" => $img_url,
                "tag" => $tag,
                'sound' => 'default',
                "notification_type" => 'thyro_lab_booking',
                "notification_date" => $date
            )
        );
        $headers = array(
            GOOGLE_GCM_URL,
            'Content-Type: application/json',
            $agent === 'android' ? 'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM' : 'Authorization: key=AIzaSyCN1vVES0Jb3zVHeNCYPAMNnPYTl96H1uE'
        );
        $ch      = curl_init();
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
    
    
    public function lab_test_list($user_id, $page){
        $limit = 5;
        $start = 0;
        if ($page > 0) {
            if (!is_numeric($page)) {
                $page = 1;
            }
        }
        $test_in_home_list = array();
        $test_in_lab_list = array();
        $start      = ($page - 1) * $limit;
        $query      = $this->db->query("SELECT * FROM `lab_test_details` WHERE `user_id` = '$user_id'");
        $total_user = $query->num_rows();
        $qRows      = $query->row();
        if ($total_user > 0) {
            //echo "SELECT * FROM `tbl_invoice_attachment` WHERE `invoice_id` = '$qRows->id'";
            $query        = $this->db->query("SELECT * FROM `lab_test_details` WHERE `user_id` = '$user_id' order by id DESC limit $start,$limit");
            $num_count    = $query->num_rows();
            $qRows_attach = $query->result();
            if ($num_count > 0) {
                foreach ($qRows_attach as $test_row) {
                   
                    $user_id        = $test_row->user_id;
                        $test           = $test_row->test;
                        $test_id        = $test_row->test_id;
                        $price          = $test_row->price;
                        $offer          = $test_row->offer;
                        $executive_rate = $test_row->executive_rate;
                        $home_delivery  = $test_row->home_delivery;
                        if ($home_delivery == "0") {
                            $test_in_lab_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "0"
                            );
                        } else {
                            $test_in_home_list[] = array(
                                'user_id' => $user_id,
                                'test_id' => $test_id,
                                'test' => $test,
                                'price' => $price,
                                'offer' => $offer,
                                'executive_rate' => $executive_rate,
                                'home_delivery' => "1"
                            );
                        }
                }
            } else {
               $test_in_home_list = array();
               $test_in_lab_list = array();
            }
            $tests = array(
                'total' => $total_user,
                'home_test_done' => $test_in_home_list,
                'lab_test_done' => $test_in_lab_list
            );
            return array(
                'status' => 200,
                'message' => 'success',
                'data' => $tests
            );
        } else {
            return array(
                'status' => 201,
                'message' => 'No data found!'
            );
        }
    }
    
    public function lab_booked_list($user_id, $listing_id){
        
        $count_query = $this->db->query("SELECT * from lab_booking_details where user_id='$user_id' and listing_id='$listing_id'");
        $lab_booked       = $count_query->num_rows();
        if ($lab_booked > 0) {
            foreach ($count_query->result_array() as $Lbooked) {
                    
                
                 $Lab_Bkooed_list[] = array(
                    'user_id'=> $Lbooked['user_id'],
                    'patient_id'=> $Lbooked['patient_id'],
                    'listing_id'=> $Lbooked['listing_id'],
                    'vendor_type'=> $Lbooked['vendor_type'],
                    'branch_id'=> $Lbooked['branch_id'],
                    'branch_name'=> $Lbooked['branch_name'],
                    'at_home'=> $Lbooked['at_home'],
                    'address_line1'=> $Lbooked['address_line1'],
                    'address_line2'=> $Lbooked['address_line2'],
                    'city'=> $Lbooked['city'],
                    'state'=> $Lbooked['state'],
                    'pincode'=> $Lbooked['pincode'],
                    'mobile_no'=> $Lbooked['mobile_no'],
                    'email_id'=> $Lbooked['email_id'],
                    'address_id'=> $Lbooked['address_id'],
                    'test_id'=> $Lbooked['test_id'],
                    'package_id'=> $Lbooked['package_id'],
                    'booking_date'=> $Lbooked['booking_date'],
                    'booking_time'=> $Lbooked['booking_time'],
                    'booking_id'=> $Lbooked['booking_id'] 
                );
            }
            return $Lab_Bkooed_list;
        }else{
            return $Lab_Bkooed_list= array();
        }
        
    }
    
    public function tyrocare_booked_list($vendor_type,$userid){
        
        $count_query = $this->db->query("SELECT * from lab_booking_details where vendor_type='$vendor_type' and user_id='$userid' and reference_id!=''");
                $lab_booked       = $count_query->num_rows();
        if ($lab_booked > 0) {
            foreach ($count_query->result_array() as $Lbooked) {
                    
                $uid =$Lbooked['user_id'];    
            $user_query = $this->db->query("SELECT email,phone,name FROM users WHERE id='$uid'");
                $email = $user_query->row()->email;
                $phone = $user_query->row()->phone;
                $user_name = $user_query->row()->name;        
                    
                $bk_id = $Lbooked['booking_id'];   
                /*echo"SELECT status FROM booking_master WHERE booking_id='$bk_id'";*/
            $book_query = $this->db->query("SELECT status FROM booking_master WHERE booking_id='$bk_id'");
            if(sizeof($book_query) > 0){
                $status = $book_query->row()->status;
            } else{
                $status = "";
            }
            
            
            //NAWAZ
            //echo "SELECT report_path FROM reports WHERE booking_id='$bk_id'";
            $report_path ='';
            $book_query_path = $this->db->query("SELECT report_path FROM reports WHERE booking_id='$bk_id'");
            $report_path_count =  $book_query_path->num_rows();;
            if($report_path_count > 0){
                $report_path = $book_query_path->row()->report_path;
            }
            
                      
                    
                $Lab_Bkooed_list[] = array(
                'user_id'=> $Lbooked['user_id'], 
                'user_name'=>$user_name,
                'amount'=> $Lbooked['amount'],
                'report_code'=> $Lbooked['report_code'],
                'becount'=> $Lbooked['becount'],
                'hc'=> $Lbooked['hc'],
                'ref_code'=> $Lbooked['ref_code'],
                'reports'=> $Lbooked['reports'],
                'bendataxml'=> $Lbooked['bendataxml'],
                'product'=> $Lbooked['product'],
                'vendor_type'=> $Lbooked['vendor_type'],
                'address_line1'=> $Lbooked['address_line1'],
                'pincode'=> $Lbooked['pincode'],
                'mobile_no'=> $Lbooked['mobile_no'],
                'email_id'=> $Lbooked['email_id'],
                'booking_date'=> $Lbooked['booking_date'],
                'booking_time'=> $Lbooked['booking_time'],
                'booking_id'=> $Lbooked['booking_id'],
                'reference_id'=> $Lbooked['reference_id'],
                'lead_id'=> $Lbooked['lead_id'],
                'status'=>$status,
                'report_path'=>$report_path
                );
            }
            return $Lab_Bkooed_list;
        }else{
            return $Lab_Bkooed_list= array();
        }
        
    }
    
    
    //to display instruction for perticular test
     public function lab_instruction_details($user_id,$id){
         $query = $this->db->query("SELECT * FROM labs_instruction WHERE id='$id'");
         $query_details = $query->row_array();
          $count = $query->num_rows();
          if($count>0)
          {
                $sample_type = $query_details['sample_type'];
                $test_name   = $query_details['test_name'];
                $instruction = $query_details['instruction'];
                
                $instruction_data = array(
                      'sample_type' => $sample_type,
                      'test_name' => $test_name,
                      'instruction' => $instruction
                    );
                
                return array(
                'status' => 201,
                'message' => 'success',
                'data' => $instruction_data
            );
          }     
          else
          {
               return array(
                'status' => 201,
                'message' => 'success',
                'data' => array()
            );
          }
     }
     
     
       
    //Added by Swapnali 
    public function lab_tests($user_id){
        $result = $this->db->query("SELECT * FROM `lab_all_test`")->result_array();
          return $result;
    }
    
    // lab_vendor_by_test
    public function lab_vendor_by_test1($user_id,$test_id){
        //SELECT * FROM `lab_test_details` LIMIT 10 OFFSET 10

        $testInfo = $this->db->query("SELECT * FROM `lab_all_test` WHERE `id` = '$test_id'")->row_array();
        $testName = $testInfo['test'];
        $results = $this->db->query("SELECT * FROM `lab_test_details` WHERE `test_id` = '$test_id' limit 20")->result_array();
        foreach($results as $r){
            $vendorId = $r['user_id'];
            $vendorInfo = $this->db->query("SELECT * FROM `lab_center` WHERE `is_active` = 1 AND `user_id` = '$vendorId'")->row_array();
            if(!empty($vendorInfo)){
                $r['vendor_info'] = $vendorInfo;
            } else {
                $r['vendor_info'] = (object)[];
            }
            
            $res[] = $r;
        }
        $test['test_name'] = $testName;
        $test['test_available'] = $res;
        
        // print_r($res);
        // die();
        return $test;
    }
    
      public function lab_vendor_by_test($user_id,$test_id,$per_page,$page_no){
        //SELECT * FROM `lab_test_details` LIMIT 10 OFFSET 10
       
            // $image1             = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/' . $image;
        if($per_page == 0 || $page_no == 0){
            $testInfo = $this->db->query("SELECT * FROM `lab_all_test` WHERE `id` = '$test_id'")->row_array();
            $testName = $testInfo['test'];
            // $results = $this->db->query("SELECT * FROM `lab_test_details` WHERE `test_id` = '$test_id' limit 20")->result_array();
            $results = $this->db->query("SELECT lt.* FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE lc.`is_active` = 1 AND `test_id` = '$test_id'")->result_array();
            
            foreach($results as $r){
                $vendorId = $r['user_id'];
                $vendorInfo = $this->db->query("SELECT * FROM `lab_center` WHERE `is_active` = 1 AND `user_id` = '$vendorId'")->row_array();
                if(!empty($vendorInfo)){
                    foreach($vendorInfo as $key => $value){
                       if($key == 'profile_pic' ){
                            $vendorInfo[$key] = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$vendorInfo['profile_pic'] ;
                        }
                    }
                    
                    $r['vendor_info'] = $vendorInfo;
                } else {
                    $r['vendor_info'] = (object)[];
                }
                
                $res[] = $r;
            }
            $test['test_name'] = $testName;
            $test['test_available'] = $res;
        } else {
            $offset = $per_page*($page_no - 1);
            
            $testInfo = $this->db->query("SELECT * FROM `lab_all_test` WHERE `id` = '$test_id'")->row_array();
            $testName = $testInfo['test'];
            // SELECT lt.* FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE `test_id` = '1' 
            // SELECT count(lt.id) FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE `test_id` = '1'

            //$results = $this->db->query("SELECT * FROM `lab_test_details` WHERE `test_id` = '$test_id' LIMIT $per_page OFFSET $offset")->result_array();
            // $totalRowCount = $this->db->query("SELECT COUNT(`id`) as count FROM `lab_test_details` WHERE `test_id` = '$test_id'")->row_array();
            
            $results = $this->db->query("SELECT lt.* FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE lc.`is_active` = 1 AND `test_id` = '$test_id' LIMIT $per_page OFFSET $offset")->result_array();
            $totalRowCount = $this->db->query("SELECT count(lt.id) as count FROM `lab_test_details` as lt join lab_center as lc on (lt.user_id = lc.user_id ) WHERE lc.`is_active` = 1 AND `test_id` = '$test_id'")->row_array();
            
            
            
            $totalData = $totalRowCount['count'];
            $last_page = ceil($totalData/$per_page);
            foreach($results as $r){
                $vendorId = $r['user_id'];
                $vendorInfo = $this->db->query("SELECT * FROM `lab_center` WHERE  `is_active` = 1 AND `user_id` = '$vendorId'")->row_array();
                if(!empty($vendorInfo)){
                     foreach($vendorInfo as $key => $value){
                       if($key == 'profile_pic' ){
                            $vendorInfo[$key] = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$vendorInfo['profile_pic'] ;
                        }
                    }
                    $r['vendor_info'] = $vendorInfo;
                } else {
                    $r['vendor_info'] = (object)[];
                }
                
                $res[] = $r;
            }
            $test['test_name'] = $testName;
            $test['data_count'] = $totalData;
            $test['per_page'] = $per_page;
            $test['current_page'] = $page_no;
            $test['first_page'] = 1;
            $test['last_page'] = $last_page;
            $test['test_available'] = $res;
        }
       
        return $test;
    }
    
    public function lab_test_by_vendor($vendor_id){
        $data = array();
        $data =  $this->db->query("SELECT * FROM `lab_test_details` WHERE `user_id` = '$vendor_id'")->result_array();
     
        return $data;
    }
    
    public function update_email($user_id,$email){
       $up=$this->db->query("UPDATE users SET email='$email' WHERE id='$user_id'");
       $data=  array(
                'status' => 201,
                'message' => 'success');
        return $data;
    }
    //end  
}




