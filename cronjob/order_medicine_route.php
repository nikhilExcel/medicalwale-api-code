<?php
/*require_once("../config.php");
require_once("function.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d H:i:s');
$sql = "SELECT lat,lng,order_id,invoice_no,order_status,order_date,order_type,user_id,listing_id FROM user_order WHERE listing_type='13' and order_type='prescription' and order_status='Awaiting Confirmation' and is_routed='0' and stop_routing='0' ORDER BY order_id DESC";
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);

  function GetDrivingDistance($lat1, $lat2, $long1, $long2) {
            $url = "https://maps.googleapis.com/maps/api/distancematrix/json?origins=" . $lat1 . "," . $long1 . "&destinations=" . $lat2 . "," . $long2 . "&mode=walking&language=pl-PL&key=AIzaSyDg9YiklFpUhqkDXlwzuC9Cnip4U-78N60&callback=initMap";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
            $response_a = json_decode($response, true);
            $dist = $response_a['rows'][0]['elements'][0]['distance']['value'];
            return $dist;
        }
        
if ($count_data > 0) {
  
    while ($list = mysqli_fetch_array($res)) {
        $distance_array = array();
        $final_id ="";
        $mlat = $list['lat'];
        $mlng = $list['lng'];
        $order_date = $list['order_date'];
        $order_id = $list['order_id'];
        $invoice_no = $list['invoice_no'];
        $listing_id = $list['listing_id'];
        $user_id = $list['user_id'];
        $order_type = $list['order_type'];
        $order_status = $list['order_status'];
        $listing_name ='Generico Generic Medicine Shop';
        $hourdiff = round((strtotime($current_date) - strtotime($order_date)) / 3600, 1);
        if ($hourdiff >= 1.0 && $order_id > 0) {
            $array = explode(',', $listing_id);
            foreach ($array as $array_listing_id) {
                $get_query = mysqli_query($hconnection, "SELECT id,token,agent,vendor_id,name FROM `users` WHERE id='$array_listing_id' limit 1");
                $user_info = mysqli_fetch_array($get_query);
                $reg_id = $user_info['token'];
			    $agent = $user_info['agent'];
                $name = $user_info['name'];
                $msg = 'Your order is routed to '.$listing_name;
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Routed';
				$insert= mysqli_query($hconnection,"INSERT INTO `pharmacy_notifications`(`listing_id`, `order_id`, `title`, `msg`, `image`, `notification_type`, `order_status`, `invoice_no`, `order_date`) VALUES ('$array_listing_id','$order_id','$title','$msg','$image','prescription','$order_status','$invoice_no','$order_date')");
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
            }
                $update_pre = mysqli_query($hconnection, "UPDATE `user_order` SET copy_listing_id='$listing_id' WHERE order_id='$order_id'");
                if($mlat==0  || $mlng == 0) {
                    $update = mysqli_query($hconnection, "UPDATE `user_order` SET listing_id='33654',is_routed='1',updated_at='$current_date' WHERE order_id='$order_id'");
                }
                else if($mlat!=0  && $mlng != 0) 
                {
                    $get_query1 = mysqli_query($hconnection, "SELECT pharmacy_branch_user_id,lat,lng FROM `medical_stores` WHERE user_id='33569'");
                    
                    while ($user_info1 = mysqli_fetch_array($get_query1)) {
                        $lat = $user_info1['lat'];
                        $lng = $user_info1['lng'];
                        $branch_id = $user_info1['pharmacy_branch_user_id'];
                        $distances = str_replace(',', '.', GetDrivingDistance($mlat, $lat, $mlng, $lng));
                        $distance_array[] = array('branch_id'=>$branch_id,'distance'=>$distances);
                    }
                }
                if(!empty($distance_array)) {
                    usort($distance_array, function($a, $b) {
                        return $a['distance'] - $b['distance'];
                    });
                   
                    if($distance_array[0]['branch_id'] == 0) {
                        $final_id = $distance_array[1]['branch_id'];
                    }
                    else
                    {
                        $final_id = $distance_array[0]['branch_id'];
                    }
                }
                if($final_id =="")
                {
                    $update = mysqli_query($hconnection, "UPDATE `user_order` SET listing_id='33654',is_routed='1',updated_at='$current_date' WHERE order_id='$order_id'");
                }
                else
                {
                    $update = mysqli_query($hconnection, "UPDATE `user_order` SET listing_id='$final_id',is_routed='1',updated_at='$current_date' WHERE order_id='$order_id'");
                }
            
		    	$get_query = mysqli_query($hconnection, "SELECT id,token,agent,vendor_id FROM `users` WHERE id='$user_id' limit 1");
                $user_info = mysqli_fetch_array($get_query);
                $reg_id = $user_info['token'];
				//$reg_id ='fgPOOEVh7cA:APA91bFnULuE1Ls6KfPz0Lrid4JdmDfO0WznF_166KXEB1V_K4oXQ5HSinuUZa7KaIjYtKENtKGreS2rqNFNMeERuu4QCRzV1UNeYjMovWvDv0w-5-bk1XhuEA3THXw-xFPEL988dez6';
                $agent = $user_info['agent'];
                $name = $user_info['name'];
                $msg = 'Your order no.' . $invoice_no . ' has been routed to Generico Generic Medicine Shop';
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Routed';
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
        }
    }
}*/
?>