<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bulk_order_hm_Model extends CI_Model {
    
    public function get_brands($user_id, $vendor_type,$page_no , $per_page,$search){
        $whereSearch = "";
        if($search != ""){
            $whereSearch = "AND (v_name like '%$search%' OR v_address1 like '%$search%' OR v_address2  like '%$search%' OR v_map_location  like '%$search%' OR v_company_name like '%$search%')";
        }
        $withrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' and `rank` != 0 AND pd.b2b = '1' AND vd.b2b = '1' $whereSearch ORDER BY `rank` ASC")->result_array();
        
        $withoutrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' and `rank` = 0 AND pd.b2b = '1' AND vd.b2b = '1' $whereSearch  ORDER BY `rank`")->result_array();
        $result['brands'] = array_merge($withrank, $withoutrank);
        // print_r($result); die();
        return $result;
    }
    
    public function get_all_products($user_id, $brand_id, $page_no, $per_page){
        $products_all_count = $products_all = $result = array();
        $limit = $whereBrand = "";
        $status = 0;
        if($brand_id != ""){
            $whereBrand = "AND pd_added_v_id = $brand_id";
        }
        
        if($per_page != 0 && $page_no != 0){
            $offset = $per_page*($page_no - 1);
            $limit = "LIMIT $per_page OFFSET $offset"; 
        }
        
        
        if($page_no == "" || $per_page == ""){
            $products_all_count = $products_all = $this->db->query("SELECT `pd_id`,`pd_added_v_id`,`brand_name`,`pd_name`,`pd_photo_1`,`pd_mrp_price`,`pd_vendor_price` FROM `product_details_hm` WHERE `b2b` = 1 $whereBrand")->result_array();
            $status = 1; // found
        } else {
            $products_all = $this->db->query("SELECT `pd_id`,`pd_added_v_id`,`brand_name`,`pd_name`,`pd_photo_1`,`pd_mrp_price`,`pd_vendor_price` FROM `product_details_hm` WHERE `b2b` = 1 $whereBrand $limit")->result_array();
            $products_all_count = $this->db->query("SELECT `pd_id`  FROM `product_details_hm`  WHERE `b2b` = 1 $whereBrand")->result_array();
            $status = 1; // found
        }
        $products_count = sizeof($products_all_count);
        if($per_page > 0){
            $last_page = ceil($products_count/$per_page);
        } else if($products_count > 0) {
            $page_no = $first_page = $last_page = 1;
            $per_page = $products_count;
        } else {
           $page_no = $first_page = $last_page = 0;
        }
        
        if($products_count > 0) {
            $first_page = 1;
        } else {
            $first_page = 0;
        }
        
        $result['data_count'] = intval($products_count);
        $result['per_page'] = intval($per_page);
        $result['current_page'] = intval($page_no);
        $result['first_page'] = intval($first_page);
        $result['last_page'] = intval($last_page);
            
            
        $result['products'] = $products_all;
        return $result;
        
    }
    
    
        
        
}
?>
