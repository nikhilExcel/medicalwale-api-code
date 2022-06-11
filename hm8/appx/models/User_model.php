    <?php
     class User_model extends CI_Model {
           
        public function __construct(){
              
            
            $this->load->database();
            //  $this->load->model('MedicalMall_model_2');
           
          }
          
        var $client_service = "frontend-client";
        var $auth_key = "medicalwalerestapi";
        
        public function slugify($text) {
           $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
           $text = trim($text, '-');
           $text = strtolower($text);
           $text = preg_replace('~[^-\w]+~', '', $text);
           if (empty($text))
               return 'n-a';
           return $text;
        }
        
        public function generate_user_code($user_id, $pd_id){
            $getUserDetails = $this->db->query("SELECT * FROM `users` WHERE `id` = '$user_id'")->row_array();
            // print_r($getUserDetails);
            $userName = $getUserDetails['name'];
            
            $today = $created_date = date("Y-m-d  H:i:s");
            // $codeDate = date("jmy");
            $codeDate = rand(0,999);
            
            $tomorrow = new DateTime($created_date);
            $expiry = $tomorrow->format('Y-m-d H:i:s');
            
            $expiry=Date('Y-m-d H:i:s', strtotime("+10 days"));
    
            // $expiry = $created_date->modify('+1 day');
            $status = 1;
    
            $name = substr(strtolower(preg_replace('/[^a-zA-Z0-9]/','', $userName)), 0, 3);
            // $checkCode = $name.$user_id.$pd_id.$codeDate;
            $checkCode = $name.$pd_id.$codeDate;
            
            // check code
            
            $code = $this->User_model->check_code($checkCode,$user_id, $pd_id);
            // echo $code;
             //die();
             $productDetails  = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` =  '$pd_id'")->row_array();
            
            
            $share_url = "https://medicalwale.com/healthmall/#/product/".$pd_id;
           
            $sharePdName = $this->slugify($productDetails['pd_name']);
            $share_url = "https://medicalwale.com/healthmall/product_details/".$sharePdName."/".$pd_id;
           /* echo $share_url; die();*/
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
                
                $product_name ="";
                $images1 = "";
                $pd_mrp_price ="";
                $vendor_name ="";
                $product_desc= "";
                 $items = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_name,pd.pd_photo_1,pd.pd_mrp_price,pd.pd_vendor_price,pd.pd_long_desc, vd.v_id,vd.v_name, vd.v_delivery_charge FROM product_details_hm pd LEFT JOIN vendor_details_hm vd ON pd.pd_added_v_id = vd.v_id WHERE pd.pd_id = '$pd_id' AND pd.pd_status = '1'");
    	         if($items->num_rows()>0)
    	         {
    	             $rec = $items->row_array();
    	             $product_name = $rec['pd_name'];
    	             $images1 = $rec['pd_photo_1'];
    	             $pd_mrp_price = $rec['pd_mrp_price'];
    	             $vendor_name= $rec['v_name'];
    	             $product_desc= $rec['pd_long_desc'];
    	             
    	         }
    	         
                 if($count == 0) {
                    $insertRow  = $this->db->query("INSERT INTO `refer_product_hm` (`user_id`, `pd_id`, `code`, `created_date`, `expiry`, `status`) VALUES ('$user_id', '$pd_id', '$code', '$created_date', '$expiry', '$status')");
                
                 $message = "success";
                
                    $result = array(
                         "status"=> 200,
                         "message"=> $message,
                         "code" => $code,
                         "expiry" => $expiry,
                         "share_url" => $share_url,
                         "product_name" =>$product_name,
    	                 "procuct_image" => $images1,
    	                 "pd_mrp_price" => $pd_mrp_price,
    	                 "vendor_name" => !empty($vendor_name) ? "$vendor_name" : "",
    	                 "product_description" => $product_desc
                         
                    );
                
                   
                } else {
                    $message = "Could not create the code. Existing code will expire on  $rowExp";
                    
                    $result = array(
                         "status"=> 400,
                         "message"=> $message,
                         "code" => $rowCode,
                         "expiry" => $rowExp,
                         "share_url" => $share_url,
                         "product_name" =>$product_name,
    	                 "procuct_image" => $images1,
    	                 "pd_mrp_price" => $pd_mrp_price,
    	                 "vendor_name" => !empty($vendor_name) ? "$vendor_name" : ""
                         
                    );
                    
                }
            } else {
                $insertRow  = $this->db->query("INSERT INTO `refer_product_hm` (`user_id`, `pd_id`, `code`, `created_date`, `expiry`, `status`) VALUES ('$user_id', '$pd_id', '$code', '$created_date', '$expiry', '$status')");
                $items = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_name,pd.pd_photo_1,pd.pd_mrp_price,pd.pd_vendor_price,pd.pd_long_desc, vd.v_id,vd.v_name, vd.v_delivery_charge FROM product_details_hm pd LEFT JOIN vendor_details_hm vd ON pd.pd_added_v_id = vd.v_id WHERE pd.pd_id = '$pd_id' AND pd.pd_status = '1'");
                
                if($items->num_rows()>0)
    	         {
    	             $rec = $items->row_array();
    	             $product_name = $rec['pd_name'];
    	             $images1 = $rec['pd_photo_1'];
    	             $pd_mrp_price = $rec['pd_mrp_price'];
    	             $vendor_name= $rec['v_name'];
    	             $product_desc= $rec['pd_long_desc'];
    	             
    	         }
                
                $message = "success";
                
                $result = array(
                         "status"=> 200,
                         "message"=> $message,
                         "code" => $code,
                         "expiry" => $expiry,
                         "share_url" => $share_url,
                         "product_name" =>!empty($product_name) ? "$product_name" : "" , 
    	                 "procuct_image" => !empty($images1) ? "$images1" : "" , 
    	                 "pd_mrp_price" => !empty($pd_mrp_price) ? "$pd_mrp_price" : "" , 
    	                 "vendor_name" => !empty($vendor_name) ? "$vendor_name" : ""
                         
                         
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
            $today = date("Y-m-d");
            $nonExpRows = array();
            $codeQuery = $this->db->query("SELECT * FROM `vendor_offers` WHERE `name` = '$physical_code' AND `status` = '0' AND `listing_id` = '34' AND end_date >= '$today' ");
            $count = $codeQuery->num_rows();
            $codeRows = $codeQuery->result_array();
            $today = date("Y-m-d");
            //  print_r($codeRows); die();
             
            if($count > 0){
                
                
               
                
                // check expiry
                $expCount = 0;
                foreach($codeRows as $codeRow){
                    $exp = $codeRow['end_date'];
                    if($today <= $exp){
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
        
        
        // all_promotional_ads
        public function all_promotional_ads($user_id){
            $this->load->model('MedicalMall_model');
            $result = $data = array();
            $oldName = "";
            $today = $created_date = date("Y-m-d");
            
            $prmotionals = $this->db->query("SELECT * FROM `vendor_offers` WHERE `listing_id` = 34 AND end_date >= '$today' AND `promotional_ad` > 0 ORDER BY name ASC")->result_array();
            foreach($prmotionals as $p){
                $name = $p['name'];
                if($oldName != $name){
                    $r['offer_id'] = $p['id'];
                    $r['image'] = $p['offer_image'];
                    $result[] = $r;
                }
                $oldName = $p['name'];
            }
            //  print_r($result);
            // die();
            $data = $result;
            return $data;
        }
        
        public function view_promotional_ads($user_id,$offer_id){
            
           $offerOtherId = $offer_description = $name = $offer_image = "";
             $this->load->model('MedicalMall_model');
             $sliderother1 = $sliderother2 = $sliderother3 = $slidersOther = $allProducts = $resultOther = $productsOther = $prmotionalsOthers = $products = $brands = $vendor_ids = $slider1 = $slider2 = $slider3 = $result =  $data = array();
            $today = $created_date = date("Y-m-d");
            
            $prmotionals = $this->db->query("SELECT * FROM `vendor_offers` WHERE `listing_id` = 34 AND end_date >= '$today' AND `promotional_ad` > 0 AND `id` = '$offer_id'")->result_array();
            
            
    
            foreach($prmotionals as $p){
                // offer_on
                $offer_id = $p['id'];
                // print_r($p); die();
                $offer_image = $p['offer_image'];
                $name = $p['name'];
                $id = $p['id'];
                $offer_description = $p['offer_description'];
                
                $sliders = $this->db->query("SELECT * FROM `promotional_ads_images` WHERE `offer_id` = '$id'")->result_array();
                
                foreach($sliders as $s){
                    // print_r($s); die();
                    $image_url['image_url'] = $s['image_url'];
                    if($s['slider_no'] == 1){
                        $slider1[] = $image_url;
                    } else if($s['slider_no'] == 2){
                        $slider2[] = $image_url;
                    } else {
                        $slider3[] = $image_url;
                    }
                    
                }
                $result = $this->MedicalMall_model->get_offer_products($offer_id);
                
                $products = $result['products'];
                
    	        foreach($products as $pr){
                    // echo $res['vendor_id']; 
                    $vendor_ids[] = $pr['pd_added_v_id'];
                
                }
                
            }
            $prmotionalsOthers = $this->db->query("SELECT * FROM `vendor_offers` WHERE `listing_id` = 34 AND end_date >= '$today' AND `promotional_ad` > 0 AND `id` != '$offer_id' AND `name` = '$name'")->result_array();
            
            foreach($prmotionalsOthers as $p){
                $offerOtherId = $p['id'];
                $slidersOther = $this->db->query("SELECT * FROM `promotional_ads_images` WHERE `offer_id` = '$offerOtherId'")->result_array();
                
                foreach($slidersOther as $s){
                    // print_r($s); die();
                    $image_url['image_url'] = $s['image_url'];
                    if($s['slider_no'] == 1) {
                        $sliderother1[] = $image_url;
                    } else if($s['slider_no'] == 2) {
                        $sliderother2[] = $image_url;
                    } else {
                        $sliderother3[] = $image_url;
                    }
                    
                }
                
                $resultOther = $this->MedicalMall_model->get_offer_products($offerOtherId);
                
                $productsOther = $resultOther['products'];
                
    	        foreach($productsOther as $pr){
                    // echo $res['vendor_id']; 
                    $vendor_ids[] = $pr['pd_added_v_id'];
                
                }
                
            }
            
            
            $vendor_id = array_unique($vendor_ids);
            // print_r($products); die();
            $allProducts = array_merge($products, $productsOther);
            
            $allSlider1 = array_merge($slider1, $sliderother1);
            $allSlider2 = array_merge($slider2, $sliderother2);
            $allSlider3 = array_merge($slider3, $sliderother3);
            
            
            $allIds = implode(",",$vendor_id);
            if($allIds != ""){
                $brands = $this->db->query("SELECT `v_id` , `v_company_logo`,`v_name`, `v_company_name`  FROM `vendor_details_hm` WHERE `v_id` IN ($allIds) AND `v_status` = 1")->result_array();
            }
            $data['banner_image'] = $offer_image;
            $data['coupon_code'] = $name;
            $data['offer_description'] = $offer_description;
            $data['slider1'] = $allSlider1;
            $data['slider2'] = $allSlider2;
            $data['slider3'] = $allSlider3;
            $data['products'] = $allProducts;
            $data['brands'] = $brands;
            
            
            return $data;
        }
      
     }
     ?>
