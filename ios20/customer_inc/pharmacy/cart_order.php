<?php

require_once("config.php");
$result = array();
if (isset($_POST['user_id']) && isset($_POST['listing_id']) && isset($_POST['listing_name']) && isset($_POST['listing_type']) && isset($_POST['address_id']) && isset($_POST['payment_method']) && isset($_POST['product_id']) && isset($_POST['product_price']) && isset($_POST['product_quantity'])) {
    $user_id = $_POST['user_id'];
    $listing_id = $_POST['listing_id'];
    $listing_name = $_POST['listing_name'];
    $listing_type = $_POST['listing_type'];
    $address_id = $_POST['address_id'];
    $payment_method = $_POST['payment_method'];
    $product_id = $_POST['product_id'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];
    $delivery_charge = $_POST['delivery_charge'];
    $chat_id = $_POST['chat_id'];
    $action_by = 'customer';

    date_default_timezone_set('Asia/Kolkata');
    $order_date = date('Y-m-d H:i:s');
    $invoice_no = date("YmdHis");
    $order_status = 'Awaiting Confirmation';
    $order_total = '0';

    $product_id = explode(",", $_POST['product_id']);
    $product_quantity = explode(",", $_POST['product_quantity']);
    $product_price = explode(",", $_POST['product_price']);
    $product_name = explode(",", $_POST['product_name']);
    $product_img = explode(",", $_POST['product_img']);
    $product_unit = explode(",", $_POST['product_unit']);
    $product_unit_value = explode(",", $_POST['product_unit_value']);
    $cnt = count($product_id);
    for ($i = 0; $i < $cnt; $i++) {
        $order_total = $order_total + ($product_price[$i] * $product_quantity[$i]);
    }

    if ($user_id != '' && $address_id != '' && $listing_id != '') {
        $address_query = mysqli_query($connection, "SELECT name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' and address_id='$address_id' limit 1");
        $get_address = mysqli_fetch_array($address_query);
        $name = $get_address['name'];
        $mobile = $get_address['mobile'];
        $address1 = $get_address['address1'];
        $address2 = $get_address['address2'];
        $landmark = $get_address['landmark'];
        $city = $get_address['city'];
        $state = $get_address['state'];
        $pincode = $get_address['pincode'];

        $cart_order = mysqli_query($connection, "INSERT INTO `user_order`(`order_type`, `listing_id`, `listing_name`, `listing_type`, `user_id`, `invoice_no`, `address_id`, `name`, `mobile`, `pincode`, `chat_id`, `address1`, `address2`, `landmark`, `city`, `state`, `order_total`, `delivery_charge`, `delivery_time`, `payment_method`, `order_date`, `order_status`, `cancel_reason`, `action_by`) VALUES ('order', '$listing_id', '$listing_name', '$listing_type', '$user_id', '$invoice_no', '$address_id', '$name', '$mobile', '$pincode', '$chat_id', '$address1', '$address2', '$landmark', '$city', '$state', '$order_total', '$delivery_charge', '', '$payment_method', '$order_date', '$order_status', '$cancel_reason', '$action_by')");
    }
    $order_id = mysqli_insert_id($connection);
    $sub_total = '0';
    $product_status = '';
    $product_status_type = '';
    $product_status_value = '';
    $order_status = 'Awaiting Confirmation';
    for ($i = 0; $i < $cnt; $i++) {
        $sub_total = $product_price[$i] * $product_quantity[$i];

        $cart_product_order = mysqli_query($connection, "INSERT INTO `user_order_product`(`order_id`,`product_name`,`product_img`, `product_id`, `product_quantity`, `product_price`, `sub_total`, `product_status`, `product_status_type`,`product_unit`,`product_unit_value`, `product_status_value`, `order_status`) VALUES ('$order_id','$product_name[$i]','$product_img[$i]', '$product_id[$i]', '$product_quantity[$i]', '$product_price[$i]', '$sub_total', '$product_status', '$product_status_type','$product_unit[$i]','$product_unit_value[$i]', '$product_status_value', '$order_status')");
    }

    $legacy_server_key = '';
    $order_date = date('j M Y h:i A', strtotime($order_date));

    function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name) {
        define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
        $fields = array(
            'to' => $reg_id,
            'priority' => "high",
            'data' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_type" => "order", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
        );
        if ($key_count == '1') {
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                'Authorization: key=AIzaSyDTr3BzDHKj_6FnUiZXT__VCmamH-_gupM'
            );
        }
        if ($key_count == '2') {
            $headers = array(
                GOOGLE_GCM_URL,
                'Content-Type: application/json',
                'Authorization: key=AIzaSyCQLqGPoUzIKmh3qZHLFUHFnfzLTXsp2C4'
            );
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, GOOGLE_GCM_URL);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
        $result = curl_exec($ch);
        if ($result === FALSE) {
            die('Problem occurred: ' . curl_error($ch));
        }
        curl_close($ch);
        //echo $result;
    }

    if ($cart_order) {
        $order_date = date('j M Y h:i A', strtotime($order_date));
        $res_token = mysqli_query($connection, "select token,token_status from users where id='$user_id' limit 1");
        $token_value = mysqli_fetch_array($res_token);
        $token_status = $token_value['token_status'];
        if ($token_status > 0) {
            $reg_id = $token_value['token'];
            $msg = 'Thanks for placing your order with ' . $listing_name;
            $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
            $tag = 'text';
            $key_count = '1';
            $title = 'Order Placed';
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name);
        }
        $legacy_server_key = '';
        $partner_token = mysqli_query($connection, "select token,token_status from users where id='$listing_id' limit 1");
        $partner_token_value = mysqli_fetch_array($partner_token);
        $partner_token_status = $partner_token_value['token_status'];
        if ($partner_token_status > 0) {
            $reg_id = $partner_token_value['token'];
            $msg = 'You Have Received a New General Order';
            $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
            $tag = 'text';
            $key_count = '2';
            $title = 'New Order';
            send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name);
        }
        echo json_encode(array("status" => 201, "message" => "success", "order_id" => $invoice_no));
    } else {
        $invoice_no = '0';
        echo json_encode(array("status" => 201, "message" => "failed", "order_id" => $invoice_no));
    }
} else {
    $invoice_no = '0';
    echo json_encode(array("status" => 201, "message" => "failed", "order_id" => $invoice_no));
    mysqli_close($connection);
}
?>