<?php
 class MedicalMall_model extends CI_Model {
       
    public function __construct(){
          
        
        $this->load->database();
        
      
      }
      
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";

    /*public function check_auth_client() {
        $auth_key = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            $resp = array(
                "status" => "Unauthorized",
                "statuspic_root_code" => "401",
                );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }
    }*/
    
  /*  public function auth() {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token = $this->input->get_request_header('Authorizations', TRUE);
        $q = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            $resp = array(
                "status" => "Unauthorized",
                "statuspic_root_code" => "401",
                );
            return $this->output->set_content_type('Content-Type: application/json')->set_output(json_encode($resp));
        }  else {
            
             if ($q->expired_at < date("Y-m-d H:i:s", strtotime("-1 days"))) {
                // return json_output(401, array(
                //     'status' => 401,
                //     'message' => 'Your session has been expired.'
                // ));
                return  array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                );
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = date("Y-m-d H:i:s", strtotime("+1 days"));//'2020-12-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                //echo $this->db->last_query(); die();
                return 200;
            }
        }
    }*/
    
    //start of me
    public function get_num_ratings($pro_id){
        
        $rat5 = $this->db->query("SELECT count(*) as rating5 FROM product_reviews WHERE pr_rating = 5 AND pr_pd_id = $pro_id")->result_array();
        $rat4 = $this->db->query("SELECT count(*) as rating4 FROM product_reviews WHERE pr_rating = 4 AND pr_pd_id = $pro_id")->result_array();
        $rat3 = $this->db->query("SELECT count(*) as rating3 FROM product_reviews WHERE pr_rating = 3 AND pr_pd_id = $pro_id")->result_array();
        $rat2 = $this->db->query("SELECT count(*) as rating2 FROM product_reviews WHERE pr_rating = 2 AND pr_pd_id = $pro_id")->result_array();
        $rat1 = $this->db->query("SELECT count(*) as rating1 FROM product_reviews WHERE pr_rating = 1 AND pr_pd_id = $pro_id")->result_array();
        $totalCount = $rat1[0]['rating1'] + $rat2[0]['rating2'] + $rat3[0]['rating3'] + $rat4[0]['rating4'] + $rat5[0]['rating5'];
        $data[] = array(
                "product_id" => $pro_id,
                "count" => $totalCount,
                "rating1" => $rat1[0]['rating1'],
                "rating2" => $rat2[0]['rating2'],
                "rating3" => $rat3[0]['rating3'],
                "rating4" => $rat4[0]['rating4'],
                "rating5" => $rat5[0]['rating5']
            ); 
        
        return $data;
    }
    
    public function get_similar_items($pro_id){
        $result = $this->db->query("SELECT pd_pc_id FROM product_details_hm WHERE pd_id = '$pro_id' and pd_status = '1'")->result_array();
        $pro_cat_id = $result[0]['pd_pc_id'];
       // $items = $this->db->query("SELECT pd_id,pd_pc_id,pd_name,pd_photo_1,pd_mrp_price FROM product_details_hm WHERE pd_pc_id = '$pro_cat_id' AND pd_id <> '$pro_id' AND pd_status = '1' ORDER BY rand() LIMIT 10");
         $items = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_name,pd.pd_photo_1,pd.pd_mrp_price,pd.pd_vendor_price, vd.v_id,vd.v_name, vd.v_delivery_charge FROM product_details_hm pd LEFT JOIN vendor_details_hm vd ON pd.pd_added_v_id = vd.v_id WHERE pd.pd_pc_id = '$pro_cat_id' AND pd_id <> '$pro_id' AND pd.pd_status = '1' ORDER BY rand() LIMIT 10");
        $res = array(
            "status" => "true",
            "statuspic_root_code" => "200",
            "data" => $items->result_array()
            );
        return $res;
    }
    
    public function update_cart($user_id, $cart_list){
        $final_cart_list = $new_cart_list = array();
        $cart = json_decode($cart_list,TRUE);
        $count = count($cart['list']);
        for($i=0;$i<$count;$i++){
            $p_id = $cart['list'][$i]['product_id'];
            $p_qty = $cart['list'][$i]['quantity'];
            $off_id = $cart['list'][$i]['offer_id'];
            if(!empty($cart['list'][$i]['refferal_code'])){
                $refferal_code = $cart['list'][$i]['refferal_code'];   
            } else {
                $refferal_code = 0;
            }
            
            if(!empty($cart['list'][$i]['variable_pd_id'])){
                $variable_pd_id = $cart['list'][$i]['variable_pd_id'];   
            } else {
                $variable_pd_id = 0;
            }
            
            
            if(!empty($cart['list'][$i]['sku'])){
                $sku = $cart['list'][$i]['sku'];   
            } else {
                $sku = 0;
            }
            
            
            
            // echo $refferal_code ; die();
        // $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `customer_id` = '$user_id'");
        // $count = $checkAvailable->num_rows();
        // if($count != 0){
            $this->db->query("DELETE FROM `user_cart` WHERE `product_id` = '$p_id' AND `variable_pd_id` = '$variable_pd_id' AND `customer_id` = '$user_id'");
            $results = $this->db->query("INSERT INTO `user_cart` (`product_id`, `customer_id`, `quantity`, `offer_id`,`referal_code`,`variable_pd_id`,`sku`) VALUES ('$p_id', '$user_id', '$p_qty', '$off_id','$refferal_code','$variable_pd_id','$sku')");
            $insert_id = $this->db->insert_id();
            
            
            if($insert_id){
                
                $new_cart_list['product_id'] = $p_id;
                $new_cart_list['customer_id'] = $user_id;
                $new_cart_list['quantity'] = $p_qty;
                $new_cart_list['offer_id'] = $off_id;
                $new_cart_list['refferal_code'] = $refferal_code;
                $new_cart_list['variable_pd_id'] = $variable_pd_id;
                $new_cart_list['sku'] = $sku;
                
                $final_cart_list[] = $new_cart_list;
                $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to Stack",
                    // "data" => json_decode($cart_list)->list
                    "data" => $final_cart_list
                );
            } else {
                $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong, please try again",
                );
                break;
            }
            
        //} 
        }
        return $res;
    }
    //end of me
      
    public function getallAdvertisements(){
        $data = array();
        $stmt = "SELECT * from advertisement_hm where avd_status=1 AND imgfor='M'";
    
        $query = $this->db->query($stmt)->result_array();

        foreach($query as $q){
            foreach($q as $key => $value){
                if($value == null){
                    $q[$key] = "";
                }
            }
            $data[] = $q;
        }
            
        return $data;
    
    
    }
    
    public function getallAdvertisementsWebsite(){
    
        $stmt = "SELECT * from advertisement_hm where avd_status=1 AND imgfor='W'";
    
        $query = $this->db->query($stmt);

        return $query;
    
    
    }
    public function getallVendors($data){
    
        $stmt = "SELECT * FROM vendor_details_hm WHERE v_id = '$data'";
    
        $query = $this->db->query($stmt);

        return $query;
    
    
    }
    public function getallProductsForVendor($v_id){
    
        $stmt = "SELECT d.pd_id, d.pd_name,d.pd_photo_1,d.pd_mrp_price,pd_vendor_price,d.pd_short_desc, avg(r.pr_rating) as avg,COUNT(r.pr_rating) as total FROM `product_details_hm` d LEFT JOIN product_reviews r ON d.pd_id = r.pr_pd_id where d.pd_added_v_id = ".$v_id." AND d.pd_status = '1' GROUP BY d.pd_id";
        $query = $this->db->query($stmt);
        return $query;
    
    }

        public function getProductForVendor1($pd_id,$user_id){ 
          $offers = array();
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
        
        /*$recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
        $recentlyViewedRows = $recentlyViewed->row_array();
        $recentlyViewedCount = $recentlyViewed->num_rows();
        $viewed_date = date('Y-m-d H:i:s');
        if($recentlyViewedCount > 0){
            $recentId = $recentlyViewedRows['id'];
            $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
        } else {
            $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `viewed_date`) VALUES ('$user_id', '$pd_id', '$viewed_date')");
        }*/
        
       // print_r($offers);
       //  echo count($afterstr);
//      die(); 


        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_company_logo,vd.v_delivery_charge,d.item_type,d.total_units,d.directions,d.ingredients,d.gender_type, d.origin_country   ,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_added_v_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.legal_disclaimer,d.pd_vendor_price,d.safety_warnings,d.is_container_glass,d.speciality,d.is_liquid,d.indications,d.servings,d.variable_product,vd.cap_available,vd.cap_charge,d.pd_quantity,d.pd_long_desc,avg(r.pr_rating) as ratting,COUNT(r.pr_rating) as total_rating,d.pd_view_count as total_view FROM `product_details_hm` d 
        LEFT JOIN product_reviews r ON d.pd_id = r.pr_pd_id 
        LEFT join vendor_details_hm vd 
        on vd.v_id=d.pd_added_v_id      
        
        where d.pd_status = '1' AND d.pd_id = ".$pd_id."";
        
        
    
        $query = $this->db->query($stmt);
//      $finalArray = array();
//      if($query->result_array()[0]['pd_vendor_price'] == 0){
//          return $finalArray;  
//      }
  
        foreach($query->result_array() as $query1 ){
//       print_r($query1); die();
         
         
         // print_r($query1); die();  



            $isVariable = $query1['variable_product'];
            $prod_mrp = $query1['pd_mrp_price'];
            $pd_vendor_price = $query1['pd_vendor_price'];
            $vendor_id = $query1['pd_added_v_id'];
             $pd_psc_id=$query1['pd_psc_id'];
            $pd_pc_id=$query1['pd_pc_id'];
            $pd_name=$query1['pd_name'];
            
            $recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
            $recentlyViewedRows = $recentlyViewed->row_array();
            $recentlyViewedCount = $recentlyViewed->num_rows();
            $viewed_date = date('Y-m-d H:i:s');
            if($recentlyViewedCount > 0){
                $recentId = $recentlyViewedRows['id'];
                $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
            } else {
                $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `viewed_date`,`pd_pc_id`,`pd_psc_id`,`pd_added_v_id`,`brand_name`,`pd_name`) VALUES ('$user_id', '$pd_id', '$viewed_date','$pd_pc_id','$pd_psc_id','$vendor_id','$v_name','$pd_name')");
            }
        } 
        
  
        
        $offersProd = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND vendor_id = '$vendor_id' AND end_date >= '$today'");
           
        foreach ($offersProd->result_array() as $offer){
            
            $offersProd = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` = '$pd_id' and pd_status = 1");
           
            $pd_Info = $offersProd->result_array();
            foreach($pd_Info as $cats ){
                 $catIds = $cats['pd_pc_id'];
                $subCatIds = $cats['pd_psc_id'];
            } 
           
         
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
                                
                                // echo $dis;
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                 $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                $offer['offer_best_price'] = $prod_best_price;
                            } else {
                                
                                $dis =   $prod_mrp * ( $offer['price'] / 100 ) ;
                                // echo $dis;
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
                            $offer['offer_best_price'] = "0";
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
                                
                            $dis =   $prod_mrp * ( $offer['price'] / 100 ) ;
                                
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
                            $offer['offer_best_price'] = "0";
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
                                
                                if($offer['max_discound']!= 0  && $dis > $offer['max_discound']){
                                    $prod_best_price = $offer['max_discound'];
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                
                                $offer['offer_best_price'] = $prod_best_price;
                               
                            }
                        } else {
                            $offer['message'] = "Minimum amount must be $min_amount rupees"  ;
                            $offer['offer_best_price'] = "0";
                        }
                           
                        
                    
                    $off = $offer;
                    $offers[] = $off;
                }
                }
                
            }
            
        }
        
        $query_for_count="SELECT pd_view_count from product_details_hm where pd_id=".$pd_id."";
        $view_count = $this->db->query($query_for_count)->num_rows();
        $view_data = $this->db->query($query_for_count)->result_array();
        $count=$view_data[0]['pd_view_count']+1;
        $update_query="UPDATE product_details_hm set pd_view_count=".$count." where pd_id=".$pd_id."";
        $updatedata = $this->db->query($update_query);
        // $query = $query->result_array();
       
       
       //       added to wishlist or not
        
            if($user_id != ""){
                $wishlist = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$pd_id' AND `customer_id` = '$user_id'")->result_array();
                if(!empty($wishlist)){
                    $wishlist = 1;
                } else {
                    $wishlist = 0;
                }
                
            }else{
                $wishlist = 0;
            }
  
        foreach($query->result_array() as $query ){
            $query['offers'] = $offers;
        }    
        $query['wishlist'] = $wishlist;
        $query['allowed_sharing'] = 0;
         $codeExists  = $this->db->query("SELECT * FROM `refer_product_hm` WHERE user_id = '$user_id' AND pd_id = '$pd_id'");
        $today = date("Y-m-d  H:i:s");
        
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
                $sharingAlloed  = 1;
            } else {
                 $sharingAlloed  = 0;
            }
        } else {
            $sharingAlloed  = 1;
            
        }
        
        $query['allowed_sharing'] = $sharingAlloed;
        
        $videos = $video = $photos = $photos = $finalMedia = array();
        
        $medias = $this->db->query("SELECT * FROM `product_media_hm` WHERE `pd_id` = '$pd_id' AND `status` = 1")->result_array();
        foreach($medias as $media){
            if($media['photo_or_video'] == 1){
                $photo['photo'] =  $media['url'];
                $photo['description'] =  $media['description'];
                $photos[] = $photo;
            } else {
                $video['video'] =  $media['url'];
                $video['thumbnail_image'] =  $media['thumbnail_image'];
                $video['description'] =  $media['description'];
                $videos[] =  $video;
            }
           
        }
  
  
        $variable_products = (object)[];
        
        if($isVariable == 1){
             $products = $variable_products = $colorQ = $sizeQ = $variable_colors = $variable_sizes = array();
       
            // $vQuery  = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `pd_id` = '$pd_id'");
            $vQuery  = $this->db->query("SELECT `id`as variable_pd_id, `pd_id`, `sku`, `color`, `size`, `quantity`, `price`, `vendor_price`, `image` FROM `variable_products_hm` WHERE `pd_id` = '$pd_id'");
            
            foreach($vQuery->result_array() as $v){
                $c = $v['color'];
                $s = $v['size'];
                $vp_id = $v['variable_pd_id'];
                
                if($c != null && $c != ''){
                    $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                
                    foreach($colorQ->result_array() as $colorRows){
                        $colorValue['variale_pd_id'] = $vp_id;
                        $colorValue['color'] = $colorRows['color'];
                        $colorValue['color_code'] = $colorRows['color_code'];
                        $colorValue['color_image'] = $colorRows['color_image'];
                        
                        $variable_colors[] = $colorValue;
                    }    
                }
                if($s != null && $s != ''){
                    $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                    
                    foreach($sizeQ->result_array() as $sizeRows){
                        $sizeValue['variale_pd_id'] = $vp_id;
                        $sizeValue['size_name'] = $sizeRows['size_name'];
                       
                        $variable_sizes[] = $sizeValue;
                    }
                }
                $products[] = $v;
                
            }
        //    print_r($variable_sizes);
        //    print_r($variable_colors); die();
            
            $variable_products['variable_sizes'] = $variable_sizes;
            $variable_products['variable_colors']= $variable_colors;
            $variable_products['products']= $products;
            
            
        }
        $finalMedia['photos'] = $photos; 
        $finalMedia['videos'] = $videos; 
      //    print_r($finalMedia); die();
        $query['media'] = $finalMedia;
        
        $query['variable_products'] = $variable_products;
            // $query[] = $query;
            $finalArray[] = $query;
    //    print_r($finalArray[0]); die();   
       
       foreach($finalArray[0] as $key => $value) {
            
            if (is_null($value)) {
              // print_r($key); die();
                 $finalArray[0][$key] = "";
              
            }
        }
           // print_r($finalArray); die();
            return $finalArray;

    }
    
        public function getProductForVendor2($pd_id,$user_id){ 
          $products = $offers = array();
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
        
       /* $recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
        $recentlyViewedRows = $recentlyViewed->row_array();
        $recentlyViewedCount = $recentlyViewed->num_rows();
        $viewed_date = date('Y-m-d H:i:s');
        if($recentlyViewedCount > 0){
            $recentId = $recentlyViewedRows['id'];
            $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
        } else {
            $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `viewed_date`) VALUES ('$user_id', '$pd_id', '$viewed_date')");
        }*/
        
       // print_r($offers);
       //  echo count($afterstr);
//      die(); 


        $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_company_logo,vd.v_delivery_charge,d.item_type,d.total_units,d.directions,d.ingredients,d.gender_type, d.origin_country   ,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_added_v_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.legal_disclaimer,d.pd_vendor_price,d.safety_warnings,d.is_container_glass,d.speciality,d.is_liquid,d.indications,d.servings,d.variable_product,vd.cap_available,vd.cap_charge,d.pd_quantity,d.pd_long_desc,avg(r.pr_rating) as ratting,COUNT(r.pr_rating) as total_rating,d.pd_view_count as total_view FROM `product_details_hm` d 
        LEFT JOIN product_reviews r ON d.pd_id = r.pr_pd_id 
        LEFT join vendor_details_hm vd 
        on vd.v_id=d.pd_added_v_id      
        
        where d.pd_status = '1' AND d.pd_id = ".$pd_id."";
        
        
    
        $query = $this->db->query($stmt);
//      $finalArray = array();
//      if($query->result_array()[0]['pd_vendor_price'] == 0){
//          return $finalArray;  
//      }
  
        foreach($query->result_array() as $query1 ){
//       print_r($query1); die();
         
         
         // print_r($query1); die();  



            $isVariable = $query1['variable_product'];
            $prod_mrp = $query1['pd_mrp_price'];
            $pd_vendor_price = $query1['pd_vendor_price'];
            $vendor_id = $query1['pd_added_v_id'];
             $pd_psc_id=$query1['pd_psc_id'];
            $pd_pc_id=$query1['pd_pc_id'];
            $pd_name=$query1['pd_name'];
            
            $recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
            $recentlyViewedRows = $recentlyViewed->row_array();
            $recentlyViewedCount = $recentlyViewed->num_rows();
            $viewed_date = date('Y-m-d H:i:s');
            if($recentlyViewedCount > 0){
                $recentId = $recentlyViewedRows['id'];
                $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
            } else {
                $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `viewed_date`,`pd_pc_id`,`pd_psc_id`,`pd_added_v_id`,`brand_name`,`pd_name`) VALUES ('$user_id', '$pd_id', '$viewed_date','$pd_pc_id','$pd_psc_id','$vendor_id','$v_name','$pd_name')");
            }
        } 
        
  
        
        $offersProd = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND vendor_id = '$vendor_id' AND end_date >= '$today'");
           
        foreach ($offersProd->result_array() as $offer){
            
            $offersProd = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` = '$pd_id'");
           
            $pd_Info = $offersProd->result_array();
            foreach($pd_Info as $cats ){
                 $catIds = $cats['pd_pc_id'];
                $subCatIds = $cats['pd_psc_id'];
            } 
           
         
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
                                
                                // echo $dis;
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                 $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                $offer['offer_best_price'] = $prod_best_price;
                            } else {
                                
                                $dis =   $prod_mrp * ( $offer['price'] / 100 ) ;
                                // echo $dis;
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
                            $offer['offer_best_price'] = "0";
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
                                
                            $dis =   $prod_mrp * ( $offer['price'] / 100 ) ;
                                
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
                            $offer['offer_best_price'] = "0";
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
                                
                                if($offer['max_discound']!= 0  && $dis > $offer['max_discound']){
                                    $prod_best_price = $offer['max_discound'];
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                
                                $offer['offer_best_price'] = $prod_best_price;
                               
                            }
                        } else {
                            $offer['message'] = "Minimum amount must be $min_amount rupees"  ;
                            $offer['offer_best_price'] = "0";
                        }
                           
                        
                    
                    $off = $offer;
                    $offers[] = $off;
                }
                }
                
            }
            
        }
        
        $query_for_count="SELECT pd_view_count from product_details_hm where pd_id=".$pd_id."";
        $view_count = $this->db->query($query_for_count)->num_rows();
        $view_data = $this->db->query($query_for_count)->result_array();
        $count=$view_data[0]['pd_view_count']+1;
        $update_query="UPDATE product_details_hm set pd_view_count=".$count." where pd_id=".$pd_id."";
        $updatedata = $this->db->query($update_query);
        // $query = $query->result_array();
       
       
       //       added to wishlist or not
        
            if($user_id != ""){
                $wishlist = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$pd_id' AND `customer_id` = '$user_id'")->result_array();
                if(!empty($wishlist)){
                    $wishlist = 1;
                } else {
                    $wishlist = 0;
                }
                
            }else{
                $wishlist = 0;
            }
  
        foreach($query->result_array() as $query ){
            $query['offers'] = $offers;
        }    
        $query['wishlist'] = $wishlist;
        $query['allowed_sharing'] = 0;
         $codeExists  = $this->db->query("SELECT * FROM `refer_product_hm` WHERE user_id = '$user_id' AND pd_id = '$pd_id'");
        $today = date("Y-m-d  H:i:s");
        
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
                $sharingAlloed  = 1;
            } else {
                 $sharingAlloed  = 0;
            }
        } else {
            $sharingAlloed  = 1;
            
        }
        
        $query['allowed_sharing'] = $sharingAlloed;
        
        $videos = $video = $photos = $photos = $finalMedia = array();
        
        $medias = $this->db->query("SELECT * FROM `product_media_hm` WHERE `pd_id` = '$pd_id' AND `status` = 1")->result_array();
        foreach($medias as $media){
            if($media['photo_or_video'] == 1){
                $photo['photo'] =  $media['url'];
                $photo['description'] =  $media['description'];
                $photos[] = $photo;
            } else {
                $video['video'] =  $media['url'];
                $video['thumbnail_image'] =  $media['thumbnail_image'];
                $video['description'] =  $media['description'];
                $videos[] =  $video;
            }
           
        }
  
  
        $variable_products = (object)[];
        
        if($isVariable == 1){
            $oldSize = "";
            $variables = $this->db->query("SELECT vp.`id`as variable_pd_id, vp.`pd_id`, vp.`sku`, vp.`color`, vp.`size`, vp.`quantity`, vp.`price`, vp.`vendor_price`, vp.`image`, s.size_area, s.`size_measure_cm`, s.`size_measure_inch`, s.`size_name`, c.color, c.color_code, c.color_image FROM `variable_products_hm` as vp LEFT JOIN `size_chart_hm` as s ON (s.id = vp.`size`) LEFT JOIN `color_hm` as c ON (c.id = vp.`color`) WHERE vp.`pd_id` = '$pd_id' ORDER BY s.size_name ASC")->result_array();
            
           
            $i=0;
           foreach($variables as $v){
                
                if($i != 0){
                    if($oldSize == $v['size_name']){
                        $c = array();
                        $c['color_code']=$v['color_code'];
                        $c['color_image']=$v['color_image'];
                        $c['image']=$v['image'];
                        $c['prod_info'][] = $v;
                        $prod['color_info'][] = $c;
                        
                    }else{
                        
                        $products[] = $prod;
                        $c = $prod = array();
                        $prod['size_name'] = $v['size_name'];
                        $c['color_code']=$v['color_code'];
                        $c['color_image']=$v['color_image'];
                        $c['image']=$v['image'];
                        $c['prod_info'][] = $v;
                        $prod['color_info'][] = $c;
                        
                       // $prod['prod_info'][] = $v;
                    }
                } else {
                    $prod['size_name'] = $v['size_name'];
                    $c['color_code']=$v['color_code'];
                    $c['color_image']=$v['color_image'];
                    $c['image']=$v['image'];
                    $c['prod_info'][] = $v;
                    $prod['color_info'][] = $c;
                }
                    
               
                $oldSize = $v['size_name'];
                $i++;    
            }
            $products[] = $prod;
            // print_r($products);
            // die();
             
            //$variable_products['variable_sizes'] = $variable_sizes;
            //$variable_products['variable_colors']= $variable_colors;
            //$variable_products['products']= $products;
            $variable_products = $products;
            
        }
        $finalMedia['photos'] = $photos; 
        $finalMedia['videos'] = $videos; 
      //    print_r($finalMedia); die();
        $query['media'] = $finalMedia;
        
        $query['variable_products'] = $products;
            // $query[] = $query;
            $finalArray[] = $query;
    //    print_r($finalArray[0]); die();   
       
       foreach($finalArray[0] as $key => $value) {
            
            if (is_null($value)) {
              // print_r($key); die();
                 $finalArray[0][$key] = "";
              
            }
        }
           // print_r($finalArray); die();
            return $finalArray;

    }
    
        public function getProductForVendor($pd_id,$user_id){ 
        $specifications =  $products = $offers = array();
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
        
        /*$recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
        $recentlyViewedRows = $recentlyViewed->row_array();
        $recentlyViewedCount = $recentlyViewed->num_rows();
        $viewed_date = date('Y-m-d H:i:s');
        if($recentlyViewedCount > 0){
            $recentId = $recentlyViewedRows['id'];
            $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
        } else {
            $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `viewed_date`) VALUES ('$user_id', '$pd_id', '$viewed_date')");
        }*/

        //$stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_company_logo,vd.v_delivery_charge,d.item_type,d.total_units,d.directions,d.ingredients,d.gender_type, d.origin_country   ,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_added_v_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.legal_disclaimer,d.pd_vendor_price,d.safety_warnings,d.is_container_glass,d.speciality,d.is_liquid,d.indications,d.servings,d.variable_product,vd.cap_available,vd.cap_charge,d.pd_quantity,d.pd_long_desc,avg(r.pr_rating) as ratting,COUNT(r.pr_rating) as total_rating,d.pd_view_count as total_view FROM `product_details_hm` d 
        $stmt = "SELECT d.*,vd.v_id,vd.v_name,vd.v_company_logo,vd.v_delivery_charge,vd.cap_available,vd.cap_charge,avg(r.pr_rating) as ratting,COUNT(r.pr_rating) as total_rating,d.pd_view_count as total_view FROM `product_details_hm` d 
        LEFT JOIN product_reviews r ON d.pd_id = r.pr_pd_id 
        LEFT join vendor_details_hm vd 
        on vd.v_id=d.pd_added_v_id      
        
        where d.pd_status = '1' AND d.pd_id = ".$pd_id."";
        
        
    
        $query = $this->db->query($stmt);
        foreach($query->result_array() as $query1 ){
          //  print_r($query1); die();
            $s = array();
            $isVariable = $query1['variable_product'];
            $prod_mrp = $query1['pd_mrp_price'];
            $pd_vendor_price = $query1['pd_vendor_price'];
            $vendor_id = $query1['pd_added_v_id'];
             $pd_psc_id=$query1['pd_psc_id'];
            $pd_pc_id=$query1['pd_pc_id'];
            $pd_name=$query1['pd_name'];
            $v_name = $query1['v_name'];
            $recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
            $recentlyViewedRows = $recentlyViewed->row_array();
            $recentlyViewedCount = $recentlyViewed->num_rows();
            $viewed_date = date('Y-m-d H:i:s');
            if($recentlyViewedCount > 0){
                $recentId = $recentlyViewedRows['id'];
                $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
            } else {
                $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `viewed_date`,`pd_pc_id`,`pd_psc_id`,`pd_added_v_id`,`brand_name`,`pd_name`) VALUES ('$user_id', '$pd_id', '$viewed_date','$pd_pc_id','$pd_psc_id','$vendor_id','$v_name','$pd_name')");
            }
            
            //   $pd_long_desc1           =                $query1['pd_long_desc'];
            //   $directions1          =                   $query1['directions'];
            //   $legal_disclaimer1   =                    $query1['legal_disclaimer'];
            //   $safety_warnings1    =                    $query1['safety_warnings'];
            //   $indications1            =                $query1['indications'];
            //   $item_type1           =                   $query1['item_type'];
            //   $total_units1            =                $query1['total_units'];
            //   $servings1            =                   $query1['servings'];
            //   $tablet_weight1          =                $query1['tablet_weight'];
            //   $dim_length1          =                   $query1['dim_length'];
            //   $dim_width1           =                   $query1['dim_width'];
            //   $dim_height1         =                    $query1['dim_height'];
            //   $product_weight1     =                    $query1['product_weight'];
            //   $shipping_weight1    =                    $query1['shipping_weight'];
            //   $ingredients1            =                $query1['ingredients'];
            //   $flavor1                  =                      $query1['flavor'];
            //   $gender_type1             =                    $query1['gender_type'];
            //   $origin_country1         =                $query1['origin_country'];
            //   $require_temp1               =                   $query1['require_temp'];
            //   $max_temp1                   =                   $query1['max_temp'];
            //   $is_hazardous1               =                   $query1['is_hazardous'];
            //   $hazardous_type1             =                   $query1['hazardous_type'];
            //   $has_expiration1             =                   $query1['has_expiration'];
            //   $shelf_life1                  =                  $query1['shelf_life'];
            //   $require_discrete_package1  =                    $query1['require_discrete_package'];
            //   $package_type1              =                    $query1['package_type'];
            //   $is_container_glass1        =                    $query1['is_container_glass'];
            //   $is_liquid1                 =                    $query1['is_liquid'];
            //   $speciality1                =                    $query1['speciality'];

                     
                if(!empty($query1['directions'])){
                       $nv['name'] = "Directions";
                       $nv['value'] = $query1['directions'];
                       $s[] = $nv;
                } 
                if(!empty($query1['legal_disclaimer'])){
                       $nv['name'] = "Disclaimer";
                       $nv['value'] = $query1['legal_disclaimer'];
                       $s[] = $nv;
                }
                if(!empty($query1['safety_warnings'])){
                       $nv['name'] = "Safety warnings";
                       $nv['value'] = $query1['safety_warnings'];
                       $s[] = $nv;
                }      
                if(!empty($query1['indications'])){
                       $nv['name'] = "Indications";
                       $nv['value'] = $query1['indications'];
                       $s[] = $nv;
                } 
                if(!empty($query1['item_type'])){
                       $nv['name'] = "Item type";
                       $nv['value'] = $query1['item_type'];
                       $s[] = $nv;
                }
                if(!empty($query1['total_units'])){
                       $nv['name'] = "Units";
                       $nv['value'] = $query1['total_units'];
                       $s[] = $nv;
                }      
                if(!empty($query1['servings'])){
                       $nv['name'] = "Servings";
                       $nv['value'] = $query1['servings'];
                       $s[] = $nv;
                } 
                if(!empty($query1['tablet_weight'])){
                       $nv['name'] = "Tablet weight";
                       $nv['value'] = $query1['tablet_weight'] . " ml";
                       $s[] = $nv;
                }
                if(!empty($query1['dim_length'])){
                       $nv['name'] = "Length";
                       $nv['value'] = $query1['dim_length'] . " cm";
                       $s[] = $nv;
                }      
                if(!empty($query1['dim_width'])){
                       $nv['name'] = "Width";
                       $nv['value'] = $query1['dim_width'] . " cm";
                       $s[] = $nv;
                } 
                if(!empty($query1['dim_height'])){
                       $nv['name'] = "Height";
                       $nv['value'] = $query1['dim_height'] . " cm";
                       $s[] = $nv;
                }
                if(!empty($query1['product_weight'])){
                       $nv['name'] = "Weight";
                       $nv['value'] = $query1['product_weight'];
                       $s[] = $nv;
                }      
                if(!empty($query1['ingredients'])){
                       $nv['name'] = "Ingredients";
                       $nv['value'] = $query1['ingredients'];
                       $s[] = $nv;
                }
                if(!empty($query1['flavor'])){
                       $nv['name'] = "Flavor";
                       $nv['value'] = $query1['flavor'];
                       $s[] = $nv;
                }      
                if(!empty($query1['gender_type'])){
                       $nv['name'] = "Suitable for";
                       $nv['value'] = $query1['gender_type'];
                       $s[] = $nv;
                }
                
                if(!empty($query1['color'])){
                       $nv['name'] = "Color";
                       $nv['value'] = $query1['color'];
                       $s[] = $nv;
                }
                
                
                
                
                if(!empty($query1['origin_country'])){
                       $nv['name'] = "Country";
                       $nv['value'] = $query1['origin_country'];
                       $s[] = $nv;
                }      
                if(!empty($query1['require_temp'])){
                       $nv['name'] = "Require Temperature";
                       $nv['value'] = $query1['require_temp'] . " °C";
                       $s[] = $nv;
                }
                if(!empty($query1['max_temp'])){
                       $nv['name'] = "Max Temperature";
                       $nv['value'] = $query1['max_temp'] . " °C";
                       $s[] = $nv;
                }      
                if(!empty($query1['is_hazardous']) || !empty($query1['hazardous_type'])){
                       $nv['name'] = "hazardous";
                       $nv['value'] = $query1['is_hazardous'] ."".$query1['hazardous_type'];
                       $s[] = $nv;
                } 
                
                
                
                if(!empty($query1['has_expiration'])){
                       $nv['name'] = "Has expiration?";
                       $nv['value'] = $query1['has_expiration'];
                       $s[] = $nv;
                }
                if(!empty($query1['shelf_life'])){
                       $nv['name'] = "Shelf life";
                       $nv['value'] = $query1['shelf_life'] . " days";
                       $s[] = $nv;
                }      
                if(!empty($query1['require_discrete_package'])){
                       $nv['name'] = "Require discrete package";
                       $nv['value'] = $query1['require_discrete_package'];
                       $s[] = $nv;
                }      
                if(!empty($query1['is_container_glass'])){
                       $nv['name'] = "Contains glass?";
                       $nv['value'] = $query1['is_container_glass'];
                       $s[] = $nv;
                }
                
                
                if(!empty($query1['is_liquid'])){
                       $nv['name'] = "Is liquid?";
                       $nv['value'] = $query1['is_liquid'];
                       $s[] = $nv;
                }      
                if(!empty($query1['speciality'])){
                       $nv['name'] = "Speciality";
                       $nv['value'] = $query1['speciality'];
                       $s[] = $nv;
                }

            
            // print_r($query1); die();
            // print_r($s); die();
            
            $specifications = $s;
            
        } 
        
        
        $offersProd = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND vendor_id = '$vendor_id' AND `status` = 0 AND end_date >= '$today'");
           
        foreach ($offersProd->result_array() as $offer){
            
            $offersProd = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` = '$pd_id'");
           
            $pd_Info = $offersProd->result_array();
            foreach($pd_Info as $cats ){
                 $catIds = $cats['pd_pc_id'];
                $subCatIds = $cats['pd_psc_id'];
            } 
           
         
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
                                
                                // echo $dis;
                                $prod_best_price = $prod_mrp - $dis;
                                
                                if($prod_best_price < 0){
                                    $prod_best_price = 0;
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                 $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                $offer['offer_best_price'] = $prod_best_price;
                            } else {
                                
                                $dis =   $prod_mrp * ( $offer['price'] / 100 ) ;
                                // echo $dis;
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
                            $offer['offer_best_price'] = "0";
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
                                
                            $dis =   $prod_mrp * ( $offer['price'] / 100 ) ;
                                
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
                            $offer['offer_best_price'] = "0";
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
                                
                                if($offer['max_discound']!= 0  && $dis > $offer['max_discound']){
                                    $prod_best_price = $offer['max_discound'];
                                } else {
                                    $prod_best_price = $prod_best_price;
                                }
                                
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                
                                $offer['offer_best_price'] = $prod_best_price;
                               
                            }
                        } else {
                            $offer['message'] = "Minimum amount must be $min_amount rupees"  ;
                            $offer['offer_best_price'] = "0";
                        }
                           
                        
                    
                    $off = $offer;
                    $offers[] = $off;
                }
                }
                
            }
            
        }
        
        $query_for_count="SELECT pd_view_count from product_details_hm where pd_id=".$pd_id."";
        $view_count = $this->db->query($query_for_count)->num_rows();
        $view_data = $this->db->query($query_for_count)->result_array();
        $count=$view_data[0]['pd_view_count']+1;
        $update_query="UPDATE product_details_hm set pd_view_count=".$count." where pd_id=".$pd_id."";
        $updatedata = $this->db->query($update_query);
        // $query = $query->result_array();
       
       
       //       added to wishlist or not
        
            if($user_id != ""){
                $wishlist = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$pd_id' AND `customer_id` = '$user_id'")->result_array();
                if(!empty($wishlist)){
                    $wishlist = 1;
                } else {
                    $wishlist = 0;
                }
                
            }else{
                $wishlist = 0;
            }
  
        foreach($query->result_array() as $query ){
            $query['offers'] = $offers;
        }    
        $query['wishlist'] = $wishlist;
        $query['allowed_sharing'] = 0;
         $codeExists  = $this->db->query("SELECT * FROM `refer_product_hm` WHERE user_id = '$user_id' AND pd_id = '$pd_id'");
        $today = date("Y-m-d  H:i:s");
        
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
                $sharingAlloed  = 1;
            } else {
                 $sharingAlloed  = 0;
            }
        } else {
            $sharingAlloed  = 1;
            
        }
        
        $query['allowed_sharing'] = $sharingAlloed;
        
        $videos = $video = $photos = $photos = $finalMedia = array();
        
        $medias = $this->db->query("SELECT * FROM `product_media_hm` WHERE `pd_id` = '$pd_id' AND `status` = 1")->result_array();
        foreach($medias as $media){
            if($media['photo_or_video'] == 1){
                $photo['photo'] =  $media['url'];
                $photo['description'] =  $media['description'];
                $photos[] = $photo;
            } else {
                $video['video'] =  $media['url'];
                $video['thumbnail_image'] =  $media['thumbnail_image'];
                $video['description'] =  $media['description'];
                $videos[] =  $video;
            }
           
        }
  
        $products = array();
        $variable_products_info = array();
        if($isVariable == 1){
            $oldSize = "";
            $variables = $this->db->query("SELECT vp.`id`as variable_pd_id, vp.`pd_id`, vp.`sku`, vp.`color`, vp.`size`, vp.`quantity`, vp.`price`, vp.`vendor_price`, vp.`image`, s.size_area, s.`size_measure_cm`, s.`size_measure_inch`, s.`size_name`, c.color as color_name, c.color_code, c.color_image FROM `variable_products_hm` as vp LEFT JOIN `size_chart_hm` as s ON (s.id = vp.`size`) LEFT JOIN `color_hm` as c ON (c.id = vp.`color`) WHERE vp.`pd_id` = '$pd_id' ORDER BY s.size_name ASC")->result_array();
            
           
            $i=0;
           foreach($variables as $v){
                foreach($v as $key => $value){
                    if($value == null){
                        $v[$key] = "";
                    }
                    
                    if($key == 'size_area' || $key == 'size_measure_cm' || $key == 'size_measure_inch' ){
                        unset($v[$key]);
                        
                    }  
                }
                if($i != 0){
                    if($oldColor == $v['color']){
                        $c = array();
                        // $c['color_code']=$v['color_code'];
                        // $c['color_image']=$v['color_image'];
                        // $c['image']=$v['image'];
                        
                        $c['size_name'] = $v['size_name'];
                        $c['variable_pd_id'] = $v['variable_pd_id'];
                        //$c['prod_info'][] = $v;
                        $prod['size_info'][] = $c;
                        
                    }else{
                        
                        $products[] = $prod;
                        $c = $prod = array();
                        
                        $c['variable_pd_id'] = $v['variable_pd_id'];
                        $c['size_name'] = $v['size_name'];
                        
                        $prod['color_name']=$v['color_name'];
                        $prod['color_code']=$v['color_code'];
                        $prod['color_image']=$v['color_image'];
                        
                        
                        $prod['image']=$v['image'];
                    
                        $prod['size_info'][] = $c;
                    }
                } else {
                    
                    
                    
                    
                    $c['size_name'] = $v['size_name'];
                    $c['variable_pd_id'] = $v['variable_pd_id'];
                    
                    $prod['color_name']=$v['color_name'];
                    $prod['color_code']=$v['color_code'];
                    $prod['color_image']=$v['color_image'];
                    $prod['image']=$v['image'];
                    $prod['size_info'][] = $c;
                }
                    
               
                $oldSize = $v['size_name'];
                $oldColor = $v['color'];
                $i++;  
                
                $variable_products_info[] = $v;
            }
            $products[] = $prod;
            $variable_products = $products;
            
        }
        $finalMedia['photos'] = $photos; 
        $finalMedia['videos'] = $videos; 
      //    print_r($finalMedia); die();
        $query['media'] = $finalMedia;
        
        $query['variable_products'] = $products;
        $query['variable_products_info'] = $variable_products_info;
        $query['specifications'] = $specifications;
//      print_r($specifications); die();
            // $query[] = $query;
            $finalArray[] = $query;
    //    print_r($finalArray[0]); die();   
    
    // $finalArray[]['specifications'] = $specifications;
       
       foreach($finalArray[0] as $key => $value) {
            
            if (is_null($value)) {
              // print_r($key); die();
                 $finalArray[0][$key] = "";
              
            }
        }
         
  
           // print_r($finalArray); die();
            return $finalArray;

    }
    
    public function getallCategoriesHealthMall(){
        $data = array();
        $stmt = "SELECT id,cat_name,photo from categories WHERE parent_cat_id = 0 AND pc_status != 3";
        
        $query = $this->db->query($stmt);
        
        foreach($query->result_array() as $q){
            
            $id  = $q['id'];
            $prods = $this->db->query("SELECT * from product_details_hm WHERE pd_pc_id = '$id'");
            
            $pro = $prods->result_array();
            if(sizeof($pro) > 0){
                $data[] =  $q;
            }
        }
        
        return $data;
    }
    public function getallSubCategoriesHealthMall($c_id){
        //$stmt = "SELECT psc_id,psc_name,psc_photo from product_sub_category where psc_pc_id = ".$c_id."";
        $stmt = "SELECT id,cat_name FROM categories WHERE parent_cat_id = '$c_id'";
        $query = $this->db->query($stmt);
        $query1 = $query->result_array();
        $querys = $query->result_array();
        foreach($querys as $q){
            $dataSubcatIdInSub = $q['id'];
            
            
            
            $results1 = $this->MedicalMall_model->get_subcat($dataSubcatIdInSub);
            // print_r($q);
            
            $data[] = array(
                "id" => $dataSubcatIdInSub,
                "cat_name" => $q['cat_name'],
                "subcategory" => $results1
            ); 
        }
        
        
        
        
        return $data;
    }
    public function getProductByCategories($data){
    
        $stmt = "SELECT categories.*,v_distance,product_details_hm.pd_id,product_details_hm.pd_name,product_details_hm.pd_mrp_price,product_details_hm.pd_vendor_price,product_details_hm.pd_overall_ratting,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4
        FROM product_details_hm 
        JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
        JOIN categories ON product_details_hm.pd_pc_id = categories.id OR product_details_hm.pd_psc_id = categories.id
        WHERE categories.id=".$data['c_id']."";
        
        $query = $this->db->query($stmt);

        return $query;
    }
    public function getProductBySubCategories($data){
    
        $stmt = "SELECT categories.*,v_distance,product_details_hm.pd_id,product_details_hm.pd_name,product_details_hm.pd_mrp_price,product_details_hm.pd_vendor_price,product_details_hm.pd_overall_ratting,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4
        FROM product_details_hm
        JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
        JOIN categories ON product_details_hm.pd_psc_id = ".$data['sub_id']."
        WHERE categories.id=".$data['sub_id']."";
        
        $query = $this->db->query($stmt);

        return $query;
    }
    public function checkProductAvailibility1($data){ 
    
        $stmt = "SELECT product_details_hm.pd_name,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4
        FROM product_details_hm
        JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
        JOIN pincode_details ON vendor_details_hm.v_id = pincode_details.vp_id
        WHERE product_details_hm.pd_id=".$data['p_id']." && pincode_details.pincode = ".$data['pincode']."";
        
        $query = $this->db->query($stmt);

        return $query;
    }
    
        public function checkProductAvailibility($data){
       // print_r($data); 
        $isPanIndia = 0;
        
        
        $stmt = "SELECT product_details_hm.pd_name,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4, vendor_details_hm.is_panindia
        FROM product_details_hm
        JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
        WHERE product_details_hm.pd_id=".$data['p_id'];
    
        $query = $this->db->query($stmt);
        $qarray = $query->row_array();
        //  print_r($qarray); die();
        $isPanIndia = $qarray['is_panindia'];
        // echo $isPanIndia; die();
        if($isPanIndia == 1){
            $pincode = $data['pincode'];
            $stmt = "SELECT * FROM `pan_india` WHERE `pincodes` = $pincode";
            $query = $this->db->query($stmt);
            $qarray = $query->result_array();
            // print_r($qarray); die();
            return $query->num_rows();
            
        } else {
            
            $stmt = "SELECT product_details_hm.pd_name,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4, vendor_details_hm.is_panindia
        FROM product_details_hm
        JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
        JOIN pincode_details ON vendor_details_hm.v_id = pincode_details.vp_id
        WHERE product_details_hm.pd_id=".$data['p_id']." && pincode_details.pincode = ".$data['pincode']."";
        
        $query = $this->db->query($stmt);
        $qarray = $query->row_array();
        
          return $query->num_rows();
        }
        
    }
    
    
    public function checkLoginUser($data){
        $stmt = "SELECT * from users where BINARY email='".$data['email']."'AND password='".md5($data['password'])."' AND is_active=0";
        
        $query = $this->db->query($stmt);


        return $query;
    }
    public function addUser($data){
        $stmt = "SELECT * from users where email='".$data['email']."'";
        
        $query = $this->db->query($stmt);
        
        $count = $query->num_rows();
        if($count > 0){   
        
            $result = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "message" => "User with Email Already Exists",
                    
                );
            
        }else{
            $query_to_insert = "INSERT INTO users (name,email,phone,lat,lng,password)
            VALUES ('".$data['name']."','".$data['email']."','".$data['phoneno']."','".$data['lat']."','".$data['long']."','".md5($data['password'])."')";
            $user = $this->db->query($query_to_insert);
            $result = array(
            "status" => "true",
            "message" => "User Created"
            );
            
        }

        return $result;
    }
    
    public function getAllProductsByName($data){
    
//      $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
//         FROM vendor_details_hm vd 
//         join product_details_hm pd 
//         on vd.v_id=pd.pd_added_v_id
//      JOIN product_category ON 
//      pd.pd_pc_id = product_category.pc_id
//      JOIN product_sub_category ON 
//      pd.pd_pc_id = product_sub_category.psc_id
//         WHERE pd.pd_name LIKE '%".$data['name']."%' || product_sub_category.psc_name LIKE '%".$data['name']."%' || product_category.pc_name LIKE '%".$data['name']."%'";
        $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_name LIKE '%".$data['name']."%' AND pd.pd_status = '1'";
        $query = $this->db->query($stmt);

        return $query;
        
    }
    public function getallCountries(){
        $stmt = "SELECT * from countries";
    
        $query = $this->db->query($stmt);

        return $query;
    }
    public function getallStates($country_id){
        $stmt = "SELECT state_id,state_name from states where country_id=".$country_id."";
    
        $query = $this->db->query($stmt);

        return $query;
    }
    public function getallCities($state_id){
        $stmt = "SELECT city_id,city_name from cities where state_id =".$state_id."";
    
        $query = $this->db->query($stmt);

        return $query;
    }
    public function setallRatings($data){
        $query_for_insert = "INSERT INTO `product_reviews`(`pr_pd_id`, `pr_added_c_id`, `pr_review`, `pr_rating`, `listing_id`) 
        VALUES ('".$data['product_id']."',
        '".$data['cus_id']."',
        '".$data['pr_review']."',
        '".$data['ratting']."',
        '34')"; 
        $query = $this->db->query($query_for_insert);
        if($query){
             $query_for_rating = "SELECT AVG(pr_rating) as avgrating from product_reviews where pr_pd_id =".$data['product_id']."";
             $query = $this->db->query($query_for_rating);
             $rat_data = $query->result_array();
             //$rating=bcdiv($rat_data[0]['avgrating'],1,1);
             $update_query="UPDATE `product_details_hm` SET pd_overall_ratting=".$rat_data[0]['avgrating']." where pd_id=".$data['product_id']."";
             $updatedata = $this->db->query($update_query);
             if($updatedata){
                 $result = array(
                    "status" => "true",
                    "message" => "rating updated"    

                );
             }
        }

        return $result;
    }
    public function getRatingsForProduct($pd_id){
        $stmt = "SELECT AVG(pr_rating) as avgrating from product_reviews where pr_pd_id=".$pd_id."";
        $query = $this->db->query($stmt);
        
        $stmtNew = "SELECT * from product_reviews where pr_pd_id=".$pd_id."";
        $queryNew = $this->db->query($stmtNew);
        $reviewData = $queryNew->result_array();
        $dataAll = array();
        foreach($reviewData as $review){
            $user_id = $review['pr_added_c_id'];
            
            $userData = $this->db->query("SELECT * FROM `users` WHERE `id` = '$user_id'")->result_array();
            $userName = "";
            foreach($userData as $user){
                $userName = $user['name'];
            }
            
            $data = $review;
            $data['name'] = $userName;
            
            $dataAll[] = $data;
        }
        
        
        
        $result = array(
                    "status" => "true",
                    "statuspic_root_code" => "200",
                    "rating" => $query->result_array(),
                    "data" => $dataAll
                );
        return $result;
    }
    public function getallSearch(){
    $resp = $results =  $result = array();
//      $stmt = "SELECT product_details_hm.pd_name FROM product_details_hm UNION SELECT categories.cat_name FROM categories";
    
//      $rows = $this->db->query($stmt)->result_array();
        
//      foreach($rows as $row){
         //   print_r($row); die();
          //  $pdName =$row['pd_name'];
          // $stmt1 = "SELECT pd_id, pd_name FROM product_details_hm WHERE `pd_name` = $pdName AND `pd_status` = 1 " ;
    
        $stmt1 = "SELECT pd_id, pd_name FROM product_details_hm WHERE `pd_status` = 1 " ;
    
        $result = $this->db->query($stmt1)->result_array();
        //print_r($result); die();
        if(sizeof($result)>0){
            $results[] = $result;
//      }

        }
        $results = $results[0];
        //print_r($results); die();
        
        
        
        return $results;
        
    }
    public function getBestSellerProducts(){
        $stmt = "SELECT pd.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,pd.pd_added_v_id,pd.pd_short_desc,pd.pd_mrp_price,pd.pd_vendor_price,pd.pd_name,pd.pd_photo_1,pd.pd_overall_ratting as ratting
        FROM product_details_hm pd
        join vendor_details_hm vd 
        on vd.v_id=pd.pd_added_v_id     
        WHERE pd.pd_best_seller > 0  AND pd.pd_status = '1' ORDER BY RAND()";
        
        $query = $this->db->query($stmt)->result_array();

        return $query;
    }
    
    public function getallProductByCatSubCat($data){
        $stmt = "SELECT pd.pd_id,pd.pd_short_desc,pd.pd_added_v_id,pd.pd_mrp_price,pd.pd_vendor_price,pd.pd_psc_id,pd.pd_pc_id,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_overall_ratting as ratting
        FROM vendor_details_hm vd 
        join product_details_hm pd 
        on vd.v_id=pd.pd_added_v_id
        JOIN categories AS cc ON 
        pd.pd_pc_id = cc.id
        JOIN categories AS sc ON
        pd.pd_psc_id = sc.id
        WHERE pd.pd_psc_id = ".$data['sc_id']." && pd.pd_pc_id = ".$data['c_id']."";
        
        $query = $this->db->query($stmt);

        return $query;
    
    }
    public function getVendorById($data){
        $res = $photos = $videos = $medias = $media = $finalMedia = $med = array();
        $stmt = "SELECT vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,vd.v_email,vd.v_landno,vd.v_phoneno,vd.v_address1,vd.v_address2,vd.v_city,vd.v_state,vd.v_country,vd.v_pincode,vd.v_lat,vd.v_long,vd.v_company_name,vd.v_delivery_charge,vd.v_min_order,vd.v_delivery_days,vd.v_delivery_time,vd.v_cashback,vd.tnc,vd.aboutus,vd.return_policy,vd.return_days
        FROM vendor_details_hm vd 
        WHERE vd.v_id = ".$data['v_id']."";
        
        $query = $this->db->query($stmt)->result_array();

        $medias = $this->db->query("SELECT * FROM `vendor_media_hm` WHERE v_id = ".$data['v_id']." AND `status` = 1 ")->result_array();
        foreach($medias as $media){
            if($media['photo_or_video'] == 1){
                $photo['photo'] = $media['url'];
                $photo['description'] = $media['description'];
                $photos[] = $photo;
            } else {
                $video['video'] = $media['url'];
                $video['description'] = $media['description'];
                $video['thumbnail_image'] = $media['thumbnail_image'];
                $videos[] = $video;
            }
        }
        $med['photos'] = $photos;
        $med['videos'] = $videos;
        // $finalMedia = $med;
        $finalMedia['media'] = $med;
         $query = array_merge($query[0],$finalMedia);
         //$query[0] = $finalMedia;
        
     $res[] = $query;
        return $res;
    
    }
    
    public function get_description($pro_id){
        $fetch = $this->db->query("SELECT pd_id,pd_name,item_type,origin_country,speciality,gender_type,ingredients,total_units,indications,safety_warnings,directions,pd_short_desc FROM product_details_hm WHERE pd_id='$pro_id'");
        $res = array(
                "status" => "true",
                "statuspic_root_code" => "200",
                "data" => $fetch->result_array()
                );
       return $res;
    }
    
//  get_offers
    public function get_offers($user_id){
        
        $allOffers = array();
        $today = $created_date = date("Y-m-d");
        $offers = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND status = '0' AND end_date >= '$today'");
        foreach ($offers->result_array() as $offer) {
           // $offer[''] = "100";
           $offer_id = $offer['id']; 
            $result = $this->MedicalMall_model->get_offer_products($offer_id);
            $offers = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND status = '0' AND end_date >= '$today'");
            if($result['products_count'] > 0){
                $coupon_id=$offer['vendor_id'];
                
                $query = $this->db->query("SELECT * FROM `spin_value` WHERE coupon_id='$coupon_id' AND user_id = '$user_id' and type='1'");
                $count = $query->num_rows();
             $coupon_status="1";
             if($count > 0){
                
                $coupon_status="0"; 
                 
             }
            
            $query = $this->db->query("SELECT * FROM `spin_value` WHERE coupon_id='$coupon_id' AND user_id = '$user_id'");
                $count = $query->num_rows();
             $coupon_status="1";
             if($count == 5){
                
                $coupon_status="0"; 
                 
             }
            
            $v_id= $offer['vendor_id'];
                    $query_vendor = $this->db->query("SELECT v_name FROM `vendor_details_hm` WHERE `v_id` = '$v_id'"); 
                    $count_vendor = $query_vendor->num_rows();
                    $vendor_ = $query_vendor->row_array();
                    if($count_vendor > 0){
                        
                        $v_name =$vendor_['v_name'];
                         
                     }else{
                         $v_name="";
                         
                     }
               
               
               $allOffers[]=array("id"=>$offer['id'],
            "vendor_id"=>$offer['vendor_id'],
            "vendor_type"=>$offer['vendor_type'],
        "vendor_name"=>$v_name,
            "name"=>$offer['name'],
            "price"=> $offer['price'],
            "offer_on"=> $offer['offer_on'],
            "offer_on_ids"=> $offer['offer_on_ids'],
            "save_type"=> $offer['save_type'],
            "offer_type"=> $offer['offer_type'],
            "max_discound"=> $offer['max_discound'],
            "min_amount"=>$offer['min_amount'],
            "offer_description"=> $offer['offer_description'],
            "offer_tnc"=>$offer['offer_tnc'],
            "listing_id"=> $offer['listing_id'],
            "offer_image"=> $offer['offer_image'],
            "end_date"=> $offer['end_date'],
            "physical_offer"=> $offer['physical_offer'],
            "promotional_ad"=> $offer['promotional_ad'],
            "status"=>$offer['status'],
            "coupon_status"=>$coupon_status);
               
               
              /*  $allOffers[] = $offer; */ 
            }
            
        }
        $res['status'] =  200;
        $res['message'] =  "success";
        $res['data'] =  $allOffers;
        return $res;
    }
    
    public function add_address($address){
        
        $address = $this->db->insert('user_address', $address);
        $insert_id = $this->db->insert_id(); 
        if($insert_id != " "){
            $res = array(
                "status" => 200,
                "messase" => "success"
            );
        } else {
            $res = array(
                "status" => 400,
                "messase" => "Something went wrong, please try again"
            );
        }
        return $res;
    }
//  get_address
    public function get_address($user_id){
        
        $results = $this->db->query("SELECT * FROM user_address WHERE user_id = '$user_id'")->result_array();
        foreach($results as $result){
            $address[] = $result;
        }
        
            $res = array(
                "status" => 200,
                "message" => "success",
                "data" => $address 
         );
        return $res;
    }
//  
    
    public function get_cat_subcat() {
        $results = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE parent_cat_id='0'");
        foreach ($results->result_array() as $result) {
            $catId = $result['id'];
            $result_sub = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id` = '$catId'");
            $prods      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE pd_pc_id = '$catId'");
            $get_list   = $prods->row_array();
            $pro        = $get_list['p_count'];
            if ($pro>0) {
                $data_subcat_all = [];
                foreach ($result_sub->result_array() as $subcat) {
                    $dataSubcatId = $subcat['id'];
                    $subcat_photo = $subcat['photo'];
                    $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                    $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                    $results1 = $this->MedicalMall_model->get_subcat($dataSubcatId);
                    $dataSubcat['psc_id'] = $subcat['id'];
                    $dataSubcat['psc_name'] = $subcat['cat_name'];
                    $dataSubcat['psc_photo'] = $subcat_photo;
                    $dataSubcat['subcategory'] = $results1;
                    $data_subcat_all[] = $dataSubcat;
                }
                
                $result_photo = $result['photo'];
                $result_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
                $result_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
                $cat['pc_id'] = $result['id'];
                $cat['pc_name'] = $result['cat_name'];
                $cat['pc_photo'] = $result_photo;
                $cat['subcategory'] = $data_subcat_all;
                $data[] = $cat;
            }
        }
        $res = array(
            "status" => 200,
            "message" => "success",
            "data" => $data
        );
        return $res;
    }

    public function get_subcat($dataSubcatId) {
        $data = array();
        $results = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id`='$dataSubcatId'");
        foreach ($results->result_array() as $result) { 
            $catId = $result['id'];
            $result_sub = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id` = '$catId'");
            $prods      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE pd_pc_id = '$catId' OR pd_psc_id = '$catId'");
            $get_list   = $prods->row_array();
            $pro        = $get_list['p_count'];         
            $data_subcat_all = [];
            if ($pro>0) {
                foreach ($result_sub->result_array() as $subcat) {
                    $dataSubcatIdInSub = $subcat['id'];
                    $subcat_photo = $subcat['photo'];
                    $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                    $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                    $results1 = $this->MedicalMall_model->get_subcat($dataSubcatIdInSub);
                    $dataSubcat['psc_id'] = $subcat['id'];
                    $dataSubcat['psc_name'] = $subcat['cat_name'];
                    $dataSubcat['psc_photo'] = $subcat_photo;
                    $dataSubcat['subcategory'] = $results1;
                    $data_subcat_all[] = $dataSubcat;
                }
                $result_photo = $result['photo'];
                $result_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
                $result_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
                $cat['pc_id'] = $result['id'];
                $cat['pc_name'] = $result['cat_name'];
                $cat['pc_photo'] = $result_photo;
                $cat['subcategory'] = $data_subcat_all;
                $data[] = $cat;
            }
        }
        $res = array(
            "data" => $data
        );
        return $data;
    }
    
    
    
