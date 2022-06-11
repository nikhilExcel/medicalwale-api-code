<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model
{
    public function send_notification_to_delivery($title, $token, $msg, $agent) {
        date_default_timezone_set('Asia/Kolkata');
        $date = date('j M Y h:i A');
        $img_url='https://bulkorder.webwork.co.in/assets/logo.svg';
        $tag='text';
        $agent='android';
        $notification_url='';
        $fields  = array(
            'to' => $token,
            'priority' => "high",
            'notification' => array(
                "title" => $title,
                "body" => $msg,
                "url"=>$notification_url,
                'vibrate'	=> 1
            )
        );
        
        $authorization_key='AAAAgt4vy_Q:APA91bG8DNhtRNbScpjnNSpQ5aLbg0sZytC2FguNLtlQiWys7XN1mC6FeKeMi3HMGogEeMcb17NvFQaZP2wtrY20awSn5nRua6Xq7j7MISM9tYSDiBc9NcHBP1NiK--FrAtvv1PppW-T';
        $headers = array(
            'Content-Type: application/json',
            'Authorization: key='.$authorization_key
        );
        $ch      = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            //die('Problem occurred: ' . curl_error($ch));
        }
        //echo $result;
        curl_close($ch);
    }
    
    public function state_list($user_id)
    {
        $query = $this->db->query("SELECT * FROM state_list WHERE country_id='101' order by name");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function city_list($user_id, $state_id)
    {
        $query = $this->db->query("SELECT * FROM city_list WHERE state_id='$state_id' order by name");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function menu_list() {
        $query = $this->db->query("SELECT id,menu_name FROM json_menu order by sort asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['menu_name'];
            
            $menu_sub=array();
            $sub_query = $this->db->query("SELECT id,menu_name FROM json_menu_sub where menu_id='$id' order by sort asc");
            $sub_count = $sub_query->num_rows();
            if($sub_count>0){
                foreach ($sub_query->result_array() as $sub_row) {
                    $sub_id   = $sub_row['id'];
                    $sub_name = $sub_row['menu_name'];
                    
                    
                    $menu_sub_child=array();
                    $sub_query_child = $this->db->query("SELECT id,menu_name,menu_slug FROM json_menu_sub_child where menu_id='$id' and sub_menu_id='$sub_id' order by sort asc");
                    $sub_count_child = $sub_query_child->num_rows();
                    if($sub_count_child>0){
                        foreach ($sub_query_child->result_array() as $sub_row_child) {
                            $sub_child_id   = $sub_row_child['id'];
                            $sub_child_name = $sub_row_child['menu_name'];
                            $sub_child_slug = $sub_row_child['menu_slug'];
         
                            $menu_sub_child[] = array(
                                "id" => $sub_child_id,
                                "menu_name" => $sub_child_name,
                                "menu_slug" => $sub_child_slug
                            );
                        }
                    }
 
                    $menu_sub[] = array(
                        "id" => $sub_id,
                        "menu_name" => $sub_name,
                        "menu_sub_child" => $menu_sub_child
                    );
                }
            }
            
            
            $data[] = array(
                "id" => $id,
                "menu_name" => $name,
                "menu_sub" => $menu_sub
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function shorter($text, $chars_limit)
    {
        // Check if length is larger than the character limit
        if (strlen($text) > $chars_limit)
        {
            // If so, cut the string at the character limit
            $new_text = substr($text, 0, $chars_limit);
            // Trim off white space
            $new_text = trim($new_text);
            // Add at end of text ...
            return $new_text . "...";
        }
        // If not just return the text as is
        else
        {
        return $text;
        }
    }

    public function get_bank_name($id)
    {
        $id = clean_number($id);
        $this->db->where('banks.id', $id);
        $query = $this->db->get('banks');
        $sql= $query->row();
        return $sql->name;
    }



    public function publisher_list($user_id="")   {
        $query = $this->db->query("SELECT * FROM publisher order by name asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    

  public function board_list($user_id="")   {
        $query = $this->db->query("SELECT * FROM board order by id asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    


  public function banks_list($user_id="")
    {
        $query = $this->db->query("SELECT * FROM banks order by name asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    


  public function grades_list($user_id="")
    {
        $query = $this->db->query("SELECT * FROM grade_list order by sort asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
               "id" => $id,
               "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function categories($product_type)
    {
        if($product_type=='2'){
            $query = $this->db->query("SELECT * FROM machine_type order by name asc");
        }
        else{
            $query = $this->db->query("SELECT * FROM categories order by name asc");
        }
        
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
               "id" => $id,
               "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
   
    public function brand_list($category_id="",$product_type)
    {
        if($product_type=='1'){
            $query = $this->db->query("SELECT * FROM hire_brand order by name asc");
        }
        else if($product_type=='2'){
            $query = $this->db->query("SELECT * FROM used_brand order by name asc");
        }
        else{
            $query = $this->db->query("SELECT * FROM spare_brand order by name asc");
        }
        
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
               "id" => $id,
               "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function machine_type()
    {
        $query = $this->db->query("SELECT id,name FROM machine_type order by name asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
               "id" => $id,
               "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function school_city($user_id="")
    {
        $kirti_id = kirti_id();
        $query = $this->db->query("SELECT s.city_id,vc.vendor_id
            FROM vendor_commission as vc
            INNER JOIN school as s
            ON vc.vendor_id = s.vendor_id 
            where s.vendor_id != '$kirti_id' AND vc.category_id='7'
            group by s.city_id ");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $city_id   = $row['city_id'];
            $vendor_id   = $row['vendor_id'];
            
            $query_city = $this->db->query("SELECT name FROM city_list where id = '$city_id'");
            $row_city = $query_city->row_array();
            $city_name = $row_city['name'];
            
            $school_array = array();
            $query_school = $this->db->query("SELECT id,name,slug,board FROM school where city_id = '$city_id' and vendor_id='$vendor_id' and FIND_IN_SET('7', category) and status='1'");
            foreach ($query_school->result_array() as $row_school) {
                
                $school_slug = $row_school['slug'];
                $id = $row_school['id'];
                $board = explode(',',$row_school['board']);
                foreach($board as $board_id){
                    
                    $query_board = $this->db->query("SELECT name,slug FROM board where id = '$board_id'");
                    $row_board = $query_board->row_array();
                    $board_name = $row_board['name'];
                    $board_slug = $row_board['slug'];
                    
                    $school_name = ucwords(strtolower($row_school['name'])).' - '.$board_name;
                    
                     $school_array[] = array(
                                    "id" => $id,
                                    "school_name" => $school_name,
                                    "school_slug" => $school_slug,
                                    "board_id" => $board_id,
                                    "board_slug" => $board_slug,
                                    "board_name" => $board_name,
                                );
                }
               
            }
            
            
            
            $data[] = array(
               "id" => $city_id,
               "city_name" => $city_name,
               "school_array" => $school_array,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function book_school_city($user_id="")
    {
        $kirti_id = kirti_id();
        $query = $this->db->query("SELECT s.city_id,s.vendor_id FROM `school` as s 
        INNER JOIN users as u ON u.id = s.vendor_id
        WHERE FIND_IN_SET('7', s.category) AND u.email_status = 1 AND s.vendor_id != '$kirti_id'");
        // echo $this->db->last_query();
        // exit();
        $count = $query->num_rows();
        $data  = array();
        $master_array=array();
        foreach ($query->result_array() as $row) {
            
            
            $city_id   = $row['city_id'];
            $vendor_id   = $row['vendor_id'];
            
            if (!in_array($city_id, $master_array)) {
                
                 $master_array[] = $city_id;
            
            $query_city = $this->db->query("SELECT name FROM city_list where id = '$city_id'");
            $row_city = $query_city->row_array();
            $city_name = $row_city['name'];
            
            $school_array = array();
            $query_school = $this->db->query("SELECT s.id,s.name,s.slug,s.board FROM school as s 
            INNER JOIN users as u ON u.id = s.vendor_id
            where s.city_id = '$city_id' and FIND_IN_SET('7', s.category) and s.status='1' and u.email_status = 1 AND s.vendor_id != '$kirti_id' Limit 10");
            foreach ($query_school->result_array() as $row_school) {
                
                $school_slug = $row_school['slug'];
                $id = $row_school['id'];
                $board = explode(',',$row_school['board']);
                foreach($board as $board_id){
                    
                    $query_board = $this->db->query("SELECT name,slug FROM board where id = '$board_id'");
                    $row_board = $query_board->row_array();
                    $board_name = $row_board['name'];
                    $board_slug = $row_board['slug'];
                    
                    $school_name = ucwords(strtolower($row_school['name'])).' - '.$board_name;
                    
                     $school_array[] = array(
                                    "id" => $id,
                                    "school_name" => $school_name,
                                    "school_slug" => $school_slug,
                                    "board_id" => $board_id,
                                    "board_slug" => $board_slug,
                                    "board_name" => $board_name,
                                );
                }
               
            }
            
            
            
            $data[] = array(
               "id" => $city_id,
               "city_name" => $city_name,
               "school_array" => $school_array,
            );
            
            
            }
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function school_uniform($user_id="")
    {
        $kirti_id = kirti_id();
        $query = $this->db->query("SELECT s.city_id,s.vendor_id FROM `school` as s 
        INNER JOIN users as u ON u.id = s.vendor_id
        WHERE FIND_IN_SET('22', s.category) AND u.email_status =1 AND s.vendor_id != '$kirti_id'");
        
            
        $count = $query->num_rows();
        $data  = array();
        $master_array=array();
        foreach ($query->result_array() as $row) {
            
            
            $city_id   = $row['city_id'];
            $vendor_id   = $row['vendor_id'];
            
            if (!in_array($city_id, $master_array)) {
                
                 $master_array[] = $city_id;
            
            $query_city = $this->db->query("SELECT name FROM city_list where id = '$city_id'");
            $row_city = $query_city->row_array();
            $city_name = $row_city['name'];
            
            $school_array = array();
            $query_school = $this->db->query("SELECT s.id,s.name,s.slug,s.board FROM school as s 
            INNER JOIN users as u ON u.id = s.vendor_id
            where s.city_id = '$city_id' and FIND_IN_SET('22', s.category) and s.status='1' and u.email_status = 1 AND s.vendor_id != '$kirti_id' Limit 10");
            foreach ($query_school->result_array() as $row_school) {
                
                $school_slug = $row_school['slug'];
                $id = $row_school['id'];
                
                    
                    // $query_board = $this->db->query("SELECT name,slug FROM board where id = '$board_id'");
                    // $row_board = $query_board->row_array();
                    // $board_name = $row_board['name'];
                    // $board_slug = $row_board['slug'];
                    
                    $school_name = ucwords(strtolower($row_school['name']));
                    
                     $school_array[] = array(
                                    "id" => $id,
                                    "school_name" => $school_name,
                                    "school_slug" => $school_slug,
                                    // // "board_id" => $board_id,
                                    // "board_slug" => $board_slug,
                                    // "board_name" => $board_name,
                                );
               
               
            }
            
            
            
            $data[] = array(
               "id" => $city_id,
               "city_name" => $city_name,
               "school_array" => $school_array,
            );
            
            
            }
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
   
    public function stationary_list($user_id="",$limit)
    {
        $kirti_id = kirti_id();
        $limit_seacrh = '';
        if($limit != ''){
            $limit_seacrh = 'Limit '.$limit;
        }
        // $query = $this->db->query("SELECT * FROM categories where `parent_id` = 6  order by name asc $limit_seacrh");
        $query = $this->db->query("SELECT c.id,c.name,c.slug FROM products as p INNER JOIN product_master as pm ON p.master_id = pm.id INNER JOIN products_category as pc ON pm.id = pc.product_id INNER JOIN categories as sub_cat ON sub_cat.id = pc.category_id INNER JOIN categories as c ON sub_cat.parent_id = c.id WHERE p.user_id != '$kirti_id' AND p.parent_cid = '6' and p.id NOT IN (SELECT product_id FROM package_products) group by c.id order by c.id asc $limit_seacrh");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            $type = array();
            $query_type = $this->db->query("SELECT * FROM categories where `parent_id` = '$id'  order by name asc");
            foreach ($query_type->result_array() as $row_type) {
                $type_id   = $row_type['id'];
                $type_name   = ucwords(strtolower($row_type['name']));
                $type[] = array(
                   "type_id" => $type_id,
                   "type_name" => ucwords(strtolower($type_name))
                );
            }
            if(count($type) > 0){
                $data[] = array(
                   "id" => $id,
                   "slug" => $slug,
                   "name" => ucwords(strtolower($name)),
                   "type" => $type,
                );
            }
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    
   
   
    public function subject_list($user_id="")
    {
        $query = $this->db->query("SELECT *  FROM subject order by name asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = ucwords(strtolower($row['name']));
            
            $data[] = array(
               "id" => $id,
               "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
     
     
     
    
    public function category_by_parent($parent_id="")
    {
        $query = $this->db->query("SELECT * FROM categories WHERE parent_id='$parent_id' order by name asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = ucwords(strtolower($row['name']));
            
            $data[] = array(
               "id" => $id,
               "name" => $name
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
     
     
    
     
    
    public function vendor_shipping_list($user_id="")
    {
        $query = $this->db->query("SELECT * FROM vendor_shipping_details WHERE vendor_id='$user_id' AND is_deleted='0' order by id asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $data[] = array(
               "id" => $row['id'],
               "address" =>  $row['address'].', '.get_city_name($row['city_id']).', '.get_state_name($row['state_id']),
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function school_text_book_category($user_id="",$limit)
    {
        $kirti = kirti_id();
        $limit_seacrh = '';
        if($limit != ''){
            $limit_seacrh = 'Limit '.$limit;
        }
        // $query = $this->db->query("SELECT id,name,slug FROM categories WHERE parent_id='8' order by id asc $limit_seacrh");
        $query = $this->db->query("SELECT c.id,c.name,c.slug FROM products as p INNER JOIN product_master as pm ON p.master_id = pm.id INNER JOIN products_category as pc ON pm.id = pc.product_id INNER JOIN categories as c ON c.id = pc.category_id WHERE p.parent_cid = '8' and p.status='1' and p.user_id!='$kirti' group by c.id order by c.id asc $limit_seacrh");
        // echo $this->db->last_query();
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $category_id = $row['id'];
            $category_name = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            
            // $category = array();
            // $query_sub = $this->db->query("SELECT id,name FROM categories WHERE parent_id='$category_id' order by id asc");
            // foreach ($query_sub->result_array() as $row_sub) {
            //     $sub_category_id = $row_sub['id'];
            //     $sub_category_name = $row_sub['name'];
            //     $category[] = array(
            //       "id" => $sub_category_id,
            //       "name" =>  $sub_category_name,
            //     );
            // }
            
            $data[] = array(
               "id" => $category_id,
               "name" =>  $category_name,
               "slug" =>  $slug,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function note_book_category($user_id="",$limit)
    {   
        $kirti_id = kirti_id();
        $limit_seacrh = '';
        if($limit != ''){
            $limit_seacrh = 'Limit '.$limit;
        }
        // $query = $this->db->query("SELECT id,name,slug FROM categories WHERE parent_id='10' order by id asc $limit_seacrh");
        $query = $this->db->query("SELECT c.id,c.name,c.slug FROM products as p INNER JOIN product_master as pm ON p.master_id = pm.id INNER JOIN products_category as pc ON pm.id = pc.product_id INNER JOIN categories as c ON c.id = pc.category_id WHERE p.user_id != '$kirti_id' AND p.parent_cid = '10' and p.status='1' group by c.id order by c.id asc $limit_seacrh");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $category_id = $row['id'];
            $category_name = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            
            $data[] = array(
               "id" => $category_id,
               "name" =>  $category_name,
                "slug" =>  $slug,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function other_book_category($user_id="",$limit)
    {
        $kirti_id = kirti_id();
        $limit_seacrh = '';
        if($limit != ''){
            $limit_seacrh = 'Limit '.$limit;
        }
        
        // $query = $this->db->query("SELECT id,name,slug FROM categories WHERE parent_id='45' order by id asc $limit_seacrh");
        $query = $this->db->query("SELECT c.id,c.name,c.slug FROM products as p INNER JOIN product_master as pm ON p.master_id = pm.id INNER JOIN products_category as pc ON pm.id = pc.product_id INNER JOIN categories as c ON c.id = pc.category_id WHERE p.user_id != '$kirti_id' AND p.parent_cid = '45' and p.status='1' group by c.id order by c.id asc $limit_seacrh");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $category_id = $row['id'];
            $category_name = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            
            $data[] = array(
               "id" => $category_id,
               "name" =>  $category_name,
              "slug" =>  $slug,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function uniform_category($user_id="",$cat_id,$limit)
    {
        $limit_seacrh = '';
        if($limit != ''){
            $limit_seacrh = 'Limit '.$limit;
        }
        $kirti_id = kirti_id();
        // $query = $this->db->query("SELECT id,name,slug FROM categories WHERE parent_id='$cat_id' order by id asc $limit_seacrh");
        $query = $this->db->query("SELECT c.id,c.name,c.slug FROM products as p INNER JOIN categories as c ON p.category_id = c.id WHERE p.user_id != '$kirti_id' AND p.uniform_cat = '$cat_id' and p.status='1' group by c.id order by id asc $limit_seacrh");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $category_id = $row['id'];
            $category_name = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            
            $data[] = array(
               "id" => $category_id,
               "name" =>  $category_name,
              "slug" =>  $slug,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function educational_product_category($user_id="",$cat_id,$limit)
    {
        $limit_seacrh = '';
        if($limit != ''){
            $limit_seacrh = 'Limit '.$limit;
        }
        $kirti_id = kirti_id();
        // $query = $this->db->query("SELECT id,name,slug FROM categories WHERE parent_id='$cat_id' order by id asc $limit_seacrh");
        $query = $this->db->query("SELECT c.id,c.name,c.slug FROM products as p INNER JOIN categories as c ON p.category_id = c.id WHERE p.user_id != '$kirti_id' AND p.parent_cid = '$cat_id' and p.status='1' group by c.id order by id asc $limit_seacrh");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $category_id = $row['id'];
            $category_name = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            
            $data[] = array(
               "id" => $category_id,
               "name" =>  $category_name,
              "slug" =>  $slug,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function shoes_category($user_id="",$limit)
    {
        $kirti_id = kirti_id();
        $limit_seacrh = '';
        if($limit != ''){
            $limit_seacrh = 'Limit '.$limit;
        }
        
        // $query = $this->db->query("SELECT id,name,slug FROM categories WHERE parent_id='38' order by id asc $limit_seacrh");
        $query = $this->db->query("SELECT c.id,c.name,c.slug FROM products as p INNER JOIN product_master as pm ON p.master_id = pm.id INNER JOIN products_category as pc ON pm.id = pc.product_id INNER JOIN categories as c ON c.id = pc.category_id WHERE p.user_id != '$kirti_id' AND p.parent_cid = '38' and p.status='1' group by c.id order by c.id asc $limit_seacrh");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $category_id = $row['id'];
            $category_name = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            
            $data[] = array(
               "id" => $category_id,
               "name" =>  $category_name,
              "slug" =>  $slug,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    
    
    public function header_search($user_id,$keyword)
    {
        $kirti_id = kirti_id();
        $query_book_school = $this->db->query("SELECT s.id,s.name,s.slug,s.board FROM school as s INNER JOIN users as u ON s.vendor_id = u.id WHERE s.vendor_id != '$kirti_id' AND s.name like '%$keyword%' and  FIND_IN_SET('7', s.category) and s.status = '1' and u.approve = '1' and u.email_status = '1' order by s.name asc Limit 0,5");
        $count_school_list = $query_book_school->num_rows();
        $book_school_list  = array();
        foreach ($query_book_school->result_array() as $row) {
            $category_id = $row['id'];
            $title = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            $board = explode(',',$row['board']);
            foreach($board as $board_id){
                $query_board = $this->db->query("SELECT name,slug FROM board where id = '$board_id' limit 1");
                $row_board = $query_board->row_array();
                $board_name = $row_board['name'];
                $board_slug = $row_board['slug'];
                
                $title = str_replace(' ', '-', $title);
                $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
                $title = str_replace('-', ' ', $title);
                
                $book_school_list[] = array(
                   "id" => $category_id,
                   "name" =>  $title .' - '.$board_name,
                   "slug" =>  'school/bookset/'.$board_slug.'/'.$category_id,
                );
            }
            
        }
        
        $query_uniform_school = $this->db->query("SELECT s.id,s.name,s.slug,s.board FROM school as s INNER JOIN users as u ON s.vendor_id = u.id WHERE s.vendor_id != '$kirti_id' AND s.name like '%$keyword%' and  FIND_IN_SET('22', s.category) and s.status = '1' and u.approve = '1' and u.email_status = '1' order by s.name asc Limit 0,5");
        $count_uniform_school = $query_uniform_school->num_rows();
        $uniform_school_list  = array();
        foreach ($query_uniform_school->result_array() as $row) {
            $category_id = $row['id'];
            $title = ucwords(strtolower($row['name']));
            $slug = $row['slug'];
            
            $title = str_replace(' ', '-', $title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
            $title = str_replace('-', ' ', $title);
            
            $uniform_school_list[] = array(
               "id" => $category_id,
               "name" =>  $title,
               "slug" =>  'uniform/'.$slug.'/'.$category_id,
            );
        }
        
        $query_books = $this->db->query("SELECT sub_cat.name as cat_name,p.id,pm.title,pm.slug,pm.subcate,pm.id as master_id FROM products as p 
        INNER JOIN product_master as pm ON p.master_id = pm.id 
        INNER JOIN users as u ON p.user_id = u.id 
        INNER JOIN products_category as pc ON pc.product_id = p.master_id 
        INNER JOIN categories as cat ON cat.id = pc.category_id 
        INNER JOIN categories as sub_cat ON sub_cat.id = cat.parent_id 
        WHERE p.user_id != '$kirti_id' AND (pm.title like '%$keyword%' OR sub_cat.name like '%$keyword%') and p.status='1' and (u.approve = '1' and u.email_status = '1') and p.is_individually='1' and p.parent_cid IN ('8', '10', '45') and p.id NOT IN (SELECT product_id FROM package_products) order by pm.title asc Limit 0,5");
        
        // echo $this->db->last_query();
        // exit();
        
        $count_book = $query_books->num_rows();
        $book_list  = array();
        foreach ($query_books->result_array() as $row) {
            $category_id = $row['id'];
            $title = ucwords(strtolower($row['title']));
            $slug = $row['slug'];
            $subcate = $row['subcate'];
            $master_id = $row['master_id'];
            $cat_name = $row['cat_name'];
            
            $title = str_replace(' ', '-', $title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', ' ', $title);
            $title = str_replace('-', ' ', $title);
            
            $query_categ_slug = $this->db->query("SELECT c.slug FROM `categories` as c INNER JOIN products_category as pc ON pc.category_id = c.id WHERE pc.product_id = '$master_id' limit 1");
            
            $row_categ_slug = $query_categ_slug->row_array();
            $cat_slug  = $row_categ_slug['slug'];
            
            $book_list[] = array(
               "id" => $category_id,
               "name" =>  $title.' - '.$cat_name,
               "slug" =>  'product/'.$cat_slug.'/'.$slug.'/'.$master_id.'/'.$category_id,
            );
        }
        
        $query_educational_product = $this->db->query("SELECT p.id,p.title,p.slug,p.category_id,p.id as master_id FROM products as p INNER JOIN users as u ON p.user_id = u.id WHERE p.user_id != '$kirti_id' AND p.title like '%$keyword%' and p.status='1' and u.approve = '1' and u.email_status = '1' and p.is_individually='1' and p.parent_cid = '262' and p.id NOT IN (SELECT product_id FROM package_products) order by p.title asc Limit 0,5");
        $count_educational_product = $query_educational_product->num_rows();
        $educational_product_list  = array();
        foreach ($query_educational_product->result_array() as $row) {
            $category_id = $row['id'];
            $title = ucwords(strtolower($row['title']));
            $slug = $row['slug'];
            $subcate = $row['category_id'];
            $master_id = $row['master_id'];
            
            $title = str_replace(' ', '-', $title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
            $title = str_replace('-', ' ', $title);
            
            $query_categ_slug = $this->db->query("SELECT c.slug FROM `categories` as c  WHERE c.id = '$subcate' limit 1");
            $row_categ_slug = $query_categ_slug->row_array();
            $cat_slug  = $row_categ_slug['slug'];
            
            $educational_product_list[] = array(
               "id" => $category_id,
               "name" =>  $title,
               "slug" =>  'product/'.$cat_slug.'/'.$slug.'/'.$master_id,
            );
        }
        
        
        $query_stationery = $this->db->query("SELECT cat.name as cat_name,p.id,pm.title,pm.slug,pm.subcate,pm.id as master_id FROM products as p 
        INNER JOIN product_master as pm ON p.master_id = pm.id 
        INNER JOIN users as u ON p.user_id = u.id 
        INNER JOIN products_category as pc ON pc.product_id = p.master_id 
        INNER JOIN categories as cat ON cat.id = pc.category_id 
        INNER JOIN categories as sub_cat ON sub_cat.id = cat.parent_id 
        WHERE p.user_id != '$kirti_id' AND (pm.title like '%$keyword%' OR sub_cat.name like '%$keyword%') and p.status='1' and u.approve = '1' and u.email_status = '1' and p.is_individually='1' and p.parent_cid IN ('6') and p.id NOT IN (SELECT product_id FROM package_products) GROUP BY pm.id order by pm.title asc Limit 0,5");
        $count_stationery = $query_stationery->num_rows();
        $stationery_list  = array();
        foreach ($query_stationery->result_array() as $row) {
            $category_id = $row['id'];
            $title = ucwords(strtolower($row['title']));
            $slug = $row['slug'];
            $subcate = $row['subcate'];
            $master_id = $row['master_id'];
            $cat_name = $row['cat_name'];
            
            $title = str_replace(' ', '-', $title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', ' ', $title);
            $title = str_replace('-', ' ', $title);
            
            $query_categ_slug = $this->db->query("SELECT c.slug,c.parent_id FROM `categories` as c INNER JOIN products_category as pc ON pc.category_id = c.id WHERE pc.product_id = '$master_id' limit 1");
            $row_categ_slug = $query_categ_slug->row_array();
            $cat_parent_id  = $row_categ_slug['parent_id'];
            
            
            $query_subcateg_slug = $this->db->query("SELECT slug FROM `categories` WHERE id = '$cat_parent_id' limit 1");
            $row_subcateg_slug = $query_subcateg_slug->row_array();
            $cat_slug  = $row_subcateg_slug['slug'];
            
            $stationery_list[] = array(
               "id" => $category_id,
               "name" =>  $title.' - '.$cat_name,
               "slug" =>  'product/'.$cat_slug.'/'.$slug.'/'.$master_id,
            );
        }
        
        $query_dress = $this->db->query("SELECT p.id,p.slug,p.title,p.category_id FROM products as p INNER JOIN users as u ON p.user_id = u.id WHERE p.user_id != '$kirti_id' AND p.title like '%$keyword%' and  p.uniform_cat = '381' and p.status = '1' and p.is_individually='1' and u.approve = '1' and u.email_status = '1' and p.id NOT IN (SELECT product_id FROM package_products) order by p.title asc Limit 0,5");
        $count_dress = $query_dress->num_rows();
        $dress_list  = array();
        foreach ($query_dress->result_array() as $row) {
            $prod_id = $row['id'];
            $title = ucwords(strtolower($row['title']));
            $slug = $row['slug'];
            $category_id = $row['category_id'];
            
            $title = str_replace(' ', '-', $title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
            $title = str_replace('-', ' ', $title);
            
                $query_board = $this->db->query("SELECT name,slug FROM categories where id = '$category_id' limit 1");
                $row_board = $query_board->row_array();
                $board_name = $row_board['name'];
                $board_slug = $row_board['slug'];
                
                $dress_list[] = array(
                   "id" => $prod_id,
                   "name" =>  $title,
                   "slug" =>  'product/'.$board_slug.'/'.$slug.'/'.$prod_id,
                );
           
            
        }
        
        $query_vendor = $this->db->query("SELECT id,firm_name FROM users WHERE id != '$kirti_id' AND (firm_name like '%$keyword%') and  role = 'vendor' and approve = '1' and email_status = '1' order by firm_name asc Limit 0,5");
        $count_vendor = $query_vendor->num_rows();
        $vendor_list  = array();
        foreach ($query_vendor->result_array() as $row) {
            $prod_id = $row['id'];
            $title = ucwords(strtolower($row['firm_name']));
            
            
            $title = str_replace(' ', '-', $title);
            $vendor_slug = strtolower($title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
            $title = str_replace('-', ' ', $title);
            
               
                
                $vendor_list[] = array(
                   "id" => $prod_id,
                   "name" =>  $title,
                   "slug" =>  'seller/'.$vendor_slug.'/'.$prod_id,
                );
           
            
        }
        
        $query_shoes = $this->db->query("SELECT p.id,pm.title,pm.slug,pm.subcate,pm.id as master_id FROM products as p INNER JOIN product_master as pm ON p.master_id = pm.id INNER JOIN users as u ON p.user_id = u.id WHERE p.user_id != '$kirti_id' AND pm.title like '%$keyword%' and p.status='1' and u.approve = '1' and u.email_status = '1' and p.is_individually='1' and p.parent_cid IN ('38') and p.id NOT IN (SELECT product_id FROM package_products) GROUP BY pm.id order by pm.title asc Limit 0,5");
        $count_shoes = $query_shoes->num_rows();
        $shoes_list  = array();
        foreach ($query_shoes->result_array() as $row) {
            $category_id = $row['id'];
            $title = ucwords(strtolower($row['title']));
            $slug = $row['slug'];
            $subcate = $row['subcate'];
            $master_id = $row['master_id'];
            
            $title = str_replace(' ', '-', $title);
            $title = preg_replace('/[^A-Za-z0-9\-]/', '', $title);
            $title = str_replace('-', ' ', $title);
            
            $query_categ_slug = $this->db->query("SELECT c.slug,c.parent_id FROM `categories` as c INNER JOIN products_category as pc ON pc.category_id = c.id WHERE pc.product_id = '$master_id' limit 1");
            $row_categ_slug = $query_categ_slug->row_array();
            $cat_parent_id  = $row_categ_slug['parent_id'];
            $cat_parent_slug  = $row_categ_slug['slug'];
            
            
            $query_subcateg_slug = $this->db->query("SELECT slug FROM `categories` WHERE id = '$cat_parent_id' limit 1");
            $row_subcateg_slug = $query_subcateg_slug->row_array();
            $cat_slug  = $row_subcateg_slug['slug'];
            
            $shoes_list[] = array(
               "id" => $category_id,
               "name" =>  $title,
               "slug" =>  'product/'.$cat_parent_slug.'/'.$slug.'/'.$master_id,
            );
        }
        
        if($count_shoes == 0 && $count_dress == 0 && $count_stationery == 0 && $count_book == 0 && $count_uniform_school == 0 && $count_school_list == 0 && $count_vendor == 0 && $count_educational_product == 0 ){
            $is_visible = 0;
        }else{
            $is_visible = 1;
        }
        
        $data[] = array(
               "educational_product_list" => $educational_product_list,
               "book_list" => $book_list,
               "stationery_list" => $stationery_list,
               "dress_list" => $dress_list,
               "book_school_list" => $book_school_list,
               "uniform_school_list" => $uniform_school_list,
               "vendor_list" => $vendor_list,
               "shoes_list" => $shoes_list,
               "is_visible" => $is_visible,
            );
            
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function dashboard_book_by_school($user_id="")
    {
        $kirti_id = kirti_id();
        $data = array();
        $query_school = $this->db->query("SELECT s.id,s.name,s.slug,s.board,s.avatar,s.city_id FROM school as s 
        INNER JOIN users as u ON u.id = s.vendor_id
        where s.vendor_id != '$kirti_id' AND FIND_IN_SET('7', s.category) and s.status='1' and u.email_status =1");
        
        foreach ($query_school->result_array() as $row_school) {
                
                $school_slug = $row_school['slug'];
                $id = $row_school['id'];
                $image     = $row_school['avatar']; 
                $city_id     = $row_school['city_id']; 
                $board = explode(',',$row_school['board']);
                
                if ($image!='') {
                        $image_url = 'https://api.skoozo.com/vendor/v1/' . $image;
                    } else {
                        $image_url = 'https://api.skoozo.com/vendor/v1/uploads/default_school.png';
                    }
                
                $query_city = $this->db->query("SELECT name FROM city_list where id = '$city_id'");
                $row_city = $query_city->row_array();
                $city_name = $row_city['name'];    
                    
                foreach($board as $board_id){
                    
                    $query_board = $this->db->query("SELECT name,slug FROM board where id = '$board_id'");
                    $row_board = $query_board->row_array();
                    $board_name = $row_board['name'];
                    $board_slug = $row_board['slug'];
                    
                    $school_name = ucwords(strtolower($row_school['name'])).' - '.$board_name;
                    
                    
                    
                     $data[] = array(
                                    "id" => $id,
                                    "school_name" => $this->shorter($school_name, '18'),
                                    "image_url" => $image_url,
                                    "city_name" => $city_name,
                                    "link" => 'school/bookset/'.$board_slug.'/'.$id,
                                );
                }
               
            }
        
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function dashboard_uniform_by_school($user_id="")
    {
        $kirti_id = kirti_id();
        $data = array();
        // $query_school = $this->db->query("SELECT id,name,slug,board,avatar,city_id FROM school where FIND_IN_SET('22', category) and status='1'");
        $query_school = $this->db->query("SELECT s.id,s.name,s.slug,s.board,s.avatar,s.city_id FROM school as s 
        INNER JOIN users as u ON u.id = s.vendor_id
        where s.vendor_id != '$kirti_id' AND FIND_IN_SET('22', s.category) and s.status='1' and u.email_status =1");
        foreach ($query_school->result_array() as $row_school) {
                
                $school_slug = $row_school['slug'];
                $id = $row_school['id'];
                $image     = $row_school['avatar']; 
                $city_id     = $row_school['city_id']; 
                
                if ($image!='') {
                        $image_url = 'https://api.skoozo.com/vendor/v1/' . $image;
                    } else {
                        $image_url = 'https://api.skoozo.com/vendor/v1/uploads/default_school.png';
                    }
                
                $query_city = $this->db->query("SELECT name FROM cities where id = '$city_id'");
                $row_city = $query_city->row_array();
                $city_name = $row_city['name'];    
                    
                
                    
                $school_name = ucwords(strtolower($row_school['name']));
                    
                    
                    
                     $data[] = array(
                                    "id" => $id,
                                    "school_name" => $this->shorter($school_name, '18'),
                                    "image_url" => $image_url,
                                    "city_name" => $city_name,
                                    "link" => 'uniform/'.$school_slug.'/'.$id,
                                );
               
               
            }
        
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    } 
    
    public function complaint_category($user_id="")   {
        $query = $this->db->query("SELECT * FROM scd_category order by sort asc");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            $short_name = $row['short_name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name,
                "short_name" => $short_name,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function complaint_sub_category($category_id="")   {
        $query = $this->db->query("SELECT * FROM scd_subcategory where category_id = '$category_id'");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function complaint_category_details($user_id,$complaint_id)   {
        $query = $this->db->query("SELECT * FROM scd_category where id = '$complaint_id'");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            $short_name = $row['short_name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name,
                "short_name" => $short_name,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function get_sub_category($category_id="")   {
        $category_id = implode(',',$category_id);
        $query = $this->db->query("SELECT * FROM categories where FIND_IN_SET(id, '$category_id')");
        $count = $query->num_rows();
        $data  = array();
        foreach ($query->result_array() as $row) {
            $id   = $row['id'];
            $name = $row['name'];
            
            $data[] = array(
                "id" => $id,
                "name" => $name,
            );
        }
        $resultpost = array(
            'status' => 200,
            'message' => 'success',
            'data' => $data
        );
        return $resultpost;
    }
    
    public function get_order_status_by_payment($payment_id)  {
       
        $query = $this->db->query("SELECT payment_status FROM orders where txn_id='$payment_id' group by payment_id");
        $row = $query->row_array();
        // echo $this->db->last_query();
        $payment_status   = $row['payment_status'];
            
        if($payment_status == 'payment_received'){
            $resultpost = array(
                'status' => 200,
                'message' => 'success',
            );
        }else{
             $resultpost = array(
                'status' => 400,
                'message' => 'failure',
            );
        }
        

        return $resultpost;
    }
    
    

        
    public function get_warehouse_city($id)
    {
        $id = clean_number($id);
        $this->db->where('id', $id);
        $query = $this->db->get('vendor_shipping_details');
        if($query->num_rows()>0){
         $sql= $query->row();
         return $sql->city_id;
        }
        else{
         return '';
        }
    } 
     

}