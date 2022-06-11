<?php
require_once("config.php");
if(isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $mlat     = $_POST['lat'];
    $mlng     = $_POST['lng'];
    $radius  = '5';
    
    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist  = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist  = acos($dist);
        $dist  = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit  = strtoupper($unit);
        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
    
    $sql = sprintf("SELECT user_id as id, medical_name, lat, lng, address1, address2, pincode, city, state, contact_no, whatsapp_no, email, profile_pic, license_registration, unique_id, about_us, website, day_night_delivery, delivery_till, delivery_time, days_closed, reach_area, url,online_offline,payment_type,store_open,store_close,min_order,min_order_delivery_charge,night_delivery_charge,min_night_delivery_charge,store_since,free_start_time,free_end_time,night_start_time,night_end_time, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' HAVING distance < '%s' ORDER BY distance LIMIT 0 , 20", ($mlat), ($mlng), ($mlat), ($radius));
    
    $res = mysqli_query($connection, $sql);
    while($row = mysqli_fetch_array($res))
	{
        $id             = $row['id'];
        $lat            = $row['lat'];
        $lng            = $row['lng'];
        $medical_id     = $row['id'];
        $medical_name   = $row['medical_name'];
        $address1       = $row['address1'];
        $address2       = $row['address2'];
        $pincode        = $row['pincode'];
        $city           = $row['city'];
        $state          = $row['state'];
        $contact_no     = $row['contact_no'];
        $whatsapp_no    = $row['whatsapp_no'];
        $email          = $row['email'];
        $website        = $row['website'];
        $delivery_till  = '';
        $delivery_time  = '';
        $reach_area     = $row['reach_area'];
        $rating         = '4.5';
        $online_offline = $row['online_offline'];
        $km             = $row['distance'];
        $profile_pic    = $row['profile_pic'];
        if ($row['profile_pic'] != '') {
            $profile_pic = $row['profile_pic'];
            $profile_pic = str_replace(' ', '%20', $profile_pic);
            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/images/' . $profile_pic;
        } else {
            $profile_pic = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/default_medical_app.jpg';
        }
        
        $day_night_delivery        = $row['day_night_delivery'];
        $days_closed               = $row['days_closed'];
        $payment_type              = $row['payment_type'];
        $store_open                = '10 : 00 AM';
        $store_close               = '10 : 00 PM';
        $min_order                 = '300';
        $min_order_delivery_charge = '60';
        $night_delivery_charge     = '500';
        $min_night_delivery_charge = '100';
        $store_since               = '2016';
        $free_start_time           = '10 : 00 AM';
        $free_end_time             = '09 : 00 PM';
        $night_start_time          = '09 : 00 PM';
        $night_end_time            = '11 : 00 PM';
        $followers                 = '0';
        $following                 = '0';
        $profile_view              = '0';
        $activity_id               = '0';
        $is_follow                 = 'No';
        $chat_id                   = 'ambikamedical';
        $chat_display              = 'Ambika Medical Store';
        $is_chat                   = 'Yes';
        $review                    = '0';
        
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
        
        $distance = distance($mlat, $mlng, $lat, $lng, "K");
		$current_day='';
		$opening_day='';
		$current_day = array('day' => 'Open','time'=>'09:00am - 07:00pm');
		$days_list="Sunday Closed,Monday Closed,Tuesday Closed,Wednesday Closed,Thursday Closed,Friday Closed,Saturday Closed";
		$days_list_array  = explode(",", $days_list);
		$cnt              = count($days_list_array);
		
		for ($i = 0; $i < $cnt; $i++) {
		$opening_day[] = array('day' => str_replace(' Closed','',$days_list_array[$i]),'time'=>'09:00am - 07:00pm');
		}		
        
        $resultpost[] = array(
            'id' => $medical_id,
            'medical_name' => $medical_name,
            'address1' => $address1,
            'address2' => $address2,
            'pincode' => $pincode,
            'city' => $city,
            'state' => $state,
            'contact_no' => $contact_no,
            'whatsapp_no' => $whatsapp_no,
            'email' => $email,
            'website' => $website,
            'delivery_till' => $delivery_till,
            'delivery_time' => $delivery_time,
            'reach_area' => $ranges,
            'store_distance' => $store_distance,
            'distance' => $distance,
            'profile_pic' => $profile_pic,
            'rating' => $rating,
            'online_offline' => $online_offline,
            'distance' => $meter,
            'day_night_delivery' => $day_night_delivery,
            'days_closed' => $days_closed,
            'payment_type' => $payment_type,
            'store_open' => $store_open,
            'store_close' => $store_close,
            'min_order' => $min_order,
            'min_order_delivery_charge' => $min_order_delivery_charge,
            'night_delivery_charge' => $night_delivery_charge,
            'min_night_delivery_charge' => $min_night_delivery_charge,
            'store_since' => $store_since,
            'free_start_time' => $free_start_time,
            'free_end_time' => $free_end_time,
            'night_start_time' => $night_start_time,
            'night_end_time' => $night_end_time,
            'followers' => $followers,
            'following' => $following,
            'profile_view' => $profile_view,
            'activity_id' => $activity_id,
            'is_follow' => $is_follow,
            'chat_id' => $chat_id,
            'chat_display' => $chat_display,
            'is_chat' => $is_chat,
            'review' => $review,
			'current_day' => $current_day,
			'opening_day' => $opening_day
        );
    }
	$resultset      = array(
        'status' => '200',
        'message' => 'success',
        'count' => sizeof($resultpost),
        'data' => $resultpost
    );
	echo json_encode($resultset);
} else {
    $resultpost[] = array();
    $resultset      = array(
        'status' => '200',
        'message' => 'success',
        'count' => '0',
        'data' => $resultpost
    );
    echo json_encode($resultset);
    mysqli_close($connection);
}
?>