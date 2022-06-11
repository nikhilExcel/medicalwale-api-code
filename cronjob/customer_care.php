<?php
require_once("../config.php");
date_default_timezone_set('Asia/Calcutta');
$current_date = date('Y-m-d H:i:s');  

//for pharmacy data 
$sql = "select * from user_order where order_status = 'Payment Pending' or order_status = 'Awaiting Confirmation' or order_status = 'Payment Pending'";
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);

if($count_data>0)
{
    while ($list = mysqli_fetch_array($res)) {
        
        $user_id = $list['user_id'];
        $listing_id = $list['listing_id'];
        $vendor_id = $list['listing_type'];
        $invoice_no = $list['invoice_no'];
        $order_id = $list['order_id'];
        $order_status = $list['order_status'];
        
    //   $customercare_notification = array(
    //           'user_id' => $user_id,
    //           'listing_id' => $listing_id,
    //           'vendor_type' => $vendor_id,
    //           'invoice_no' => $invoice_no,
    //           'order_id' => $order_id,
    //           'status'   => $order_status,
    //       );
          
          
        $sql_insert = "INSERT INTO `customercare_notification`(`user_id`, `listing_id`, `vendor_type`, `order_id`, `invoice_number`, `status`) VALUES ('$user_id','$listing_id','$vendor_id','$invoice_no','$order_id','$order_status')";
          $insert_res = mysqli_query($hconnection, $sql_insert);
        
    }
}

//for booking master 
$sql = "SELECT * FROM `booking_master` WHERE `status`='Pending' or status='Payment Pending' or status='1' or status='pending'";
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if($count_data>0)
{
    while ($list = mysqli_fetch_array($res)) {
        
        $user_id = $list['user_id'];
        $listing_id = $list['listing_id'];
        $vendor_id = $list['vendor_id'];
        $invoice_no = $list['booking_id'];
        $order_id = $list['id'];
        $order_status = $list['status'];
        
    //   $customercare_notification = array(
    //           'user_id' => $user_id,
    //           'listing_id' => $listing_id,
    //           'vendor_type' => $vendor_id,
    //           'invoice_no' => $invoice_no,
    //           'order_id' => $order_id,
    //           'status'   => $order_status,
    //       );
          
          
        $sql_insert = "INSERT INTO `customercare_notification`(`user_id`, `listing_id`, `vendor_type`, `order_id`, `invoice_number`, `status`) VALUES ('$user_id','$listing_id','$vendor_id','$invoice_no','$order_id','$order_status')";
          $insert_res = mysqli_query($hconnection, $sql_insert);
        
    }
}

//for doctor booking master
$sql = "SELECT * FROM `doctor_booking_master` WHERE `status`='1' or status='Awaiting Confirmation' or status='awaiting confirmation' or status='Payment Pending'";
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if($count_data>0)
{
    while ($list = mysqli_fetch_array($res)) {
        
        $user_id = $list['user_id'];
        $listing_id = $list['listing_id'];
        $vendor_id = '5';
        $invoice_no = $list['booking_id'];
        $order_id = $list['id'];
        $order_status = $list['status'];
        
    //   $customercare_notification = array(
    //           'user_id' => $user_id,
    //           'listing_id' => $listing_id,
    //           'vendor_type' => $vendor_id,
    //           'invoice_no' => $invoice_no,
    //           'order_id' => $order_id,
    //           'status'   => $order_status,
    //       );
          
          
        $sql_insert = "INSERT INTO `customercare_notification`(`user_id`, `listing_id`, `vendor_type`, `order_id`, `invoice_number`, `status`) VALUES ('$user_id','$listing_id','$vendor_id','$invoice_no','$order_id','$order_status')";
          $insert_res = mysqli_query($hconnection, $sql_insert);
        
    }
}


//for hospital booking 
$sql = "SELECT * FROM `hospital_booking_master` WHERE `status`='1' or status='Awaiting Confirmation' or status='awaiting confirmation' or status='Payment Pending'";
$res = mysqli_query($hconnection, $sql);
$count_data = mysqli_num_rows($res);
if($count_data>0)
{
    while ($list = mysqli_fetch_array($res)) {
        
        $user_id = $list['user_id'];
        $listing_id = $list['listing_id'];
        $vendor_id = '8';
        $invoice_no = $list['booking_id'];
        $order_id = $list['id'];
        $order_status = $list['status'];
        
    //   $customercare_notification = array(
    //           'user_id' => $user_id,
    //           'listing_id' => $listing_id,
    //           'vendor_type' => $vendor_id,
    //           'invoice_no' => $invoice_no,
    //           'order_id' => $order_id,
    //           'status'   => $order_status,
    //       );
          
          
        $sql_insert = "INSERT INTO `customercare_notification`(`user_id`, `listing_id`, `vendor_type`, `order_id`, `invoice_number`, `status`) VALUES ('$user_id','$listing_id','$vendor_id','$invoice_no','$order_id','$order_status')";
          $insert_res = mysqli_query($hconnection, $sql_insert);
        
    }
}



?>