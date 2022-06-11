<?php
ini_set('memory_limit', '-1');
defined('BASEPATH') OR exit('No direct script access allowed');

class HealthmallModel extends CI_Model {
    
      
    public function get_api_version() {
        /*$api_url = "http://sandboxapi.medicalwale.com/v52/";*/
       $api_url = "https://live.medicalwale.com/v70/";
        return $api_url;
    }
     public function get_api_python() {
       
        /* $api_url="https://dhlr-bot.medicalwale.com/ads/sandbox/handpicked/";*/
         $api_url="https://dhlr-bot.medicalwale.com/ads/handpicked/";
       
        return $api_url;
    }
    function number_format_short( $n, $precision = 1 ) {
  if ($n < 900) {
    // 0 - 900
    $n_format = number_format($n, $precision);
    $suffix = '';
  } else if ($n < 900000) {
    // 0.9k-850k
    $n_format = number_format($n / 1000, $precision);
    $suffix = 'K';
  } else if ($n < 900000000) {
    // 0.9m-850m
    $n_format = number_format($n / 1000000, $precision);
    $suffix = 'M';
  } else if ($n < 900000000000) {
    // 0.9b-850b
    $n_format = number_format($n / 1000000000, $precision);
    $suffix = 'B';
  } else {
    // 0.9t+
    $n_format = number_format($n / 1000000000000, $precision);
    $suffix = 'T';
  }
  // Remove unecessary zeroes after decimal. "1.0" -> "1"; "1.00" -> "1"
  // Intentionally does not affect partials, eg "1.50" -> "1.50"
  if ( $precision > 0 ) {
    $dotzero = '.' . str_repeat( '0', $precision );
    $n_format = str_replace( $dotzero, '', $n_format );
  }
  return $n_format . $suffix;
}
    