//  get_user_orders
    public function get_user_orders($user_id){
        $data = array();
        $pro_list = array();
        $results = $this->db->query("SELECT pm.payment_method as payment_method_name,uo.*,vd.delivery_by_medicalwale FROM `user_order` as uo left join vendor_details_hm as vd on (uo.`listing_id` = vd.v_id) left join payment_method as pm on (uo.payment_method = pm.id) WHERE `user_id` = '$user_id' AND  `listing_type`='34' ORDER BY order_id DESC");
        foreach($results->result_array() as $order){
            $products = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id` = ".$order['order_id']." AND `user_id` = '$user_id'");
            $details = $products->result_array();
           // print_r($products->result_array());die;
            $pro_list =array();
            $i = 0;
           foreach($products->result_array() as $pro){
                $product_qty = $pro['product_qty'];
                $price = $pro['price'];  
                $res = $this->db->query("SELECT brand_name,pd_name,pd_id, pd_photo_1,pd_mrp_price,pd_vendor_price FROM product_details_hm WHERE pd_id = '".$pro['product_id']."'")->result_array();
                $res[0]['product_qty'] = $product_qty;
                $res[0]['price'] = $price;
                
                if($pro['variable_pd_id'] > 0){
                    $variable_pd_id = $pro['variable_pd_id'];
                    $variable_product = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = $variable_pd_id ")->row_array(); 
                    
                    $colorId = $variable_product['color'];
                    $sizeId = $variable_product['size'];
                    $color = $this->MedicalMall_model->get_color_by_id($colorId);
                    $size = $this->MedicalMall_model->get_size_by_id($sizeId);
                    
                    $c = $color[0]['color'];
                    $s = $size[0]['size_name'];
                    $prodName = $res[0]['pd_name'];
                    if($s != null && $s != ''){
                        $prodName = $prodName." - $s"    ;
                    }
                    if($c != null && $c != ''){
                        $prodName = $prodName." - $c"    ;
                    }                   
                    $res[0]['pd_name'] = $prodName;
                    
                  // print_r($variable_product); die();
                    
                    if(!empty($variable_product['image'])){
                        $res[0]['pd_photo_1'] = $variable_product['image'];
                        $res[0]['pd_mrp_price'] = $variable_product['price'];
                        $res[0]['pd_vendor_price'] = $variable_product['vendor_price'];
                    }
                   // $res[0]['pd_photo_1'] = $variable_product['image'];
                    
                    $res[0]['variable_product'] = $variable_product;
                    //print_r($variable_product); die();   
                    
                } else {
                    $variable_product = (object)[];
                    $res[0]['variable_product']=$variable_product;
                } 
               
                array_push($pro_list,$res[0]);
               
                $i++;
            }
            
            $order += ['products'=>$pro_list];
                foreach($order as $key => $value){
                    if($value == null){
                        $order[$key] = "";
                    }
                }
                
            $data[] = $order;
        }
        
        
        $res1 = array(
            "status" => 200,
            "message" => "success",
            "data" => $data 
        );
       //  print_r($data);die;
        return $res1;
    }
//  add_to_wishlist
    public function add_to_wishlist($user_id, $product_id){
        
        $checkAvailable = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$product_id' AND `customer_id` = '$user_id'");
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
             $res = array(
                "status" => 400,
                "message" => "This product is already in interest list"
            );
        } else {
            $results = $this->db->query("INSERT INTO `customer_wishlist` (`product_id`, `customer_id`) VALUES ('$product_id', '$user_id');");
            $insert_id = $this->db->insert_id(); 
            if($insert_id){
                $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to interest list",
                     
                );
            } else {
                $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );
            }
        }
        
        return $res;
    }
    
    

//  product_like
    public function product_like($user_id, $product_id){
        
        $checkAvailable = $this->db->query("SELECT id FROM `product_like` WHERE `product_id` = '$product_id' AND `customer_id` = '$user_id' limit 1");
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
             $this->db->query("DELETE FROM `product_like` WHERE product_id='$product_id' and customer_id='$user_id'");
             $res = array(
                "status" => 400,
                "message" => "This product is disliked"
            );
        } else {
            $results = $this->db->query("INSERT INTO `product_like` (`product_id`, `customer_id`) VALUES ('$product_id', '$user_id');");
            $insert_id = $this->db->insert_id(); 
            if($insert_id){
                $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully like",
                     
                );
            } else {
                $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );
            }
        }
        
        return $res;
    }   
    
  public function get_product_offer($pd_id){
    $today = $created_date = date("Y-m-d");
    // echo $today; die();
    $vendor_id = "";
    $offers = array();
    $query = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` = '$pd_id'");
        
        foreach($query->result_array() as $query1 ){
            
            $prod_mrp = $query1['pd_mrp_price'];
            $pd_vendor_price = $query1['pd_vendor_price'];
            
            $catIds = $query1['pd_pc_id'];
                $subCatIds = $query1['pd_psc_id'];
                
                $vendor_id =    $query1['pd_added_v_id'];
        } 
        
//      echo  $vendor_id; die();
        
        
        $offersProd = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND end_date >= '$today' AND (`vendor_id` = '$vendor_id' || `vendor_id` = '') ");
        date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
        foreach ($offersProd->result_array() as $offer){
           // print_r($offer); die();
       //     $offersProd = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` = '$pd_id'");
           
       //     $pd_Info = $offersProd->result_array();
       //     foreach($pd_Info as $cats ){
                 
    //      } 
       //    print_r($offer); die();
         
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
                            $offer['offer_best_price'] = "0";
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
                            $offer['offer_best_price'] = "0";
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
                            $offer['offer_best_price'] = "0";
                        }
                           
                        
                    
                    $off = $offer;
                    $offers[] = $off;
                }
                }
                
            }
            
        }
       // print_r($offers);die();
        return $offers;
 }
