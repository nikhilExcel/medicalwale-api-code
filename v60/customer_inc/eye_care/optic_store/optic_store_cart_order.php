<?php

include_once('../../../config.php');
$result = array();
if (isset($_POST['user_id']) && isset($_POST['address_id'])) {
    $user_id = $_POST['user_id'];
    $address_id = $_POST['address_id'];
//$medical_id= $_POST['medical_id'];
//$payType= $_POST['payType'];

    $status = "Pending";
    $product_status = 'Pending';
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d h:i:s');

    $b = date("Y");
    $c = date("m");
    $d = date("d");
    $e = date("H");
    $f = date("i");
    $g = date("s");
    $uni_id = $b . $c . $d . $e . $f . $g;

    $discount = '0';
    $grand_total = '0';
    $final_total = '0';
    $discount_rate = '0';

//$medical_id= $_POST['medical_id'];
    $product_id = explode(",", $_POST['product_id']);
    $product_quantity = explode(",", $_POST['product_quantity']);
    $product_price = explode(",", $_POST['product_price']);
    $cnt = count($product_id);
    for ($i = 0; $i < $cnt; $i++) {
        $final_total = $final_total + ($product_price[$i] * $product_quantity[$i]);
    }

    $grand_total = $final_total;

    if ($user_id != '' && $address_id != '') {
        $insert1 = mysqli_query($connection, "INSERT INTO `optic_store_cart_order`(`user_id`, `address_id`, `uni_id`, `date`, `status`, `store_status`, `customer_status`, `total`, `discount`,`payType`) VALUES ('$user_id', '$address_id', '$uni_id', '$date', 'pending', '0', '0', '$grand_total', '$discount','0')");
    }
    $order_id = mysqli_insert_id($connection);
    $medical_id = $_POST['medical_id'];
    $product_id = explode(",", $_POST['product_id']);
    $product_quantity = explode(",", $_POST['product_quantity']);
    $product_price = explode(",", $_POST['product_price']);
    $cnt = count($product_id);
    for ($i = 0; $i < $cnt; $i++) {
        $sub_total = $product_price[$i] * $product_quantity[$i];

        $insert2 = mysqli_query($connection, "INSERT INTO `optic_store_cart_order_products`(`order_id`,`product_id`, `product_quantity`, `product_price`, `sub_total`, `product_status`, `product_status_type`, `product_status_value`, `uni_id`) VALUES ('$order_id', '$product_id[$i]', '$product_quantity[$i]', '$product_price[$i]', '$sub_total', 'pending', '', '', '$uni_id')");
    }

    if ($insert1) {
        date_default_timezone_set('Asia/Kolkata');
        $order_type = 'order';
        $noti_message = 'Thanks for placing your order with Medicalwale';
        $date_time = date('Y-m-d H:i:s');

//$insert_notification= mysqli_query($connection,"INSERT INTO `notification`(`user_id`, `order_id`, `order_type`, `message`, `status`, `date`) VALUES ('$user_id', '$order_id', '$order_type', '$noti_message', '0', '$date_time')");	
//$store_notification= mysqli_query($connection,"INSERT INTO `store_notification`(`medical_id`, `order_id`, `order_type`, `message`, `status`, `date`) VALUES ('$medical_id', '$order_id', '$order_type', 'New Order Has Been Placed To Your Store', '0', '$date_time')");


        array_push($result, array('true_false' => $true_false, 'id' => $order_id, 'msg' => $msg, 'order_date' => $date));
        $status = '1';
        $msg = 'Order Placed Successfully';
        $result = array('status' => $status, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    } else {
        $status = '0';
        $msg = 'Error occured while ordering!';
        $result = array('status' => $status, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Please enter all fields!';
    $result = array('status' => $status, 'msg' => $msg);
    echo json_encode($result);
    mysqli_close($connection);
}
?>