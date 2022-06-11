<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Bulk_order_hm_Model extends CI_Model {
    
    public function get_brands($user_id, $vendor_type,$page_no , $per_page){
       
        $withrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' and `rank` != 0 AND pd.b2b = '1' AND vd.b2b = '1' ORDER BY `rank` ASC")->result_array();
        
        $withoutrank = $this->db->query("SELECT DISTINCT vd.v_id, vd.v_name, vd.v_company_name, vd.v_company_logo, vd.rank FROM vendor_details_hm as vd join product_details_hm as pd ON vd.v_id = pd.pd_added_v_id WHERE `v_status` = '1' and `rank` = 0 AND pd.b2b = '1' AND vd.b2b = '1'  ORDER BY `rank`")->result_array();
        $result['brands'] = array_merge($withrank, $withoutrank);
        // print_r($result); die();
        return $result;
    }
    
    public function get_all_products($user_id, $brand_id, $page_no, $per_page){
        echo $brand_id;
        die();
        
    }
        
        
}
?>
