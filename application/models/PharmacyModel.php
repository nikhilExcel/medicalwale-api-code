<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PharmacyModel extends CI_Model
{
    var $client_service = "frontend-client";
    var $auth_key = "medicalwalerestapi";
    
    public function check_auth_client()
    {
        $client_service = $this->input->get_request_header('Client-Service', TRUE);
        $auth_key       = $this->input->get_request_header('Auth-Key', TRUE);
        if ($client_service == $this->client_service && $auth_key == $this->auth_key) {
            return true;
        } else {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        }
    }
    
    public function auth()
    {
        date_default_timezone_set('Asia/Kolkata');
        $users_id = $this->input->get_request_header('User-ID', TRUE);
        $token    = $this->input->get_request_header('Authorizations', TRUE);
        $q        = $this->db->select('expired_at')->from('api_users_authentication')->where('users_id', $users_id)->where('token', $token)->get()->row();
        if ($q == "") {
            return json_output(401, array(
                'status' => 401,
                'message' => 'Unauthorized.'
            ));
        } else {
            if ($q->expired_at < date('Y-m-d H:i:s')) {
                return json_output(401, array(
                    'status' => 401,
                    'message' => 'Your session has been expired.'
                ));
            } else {
                $updated_at = date('Y-m-d H:i:s');
                $expired_at = '2018-11-12 08:57:58';
                $this->db->where('users_id', $users_id)->where('token', $token)->update('api_users_authentication', array(
                    'expired_at' => $expired_at,
                    'updated_at' => $updated_at
                ));
                return array(
                    'status' => 200,
                    'message' => 'Authorized.'
                );
            }
        }
    }
	
	                     
            
public function check_day_status($day_type,$days_closed,$is_24hrs_available,$store_open,$store_close)
{


	if($is_24hrs_available==='Yes')
	{		
		if($day_type=='Monday')	{  $time='12:00 AM-11:59 PM'; 	}
		
		if($day_type=='Tuesday') {  $time='12:00 AM-11:59 PM'; 	}
		
		if($day_type=='Wednesday')	{  $time='12:00 AM-11:59 PM'; 	}
		
		if($day_type=='Thursday') {  $time='12:00 AM-11:59 PM'; 	}
		
		if($day_type=='Friday')	{  $time='12:00 AM-11:59 PM'; 	}
		
		if($day_type=='Saturday') {  $time='12:00 AM-11:59 PM'; 	}
		
		if($day_type=='Sunday')	{  $time='12:00 AM-11:59 PM'; 	}
	}
	else
	{	
		if($day_type=='Monday')
		{
			if($days_closed=='Monday Closed') { $time='close-close';}
			else { $time=$store_open.'-'.$store_close; }
		}
		
		if($day_type=='Tuesday')
		{
			if($days_closed=='Tuesday Closed') { $time='close-close';}
			else { $time=$store_open.'-'.$store_close; }
		}
		
		if($day_type=='Wednesday')
		{
			if($days_closed=='Wednesday Closed') { $time='close-close';}
			else { $time=$store_open.'-'.$store_close; }
		}
		
		if($day_type=='Thursday')
		{
			if($days_closed=='Thursday Closed') { $time='close-close';}
			else { $time=$store_open.'-'.$store_close; }
		}
		
		if($day_type=='Friday')
		{
			if($days_closed=='Friday Closed') { $time='close-close';}
			else { $time=$store_open.'-'.$store_close; }
		}
		
		if($day_type=='Saturday')
		{
			if($days_closed=='Saturday Closed') { $time='close-close';}
			else { $time=$store_open.'-'.$store_close; }
		}
		
		if($day_type=='Sunday')
		{
			if($days_closed=='Sunday Closed') { $time='close-close';}
			elseif($days_closed=='Sunday Half Day') { $time=$store_open.'-02:00 PM';}
			else { $time=$store_open.'-'.$store_close; }
		}
		
	}

	return $time;
}



public function check_current_delivery_charges($is_24hrs_available,$day_night_delivery,$free_start_time,$free_end_time,$night_delivery_charge,$days_closed,$is_min_order_delivery,$min_order)
{

	date_default_timezone_set('Asia/Kolkata');
	$free_start_time_st = date("h:i A", strtotime($free_start_time));
    $free_end_time_st = date("h:i A", strtotime($free_end_time)); 
	$current_time_st      = date('h:i A');
							 
    $current_time = DateTime::createFromFormat('H:i a', $current_time_st);
    $system_start_time = DateTime::createFromFormat('H:i a', $free_start_time_st);
    $system_end_time = DateTime::createFromFormat('H:i a', $free_end_time_st); 

	if($system_start_time < $system_end_time && $current_time <= $system_end_time){
        $system_end_time->modify('+1 day')->format('H:i a');                            
    }
     elseif ($system_start_time > $system_end_time && $current_time >= $system_end_time){
        $system_end_time->modify('+1 day')->format('H:i a');
    } 
    
  

	if($is_24hrs_available=='Yes')
	{
		if($day_night_delivery=='Yes'){
		
		if ($current_time > $system_start_time && $current_time < $system_end_time) {
		 if($is_min_order_delivery=='Yes'){
		    $current_delivery_charges = 'Free Delivery Available Above Rs '.$min_order;
		 }
		 else{
            $current_delivery_charges = 'Delivery Not Available Below Rs '.$min_order;
			}
        } 

        else
		{
          $current_delivery_charges = 'Delivery Charges Applied Rs '.$night_delivery_charge;                                         
        }		
		
		}
		
		else{		
			if ($current_time > $system_start_time && $current_time < $system_end_time) {
           // $current_delivery_charges = 'Free Delivery Available';	
			 if($is_min_order_delivery=='Yes'){
		     $current_delivery_charges = 'Free Delivery Available Above Rs '.$min_order;
		      }
		     else{
             $current_delivery_charges = 'Delivery Not Available Below Rs '.$min_order;
			  }
			}
			else
			{
			  $current_delivery_charges = 'Delivery Not Available Now';                                         
			}			
		
		}
	}
	else
	{

		if ($current_time > $system_start_time && $current_time < $system_end_time) {
            //$current_delivery_charges = 'Free Delivery Available';
			if($is_min_order_delivery=='Yes'){
		     $current_delivery_charges = 'Free Delivery Available Above Rs '.$min_order;
		      }
		     else{
             $current_delivery_charges = 'Delivery Not Available Below Rs '.$min_order;
			  }
        } 
        else
		{
          $current_delivery_charges = 'Delivery Not Available Now';                                         
        }	
	
	
	}

	
	return $current_delivery_charges;
}
   
   
public function check_time_format($time)
{
  $time_filter = preg_replace('/\s+/', '', $time);   
  $final_time = date("h:i A", strtotime($time_filter));  
  return $final_time;
}
     
   
   
   public function is_free_delivery_staus($free_start_time,$free_end_time){
    $free_start_time_st = date("h:i A", strtotime($free_start_time));
    $free_end_time_st = date("h:i A", strtotime($free_end_time));
    $current_time_st      = date('h:i A');


    $date1 = DateTime::createFromFormat('H:i a', $current_time_st);
    $date2 = DateTime::createFromFormat('H:i a', $free_start_time_st);
    $date3 = DateTime::createFromFormat('H:i a', $free_end_time_st); 
                                 
   if($date2 < $date3 && $date1 <= $date3){
       $date3->modify('+1 day')->format('H:i a');
    }
    elseif ($date2 > $date3 && $date1 >= $date3){
         $date3->modify('+1 day')->format('H:i a');
    }
                                    
                                    
     if ($date1 > $date2 && $date1 < $date3) {
         $is_free_delivery = 'Yes';
      } 
     else{
        $is_free_delivery = 'No'; 
      }

	  return $is_free_delivery;

}
    
    public function pharmacy_list($user_id, $mlat, $mlng)
    {
        $radius = '5';
        function GetDrivingDistance($lat1, $lat2, $long1, $long2)
        {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch  = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist       = $response_a['rows'][0]['elements'][0]['distance']['text'];
            return $dist;
        }
        
        $sql = sprintf("SELECT `id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));        
        
        $query = $this->db->query($sql);
        foreach ($query->result_array() as $row) {
            $lat                       = $row['lat'];
            $lng                       = $row['lng'];
            $medical_id                = $row['user_id'];
            $medical_name              = $row['medical_name'];
            $store_manager             = $row['store_manager'];
            $address1                  = $row['address1'];
            $address2                  = $row['address2'];
            $pincode                   = $row['pincode'];
            $city                      = $row['city'];
            $state                     = $row['state'];
            $contact_no                = $row['contact_no'];
            $whatsapp_no               = $row['whatsapp_no'];
            $email                     = $row['email'];
            $store_since               = $row['store_since'];
            $website                   = $row['website'];
            $reach_area                = $row['reach_area'];
            $is_24hrs_available        = $row['is_24hrs_available'];
            if($is_24hrs_available=='Yes'){
            $store_open                = date("h:i A", strtotime("12:00 AM"));
            $store_close               = date("h:i A", strtotime("11:59 PM"));    
            }
            else{
            $store_open                = $this->check_time_format($row['store_open']);
            $store_close               = $this->check_time_format($row['store_close']);
            }
            $day_night_delivery        = $row['day_night_delivery'];
            $free_start_time           = $this->check_time_format($row['free_start_time']);  
            $free_end_time             = $this->check_time_format($row['free_end_time']);	
            $is_free_delivery = $this->is_free_delivery_staus($free_start_time,$free_end_time);    
			$days_closed               = $row['days_closed'];
            $min_order                 = $row['min_order'];
            $is_min_order_delivery       = $row['is_min_order_delivery'];
            $min_order_delivery_charge = $row['min_order_delivery_charge'];
            $night_delivery_charge     = $row['night_delivery_charge'];
            $payment_type              = $row['payment_type'];
            
            $online_offline = $row['online_offline'];
            $km             = $row['distance'];
            $profile_pic    = $row['profile_pic'];
            if ($row['profile_pic'] != '') {
                $profile_pic = $row['profile_pic'];
                $profile_pic = str_replace(' ', '%20', $profile_pic);
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$profile_pic;
            } else {
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
            }            
            
            $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $medical_id)->get()->num_rows();
            $following = $this->db->select('id')->from('follow_user')->where('user_id', $medical_id)->get()->num_rows();
            $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
            
            if ($is_follow > 0) {
                $is_follow = 'Yes';
            } else {
                $is_follow = 'No';
            }            
            
            $profile_view = $this->db->select('id')->from('pharmacy_view')->where('listing_id', $medical_id)->get()->num_rows();

          
            $activity_id  = '0';
            
             $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$medical_id'"); 
        	$row_pharma=$query_pharmacy->row_array();
	    	$rating = $row_pharma['avg_rating']; 
	    	if($rating === NULL) {
	    	    $rating='0';
	    	}
         
        
        
           
             $review = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $medical_id)->get()->num_rows();
            
            $chat_id      =  $row['user_id'];
            $chat_display =  $row['medical_name'];
            $is_chat      = 'Yes';
                 
            $meter          = '0';
            $distance       = $km * 1000;
            $store_distance = round($distance, 2);
            if ($distance > 999) {
                $distance = $distance / 1000;
                $meter    = round($distance, 2) . ' km';
            } else {
                $meter = round($distance) . ' meters away';
            }
            
            $reach_area = str_replace(" Mtr", "", $reach_area);
            $reach_area = str_replace(" Km", "", $reach_area);
            if ($reach_area > 10) {
                $ranges = ($reach_area / 1000);
            } else {
                $ranges = $reach_area;
            }
            
            $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
            

//All Days Open
                      

$Monday=$this->check_day_status('Monday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Tuesday=$this->check_day_status('Tuesday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Wednesday=$this->check_day_status('Wednesday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Thursday=$this->check_day_status('Thursday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Friday=$this->check_day_status('Friday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Saturday=$this->check_day_status('Saturday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Sunday=$this->check_day_status('Sunday',$days_closed,$is_24hrs_available,$store_open,$store_close);


$opening_hours="Monday>$Monday,Tuesday>$Tuesday,Wednesday>$Wednesday,Thursday>$Thursday,Friday>$Friday,Saturday>$Saturday,Sunday>$Sunday";

                $open_days         = '';
                $day_array_list    = '';
                $day_list          = '';
                $day_time_list     = '';
                $time_list1        = '';
                $time_list2        = '';
                $time              = '';
                $system_start_time = '';
                $system_end_time   = '';
                $time_check        = '';
                $current_time      = '';
                $open_close        = array();
                $time              = array();
                date_default_timezone_set('Asia/Kolkata');
                $data           = array();
                $final_Day      = array();
                $day_array_list = explode(',', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time       = array();
                                $open_close = array();
                                for ($l = 0; $l < count($time_list1); $l++) {
                                    $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                    $time            = str_replace('close-close', 'close', $time_check); 
                                    if($time=='12:00 AM-11:59 PM'){
									$time='24 hrs open';
									}
                                    $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                    $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                    $current_time      = date('h:i A');


                             $date1 = DateTime::createFromFormat('H:i a', $current_time);
                             $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                             $date3 = DateTime::createFromFormat('H:i a', $system_end_time); 
                             
                             if($date2 < $date3 && $date1 <= $date3){
                             $date3->modify('+1 day')->format('H:i a');                            
                             }
                             elseif ($date2 > $date3 && $date1 >= $date3){
                                $date3->modify('+1 day')->format('H:i a');
                             }
                                    
                                    
                                    if ($date1 > $date2 && $date1 < $date3) {
                                        $open_close = 'open';
                                    }
                                    else{
                                       $open_close = 'closed'; 
                                        
                                    }
                                }
                            }
                        }
                        $final_Day[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                    }
                } else {
                    $final_Day[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }
                $current_day = "";        
          
			$current_delivery_charges=$this->check_current_delivery_charges($is_24hrs_available,$day_night_delivery,$free_start_time,$free_end_time,$night_delivery_charge,$days_closed,$is_min_order_delivery,$min_order);
            
          $product_category_list=array();              
	    $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
        foreach ($query_category->result_array() as $row) {
            $product_id       = $row['id'];
            $product_category = $row['category'];
            $product_image    = $row['image'];
            $product_image    = str_replace(" ", "", $product_image);
            $product_image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
            
            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                'image' => $product_image);
        }
			
			
            
            $resultpost[] = array(
                'id' => $medical_id, 
                'medical_name' => $medical_name,
                'listing_id' => $chat_id,
                'listing_type' => '13',
                'latitude' => $lat,
                'longitude' => $lng,
                'store_manager' => $store_manager,
                'address1' => $address1,
                'address2' => $address2,
                'pincode' => $pincode,
                'city' => $city,
                'state' => $state,
                'contact_no' => $contact_no,
                'whatsapp_no' => $whatsapp_no,
                'email' => $email,
                'store_since' => $store_since,
                'website' => $website,
                'reach_area' => $ranges,
                'store_distance' => $store_distance,
                'distance' => $distances,
                'is_24hrs_available' => $is_24hrs_available,
                'store_open' => $store_open,
                'store_close' => $store_close,
                'day_night_delivery' => $day_night_delivery,
                'free_start_time' => $free_start_time,
                'free_end_time' => $free_end_time,
                'is_free_delivery' => $is_free_delivery,
                'days_closed' => $days_closed,
                'min_order' => $min_order,
                'is_min_order_delivery' => $is_min_order_delivery,
                'min_order_delivery_charge' => $min_order_delivery_charge,
                'night_delivery_charge' => $night_delivery_charge,
				'opening_day' => $final_Day,
                'current_delivery_charges' => $current_delivery_charges,
                'payment_type' => $payment_type,
                'online_offline' => $online_offline,
                'profile_pic' => $profile_pic,
                'rating' => (string)$rating,
                'followers' => $followers,
                'following' => $following,
                'profile_view' => $profile_view,
                'activity_id' => $activity_id,
                'is_follow' => $is_follow,
                'chat_id' => $chat_id,
                'chat_display' => $chat_display,
                'is_chat' => $is_chat,
                'review' => $review,
                'category_list' => $product_category_list
            );
        }
        function array_sort_by_column(&$arr, $col, $dir = SORT_ASC)
        {
            $sort_col = array();
            foreach ($arr as $key => $row) {
                $sort_col[$key] = $row[$col];
            }            
            array_multisort($sort_col, $dir, $arr);
        }    
        array_sort_by_column($resultpost, 'distance');
        return $resultpost;
    }
    
    
    public function pharmacy_details($user_id,$listing_id)
    {
  $query = $this->db->query("SELECT `id`, `user_id`, `email`, `medical_name`, `about_us`, `reg_date`, `store_manager`, `lat`, `lng`, `address1`, `address2`, `pincode`, `city`, `state`, `contact_no`, IFNULL(whatsapp_no,'') AS whatsapp_no, `profile_pic`, IFNULL(website,'') AS website, `day_night_delivery`, `delivery_till`, `delivery_time`, `days_closed`, `reach_area`, `is_24hrs_available`, `payment_type`, `store_open`, `store_close`, `min_order`,`is_min_order_delivery`, `min_order_delivery_charge`, `night_delivery_charge`, `store_since`, `free_start_time`, `free_end_time`,IFNULL(online_offline,'') AS online_offline FROM medical_stores  WHERE  user_id='$listing_id'");
   
             $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
            $lat                       = $row['lat'];
            $lng                       = $row['lng'];
            $medical_id                = $row['user_id'];
            $medical_name              = $row['medical_name'];
            $store_manager             = $row['store_manager'];
            $address1                  = $row['address1'];
            $address2                  = $row['address2'];
            $pincode                   = $row['pincode'];
            $city                      = $row['city'];
            $state                     = $row['state'];
            $contact_no                = $row['contact_no'];
            $whatsapp_no               = $row['whatsapp_no'];
            $email                     = $row['email'];
            $store_since               = $row['store_since'];
            $website                   = $row['website'];
            $reach_area                = $row['reach_area'];
            $is_24hrs_available        = $row['is_24hrs_available'];
            if($is_24hrs_available=='Yes'){
            $store_open                = date("h:i A", strtotime("12:00 AM"));
            $store_close               = date("h:i A", strtotime("11:59 PM"));    
            }
            else{
            $store_open                = $this->check_time_format($row['store_open']);
            $store_close               = $this->check_time_format($row['store_close']);
            }
            $day_night_delivery        = $row['day_night_delivery'];
            $free_start_time           = $this->check_time_format($row['free_start_time']);  
            $free_end_time             = $this->check_time_format($row['free_end_time']);
			$is_free_delivery = $this->is_free_delivery_staus($free_start_time,$free_end_time);  
            $days_closed               = $row['days_closed'];
            $min_order                 = $row['min_order'];
            $is_min_order_delivery     = $row['is_min_order_delivery'];
            $min_order_delivery_charge = $row['min_order_delivery_charge'];
            $night_delivery_charge     = $row['night_delivery_charge'];
            $payment_type              = $row['payment_type'];
            
            $online_offline = $row['online_offline'];
         
            $profile_pic    = $row['profile_pic'];
            if ($row['profile_pic'] != '') {
                $profile_pic = $row['profile_pic'];
                $profile_pic = str_replace(' ', '%20', $profile_pic);
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/healthwall_avatar/'.$profile_pic;
            } else {
                $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
            }            
            
            $followers = $this->db->select('id')->from('follow_user')->where('parent_id', $medical_id)->get()->num_rows();
            $following = $this->db->select('id')->from('follow_user')->where('user_id', $medical_id)->get()->num_rows();
            $is_follow = $this->db->select('id')->from('follow_user')->where('user_id', $user_id)->where('parent_id', $medical_id)->get()->num_rows();
            
            if ($is_follow > 0) {
                $is_follow = 'Yes';
            } else {
                $is_follow = 'No';
            }            
            
            $profile_view = $this->db->select('id')->from('pharmacy_view')->where('listing_id', $medical_id)->get()->num_rows();

          
            $activity_id  = '0';
            
             $query_pharmacy = $this->db->query("SELECT  ROUND(AVG(rating),1) AS avg_rating FROM medical_stores_review WHERE medical_stores_id='$medical_id'"); 
        	$row_pharma=$query_pharmacy->row_array();
	    	$rating = $row_pharma['avg_rating']; 
	    	if($rating === NULL) {
	    	    $rating='0';
	    	}
         
         $review = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $medical_id)->get()->num_rows();
            
            $chat_id      =  $row['user_id'];
            $chat_display =  $row['medical_name'];
            $is_chat      = 'Yes';
             
             

//All Days Open
                      

$Monday=$this->check_day_status('Monday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Tuesday=$this->check_day_status('Tuesday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Wednesday=$this->check_day_status('Wednesday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Thursday=$this->check_day_status('Thursday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Friday=$this->check_day_status('Friday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Saturday=$this->check_day_status('Saturday',$days_closed,$is_24hrs_available,$store_open,$store_close);
$Sunday=$this->check_day_status('Sunday',$days_closed,$is_24hrs_available,$store_open,$store_close);


$opening_hours="Monday>$Monday,Tuesday>$Tuesday,Wednesday>$Wednesday,Thursday>$Thursday,Friday>$Friday,Saturday>$Saturday,Sunday>$Sunday";

                $open_days         = '';
                $day_array_list    = '';
                $day_list          = '';
                $day_time_list     = '';
                $time_list1        = '';
                $time_list2        = '';
                $time              = '';
                $system_start_time = '';
                $system_end_time   = '';
                $time_check        = '';
                $current_time      = '';
                $open_close        = array();
                $time              = array();
                date_default_timezone_set('Asia/Kolkata');
                $data           = array();
                $final_Day      = array();
                $day_array_list = explode(',', $opening_hours);
                if (count($day_array_list) > 1) {
                    for ($i = 0; $i < count($day_array_list); $i++) {
                        $day_list = explode('>', $day_array_list[$i]);
                        for ($j = 0; $j < count($day_list); $j++) {
                            $day_time_list = explode('-', $day_list[$j]);
                            for ($k = 1; $k < count($day_time_list); $k++) {
                                $time_list1 = explode(',', $day_time_list[0]);
                                $time_list2 = explode(',', $day_time_list[1]);
                                $time       = array();
                                $open_close = array();
                                for ($l = 0; $l < count($time_list1); $l++) {
                                    $time_check        = $time_list1[$l] . '-' . $time_list2[$l];
                                    $time            = str_replace('close-close', 'close', $time_check); 
                                    if($time=='12:00 AM-11:59 PM'){
									$time='24 hrs open';
									}
                                    $system_start_time = date("h:i A", strtotime($time_list1[$l]));
                                    $system_end_time = date("h:i A", strtotime($time_list2[$l]));
                                    $current_time      = date('h:i A');


                                 $date1 = DateTime::createFromFormat('H:i a', $current_time);
                                 $date2 = DateTime::createFromFormat('H:i a', $system_start_time);
                                 $date3 = DateTime::createFromFormat('H:i a', $system_end_time); 
                                 
                                 if($date2 < $date3 && $date1 <= $date3){
                                 $date3->modify('+1 day')->format('H:i a');
                                
                                 }
                                 elseif ($date2 > $date3 && $date1 >= $date3){
                                    $date3->modify('+1 day')->format('H:i a');
                                 }
                                    
                                    
                                    if ($date1 > $date2 && $date1 < $date3) {
                                        $open_close = 'open';
                                    } 
                                    else{
                                       $open_close = 'closed'; 
                                        
                                    }
                                    
                                }
                            }
                        }
                        $final_Day[] = array(
                            'day' => $day_list[0],
                            'time' => $time,
                            'status' => $open_close
                        );
                    }
                } else {
                    $final_Day[] = array(
                        'day' => 'close',
                        'time' => array(),
                        'status' => array()
                    );
                }
                $current_day = "";        
          
			$current_delivery_charges=$this->check_current_delivery_charges($is_24hrs_available,$day_night_delivery,$free_start_time,$free_end_time,$night_delivery_charge,$days_closed,$is_min_order_delivery,$min_order);
		              
	   $product_category_list=array();              
	   $query_category = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
        foreach ($query_category->result_array() as $row) {
            $product_id       = $row['id'];
            $product_category = $row['category'];
            $product_image    = $row['image'];
            $product_image    = str_replace(" ", "", $product_image);
            $product_image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/product_category/' . $product_image;
            
            $product_category_list[] = array(
                "id" => $product_id,
                "category" => $product_category,
                'image' => $product_image);
        }
			
			
		  
            $resultpost[] = array(
                'id' => $medical_id,   
                'medical_name' => $medical_name,   
                'latitude' => $lat,
                'longitude' => $lng,
                'store_manager' => $store_manager,
                'address1' => $address1,
                'address2' => $address2,
                'pincode' => $pincode,
                'city' => $city,
                'state' => $state,
                'contact_no' => $contact_no,
                'whatsapp_no' => $whatsapp_no,
                'email' => $email,
                'store_since' => $store_since,
                'website' => $website,
                'is_24hrs_available' => $is_24hrs_available,
                'store_open' => $store_open,
                'store_close' => $store_close,
                'day_night_delivery' => $day_night_delivery,
                'free_start_time' => $free_start_time,
                'free_end_time' => $free_end_time,
				'is_free_delivery' => $is_free_delivery,
                'days_closed' => $days_closed,
                'min_order' => $min_order,
                'is_min_order_delivery' => $is_min_order_delivery,
                'min_order_delivery_charge' => $min_order_delivery_charge,
                'night_delivery_charge' => $night_delivery_charge,
				'opening_day' => $final_Day,
                'current_delivery_charges' => $current_delivery_charges,
                'payment_type' => $payment_type,
                'online_offline' => $online_offline,
                'profile_pic' => $profile_pic,
                'rating' => (string)$rating,
                'followers' => $followers,
                'following' => $following,
                'profile_view' => $profile_view,
                'activity_id' => $activity_id,
                'is_follow' => $is_follow,
                'chat_id' => $chat_id,
                'chat_display' => $chat_display,
                'is_chat' => $is_chat,
                'review' => $review,
                'category_list' => $product_category_list
            );
        }
        }
        else{
            $resultpost=array();
        }
        return $resultpost;
    }
    
    public function category_list()
    {        
        $query = $this->db->query("SELECT id,category,image FROM `category` WHERE id<>'3' order by id asc");
        foreach ($query->result_array() as $row) {
            $id       = $row['id'];
            $category = $row['category'];
            $image    = $row['image'];
            $image    = str_replace(" ", "", $image);
            $image    = 'https://d2c8oti4is0ms3.cloudfront.net/images/medicalwale_mobile_app_icons/product_category/' . $image;
            
            $resultpost[] = array(
                "id" => $id,
                "category" => $category,
                'image' => $image
            );
        }
        return $resultpost;        
    }
    
    public function sub_category($category_id)
    {        
        $query = $this->db->query("SELECT id,category,sub_category FROM `product_sub_category` WHERE category='$category_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
                $sub_category = $row['sub_category'];
                $resultpost[] = array(
                    "id" => $id,
                    "sub_category" => $sub_category
                );
            }
        } else {
            $resultpost = '';
        }
        return $resultpost;        
    }    
    
    public function product_list($sub_category_id)
    {        
        $query = $this->db->query("SELECT id as product_id,product_name,is_prescription_needed,product_price,pack FROM `product` WHERE sub_category='$sub_category_id'");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $product_id    = $row['product_id'];
                $product_name  = $row['product_name'];
                $product_price = $row['product_price'];
                 $str          = $row['pack'];
                
                 if($str == ''){
                     $pack = '1Others';
                }
                else{
                if (preg_match('#[0-9]#',$str)){ 
     
                    $pack = $str; 
                }else{ 
                    $pack = '1strip'; 
                     
                }  
                }
                $product_name  = $row['product_name'];   
                $is_prescription_needed  = $row['is_prescription_needed'];
                $product_image = $product_name . '.jpg';
                $product_image = str_replace(' ', '%20', $product_image);
                $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $product_image;
                $resultpost[]  = array(
                    "sub_category_id" => $sub_category_id,
                    "product_id" => $product_id,
                    "product_name" => $product_name,
                    "is_prescription_needed" => $is_prescription_needed,
                    "product_price" => $product_price,
                    'product_weight' => $pack,
                    'product_image' => $product_image
                );
            }
        } else {
            $resultpost = array();
        }        
        return $resultpost;        
    }
    
    public function product_search($keyword)
    {        
        $query = $this->db->query("SELECT id,sub_category,product_name,is_prescription_needed,pack,product_price FROM product WHERE product_name LIKE '%$keyword%' limit 15");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $product_id    = $row['id']; 
                $sub_category  = $row['sub_category'];
                $product_name  = $row['product_name']; 
                $is_prescription_needed  = $row['is_prescription_needed'];
                $product_price = $row['product_price'];
                 $str          = $row['pack'];
                
                 if($str == ''){
                     $pack = '1Others';
                }
                else{
                if (preg_match('#[0-9]#',$str)){ 
     
                    $pack = $str; 
                }else{ 
                    $pack = '1strip'; 
                     
                }  
                }
            
                $product_image = $product_name . '.jpg';
                $product_image = str_replace(' ', '%20', $product_image);
                $product_image = 'https://d2c8oti4is0ms3.cloudfront.net/images/product_images/' . $product_image;
                $resultpost[]  = array(  
                    "sub_category_id" => $sub_category,
                    "product_id" => $product_id,
                    "product_name" => $product_name,  
                    "is_prescription_needed" => $is_prescription_needed,
                    "product_price" => $product_price,
                    'product_weight' => $pack,
                    'product_image' => $product_image
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;        
    }
    
    public function cart_order($user_id, $address_id, $medical_id, $payType, $product_id, $product_quantity, $product_price)
    {       
        $status         = "Pending";
        $product_status = 'Pending';        
        date_default_timezone_set('Asia/Kolkata');
        $date   = date('Y-m-d');
        $uni_id = date('YmdHis');        
        $discount      = '0';
        $grand_total   = '0';
        $final_total   = '0';
        $discount_rate = '0';              
        
        $product_id       = explode(",", $product_id);
        $product_quantity = explode(",", $product_quantity);
        $product_price    = explode(",", $product_price);
        $cnt              = count($product_id);
        for ($i = 0; $i < $cnt; $i++) {
            $final_total = $final_total + ($product_price[$i] * $product_quantity[$i]);
        }        
        $discount_query = $this->db->query("SELECT discount FROM `discount` WHERE medical_id='$medical_id'");
        $discount_list  = $discount_query->row();
        if ($discount_list) {
            $discount      = $discount_list->discount;
            $discount_rate = ($final_total * $discount) / 100;
            $grand_total   = $final_total - $discount_rate;
        } else {
            $grand_total = $final_total;
        }        
        $cart_order_data = array(
            'medical_id' => $medical_id,
            'user_id' => $user_id,
            'address_id' => $address_id,
            'uni_id' => $uni_id,
            'date' => $date,
            'status' => $status,
            'store_status' => '0',
            'customer_status' => '0',
            'total' => $grand_total,
            'discount' => $discount,
            'payType' => $payType
        );
        $insert1         = $this->db->insert('cart_order', $cart_order_data);
        $order_id        = $this->db->insert_id();        
        $cnt = count($product_id);        
        for ($i = 0; $i < $cnt; $i++) {
            $sub_total = $product_price[$i] * $product_quantity[$i];
            
            $cart_order_products_data = array(
                'order_id' => $order_id,
                'medical_id' => $medical_id,
                'product_id' => $product_id[$i],
                'product_quantity' => $product_quantity[$i],
                'product_price' => $product_price[$i],
                'sub_total' => $sub_total,
                'product_status' => 'pending',
                'product_status_type' => '',
                'product_status_value' => '',
                'uni_id' => $uni_id                
            );
            $insert2  = $this->db->insert('cart_order_products', $cart_order_products_data);
        }        
        if ($insert1 & $insert2) {
            $order_type    = 'order';
            $noti_message  = 'Thanks for placing your order with Medicalwale';
            $noti_message2 = 'New Order Has Been Placed To Your Store';
            $date_time     = date('Y-m-d H:i:s');
            
            $insert_notification = array(
                'user_id' => $user_id,
                'order_id' => $order_id,
                'order_type' => $order_type,
                'message' => $noti_message,
                'status' => '0',
                'date' => $date_time
            );
            $this->db->insert('notification', $insert_notification);            
            
            $store_notification = array(
                'medical_id' => $medical_id,
                'order_id' => $order_id,
                'order_type' => $order_type,
                'message' => $noti_message2,
                'status' => '0',
                'date' => $date_time
            );
            $this->db->insert('store_notification', $store_notification);
        }
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }  
    
    public function cart_order_list($user_id)
    {        
        $query = $this->db->query("SELECT cart_order.id AS order_id,cart_order.uni_id,cart_order.date,cart_order.status,medical_stores.medical_name  FROM `cart_order`
		INNER JOIN `cart_order_products`
		ON cart_order.id=cart_order_products.order_id
		INNER JOIN `medical_stores`
		ON medical_stores.user_id=cart_order_products.medical_id
		WHERE cart_order.user_id='$user_id' GROUP BY cart_order.uni_id");
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_id     = $row['order_id'];
                $order_no     = $row['uni_id'];
                $medical_name = $row['medical_name'];
                $order_status = $row['status'];
                $order_date   = $row['date'];
                
                $resultpost[] = array(
                    "order_id" => $order_id,
                    "order_no" => $order_no,
                    "medical_name" => $medical_name,
                    'order_status' => $order_status,
                    'order_date' => $order_date
                );
            }
        } else {
            $resultpost = '';
        }  
        return $resultpost;
    }
    
    public function cart_order_details($user_id, $order_id)
    {
        $query = $this->db->query("SELECT cart_order.uni_id,cart_order.date,cart_order.status,medical_stores.medical_name,GROUP_CONCAT(product.product_name) AS product_name,GROUP_CONCAT(product.product_price) AS product_price,GROUP_CONCAT(cart_order_products.product_quantity) AS product_quantity,product.is_active,IFNULL(oc_address.address_id,'') AS address_id,IFNULL(oc_address.customer_id,'') AS customer_id,IFNULL(oc_address.firstname,'') AS firstname,IFNULL(oc_address.lastname,'') AS lastname,IFNULL(oc_address.email,'') AS email,IFNULL(oc_address.telephone,'') AS telephone,IFNULL(oc_address.lastname,'') AS lastname,IFNULL(oc_address.address_1,'') AS address_1,IFNULL(oc_address.address_2,'') AS address_2
            FROM `cart_order`
            INNER JOIN `cart_order_products`
            ON cart_order.id=cart_order_products.order_id
            INNER JOIN product
            ON product.id=cart_order_products.product_id
            INNER JOIN medical_stores
            ON medical_stores.user_id=cart_order_products.medical_id
            LEFT JOIN `oc_address`
            ON oc_address.address_id=cart_order.address_id
            WHERE cart_order.user_id='$user_id' AND cart_order.id='$order_id'");
        
        $count = $query->num_rows();
        if ($count > 0) {
            foreach ($query->result_array() as $row) {
                $order_no          = $row['uni_id'];
                $order_date        = $row['date'];
                $order_status      = $row['status'];
                $medical_name      = $row['medical_name'];
                $product_name      = $row['product_name'];
                $product_price     = $row['product_price'];
                $product_quantity  = $row['product_quantity'];
                $firstname         = $row['firstname'];
                $lastname          = $row['lastname'];
                $addr_patient_name = $firstname . ' ' . $lastname;
                $addr_address1     = $row['address_1'];
                $addr_address2     = $row['address_2'];
                $addr_landmark     = $row['landmark'];
                $addr_mobile       = $row['telephone'];
                $is_active         = $row['is_active'];
                
                if ($is_active == 1) {
                    $product_availability = 'Available';
                } else {
                    $product_availability = 'Not Available';
                }                
                $resultpost[] = array(
                    "order_no" => $order_no,
                    "order_date" => $order_date,
                    "order_status" => $order_status,
                    "medical_name" => $medical_name,
                    "product_name" => $product_name,
                    "product_price" => $product_price,
                    "product_quantity" => $product_quantity,
                    "product_availability" => $product_availability,
                    "addr_patient_name" => $addr_patient_name,
                    "addr_address1" => $addr_address1,
                    "addr_address2" => $addr_address2,
                    "addr_landmark" => $addr_landmark,
                    "addr_mobile" => $addr_mobile
                );
            }
        } else {
            $resultpost = '';
        }        
        return $resultpost;
    } 
    
    
    
    public function review_list($user_id, $listing_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . " yrs ago";
            } else if (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . " yr ago";
            } else if (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . " months ago";
            } else if (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . " month ago";
            } else if (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . " days ago";
            } else if (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . " day ago";
            } else if (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . " hrs ago";
            } else if (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . " hr ago";
            } else if (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . " min ago";
            } else if (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . " sec ago";
            } else {
                return "few seconds ago";
            }
        }
        $resultpost   = '';
        $review_count = $this->db->select('id')->from('medical_stores_review')->where('medical_stores_id', $listing_id)->get()->num_rows();
        if ($review_count > 0) {
            $query = $this->db->query("SELECT medical_stores_review.id,medical_stores_review.user_id,medical_stores_review.medical_stores_id,medical_stores_review.rating,medical_stores_review.review, medical_stores_review.service,medical_stores_review.date as review_date,users.id as user_id,users.name as firstname FROM `medical_stores_review` INNER JOIN `users` ON medical_stores_review.user_id=users.id WHERE medical_stores_review.medical_stores_id='$listing_id' order by medical_stores_review.id desc");
            
            foreach ($query->result_array() as $row) {
                $id          = $row['id'];
                $user_id     = $row['user_id'];
                $username    = $row['firstname'];
                $rating      = $row['rating'];
                $review      = $row['review'];	
                $review = preg_replace('~[\r\n]+~', '', $review);
                if(base64_encode(base64_decode($review)) === $review){
                    $review=base64_decode($review);
                }	
                $service     = $row['service'];
                $review_date = $row['review_date'];
                $review_date = get_time_difference_php($review_date);
                
                $like_count  = $this->db->select('id')->from('medical_stores_review_likes')->where('post_id', $id)->get()->num_rows();
                $post_count  = $this->db->select('id')->from('medical_stores_review_comment')->where('post_id', $id)->get()->num_rows();
                $like_yes_no = $this->db->select('id')->from('medical_stores_review_likes')->where('user_id', $user_id)->where('post_id', $id)->get()->num_rows();
                
                $resultpost[] = array(
                    'id' => $id, 
                    'user_id' => $user_id,
                    'username' => $username,
                    'rating' => $rating,
                    'review' => $review,
                    'service' => $service,
                    'review_date' => $review_date,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'comment_count' => $post_count
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
	
    public function review_like($user_id, $post_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id from medical_stores_review_likes where post_id='$post_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_stores_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $like_query = $this->db->query("SELECT id from medical_stores_review_likes where post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'like' => '0',
                'total_like' => $total_like
            );
        } else {
            $medical_stores_review_likes = array(
                'user_id' => $user_id,
                'post_id' => $post_id
            );
            $this->db->insert('medical_stores_review_likes', $medical_stores_review_likes);
            $like_query = $this->db->query("SELECT id FROM medical_stores_review_likes WHERE post_id='$post_id'");
            $total_like = $like_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'like' => '1',
                'total_like' => $total_like
            );
        }
    }
    
    public function add_review($user_id, $listing_id, $rating, $review, $service)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date         = date('Y-m-d H:i:s');
        $review_array = array(
            'user_id' => $user_id,
            'medical_stores_id' => $listing_id,
            'rating' => $rating,
            'review' => $review,
            'service' => $service,
            'date' => $date
        );
        $this->db->insert('medical_stores_review', $review_array);
        return array(
            'status' => 201,
            'message' => 'success'
        );
    }
    
    public function review_comment($user_id, $post_id, $comment)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at = date('Y-m-d H:i:s');
        $medical_stores_review_comment = array(
            'user_id' => $user_id,
            'post_id' => $post_id,
            'comment' => $comment,
            'date' => $created_at
        );
        $this->db->insert('medical_stores_review_comment', $medical_stores_review_comment);
        $medical_stores_review_comment_query = $this->db->query("SELECT id FROM medical_stores_review_comment WHERE post_id='$post_id'");
        $total_comment  = $medical_stores_review_comment_query->num_rows();
        return array(
            'status' => 201,
            'message' => 'success',
            'comment' => '1',
            'total_comment' => $total_comment
        );
    }
    
    public function review_comment_like($user_id, $comment_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $created_at  = date('Y-m-d H:i:s');
        $count_query = $this->db->query("SELECT id FROM medical_stores_review_comment_like WHERE comment_id='$comment_id' and user_id='$user_id'");
        $count       = $count_query->num_rows();
        if ($count > 0) {
            $this->db->query("DELETE FROM `medical_stores_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $comment_query = $this->db->query("SELECT id FROM medical_stores_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'deleted',
                'comment_like' => '0',
                'total_comment' => $total_comment
            );
        } else {
            $medical_stores_review_comment_like = array(
                'user_id' => $user_id,
                'comment_id' => $comment_id
            );
            $this->db->insert('medical_stores_review_comment_like', $medical_stores_review_comment_like);
            $comment_query      = $this->db->query("SELECT id FROM medical_stores_review_comment_like WHERE comment_id='$comment_id'");
            $total_comment_like = $comment_query->num_rows();
            return array(
                'status' => 201,
                'message' => 'success',
                'comment_like' => '1',
                'total_comment_like' => $total_comment_like
            );
        }
    }
    
    public function review_comment_list($user_id, $post_id)
    {
        function get_time_difference_php($created_time)
        {
            date_default_timezone_set('Asia/Calcutta');
            $str            = strtotime($created_time);
            $today          = strtotime(date('Y-m-d H:i:s'));
            $time_differnce = $today - $str;
            $years          = 60 * 60 * 24 * 365;
            $months         = 60 * 60 * 24 * 30;
            $days           = 60 * 60 * 24;
            $hours          = 60 * 60;
            $minutes        = 60;
            if (intval($time_differnce / $years) > 1) {
                return intval($time_differnce / $years) . ' yrs ago';
            } elseif (intval($time_differnce / $years) > 0) {
                return intval($time_differnce / $years) . ' yr ago';
            } elseif (intval($time_differnce / $months) > 1) {
                return intval($time_differnce / $months) . ' months ago';
            } elseif (intval(($time_differnce / $months)) > 0) {
                return intval(($time_differnce / $months)) . ' month ago';
            } elseif (intval(($time_differnce / $days)) > 1) {
                return intval(($time_differnce / $days)) . ' days ago';
            } elseif (intval(($time_differnce / $days)) > 0) {
                return intval(($time_differnce / $days)) . ' day ago';
            } elseif (intval(($time_differnce / $hours)) > 1) {
                return intval(($time_differnce / $hours)) . ' hrs ago';
            } elseif (intval(($time_differnce / $hours)) > 0) {
                return intval(($time_differnce / $hours)) . ' hr ago';
            } elseif (intval(($time_differnce / $minutes)) > 1) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce / $minutes)) > 0) {
                return intval(($time_differnce / $minutes)) . ' min ago';
            } elseif (intval(($time_differnce)) > 1) {
                return intval(($time_differnce)) . ' sec ago';
            } else {
                return 'few seconds ago';
            }
        }
        $review_list_count = $this->db->select('id')->from('medical_stores_review_comment')->where('post_id', $post_id)->get()->num_rows();
        if ($review_list_count) {
            $query = $this->db->query("SELECT medical_stores_review_comment.id,medical_stores_review_comment.post_id,medical_stores_review_comment.comment as comment,medical_stores_review_comment.date,users.name,medical_stores_review_comment.user_id as post_user_id FROM medical_stores_review_comment INNER JOIN users on users.id=medical_stores_review_comment.user_id WHERE medical_stores_review_comment.post_id='$post_id' order by medical_stores_review_comment.id asc");
            
            foreach ($query->result_array() as $row) {
                $id           = $row['id'];
                $post_id      = $row['post_id'];
                $comment      = $row['comment'];
                $comment = preg_replace('~[\r\n]+~', '', $comment);
                if(base64_encode(base64_decode($comment)) === $comment){
                    $comment=base64_decode($comment);
                }	
                $username     = $row['name'];
                $date         = $row['date'];
                $post_user_id = $row['post_user_id'];
                $like_count   = $this->db->select('id')->from('medical_stores_review_comment_like')->where('comment_id', $id)->get()->num_rows();
                $like_yes_no  = $this->db->select('id')->from('medical_stores_review_comment_like')->where('comment_id', $id)->where('user_id', $user_id)->get()->num_rows();
                $comment_date = get_time_difference_php($date);
                $img_count = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->num_rows();
                if ($img_count > 0) {
                    $profile_query = $this->db->select('media.source')->from('media')->join('users', 'users.avatar_id=media.id')->where('users.id', $post_user_id)->get()->row();
                    $img_file      = $profile_query->source;
                    $userimage     = 'https://medicalwale.com/healthwall_avatar/' . $img_file;
                } else {
                    $userimage = 'https://medicalwale.com/img/default_user.jpg';
                }
                $date         = get_time_difference_php($date);
                $resultpost[] = array(
                    'id' => $id,
                    'username' => $username,
                    'userimage' => $userimage,
                    'like_count' => $like_count,
                    'like_yes_no' => $like_yes_no,
                    'post_id' => $post_id,
                    'comment' => $comment,
                    'comment_date' => $comment_date
                );
            }
        } else {
            $resultpost = array();
        }
        return $resultpost;
    }
    
    
    
    public function pharmacy_view($listing_id,$user_id)
    {
        date_default_timezone_set('Asia/Kolkata');
        $date                = date('Y-m-d H:i:s');
        $pharmacy_view_array = array(
            'listing_id' => $listing_id,
            'user_id' => $user_id
        );
        $this->db->insert('pharmacy_view', $pharmacy_view_array);

        $pharmacy_view = $this->db->select('id')->from('pharmacy_view')->where('listing_id', $listing_id)->get()->num_rows();

        return array(
            'status' => 200,
            'message' => 'success',
            'pharmacy_view' => $pharmacy_view
        );
    }

}