//  get_user_wishlist

    public function get_user_wishlist($user_id){
        
        $checkAvailable = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
        
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
            
            foreach($checkAvailable->result_array() as $prodInfo)
            {
                $product_id = $prodInfo['product_id'];
               
                 $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
                 
                 $offers = $this->MedicalMall_model->get_product_offer($product_id);
                 
                //  print_r($results); die();
                
                $prod_data = $this->db->query($stmt)->result_array();
                foreach($prod_data as $prod){
                    $product = $prod;
                    $product['offers'] = $offers;
                }
               
                $prod_data_all[] = $product;
            }

            
                $res = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $prod_data_all,
                     
                );
            
        } else {
            
             $res = array(
                "status" => 400,
                "message" => "interest list is empty"
            );
            
        
        }
        
        return $res;
    }
    
    //  get_product_like

    public function get_product_like($user_id){
        
        $checkAvailable = $this->db->query("SELECT * FROM `product_like` WHERE `customer_id` = '$user_id'");
        
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
            
            foreach($checkAvailable->result_array() as $prodInfo)
            {
                $product_id = $prodInfo['product_id'];
               
                 $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
                 
                 $offers = $this->MedicalMall_model->get_product_offer($product_id);
                 
                //  print_r($results); die();
                
                $prod_data = $this->db->query($stmt)->result_array();
                foreach($prod_data as $prod){
                    $product = $prod;
                    $product['offers'] = $offers;
                }
               
                $prod_data_all[] = $product;
            }

            
                $res = array(
                    "status" => 200,
                    "message" => "success",
                    "data" => $prod_data_all,
                     
                );
            
        } else {
            
             $res = array(
                "status" => 400,
                "message" => "No product_like"
            );
            
        
        }
        
        return $res;
    }
    
    
    public function add_to_cart($user_id, $product_id, $quantity, $offer_id,$referal_code,$variable_pd_id,$sku){
        $cart = array("user_id"=>$user_id, "product_id"=>$product_id, "quantity"=>$quantity, "offer_id"=>$offer_id,'referal_code'=>$referal_code,"variable_pd_id"=>$variable_pd_id,"sku"=>$sku);
       // print_r($cart); die();
        //$checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$product_id' AND `customer_id` = '$user_id'");
        $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$product_id' AND `variable_pd_id` = '$variable_pd_id' AND `sku` = '$sku' AND `customer_id` = '$user_id'");
        $count = $checkAvailable->num_rows();

        if($count > 0){
            $prevQuant = 0;
            $cartAvailable =  $checkAvailable->result_array();
            foreach($cartAvailable as $cart){
                $prevQuant = $cart['quantity'];
            }
            // echo $prevQuant;
            // $totalQuantity = $prevQuant + $quantity;
            $totalQuantity = $quantity;
            $results = $this->db->query("UPDATE `user_cart` SET `quantity` = '$totalQuantity', `offer_id` = '$offer_id',`referal_code` = $referal_code, `variable_pd_id`='$variable_pd_id',`sku` = '$sku' WHERE `product_id` = '$product_id' AND `variable_pd_id`='$variable_pd_id' AND `sku` = '$sku' AND `customer_id` = '$user_id'");
            $res = array(
                "status" => 201,
                "message" => "Stack updated successfully",
                "data" => $cart
            );
        } else {
            $results = $this->db->query("INSERT INTO `user_cart` (`product_id`, `customer_id`, `quantity`, `offer_id`,`referal_code`,`variable_pd_id`,`sku`) VALUES ('$product_id', '$user_id', '$quantity', '$offer_id','$referal_code','$variable_pd_id','$sku')");
            $insert_id = $this->db->insert_id(); 
            if($insert_id){
                $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to Stack",
                    "data" => $cart
                );
            } else {
                $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong, please try again",
                );
            }
        }
        
        return $res;
    }
    
