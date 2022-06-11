<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class HealthmallModel extends CI_Model {
    
    
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
        $top_menu = $this->db->query("SELECT menu_id , title , image , type FROM `hm_top_menu_master` WHERE status = '1' ORDER BY `menu_order` ASC ")->result_array();
        $data['top_menu'] = $top_menu;
        $data['description'] = 'Type will decide where to redirect. 1 : Categories /get_categories; 2:brands; 3: offers; 4: try on ; 5 : deals ';
        return $data;
    }
    public function top_menu_brand($user_id){
        $data= array();
        $d=array();
        $top_menu = $this->db->query("SELECT menu_id , title , image , type FROM `hm_top_menu_master` WHERE status = '1' ORDER BY `menu_order` ASC ");
        $count = $top_menu->num_rows();
       if($count>0)
        {
             $d[]=array( "menu_id"=> "0",
                "title"=> "About Us",
                "image"=> "https://medicalwale.s3.amazonaws.com/images/assets/Healthmall/Icons/category.png",
                "type"=> "0"
                        );
         foreach ($top_menu->result_array() as $row) 
                 {
                    $menu_id= $row['menu_id'];
                    $title = $row['title'];
                    $image = $row['image'];  
                    $type = $row['type'];
                    $d[] = array( "menu_id"=> $menu_id,
                "title"=> $title,
                "image"=> $image,
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
		    $get_list  	= $prods->row_array();
            $pro 	    = $get_list['p_count'];
            if ($pro>0) {
                $data_subcat_all = [];
                $dataSubcat['product_count']=0;
                foreach ($result_sub->result_array() as $subcat) {
                    $dataSubcatId = $subcat['id'];
                    $prods1      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE (pd_pc_id IN ('$dataSubcatId') OR `pd_psc_id` IN ('$dataSubcatId')) and pd_status='1'  $wh ");
        		    $get_list1  	= $prods1->row_array();
                    $pro1 	    = $get_list1['p_count'];
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
		    $get_list  	= $prods->row_array();
            $pro 	    = $get_list['p_count'];			
            $data_subcat_all = [];
           
            if ($pro>0) {
                 $dataSubcat['product_count'] = 0;
                foreach ($result_sub->result_array() as $subcat) {
                    
                    $dataSubcatIdInSub = $subcat['id'];
                    $prods1      = $this->db->query("SELECT COUNT(pd_id) as p_count from product_details_hm WHERE (pd_pc_id IN ('$dataSubcatIdInSub') OR `pd_psc_id` IN ('$dataSubcatIdInSub')) and pd_status='1'  $wh ");
        		    $get_list1  	= $prods1->row_array();
                    $pro1 	    = $get_list1['p_count'];
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
            $wh .="ORDER BY rank = 0, rank";
        }
        
        $finalDataNew = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id  WHERE `v_status` = '1' $wh ")->result_array();
        $withrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' $wh  $limit")->result_array();
       /* $withoutrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' and `rank` = 0 ORDER BY `rank`")->result_array();
        */
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
	            
	            $vendor_id = 	$query1['pd_added_v_id'];
		} 
		
// 		echo  $vendor_id; die();
		
		
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
 
        public function get_brand_images($user_id,$vid)
        {
           $d=array();
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
	
public function get_product_list($brands,$page_no,$per_page,$category_ids,$sort,$user_id,$price_min,$price_max,$offer_count,$best_seller){
    
       
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
        
        
        $finalDataNew = $this->db->query("SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all.")) AND pd_status = 1 $wh_price $wh_best_seller")->result_array();
        $finalData = "SELECT DISTINCT * FROM `product_details_hm` WHERE pd_added_v_id IN (".$brnd.") AND  (`pd_pc_id` IN (".$all.") OR `pd_psc_id` IN (".$all."))  AND pd_status = 1 $wh_price $wh_best_seller $sort_a_zh $limit";
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
                                                 "is_cart" => $cart
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
	
	public function get_product_details($pd_id,$user_id)
	{
	    $specifications =  $products = $offers = array();
	    date_default_timezone_set('Asia/Kolkata');
        $today = date('Y-m-d');
	    
	    $recentlyViewed = $this->db->query("SELECT * FROM `recently_viewed_products` WHERE `user_id` = '$user_id' AND `pd_id` = '$pd_id'");
	    $recentlyViewedRows = $recentlyViewed->row_array();
	    $recentlyViewedCount = $recentlyViewed->num_rows();
	    $viewed_date = date('Y-m-d H:i:s');
	    if($recentlyViewedCount > 0){
	        $recentId = $recentlyViewedRows['id'];
	        $this->db->query("UPDATE `recently_viewed_products` SET `viewed_date` = '$viewed_date' WHERE `id` = $recentId;");
	    } else {
	        $this->db->query("INSERT INTO `recently_viewed_products` (`user_id`, `pd_id`, `viewed_date`) VALUES ('$user_id', '$pd_id', '$viewed_date')");
	    }
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
    	    if(!empty($p_id))
    	    {
                $cat_id     = $query1['pd_pc_id'];
                $cat_sub_id = $query1['pd_psc_id'];  
                $v_id       = $query1['pd_added_v_id'];
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
                $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$p_id'  AND `customer_id` = '$user_id'");
                $cart = $checkAvailable->num_rows();
                $checkAvailable1 = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `product_id` = '$p_id' AND `customer_id` = '$user_id'");
                $fav = $checkAvailable1->num_rows();
                $checkOffers = $this->HealthmallModel->get_product_offer($p_id);
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
            
        
              
              
             
              
              
              
              
                             $finalArray[] = array("product_name" =>$pd_name,
                                       "vendor_price"=>$pd_vendor_price,
                                       "mrp"=>$pd_mrp_price,
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
                                       "p_id"=> $p_id
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

    public function add_to_wishlist($product_id,$type,$user_id)
    {
        //0 means remove from whislit & 1 means add wishlist
	    if($type=="0")
	    {
	        $checkAvailable = $this->db->query("DELETE FROM `customer_wishlist` WHERE `customer_id` = '$user_id' AND `product_id` = '$product_id' ");
            $affected_rows = $this->db->affected_rows();
            if($affected_rows > 0)
                {
                 $res = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => "Successfully remove to interest list",
                         
                    );  
                } 
            else 
               {
                    $res = array(
                        "status" => 400,
                        "message" => "fail",
                        "description" => "Something went wrong",
                    );
                }
    	}
	    elseif($type=="1")
	    {
	       $results = $this->db->query("INSERT INTO `customer_wishlist` (`product_id`, `customer_id`) VALUES ('$product_id', '$user_id');");
    	   $insert_id = $this->db->insert_id(); 
    	   if($insert_id)
    	      {
    	        $res = array(
                    "status" => 200,
                    "message" => "success",
                    "description" => "Successfully added to interest list",
                     
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
	      $res = array(
                    "status" => 400,
                    "message" => "fail",
                    "description" => "Something went wrong",
                );  
	    }
	   
	    return $res;
	}

public function get_user_wishlist($user_id){
        
        $checkAvailable = $this->db->query("SELECT * FROM `customer_wishlist` WHERE `customer_id` = '$user_id'");
        
        $count = $checkAvailable->num_rows();
        
        if($count > 0){
            
            foreach($checkAvailable->result_array() as $prodInfo)
            {
                $product_id = $prodInfo['product_id'];
               
                 $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,d.pd_quantity,d.pd_long_desc,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
                 
                 $offers = $this->HealthmallModel->get_product_offer($product_id);
                 
                //  print_r($results); die();
                
                $prod_data = $this->db->query($stmt)->row_array();
                /*foreach($prod_data as $prod){
                    $product = $prod;
                    $product['offers'] = $offers;
                }
               */
                $prod_data_all[] = array( "pd_id"=> $prod_data['pd_id'],
                                                 "v_id"=> $prod_data['v_id'],
                                                 "v_name"=> $prod_data['v_name'],
                                                 "v_delivery_charge"=> $prod_data['v_delivery_charge'],
                                                 "pd_name"=>$prod_data['pd_name'],
                                                 "pd_pc_id"=>$prod_data['pd_pc_id'],
                                                 "pd_psc_id" =>$prod_data['pd_psc_id'],
                                                 "pd_photo_1" => $prod_data['pd_photo_1'],
                                                 "pd_photo_2"=>$prod_data['pd_photo_2'],
                                                 "pd_photo_3"=>$prod_data['pd_photo_3'],
                                                 "pd_photo_4"=>$prod_data['pd_photo_4'],
                                                 "pd_mrp_price"=>$prod_data['pd_mrp_price'],
                                                 "pd_vendor_price"=>$prod_data['pd_vendor_price'],
                                                 "pd_quantity" => $prod_data['pd_quantity'],
                                                 "pd_long_desc" => $prod_data['pd_long_desc'],
                                                 "total_view"=>$prod_data['total_view'],
                                                 "offers"=>$offers
                                                 
                                                );  
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
	
public function add_to_cart($user_id, $product_id, $quantity, $offer_id,$referal_code,$variable_pd_id,$sku,$type){
	    
	    if($type=="1")
	    {
	        
	        $checkAvailable2 = $this->db->query("SELECT * FROM `user_cart` WHERE `customer_id` = '$user_id'");
            $count2 = $checkAvailable2->num_rows();
            
    	    $checkAvailable = $this->db->query("SELECT * FROM `user_cart` WHERE `product_id` = '$product_id' AND `variable_pd_id` = '$variable_pd_id' AND `sku` = '$sku' AND `customer_id` = '$user_id'");
            $count = $checkAvailable->num_rows();
    
            if($count > 0){
                $prevQuant = 0;
                $totalQuantity = $quantity;
                $results = $this->db->query("UPDATE `user_cart` SET `quantity` = '$totalQuantity', `offer_id` = '$offer_id',`referal_code` = $referal_code, `variable_pd_id`='$variable_pd_id',`sku` = '$sku' WHERE `product_id` = '$product_id' AND `variable_pd_id`='$variable_pd_id' AND `sku` = '$sku' AND `customer_id` = '$user_id'");
                $res = array(
                    "status" => 201,
                    "message" => "success",
                    "description" => "Stack updated Successfully",
                    "cart_count"=>"$count2"
                );
            } else {
                $results = $this->db->query("INSERT INTO `user_cart` (`product_id`, `customer_id`, `quantity`, `offer_id`,`referal_code`,`variable_pd_id`,`sku`) VALUES ('$product_id', '$user_id', '$quantity', '$offer_id','$referal_code','$variable_pd_id','$sku')");
        	    $insert_id = $this->db->insert_id(); 
        	    $fin=$count2+1;
        	    if($insert_id){
        	        $res = array(
                        "status" => 200,
                        "message" => "success",
                        "description" => "Successfully added to Stack",
                        "cart_count"=>"$fin"
                      
                    );
        	    } else {
        	        $res = array(
                        "status" => 400,
                        "message" => "fail",
                        "description" => "Something went wrong, please try again",
                        "cart_count"=>""
                    );
        	    }
        }
            
	    }
	    elseif($type=="0")
	    {
	      $checkAvailable2 = $this->db->query("SELECT * FROM `user_cart` WHERE `customer_id` = '$user_id'");
            $count2 = $checkAvailable2->num_rows(); 
            
	      $checkAvailable = $this->db->query("DELETE FROM `user_cart` WHERE `customer_id` = '$user_id' AND `variable_pd_id` = '$variable_pd_id' AND `product_id` = '$product_id' ");
          $affected_rows = $this->db->affected_rows();
          if($affected_rows > 0)
            {
                $fina=$count2-1;
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
           
            
         for($i=0;$i<sizeof($availableCartFull);$i++){
             $ven_data=array();
                 $oldCost = $finalCost = 0;
                 
                if(sizeof($availableCartFull[$i]) > 0)
                   {
                        $v_id = $availableCartFull[$i]['v_id'];
                      
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
                         
                         $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_min_order,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price
                         ,d.pd_vendor_price,vd.cap_available,vd.cap_charge,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
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
                                    else 
                                     {
                                        $variableProduct = array();
                                     }
                                    $productCost = 0;
                                    if(!empty($offers))
                                      {
                                        $productCost = intval($offers[0]['offer_best_price'] * $quantity);
                                      }  
                                    else if($var_ven_price > 0 && $var_ven_price != null)
                                      {
                                        $productCost = intval (round($var_ven_price)) * $quantity;
                                        $prod['pd_vendor_price'] = intval($var_ven_price);
                                      } 
                                    else 
                                      {
                                        $productCost = intval (round($prod['pd_vendor_price'])) * $quantity;
                                      }  
                                    
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
                                      $vendorprice=intval (round($prod['pd_vendor_price']));
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
                                                     'product_cost'=>intval (round($productCost)),
                                                     'referal_code_id'=>$referal_code_id,
                                                     'referal_code'=>$referal_code,
                                                     'offer_id'=>$offer_id,
                                                     'variable_pd_id'=>$variable_pd_id,
                                                     'variable_product'=>$variableProduct,
                                                     'offer_price'=>$offer_price,
                                                     'offers'=>$offers 
                                                    );
                                    $del['pd_id'] = $prod['pd_id'];
                                    $del['v_id'] = $prod['v_id'];
                                    $del['v_delivery_charge'] = intval (round($prod['v_delivery_charge'])) ;
                                    $del['v_min_order'] = intval (round($prod['v_min_order'])) ;
                                    $del['pd_vendor_price'] = intval (round($prod['pd_vendor_price'])) ;
                                    $del['quantity'] = $quantity;
                                    $delivery[] = $del;
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
                $product_id = $availableCartFull[$i]['product_id'][$j];
                $stmt = "SELECT d.pd_id,vd.v_id,vd.v_name,vd.v_delivery_charge,vd.v_min_order,d.pd_name,d.pd_pc_id,d.pd_psc_id,d.pd_photo_1,d.pd_photo_2,d.pd_photo_3,d.pd_photo_4,d.pd_mrp_price,d.pd_vendor_price,vd.cap_available,vd.cap_charge,d.pd_view_count as total_view FROM `product_details_hm` d LEFT join vendor_details_hm vd on vd.v_id=d.pd_added_v_id where d.pd_id = '$product_id'";
                //echo $stmt."<br>";
                $prod_dataAll = $this->db->query($stmt);
                // $prod_data = $prod_dataAll->result_array();
                $prod_num = $prod_dataAll->num_rows();
                // print_r($prod_data); die();
                if($prod_num > 0){
                    
                    $offers = $this->HealthmallModel->get_product_offer($product_id);
                
                    $rating = $this->HealthmallModel->get_num_ratings($product_id);
                    
                    if(empty($referal_code_id)){
                        $referal_code =array();
                    } else {
                        $referal_code = $this->HealthmallModel->get_referal_code($product_id,$referal_code_id);
                        // $referal_code = $referal_code_id;
                    }
                   
                   //echo sizeof($prod_data); 
                    foreach($prod_dataAll->result_array() as $prod){
                       // $product = $prod;
                       $prodName = $prod['pd_name'];
                       $variableProduct = array();
                        if($variable_pd_id > 0){
                            $variableProds = $this->db->query("SELECT * FROM `variable_products_hm` WHERE `id` = '$variable_pd_id'");
                            // print_r($variableProds->result_array()); die();
                            foreach($variableProds->result_array() as $variableProd){
                                /* print_r($variableProd); die();*/
                                $colorId = $variableProd['color'];
                                $sizeId = $variableProd['size'];
                                
                                $color = $this->HealthmallModel->get_color_by_id($colorId);
                                $size = $this->HealthmallModel->get_size_by_id($sizeId);
                                
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
                            $variableProduct = array();
                        }
                       
                        $product['pd_name'] = $prodName;
                      
                        $productCost = 0;
                        if(!empty($offers)){
                            $productCost = intval($offers[0]['offer_best_price'] * $quantity);
                        }  else if($var_ven_price > 0 && $var_ven_price != null){
                            $productCost = intval (round($var_ven_price)) * $quantity;
                            $prod['pd_vendor_price'] = intval($var_ven_price);
                        } else {
                            $productCost = intval (round($prod['pd_vendor_price'])) * $quantity;
                        }  
                        
                   
                        $prod['pd_vendor_price'] = intval (round($prod['pd_vendor_price']));
                        $finalCost = $finalCost + $productCost;
                         
                     
                            
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
                        
                     
                        $del['pd_id'] = $prod['pd_id'];
                        $del['v_id'] = $prod['v_id'];
                        $del['v_delivery_charge'] = intval (round($prod['v_delivery_charge'])) ;
                        $del['v_min_order'] = intval (round($prod['v_min_order'])) ;
                        $del['pd_vendor_price'] = intval (round($prod['pd_vendor_price'])) ;
                        $del['quantity'] = $quantity;
                       
                        $delivery[] = $del;
                       
                    }
                    
                    
                }
             
                    $prod_data[] = $product;
                    
                
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
                                                 "is_cart" => $cart
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
		
}
?>