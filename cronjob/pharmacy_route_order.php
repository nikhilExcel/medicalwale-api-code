<?php
require_once("../config.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d H:i:s');  
define("POST_URL", "https://newlive.medicalwale.com/v29/order/order_route");
$headers = array(
    'Client-Service:frontend-client',
    'Auth-Key:medicalwalerestapi',
    'Content-Type:application/json',
    'User-ID:1',
    'Authorizations:25iwFyq/LSO1U'
);
$sql = "SELECT order_id,invoice_no,order_status,order_date,order_type,user_id,listing_id,listing_type,listing_name,address_id,payment_method,delivery_charge,chat_id,is_night_delivery,lat,lng FROM user_order WHERE order_id IN (SELECT MAX(order_id) FROM user_order GROUP BY invoice_no HAVING COUNT(*) <=3) and listing_type='13' and order_type='order' and order_status='Awaiting Confirmation' ORDER BY order_id DESC"; 
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if($count_data>0)
{
while ($list = mysqli_fetch_array($res)) {
    $order_date = $list['order_date'];  
    $order_id           = $list['order_id'];
    $invoice_no           = $list['invoice_no'];
	$mlat 		= $list['lat'];
	$mlng 		= $list['lng'];
	$current_listing_id = $list['listing_id'];
	$current_listing_name = $list['listing_name'];
    $hourdiff = round((strtotime($current_date) - strtotime($order_date)) / 3600, 1);
    if($hourdiff >= 2 && $order_id>2057){
        $radius = '5';
        $route_sql = sprintf("SELECT user_id,medical_name, ( 6371 * acos( cos( radians('%s') ) * cos( radians( lat ) ) * cos( radians( lng ) - radians('%s') ) + sin( radians('%s') ) * sin( radians( lat ) ) ) ) AS distance FROM medical_stores  WHERE is_approval='1' AND is_active='1' and user_id<>'$current_listing_id' HAVING distance < '%s' ORDER BY distance LIMIT 1", ($mlat), ($mlng), ($mlat), ($radius));
		$route_res     = mysqli_query($hconnection, $route_sql);
		$route_list = mysqli_fetch_array($route_res);
		$listing_id 		= $route_list['user_id'];
		if($listing_id!=$current_listing_id){
		$listing_name 		= $route_list['medical_name'];		
        $order_id           = $list['order_id'];
        $order_type         = $list['order_type'];
        $user_id            = $list['user_id'];
        $listing_type       = $list['listing_type'];
        $address_id         = $list['address_id'];
        $payment_method     = $list['payment_method'];
        $delivery_charge    = $list['delivery_charge'];
        $chat_id            = $list['chat_id'];
        $is_night_delivery  = $list['is_night_delivery'];
		
        $product_sql     = "SELECT product_id,product_quantity,product_price,product_name,product_img,product_unit,product_unit_value FROM user_order_product where order_id='$order_id'";
		$product_res     = mysqli_query($hconnection, $product_sql);
		while ($product_list = mysqli_fetch_array($product_res)) {
        $product_id         .= $product_list['product_id'].',';
        $product_quantity   .= $product_list['product_quantity'].',';
        $product_price      .= $product_list['product_price'].',';
        $product_name       .= $product_list['product_name'].',';
        $product_img        .= $product_list['product_img'].',';
        $product_unit       .= $product_list['product_unit'].',';
        $product_unit_value .= $product_list['product_unit_value'].',';
		}
        
        $fields = array(
            'invoice_no' => $invoice_no,
            'current_listing_id' => $current_listing_id,
            'current_listing_name' => $current_listing_name,
            'order_id' => $order_id,
            'user_id' => $user_id,
            'listing_id' => $listing_id,
            'listing_type' => $listing_type,
            'listing_name' => $listing_name,
            'address_id' => $address_id,
            'payment_method' => $payment_method,
            'product_id' => rtrim($product_id,','),
            'product_price' => rtrim($product_price,','),
            'product_quantity' => rtrim($product_quantity,','),
            'product_name' => rtrim($product_name,','),
            'product_img' => rtrim($product_img,','),			
            'product_unit' => rtrim($product_unit,','),
            'product_unit_value' => rtrim($product_unit_value,','),
            'delivery_charge' => $delivery_charge,
            'chat_id' => $chat_id,
			'lat' => $mlat,
            'lng' => $mlng,
            'is_night_delivery' => $is_night_delivery,
            'schedule_date' => '',
            'device_type' => '',
            'cancel_status' => '',
            'thyrocare_cancel_status' => '',
            'lead_id' => '',
            'reference_id' => ''
        );
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, POST_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        curl_close($ch);
        echo json_encode($fields);
		
		}
    }
}
}
?>