    public function top_menu($user_id){
        $data= array();
        $top_menu = $this->db->query("SELECT menu_id , title , image , type,b_image FROM `hm_top_menu_master` WHERE status = '1' ORDER BY `menu_order` ASC ")->result_array();
        $data['top_menu'] = $top_menu;
        $data['description'] = 'Type will decide where to redirect. 1 : Categories /get_categories; 2:brands; 3: offers; 4: try on ; 5 : deals ';
        return $data;
    }
    public function top_menu_brand($user_id){
        $data= array();
        $d=array();
        $top_menu = $this->db->query("SELECT menu_id , title , image , type,b_image FROM `hm_top_menu_master` WHERE status = '1' ORDER BY `menu_order` ASC ");
        $count = $top_menu->num_rows();
       if($count>0)
        {
             $d[]=array( "menu_id"=> "0",
                "title"=> "About Us",
                "image"=> "https://d2c8oti4is0ms3.cloudfront.net/images/assets/Healthmall/Icons/ABOUT_US.png",
                "b_image"=>"https://d2c8oti4is0ms3.cloudfront.net/images/assets/Healthmall/Icons/index_images.png",
                "type"=> "0"
                        );
         foreach ($top_menu->result_array() as $row) 
                 {
                    $menu_id= $row['menu_id'];
                    $title = $row['title'];
                    $image = $row['image'];  
                    $b_image = $row['b_image']; 
                    $type = $row['type'];
                    $d[] = array( "menu_id"=> $menu_id,
                "title"=> $title,
                "image"=> $image,
                "b_image"=>$b_image,
                "type"=> $type
                        );
                 }
        }         
        
       
        $data['top_menu'] = $d;
        
        return $data;
    }
    public function get_categories($user_id,$brand_id) {
       $wh="";
       if(!empty($brand_id) || $brand_id!='0')
       {
           $wh.="AND pd_added_v_id = '$brand_id'";
       }
       $results1=array();
       $results1['product_count']="0";
        $results = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE parent_cat_id='0'");
        foreach ($results->result_array() as $result) {
            $catId = $result['id'];
            $result_sub = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id` = '$catId'");
      $prods      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE pd_pc_id = '$catId' and pd_status='1' $wh ");
        $get_list   = $prods->row_array();
            $pro      = $get_list['p_count'];
            if ($pro>0) {
                $data_subcat_all = [];
                $dataSubcat['product_count']=0;
                foreach ($result_sub->result_array() as $subcat) {
                    $dataSubcatId = $subcat['id'];
                    $prods1      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE (pd_pc_id IN ('$dataSubcatId') OR `pd_psc_id` IN ('$dataSubcatId')) and pd_status='1'  $wh ");
                $get_list1    = $prods1->row_array();
                    $pro1       = $get_list1['p_count'];
          $subcat_photo = $subcat['photo'];
          $subcat_photo = $subcat['photo'];
          $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
          $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                    $results1 = $this->HealthmallModel->get_subcat($dataSubcatId,$brand_id);
                    $dataSubcat['pc_id'] = $subcat['id'];
                    $dataSubcat['pc_name'] = $subcat['cat_name'];
                    $dataSubcat['pc_photo'] = $subcat_photo;
                    $dataSubcat['cat_count'] = $pro1+$results1['product_count'];
                    $dataSubcat['product_count'] += $pro1+$results1['product_count'];
                   // $dataSubcat['product_count'] += $pro1;
                    $dataSubcat['subcategory'] = $results1['data'];
                    $data_subcat_all[] = $dataSubcat;
                }
        
        $result_photo = $result['photo'];
        $result_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
        $result_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
                $cat['pc_id'] = $result['id'];
                $cat['pc_name'] = $result['cat_name'];
                $cat['pc_photo'] = $result_photo;
                $cat['cat_count'] = $dataSubcat['product_count'];
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

   public function get_subcat($dataSubcatId,$brand_id) {
        $data = array();
        $wh="";
       if(!empty($brand_id) || $brand_id!='0')
       {
           $wh.="AND pd_added_v_id = '$brand_id'";
       }
        $results = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id`='$dataSubcatId'");
         $cat['product_count']=0;
        foreach ($results->result_array() as $result) { 
            $catId = $result['id'];
            $result_sub = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id` = '$catId'");
      $prods      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE (pd_pc_id = '$catId' OR pd_psc_id = '$catId') and pd_status='1' $wh ");
        $get_list   = $prods->row_array();
            $pro      = $get_list['p_count'];     
            $data_subcat_all = [];
           
            if ($pro>0) {
                 $dataSubcat['product_count'] = 0;
                foreach ($result_sub->result_array() as $subcat) {
                    
                    $dataSubcatIdInSub = $subcat['id'];
                    $prods1      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE (pd_pc_id IN ('$dataSubcatIdInSub') OR `pd_psc_id` IN ('$dataSubcatIdInSub')) and pd_status='1'  $wh ");
                $get_list1    = $prods1->row_array();
                    $pro1       = $get_list1['p_count'];
          $subcat_photo = $subcat['photo'];
          $subcat_photo = $subcat['photo'];
          $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
          $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                    $results1 = $this->HealthmallModel->get_subcat($dataSubcatIdInSub,$brand_id);
                    $dataSubcat['pc_id'] = $subcat['id'];
                    $dataSubcat['pc_name'] = $subcat['cat_name'];
                    $dataSubcat['pc_photo'] = $subcat_photo;
                    
                    $dataSubcat['product_count'] += $pro1 + $results1['product_count'];
                    $dataSubcat['cat_count'] = $pro1;
                    $dataSubcat['subcategory'] = $results1['data'];
                    
                    $data_subcat_all[] = $dataSubcat;
                }
        $result_photo = $result['photo'];
        $result_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
        $result_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$result_photo);
                $cat['pc_id'] = $result['id'];
                $cat['pc_name'] = $result['cat_name'];
                $cat['pc_photo'] = $result_photo;
                
                $cat['product_count'] += $pro+$dataSubcat['product_count'];
                $cat['cat_count'] = $pro;
                $cat['subcategory'] = $data_subcat_all;
                $data[] = $cat;
            }
        }
        $res = array(
            "data" => $data,
            "product_count"=>$cat['product_count']
        );
        return $res;
    }
    
    public function get_brands($user_id, $page_no, $per_page,$search){
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
        
        $data = $withrank = $withoutrank = $finalDataNew = $finalData =  array();
        $wh="";
        if(!empty($search))
        {
            // * A- Z    # Rank 0 last // alphabet starting 1 letter
            if($search=="*")
            {
                $wh .="ORDER BY vd.v_name asc"; 
            }
            else
            {
            $wh .="and vd.v_name LIKE '$search%' ";
            }
        }
        else
        {
            $wh .="ORDER BY rank=0,rank,vd.v_name";
        }
        
       $finalDataNew = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo FROM vendor_details_hm as vd inner join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id  WHERE `v_status` = '1' $wh ")->result_array();
       
        $withrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd inner join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE vd.v_status = '1' $wh  $limit")->result_array();
        $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
       // $data['data'] = array_merge($withrank, $withoutrank);
        $data['data'] = $withrank;
        return $data;
        
  }
    public function get_offers($user_id){
        
        $allOffers = array();
        $today = $created_date = date("Y-m-d");
      $offers = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND status = '0' AND end_date >= '$today'");
      foreach ($offers->result_array() as $offer) {
         // $offer[''] = "100";
         $offer_id = $offer['id']; 
          $result = $this->HealthmallModel->get_offer_products($offer_id);
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
          
          }
          
      }
      $res['status'] =  200;
      $res['message'] =  "success";
      $res['data'] =  $allOffers;
      return $res;
  }
  
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
     
      if($vendor_offers_nums > 0){
       if($offer['offer_on'] == '1' ){
              $str = $offer['offer_on_ids'];
              $afterstr =  (explode(",",$str));
              for($i=0;$i<count($afterstr);$i++){
                  $pdId = $afterstr[$i];
                $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_pc_id = '$pdId' $vendorQuery";
               
                $query = $this->db->query($stmt);
                  $rows = $query->result_array();
                  $rows_num = $query->num_rows();
                 foreach($rows as $row){
                      $results = $this->HealthmallModel->get_product_offer($row['pd_id']);
                      
                      foreach($results as $result){
                        
                          if($result['id'] == $offer_id){
                              $row['offer'] = $result;
                          }
                      }
                      
                      $all_products[] = $row;
                  }
                  
                  $all_products_count = $all_products_count + $rows_num; 
                  }
         
       } 
       
       if($offer['offer_on'] == '2' ){
              $str = $offer['offer_on_ids'];
              $afterstr =  (explode(",",$str));
              for($i=0;$i<count($afterstr);$i++){
                  $pdId = $afterstr[$i];
                $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_psc_id = '$pdId' $vendorQuery";
               
                $query = $this->db->query($stmt);
                  $rows = $query->result_array();
                  $rows_num = $query->num_rows();
                 // print_r($rows); die();
                  foreach($rows as $row){
                      $results = $this->HealthmallModel->get_product_offer($row['pd_id']);
                      
                      foreach($results as $result){
                        
                          if($result['id'] == $offer_id){
                              $row['offer'] = $result;
                          }
                      }
                      
                      $all_products[] = $row;
                  }
                  
                  $all_products_count = $all_products_count + $rows_num; 
               
          
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
                      $results = $this->HealthmallModel->get_product_offer($row['pd_id']);
                      
                      foreach($results as $result){
                          if($result['id'] == $offer_id){
                              $row['offer'] = $result;
                          }
                      }
                      
                      $all_products[] = $row;
                  }
                  
                  $all_products_count = $all_products_count + $rows_num; 
                
          
              }
            
       } 
       
    
        $offer['products'] = $all_products;
         $offer['products_count'] = $all_products_count;
        $res = $offer;
      } else {
          $res = array();
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
              
              $vendor_id =  $query1['pd_added_v_id'];
    } 
    
//    echo  $vendor_id; die();
    
    
      $offersProd = $this->db->query("SELECT * FROM `vendor_offers` WHERE listing_id='34' AND end_date >= '$today' AND (`vendor_id` = '$vendor_id' || `vendor_id` = '') ");
      date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
      foreach ($offersProd->result_array() as $offer){
      
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
                                
                                $prod_best_price =  number_format((float)$prod_best_price, 2, '.', '');
                                $offer['offer_best_price'] = intval (round($prod_best_price));
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
                                
                                $offer['offer_best_price'] = intval (round($prod_best_price));
                               
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
                                $offer['offer_best_price'] = intval (round($prod_best_price));
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
                                
                                $offer['offer_best_price'] = intval (round($prod_best_price));
                               
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
                                $offer['offer_best_price'] = intval (round($prod_best_price));
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
                                
                                $offer['offer_best_price'] = intval (round($prod_best_price));
                               
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
    
      return $offers;
 }
 
        public function get_brand_images($user_id,$vid)
        {
           $d=array();
            $bannerImages1 = $this->db->query("SELECT * FROM `vendor_details_hm` where v_id='$vid'");
         $count1 = $bannerImages1->num_rows();
           if($count1>0)
              {
                $row1=$bannerImages1->row_array(); 
                $id1= $row1['v_id'];
                $banner_image1 = $row1['v_company_logo'];
                $v_id1 = $row1['v_id'];  
                $d[] = array( "id"=> $id1,
                              "banner_image"=> $banner_image1,
                              "v_id"=> $v_id1
                            );
                
        } 
       
         $bannerImages = $this->db->query("SELECT * FROM `banner_images` WHERE v_id='$vid' and `status` = 1 ORDER BY `id` DESC");
         $count = $bannerImages->num_rows();
           if($count>0)
              {
                foreach ($bannerImages->result_array() as $row) 
                        {
                            $id= $row['id'];
                            $banner_image = $row['banner_image'];
                            $v_id = $row['v_id'];  
                            $d[] = array( "id"=> $id,
                                          "banner_image"=> $banner_image,
                                          "v_id"=> $v_id
                                        );
                 }
        }  
            
        
        
       $data['banner']=$d;
       return $data;
      }
   
   public function get_vendor_detail($user_id,$vid){
      $res = array();
    
    $stmt = $this->db->query("SELECT vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_min_order,vd.v_company_logo,vd.v_email,vd.v_landno,vd.v_phoneno,vd.v_address1,vd.v_address2,vd.v_city,vd.v_state,vd.v_country,vd.v_pincode,vd.v_lat,vd.v_long,vd.v_company_name,vd.v_delivery_charge,vd.v_min_order,vd.v_delivery_days,vd.v_delivery_time,vd.v_cashback,vd.tnc,vd.aboutus,vd.return_policy,vd.return_days
        FROM vendor_details_hm vd 
        WHERE vd.v_id ='$vid'");
        $count = $stmt->num_rows();
        if($count>0)
          {
       $query = $stmt->row_array();
       $days=$query['v_delivery_days'];
       if(empty($days))
       {
           $days="0";
       }
       $time=$query['v_delivery_time'];
       if(empty($time))
       {
           $time="00:00:00";
       }
       $res[] = array( "v_id"=> $vid,
                          "name"=> $query['v_company_name'],
                          "logo"=> $query['v_company_logo'],
                          "delivery_charge" => $query['v_delivery_charge'],
                          "min_order"=>$query['v_min_order'],
                          "delivery_days"=>$days,
                          "delivery_time"=>$time,
                          "cashback"=>$query['v_cashback'],
                          "aboutus"=>$query['aboutus'],
                          "return_policy"=>$query['return_policy'],
                          "return_days"=>$query['return_days'],
                          "tnc"=>$query['v_cashback']
                         );
          }
         
    return $res;
  
  }
  
public function get_product_list($brands,$page_no,$per_page,$category_ids,$sort,$user_id,$price_min,$price_max,$offer_count,$best_seller,$gender){
    
       
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
        if(!empty($brands))
          {
            $brnd = $brands;
          } 
        else 
         {
            $allBrands =  $this->db->query("SELECT `v_id` FROM `vendor_details_hm` WHERE `v_status` = 1")->result_array();
            foreach($allBrands as $brand)
                    {
                     $brnd[] = $brand['v_id'];
                    }
            $brnd = implode(",",$brnd);
          }
        if(!empty($category_ids))
          {
            $catFilterArray = explode(",",$category_ids);
            $allSubcategories[] = $catFilterArray;
            $subCatsProducts=array();
            foreach($catFilterArray as $catId)
                  {
                    $subCatsProducts1 =  $this->HealthmallModel->get_subcat_id($catId);
                    
                    if(sizeof($subCatsProducts1) > 0)
                              { 
                                $subCatsProducts = call_user_func_array("array_merge", $subCatsProducts1);
                              } 
                              
                    $allSubcategories[] = $subCatsProducts;
                  }
            $allSubcategories = call_user_func_array("array_merge", $allSubcategories);
            
            $all=implode(",",$allSubcategories);
            
          } 
        else 
         {
            $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
            foreach($allcats as $cat)
                  {
                     $cats[] = $cat['id'];
                  }  
            $all = implode(",",$cats);
        }
        
        $sort_a_zh = "";
        if(!empty($sort))
        {
            if($sort=="1")
            {
                $sort_a_zh .= "ORDER BY pd_name ASC";
            }
            elseif($sort=="2")
            {
                $sort_a_zh .= "ORDER BY pd_name DESC";
            }
            elseif($sort=="3")
            {
                $sort_a_zh .= "ORDER BY pd_vendor_price ASC";
            }
            elseif($sort=="4")
            {
                $sort_a_zh .= "ORDER BY pd_vendor_price DESC";
            }
        }
        else
        {
           $sort_a_zh .= ""; 
        }
        //column_name BETWEEN value1 AND value2
        $wh_price ="";
        if(!empty($price_min) and !empty($price_max))
        {
           $wh_price .="AND pd_vendor_price BETWEEN '$price_min' AND '$price_max'";
        }
        elseif(!empty($price_min) and empty($price_max))
        {
            $wh_price .="AND pd_vendor_price >= '$price_min'";
        }
        elseif(empty($price_min) and !empty($price_max))
        {
            $wh_price .="AND pd_vendor_price <= '$price_max'";
        }
        
         $wh_best_seller ="";
        if(!empty($best_seller))
        {
           $wh_best_seller .="AND pd_best_seller > 0";
        }
        else
        {
            $wh_best_seller .="";
        }
        $wh_gender = "";
        
        if(!empty($gender))
        {
           $wh_gender .="AND gender_type IN (".$gender.")";
        }
        else
        {
            $wh_gender .="";
        }
        $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all.")) AND pd_status = 1 $wh_price $wh_best_seller $wh_gender")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))  AND pd_status = 1 $wh_price $wh_best_seller $wh_gender $sort_a_zh $limit";
        $productArray = array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
              
            foreach($query->result_array() as $q)
                   {
                        $deal_array=array();
                      $p_id       = $q['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id' AND try_on='0'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id' AND try_on='0'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = $checkOffers;
                     
                      $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                     
                      $sku=$q['SKU_code1'];
                      $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                      
                          $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                    
                   
             }
          }       
   
         
        
      $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
  
        
        return $data; 
       
  }
  
  public function get_deal_price($p_id_old)
  {
       $data=array();
        $date = date('Y-m-d');
      $deal_of_day = $this->db->query("SELECT * FROM `hm_deal_of_the_day` WHERE FIND_IN_SET('" . $p_id_old . "', pid) and ddate='$date'");
        $count = $deal_of_day->num_rows();
        if($count>0)
        {
            
            foreach($deal_of_day->result_array() as $deal_row)
            {
            $p_id=$deal_row['pid'];
            $v_id=$deal_row['vid'];
            $c_id=$deal_row['cid'];
            $day=date('Y-m-d H:i:s',strtotime($deal_row['ddate'] . "+1 days"));
            $datetime1 = date('Y-m-d H:i:s');
            $p_id_new=explode(",",$p_id);
            if(in_array($p_id_old,$p_id_new))
            {
            $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_id='$p_id_old' AND pd_status = 1 ";
            $query = $this->db->query($finalData);
            $rows_num = $query->num_rows();
            if($rows_num > 0)
               {
                   $q=$query->row_array();
                   $deal_price=$q['deal_price'];
                   $data[]=array('deal_type'=>"1",
                                  'deal_id'=>$deal_row['id'],
                                  'start_time'=>$datetime1,
                                  'end_time'=>$day,
                                  'text1'=> $deal_row['text'],
                                  "text2"=> $deal_row['offtext'],
                                  'deal_price'=>intval (round($deal_price)));   
               }  
            }
            }
               
        }  
        
        $deal_of_day = $this->db->query("SELECT * FROM `hm_season` WHERE FIND_IN_SET('" . $p_id_old . "', pid) and  '$date' BETWEEN start_date AND end_date");
        $count = $deal_of_day->num_rows();
        if($count>0)
        {
            
            foreach($deal_of_day->result_array() as $deal_row)
            {
            $p_id=$deal_row['pid'];
            $v_id=$deal_row['vid'];
            $c_id=$deal_row['cid'];
            $day=date('Y-m-d H:i:s',strtotime($deal_row['end_date'] . "+1 days"));
            $datetime1 = date('Y-m-d H:i:s');
            $p_id_new=explode(",",$p_id);
            if(in_array($p_id_old,$p_id_new))
            {
            $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_id='$p_id_old' AND pd_status = 1 ";
            $query = $this->db->query($finalData);
            $rows_num = $query->num_rows();
            if($rows_num > 0)
               {
                   $q=$query->row_array();
                   $deal_price=$q['season_price'];
                   $data[]=array('deal_type'=>"2",
                                  'deal_id'=>$deal_row['id'],
                                 'start_time'=>$datetime1,
                                 'end_time'=>$day,
                                 'text1'=> $deal_row['text'],
                                  "text2"=> $deal_row['season_name'],
                                 'deal_price'=>intval (round($deal_price)));   
               }  
            }
            }
               
        }  
        
        
        
      return $data;
      
  }
  	public function get_product_list_deal($page_no,$per_page,$user_id,$deal_id,$type)
	{
	    $data=array();
	    if($type=="1")
	    {
	    $deal_of_day = $this->db->query("SELECT * FROM `hm_deal_of_the_day` WHERE id = '$deal_id' ORDER BY `id` ASC LIMIT 4");
        $count = $deal_of_day->num_rows();
        if($count>0)
        {
            
            $deal_row=$deal_of_day->row_array();
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $p_id=$deal_row['pid'];
            $v_id=$deal_row['vid'];
            $c_id=$deal_row['cid'];
            $day=date('Y-m-d H:i:s',strtotime($deal_row['ddate'] . "+1 days"));
            $datetime1 = date('Y-m-d H:i:s');
            $brand_id="";
            if(!empty($v_id))
          {
            $brnds = $v_id;
            $brand_id .="AND pd_added_v_id IN (".$brnds.")";
          } 
        else 
         {
             $brand_id .="";
         }
         $cat_id="";
          if(!empty($c_id))
          {
            $all = $c_id;
            $cat_id .="AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))";
          } 
        else 
         {
            $cat_id .="";
          }
            $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_id IN (".$p_id.") $brand_id $cat_id  AND pd_status = 1")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_id IN (".$p_id.") $brand_id $cat_id AND pd_status = 1 ORDER BY 	pd_quantity=0,pd_quantity,pd_id  $limit";
        $productArray = array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
            foreach($query->result_array() as $q)
                   {
    	                $p_id       = $q['pd_id'];
    	                $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
    	                $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
	                    $offers = $checkOffers;
    	               
    	               
    	               
    	               $sku=$q['SKU_code1'];
    	               $deal_price=$q['deal_price'];
    	                $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                       
	                        $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 "deal_price"=>intval (round($deal_price)),
                                                 "deal_status"=>"1"
                                                );  
	                  
    	             
    	       }
          }       
	 
	       
        
	    $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['start_time'] = $datetime1;
        $data['end_time'] = $day;
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
               
        } 
	    }
	    elseif($type=="2")
	    {
	        $deal_of_day = $this->db->query("SELECT * FROM `hm_season` WHERE id = '$deal_id' ORDER BY `id` ASC LIMIT 4");
        $count = $deal_of_day->num_rows();
        if($count>0)
        {
            
            $deal_row=$deal_of_day->row_array();
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $p_id=$deal_row['pid'];
            $v_id=$deal_row['vid'];
            $c_id=$deal_row['cid'];
            $day=date('Y-m-d H:i:s',strtotime($deal_row['end_date'] . "+1 days"));
            $datetime1 = date('Y-m-d H:i:s');
            $brand_id="";
            if(!empty($v_id))
          {
            $brnds = $v_id;
            $brand_id .="AND pd_added_v_id IN (".$brnds.")";
          } 
        else 
         {
             $brand_id .="";
         }
         $cat_id="";
          if(!empty($c_id))
          {
            $all = $c_id;
            $cat_id .="AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))";
          } 
        else 
         {
            $cat_id .="";
          }
            $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_id IN (".$p_id.") $brand_id $cat_id  AND pd_status = 1")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_id IN (".$p_id.") $brand_id $cat_id AND pd_status = 1 ORDER BY 	pd_quantity=0,pd_quantity,pd_id $limit";
        $productArray = array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
            foreach($query->result_array() as $q)
                   {
    	                $p_id       = $q['pd_id'];
    	                $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
    	                $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
	                    $offers = $checkOffers;
    	               
    	               
    	               
    	                $sku=$q['SKU_code1'];
    	                $season_price=$q['season_price'];
    	                $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                       
	                        $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 "deal_price"=>intval (round($season_price)),
                                                 "deal_status"=>"1",
                                                );  
	                  
    	             
    	       }
          }       
	 
	       
        
	    $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['start_time'] = $datetime1;
        $data['end_time'] = $day;
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
               
        } 
	    }
	     elseif($type=="3")
	    {
	        $deal_of_day = $this->db->query("SELECT * FROM `hm_under_product` WHERE id = '$deal_id' ORDER BY `id` ASC LIMIT 4");
        $count = $deal_of_day->num_rows();
        if($count>0)
        {
            
            $deal_row=$deal_of_day->row_array();
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $d_price=$deal_row['price'];
            $e_price=$deal_row['start_price'];
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $day=date('Y-m-d H:i:s');
            $datetime1 = date('Y-m-d H:i:s');
            
            $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_status = 1 AND pd_vendor_price BETWEEN $e_price AND $d_price  ")->result_array();
            $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_status = 1 AND pd_vendor_price BETWEEN $e_price AND $d_price ORDER BY 	pd_quantity=0,pd_quantity,pd_id $limit";
            $productArray = array();
            $offers=array();
            $query = $this->db->query($finalData);
            $rows_num = $query->num_rows();
            if($rows_num > 0)
              {
                foreach($query->result_array() as $q)
                       {
        	                $p_id       = $q['pd_id'];
        	                $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                            $cart = $checkAvailable->num_rows();
                            $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                            $fav = $checkAvailable1->num_rows();
                            
        	                $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
    	                    $offers = $checkOffers;
        	               
        	               
        	               
        	                $sku=$q['SKU_code1'];
        	                $season_price=$q['season_price'];
        	                $cat_id     = $q['pd_pc_id'];
                            $cat_sub_id = $q['pd_psc_id'];  
                            $v_id       = $q['pd_added_v_id'];
                            $manuf_id   = $q['manuf_id'];
                            $brand_name = $q['brand_name'];
                            $pd_name    = $q['pd_name'];
                            $pd_photo_1 = $q['pd_photo_1'];
                            $pd_mrp_price =$q['pd_mrp_price'];
                            $pd_vendor_price =$q['pd_vendor_price'];
                            $pd_quantity =$q['pd_quantity'];
                            $discount= $q['pd_discount_percentage'];
                            $rating = $q['pd_overall_ratting'];
                            $view_count = $q['pd_view_count'];
                            $pd_status = $q['pd_status'];
                            $best_seller = $q['pd_best_seller'];
                            $item_type = $q['item_type'];
                            $color = $q['color'];
                            $size  = $q['size'];
                            $flavor = $q['flavor'];
                            $gender_type  = $q['gender_type'];
                            $speciality = $q['speciality'];
                            $is_featured  = $q['is_featured'];
                            $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                            if(empty($pd_quantity))
                            {
                                $pd_quantity="0";
                            }
                            if(empty($discount))
                            {
                                $discount="0";
                            }
                            if(empty($manuf_id))
                            {
                                $manuf_id="0";
                            }
                           $variable_colors=array(); 
                           $variable_sizes=array();
                           $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                           $rows_num1 = $vQuery->num_rows();
                           if($rows_num1 > 0)
                             {
                                  foreach($vQuery->result_array() as $v)
                                  {
                                    $c = $v['color'];
                                    $s = $v['size'];
                                    $vp_id = $v['variable_pd_id'];
                                    if($c != null && $c != '')
                                        {
                                            $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                            foreach($colorQ->result_array() as $colorRows)
                                               {
                                                    $colorValue= $colorRows['color_code'];
                                                    $variable_colors[] = $colorValue;
                                                }    
                                        }
                                    if($s != null && $s != '')
                                        {
                                            $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                            foreach($sizeQ->result_array() as $sizeRows)
                                                    {
                                                     $sizeValue = $sizeRows['size_name'];
                                                     $variable_sizes[] = $sizeValue;
                                                    }
                                        }
                                  }
                             }
                             else
                             {
                                if($color != null && $color != '')
                                        {
                                            $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                            foreach($colorQ->result_array() as $colorRows)
                                               {
                                                    $colorValue= $colorRows['color_code'];
                                                    $variable_colors[] = $colorValue;
                                                }    
                                        }
                                if($size != null && $size != '')
                                        {
                                             $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                            foreach($sizeQ->result_array() as $sizeRows)
                                                    {
                                                     $sizeValue = $sizeRows['size_name'];
                                                     $variable_sizes[] = $sizeValue;
                                                    }   
                                        }        
                                
                             }
                            if(sizeof($variable_colors) <= 0)
                            {
                                $colors_final="";
                            }
                            else
                            {
                                $colors_final=implode(",",$variable_colors);
                            }
                            
                            if(sizeof($variable_sizes) <= 0)
                            {
                                $size_final="";
                            }
                            else
                            {
                                $size_final=implode(",",$variable_sizes);
                            }
                           
    	                        $productArray[] = array( "p_id"=> $p_id,
                                                     "cat_id"=> $cat_id,
                                                     "cat_sub_id"=> $cat_sub_id,
                                                     "v_id"=> $v_id,
                                                     "manuf_id"=>$manuf_id,
                                                     "sku"=>$sku,
                                                     "brand_name"=>$brand_name,
                                                     "product_name" =>$pd_name,
                                                     "front_photo" => $pd_photo_1,
                                                     "mrp"=>$pd_mrp_price,
                                                     "vendor_price"=>$pd_vendor_price,
                                                     "quantity"=>$pd_quantity,
                                                     "discount_percentage"=>$discount,
                                                     "overall_ratting"=>$rating,
                                                     "view_count" => $count_view,
                                                     "status" => $pd_status,
                                                     "best_seller"=>$best_seller,
                                                     "item_type"=>$item_type,
                                                     "color"=>$colors_final,
                                                     "size"=>$size_final,
                                                     "flavor"=>$flavor,
                                                     "gender_type" => $gender_type,
                                                     "speciality" => $speciality,
                                                     "is_featured"=>$is_featured,
                                                     "offers"=>$offers,
                                                     "is_fav"=>$fav,
                                                     "is_cart" => $cart,
                                                     "deal_price"=>intval (round($season_price)),
                                                     "deal_status"=>"0",
                                                    );  
    	                  
        	             
        	       }
          }       
	 
	       
        
	    $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['start_time'] = $datetime1;
        $data['end_time'] = $day;
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
               
        } 
	    }
	      elseif($type=="4")
	    {
	    $deal_of_day = $this->db->query("SELECT * FROM `hm_gender_master` WHERE id = '$deal_id' ORDER BY `id` ASC LIMIT 4");
        $count = $deal_of_day->num_rows();
        if($count>0)
        {
            
            $deal_row=$deal_of_day->row_array();
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $d_id=$deal_row['id'];
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $day=date('Y-m-d H:i:s');
            $datetime1 = date('Y-m-d H:i:s');
            $allBrands =  $this->db->query("SELECT `v_id` FROM `vendor_details_hm` WHERE `v_status` = 1")->result_array();
            foreach($allBrands as $brand)
                    {
                     $brnd[] = $brand['v_id'];
                    }
            $brnd = implode(",",$brnd);
          
            $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
            foreach($allcats as $cat)
                  {
                     $cats[] = $cat['id'];
                  }  
            $all = implode(",",$cats);
      
        
            
            
            
            $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))  AND  gender_type IN (".$d_id.") AND pd_status = 1")->result_array();
            $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))  AND gender_type IN (".$d_id.") AND pd_status = 1 ORDER BY 	pd_quantity=0,pd_quantity,pd_id $limit";
            $productArray = array();
            $offers=array();
            $query = $this->db->query($finalData);
            $rows_num = $query->num_rows();
            if($rows_num > 0)
              {
                foreach($query->result_array() as $q)
                       {
        	                $p_id       = $q['pd_id'];
        	                $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                            $cart = $checkAvailable->num_rows();
                            $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                            $fav = $checkAvailable1->num_rows();
                            
        	                $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
    	                    $offers = $checkOffers;
        	               
        	               
        	               
        	                $sku=$q['SKU_code1'];
        	                $season_price=$q['season_price'];
        	                $cat_id     = $q['pd_pc_id'];
                            $cat_sub_id = $q['pd_psc_id'];  
                            $v_id       = $q['pd_added_v_id'];
                            $manuf_id   = $q['manuf_id'];
                            $brand_name = $q['brand_name'];
                            $pd_name    = $q['pd_name'];
                            $pd_photo_1 = $q['pd_photo_1'];
                            $pd_mrp_price =$q['pd_mrp_price'];
                            $pd_vendor_price =$q['pd_vendor_price'];
                            $pd_quantity =$q['pd_quantity'];
                            $discount= $q['pd_discount_percentage'];
                            $rating = $q['pd_overall_ratting'];
                            $view_count = $q['pd_view_count'];
                            $pd_status = $q['pd_status'];
                            $best_seller = $q['pd_best_seller'];
                            $item_type = $q['item_type'];
                            $color = $q['color'];
                            $size  = $q['size'];
                            $flavor = $q['flavor'];
                            $gender_type  = $q['gender_type'];
                            $speciality = $q['speciality'];
                            $is_featured  = $q['is_featured'];
                            $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                            if(empty($pd_quantity))
                            {
                                $pd_quantity="0";
                            }
                            if(empty($discount))
                            {
                                $discount="0";
                            }
                            if(empty($manuf_id))
                            {
                                $manuf_id="0";
                            }
                           $variable_colors=array(); 
                           $variable_sizes=array();
                           $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                           $rows_num1 = $vQuery->num_rows();
                           if($rows_num1 > 0)
                             {
                                  foreach($vQuery->result_array() as $v)
                                  {
                                    $c = $v['color'];
                                    $s = $v['size'];
                                    $vp_id = $v['variable_pd_id'];
                                    if($c != null && $c != '')
                                        {
                                            $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                            foreach($colorQ->result_array() as $colorRows)
                                               {
                                                    $colorValue= $colorRows['color_code'];
                                                    $variable_colors[] = $colorValue;
                                                }    
                                        }
                                    if($s != null && $s != '')
                                        {
                                            $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                            foreach($sizeQ->result_array() as $sizeRows)
                                                    {
                                                     $sizeValue = $sizeRows['size_name'];
                                                     $variable_sizes[] = $sizeValue;
                                                    }
                                        }
                                  }
                             }
                             else
                             {
                                if($color != null && $color != '')
                                        {
                                            $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                            foreach($colorQ->result_array() as $colorRows)
                                               {
                                                    $colorValue= $colorRows['color_code'];
                                                    $variable_colors[] = $colorValue;
                                                }    
                                        }
                                if($size != null && $size != '')
                                        {
                                             $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                            foreach($sizeQ->result_array() as $sizeRows)
                                                    {
                                                     $sizeValue = $sizeRows['size_name'];
                                                     $variable_sizes[] = $sizeValue;
                                                    }   
                                        }        
                                
                             }
                            if(sizeof($variable_colors) <= 0)
                            {
                                $colors_final="";
                            }
                            else
                            {
                                $colors_final=implode(",",$variable_colors);
                            }
                            
                            if(sizeof($variable_sizes) <= 0)
                            {
                                $size_final="";
                            }
                            else
                            {
                                $size_final=implode(",",$variable_sizes);
                            }
                           
    	                        $productArray[] = array( "p_id"=> $p_id,
                                                     "cat_id"=> $cat_id,
                                                     "cat_sub_id"=> $cat_sub_id,
                                                     "v_id"=> $v_id,
                                                     "manuf_id"=>$manuf_id,
                                                     "sku"=>$sku,
                                                     "brand_name"=>$brand_name,
                                                     "product_name" =>$pd_name,
                                                     "front_photo" => $pd_photo_1,
                                                     "mrp"=>$pd_mrp_price,
                                                     "vendor_price"=>$pd_vendor_price,
                                                     "quantity"=>$pd_quantity,
                                                     "discount_percentage"=>$discount,
                                                     "overall_ratting"=>$rating,
                                                     "view_count" => $count_view,
                                                     "status" => $pd_status,
                                                     "best_seller"=>$best_seller,
                                                     "item_type"=>$item_type,
                                                     "color"=>$colors_final,
                                                     "size"=>$size_final,
                                                     "flavor"=>$flavor,
                                                     "gender_type" => $gender_type,
                                                     "speciality" => $speciality,
                                                     "is_featured"=>$is_featured,
                                                     "offers"=>$offers,
                                                     "is_fav"=>$fav,
                                                     "is_cart" => $cart,
                                                     "deal_price"=>intval (round($season_price)),
                                                     "deal_status"=>"0",
                                                    );  
    	                  
        	             
        	       }
          }       
	 
	       
        
	    $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['start_time'] = $datetime1;
        $data['end_time'] = $day;
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
               
        } 
	    }
	    elseif($type=="5"){
	        
	    
            $finalDataNew="0";
            
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
          
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $day=date('Y-m-d H:i:s');
            $datetime1 = date('Y-m-d H:i:s');
            $allBrands =  $this->db->query("SELECT `v_id` FROM `vendor_details_hm` WHERE `v_status` = 1")->result_array();
            foreach($allBrands as $brand)
                    {
                     $brnd[] = $brand['v_id'];
                    }
            $brnd = implode(",",$brnd);
          
            $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
            foreach($allcats as $cat)
                  {
                     $cats[] = $cat['id'];
                  }  
            $all = implode(",",$cats);
      
        
        $data= array();
        $gender2=array();
        date_default_timezone_set("Asia/Calcutta");
        $date=date('Y-m-d');
        $this11 = new \stdClass();
        $get_api_python = $this->get_api_python();
        $url=$get_api_python;
        $this11->user_id=$user_id;
        $this11->page_no=$page_no;
        $this11->page_size=$per_page;
        $data11 = json_encode($this11);
        $data12=healthwall_adver_crul($url,$data11);
        $data131 = json_decode($data12); 
         if(!empty($data131))
          {
		$re=$data131->data;
		
		 $store_count=count($finalDataNew);
            $last_page = ceil($store_count/$per_page);
            $data['status'] = 200;
            $data['message'] = "Success";
            $data['start_time'] = $datetime1;
            $data['end_time'] = $day;
            $data['data_count'] = intval($store_count);
            $data['per_page'] = $per_page;
            $data['current_page'] = $page_no;
            $data['first_page'] = 1;
            $data['last_page'] = $last_page;
            $data['data'] = $re;
 
             
          }     
	 
	       
        
    	   
               
   
	    
	        
	        
	           
	        
	    }
	    elseif($type=="7")
	    {
	    $finalDataNew="0";
            
           
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
           
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
            $day=date('Y-m-d H:i:s');
            $datetime1 = date('Y-m-d H:i:s');
            $allBrands =  $this->db->query("SELECT `v_id` FROM `vendor_details_hm` WHERE `v_status` = 1")->result_array();
            foreach($allBrands as $brand)
                    {
                     $brnd[] = $brand['v_id'];
                    }
            $brnd = implode(",",$brnd);
          
            $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
            foreach($allcats as $cat)
                  {
                     $cats[] = $cat['id'];
                  }  
            $all = implode(",",$cats);
      
        
            
            
            
             $limit = "LIMIT 10 OFFSET 1"; 
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE tryon_status='1' AND  pd_status = 1 ORDER BY pd_quantity=0,pd_quantity,pd_id $limit";
        $productArray = array();
        $gender1=array();
        $gender2=array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
              
            foreach($query->result_array() as $q)
                   {
                        $deal_array=array();
    	                $p_id       = $q['pd_id'];
    	                $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id' AND try_on='1' ");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id' AND try_on='1'");
                        $fav = $checkAvailable1->num_rows();
                        
    	               
    	                $sku=$q['SKU_code1'];
    	                $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_photo_2 = $q['pd_photo_2'];
                        $pd_photo_3 = $q['pd_photo_3'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $try_price = $q['tryon_price'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                      
                        $sharePdName = $this->slugify($pd_name);
                        $share_url = "https://medicalwale.com/healthmall/product_details/".$sharePdName."/".$p_id;
                      
	                        $productArray[] = array( "p_id"=> $p_id,
                                                     "cat_id"=> $cat_id,
                                                     "cat_sub_id"=> $cat_sub_id,
                                                     "v_id"=> $v_id,
                                                     "manuf_id"=>$manuf_id,
                                                     "sku"=>$sku,
                                                     "brand_name"=>$brand_name,
                                                     "product_name" =>$pd_name,
                                                     "front_photo" => $pd_photo_1,
                                                     "try_on_image"=>$pd_photo_2,
                                                     "try_on_video"=>$pd_photo_3,
                                                     "try_on_price"=>$try_price,
                                                     "try_on_quantity"=>$pd_quantity,
                                                     "overall_ratting"=>$rating,
                                                     "view_count" => $count_view,
                                                     "status" => $pd_status,
                                                     "is_fav"=>$fav,
                                                     "is_cart" => $cart,
                                                     "description"=>"",
                                                     "share_url"=>$share_url
                                                );  
	                  
    	             
    	       }
    	       
    	       
          
          }       
	 
	       
        
	    $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['start_time'] = $datetime1;
        $data['end_time'] = $day;
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
               
         
	    }
	   
	    
	    return $data;
	    
	}
  
  
  
  public function get_subcat_id($dataSubcatId){
      $data = array();
      $results = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id`='$dataSubcatId'");

      foreach($results->result_array() as $result){
          
          $catId = $result['id'];
          
            $result_sub = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$catId'");
            $prods = $this->db->query("SELECT * from product_details_hm WHERE pd_pc_id = '$catId' OR pd_psc_id = '$catId'")->result_array();
            $data_subcat_all = [] ;
            $data_subcat_all[] = $catId;
            if(sizeof($prods) != 0){
                foreach($result_sub->result_array() as $subcat){
                    
                    $dataSubcatIdInSub = $subcat['id'];
                    $results1 = $this->HealthmallModel->get_subcat_id($dataSubcatIdInSub);
                    
                    $data_subcat_all[] = $subcat['id'];
                   
                }
          $data[] = $data_subcat_all;
            }
      }
    
      return $data;
  }
  
  public function get_product_details($pd_id,$user_id,$try_on)
  {
      $specifications =  $products = $offers = array();
      date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
      
      
      $query_for_count="SELECT pd_view_count from product_details_hm where pd_id=".$pd_id."";
    $view_count = $this->db->query($query_for_count)->num_rows();
    $view_data = $this->db->query($query_for_count)->row_array();
    $count=$view_data['pd_view_count']+1;
    
    $update_query="UPDATE product_details_hm set pd_view_count=".$count." where pd_id=".$pd_id."";
        $updatedata = $this->db->query($update_query);
      
      $stmt = "SELECT d.*,vd.v_id,vd.v_name,vd.v_company_logo,vd.v_delivery_charge,vd.cap_available,vd.cap_charge,avg(r.pr_rating) as ratting,COUNT(r.pr_rating) as total_rating,d.pd_view_count as total_view FROM `product_details_hm` d 
    LEFT JOIN product_reviews r ON d.pd_id = r.pr_pd_id 
    LEFT join vendor_details_hm vd 
        on vd.v_id=d.pd_added_v_id where d.pd_status = '1' AND d.pd_id ='$pd_id'";
      $query = $this->db->query($stmt);
       $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
        $query1=$query->row_array();
          $p_id       = $query1['pd_id'];
          $deal_array=array();
          if(!empty($p_id))
          {
                $cat_id     = $query1['pd_pc_id'];
                $cat_sub_id = $query1['pd_psc_id'];  
                $v_id       = $query1['pd_added_v_id'];
                $vendor_logo = $query1['v_company_logo'];
                $brand_name = $query1['brand_name'];
                $pd_name    = $query1['pd_name'];
                $pd_mrp_price =$query1['pd_mrp_price'];
                $pd_vendor_price =$query1['pd_vendor_price'];
                $cap_available =$query1['cap_available'];
                $rating = $query1['pd_overall_ratting'];
                $total_rating =$query1['total_rating'];
                $discount= $query1['pd_discount_percentage'];
                $view_count = $query1['pd_view_count'];
                $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                $total_view = $query1['total_view'];
                $count_total_view= $this->HealthmallModel->number_format_short( $total_view, $precision = 1 );
                $pd_quantity =$query1['pd_quantity'];
                $pd_status = $query1['pd_status'];                
                $best_seller = $query1['pd_best_seller'];                
                $item_type = $query1['item_type'];
                $gender_type = $query1['gender_type']; 
                $is_featured  = $query1['is_featured'];
                $speciality = $query1['speciality'];
                $short_desc  = $query1['pd_short_desc'];
                $long_desc = $query1['pd_long_desc'];
                $isVariable = $query1['variable_product'];
                $sku =$query1['SKU_code1'];
                $try_on_price = $query1['tryon_price'];
                
                
                $recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
      $recentlyViewedRows = $recentlyViewed->row_array();
      $recentlyViewedCount = $recentlyViewed->num_rows();
      $viewed_date = date('Y-m-d H:i:s');
      if($recentlyViewedCount > 0){
          $recentId = $recentlyViewedRows['id'];
          $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
      } else {
          $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `pd_pc_id`, `pd_psc_id`, `pd_added_v_id`, `brand_name`, `pd_name`,`viewed_date`) 
                                                            VALUES ('$user_id', '$pd_id','$cat_id','$cat_sub_id','$v_id','$brand_name','$pd_name', '$viewed_date')
          
          
          ");
      }
                
                if(empty($pd_quantity))
                    {
                        $pd_quantity="0";
                    }
                if(empty($discount))
                    {
                        $discount="0";
                    }
                if(empty($manuf_id))
                    {
                        $manuf_id="0";
                    }            
                $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id' AND try_on='0'");
                $cart = $checkAvailable->num_rows();
                $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id' AND try_on='0'");
                $fav = $checkAvailable1->num_rows();
                if($try_on=="1")
                {
                   $checkOffers=array(); 
                }
                else
                {
                $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                }
              $offers = $checkOffers;
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
                       $nv['value'] = $query1['require_temp'] . " 째C";
                       $s[] = $nv;
                }
                if(!empty($query1['max_temp'])){
                       $nv['name'] = "Max Temperature";
                       $nv['value'] = $query1['max_temp'] . " 째C";
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
              
            $specifications = $s;
            $image1=array();
            $pd_photo_1 = $query1['pd_photo_1'];
            if(!empty($pd_photo_1))
            {
             $image1 []= array('image'=>$pd_photo_1);
            }
            $pd_photo_2 = $query1['pd_photo_2'];
            if(!empty($pd_photo_2))
            {
             $image1 []= array('image'=>$pd_photo_2);
            }
            
            $pd_photo_3 = $query1['pd_photo_3'];
            if(!empty($pd_photo_3))
            {
            $image1 []= array('image'=>$pd_photo_3);
            }
            $pd_photo_4 = $query1['pd_photo_4'];
            if(!empty($pd_photo_4))
            {
            $image1 []= array('image'=>$pd_photo_4);
            }
            $variable_products=array();
           $products=array();
           $variable_products_info=array();
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
            
        
              
              if($try_on=="1")
              {
                 $checkOffers_deal=array(); 
              }
              else
              {
              $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
              }
                      $deal_array = $checkOffers_deal;
                      
              
              
              if($try_on=="1")
               {
                  $new_pd_vendor_price=$try_on_price;
                  $new_pd_mrp_price=$try_on_price;
               }
              else
              {
                $new_pd_vendor_price=$pd_vendor_price;
                $new_pd_mrp_price=$pd_mrp_price;
              }
              
                             $finalArray[] = array("product_name" =>$pd_name,
                                       "vendor_price"=>$new_pd_vendor_price,
                                       "mrp"=>$new_pd_mrp_price,
                                       "sku"=>$sku,
                                       "cap_available"=>$cap_available,
                                       "rating"=>$rating,
                                       "number_of_rating"=>$total_rating,
                                       "coupon_code"=>$offers,
                                       "product_image"=>$image1,
                                       "short_desc"=>$short_desc,
                                       "long_desc"=>$long_desc,
                                       "specification"=>$specifications,
                                       "vendor_id"=> $v_id,
                                       "vendor_name"=>$brand_name,
                                       "vendor_image"=>$vendor_logo,
                                       "discount_percentage"=>$discount,
                                       "view_count" => $count_view, 
                                       "is_fav"=>$fav,
                                       "is_cart" => $cart,
                                       "best_seller"=>$best_seller,
                                       "cat_id"=> $cat_id,
                                       "cat_sub_id"=> $cat_sub_id,
                                       "gender_type" => $gender_type,
                                       "item_type"=>$item_type,
                                       "is_featured"=>$is_featured,
                                       "quantity"=>$pd_quantity,
                                       "status" => $pd_status,
                                       "speciality" => $speciality,
                                       "variable_products"=>$products,
                                       "variable_products_info"=>$variable_products_info,
                                       "p_id"=> $p_id,
                                       
                                       'deal_offer'=>$deal_array
                                       );  
          }
          else
          {
              $finalArray=array();
          }
          }    
        
        
        
        
                        
    
            
            
  
      return $finalArray;
  }
    
