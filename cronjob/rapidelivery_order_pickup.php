<?php
require_once("../config.php");
require_once("function.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d H:i:s');
$sql = "SELECT user_order.order_id,vdhm.delivery_by_medicalwale,user_order.waybill,user_order.order_id,user_order.name,user_order.mobile,user_order.pincode,user_order.address1,address2,user_order.landmark,user_order.city,user_order.state FROM user_order INNER JOIN vendor_details_hm vdhm ON(vdhm.v_id=user_order.listing_id) WHERE vdhm.delivery_by_medicalwale=1 AND user_order.order_status='Order Confirmed'";
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if ($count_data > 0) {
    while ($list = mysqli_fetch_array($res)) {
        $name                       =   $list['name'];
        $mobileno                   =   $list['mobile'];
        $address                    =   $list['address1'].' '.$list['address2'].' '.$list['landmark'].' '.$list['city'].' '.$list['state'];
        $pincode                    =   $list['pincode'];
        $order_count                =   $count_data;
        $order_id                   =   $list['order_id'];
        
        $delivery_by_medicalwale    =   $list['delivery_by_medicalwale'];
        if($delivery_by_medicalwale==='1'){
            $rapidelivery_pickup_response = rapidelivery_pickup($name,$mobileno,$address,$pincode,$order_count);
            // echo '<pre>';
            // print_r($rapidelivery_pickup_response->Pickup[0]);
            if($rapidelivery_pickup_response->Pickup[0]->status==='Scheduled'){
                $pickup_no = $rapidelivery_pickup_response->Pickup[0]->pickup_no;
                mysqli_query($hconnection, "UPDATE user_order SET pickup_no = '$pickup_no' WHERE order_id = '$order_id'");
            }
		}
    }
}

function rapidelivery_pickup($name,$mobileno,$address,$pincode,$order_count){
    $url = "pickup.php";  
    
    $data=array(
                    // "token"        =>   "AD273C2E0AE503CC4F8324C",   // Live credientials
                    // "client"       =>   "medicalwale",               // Live credientials
                    "token"         =>   "test321",                     // sandbox testing credientials
                    "client"        =>   "test95",                      // sandbox testing credientials
                    "day"           =>   "0",
                    "address"       =>   $address,
                    "pincode"       =>   $pincode,
                    "name"          =>   $name,
                    "shipments"     =>   $order_count,
                    "weight"        =>   "",
                    "phone"         =>   $mobileno
                );
    
    // $data = json_encode($this);
    // print_r($data); die;
    $result=crul_rapid_delivery($url,$data);
    $results= json_decode($result);
    // print_r($results); //die;
    return $results;
    
}

function crul_rapid_delivery($u,$data){
    $u=$u;
    $url="trace.rapiddelivery.co/api/".$u;
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}  
?>