//  get_user_cart
    public function get_user_cart($user_id){
        $cartQuantity = $delvChargeOld = 0;
        // $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `customer_id` = '$user_id'");
        $checkAvailable = $this->db->query("SELECT uc.`id`,uc.`customer_id`, uc.`product_id`,uc.`quantity`, uc.`offer_id`,pd.pd_added_v_id,pd.pd_vendor_price FROM `user_cart` uc JOIN product_details_hm pd ON uc.product_id = pd.pd_id WHERE uc.`customer_id` = '$user_id' ORDER BY pd.pd_added_v_id ASC");
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
            
            foreach($checkAvailable->result_array() as $prodInfo)
            {
                $product = array();
                $product_id = $prodInfo['product_id'];
                $quantity = $prodInfo['quantity'];
                // $product_id = $prodInfo['product_id'];
               
                $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_min_order,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,vd.cap_available,vd.cap_charge,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
                $prod_dataAll = $this->db->query($stmt);
                $prod_data = $prod_dataAll->result_array();
                $prod_num = $prod_dataAll->num_rows();
                // print_r($prod_data); die();
                if($prod_num > 0){
                    $offers = $this->MedicalMall_model->get_product_offer($product_id);
                
                    $rating = $this->MedicalMall_model->get_num_ratings($product_id);
                   //print_r($offers);die;
                   //echo sizeof($prod_data); 
                    foreach($prod_data as $prod){
                        $product = $prod;
                        // print_r($product); die();
                         $cartQuantity = $cartQuantity + $quantity;
                        $product['quantity'] = $quantity;
                        $product['rating'] = $rating;
                        $product['offer_id'] = $prodInfo['offer_id'];
                        if($prodInfo['offer_id'] != 0){
                            foreach($offers as $off){
                                if($off['id'] == $prodInfo['offer_id']){
                                    $product['offer_price'] = $off['offer_mrp'];
                                    break;
                                }
                            }
                            // $oid = $prodInfo['offer_id']-1;
                            // print_r($oid);die;
                            // $product['offer_price'] = $offers[$oid]['offer_mrp'];  
                        }
                        $product['offers'] = $offers;
                        // print_r($prod); die();
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
               
                $prod_data_all[] = $product;
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
                $prod_data_final['products'] = $prod_data_all;
                $prod_data_final['product_quantity'] = $cartQuantity;
               
                $res = array(
                    "status" => 200, 
                    "message" => "success",
                    "data" => $prod_data_final
                );
            
        } else {
            
             $res = array(
                "status" => 400,
                "message" => "Cart is empty",
                "data" => (object)[]
            );
            
        
        }
        
        return $res;
    }
    
    //  get_cart
   
    
       public function get_cart($user_id){
        $var_prod_price = $var_ven_price = $oldId = 0;
        $prodName = "";
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
                    
                    $offers = $this->MedicalMall_model->get_product_offer($product_id);
                
                    $rating = $this->MedicalMall_model->get_num_ratings($product_id);
                    
                    if(empty($referal_code_id)){
                        $referal_code = (object)[];
                    } else {
                        $referal_code = $this->MedicalMall_model->get_referal_code($product_id,$referal_code_id);
                        // $referal_code = $referal_code_id;
                    }
                   
                   //echo sizeof($prod_data); 
                    foreach($prod_dataAll->result_array() as $prod){
                        $product = $prod;
                       $prodName = $prod['pd_name'];
                       $variableProduct = array();
                        if($variable_pd_id > 0){
                            $variableProds = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = '$variable_pd_id'");
                            // print_r($variableProds->result_array()); die();
                            foreach($variableProds->result_array() as $variableProd){
                                /* print_r($variableProd); die();*/
                                $colorId = $variableProd['color'];
                                $sizeId = $variableProd['size'];
                                
                                $color = $this->MedicalMall_model->get_color_by_id($colorId);
                                $size = $this->MedicalMall_model->get_size_by_id($sizeId);
                                
                                $variableProduct['id'] = $variableProd['id'];
                                $variableProduct['pd_id'] = $variableProd['pd_id'];
                                $variableProduct['sku'] = $variableProd['sku'];
                                $variableProduct['quantity'] = $variableProd['quantity'];
                                $variableProduct['price'] = $variableProd['price'];
                                $variableProduct['vendor_price'] = $variableProd['vendor_price'];
                                $variableProduct['image'] = $variableProd['image'];
                                $variableProduct['color'] = $color;
                                $variableProduct['size'] = $size;
                                
                                $var_prod_price = intval($variableProd['price']);
                                $var_ven_price = $variableProd['vendor_price'];
                                $s = $c = '';
                                if(sizeof($color) > 0){
                                    $c = $color[0]['color'];
                                }
                                
                                if(sizeof($size) > 0){
                                    $s = $size[0]['size_name'];
                                }
                                
                                
                                
                                if($s != null && $s != ''){
                                    $prodName = $prodName." - $s"    ;
                                }
                                if($c != null && $c != ''){
                                    $prodName = $prodName." - $c"    ;
                                }                   
                               /* echo $prodName;
                                die();*/
      
                            }
                        } else {
                            $variableProduct = (object)[];
                        }
                        
                        // productName 
                        $product['pd_name'] = $prodName;
                        //   $oldCost $finalCost
                        $productCost = 0;
                        if(!empty($offers)){
                            $productCost = intval($offers[0]['offer_best_price'] * $quantity);
                        }  else if($var_ven_price > 0 && $var_ven_price != null){
                            $productCost = intval (round($var_ven_price)) * $quantity;
                            $prod['pd_vendor_price'] = intval($var_ven_price);
                        } else {
                            $productCost = intval (round($prod['pd_vendor_price'])) * $quantity;
                        }  
                        
                      /*  $var_prod_price

*/
                        $prod['pd_vendor_price'] = intval (round($prod['pd_vendor_price']));
                        $finalCost = $finalCost + $productCost;
                         
                        // $oldCost = $productCost;
                            
                        $cartQuantity = $cartQuantity + $quantity;
                        $product['quantity'] = $quantity;
                        $product['product_cost'] = intval (round($productCost));
                        $product['rating'] = $rating;
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
                                   // echo $product['offer_price']; die();
                                    break;
                                }
                            }
                        }
                        $product['offers'] = $offers;
                        $product['pd_vendor_price'] = $prod['pd_vendor_price'];
                        if($variable_pd_id > 0){
                            if(!empty($variableProduct['image'])){
                                 $product['pd_photo_1'] = $variableProduct['image'];
                            }
                            if(!empty($variableProduct['price'])){
                                 $product['pd_mrp_price'] = intval (round($variableProduct['price']));
                            }
                            if(!empty($variableProduct['vendor_price'])){
                                 $product['pd_vendor_price1'] = intval (round($variableProduct['vendor_price']));
                            }
                        }
                        
                        
                        //  print_r($variableProduct['image']);
                       /*  print_r($product);*/
                        //  die();
                        $del['pd_id'] = $prod['pd_id'];
                        $del['v_id'] = $prod['v_id'];
                        $del['v_delivery_charge'] = intval (round($prod['v_delivery_charge'])) ;
                        $del['v_min_order'] = intval (round($prod['v_min_order'])) ;
                        $del['pd_vendor_price'] = intval (round($prod['pd_vendor_price'])) ;
                        $del['quantity'] = $quantity;
                       
                        $delivery[] = $del;
                       /* print_r($product);*/
                    }
                    
                    
                }
              /*  die();*/
               
                // deliveryCharges begins 
                
                    $prod_data[] = $product;
                    
                    //   print_r($chc); die();
                   /*  print_r($product);*/
                    }
                  /*  die();*/
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
                    $ven_data['v_delivery_charge'] = intval (round($prod['v_delivery_charge'])) ;
                    $ven_data['cash_handling_charges'] = intval (round($charge)) ;
                    $ven_data['vendor_cost'] = intval (round($finalCost)) ;
                    $ven_data['v_min_order'] = intval (round($prod['v_min_order'])) ;
                    $ven_data['v_name'] = $prod['v_name'] ;
                  //  print_r($prod_data);
                    $prod_data_all['vendor'] = $ven_data;
                    $prod_data_all['product'] = $prod_data;
                    
                    // print_r($availableCartFull[$i]);
                        $cart_data_all[] = $prod_data_all;
                }
                
                 
            }      
            
        
          // print_r($prod);
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
             
