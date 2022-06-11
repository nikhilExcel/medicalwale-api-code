<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class AdsModel extends CI_Model {

    public function ads_list($user_id) {
        $query = $this->db->query("SELECT id,ad_title,ad_descr,ad_link,ad_img,ad_date_add FROM ads ORDER BY rand()");			
        $count = $query->num_rows(); 
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $ad_img = $row['ad_img'];
				$ad_date_add = $row['ad_date_add'];	
                $image= 'http://ad.sandboxstg.medicalwale.com/images/'.$ad_img;
                $resultpost[] = array(
                    'id' => $row['id'],
                    'title' => $row['ad_title'],
                    'description' => $row['ad_descr'],
                    'link' => $row['ad_link'],
                    'image' => $image
                );
            }
			$resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => $count,
                "data" => $resultpost
            );
        } else {
            $resultpost = array(
                "status" => 200,
                "message" => "success",
                "count" => 0,
                "data" => array()
            );
        }
        return $resultpost;
    }

    public function add_click($user_id, $ads_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
		$total_query = $this->db->query("SELECT ad_clicks FROM `ads` where id='$ads_id' limit 1");			
        $get_list   = $total_query->row_array();
        $clicks = $get_list['ad_clicks'];
        $ad_clicks = $clicks+1;
		$querys = $this->db->query("UPDATE `ads` SET `ad_clicks`='$ad_clicks' WHERE id='$ads_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }
	
	public function add_views($user_id, $ads_id) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');
		$total_query = $this->db->query("SELECT ad_views FROM `ads` where id='$ads_id' limit 1");			
        $get_list   = $total_query->row_array();
        $views = $get_list['ad_views'];
        $ads_views = $views+1;
		$querys = $this->db->query("UPDATE `ads` SET `ad_views`='$ads_views' WHERE id='$ads_id'");
        return array(
            'status' => 200,
            'message' => 'success'
        );
    }


}