    public function get_brand_details($category_ids,$user_id)
  {
     $data=array();
     $student_unique_arr=array();
      if(!empty($category_ids))
          {
            $catFilterArray = explode(",",$category_ids);
            $allSubcategories[] = $catFilterArray;
            $subCatsProducts=array();
            foreach($catFilterArray as $catId)
                  {
                    $subCatsProducts1 =  $this->HealthmallModel->get_subcat_id($catId);
                    
                    if(sizeof($subCatsProducts1) > 0)
                              { 
                                $subCatsProducts = call_user_func_array("array_merge", $subCatsProducts1);
                              } 
                              
                    $allSubcategories[] = $subCatsProducts;
                  }
            $allSubcategories = call_user_func_array("array_merge", $allSubcategories);
            
            $all=implode(",",$allSubcategories);
            
          } 
        else 
         {
            $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
            foreach($allcats as $cat)
                  {
                     $allSubcategories[] = $cat['id'];
                  }  
            //$all = implode(",",$cats);
        }
        for($i=0; $i < sizeof($allSubcategories); $i++)
        {
        $finalData = "SELECT DISTINCT pd_added_v_id  FROM `product_details_hm` WHERE (`pd_pc_id` IN (".$allSubcategories[$i].") OR `pd_psc_id` IN (".$allSubcategories[$i]."))  AND pd_status = 1";
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
        {
        
        foreach($query->result_array() as $q )
        {
            $v_id=$q['pd_added_v_id'];
            $finalData1 = "SELECT count(pd_added_v_id) as id  FROM `product_details_hm` WHERE pd_added_v_id='$v_id' and (`pd_pc_id` IN (".$allSubcategories[$i].") OR `pd_psc_id` IN (".$allSubcategories[$i]."))  AND pd_status = 1";
            $query1 = $this->db->query($finalData1);
            $q1=$query1->row_array();
            $ven="SELECT DISTINCT v_id, v_name, v_company_name, v_company_logo FROM vendor_details_hm where v_id='$v_id'";
            $qven1 = $this->db->query($ven);
            $q21=$qven1->row_array();
        
        
        $data []=array(
                 "cat_id"=>$allSubcategories[$i],
                 "v_id"=>$q['pd_added_v_id'],
                 "vendor_name"=>$q21['v_name'],
                 "vendor_company"=>$q21['v_company_name'],
                 "count_id"=>$q1['id']
            );
        }
        }
        }    
         
 $student_unique_arr = $this->HealthmallModel->unique_key($data,'v_id');
 
  
      return $student_unique_arr;
  }
  
  public function unique_key($array,$keyname){

 $new_array = array();
 foreach($array as $key=>$value){

   if(!isset($new_array[$value[$keyname]])){
     $new_array[$value[$keyname]] = $value;
   }

 }
 $new_array = array_values($new_array);
 return $new_array;
}

    public function add_to_wishlist($product_id,$type,$user_id,$try_on)
    {
        //0 means remove from whislit & 1 means add wishlist
      if($type=="0")
      {
        
            
          $checkAvailable = $this->db->query("DELETE FROM `customer_wishlist` WHERE `customer_id` = '$user_id' AND `product_id` = '$product_id' and try_on='$try_on' ");
            $affected_rows = $this->db->affected_rows();
            if($affected_rows > 0)
                {
                     $checkAvailable2 = $this->db->query("SELECT count(id) as id FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
                      $count2 = $checkAvailable2->num_rows();
                      $prod_wish = $checkAvailable2->row_array();
      $count_wish=$prod_wish['id'];
                 $res = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => "Successfully remove to interest list",
                        "wishlist_count"=>$count_wish
                         
                    );  
                } 
            else 
               {
                     $results = $this->db->query("INSERT INTO `customer_wishlist` (`product_id`, `customer_id`,`try_on`) VALUES ('$product_id', '$user_id','$try_on');");
         $insert_id = $this->db->insert_id(); 
         if($insert_id)
            {
                $checkAvailable2 = $this->db->query("SELECT count(id) as id FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
                      $count2 = $checkAvailable2->num_rows();
               $prod_wish = $checkAvailable2->row_array();
      $count_wish=$prod_wish['id'];
              $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to interest list",
                    "wishlist_count"=>$count_wish
                     
                );} 
          else 
             {
              $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );
          }
                }
      }
      elseif($type=="1")
      {
          
             $checkAvailable = $this->db->query("DELETE FROM `customer_wishlist` WHERE `customer_id` = '$user_id' AND `product_id` = '$product_id' and try_on='$try_on' ");
            $affected_rows = $this->db->affected_rows();
            if($affected_rows > 0)
                {          
         $results = $this->db->query("INSERT INTO `customer_wishlist` (`product_id`, `customer_id`,`try_on`) VALUES ('$product_id', '$user_id','$try_on');");
         $insert_id = $this->db->insert_id(); 
         if($insert_id)
            {
                $checkAvailable2 = $this->db->query("SELECT count(id) as id FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
                      $count2 = $checkAvailable2->num_rows();
               $prod_wish = $checkAvailable2->row_array();
      $count_wish=$prod_wish['id'];
              $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to interest list",
                    "wishlist_count"=>$count_wish
                     
                );} 
          else 
             {
              $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );
          }
                }
                else
                {
                     $results = $this->db->query("INSERT INTO `customer_wishlist` (`product_id`, `customer_id`,`try_on`) VALUES ('$product_id', '$user_id','$try_on');");
         $insert_id = $this->db->insert_id(); 
         if($insert_id)
            {
                $checkAvailable2 = $this->db->query("SELECT count(id) as id FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
                      $count2 = $checkAvailable2->num_rows();
               $prod_wish = $checkAvailable2->row_array();
      $count_wish=$prod_wish['id'];
              $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to interest list",
                    "wishlist_count"=>$count_wish
                     
                );} 
          else 
             {
              $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );
          }
                }
      }
      else
      {
        $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );  
      }
     
      return $res;
  }

public function get_user_wishlist($user_id,$per_page,$page_no){
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset";
        $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `customer_wishlist` WHERE `customer_id` = '$user_id'")->result_array();
        $checkAvailable = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
        
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
             $productArray = array();
        $offers=array();
            foreach($checkAvailable->result_array() as $prodInfo)
            {
                $product_id = $prodInfo['product_id'];
               
               
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_id = '$product_id' AND pd_status = 1 ";
       
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
            foreach($query->result_array() as $q)
                   {
                      $p_id       = $q['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = $checkOffers;
                     
                     
                     
                     $sku=$q['SKU_code1'];
                      $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                       
                       
                        $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                       
                          $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 "try_on"=>$prodInfo['try_on'],
                                                 'deal_offer'=>$deal_array
                                                );  
                    
                   
             }
          }       
   
            }

            $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
       
                $res = array(
                    "status" => 200,
                    "message" => "success",
                    "data_count"=>intval($store_count),
                    "per_page"=>$per_page,
                    "current_page"=>$page_no,
                    "first_page"=>1,
                    "last_page"=>$last_page,
                    "data" => $productArray
                     
                );
            
        } else {
            
             $res = array(
                "status" => 400,
                "message" => "interest list is empty"
            );
            
        
        }
        
      return $res;
  }
  
public function get_user_cart_wishlist_count($user_id)
{
   
   $checkAvailable2 = $this->db->query("SELECT sum(quantity) as qty FROM `user_cart` WHERE `customer_id` = '$user_id'");
   $count2 = $checkAvailable2->num_rows();
   if($count2 > 0)
   {
      $prod_data = $checkAvailable2->row_array();
      $count_qty=$prod_data['qty'];
      if(empty($count_qty))
      {
          $count_qty=0;
      }
      
   }
   else
   {
     $count_qty=0;  
   }
   
   $checkAvailable21 = $this->db->query("SELECT count(id) as id FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
   $count21 = $checkAvailable21->num_rows();
   if($count21 > 0)
   {
      $prod_wish = $checkAvailable21->row_array();
      $count_wish=$prod_wish['id'];
   }
   else
   {
     $count_wish=0;  
   }
   
   $res = array(
                        "status" => 200,
                        "message" => "success",
                        "wishlist_count" => "$count_wish",
                        "cart_count"=>"$count_qty"
                      
                    );
   
   return $res;
}
  
public function add_to_cart($user_id, $product_id, $quantity, $offer_id,$referal_code,$variable_pd_id,$sku,$type,$deal_id,$deal_type,$try_on){
      
      if($type=="1")
      {
          
          
          $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$product_id' AND `variable_pd_id` = '$variable_pd_id' AND `sku` = '$sku' AND `customer_id` = '$user_id' AND `try_on`='$try_on'");
            $count = $checkAvailable->num_rows();
            $count_qty=0;
            if($count > 0){
               
                $prevQuant = 0;
                $totalQuantity = $quantity;
                
                
                $results = $this->db->query("UPDATE `user_cart` SET `quantity` = '$totalQuantity', `offer_id` = '$offer_id',`referal_code` = $referal_code, `variable_pd_id`='$variable_pd_id',`sku` = '$sku',`deal_id`='$deal_id',`try_on`='$try_on',`deal_type`='$deal_type' WHERE `product_id` = '$product_id' AND `variable_pd_id`='$variable_pd_id' AND `sku` = '$sku' AND `customer_id` = '$user_id'");
                $checkAvailable2 = $this->db->query("SELECT sum(quantity) as qty FROM `user_cart` WHERE `customer_id` = '$user_id'");
                $count2 = $checkAvailable2->num_rows();
                $prod_data = $checkAvailable2->row_array();
                $count_qty=$prod_data['qty'];
                $res = array(
                    "status" => 201,
                    "message" => "success",
                    "description" => "Cart updated Successfully",
                    "cart_count"=>"$count_qty"
                );
            } else {
                $results = $this->db->query("INSERT INTO `user_cart` (`product_id`, `customer_id`, `quantity`, `offer_id`,`referal_code`,`variable_pd_id`,`sku`,`deal_id`,`deal_type`,`try_on`) VALUES ('$product_id', '$user_id', '$quantity', '$offer_id','$referal_code','$variable_pd_id','$sku','$deal_id','$deal_type','$try_on')");
              $insert_id = $this->db->insert_id(); 
              $checkAvailable2 = $this->db->query("SELECT sum(quantity) as qty FROM `user_cart` WHERE `customer_id` = '$user_id'");
                $count2 = $checkAvailable2->num_rows();
                $prod_data = $checkAvailable2->row_array();
              $count_qty=$prod_data['qty'];
              if(empty($count_qty))
                {
                    $count_qty=0;
                }
              if($insert_id){
                  $res = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => "Successfully added to Cart",
                        "cart_count"=>"$count_qty"
                      
                    );
              } else {
                  $res = array(
                        "status" => 400,
                        "message" => "fail",
                        "description" => "Something went wrong, please try again",
                        
                    );
              }
        }
            
      }
      elseif($type=="0")
      {
        $checkAvailable2 = $this->db->query("SELECT sum(quantity) as qty FROM `user_cart` WHERE `customer_id` = '$user_id'");
            $count2 = $checkAvailable2->num_rows(); 
            $fina=0;
        $checkAvailable = $this->db->query("DELETE FROM `user_cart` WHERE `customer_id` = '$user_id' AND `variable_pd_id` = '$variable_pd_id' AND `product_id` = '$product_id' AND `try_on`='$try_on'");
          $affected_rows = $this->db->affected_rows();
          if($affected_rows > 0)
            {
                $checkAvailable21 = $this->db->query("SELECT sum(quantity) as qty FROM `user_cart` WHERE `customer_id` = '$user_id'");
                $count2 = $checkAvailable21->num_rows(); 
                $prod_data1 = $checkAvailable21->row_array();
                $fina=$prod_data1['qty'];
                if(empty($fina))
                {
                    $fina=0;
                }
               $res = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => "Successfully removed",
                        "cart_count"=>"$fina"
                    );   
            } 
          else 
            {
               $res = array(
                        "status" => 400,
                        "message" => "fail",
                        "description" => "Something went wrong, please try again",
                        "cart_count"=>""
                    );
            }
          
          
          
      }
        else
      {
        $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );  
      }
        
        
      return $res;
  }

