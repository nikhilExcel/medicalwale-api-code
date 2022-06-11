<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class UserstackModel extends CI_Model{
        
    // add_to_stack
    
    public function add_to_stack1($user_id, $listing_type, $stack){
        
        $stackSize = sizeof($stack);
        foreach($stack as $s){
            $vendior_id = $s['vendior_id'];
            $service_type = $s['service_type'];
            $service_id = $s['service_id'];
            $quantity = $s['quantity'];
            $t=time();
            $created_at = date("Y-m-d H:s:i",$t);
            
            $availableInStack = $this->db->query("select * from `services_stack` WHERE `service_id` = '$service_id' AND  `service_type` = '$service_type' AND `vendor_id` = '$vendior_id' AND `user_id` = '$user_id' AND  `listing_type` = '$listing_type'")->row_array();
            if(sizeof($availableInStack) > 0){
                $inStackId = $availableInStack['id'];
                
                $this->db->query("UPDATE `services_stack` SET `user_id`='$user_id',`vendor_id`='$vendior_id',`service_type`='$service_type',`service_id`='$service_id',`quantity`='$quantity',`created_at`='$created_at' WHERE `id` = '$inStackId'") ;
                
            } else {
                
                 $this->db->query("INSERT INTO `services_stack`(`listing_type`, `user_id`, `vendor_id`, `service_type`, `service_id`, `quantity`) VALUES ('$listing_type','$user_id', '$vendior_id','$service_type','$service_id','$quantity')");
                 
            }
            
        }
        
        // $result = array("status"=> 200, "message" => 'success' , "data" => 'Updated successfully');
        
       
        return 1;
    }
    
    // add_to_stack1
    
    public function add_to_stack($data){
        $old_vendor_id = 0;
        $user_id = $data['user_id'];
        $listing_type = $data['listing_type'];
        $stack = $data['stack'];
        
        $stack = json_decode($stack);
        foreach($stack as $s){
            $vendior_id = $s->vendior_id;
            $service_type = $s->service_type;
            $service_id = $s->service_id;
            $quantity = $s->quantity;
            $t=time();
            $created_at = date("Y-m-d H:i:s",$t);
            
            $availableInStack = $this->db->query("select * from `services_stack` WHERE `user_id` = '$user_id' AND  `listing_type` = '$listing_type'")->result_array();
            if(sizeof($availableInStack) > 0){
                
                $availableInStack1 = $this->db->query("select * from `services_stack` WHERE `vendor_id` = '$vendior_id' AND `service_id` = '$service_id' AND  `service_type` = '$service_type' AND `user_id` = '$user_id' AND  `listing_type` = '$listing_type'")->row_array();
                $availableInStack2 = $this->db->query("select * from `services_stack` WHERE `vendor_id` != '$vendior_id' AND `user_id` = '$user_id' AND  `listing_type` = '$listing_type'")->result_array();
               
                if(sizeof($availableInStack1) > 0 && sizeof($availableInStack2) == 0){
                    $inStackId = $availableInStack1['id'];
                    $this->db->query("UPDATE `services_stack` SET `user_id`='$user_id',`vendor_id`='$vendior_id',`service_type`='$service_type',`service_id`='$service_id',`quantity`='$quantity' WHERE `id` = '$inStackId'") ;
               
                } else if(sizeof($availableInStack2) > 0){
                    
                    $old_vendor_id = $availableInStack1['vendor_id'];
                    if(!empty($old_vendor_id)){
                        $vendor_name = $this->db->query("SELECT `name` FROM `users` WHERE `id` = '$old_vendor_id'")->row_array();
                        $vendorName = $vendor_name['name'];
                        $data = array("status" => 201, "vendor_name" => $vendorName, "message" => 'Stack contains tests / packages from '. $vendorName .'. Do you wish to clear the existing stack?' );
                    } else {
                        $data = array("status" => 201, "vendor_name" => "", "message" => 'Stack contains tests / packages from different labs. Do you wish to clear the existing stack?');
                    }
                    return $data;
                
                }  else if(sizeof($availableInStack1) == 0 && sizeof($availableInStack2) == 0) {
                    $this->db->query("INSERT INTO `services_stack`(`listing_type`, `user_id`, `vendor_id`, `service_type`, `service_id`, `quantity`,`created_at`) VALUES ('$listing_type','$user_id', '$vendior_id','$service_type','$service_id','$quantity','$created_at')");
                
                }  else {
                    $data = array("status" => 400);
                    return $data;
                }  
            } else if(sizeof($availableInStack) == 0){
               
               $this->db->query("INSERT INTO `services_stack`(`listing_type`, `user_id`, `vendor_id`, `service_type`, `service_id`, `quantity`,`created_at`) VALUES ('$listing_type','$user_id', '$vendior_id','$service_type','$service_id','$quantity','$created_at')");
    
            }  else {
                $data = array("status" => 400);
                return $data;
            }
        }
        
        // $result = array("status"=> 200, "message" => 'success' , "data" => 'Updated successfully');
        
        $data = array("status" => 200);
        return $data;
    }
    
    
    public function stack_list($user_id) {
        $this->load->model('LabcenterModel_v2');
        
        $resultpost['vendor_id'] = "";
        $resultpost['vendor_image'] = "";
         $resultpost['test'] = array();
            $resultpost['package'] = array();
        $discounted_price = 0;
        $query = $this->db->query("SELECT ss.id,ss.listing_type,ss.user_id,ss.vendor_id,ss.service_type,ss.service_id,ss.quantity, lc.profile_pic from `services_stack` as ss LEFT JOIN `lab_center` as lc ON ( ss.vendor_id = lc.user_id ) WHERE ss.user_id='$user_id' GROUP by ss.id order by id asc");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                // print_r($row); die();
                    $vendor_id = $row['vendor_id'];
                    $vendor_image = $row['profile_pic'];
                    if($row['service_type'] == 'test' ){
                        // print_r($row); die();
                        $id = $row['service_id'];
                        
                        // $test = $this->db->query("SELECT ld.*,lt.test FROM `lab_test_details1` as ld LEFT JOIN lab_all_test1 as lt ON(ld.`test_id` = lt.id) WHERE ld.`id` = '$id'")->row_array();
                        $test = $this->db->query("SELECT ld.*,lt.test FROM `lab_test_details1` as ld LEFT JOIN lab_all_test1 as lt ON(ld.`test_id` = lt.id) WHERE ld.user_id = '$vendor_id' AND ld.`test_id` = '$id'")->row_array();    
                        // print_r($test); 
                        // die();
                        $discount = $test['discount'];
                        $Price = $test['price'];
                        $discount_type = 'percent';
                        $discounted_price = $test['discounted_price'];
                        
                        
                        if($discount > 0){
                            $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                        } else {
                            $discounted_price = intval($Price); 
                        }
                        
                        $allTests = array(
                            "id" => $row['id'],
                            "listing_type" => $row['listing_type'],
                            "vendor_id" => $row['vendor_id'],
                            'service_type' => $row['service_type'],
                            'service_id' => $row['service_id'],
                            'quantity' => $row['quantity'],
                            'test_name' => $test['test'],
                            'Price' => $Price,
                            'discount' => $discount
                            
                        );
                        
                        
                        foreach($allTests as $k => $v){
                            if($v == null){
                                $allTests[$k] = "";
                            }
                        }
                         $allTests['discounted_price'] = strval($discounted_price);
                        
                        $resultpost['test'][] = $allTests;
                    }
                    
                    
                    if($row['service_type'] == 'package' ){
                         $vendor_id = $row['vendor_id'];
                         $vendor_image = $row['profile_pic'];
                        $id = $row['service_id'];
                        $test = $this->db->query("SELECT * FROM `lab_packages1` WHERE `id` = '$id'")->row_array();
                            
                       
                            $Price = $test['Price'];
                            $discount_type = $test['discount_type'];
                            $discount = $test['discount'];
                           // $discounted_price = 0;
                           // die();
                            
                            
                            if($discount > 0){
                                $discounted_price = $this->LabcenterModel_v2->get_disounted_rate($Price,$discount_type,$discount);
                            } else {
                                $discounted_price = intval($Price); 
                            }
                            
                            $data =  array(
                            "id" => $row['id'],
                            "listing_type" => $row['listing_type'],
                            "vendor_id" => $row['vendor_id'],
                            'service_type' => $row['service_type'],
                            'service_id' => $row['service_id'],
                            'quantity' => $row['quantity'],
                            
                            'package_id' => $test['id'],
                            'package_name' => $test['package_name'],
                            'Price' => $Price,
                            'discount_type' => $discount_type,
                            'discount' => $discount,
                            
                        );
                        $finaldata = array();
                        foreach($data as $k => $v){
                            if($v == null){
                                $data[$k] = "";
                            }   
                        }
                        $data['discounted_price'] = strval($discounted_price);
                            
                        $resultpost['package'][] = $data;
                    }
                    
                    if(!empty($vendor_image)){
                        $vendor_image = "https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/".$vendor_image;
                    } else {
                        $vendor_image = "";
                    }
                    
                    $resultpost['vendor_id'] = $vendor_id;
                    $resultpost['vendor_image'] = $vendor_image;
                    
                   
                
            }
            // die();
            //print_r($resultpost); die();
        } else {
            // $resultpost = array();
            $resultpost['vendor_id'] = "";
            $resultpost['vendor_image'] = "";
            $resultpost['test'] = array();
            $resultpost['package'] = array();
        }
        return $resultpost;
    }
    
    public function delete_stack($user_id,$stack_id,$remove_all) {
        if($remove_all == 1){
            $query = $this->db->query("DELETE FROM `services_stack` WHERE `user_id`='$user_id'");
              
        } else {
            $query = $this->db->query("DELETE FROM `services_stack` WHERE id='$stack_id' AND  `user_id`='$user_id'");
              
        }
        $affected_rows = $this->db->affected_rows();
        if($affected_rows > 0){
            return array(
                'status' => 201,
                'message' => 'success'
            );     
        } else {
            return array(
                'status' => 400,
                'message' => 'failed'
            ); 
        }
         
    } 
    
    // delete_test_packages
    public function delete_test_packages($user_id,$service_type,$service_id,$remove_all){
        if($remove_all == 1){
            $query = $this->db->query("DELETE FROM `services_stack` WHERE `user_id`='$user_id'");
        } else {
            $query = $this->db->query("DELETE FROM `services_stack` WHERE `user_id`='$user_id' AND `service_id` = '$service_id' AND `service_type` = '$service_type'");
        }
        $affected_rows = $this->db->affected_rows();
        if($affected_rows > 0){
            return array(
                'status' => 200,
                'message' => 'Deleted from stack'
            );     
        } else {
            return array(
                'status' => 201,
                'message' => 'This test / packages has been deleted'
            ); 
        }
    }
    
    // 
    
    
    
    
}