//print_r($delvry);
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
               
                $res = array(
                    "status" => 200, 
                    "message" => "success",
                    "data" => $prod_data_final
                );
            
        } else {
            
             $res = array(
                "status" => 400,
                "message" => "Cart is empty",
                "data" => (object)[]
            );
            
        
        }
        
        return $res;
    }
    
//  get_referal_code($product_id,$referal_code_id)

    public function get_referal_code($product_id,$referal_code_id){
        $data = array();
        $referalCode = $this->db->query("SELECT * FROM `refer_product_hm` WHERE `id` = '$referal_code_id'")->row_array();
        $data['referal_code_id'] = $referalCode['id'];
        $data['code'] = $referalCode['code'];
        // print_r($data); die();
        return $data; 
    } 
    
//  remove_from_cart
    public function remove_from_cart($user_id, $pd_id,$variable_pd_id){ 
        $checkAvailable = $this->db->query("DELETE FROM `user_cart` WHERE `customer_id` = '$user_id' AND `variable_pd_id` = '$variable_pd_id' AND `product_id` = '$pd_id' ");
       
        $affected_rows = $this->db->affected_rows();
       
        if($affected_rows > 0){
            return 1;    
        } else {
            return 0;
        }
        
    }
    
//  remove_from_wishlist

    public function remove_from_wishlist($user_id, $pd_id){ 
        $checkAvailable = $this->db->query("DELETE FROM `customer_wishlist` WHERE `customer_id` = '$user_id' AND `product_id` = '$pd_id' ");
       
        return 1;
    }
    