public function get_num_ratings($pro_id){
        
      $rat5 = $this->db->query("SELECT count(*) as rating5 FROM product_reviews WHERE pr_rating = 5 AND pr_pd_id = $pro_id")->row_array();
      $rat4 = $this->db->query("SELECT count(*) as rating4 FROM product_reviews WHERE pr_rating = 4 AND pr_pd_id = $pro_id")->row_array();
      $rat3 = $this->db->query("SELECT count(*) as rating3 FROM product_reviews WHERE pr_rating = 3 AND pr_pd_id = $pro_id")->row_array();
      $rat2 = $this->db->query("SELECT count(*) as rating2 FROM product_reviews WHERE pr_rating = 2 AND pr_pd_id = $pro_id")->row_array();
      $rat1 = $this->db->query("SELECT count(*) as rating1 FROM product_reviews WHERE pr_rating = 1 AND pr_pd_id = $pro_id")->row_array();
      $totalCount = $rat1['rating1'] + $rat2['rating2'] + $rat3['rating3'] + $rat4['rating4'] + $rat5['rating5'];
      $data[] = array(
                "product_id" => $pro_id,
                "count" => $totalCount,
                "rating1" => $rat1['rating1'],
                "rating2" => $rat2['rating2'],
                "rating3" => $rat3['rating3'],
                "rating4" => $rat4['rating4'],
                "rating5" => $rat5['rating5']
          ); 
      
      return $data;
  }

public function get_user_cart($user_id)
 {
        $var_prod_price = $var_ven_price = $oldId = 0;
        $prodName = "";
        $referal_code = $prod_data_all = $cart_data_all = $availableCartFull = $availableCart = $cart = $vendor = array();
        $cartQuantity = $delvChargeOld = 0;
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
                     $availableCart['deal_id'][] = $prodInfo['deal_id'];
                     $availableCart['deal_type'][] =$prodInfo['deal_type'];
                    
                    
                } else {
                    $availableCart['product_id'][] = $prodInfo['product_id'];
                    $availableCart['referal_code'][] = $referal_code;
                     $availableCart['offer_id'][] = $prodInfo['offer_id'];
                     $availableCart['quantity'][] =$prodInfo['quantity'];
                    $availableCart['variable_pd_id'][] =$prodInfo['variable_pd_id'];
                    $availableCart['sku'][] =$prodInfo['sku'];
                    $availableCart['deal_id'][] = $prodInfo['deal_id'];
                     $availableCart['deal_type'][] =$prodInfo['deal_type'];
                    
                    //  $availableCartFull[] = $availableCart;
                }
                
                
                
                $oldId = $prodInfo['pd_added_v_id'];
            }
            $availableCartFull[] = $availableCart;
           
            
         for($i=0;$i<sizeof($availableCartFull);$i++){
             $ven_data=array();
                 $oldCost = $finalCost = 0;
                 
                if(sizeof($availableCartFull[$i]) > 0)
                   {
                        $v_id = $availableCartFull[$i]['v_id'];
                      $vendorprice=0;
                        $prod_data = array();
                         $prod_data = array();
                    for($j=0;$j<sizeof($availableCartFull[$i]['product_id']);$j++){
                        $product = array();
                         $quantity = $availableCartFull[$i]['quantity'][$j];
                         $referal_code_id = $availableCartFull[$i]['referal_code'][$j];
                         $offer_id = $availableCartFull[$i]['offer_id'][$j];
                         $variable_pd_id = $availableCartFull[$i]['variable_pd_id'][$j];
                         $sku = $availableCartFull[$i]['sku'][$j];
                         $product_id = $availableCartFull[$i]['product_id'][$j];
                         $deal_id = $availableCartFull[$i]['deal_id'][$j];
                         $deal_type=$availableCartFull[$i]['deal_type'][$j];
                         
                         $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_min_order,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price
                         ,d.pd_vendor_price,vd.cap_available,vd.cap_charge,d.pd_view_count as total_view,d.pd_quantity FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
                         $prod_dataAll = $this->db->query($stmt);
                         $prod_num = $prod_dataAll->num_rows();
                         if($prod_num > 0)
                            {
                                $offers = $this->HealthmallModel->get_product_offer($product_id);
                                $rating = $this->HealthmallModel->get_num_ratings($product_id);
                                if(empty($referal_code_id))
                                    {
                                      $referal_code =array();
                                    } 
                                else 
                                    {
                                     $referal_code = $this->HealthmallModel->get_referal_code($product_id,$referal_code_id);
                                    }
                                foreach($prod_dataAll->result_array() as $prod)
                                {
                                    //$product = $prod;
                                   $var_ven_price=0;
                                    $prodName = $prod['pd_name'];
                                  
                                    $variableProduct = array();
                                    if($variable_pd_id > 0)
                                      {
                                        $variableProds = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = '$variable_pd_id'");
                                        foreach($variableProds->result_array() as $variableProd)
                                               {
                                                   
                                                    $colorId = $variableProd['color'];
                                                    $sizeId = $variableProd['size'];
                                                    $color = $this->HealthmallModel->get_color_by_id($colorId);
                                                    $size = $this->HealthmallModel->get_size_by_id($sizeId);
                                                    $variableProduct[]=array('id'=>$variableProd['id'],
                                                                             'pd_id'=>$variableProd['pd_id'],
                                                                             'sku'=>$variableProd['sku'],
                                                                             'quantity'=>$variableProd['quantity'],
                                                                             'price'=>$variableProd['price'],
                                                                             'vendor_price'=>$variableProd['vendor_price'],
                                                                             'image'=>$variableProd['image'],
                                                                             'color'=>$color,
                                                                             'size'=>$size
                                                                            );
                                                    $var_prod_price = intval($variableProd['price']);
                                                    $var_ven_price = $variableProd['vendor_price'];
                                                    $s = $c = '';
                                                    if(sizeof($color) > 0)
                                                       {
                                                         $c = $color[0]['color'];
                                   }
                                                    if(sizeof($size) > 0)
                                                       {
                                                         $s = $size[0]['size_name'];
                                   }
                                                    if($s != null && $s != '')
                                                        {
                                    $prodName = $prodName." - $s"    ;
                                }
                                                    if($c != null && $c != '')
                                                        {
                                    $prodName = $prodName." - $c"    ;
                                }                   
                                                }
                                      } 
                                  
                                    $productCost = 0;
                                    $vendorprice=intval (round($prod['pd_vendor_price']));
                                    if(!empty($offers))
                                      {
                                        $productCost = intval($offers[0]['offer_best_price'] * $quantity);
                                      }  
                                    else if($var_ven_price > 0 && $var_ven_price != null)
                                      {
                                        $productCost = intval (round($var_ven_price)) * $quantity;
                                        $vendorprice = intval($var_ven_price);
                                      } 
                                    else 
                                      {
                                        $productCost = intval (round($prod['pd_vendor_price'])) * $quantity;
                                      }  
                                       
                                   $vendorprice;
                                    $finalCost = $finalCost + $productCost;
                                    $cartQuantity = $cartQuantity + $quantity;
                                    $offer_price=0;
                                    if($offer_id != 0)
                                      {
                                        foreach($offers as $off)
                                               {
                                                  if($off['id'] == $offer_id)
                                                    {
                                                        $offer_price = $off['offer_mrp'];
                                                        break;
                                                    }
                                                }
                                      }
                                      
                                      $phot1=$prod['pd_photo_1'];
                                      $mrpprice=intval (round($prod['pd_mrp_price']));
                                      
                                      
                                     if($variable_pd_id > 0)
                                        {
                                            
                                            if(!empty($variableProduct['image']))
                                                {
                                                 $phot1 = $variableProduct['image'];
                                                }
                                            if(!empty($variableProduct['price'])){
                                                 $mrpprice = intval (round($variableProduct['price']));
                                            }
                                            if(!empty($variableProduct['vendor_price'])){
                                                 $vendorprice = intval (round($variableProduct['vendor_price']));
                                            }
                                        }  
                                   $vendorprice;
                                    $deal_array=array();
                        $checkOffers_deal = $this->HealthmallModel->get_deal_price($prod['pd_id']);
                      $deal_array = $checkOffers_deal; 
                      
                            
                                   
                                   
                                    $product=array('pd_id'=>$prod['pd_id'],
                                                     'pd_name'=>$prodName,
                                                     'pd_pc_id'=>$prod['pd_pc_id'],
                                                     'pd_psc_id'=>$prod['pd_psc_id'],
                                                     'sku'=>$sku,
                                                     'pd_photo_1'=>$phot1,
                                                     'pd_mrp_price'=>$mrpprice,
                                                     'pd_vendor_price'=>$vendorprice,
                                                     'cap_available'=>$prod['cap_available'],
                                                     'cap_charge'=>$prod['cap_charge'],
                                                     'quantity'=>$quantity,
                                                     'stock'=>$prod['pd_quantity'],
                                                     'stock'=>$prod['pd_quantity'],
                                                     'stock'=>$prod['pd_quantity'],
                                                     'product_cost'=>intval (round($productCost)),
                                                     'referal_code_id'=>$referal_code_id,
                                                     'referal_code'=>$referal_code,
                                                     'offer_id'=>$offer_id,
                                                     'variable_pd_id'=>$variable_pd_id,
                                                     'variable_product'=>$variableProduct,
                                                     'offer_price'=>$offer_price,
                                                     'deal_id'=>$deal_id,
                                                     'deal_type'=>$deal_type,
                                                     'offers'=>$offers,
                                                     'deal_offer'=>$deal_array
                                                    );
                                    $del['pd_id'] = $prod['pd_id'];
                                    $del['v_id'] = $prod['v_id'];
                                    $del['v_delivery_charge'] = intval (round($prod['v_delivery_charge'])) ;
                                    $del['v_min_order'] = intval (round($prod['v_min_order'])) ;
                                    $del['pd_vendor_price'] = intval (round($prod['pd_vendor_price'])) ;
                                    $del['quantity'] = $quantity;
                                    $delivery[] = $del;
                                    $vendorprice=0;
                                }
                           }
                        $prod_data[] = $product;
                    }
                       
                    $handlingCharge = $charge = 0;
                    $chcs = $this->db->query("SELECT * FROM `cash_handling_charges` WHERE `v_id` = '$v_id'")->result_array();
                    foreach($chcs as $chc)
                           {
                             $start_limit = $chc['start_limit'];
                             $end_limit = $chc['end_limit'];
                             $chargesType =$chc['charges_type'];
                             $cashHandlingChargeRow = $chc['chc'];
                             if($chargesType  == 'rupee')
                               {
                                    if($finalCost <= $end_limit &&  $finalCost >= $start_limit)
                                      {
                                          $charge = $cashHandlingChargeRow;
                                      }
                                } 
                             else 
                                {
                                    if($finalCost <= $end_limit &&  $finalCost >= $start_limit)
                                       {
                                          $handlingChargePercent = ($finalCost * $cashHandlingChargeRow) / 100;
                                          $charge = $handlingChargePercent;
                                        }    
                                }
                            }
                    
                    $ven_data=array('v_id'=>$prod['v_id'],
                                       'v_delivery_charge'=>intval (round($prod['v_delivery_charge'])),
                                       'cash_handling_charges'=>intval (round($charge)),
                                       'vendor_cost'=>intval (round($finalCost)),
                                       'v_min_order'=>intval (round($prod['v_min_order'])),
                                       'v_name'=>$prod['v_name']
                                     );
                    
                  
                     $prod_data_all=array('vendor'=>$ven_data,
                                    'product'=>$prod_data);
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
            foreach($delivery as $delvry)
                   {
                    $currentDel = $delvry;
                    $qty = $delvry['quantity'];
                    $price = $delvry['pd_vendor_price'];
                    $prodPrice = $qty * $price;
                    if($i>0)
                      {
                        if($delvry['v_id'] == $oldDel['v_id'])
                          {
                              $sameVenProd = $sameVenProd + $prodPrice;
                          } 
                        else 
                          {
                            if($oldDel['v_min_order'] > $sameVenProd) 
                              {
                                $totalProd = $totalProd + $oldDel['v_delivery_charge'];
                              }
                            $sameVenProd = $prodPrice;
                          }
                      } 
                      else 
                       {
                            $sameVenProd = $prodPrice;
                            $oldDel = $delvry;
                            $oldProdPrice = $prodPrice;
                            $delvChargeOld = $delvry['v_delivery_charge'];
                       }
                       $oldDel = $delvry;
                       $i++;
                      }
            if($delvry['v_id'] == $oldDel['v_id'] &&  $oldDel['v_min_order']>$sameVenProd) 
              {
                $totalProd = $totalProd + $oldDel['v_delivery_charge'];
              } 
                $prod_data_final[]=array('total_delivery_charges'=>$totalProd,
                                        'product_quantity'=>$cartQuantity,
                                        'cart'=>$cart_data_all);
                $res = array(
                    "status" => 200, 
                    "message" => "success",
                    "data" => $prod_data_final
                );
            
        } else {
            
             $res = array(
                "status" => 400,
                "message" => "Cart is empty",
                "data" => array()
            );
            
        
        }
        
      return $res;
  }
  
  


  public function get_referal_code($product_id,$referal_code_id){
        
        $referalCode = $this->db->query("SELECT * FROM `refer_product_hm` WHERE `id` = '$referal_code_id'")->row_array();
        
       $data[]=array('referal_code_id'=>$referalCode['id'],
                    'code'=>$referalCode['code'] );
        return $data; 
    } 


public function remove_all_cart($user_id)
{
    $checkAvailable = $this->db->query("DELETE FROM `user_cart` WHERE `customer_id` = '$user_id'");
    $affected_rows = $this->db->affected_rows();
    if($affected_rows > 0)
        {
               $res = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => "Successfully removed",
                    );   
            } 
    else 
        {
               $res = array(
                        "status" => 400,
                        "message" => "fail",
                        "description" => "Something went wrong, please try again",
                    );
            }
          
          
          
     
        
      return $res;
  } 
  
  
public function get_similar_items($user_id,$pd_id,$page_no,$per_page)
{   



      $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
      $result = "SELECT pd_pc_id FROM product_details_hm WHERE pd_id = '$pd_id' and pd_status = '1'";
      $query1 = $this->db->query($result);
      $rows_num2 = $query1->num_rows();
      if($rows_num2 > 0)
      {
        $rows_num1 = $query1->row_array();
      $pro_cat_id = $rows_num1['pd_pc_id'];
      $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE (`pd_pc_id` IN (".$pro_cat_id.") OR `pd_psc_id` IN (".$pro_cat_id.")) AND pd_status = 1 ")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  (`pd_pc_id` IN (".$pro_cat_id.") OR `pd_psc_id` IN (".$pro_cat_id."))  AND pd_status = 1 $limit";
        $productArray = array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
            foreach($query->result_array() as $q)
                   {
                      $p_id       = $q['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = array();
                     
                     
                     
                     $sku=$q['SKU_code1'];
                      $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                       
                            $deal_array=array();
              $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                          $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                    
                   
             }
          }       
   
         
        
      $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
      }
      else
      {
        $data['status'] = 201;
        $data['message'] = "Failed";
        $data['data_count'] = 0;
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = 0;
        $data['data'] =array();
      }
        
        
        
        return $data;
      
      
      
}

public function get_similar_brand($user_id,$pd_id,$page_no,$per_page)
{   
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset"; 
        $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id='$pd_id' AND pd_status = 1 ")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE  pd_added_v_id='$pd_id'  AND pd_status = 1 $limit";
        $productArray = array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
            foreach($query->result_array() as $q)
                   {
                      $p_id       = $q['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = array();
                     
                     
                     
                      $sku=$q['SKU_code1'];
                      $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                       
                            $deal_array=array();
              $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                          $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                    
                   
             }
             
             
              $store_count=count($finalDataNew);
                $last_page = ceil($store_count/$per_page);
                $data['status'] = 200;
                $data['message'] = "Success";
                $data['data_count'] = intval($store_count);
                $data['per_page'] = $per_page;
                $data['current_page'] = $page_no;
                $data['first_page'] = 1;
                $data['last_page'] = $last_page;
                $data['brand_name']=$q['brand_name'];
                $data['data'] = $productArray;
          }       
       else
          {
            $data['status'] = 201;
            $data['message'] = "Failed";
            $data['data_count'] = 0;
            $data['per_page'] = $per_page;
            $data['current_page'] = $page_no;
            $data['first_page'] = 1;
            $data['last_page'] = 0;
            $data['brand_name']="";
            $data['data'] =array();
          }
         
        
      
      
     
        
        
        
        return $data;
      
      
      
}

