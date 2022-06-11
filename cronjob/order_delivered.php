<?php
require_once("../config.php");
require_once("function.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d H:i:s');
$sql = "SELECT order_id,invoice_no,order_status,order_date,order_type,user_id,listing_name,listing_id,updated_at,delivery_time FROM user_order WHERE listing_type='13' and order_type='prescription' and order_status='Order Delivered' and feedback_reminder='0' ORDER BY order_id DESC";
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if ($count_data > 0) {
    while ($list = mysqli_fetch_array($res)) {
        $order_date = $list['updated_at'];
        $order_id = $list['order_id'];
        $invoice_no = $list['invoice_no'];
        $listing_id = $list['listing_id'];
        $listing_name = $list['listing_name'];
        $user_id = $list['user_id'];
        $order_type = $list['order_type'];
        $order_status = $list['order_status'];
        $delivery_time = $list['delivery_time'];
       // $listing_name ='Generico Generic Medicine Shop';
        $hourdiff = round((strtotime($current_date) - strtotime($order_date)) / 3600, 1);

		if ($hourdiff > 0.0833333 && $hourdiff <= 0.15) {
                $get_query = mysqli_query($hconnection, "SELECT id,token,agent,vendor_id FROM `users` WHERE id='$user_id' limit 1");
                $user_info = mysqli_fetch_array($get_query);
                $reg_id = $user_info['token'];
				//$reg_id ='fgPOOEVh7cA:APA91bFnULuE1Ls6KfPz0Lrid4JdmDfO0WznF_166KXEB1V_K4oXQ5HSinuUZa7KaIjYtKENtKGreS2rqNFNMeERuu4QCRzV1UNeYjMovWvDv0w-5-bk1XhuEA3THXw-xFPEL988dez6';
                $agent = $user_info['agent'];
                $name = $user_info['name'];
                $msg = "We hope you have a healthy recovery. Please let us know how did you like ".$listing_name."'s service";
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/img/logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Feedback';
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name, $agent);
                $update = mysqli_query($hconnection, "UPDATE `user_order` SET feedback_reminder='1' WHERE order_id='$order_id'");
        }
    }
}
?>
