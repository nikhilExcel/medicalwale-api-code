<?php
 class User_model extends CI_Model {
       
    public function __construct(){
          
        
        $this->load->database();
        //  $this->load->model('MedicalMall_model_2');
       
      }
      
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    
    
    public function generate_user_code($user_id, $pd_id){
        $getUserDetails = $this->db->query("SELECT * FROM `users` WHERE `id` = '$user_id'")->row_array();
        // print_r($getUserDetails);
        $userName = $getUserDetails['name'];
        
        $today = $created_date = date("Y-m-d  H:i:s");
        // $codeDate = date("jmy");
        $codeDate = rand(0,999);
        
        $tomorrow = new DateTime($created_date);
        $expiry = $tomorrow->format('Y-m-d H:i:s');


        // $expiry = $created_date->modify('+1 day');
        $status = 1;

        $name = substr(strtolower(preg_replace('/[^a-zA-Z0-9]/','', $userName)), 0, 3);
        $checkCode = $name.$user_id.$pd_id.$codeDate;
        
        // check code
        
        $code = $this->User_model->check_code($checkCode,$user_id, $pd_id);
        // echo $code;
         //die();
        $share_url = "https://medicalwale.com/healthmall/#/product/".$pd_id;
        $codeExists  = $this->db->query("SELECT * FROM `refer_product_hm` WHERE user_id = '$user_id' AND pd_id = '$pd_id'");
        
        if($codeExists->num_rows() > 0){
            $rows = $codeExists->result_array();
            $count = 0;
            foreach($rows as $row){
                if($today < $row['expiry']){
                    
                    $rowExp = $row['expiry'];
                    $rowCode = $row['code'];
                    $count++;
                    
                }
            }
            
             if($count == 0) {
                $insertRow  = $this->db->query("INSERT INTO `refer_product_hm` (`user_id`, `pd_id`, `code`, `created_date`, `expiry`, `status`) VALUES ('$user_id', '$pd_id', '$code', '$created_date', '$expiry', '$status')");
            
             $message = "success";
            
                $result = array(
                     "status"=> 200,
                     "message"=> $message,
                     "code" => $code,
                     "expiry" => $expiry,
                     "share_url" => $share_url
                     
                );
            
               
            } else {
                $message = "Could not create the code. Existing code will expire on  $rowExp";
                
                $result = array(
                     "status"=> 400,
                     "message"=> $message,
                     "code" => $rowCode,
                     "expiry" => $rowExp,
                     "share_url" => $share_url
                     
                );
                
            }
        } else {
            $insertRow  = $this->db->query("INSERT INTO `refer_product_hm` (`user_id`, `pd_id`, `code`, `created_date`, `expiry`, `status`) VALUES ('$user_id', '$pd_id', '$code', '$created_date', '$expiry', '$status')");
            
            $message = "success";
            
            $result = array(
                     "status"=> 200,
                     "message"=> $message,
                     "code" => $code,
                     "expiry" => $expiry,
                     "share_url" => $share_url
                     
                     
                );
            
        }
        
       
            
        return $result;
    }
    
    // check_code
    public function check_code($code, $user_id, $pd_id){
        $count = $this->db->query("SELECT * FROM `refer_product_hm` WHERE `code` = '$code'")->num_rows();
        //echo $count;
         //die();
        if($count > 0){
            
            $getUserDetails = $this->db->query("SELECT * FROM `users` WHERE `id` = '$user_id'")->row_array();
            $userName = $getUserDetails['name'];
            $codeDate = rand(0,999);
            $name = substr(strtolower(preg_replace('/[^a-zA-Z0-9]/','', $userName)), 0, 3);
            $codeNew = $name.$user_id.$pd_id.$codeDate;
            
          
            $checkAgain = $this->User_model->check_code($codeNew,$user_id, $pd_id);
            
            $finalCode = $checkAgain;
           
        } else {
            
             $finalCode = $code;
            
        
        }
        
        return $finalCode;
    }
    
    
    // add_referral_code
    public function add_referral_code($referral_code, $pd_id){
        $nonExpRows = array();
        $codeQuery = $this->db->query("SELECT * FROM `refer_product_hm` WHERE `code` = '$referral_code' ");
        $count = $codeQuery->num_rows();
        $codeRows = $codeQuery->result_array();
        $today = date("Y-m-d h:i:s");
        
        if($count > 0){
            
            
            // check expiry
            $expCount = 0;
            foreach($codeRows as $codeRow){
                $exp = $codeRow['expiry'];
                if($today < $exp){
                    $nonExpRows[] = $codeRow;
                } else {
                    $expCount++;
                }
                
            }
            if($expCount > 0){
                 $message = "Referal code is expired";
                $data = array(
                    "status" => 400,
                    "message" => "failed",
                    "description" => $message,
                    "data" => array()
                    );
               
            } else {
                
                if($pd_id != $nonExpRows[0]['pd_id']){
                    $pdName = $this->db->query("SELECT `pd_name` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
                    $message = "Try same code for ".$pdName['pd_name'] ." product";
                     $data = array(
                        "status" => 201,
                        "message" => "success",
                        "description" => $message,
                        "data" => array()
                        );
                
                } else {
                    $message = "code successfully added";
                    $data = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => $message,
                        "data" => array("pd_id" => $pd_id, "referal_id" => $nonExpRows[0]['id'], "expiry" => $nonExpRows[0]['expiry']) 
                        );
                }
                
               
            }
            
            
        } else {
            
            
            
            
            $message = "Code does not exists";
            
             $data = array(
                    "status" => 400,
                    "message" => "failed",
                    "description" => $message
                    );
                    
        }
        
      
        return $data;
    }
    
    // get_products_by_cats
    public function get_products_by_cats($allCats,$vendor_id){
        $results = $allPds = array();
        $idsComma = implode(",",$allCats);
        // (pd_added_v_id IN (".$brnd.") AND (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))) AND pd_quantity >= 1 AND pd_status = 1
        // `pd_pc_id` IN ('$idsComma') OR `pd_psc_id` IN ('$idsComma')
        $allPds = $this->db->query("SELECT DISTINCT `pd_id` FROM `product_details_hm` WHERE (pd_added_v_id IN (".$vendor_id.") AND (`pd_pc_id` IN (".$idsComma.") OR `pd_psc_id` IN (".$idsComma."))) AND pd_status = 1 ORDER BY `pd_id` ASC")->result_array();
        foreach($allPds as $Pd){
            $results[] = $Pd['pd_id'];
        }
        // print_r($results); die();
        
        return $results;
        
        
    }
    // applicable_to_products
    
    public function applicable_to_products($offer_on, $offerIdsArray,$vendor_id){
        $allCats = $cats = $pds = array();
        if($offer_on == 1 || $offer_on == 2){
             foreach($offerIdsArray as $offerIdOn){
                $subCats = $this->MedicalMall_model_2->get_subcat($offerIdOn);
             }
             foreach($subCats as $subCat){
                $cats[] =  $subCat[0];
             }
            //   = $subCats;
             $allCats = array_merge($cats,$offerIdsArray);
             $pds = $this->User_model->get_products_by_cats($allCats,$vendor_id);
        } else {
            $pds = explode(",",$offerIdsArray[0]);
        }
        
        // print_r(array_unique($allCats)); die();
        return $pds;
    }
    // add_referral_code
    public function add_physical_code($physical_code, $pd_id, $user_id){
        $pdCount = 0;
        $nonExpRows = array();
        $codeQuery = $this->db->query("SELECT * FROM `vendor_offers` WHERE `name` = '$physical_code' AND `status` = '0' AND `listing_id` = '34'  ");
        $count = $codeQuery->num_rows();
        $codeRows = $codeQuery->result_array();
        $today = date("Y-m-d");
        //  print_r($codeRow); die();
         
        if($count > 0){
            
            
           
            
            // check expiry
            $expCount = 0;
            foreach($codeRows as $codeRow){
                $exp = $codeRow['end_date'];
                if($today < $exp){
                    $nonExpRows[] = $codeRow;
                } else {
                    $expCount++;
                }
            }
            if($expCount > 0){
                 $message = "Referal code is expired";
                $data = array(
                    "status" => 400,
                    "message" => "failed",
                    "description" => $message,
                    "data" => array()
                    );
               
            } else {
                
                // SELECT * FROM `orders_details_hm` WHERE `user_id` = 5 AND `product_id` LIKE '1' AND `offer_id` = 2

                
                
                $offer_on = $nonExpRows[0]['offer_on'];
                $offerIds = $nonExpRows[0]['offer_on_ids'];
                $vendor_id = $nonExpRows[0]['vendor_id'];
                $id = $nonExpRows[0]['id'];
                $offerIdsArray = explode(', ', $offerIds);
                
                $pdArray = $this->User_model->applicable_to_products($offer_on, $offerIdsArray,$vendor_id); 
                // print_r($id); die();
                
                $offerUsed = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `user_id` = '$user_id' AND `product_id` = '$pd_id' AND `offer_id` = '$id'")->result_array();
                // print_r(sizeof($offerUsed)); die();
                $count = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `user_id` = '$user_id' AND `product_id` = '$pd_id' AND `offer_id` = '$id'")->num_rows();
                // = $offerUsed->num_rows();
                if($count == 0){
                    
                
                
                foreach($pdArray as $pd){
                    if($pd == $pd_id ){
                        $pdCount++;
                    }
                      
                } 
             
                
                
                if($pdCount == 0){
                    $pdName = $this->db->query("SELECT `pd_name` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
                    $message = "Try same code for ".$pdName['pd_name'] ." product";
                     $data = array(
                        "status" => 201,
                        "message" => "success",
                        "description" => $message,
                        "data" => array()
                        );
                
                } else {
                    $message = "code is applicable to this product";
                    $data = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => $message,
                        "data" => array("pd_id" => $pd_id, "code_id" => $nonExpRows[0]['id'], "expiry" => $nonExpRows[0]['end_date']) 
                        );
                }
                
                
            } else {
                
                $message = "Already used this code";
                    $data = array(
                        "status" => 401,
                        "message" => "failed",
                        "description" => $message,
                        "data" => array("pd_id" => $pd_id, "code_id" => $nonExpRows[0]['id'], "expiry" => $nonExpRows[0]['end_date']) 
                        );
                
            }    
                
               
            }
            
            
            
            
        } else {
            
            
            
            
            $message = "Code does not exists";
            
             $data = array(
                    "status" => 400,
                    "message" => "failed",
                    "description" => $message
                    );
                    
        }
        
      
        return $data;
    }
  
 }
 ?>