//  get_offer_products
    public function get_offer_products($offer_id){ 
        $res = array();
        $all_products = array();
        $all_products_count = 0;
        $today = $created_date = date("Y-m-d");
        $vendor_offers = $this->db->query("SELECT * FROM `vendor_offers` WHERE `id` = '$offer_id' AND end_date >= '$today' ");
        $offer =  $vendor_offers->row_array();
        $vendor_offers_nums =  $vendor_offers->num_rows();
         $v_id = $offer['vendor_id'];
       //  echo $v_id; die();
       if($v_id != ""){
           $vendorQuery = "AND pd.pd_added_v_id = '$v_id' ";
       } else {
           $vendorQuery = "";
       }
       //echo $vendorQuery; die();
        if($vendor_offers_nums > 0){
            
       //   $offer['products'] = array();
         
           //   print_r($offer['offer_on']); die();
          if($offer['offer_on'] == '1' ){
                $str = $offer['offer_on_ids'];
                $afterstr =  (explode(",",$str));
                for($i=0;$i<count($afterstr);$i++){
                    $pdId = $afterstr[$i];
                   // $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_pc_id = '$pdId'";
                   $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_pc_id = '$pdId' $vendorQuery";
                   
                    $query = $this->db->query($stmt);
                    $rows = $query->result_array();
                    $rows_num = $query->num_rows();
                   // print_r($rows); die();
                    foreach($rows as $row){
                        $results = $this->MedicalMall_model->get_product_offer($row['pd_id']);
                        
                        foreach($results as $result){
                          
                            if($result['id'] == $offer_id){
                                $row['offer'] = $result;
                            }
                        }
                        
                        $all_products[] = $row;
                    }
                    
                    $all_products_count = $all_products_count + $rows_num; 
                   // $offer['products_count'] = $rows_num;
            
                }
            // for($i=0;$i<count($afterstr);$i++){
                
            //         // offer
            //         if($offer['end_date']){
            //             if($today > $offer['end_date']){
            //                 $offer['expired'] = 1;
            //             } else {
            //                 $offer['expired'] = 0;
            //             }    
            //         } else {
            //             $offer['expired'] = 0;
            //         }
                    
                    
            //         $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_name LIKE '%".$data['name']."%'";
                  //  $query = $this->db->query($stmt);
            //         // mrp
            //         $offer['offer_mrp'] = $prod_mrp;
            //         $min_amount = $offer['min_amount'];
                        
            //             if($min_amount <= $prod_mrp){
            //                 $offer['message'] = "Offer available"  ;
            //                  if($offer['save_type'] == 'rupee'){
                                 
            //                      // max_discound
                                 
            //                     $dis =  $offer['price'];
                                
            //                     if($dis > $offer['max_discound']){
            //                         $dis = $offer['max_discound'];
            //                     } else {
            //                         $dis = $dis;
            //                     }
                                
                                
            //                     $prod_best_price = $prod_mrp - $dis;
                                
            //                     if($prod_best_price < 0){
            //                         $prod_best_price = 0;
            //                     } else {
            //                         $prod_best_price = $prod_best_price;
            //                     }
                                
            //                     if($dis > $offer['max_discound']){
            //                         $prod_best_price = $offer['max_discound'];
            //                     } else {
            //                         $prod_best_price = $prod_best_price;
            //                     }
            //                      $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
            //                     $offer['offer_best_price'] = $prod_best_price;
            //                 } else {
                                
            //                     $dis =  ( $offer['price'] / $prod_mrp ) * 100 ;
                                
            //                     if($dis > $offer['max_discound']){
            //                         $dis = $offer['max_discound'];
            //                     } else {
            //                         $dis = $dis;
            //                     }
                                
                                
            //                     $prod_best_price = $prod_mrp - $dis;
                                
            //                     if($prod_best_price < 0){
            //                         $prod_best_price = 0;
            //                     } else {
            //                         $prod_best_price = $prod_best_price;
            //                     }
                                
            //                     if($dis > $offer['max_discound']){
            //                         $prod_best_price = $offer['max_discound'];
            //                     } else {
            //                         $prod_best_price = $prod_best_price;
            //                     }
                                
            //                     $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                
            //                     $offer['offer_best_price'] = $prod_best_price;
                               
            //                 }
            //             } else {
            //                 $offer['message'] = "Minimum amount must be $min_amount rupees"  ;
            //                 $offer['offer_best_price'] = "0";
            //             }
                           
                        
                    
            //         $off = $offer;
            //         $offers[] = $off;
                
            //     }
         } 
         
         if($offer['offer_on'] == '2' ){
                $str = $offer['offer_on_ids'];
                $afterstr =  (explode(",",$str));
                for($i=0;$i<count($afterstr);$i++){
                    $pdId = $afterstr[$i];
                   // $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_pc_id = '$pdId'";
                   $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_psc_id = '$pdId' $vendorQuery";
                   
                    $query = $this->db->query($stmt);
                    $rows = $query->result_array();
                    $rows_num = $query->num_rows();
                   // print_r($rows); die();
                    foreach($rows as $row){
                        $results = $this->MedicalMall_model->get_product_offer($row['pd_id']);
                        
                        foreach($results as $result){
                          
                            if($result['id'] == $offer_id){
                                $row['offer'] = $result;
                            }
                        }
                        
                        $all_products[] = $row;
                    }
                    
                    $all_products_count = $all_products_count + $rows_num; 
                   // $offer['products_count'] = $rows_num;
            
                }
         } 
         
         
         if($offer['offer_on'] == '3' ){
                $str = $offer['offer_on_ids'];
                $afterstr =  (explode(",",$str));
                for($i=0;$i<count($afterstr);$i++){
                    $pdId = $afterstr[$i];
                    $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_id = '$pdId'";
                    $query = $this->db->query($stmt);
                    $rows = $query->result_array();
                    $rows_num = $query->num_rows();
                    foreach($rows as $row){
                        $results = $this->MedicalMall_model->get_product_offer($row['pd_id']);
                        
                        foreach($results as $result){
                            if($result['id'] == $offer_id){
                                $row['offer'] = $result;
                            }
                        }
                        
                        $all_products[] = $row;
                    }
                    
                    $all_products_count = $all_products_count + $rows_num; 
                   // $offer['products_count'] = $rows_num;
            
                }
            
         } 
         
       //  print_r($all_products); die();
          $offer['products'] = $all_products;
           $offer['products_count'] = $all_products_count;
          $res = $offer;
        } else {
            $res = array();
        }
        
        return $res;
    }
    