public function checkProductAvailibility($user_id,$pd_id,$pincode){
     // print_r($data); 
      $isPanIndia = 0;
    
    
    $stmt = "SELECT product_details_hm.pd_name,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4, vendor_details_hm.is_panindia
    FROM product_details_hm
    JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
    WHERE product_details_hm.pd_id=".$pd_id;
    
    $query = $this->db->query($stmt);
        $qarray = $query->row_array();
        //  print_r($qarray); die();
        $isPanIndia = $qarray['is_panindia'];
        // echo $isPanIndia; die();
        if($isPanIndia == 1){
            $pincode = $pincode;
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
    WHERE product_details_hm.pd_id=".$pd_id." && pincode_details.pincode = ".$pincode."";
    
    $query = $this->db->query($stmt);
        $qarray = $query->row_array();
    
          return $query->num_rows();
        }
    
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
    
   public function placing_order_allowed($data){
       // print_r($data); die();
        $allowed = $notAllowed = 0;
        $reasonArray = $allowedArray = $notAllowedArray  = $result = array();
        $today = date('Y-m-d H:s:i');
       
        $cust_id = $data['cust_id'];   
        
        // print_r($data['orders']); die();
        $orders = json_decode($data['orders'])->orders;    
        for($a=0;$a<sizeof($orders);$a++){
            $products = $orders[$a]->products;
            $proCount = sizeof($products);
            for($p=0;$p<sizeof($products);$p++){
                $referralCodeRows = array();
                $dataPdId = $products[$p]->id;
                if(!empty($products[$p]->referral_code)){
                    $referral_code_id = $products[$p]->referral_code;
                    $referralCodeRows = $this->db->query("SELECT * FROM `refer_product_hm` WHERE `id` = '$referral_code_id' AND `pd_id` = $dataPdId")->row_array();
                    //print_r($referralCodeRows); die();
                    if(sizeof($referralCodeRows)>0){
                        $codeExp = $referralCodeRows['expiry'];
                        $codePdId = $referralCodeRows['pd_id'];
                        $codeUser_id = $referralCodeRows['user_id'];
                        if($today < $codeExp){
                            
                            $reasonArray = array("referralCode"=>$referral_code_id,"pd_id"=>$dataPdId,'user_id'=>$codeUser_id,"message" => "Code Available to use");
                            $allowedArray[] = $reasonArray;
                            $allowed++;
                            
                            
                        } else {
                            $reasonArray = array("referralCode"=>$referral_code_id,"pd_id"=>$dataPdId,'user_id'=>$codeUser_id,"message" => "Code expired");
                            $notAllowedArray[] = $reasonArray;
                            $notAllowed++;
                            
                        }
                    } else {
                        $reasonArray = array("referralCode"=>$referral_code_id,"pd_id"=>$dataPdId,'user_id'=>$codeUser_id,"message" => "Code does not exists or allowed for another product");
                        $notAllowedArray[] = $reasonArray;
                        $notAllowed++;
                       
                    }
                    

                         
                } 
            }
        }
       
        if($notAllowed > 0){
            $result['status']=400;
            $result['message']="Can not use the code";
        } else {
            
            
            $result['status']=200;
            $result['message']="Can use the code";
            
            
           
        }
        
        $result['allowedArray'] = $allowedArray;
        $result['notAllowedArray'] = $notAllowedArray;
        
        return $result;
    }                     
                          
    public function address_detail($user_id, $address_id) 
    {
        $query = $this->db->query("Select *  FROM `user_address` WHERE address_id='$address_id' AND user_id='$user_id'");
        $count = $query->num_rows();
       if($count > 0)
         {
             $data=$query->row_array();
         }
         else
         {
             $data="";
         }
          return $data;
    }                 
                          
     public function place_order($data, $allowed, $products){
     $apiCalled = array();
     $discountExtra =$mrps = $dis = $chc = $del_amt = 0;  
      $ledgerAdded = 0;
   //  $apiCalled['status'] = 0;
     //print_r($data);  die(); 
      date_default_timezone_set('Asia/Kolkata');
        $order_date = date('Y-m-d H:i:s');
        // work on this
            $delivery_charge = 0;
            $listing_name = "";
            $listing_type = 34;
            $chat_id = "";
            $lat = "";
            $lng = "";
            $address_id = "";
            $v_name = $data['vendor_name0'];
            $v_id = $data['vendor_id0'];
            $chc = $data['chc0'];
            
            
            //  $chc = 0;
            // print_r($chc); die();
        // work on this ends
        $order_status = "Processing";
    $invoice = date("YmdHis")."".$data['number'];
    if($data['payment_method'] == 3){
        $paym = "Cash at point";
        $order_status = "Processing";
    }
    elseif($data['payment_method'] == 2){
        $paym = "Credit/Debit";
        $order_status = "Processing";
    }else{
        $paym = "Points";
        $order_status = "Processing";
    }
    
    //if($data['payment_method'] != 3){
      //  $order_status = "Payment Pending";
    //}
    
    $paym = $data['payment_method'];
  //    print_r($data['v_name']); die();
    $name = $data['name'];
  $invoice_no = $invoice;    
    $name = $data['name'];;
    $mobileno = $data['mobileno']; 
    $pincode = $data['pincode'];
    $address = $data['address']; 
    $address1 = $data['address1'];  
     $lat = $data['lat'];
             $lng = $data['lng'];
              $address_id = $data['address_id'];
            
            
            
    if($lat == "" || $lng == ""){
        $lat = 0;
         $lng = 0;
    }    
    
  
    $landmark = $data['landmark']; 
    $city = $data['city'];  
    $state = $data['state']; 
    $cust_id = $data['cust_id'];
    $amount = $data['amount'];
    
// actual_total  '".$data['amount']."',

for($i=0;$i<$data['total_products'];$i++){
 $pdIds[] = $data['product_id'.$i];   
 $pd_Id_current = $data['product_id'.$i];   
 $discountQuery = $this->db->query("SELECT pd_mrp_price as mrps FROM `product_details_hm` WHERE `pd_id` = '$pd_Id_current'")->row_array();
 $product_qty_cur = $data['product_qty'.$i];
 $mrps = $mrps + $discountQuery['mrps'] * $product_qty_cur;
}
$pdIds = implode(", ",$pdIds);

 
 
 $dis = $mrps - $amount;
 // do not add dicount given by vendor 
 // just insert extra discount if given
 $discountExtra = $mrps - $amount - $dis;
 

 
 
 $vendor_details = $this->db->query("SELECT delivery_by_medicalwale from vendor_details_hm WHERE v_id = '$v_id'")->row_array();
 if($vendor_details['delivery_by_medicalwale'] == 1){
    $order_deliver_by = "mw"; 
 } else {
    $order_deliver_by = "vendor"; 
 }
//  print_r($order_deliver_by); die();
$member_id=$data['member_id'];
    $query_for_order = "INSERT INTO user_order
(invoice_no,order_status,listing_id,listing_type,listing_name,name,mobile,user_id,actual_cost,order_total,discount,chc,pincode,address1,address2,lat,lng,address_id,landmark,city,state,payment_method,order_date,action_by,order_deliver_by,member_id)  VALUES ( '$invoice_no','$order_status','$v_id',$listing_type,'$v_name','$name','$mobileno','$cust_id','$mrps','$amount','$dis','$chc','$pincode','$address','$address1','$lat','$lng','$address_id','$landmark','$city','$state','$paym','$order_date','customer','$order_deliver_by','$member_id')";

//echo $query_for_order; die();

    $query = $this->db->query($query_for_order);
    
    $insert_id = $this->db->insert_id();
    
    if($insert_id != ""){
        
      
        
        if($data['is_website'] == 1){
          $query_for_order_pending = "INSERT INTO `order_pending_payments` (`user_id`, `order_id`, `listing_id`) VALUES ('$cust_id', '$insert_id', '34')";
          $query = $this->db->query($query_for_order_pending);
        
      }
      
      
      
        for($i=0;$i<$data['total_products'];$i++){
            
            if(!empty($data['referral_code'.$i])){
                
                $referralCode['referral_code_id'] = $data['referral_code'.$i];
                $referralCode['pd_id'] = $data['product_id'.$i];
                $referralCode['used_by'] = $data['cust_id'];
                $referralCode['order_id'] = $insert_id;
                
                for($a=0;$a<sizeof($allowed['allowedArray']);$a++){
                    if( $referralCode['pd_id'] == $allowed['allowedArray'][$a]['pd_id']){
                        $referralCode['user_id'] = $allowed['allowedArray'][$a]['user_id'];   
                       
                    }
                     
                 //   
                }
                
                $this->db->insert('used_referral_codes',$referralCode);
                
              //  ````````
            }
          //  print_r($referralCode);
            
          //  die();
          
          $getProductMrpRow = $this->db->query("SELECT pd_mrp_price FROM `product_details_hm` WHERE `pd_id` = '".$data['product_id'.$i]."' ")->row_array();
        $getProductMrp = $getProductMrpRow['pd_mrp_price'] * $data['product_qty'.$i];
        $member_id=$data['member_id'];
          $query_for_order_data = "INSERT INTO orders_details_hm (user_id,order_id,product_id,product_qty,vendor_id,is_offer,offer_id,offer_price,price,product_mrp
          ,variable_pd_id,sku,member_id,is_deal,deal_id,deal_price)
         VALUES ('".$cust_id."','".$insert_id."','".$data['product_id'.$i]."','".$data['product_qty'.$i]."','".$data['vendor_id'.$i]."','".$data['is_offer'.$i]."','".$data['offer_id'.$i]."','".$data['offer_total'.$i]."','".$data['total_products_price'.$i]."','$getProductMrp','".$data['variable_pd_id'.$i]."','".$data['sku'.$i]."','$member_id','".$data['is_deal'.$i]."','".$data['deal_id'.$i]."','".$data['deal_total'.$i]."')";
     
            $this->db->query($query_for_order_data);
        
          $empty_cart = "DELETE FROM user_cart WHERE customer_id = '".$data['cust_id']."' AND variable_pd_id = '".$data['variable_pd_id'.$i]."' AND product_id = '".$data['product_id'.$i]."'";
            $this->db->query($empty_cart);
        
        }
    //    $dataAll = json_decode($data['products'],TRUE);
           
        $dataAll = $products;
          //print_r($dataAll); die();
        for($i=0;$i<count($dataAll['products']);$i++){
            $dataAll['products'][$i]['total_price'] = $dataAll['products'][$i]['price'] * $dataAll['products'][$i]['quantity'];
        }
      //  print_r($dataAll);die;
        $query1=$this->db->query("SELECT * FROM `orders_details_hm` WHERE `order_id`='$insert_id' GROUP BY vendor_id")->result_array();
        //print_r($query1);die;
        $del_amt = 0;
        foreach($query1 as $q1){
            $tmp="SELECT v_delivery_charge, v_min_order FROM vendor_details_hm WHERE v_id = '".$q1['vendor_id']."'";
          //   print_r($this->db->query($tmp)->row_array()['v_delivery_charge']) ; die();
            $tm=$this->db->query($tmp)->row_array();
            $v_min_order = $tm['v_min_order'];
            $t = $tm['v_delivery_charge'];
           if($amount < $v_min_order){
                $del_amt += $t;   
           } 
            
        }
	    $bachat_service_id="0";
         if($data['bachat_service_id'] != "0")
        {
            $booking_history_bachat =   array('user_id' => $data['cust_id'],
                                            'booking_id' => $invoice_no,
                                            'vendor_type' => 34,
                                            'vendor_id' => 0,
                                            'booking_date' => date('Y-m-d H:i:s'),
                                            'created_at' => date('Y-m-d H:i:s')
                                        );
                
                $this->db->insert('bachat_card_booking',$booking_history_bachat);
                $bachat_service_id=$data['bachat_service_id'];
                $up=$this->db->query("UPDATE `user_card_services_list` SET `service_used_count`=service_used_count+1 WHERE service_id='$bachat_service_id'");
        }else {
        //insert in ledger
      if($data['payment_method'] != 3){ //not CAP 
        
        $grandTotal = $mrps - $dis + $chc + $del_amt;

        $user_id_type = 0;
        $credit = 0;
            $debit = $grandTotal;
            $payment_method = $data['payment_method'];
            $user_comments = "";
            $mw_comments = "Online payment done by customer";
            $vendor_comments = "";
            $order_type = 1;
            $transaction_of = 2;
            $transaction_id = "";
            $trans_status = "";
         
          
          $url = "Ledger/create_ledger";
          
          
            $apiData['user_id'] = $cust_id;
                $apiData['invoice_no'] = $insert_id;
                $apiData['ledger_owner_type'] = $user_id_type;
                $apiData['listing_id'] = $v_id;
                $apiData['listing_id_type'] = $listing_type;
                $apiData['credit'] = $credit;
                $apiData['debit'] = $debit;
                $apiData['payment_method'] = $payment_method;
                $apiData['user_comments'] = $user_comments;
                $apiData['mw_comments'] = $mw_comments;
                $apiData['vendor_comments'] = $vendor_comments;
                $apiData['order_type'] = $order_type;
                $apiData['transaction_of'] = $transaction_of;
                $apiData['trans_status'] = $transaction_id;
                $apiData['transaction_id'] = $trans_status;
                $apiData['transaction_date'] = "";
                $apiData['vendor_id'] = 34;
                
          $apiCalled = $this->call_api($apiData, $url);
          $apiCalled = json_decode($apiCalled);
            if($apiCalled->status == 200){
              $ledgerAdded = 1;
              $apiCalledData = $apiCalled->data;
              
              $apiDataFinal['type'] =$apiCalledData->type; 
              $apiDataFinal['ledger_id'] =$apiCalledData->ledger_id;
              $apiDataFinal['order_id'] =$insert_id;
            
          } else {
               $ledgerAdded = 0;
              $apiDataFinal = (object)[];
          }
          
      }
	 }
        
        
        
        $this->db->query("UPDATE user_order SET delivery_charge = '$del_amt',bachat_service_id='$bachat_service_id'  WHERE order_id = '$insert_id'");
        $res = $this->db->query("SELECT order_id,invoice_no,order_status,user_id,payment_method,order_date,delivery_charge FROM user_order WHERE order_id = '$insert_id'");
        $result = array(
          "status" => "true",
          "original_total_price" => $data['amount'],
          "offer_total_price" => $data['offer_amount'],
          "deal_total_price" => $data['deal_amount'],
          "saved_amount" => $data['savings'],
          "products" => $dataAll,
          "order_details" => $res->result_array(),
          "ledger" => $ledgerAdded == 1 ? $apiDataFinal : array()
          );
      
        
    //    white here ****************
            $trans_id = date("YmdHis");
            $debit = 1;
            if($data['is_website'] ==1 ){
                $final_amt_deal=$data['offer_amount']+$data['deal_amount'];
                $this->HealthmallModel->insert_payment_status($data['cust_id'], '34', $trans_id, $debit, $paym, $insert_id, $paym, $data['amount'], 'Processing', $data['amount'], $final_amt_deal, $paym);
        }
        
        
        
            //    Rapid delivery by ghanshyam parihar dated:10 March 2019 starts
            
        $query1=$this->db->query("SELECT vendor_id FROM `orders_details_hm` WHERE `order_id`='$insert_id' GROUP BY vendor_id")->result_array();
    //    print_r($query1);die;
        $delivery_by_medicalwale = 0;$ret_add;$ship_pin;$ship_phone;$ship_company;
        foreach($query1 as $q1){
            $dbmediquery="SELECT delivery_by_medicalwale,v_company_name,v_address1,v_address2,v_city,v_state,v_country,v_pincode,v_phoneno FROM vendor_details_hm WHERE v_id = '$v_id'";
              //print_r($this->db->query($dbmediquery)->row_array()) ; die();
            $dbmedi=$this->db->query($dbmediquery)->row_array()['delivery_by_medicalwale'];
            
            $delivery_by_medicalwale = $dbmedi;
         
            $ship_company  =  $this->db->query($dbmediquery)->row_array()['v_company_name']; //String,
            
        }
        
        if($data['payment_method'] == 3){
            $mode = "cod";
            $amt = $amount;
        }
        else{
            $mode = "prepaid";
            $amt = '';
        }
        
        $pid = explode(',',$pdIds);
        $pd_name = '';
    //    $pd_weight = '';
        $c = 0;
        foreach($pid as $pd){
                $c++;
                    $pd_nameQuery = $this->db->query("SELECT pd_name FROM `product_details_hm` WHERE `pd_id`='$pd'")->row_array();
                    $pd_name .= $c.") ".$pd_nameQuery['pd_name'].",\n";
                    // $pd_weight .= $c.") ".$pd_nameQuery['product_weight'].",\n";
        }
        $pd_weight = 0.3;
        $user_id    =   $data['cust_id'];
        $sqluser = "SELECT lat,lng FROM `users` WHERE `id`='$user_id'";
            // $queryuser  =   $this->db->query($sqluser)->result_array();
            //    print_r($queryuser);
        $lat        =   $this->db->query($sqluser)->row_array()['lat'];
        $lng        =   $this->db->query($sqluser)->row_array()['lng'];
        
        $radius = 5;
        
        $sql__  = sprintf("SELECT hud_id,pincode,address1,address2,city,state,phone, ( 6371 * acos( cos( radians('%s') ) * cos( radians( warehouse_hm.lat ) ) * cos( radians( warehouse_hm.lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( warehouse_hm.lat ) ) ) ) AS distance FROM warehouse_hm WHERE warehouse_hm.v_id = '$v_id'  HAVING distance < '%s' ORDER BY distance  LIMIT 1", ($lat), ($lng), ($lat), ($radius));
        $query__ = $this->db->query($sql__);
            $count__ = $query__->num_rows();
            $hub_id = '';
            $hub_pincode = '';
            $ret_add = '';
            $ship_pin = '';
            $ship_phone = '';
            
            if ($count__ > 0) {
                foreach ($query__->result_array() as $row__) 
                {
                    // print_r($row__);
                    $hub_id         = $row__['hud_id'];
                    $hub_pincode    = $row__['pincode'];
                    $ret_add       =  $row__['address1'].', '.$row__['address2'].', '.$row__['city'].', '.$row__['state'].', '.$row__['pincode']; //String,
                $ship_pin      =  $row__['pincode']; //String
                $ship_phone    =  $row__['phone']; //String,
                }
            }
            else{
                    $sql__  = "SELECT hud_id,pincode,address1,address2,city,state,phone FROM warehouse_hm WHERE v_id = '$v_id' LIMIT 1";
                $query__ = $this->db->query($sql__);
                    $count__ = $query__->num_rows();
                    if ($count__ > 0) {
                    foreach ($query__->result_array() as $row__) 
                    {
                        // print_r($row__);
                        $hub_id = $row__['hud_id'];
                        $hub_pincode    = $row__['pincode'];
                        $ret_add       =  $row__['address1'].', '.$row__['address2'].', '.$row__['city'].', '.$row__['state'].', '.$row__['pincode']; //String,
                    $ship_pin      =  $row__['pincode']; //String
                    $ship_phone    =  $row__['phone']; //String,
                    }
            }
            }
            //    echo $hub_id;
        
        $product_description       =  rtrim($pd_name,",\n"); // Product Description
            // echo $delivery_by_medicalwale; die();
            $amt = $mrps - $dis + $chc + $del_amt;
        if($delivery_by_medicalwale==='1'){
                $rapidRespone = $this->rapidelivery_create($insert_id,$name,$mobileno,$address,$address1,$pincode,$landmark,$city,$state,$amt,$mode,$ret_add,$ship_pin,$ship_phone,$ship_company,$product_description,$hub_id,$pd_weight);
                // print_r($rapidRespone); // die();
                if(count($rapidRespone)>0){
                    if($rapidRespone->status==='200'){
                        $waybill = $rapidRespone->waybill;
                        $order_id = $rapidRespone->order_id;
                        $this->db->query("UPDATE user_order SET waybill = '$waybill' WHERE order_id = '$order_id'");
                    }
                }
            }
        
        //    Rapid delivery by ghanshyam parihar dated:10 March 2019 ends
        
        
      /*  if($v_id=183){
             $rapidRespone = $this->vlcc_place_order($insert_id,$name,$mobileno,$address,$address1,$pincode,$landmark,$city,$state,$amt,$mode,$ret_add,$ship_pin,$ship_phone,$ship_company,$product_description,$hub_id,$pd_weight,$products);
            
        }*/
        
        
        
    } 
    else {
          $result = array(
              "status" => "false",
              "message" => "something went wrong"
          );
    }
//    print_r($result); die();
    return $result;
  }                     
                          
                          
                          
     public function insert_payment_status($user_id, $listing_id, $trans_id, $debit, $type, $order_id, $order_type,$amount, $status_mesg, $discount, $discount_rupee, $payment_type) {
        
      
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
        $status = 1;
        $Expire_date = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + 1 year"));
        if($debit == "1"){
            $creadit_debit = 1;
        }else{
            $creadit_debit = 0;
        }    
        $upadte_ledger_array = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => $creadit_debit,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => '1',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
            //'vendor_category'=> $vendor_category
            'vendor_category'=>  $type
        );
        
        
        $upadte_ledger_array_point = array(
            'user_id'       => $user_id,
            'listing_id'    => $listing_id,
            'trans_id'      => $trans_id,
            'trans_type'    => 4,
            'order_id'      => $order_id,
            'amount'        => $amount,
            'order_status'  => $status=='1'?'success':'failed',
            'order_type'    => $order_type,
            'status_message'=> $status_mesg,
            'discount'      => $discount,
            'discount_rupee'=> $discount_rupee,
            'trans_time'    => $date,
            'trans_mode'=> $payment_type,
             //   'vendor_category'=> $vendor_category
             'vendor_category'=>  $type
        );
        
        $upadte_user_points = array(
            'user_id'        => $user_id,
            'order_id'       => $order_id,
            'trans_id'       => $trans_id,
            'points'         => $amount,
            'created_at'     => $date,
            'expire_at'      => $Expire_date,    
            'status'         => 'active'
        );
       
       
        if($payment_type != '3'){
            
           
            $this->db->insert('user_ledger',$upadte_ledger_array);
         
         //update ledger balance but this balance is locked
            $query = $this->db->query("select * from user_ledger_balance where user_id='$user_id'");
            $row = $query->row();
            $row_count =$query ->num_rows();
            //die();
            
            if($row_count>0)
            {
                //update ledger balance 
                $existing_balance = $row->ledger_balance;
                $existing_lock_balance =$row->lock_amount;
                $total_balance = $existing_balance + $amount; 
                $total_lock_amount = $existing_lock_balance + $amount;
                $user_ledger_balance = array(
                    'ledger_balance' =>$total_balance,
                    'lock_amount' => $total_lock_amount
                    );
                     $this->db->where('user_id', $user_id);  
                $this->db->update('user_ledger_balance', $user_ledger_balance); 
            }
            else
            {
              // insert into ledger balance 
               $user_ledger_balance = array(
                    'user_id' => $user_id,
                    'ledger_balance' =>$amount,
                    'lock_amount' => $amount                                                                  
                    );
              $this->db->insert('user_ledger_balance',$user_ledger_balance);
            }
           //end
            
            // }
            
        }
      
        return array(
            'status' => 201,
            'message' => 'success'
        );
        
    }
                      
                          
                          
                          
    function rapidelivery_create($oid,$name,$mobileno,$address,$address1,$pincode,$landmark,$city,$state,$amount,$mode,$ret_add,$ship_pin,$ship_phone,$ship_company,$product_description,$hub_id,$pd_weight)
    {
        $url = "v2/createpackage.php";  
       
        $data=array(
                        "token"        =>   "AD273C2E0AE503CC4F8324C",   // Live credientials
                        "client"       =>   "medicalwale",               // Live credientials
                        // "token"         =>   "test321",                     // sandbox testing credientials
                        // "client"        =>   "test95",                      // sandbox testing credientials
                    "oid"           =>  $oid, //Your Order Number,
                    "consignee"     =>  $name,
                    "add1"          =>  $address,
                    "add2"          =>  $address1." ".$landmark,
                    "pin"           =>  $pincode,
                    "city"          =>  $city,
                    "state"         =>  $state,
                    "country"       =>  'India',
                    "phone"         =>  $mobileno,
                    "weight"        =>  $pd_weight, // double Decimal Value
                    "mode"          =>  $mode,
                    "hub_id"        =>  987,
                    "amt"           =>  $amount,
                    "ret_add"       =>  $ret_add, //String,
                    "ship_pin"      =>  $ship_pin, //String
                    "ship_phone"    =>  $ship_phone, //String,
                    "ship_company"  =>  $ship_company, //String,
                    "product"       =>  $product_description // Product Description
                    );
        
    //         $this->token        =   "AD273C2E0AE503CC4F8324C";   // Live credientials
    //         $this->client       =   "medicalwale";               // Live credientials
    //         // "token"         =>   "test321",                     // sandbox testing credientials
    //         // "client"        =>   "test95",                      // sandbox testing credientials
      //  $this->oid         =  $oid; //Your Order Number,
      //  $this->consignee     =  $name;
      //  $this->add1          =  $address;
      //  $this->add2          =  $address1." ".$landmark;
      //  $this->pin           =  $pincode;
      //  $this->city          =  $city;
      //  $this->state         =  $state;
      //  $this->country       =  'India';
      //  $this->phone         =  $mobileno;
      //  $this->weight        =  $pd_weight; // double Decimal Value
      //  $this->mode          =  $mode;
      //  $this->hub_id        =  987;
      //  $this->amt           =  $amount;
      //  $this->ret_add       =  $ret_add; //String,
      //  $this->ship_pin      =  $ship_pin; //String
      //  $this->ship_phone    =  $ship_phone; //String,
      //  $this->ship_company  =  $ship_company; //String,
      //  $this->product       =  $product_description; // Product Description
            
            // $data1 = json_encode($this);
            // print_r($data1); die;
            $result=$this->crul_rapid_delivery($url,$data);
            // print_r($result); die();
            $results= json_decode($result);
            return $results;
    }
    
    // added by swapnali on 3rd june 2019
    
    public function crul_rapid_delivery($u,$data){
      $u=$u;
      $url="trace.rapiddelivery.co/api/".$u;
    //   echo $url; 
     
      $ch = curl_init($url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POST, true);
    
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
      $output = curl_exec($ch);
      curl_close($ch);
    //  print_r($output); die;
      return $output;
    }
                         
                          
                          
    public function call_api($data,$u) {
        /*echo "in call_api";
        print_r($data); die();
       */
        $get_api_version = $this->get_api_version();
        $url = $get_api_version.$u;
         $data = json_encode($data);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        @curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER,['User-ID:1','Authorizations:25iwFyq/LSO1U','Client-Service:frontend-client','Auth-Key:medicalwalerestapi']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);
       
      
        return $result ;
       
    }                       
                          
                          
      public function get_product_image($pd_id){
      
      $results = $this->db->query("SELECT `pd_photo_1` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
     // print_r("SELECT `pd_photo_1` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'"); die();
     return $results; 
        
  }
  
  public function get_variable_product_image($pd_id){
      
      $results = $this->db->query("SELECT `image` FROM `variable_products_hm` WHERE `id` = '$pd_id'")->row_array();
     // print_r("SELECT `pd_photo_1` FROM `product_details_hm` WHERE `pd_id` = '$pd_id'"); die();
     return $results; 
        
  }
  
    public function variable_quantity_bestseller($pd_id,$pd_variable_id,$ordered_quantity){
      $new_pd_best_seller = 0;
      $results = $this->db->query("SELECT `pd_quantity`,`pd_best_seller`  FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
      $results1 = $this->db->query("SELECT *  FROM `variable_products_hm` WHERE `id` = '$pd_variable_id'")->row_array();
      $pd_quantity = $results['pd_quantity'];
      $pd_best_seller = $results['pd_best_seller'];
       $pd_variable_quantity = $results1['quantity'];
     // if($pd_quantity > 0){
     //    $newQuantity = $pd_quantity - $ordered_quantity;
     //    $this->db->query("UPDATE `product_details_hm` SET `pd_quantity` = '$newQuantity' WHERE `pd_id` = '$pd_id';");
     // } 
      
      if($pd_variable_quantity > 0){
         $newQuantity = $pd_variable_quantity - $ordered_quantity;
         $this->db->query("UPDATE `variable_products_hm` SET `quantity` = '$newQuantity' WHERE `id` = '$pd_variable_id';");
      } 
      
      
      
      if($pd_best_seller == null){
          $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = 1 WHERE `pd_id` = '$pd_id';");
      } else {
           $new_pd_best_seller = $pd_best_seller + 1;
           $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = '$new_pd_best_seller' WHERE `pd_id` = '$pd_id';");
      }
     
      
    
      
     return 1; 
        
  }
                          
        public function quantity_bestseller($pd_id,$ordered_quantity){
      $new_pd_best_seller = 0;
      $results = $this->db->query("SELECT `pd_quantity`,`pd_best_seller`  FROM `product_details_hm` WHERE `pd_id` = '$pd_id'")->row_array();
      $pd_quantity = $results['pd_quantity'];
      $pd_best_seller = $results['pd_best_seller'];
      if($pd_quantity > 0){
         $newQuantity = $pd_quantity - $ordered_quantity;
         $this->db->query("UPDATE `product_details_hm` SET `pd_quantity` = '$newQuantity' WHERE `pd_id` = '$pd_id';");
         
      } 
      
      if($pd_best_seller == null){
          $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = 1 WHERE `pd_id` = '$pd_id';");
      } else {
           $new_pd_best_seller = $pd_best_seller + 1;
           $this->db->query("UPDATE `product_details_hm` SET `pd_best_seller` = '$new_pd_best_seller' WHERE `pd_id` = '$pd_id';");
      }
     
      
    
      
     return 1; 
        
  } 
  
   public function call_post_order($user_id, $amount, $transaction_id, $transaction_status, $ledger,$payment_method) {
         $last_order_id = $debit = $total_amount = 0;
         $user_led = $vendor_led = "";
        $ids = $user_vender_data = $vender_vender_data =  $data = array();
         $user_ledger_ids = $vendor_ledger_ids = array();
        //  get amount - done
        // get ledger ids - done
        // calculated amount wrt ledger ids - done
        // if both are same then update ledger
        
        foreach($ledger as $l){
            if($l['type'] == 'user'){
                $user_ledger_ids[] = "'".$l['ledger_id']."'";
            }
            if($l['type'] == 'vendor'){
                $vendor_ledger_ids[] = "'".$l['ledger_id']."'";
            }
            $ids[] = $l['order_id'];
            $last_order_id = $l['order_id'];
        }
        // cancel order
        if(sizeof($ids) > 0){
            $ordereIds = implode(",", $ids);
            // if transaction failed or transaction cancel
            if($transaction_status == 2 || $transaction_status == 3){
                if($transaction_status == 3){
                    $paymentCancelStatus = "cancelled by customer";
                } else {
                    $paymentCancelStatus = "declined by bank";
                }
                $cancel_reason = "Payment ".$paymentCancelStatus;
                $this->db->query("UPDATE user_order SET `cancel_reason` = '$cancel_reason', `order_status` = 'Order Cancelled' WHERE `order_id` IN ($ordereIds)");
            } else {
                $this->db->query("UPDATE user_order SET `order_status` = 'Processing' WHERE `order_id` IN ($ordereIds)");
            }
        }
        
        if(sizeof($user_ledger_ids) > 0){
             $user_led =  implode(",",$user_ledger_ids);
        }
        if(sizeof($vendor_ledger_ids) > 0){
            $vendor_led =  implode(",",$vendor_ledger_ids);
        }
        if($user_led != ""){
           
            $user_vender_data = $this->db->query("SELECT * FROM `user_vendor_ledger` WHERE `ledger_id` IN ($user_led) ")->result_array();
        }
        if($vendor_led != ""){
            $vender_vender_data = $this->db->query("SELECT * FROM `vendor_vendor_ledger` WHERE `ledger_id` IN ($vendor_led) ")->result_array();
        }
        foreach($user_vender_data as $uvd){
            if($uvd['user_id'] == $user_id){
                $debit = $uvd['debit'];
            } else {
                $debit = $uvd['credit'];
            }
            $total_amount   = $total_amount  + $debit;
        }
        
        foreach($vender_vender_data as $vvd){
            
            if($vvd['user_id'] == $user_id){
                $debit = $vvd['debit'];
            } else {
                $debit = $vvd['credit'];
            }
            $total_amount   = $total_amount  + $debit;
        }
       /* echo "amount : ".$amount;
        echo "<br>";
        echo "total_amount : ".$total_amount;
        die();*/
        if($amount == $total_amount){
            $status = 1; // amount match
            
            foreach($ledger as $l){
                
                $apiData['user_id'] = $user_id;
                $apiData['ledger_id'] = $l['ledger_id'];
                $apiData['transaction_id'] = $transaction_id;
                $apiData['trans_status'] =$transaction_status;
                $apiData['invoice_no'] = $l['order_id'];
                $apiData['type'] = $l['type'];
                
                $url = "Ledger/update_ledger";
                
                $apiCalled = $this->call_api($apiData, $url);
                
            }
            
        } else {
    
            if(sizeof($ids) > 0){
                $ordereIds = implode(",", $ids);
        
             $user_id_type = 0;
        $credit = 0;
            $debit = $amount;
            $payment_method = $payment_method;
            $user_comments = "";
            $mw_comments = "Customer paid to medicalwale for ".$ordereIds;
            $vendor_comments = "";
            $order_type = 1;
            $transaction_of = 2;
            $transaction_id = $transaction_id;
            $trans_status = $transaction_status;
         
          
          $url = "Ledger/create_ledger";
          
          
            $apiData['user_id'] = $user_id;
                $apiData['invoice_no'] = $last_order_id;
                $apiData['ledger_owner_type'] = $user_id_type;
                $apiData['listing_id'] = 0;
                $apiData['listing_id_type'] = 0;
                $apiData['credit'] = $credit;
                $apiData['debit'] = $debit;
                $apiData['payment_method'] = $payment_method;
                $apiData['user_comments'] = $user_comments;
                $apiData['mw_comments'] = $mw_comments;
                $apiData['vendor_comments'] = $vendor_comments;
                $apiData['order_type'] = $order_type;
                $apiData['transaction_of'] = $transaction_of;
                $apiData['trans_status'] = $trans_status;
                $apiData['transaction_id'] = $transaction_id;
                $apiData['transaction_date'] = "";
                $apiData['vendor_id'] = 34;
            $apiCalled = $this->call_api($apiData, $url);
          //  $apiCalled = json_decode($apiCalled);
            }
            $status = 2; // amount did not match
            // print_r($apiCalled); die();
        }
        
  $data['status'] = $status;
        return $data;
     }
   
      
      
      
     
      public function hm_menu_list1($user_id){
        $data= array();
        $deal=array();
        $deal1=array();
        $season=array();
        $season1=array();
        $gender2=array();
        date_default_timezone_set("Asia/Calcutta");
        $date=date('Y-m-d');
        $deal_of_day = $this->db->query("SELECT * FROM `hm_deal_of_the_day` WHERE ddate = '$date' ORDER BY `id` ASC LIMIT 4");
        $count = $deal_of_day->num_rows();
        if($count>0)
        {
           
            
            
            


         foreach ($deal_of_day->result_array() as $row) 
                 {
                    $day=date('Y-m-d H:i:s',strtotime($row['ddate'] . "+1 days"));
                    $datetime1 = date('Y-m-d H:i:s');
                    
                    
                    $id= $row['id'];
                    $text = $row['text'];
                    $image = $row['image'];  
                    $offtext = $row['offtext'];
                    $deal[] = array( "id"=> $id,
                    "flow_type"=>"1",
                    "text1"=> $text,
                    "text2"=>$offtext,
                    "image"=> $image
                    
                        );
                 }
              $deal1[]=array('title'=>"Deal Of The Day",
                           'image'=>"",
                           'type'=>"1",
                           "flow_type"=>"1",
                           'start_time'=>$datetime1,
                           'end_time'=>$day,
                           'color'=>"ffffff",
                           'background_image'=>"",
                           'total_deal'=>$deal);   
        }         
        $start=date('Y-m-d', strtotime('-1 day', strtotime($date)));
        $season_of_day = $this->db->query("SELECT * FROM `hm_season` WHERE '$date' BETWEEN start_date AND end_date ORDER BY `id` ASC LIMIT 4");
        $count1 = $season_of_day->num_rows();
        if($count1>0)
        {
            
         foreach ($season_of_day->result_array() as $row1) 
                 {
                    $day=date('Y-m-d H:i:s',strtotime($row1['end_date'] . "+1 days"));
                    $datetime1 = date('Y-m-d H:i:s'); 
                    $id= $row1['id'];
                    $text = $row1['text'];
                    $image = $row1['image'];  
                    $season_name = $row1['season_name'];
                    $season[] = array( "id"=> $id,
                    "flow_type"=>"2",
                    "text1"=> $text,
                    "text2"=>$season_name,
                    "image"=> $image
                
                        );
                 }
                 $season1[]=array('title'=>"COVID-19",
                           'image'=>"",
                           'type'=>"2",
                           "flow_type"=>"2",
                            'start_time'=>$datetime1,
                           'end_time'=>$day,
                            'color'=>"e2f3ee",
                           'background_image'=>"",
                           'total_deal'=>$season);   
        }
        $t="Ayurvedic Products";
          $im="123";
       $gender3=$this->Product_home($im,$t,$user_id);
       if(count($gender3) > 0)
       {
           $gender2=$gender3;
       }
  
       
        $final=array_merge($deal1,$season1,$gender2);
        return $final;
    }
    
    
    public function hm_menu_list2($user_id,$page_no,$page_size){
        $data= array();
        $gender2=array();
        date_default_timezone_set("Asia/Calcutta");
        $date=date('Y-m-d');
        $this11 = new \stdClass();
        $get_api_python = $this->get_api_python();
        $url=$get_api_python;
        $this11->user_id=$user_id;
        $this11->page_no=$page_no;
        $this11->page_size=$page_size;
        $data11 = json_encode($this11);
        $data12=healthwall_adver_crul($url,$data11);
        $data131 = json_decode($data12); 
         if(!empty($data131))
          {
    $re=$data131->data;
             $data[] =array("title"=>"Hand Picked For You",
        "image"=>"",
        "type"=>"5",
        "flow_type"=>"5",
        "start_time"=>"",
        "end_time"=>"",
        "color"=>"ffffff",
        "background_image"=>"",
          "total_deal"=>$re
       );
             
          }
           $t="Sports and Fitness";
          $im="81";
       $gender3=$this->Product_home($im,$t,$user_id);
       if(count($gender3) > 0)
       {
           $gender2=$gender3;
       } 
          
        $final=array_merge($data,$gender2);
        
        return $final;
    }
       public function hm_menu_list3($user_id){
        $data= array();
        $deal=array();
        $deal1=array();
        $season=array();
        $season1=array();
        $gender2=array();
        date_default_timezone_set("Asia/Calcutta");
        $date=date('Y-m-d');
       
        $under_of_day = $this->db->query("SELECT * FROM `hm_under_product` WHERE status='0' ORDER BY `id` ASC LIMIT 4");
        $count2 = $under_of_day->num_rows();
        if($count2>0)
        {
            
         foreach ($under_of_day->result_array() as $row2) 
                 {
                    $day=date('Y-m-d H:i:s');
                    $datetime1 = date('Y-m-d H:i:s'); 
                    $id= $row2['id'];
                    $text = $row2['gender'];
                    $image = $row2['image'];  
                    $season_name = "";
                    $under[] = array( "id"=> $id,
                    "flow_type"=>"3",
                    "text1"=> $text,
                    "text2"=>$season_name,
                    "image"=> $image,
                
                        );
                 }
                 $under1[]=array('title'=>"The 99 Store",
                           'image'=>"",
                           'type'=>"3",
                           "flow_type"=>"3",
                           'start_time'=>$datetime1,
                           'end_time'=>$day,
                            'color'=>"ffffff",
                           'background_image'=>"",
                           'total_deal'=>$under);   
        } 
        $gender_of_day = $this->db->query("SELECT * FROM `hm_gender_master` WHERE status='0' ORDER BY `id` ASC LIMIT 4");
        $count3 = $gender_of_day->num_rows();
        if($count3>0)
        {
            
         foreach ($gender_of_day->result_array() as $row3) 
                 {
                    $day=date('Y-m-d H:i:s');
                    $datetime1 = date('Y-m-d H:i:s'); 
                    $id= $row3['id'];
                    $text = $row3['gender'];
                    $image = $row3['image'];  
                    $season_name = "";
                    $gender[] = array( "id"=> $id,
                    "flow_type"=>"4",
                    "text1"=> $text,
                    "text2"=>$season_name,
                    "image"=> $image,
                
                        );
                 }
                 $gender1[]=array('title'=>"Products",
                           'image'=>"",
                           'type'=>"3",
                           "flow_type"=>"4",
                           'start_time'=>$datetime1,
                           'end_time'=>$day,
                            'color'=>"ffffff",
                           'background_image'=>"",
                           'total_deal'=>$gender);   
        } 
       
       
         $t="Personal Care";
          $im="15";
       $gender3=$this->Product_home($im,$t,$user_id);
       if(count($gender3) > 0)
       {
           $gender2=$gender3;
       }
       
        $final=array_merge($under1,$gender1,$gender2);
        return $final;
    }
      
      public function hm_menu_list_page($user_id,$brand_id){
        $final= array();
      
        if($brand_id!=0 || empty($brand_id))
        {
            
            $data= array();
            $deal=array();
            $deal1=array();
            $season=array();
            $season1=array();
            $under1=array();
            $under=array();
            $gender1=array();
            $gender=array();
            date_default_timezone_set("Asia/Calcutta");
            $date=date('Y-m-d');
            $deal_of_day = $this->db->query("SELECT * FROM `hm_deal_of_the_day` WHERE ddate = '$date' ORDER BY `id` DESC");
            $count = $deal_of_day->num_rows();
            if($count>0)
            {
               
                
                
                
    
    
             foreach ($deal_of_day->result_array() as $row) 
                     {
                        $day=date('Y-m-d H:i:s',strtotime($row['ddate'] . "+1 days"));
                        $datetime1 = date('Y-m-d H:i:s');
                        
                        
                        $id= $row['id'];
                        $text = $row['text'];
                        $image = $row['image'];  
                        $offtext = $row['offtext'];
                        $deal[] = array( "id"=> $id,
                        "flow_type"=>"1",
                        "text1"=> $text,
                        "text2"=>$offtext,
                        "image"=> $image
                        
                            );
                     }
                  $deal1[]=array('title'=>"Deal Of The Day",
                               'image'=>"",
                               'type'=>"1",
                               "flow_type"=>"1",
                               'start_time'=>$datetime1,
                               'end_time'=>$day,
                               'color'=>"ffffff",
                               'background_image'=>"",
                               'total_deal'=>$deal);   
        }         
            $start=date('Y-m-d', strtotime('-1 day', strtotime($date)));
            $season_of_day = $this->db->query("SELECT * FROM `hm_season` WHERE '$date' BETWEEN start_date AND end_date ORDER BY `id` DESC");
            $count1 = $season_of_day->num_rows();
            if($count1>0)
            {
                
             foreach ($season_of_day->result_array() as $row1) 
                     {
                        $day=date('Y-m-d H:i:s',strtotime($row1['end_date'] . "+1 days"));
                        $datetime1 = date('Y-m-d H:i:s'); 
                        $id= $row1['id'];
                        $text = $row1['text'];
                        $image = $row1['image'];  
                        $season_name = $row1['season_name'];
                        $season[] = array( "id"=> $id,
                        "flow_type"=>"2",
                        "text1"=> $text,
                        "text2"=>$season_name,
                        "image"=> $image
                    
                            );
                     }
                     $season1[]=array('title'=>"COVID-19",
                               'image'=>"",
                               'type'=>"2",
                               "flow_type"=>"2",
                                'start_time'=>$datetime1,
                               'end_time'=>$day,
                                'color'=>"ffffff",
                               'background_image'=>"",
                               'total_deal'=>$season);   
        }
             $under_of_day = $this->db->query("SELECT * FROM `hm_under_product` WHERE status='0' ORDER BY `id` ASC LIMIT 4");
             $count2 = $under_of_day->num_rows();
             if($count2>0)
             {
                
             foreach ($under_of_day->result_array() as $row2) 
                     {
                        $day=date('Y-m-d H:i:s');
                        $datetime1 = date('Y-m-d H:i:s'); 
                        $id= $row2['id'];
                        $text = $row2['gender'];
                        $image = $row2['image'];  
                        $season_name = "";
                        $under[] = array( "id"=> $id,
                        "flow_type"=>"3",
                        "text1"=> $text,
                        "text2"=>$season_name,
                        "image"=> $image,
                    
                            );
                     }
                     $under1[]=array('title'=>"The 99 Store",
                               'image'=>"",
                               'type'=>"3",
                               "flow_type"=>"3",
                               'start_time'=>$datetime1,
                               'end_time'=>$day,
                                'color'=>"ffffff",
                               'background_image'=>"",
                               'total_deal'=>$under);   
        } 
            
       
        $final=array_merge($deal1,$season1,$under1,$gender1); 
        }
        return $final;
    }
      public function hm_menu_list_category ($user_id,$brand_id)
      {
        $final= array();
      
        if($brand_id!=0 || empty($brand_id))
        {
            
            $data= array();
            date_default_timezone_set("Asia/Calcutta");
            $date=date('Y-m-d');
            $deal_of_day = $this->db->query("SELECT * FROM `hm_deal_of_the_day` WHERE ddate = '$date' ORDER BY `id` DESC");
            $count = $deal_of_day->num_rows();
            if($count>0)
            {
               $deal1[]=array('title'=>"Deal Of The Day",
                              'image'=>"",
                              'type'=>"1",
                              "flow_type"=>"1"
                              );   
            }         
            $start=date('Y-m-d', strtotime('-1 day', strtotime($date)));
            $season_of_day = $this->db->query("SELECT * FROM `hm_season` WHERE '$date' BETWEEN start_date AND end_date ORDER BY `id` DESC");
            $count1 = $season_of_day->num_rows();
            if($count1>0)
            {
                
             $deal1[]=array('title'=>"Daily Lifestyle Essentials",
                               'image'=>"",
                               'type'=>"2",
                               "flow_type"=>"2"
                               );   
            }
            $under_of_day = $this->db->query("SELECT * FROM `hm_under_product` WHERE status='0' ORDER BY `id` ASC LIMIT 4");
            $count2 = $under_of_day->num_rows();
            if($count2>0)
            {
                $deal1[]=array('title'=>"The 99 Store",
                                'image'=>"",
                                'type'=>"3",
                                "flow_type"=>"3",
                                );   
            } 
            
       
        $final=$deal1; 
        }
        return $final;
    }
      
        public function check_product($user_id,$pd_id_ex,$pincode){
     // print_r($data); 
      $pd_id=explode(",",$pd_id_ex);
        $data=array();       
      $isPanIndia = 0;
      for($i = 0 ; $i < count($pd_id); $i++)
      {
      $stmt = "SELECT product_details_hm.pd_name,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4, vendor_details_hm.is_panindia
    FROM product_details_hm
    JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
    WHERE product_details_hm.pd_id=".$pd_id[$i];
        $query = $this->db->query($stmt);
        $qarray = $query->row_array();
        $isPanIndia = $qarray['is_panindia'];
        if($isPanIndia == 1)
          {
            $pincode = $pincode;
            $stmt = "SELECT * FROM `pan_india` WHERE `pincodes` = $pincode";
            $query = $this->db->query($stmt);
            $num_count=$query->num_rows();
            if($num_count > 0)
            {
                $data[]=array(
                              'p_id' =>$pd_id[$i],
                              'message'=>"Product Available"
                             );
            }
            else
            {
                 $data[]=array(
                              'p_id' =>$pd_id[$i],
                              'message'=>"Product Not Available"
                             );
            }
          } 
        else 
          {
            $stmt = "SELECT product_details_hm.pd_name,vendor_details_hm.v_id,product_details_hm.pd_photo_1,product_details_hm.pd_photo_2,product_details_hm.pd_photo_3,product_details_hm.pd_photo_4, vendor_details_hm.is_panindia
                 FROM product_details_hm
                 JOIN vendor_details_hm ON vendor_details_hm.v_id = product_details_hm.pd_added_v_id
                 JOIN pincode_details ON vendor_details_hm.v_id = pincode_details.vp_id
                 WHERE product_details_hm.pd_id=".$pd_id[$i]." && pincode_details.pincode = ".$pincode."";
      $query = $this->db->query($stmt);
            $qarray = $query->row_array();
        $num_count1= $query->num_rows();
        if($num_count1 > 0)
            {
                $data[]=array(
                              'p_id' =>$pd_id[$i],
                              'message'=>"Product Available"
                             );
            }
            else
            {
                 $data[]=array(
                              'p_id' =>$pd_id[$i],
                              'message'=>"Product Not Available"
                             );
            }
        }
      }
      return $data;
    
  }
      
      
      
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
        $pk = "";
        $getUserDetails = $this->db->query("SELECT * FROM `users` WHERE `id` = '$user_id'")->row_array();
        // print_r($getUserDetails);
        $userName = $getUserDetails['name'];
        
        $today = $created_date = date("Y-m-d  H:i:s");
        // $codeDate = date("jmy");
        $codeDate = rand(0,999);
        
        $tomorrow = new DateTime($created_date);
       // $expiry = $tomorrow->format('Y-m-d H:i:s');
        
        $expiry=Date('Y-m-d H:i:s', strtotime("+1 month"));

        // $expiry = $created_date->modify('+1 day');
        $status = 1;

        $name = substr(strtolower(preg_replace('/[^a-zA-Z0-9]/','', $userName)), 0, 3);
        // $checkCode = $name.$user_id.$pd_id.$codeDate;
        $checkCode = $name.$pd_id.$codeDate;
        
        // check code
        
        $code = $this->check_code($checkCode,$user_id, $pd_id);
        // echo $code;
         //die();
         $productDetails  = $this->db->query("SELECT * FROM `product_details_hm` WHERE `pd_id` =  '$pd_id'")->row_array();
        
        
      /*  $share_url = "https://medicalwale.com/healthmall/#/product/".$pd_id;*/
       
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
                    $pk = $row['id'];
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
                $pk = $this->db->insert_id();
                $share_url = $share_url."/".$pk;
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
                $share_url = $share_url."/".$pk;
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
             $pk = $this->db->insert_id();
            $share_url = $share_url."/".$pk;
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
        
       /*PENDING - share url with refer code id append at the end of url*/
            
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
            
          
            $checkAgain = $this->check_code($codeNew,$user_id, $pd_id);
            
            $finalCode = $checkAgain;
           
        } else {
            
             $finalCode = $code;
            
        
        }
        
        return $finalCode;
    }
      
      
     public function add_physical_code($physical_code, $pd_id, $user_id){
        $pdCount = 0;
        $today = date("Y-m-d");
        $nonExpRows = array();
        $codeQuery = $this->db->query("SELECT * FROM `vendor_offers` WHERE `name` = '$physical_code' AND `status` = '0' AND `listing_id` = '34' AND end_date >= '$today' ");
        $count = $codeQuery->num_rows();
        $codeRows = $codeQuery->result_array();
        $today = date("Y-m-d");
        if($count > 0)
          {
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
               
            } 
            else {
                
                // SELECT * FROM `orders_details_hm` WHERE `user_id` = 5 AND `product_id` LIKE '1' AND `offer_id` = 2
                $offer_on = $nonExpRows[0]['offer_on'];
                $offerIds = $nonExpRows[0]['offer_on_ids'];
                $vendor_id = $nonExpRows[0]['vendor_id'];
                $id = $nonExpRows[0]['id'];
                $offerIdsArray = explode(', ', $offerIds);
                
                $pdArray = $this->applicable_to_products($offer_on, $offerIdsArray,$vendor_id); 
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
             
                //print_r($pdArray);
                
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
                    $data[]=array("pd_id" => $pd_id, "code_id" => $nonExpRows[0]['id'], "expiry" => $nonExpRows[0]['end_date']);
                    $message = "code is applicable to this product";
                    $data = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => $message,
                        "data" => $data 
                        );
                }
                
                
            } else {
                
                $message = "Already used this code";
                $data[]=array("pd_id" => $pd_id, "code_id" => $nonExpRows[0]['id'], "expiry" => $nonExpRows[0]['end_date']);
                    $data = array(
                        "status" => 401,
                        "message" => "failed",
                        "description" => $message,
                        "data" => $data
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
      
      // applicable_to_products
    
    public function applicable_to_products($offer_on, $offerIdsArray,$vendor_id){
        $allCats = $cats = $pds = array();
        //print_r($offerIdsArray);
        $brand_id=0;
        if($offer_on == 1 || $offer_on == 2){
             foreach($offerIdsArray as $offerIdOn){
                 
                $subCats = $this->get_subcat_1($offerIdOn);
                
            
             }
             
            
             foreach($subCats as $subCat){
                $cats[] =  $subCat[0];
             }
            //   = $subCats;
             $allCats = array_merge($cats,$offerIdsArray);
             $pds = $this->get_products_by_cats($allCats,$vendor_id);
        } else {
            $pds = explode(",",$offerIdsArray[0]);
        }
        
        // print_r(array_unique($allCats)); die();
        return $pds;
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
     
    public function get_subcat_1($dataSubcatId){
      $data = array();
      $results = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id`='$dataSubcatId'");

      foreach($results->result_array() as $result){
          
          $catId = $result['id'];
          
            $result_sub = $this->db->query("SELECT * FROM `categories` WHERE `parent_cat_id` = '$catId'");
            $prods = $this->db->query("SELECT * from product_details_hm WHERE pd_pc_id = '$catId' OR pd_psc_id = '$catId'")->result_array();
            $data_subcat_all = [] ;
            $data_subcat_all[] = $catId;
            if(sizeof($prods) != 0){
                foreach($result_sub->result_array() as $subcat){
                    
                    $dataSubcatIdInSub = $subcat['id'];
                    $results1 = $this->get_subcat_1($dataSubcatIdInSub);
                    
                    $data_subcat_all[] = $subcat['id'];
                   
                }
          $data[] = $data_subcat_all;
            }
      }
    
      return $data;
  }
     
    //  get_offer_products
  public function get_offer_products_offerid($user_id,$offer_id){ 
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
               $stmt = "SELECT * from product_details_hm pd  WHERE pd.pd_pc_id = '$pdId' $vendorQuery";
               
                $query = $this->db->query($stmt);
                  $rows = $query->result_array();
                  $rows_num = $query->num_rows();
                 // print_r($rows); die();
                  foreach($rows as $row){
                      $results = $this->get_offer_products_offerid($user_id,$row['pd_id']);
                      
                      foreach($results as $result){
                        
                          if($result['id'] == $offer_id){
                              $row['offer'] = $result;
                          }
                      }
                       $deal_array=array();
                      $p_id       = $row['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = $checkOffers;
                     
                      $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                     
                      $sku=$row['SKU_code1'];
                      $cat_id     = $row['pd_pc_id'];
                        $cat_sub_id = $row['pd_psc_id'];  
                        $v_id       = $row['pd_added_v_id'];
                        $manuf_id   = $row['manuf_id'];
                        $brand_name = $row['brand_name'];
                        $pd_name    = $row['pd_name'];
                        $pd_photo_1 = $row['pd_photo_1'];
                        $pd_mrp_price =$row['pd_mrp_price'];
                        $pd_vendor_price =$row['pd_vendor_price'];
                        $pd_quantity =$row['pd_quantity'];
                        $discount= $row['pd_discount_percentage'];
                        $rating = $row['pd_overall_ratting'];
                        $view_count = $row['pd_view_count'];
                        $pd_status = $row['pd_status'];
                        $best_seller = $row['pd_best_seller'];
                        $item_type = $row['item_type'];
                        $color = $row['color'];
                        $size  = $row['size'];
                        $flavor = $row['flavor'];
                        $gender_type  = $row['gender_type'];
                        $speciality = $row['speciality'];
                        $is_featured  = $row['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                          $all_products[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                  }
                  
                  $all_products_count = $all_products_count + $rows_num; 
                 // $offer['products_count'] = $rows_num;
          
              }
           
       } 
       
       if($offer['offer_on'] == '2' ){
              $str = $offer['offer_on_ids'];
              $afterstr =  (explode(",",$str));
              for($i=0;$i<count($afterstr);$i++){
                  $pdId = $afterstr[$i];
                 // $stmt = "SELECT pd.pd_id,pd.pd_added_v_id,pd.pd_vendor_price,pd.pd_short_desc,pd.pd_name,pd.pd_photo_1,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_company_logo,pd.pd_mrp_price,pd.pd_overall_ratting as ratting FROM vendor_details_hm vd join product_details_hm pd on vd.v_id=pd.pd_added_v_id WHERE pd.pd_pc_id = '$pdId'";
               $stmt = "SELECT * From product_details_hm pd  WHERE pd.pd_psc_id = '$pdId' $vendorQuery";
               
                $query = $this->db->query($stmt);
                  $rows = $query->result_array();
                  $rows_num = $query->num_rows();
                 // print_r($rows); die();
                  foreach($rows as $row){
                      $results = $this->get_offer_products_offerid($user_id,$row['pd_id']);
                      
                      foreach($results as $result){
                        
                          if($result['id'] == $offer_id){
                              $row['offer'] = $result;
                          }
                      }
                      $deal_array=array();
                      $p_id       = $row['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = $checkOffers;
                     
                      $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                     
                      $sku=$row['SKU_code1'];
                      $cat_id     = $row['pd_pc_id'];
                        $cat_sub_id = $row['pd_psc_id'];  
                        $v_id       = $row['pd_added_v_id'];
                        $manuf_id   = $row['manuf_id'];
                        $brand_name = $row['brand_name'];
                        $pd_name    = $row['pd_name'];
                        $pd_photo_1 = $row['pd_photo_1'];
                        $pd_mrp_price =$row['pd_mrp_price'];
                        $pd_vendor_price =$row['pd_vendor_price'];
                        $pd_quantity =$row['pd_quantity'];
                        $discount= $row['pd_discount_percentage'];
                        $rating = $row['pd_overall_ratting'];
                        $view_count = $row['pd_view_count'];
                        $pd_status = $row['pd_status'];
                        $best_seller = $row['pd_best_seller'];
                        $item_type = $row['item_type'];
                        $color = $row['color'];
                        $size  = $row['size'];
                        $flavor = $row['flavor'];
                        $gender_type  = $row['gender_type'];
                        $speciality = $row['speciality'];
                        $is_featured  = $row['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                          $all_products[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
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
                  $stmt = "SELECT * from product_details_hm  WHERE pd_id = '$pdId'";
                $query = $this->db->query($stmt);
                  $rows = $query->result_array();
                  $rows_num = $query->num_rows();
                  foreach($rows as $row){
                      $results = $this->get_offer_products_offerid($user_id,$row['pd_id']);
                      
                      foreach($results as $result){
                          if($result['id'] == $offer_id){
                              $row['offer'] = $result;
                          }
                      }
                      $deal_array=array();
                      $p_id       = $row['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = $checkOffers;
                     
                      $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                     
                      $sku=$row['SKU_code1'];
                      $cat_id     = $row['pd_pc_id'];
                        $cat_sub_id = $row['pd_psc_id'];  
                        $v_id       = $row['pd_added_v_id'];
                        $manuf_id   = $row['manuf_id'];
                        $brand_name = $row['brand_name'];
                        $pd_name    = $row['pd_name'];
                        $pd_photo_1 = $row['pd_photo_1'];
                        $pd_mrp_price =$row['pd_mrp_price'];
                        $pd_vendor_price =$row['pd_vendor_price'];
                        $pd_quantity =$row['pd_quantity'];
                        $discount= $row['pd_discount_percentage'];
                        $rating = $row['pd_overall_ratting'];
                        $view_count = $row['pd_view_count'];
                        $pd_status = $row['pd_status'];
                        $best_seller = $row['pd_best_seller'];
                        $item_type = $row['item_type'];
                        $color = $row['color'];
                        $size  = $row['size'];
                        $flavor = $row['flavor'];
                        $gender_type  = $row['gender_type'];
                        $speciality = $row['speciality'];
                        $is_featured  = $row['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                          $all_products[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                      
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
    
    
     public function  get_filter_list($user_id,$brand_id,$offer_id,$cat_id)
   {
      $final_data=array();
      $data=array();
      $final_cat=array();
      $result2=array();
      $results1=array();
      if($brand_id!=0)
      {
          $wh="AND pd_added_v_id = '$brand_id'";
           
           $results1=array();
           $results1['product_count']="0";
           $results = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE parent_cat_id='0'");
           foreach ($results->result_array() as $result) 
                   {
                      $catId = $result['id']; 
                     
                     $result_sub = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id` = '$catId'");
               $prods      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE pd_pc_id = '$catId' and pd_status='1' $wh ");
                 $get_list   = $prods->row_array();
                     $pro        = $get_list['p_count'];
                     if ($pro > 0) 
                     {
                        $dataSubcatIdInSub = $result['id'];
                        $subcat_photo = $result['photo'];
              $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
              $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                        $results1 = $this->get_subcat_name($dataSubcatIdInSub);
                        $data[] = array(
                                        "pc_id" => $dataSubcatIdInSub,
                                        "pc_name" => $result['cat_name'],
                                        "subcategory" => $results1
                                        );   
                     }
                }
                
            $final_cat[]=array('filter_name'=>"Categories",
                                 'filter_array'=>$data); 
                                 
                                 
             $data_gender = $this->getallgender();                           
            $final_bran[]=array('filter_name'=>"Gender",
                                 'filter_array'=>$data_gender);                      
            $final_data=array_merge($final_cat,$final_bran);                        
      }
      
      if($cat_id!=0)
      {
          $results1 = $this->get_subcat_name($cat_id);
          $final_cat[]=array('filter_name'=>"Categories",
                              'filter_array'=>$results1); 
                              
          $prods    = $this->db->query("SELECT DISTINCT pd_added_v_id,brand_name from product_details_hm WHERE (pd_pc_id = '$cat_id' OR pd_psc_id = '$cat_id') and pd_status='1'");
      $count = $prods->num_rows();
         if($count>0)
          {
      foreach($prods->result_array() as $get_list)
      {
        
                        $brand_id = $get_list['pd_added_v_id'];
                        $brand_name = $get_list['brand_name'];
                        
                        $result2[] = array(
                                        "brand_id" => $brand_id,
                                        "brand_name" => $brand_name,
                                        
                                        ); 
                     
      }  
        }
          $final_bran[] =array('filter_name'=>"Brand",
                               'filter_array'=>$result2);
          $data_gender = $this->getallgender();                    
          $final_gender[]=array('filter_name'=>"Gender",
                                 'filter_array'=>$data_gender);                       
         $final_data=array_merge($final_cat,$final_bran,$final_gender);                       
      }
      
      if($offer_id!=0)
      {
          
           $today = $created_date = date("Y-m-d");
      $vendor_offers = $this->db->query("SELECT * FROM `vendor_offers` WHERE `id` = '$offer_id' AND end_date >= '$today' ");
      $offer =  $vendor_offers->row_array();
      $vendor_offers_nums =  $vendor_offers->num_rows();
      if($vendor_offers_nums > 0 )
      {
          $offer_on=$offer['offer_on'];
          
          if($offer_on !='3')
          {
            $cat_id=explode(",",$offer['offer_on_ids']);  
            $results1=array();
            for($i=0;$i < count($cat_id); $i++)
               {
                   $stmt = "SELECT id,cat_name,photo FROM categories WHERE id = '$cat_id[$i]'";
                 $query = $this->db->query($stmt);
                 $query1 = $query->num_rows();
                     if($query1 > 0)
                     {
                         $q = $query->row_array();
                         $dataSubcatIdInSub = $q['id'];
                         $subcat_photo = $q['photo'];
                 $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                 $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                         $results1 = $this->getallSubCategoriesHealthMall($dataSubcatIdInSub);
                         $data[] = array(
                                          "pc_id" => $dataSubcatIdInSub,
                                          "pc_name" => $q['cat_name'],
                                          "pc_photo"=>$subcat_photo,
                                          "subcategory" => $results1
                                        ); 
                     }     
                 }
             $final_cat[]=array('filter_name'=>"Categories",
                              'filter_array'=>$data); 
          }
      }
          $data_gender = $this->getallgender();                    
          $final_gender[]=array('filter_name'=>"Gender",
                                 'filter_array'=>$data_gender);                       
         $final_data=array_merge($final_cat,$final_gender);                       
      }
     
      if($brand_id==0 and $cat_id==0)
      {
          $wh="AND pd_quantity > 0 ";
           // Categories 
           $results1=array();
           $results2=array();
           $results1['product_count']="0";
           $results = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE parent_cat_id='0'");
           foreach ($results->result_array() as $result) 
                   {
                      $catId = $result['id']; 
                     
                     $result_sub = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id` = '$catId'");
               $prods      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE pd_pc_id = '$catId' and pd_status='1' $wh ");
                 $get_list   = $prods->row_array();
                     $pro        = $get_list['p_count'];
                     if ($pro > 0) 
                     {
                        $dataSubcatIdInSub = $result['id'];
                        $subcat_photo = $result['photo'];
              $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
              $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                        $results1 = $this->get_subcat_name($dataSubcatIdInSub);
                        $data[] = array(
                                        "pc_id" => $dataSubcatIdInSub,
                                        "pc_name" => $result['cat_name'],
                                        "subcategory" => $results1
                                        );   
                     }
                }
                
            $final_cat[]=array('filter_name'=>"Categories",
                                 'filter_array'=>$data); 
                // Categories 
                
                  // Brand  
                  
                 $prods_brand    = $this->db->query("SELECT DISTINCT pd_added_v_id,brand_name from product_details_hm WHERE  pd_status='1' $wh ");
      $count_brand = $prods_brand->num_rows();
         if($count_brand>0)
          {
      foreach($prods_brand->result_array() as $get_list_brand)
      {
        
                        $brand_id = $get_list_brand['pd_added_v_id'];
                        $brand_name = $get_list_brand['brand_name'];
                        
                        $result2[] = array(
                                        "brand_id" => $brand_id,
                                        "brand_name" => $brand_name,
                                        
                                        ); 
                     
      }  
        }
          $final_bran[] =array('filter_name'=>"Brand",
                               'filter_array'=>$result2);     
                  
                  
                    // Brand 
                                 
             $data_gender = $this->getallgender();                           
            $final_gender[]=array('filter_name'=>"Gender",
                                 'filter_array'=>$data_gender);                      
            $final_data=array_merge($final_cat,$final_bran,$final_gender);                        
      }
     
     
     
      return $final_data;
       
   }
    
 
    
      public function getallSubCategoriesHealthMall($c_id)
      {
          $data=array();
          $stmt = "SELECT id,cat_name,photo FROM categories WHERE parent_cat_id = '$c_id'";
        $query = $this->db->query($stmt);
        $query1 = $query->result_array();
            $querys = $query->result_array();
            foreach($querys as $q)
                   {
                    $dataSubcatIdInSub = $q['id'];
                    $subcat_photo = $q['photo'];
          $subcat_photo = str_replace("https://s3.amazonaws.com/medicalwale/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
          $subcat_photo = str_replace("https://medicalwale.s3.amazonaws.com/","https://d2c8oti4is0ms3.cloudfront.net/",$subcat_photo);
                    $results1 = $this->getallSubCategoriesHealthMall($dataSubcatIdInSub);
                    $data[] = array(
                                    "pc_id" => $dataSubcatIdInSub,
                                    "pc_name" => $q['cat_name'],
                                    "pc_photo"=>$subcat_photo,
                                    "subcategory" => $results1
                                    ); 
                    }
          return $data;
     }
      public function getallgender()
      {
          $stmt = "SELECT * FROM hm_gender_master WHERE status = '0'";
        $query = $this->db->query($stmt);
        $query1 = $query->result_array();
            $querys = $query->result_array();
            foreach($querys as $q)
                   {
                    $dataSubcatIdInSub = $q['id'];
                    
                    $data[] = array(
                                    "id" => $dataSubcatIdInSub,
                                    "gender" => $q['gender'],
                                    
                                    ); 
                    }
          return $data;
     }
   
        public function get_subcat_name($dataSubcatId){
        $data = array();
        $results = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id`='$dataSubcatId'");
        foreach ($results->result_array() as $result) { 
            $catId = $result['id'];
            $result_sub = $this->db->query("SELECT id,cat_name,photo FROM `categories` WHERE `parent_cat_id` = '$catId'");
      $prods      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE pd_pc_id = '$catId' OR pd_psc_id = '$catId'");
        $get_list   = $prods->row_array();
            $pro      = $get_list['p_count'];     
            $data_subcat_all = [];
            if ($pro>0) {
                foreach ($result_sub->result_array() as $subcat) {
                    $dataSubcatIdInSub = $subcat['id'];
          
                    $results1 = $this->get_subcat_name($dataSubcatIdInSub);
                    $dataSubcat['pc_id'] = $subcat['id'];
                    $dataSubcat['pc_name'] = $subcat['cat_name'];
                   
                    $dataSubcat['subcategory'] = $results1;
                    $data_subcat_all[] = $dataSubcat;
                }
        
                $cat['pc_id'] = $result['id'];
                $cat['pc_name'] = $result['cat_name'];
                
                $cat['subcategory'] = $data_subcat_all;
                $data[] = $cat;
            }
        }
      
        return $data;
  }
    
    public function hm_menu_list4($user_id){
    
       date_default_timezone_set("Asia/Calcutta");
        $date=date('Y-m-d');
        $day=date('Y-m-d H:i:s');
        $datetime1 = date('Y-m-d H:i:s'); 
        $limit = "LIMIT 10 OFFSET 1"; 
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE tryon_status='1' AND  pd_status = 1 $limit";
        $productArray = array();
        $gender1=array();
        $gender2=array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
        if($rows_num > 0)
          {
              
            foreach($query->result_array() as $q)
                   {
                        $deal_array=array();
                      $p_id       = $q['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id' AND try_on='1' ");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id' AND try_on='1'");
                        $fav = $checkAvailable1->num_rows();
                        
                     
                      $sku=$q['SKU_code1'];
                      $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_photo_2 = $q['pd_photo_2'];
                        $pd_photo_3 = $q['pd_photo_3'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $try_price = $q['tryon_price'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                      
                        $sharePdName = $this->slugify($pd_name);
                        $share_url = "https://medicalwale.com/healthmall/product_details/".$sharePdName."/".$p_id;
                      
                          $productArray[] = array( "p_id"=> $p_id,
                                                     "cat_id"=> $cat_id,
                                                     "cat_sub_id"=> $cat_sub_id,
                                                     "v_id"=> $v_id,
                                                     "manuf_id"=>$manuf_id,
                                                     "sku"=>$sku,
                                                     "brand_name"=>$brand_name,
                                                     "product_name" =>$pd_name,
                                                     "front_photo" => $pd_photo_1,
                                                     "try_on_image"=>$pd_photo_2,
                                                     "try_on_video"=>$pd_photo_3,
                                                     "try_on_price"=>$try_price,
                                                     "try_on_quantity"=>$pd_quantity,
                                                     "overall_ratting"=>$rating,
                                                     "view_count" => $count_view,
                                                     "status" => $pd_status,
                                                     "is_fav"=>$fav,
                                                     "is_cart" => $cart,
                                                     "description"=>"",
                                                     "share_url"=>$share_url
                                                );  
                    
                   
             }
           $gender1[]=array('title'=>"Try On",
                           'image'=>"",
                           'type'=>"5",
                           "flow_type"=>"7",
                           'start_time'=>$datetime1,
                           'end_time'=>$day,
                           'color'=>"ffffff",
                           'background_image'=>"",
                           'total_deal'=>$productArray);      
             
          }    
          //$t="Baby, Kids & Moms";
          //$im="64";
          $t="COVID-19";
          $im="283";
       $gender3=$this->Product_home($im,$t,$user_id);
       if(count($gender3) > 0)
       {
           $gender2=$gender3;
       }
  
        
      $final=array_merge($gender1,$gender2);
        return $final;
       
  }
    
    public function Product_home($id,$title,$user_id)
    {
        date_default_timezone_set("Asia/Calcutta");
        $date=date('Y-m-d');
        $day=date('Y-m-d H:i:s');
        $datetime1 = date('Y-m-d H:i:s'); 
        $limit = "LIMIT 0,10"; 
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE pd_pc_id = '$id'  AND  pd_status = 1 $limit";
        $productArray = array();
        $gender1=array();
        $gender2=array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
         if($rows_num > 0)
          {
              
            foreach($query->result_array() as $q)
                   {
                        $deal_array=array();
                      $p_id       = $q['pd_id'];
                      $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id' AND try_on='0'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id' AND try_on='0'");
                        $fav = $checkAvailable1->num_rows();
                        
                      $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                      $offers = $checkOffers;
                     
                      $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                      $deal_array = $checkOffers_deal;
                     
                      $sku=$q['SKU_code1'];
                      $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                      
                          $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                     
                   
             }
              $gender2[]=array('title'=>$title,
                           'image'=>"",
                           'type'=>"5",
                           "flow_type"=>"8",
                           "cat_id"=>"$id",
                           'start_time'=>$datetime1,
                           'end_time'=>$day,
                           'color'=>"ffffff",
                           'background_image'=>"",
                           'total_deal'=>$productArray); 
          }   
          return $gender2;
    }
    
    public function hm_menu_sponor($user_id,$page_no)
    {
       $data= array();
        date_default_timezone_set("Asia/Calcutta");
        $date=date('Y-m-d');
        $this11 = new \stdClass();
        $url="https://dhlr-bot.medicalwale.com/ads/healthmall/";
        $this11->user_id=$user_id;
  $this11->page_no=$page_no;    
        $data11 = json_encode($this11);
        $data12=healthwall_adver_crul($url,$data11);
        $data131 = json_decode($data12); 
        if(!empty($data131))
          {
             $data =$data131;
          }
        $final=$data;
        return $final; 
    }
   
         public function hm_menu_list5($user_id,$page_no,$per_page){
    
        date_default_timezone_set("Asia/Calcutta");
        $datetime1 = date('Y-m-d H:i:s'); 
        $productArray=$this->get_product_list_hm5_front($user_id,$page_no,$per_page,9,1);
             $gender1[]=array('title'=>"Ready Stock",
                           'image'=>"",
                           'type'=>"9",
                           "flow_type"=>"9",
                           'start_time'=>$datetime1,
                           'end_time'=>date('Y-m-d H:i:s'),
                           'color'=>"ffffff",
                           'background_image'=>"",
                           'total_deal'=>$productArray);      
        return $gender1;
       
    }
    
    public function get_product_list_hm5_front($user_id,$page_no,$per_page,$type,$response)
    {
        date_default_timezone_set("Asia/Calcutta");
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset";
        $wh = "";
        if($type=="9")
        {
          $wh .= "pd_quantity > 0 AND";  
        }
        $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE $wh pd_status = 1")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE $wh pd_status = 1 ORDER BY 	is_featured=0,is_featured,pd_quantity DESC $limit";
        $productArray = array();
        $gender1=array();
        $gender2=array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
         if($rows_num > 0)
          {
              
            foreach($query->result_array() as $q)
                   {
                        $deal_array=array();
                        $p_id       = $q['pd_id'];
                        $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id' AND try_on='0'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id' AND try_on='0'");
                        $fav = $checkAvailable1->num_rows();
                        
                        $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                        $offers = $checkOffers;
                       
                        $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                        $deal_array = $checkOffers_deal;
                       
                        $sku=$q['SKU_code1'];
                        $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                      
                            $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                       
                     
               }
                
          }  
          
        if($response == "0")
        {
        $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
        return $data;
        }
        else
        {
          return $productArray;  
        }
    }              
               
public function get_product_list_hm5($user_id,$page_no,$per_page,$type,$response,$brands,$category_ids,$sort,$price_min,$price_max,$offer,$best_seller,$gender)
    {
        date_default_timezone_set("Asia/Calcutta");
        $offset = $per_page*($page_no - 1);
        $limit = "LIMIT $per_page OFFSET $offset";
        $wh = "";
        if($type=="9")
        {
          $wh .= "pd_quantity > 0 AND";  
        }
        
         if(!empty($brands))
          {
            $brnd = $brands;
          } 
        else 
         {
            $allBrands =  $this->db->query("SELECT `v_id` FROM `vendor_details_hm` WHERE `v_status` = 1")->result_array();
            foreach($allBrands as $brand)
                    {
                     $brnd[] = $brand['v_id'];
                    }
            $brnd = implode(",",$brnd);
          }
        if(!empty($category_ids))
          {
            $catFilterArray = explode(",",$category_ids);
            $allSubcategories[] = $catFilterArray;
            $subCatsProducts=array();
            foreach($catFilterArray as $catId)
                  {
                    $subCatsProducts1 =  $this->HealthmallModel->get_subcat_id($catId);
                    
                    if(sizeof($subCatsProducts1) > 0)
                              { 
                                $subCatsProducts = call_user_func_array("array_merge", $subCatsProducts1);
                              } 
                              
                    $allSubcategories[] = $subCatsProducts;
                  }
            $allSubcategories = call_user_func_array("array_merge", $allSubcategories);
            
            $all=implode(",",$allSubcategories);
            
          } 
        else 
         {
            $allcats =  $this->db->query("SELECT id FROM categories WHERE 1")->result_array();
            foreach($allcats as $cat)
                  {
                     $cats[] = $cat['id'];
                  }  
            $all = implode(",",$cats);
        }
        
        $sort_a_zh = "";
        if(!empty($sort))
        {
            if($sort=="1")
            {
                $sort_a_zh .= "ORDER BY pd_name ASC";
            }
            elseif($sort=="2")
            {
                $sort_a_zh .= "ORDER BY pd_name DESC";
            }
            elseif($sort=="3")
            {
                $sort_a_zh .= "ORDER BY pd_vendor_price ASC";
            }
            elseif($sort=="4")
            {
                $sort_a_zh .= "ORDER BY pd_vendor_price DESC";
            }
        }
        else
        {
           $sort_a_zh .= "ORDER BY 	is_featured=0,is_featured,pd_quantity DESC "; 
        }
        //column_name BETWEEN value1 AND value2
        $wh_price ="";
        if(!empty($price_min) and !empty($price_max))
        {
           $wh_price .="AND pd_vendor_price BETWEEN '$price_min' AND '$price_max'";
        }
        elseif(!empty($price_min) and empty($price_max))
        {
            $wh_price .="AND pd_vendor_price >= '$price_min'";
        }
        elseif(empty($price_min) and !empty($price_max))
        {
            $wh_price .="AND pd_vendor_price <= '$price_max'";
        }
        
         $wh_best_seller ="";
        if(!empty($best_seller))
        {
           $wh_best_seller .="AND pd_best_seller > 0";
        }
        else
        {
            $wh_best_seller .="";
        }
        $wh_gender = "";
        
        if(!empty($gender))
        {
           $wh_gender .="AND gender_type IN (".$gender.")";
        }
        else
        {
            $wh_gender .="";
        }
        
         $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all.")) AND $wh pd_status = 1 $wh_price $wh_best_seller $wh_gender ORDER BY 	is_featured=0,is_featured,pd_quantity DESC")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))  AND $wh pd_status = 1 $wh_price $wh_best_seller $wh_gender $sort_a_zh $limit";
        
        
      /*  $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE $wh pd_status = 1")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE $wh pd_status = 1 ORDER BY 	is_featured=0,is_featured,pd_quantity DESC $limit";*/
        $productArray = array();
        $gender1=array();
        $gender2=array();
        $offers=array();
        $query = $this->db->query($finalData);
        $rows_num = $query->num_rows();
         if($rows_num > 0)
          {
              
            foreach($query->result_array() as $q)
                   {
                        $deal_array=array();
                        $p_id       = $q['pd_id'];
                        $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id' AND try_on='0'");
                        $cart = $checkAvailable->num_rows();
                        $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id' AND try_on='0'");
                        $fav = $checkAvailable1->num_rows();
                        
                        $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
                        $offers = $checkOffers;
                       
                        $checkOffers_deal = $this->HealthmallModel->get_deal_price($p_id);
                        $deal_array = $checkOffers_deal;
                       
                        $sku=$q['SKU_code1'];
                        $cat_id     = $q['pd_pc_id'];
                        $cat_sub_id = $q['pd_psc_id'];  
                        $v_id       = $q['pd_added_v_id'];
                        $manuf_id   = $q['manuf_id'];
                        $brand_name = $q['brand_name'];
                        $pd_name    = $q['pd_name'];
                        $pd_photo_1 = $q['pd_photo_1'];
                        $pd_mrp_price =$q['pd_mrp_price'];
                        $pd_vendor_price =$q['pd_vendor_price'];
                        $pd_quantity =$q['pd_quantity'];
                        $discount= $q['pd_discount_percentage'];
                        $rating = $q['pd_overall_ratting'];
                        $view_count = $q['pd_view_count'];
                        $pd_status = $q['pd_status'];
                        $best_seller = $q['pd_best_seller'];
                        $item_type = $q['item_type'];
                        $color = $q['color'];
                        $size  = $q['size'];
                        $flavor = $q['flavor'];
                        $gender_type  = $q['gender_type'];
                        $speciality = $q['speciality'];
                        $is_featured  = $q['is_featured'];
                        $count_view= $this->HealthmallModel->number_format_short( $view_count, $precision = 1 );
                        if(empty($pd_quantity))
                        {
                            $pd_quantity="0";
                        }
                        if(empty($discount))
                        {
                            $discount="0";
                        }
                        if(empty($manuf_id))
                        {
                            $manuf_id="0";
                        }
                       $variable_colors=array(); 
                       $variable_sizes=array();
                       $vQuery  = $this->db->query("SELECT  `color`, `size`,`id` as variable_pd_id FROM `variable_products_hm` WHERE `pd_id` = '$p_id'");
                       $rows_num1 = $vQuery->num_rows();
                       if($rows_num1 > 0)
                         {
                              foreach($vQuery->result_array() as $v)
                              {
                                $c = $v['color'];
                                $s = $v['size'];
                                $vp_id = $v['variable_pd_id'];
                                if($c != null && $c != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$c'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                                if($s != null && $s != '')
                                    {
                                        $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$s'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }
                                    }
                              }
                         }
                         else
                         {
                            if($color != null && $color != '')
                                    {
                                        $colorQ = $this->db->query("SELECT * FROM `color_hm` WHERE `id` = '$color'");
                                        foreach($colorQ->result_array() as $colorRows)
                                           {
                                                $colorValue= $colorRows['color_code'];
                                                $variable_colors[] = $colorValue;
                                            }    
                                    }
                            if($size != null && $size != '')
                                    {
                                         $sizeQ = $this->db->query("SELECT * FROM `size_chart_hm` WHERE `id` = '$size'");
                                        foreach($sizeQ->result_array() as $sizeRows)
                                                {
                                                 $sizeValue = $sizeRows['size_name'];
                                                 $variable_sizes[] = $sizeValue;
                                                }   
                                    }        
                            
                         }
                        if(sizeof($variable_colors) <= 0)
                        {
                            $colors_final="";
                        }
                        else
                        {
                            $colors_final=implode(",",$variable_colors);
                        }
                        
                        if(sizeof($variable_sizes) <= 0)
                        {
                            $size_final="";
                        }
                        else
                        {
                            $size_final=implode(",",$variable_sizes);
                        }
                      
                            $productArray[] = array( "p_id"=> $p_id,
                                                 "cat_id"=> $cat_id,
                                                 "cat_sub_id"=> $cat_sub_id,
                                                 "v_id"=> $v_id,
                                                 "manuf_id"=>$manuf_id,
                                                 "sku"=>$sku,
                                                 "brand_name"=>$brand_name,
                                                 "product_name" =>$pd_name,
                                                 "front_photo" => $pd_photo_1,
                                                 "mrp"=>$pd_mrp_price,
                                                 "vendor_price"=>$pd_vendor_price,
                                                 "quantity"=>$pd_quantity,
                                                 "discount_percentage"=>$discount,
                                                 "overall_ratting"=>$rating,
                                                 "view_count" => $count_view,
                                                 "status" => $pd_status,
                                                 "best_seller"=>$best_seller,
                                                 "item_type"=>$item_type,
                                                 "color"=>$colors_final,
                                                 "size"=>$size_final,
                                                 "flavor"=>$flavor,
                                                 "gender_type" => $gender_type,
                                                 "speciality" => $speciality,
                                                 "is_featured"=>$is_featured,
                                                 "offers"=>$offers,
                                                 "is_fav"=>$fav,
                                                 "is_cart" => $cart,
                                                 'deal_offer'=>$deal_array
                                                );  
                       
                     
               }
                
          }  
          
        if($response == "0")
        {
        $store_count=count($finalDataNew);
        $last_page = ceil($store_count/$per_page);
        $data['status'] = 200;
        $data['message'] = "Success";
        $data['data_count'] = intval($store_count);
        $data['per_page'] = $per_page;
        $data['current_page'] = $page_no;
        $data['first_page'] = 1;
        $data['last_page'] = $last_page;
        $data['data'] = $productArray;
        return $data;
        }
        else
        {
          return $productArray;  
        }
    }    





       
    } 

                   
                       
                    
                   
              

?>