//  get_banner_images
    public function get_banner_images(){
         $bannerImages = $this->db->query("SELECT * FROM `banner_images` WHERE vendor_type !=10 and  `status` = 1 ORDER BY `id` DESC");
        
        $results =  $bannerImages->result_array();
        return $results;
    }
    
    
        public function search_products_by_all($data){
    
//      $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
//         FROM vendor_details_hm vd 
//         join product_details_hm pd 
//         on vd.v_id=pd.pd_added_v_id
//      JOIN product_category ON 
//      pd.pd_pc_id = product_category.pc_id
//      JOIN product_sub_category ON 
//      pd.pd_pc_id = product_sub_category.psc_id
//         WHERE pd.pd_name LIKE '%".$data['name']."%' || product_sub_category.psc_name LIKE '%".$data['name']."%' || product_category.pc_name LIKE '%".$data['name']."%'";
        $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_name LIKE '%".$data['name']."%' OR pd.pd_short_desc LIKE '%".$data['name']."%'  AND pd.pd_status = '1'";
        $query = $this->db->query($stmt);

        return $query;
        
    }
    
//  allow_rating

    public function allow_rating($user_id, $pd_id){
        $searchUserOrders = $this->db->query("SELECT * FROM `user_order` WHERE `user_id` = '$user_id'");
       $count = 0;
        
        foreach($searchUserOrders->result_array() as $order){
            $order_id = $order['order_id'];
            $searchUserproducts = $this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id` = '$order_id' AND `product_id` = '$pd_id' ");
            
            $rowCount = $searchUserproducts->num_rows(); 
           $count = $count + $rowCount;
        }
        
        if($count > 0){
            return 1;
        } else {
            return 0;
        }
        
       
    }
    
    //  public function get_recently_viewed($user_id){
    public function get_recently_viewed($user_id) {
        $data = array();
        $recently_viewed_products = $this->db->query("SELECT pd_id FROM `recently_viewed_products` WHERE `user_id` = '$user_id' ORDER BY `viewed_date` DESC");

        foreach ($recently_viewed_products->result_array() as $row) {
            $pd_id = $row['pd_id'];
            $query = $this->db->query("SELECT pd.pd_id,pd.pd_pc_id,pd.pd_psc_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting
        FROM vendor_details_hm vd 
        join product_details_hm pd 
        on vd.v_id=pd.pd_added_v_id
        WHERE   pd_id = '$pd_id' AND pd_status = '1'");
            $res = $query->row_array();
            if ($res != null) {
                $data[] = $res;
            }
        } 

        //print_r($data); die();

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
} 
