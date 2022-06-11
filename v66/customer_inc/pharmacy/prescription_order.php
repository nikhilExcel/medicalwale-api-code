<?php

require_once("../../config.php");
$result = array();
if (isset($_POST['user_id']) && isset($_POST['listing_id']) && isset($_POST['address_id'])) {
    $user_id = $_POST['user_id'];
    $listing_id = $_POST['listing_id'];
    $listing_name = $_POST['listing_name'];
    $listing_type = $_POST['listing_type'];
    $address_id = $_POST['address_id'];
    $chat_id = $_POST['chat_id'];
    $order_status = "Awaiting Confirmation";
    $order_type = "prescription";

    date_default_timezone_set('Asia/Kolkata');
    $order_date = date('Y-m-d H:i:s');
    $invoice_no = date("YmdHis");

    if ($user_id != '' && $listing_id != '' && $address_id != '') {
        $addressQuery = mysqli_query($connection, "SELECT address_id,name,mobile,pincode,address1,address2,landmark,city,state FROM `user_address` WHERE user_id='$user_id' order by address_id desc");
        $row = mysqli_fetch_array($addressQuery);


        $name = $row['name'];
        $mobile = $row['mobile'];
        $address1 = $row['address1'];
        $address2 = $row['address2'];
        $landmark = $row['landmark'];
        $city = $row['city'];
        $state = $row['state'];
        $pincode = $row['pincode'];

        $insert1 = mysqli_query($connection, "INSERT INTO `user_order`(`order_type`, `listing_id`, `listing_name`, `listing_type`, `user_id`, `invoice_no`, `address_id`, `name`, `mobile`, `pincode`, `chat_id`, `address1`, `address2`, `landmark`, `city`, `state`, `order_total`, `delivery_charge`, `payment_method`, `order_date`, `order_status`) VALUES ('$order_type', '$listing_id', '$listing_name', '$listing_type', '$user_id', '$invoice_no', '$address_id', '$name', '$mobile', '$pincode', '$chat_id', '$address1', '$address2', '$landmark', '$city', '$state', '0', '0', '', '$order_date', '$order_status') ");
    }

    if ($insert1) {
        $order_id = mysqli_insert_id($connection);
        define('UPLOAD_DIR', '../../../public_html/prescription_images/');
        $prescription_image = '';
        $prescription_image1 = '';
        $prescription_image3 = '';
        $prescription_image4 = '';
        $file1 = '';
        $file2 = '';
        $file3 = '';
        $file4 = '';

        $prescription_image = $_POST['prescription_image'];
        $prescription_image1 = $_POST['prescription_image1'];
        $prescription_image3 = $_POST['prescription_image3'];
        $prescription_image4 = $_POST['prescription_image4'];

        if ($prescription_image != '') {
            $prescription_image = str_replace('data:image/jpeg;base64,', '', $prescription_image);
            $prescription_image = str_replace(' ', '+', $prescription_image);
            $data1 = base64_decode($prescription_image);
            $file1 = UPLOAD_DIR . uniqid() . '.jpg';
            $success1 = file_put_contents($file1, $data1);
            $file1 = str_replace('../../../public_html/prescription_images/', '', $file1);
        }

        if ($prescription_image1 != '') {
            $prescription_image1 = str_replace('data:image/jpeg;base64,', '', $prescription_image1);
            $prescription_image1 = str_replace(' ', '+', $prescription_image1);
            $data2 = base64_decode($prescription_image1);
            $file2 = UPLOAD_DIR . uniqid() . '.jpg';
            $success2 = file_put_contents($file2, $data2);
            $file2 = str_replace('../../../public_html/prescription_images/', '', $file2);
        }

        if ($prescription_image3 != '') {
            $prescription_image3 = str_replace('data:image/jpeg;base64,', '', $prescription_image3);
            $prescription_image3 = str_replace(' ', '+', $prescription_image3);
            $data3 = base64_decode($prescription_image3);
            $file3 = UPLOAD_DIR . uniqid() . '.jpg';
            $success3 = file_put_contents($file3, $data3);
            $file3 = str_replace('../../../public_html/prescription_images/', '', $file3);
        }
        if ($prescription_image4 != '') {
            $prescription_image4 = str_replace('data:image/jpeg;base64,', '', $prescription_image4);
            $prescription_image4 = str_replace(' ', '+', $prescription_image4);
            $data4 = base64_decode($prescription_image4);
            $file4 = UPLOAD_DIR . uniqid() . '.jpg';
            $success4 = file_put_contents($file4, $data4);
            $file4 = str_replace('../../../public_html/prescription_images/', '', $file4);
        }

        $insert2 = mysqli_query($connection, "INSERT INTO `prescription_order_details` (`order_id`, `prescription_image`, `prescription_image2`, `prescription_image3`, `prescription_image4`,`uni_id`) VALUES ('$order_id','$file1','$file2','$file3','$file4','$invoice_no')");
        date_default_timezone_set('Asia/Kolkata');


        $true_false = 'true';
        $msg = 'Order Placed Successfully';
        array_push($result, array('true_false' => $true_false, 'id' => $order_id, 'msg' => $msg, 'order_date' => $order_date));
        echo json_encode($result);

        function send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name) {
            define("GOOGLE_GCM_URL", "https://fcm.googleapis.com/fcm/send");
            $fields = array(
                'to' => $reg_id,
                'priority' => "high",
                'data' => array("title" => $title, "message" => $msg, "image" => $img_url, "tag" => $tag, "notification_type" => "prescription", "order_status" => $order_status, "order_date" => $order_date, "order_id" => $order_id, "invoice_no" => $invoice_no, "name" => $name, "listing_name" => $listing_name)
            );
            if ($key_count == '1') {
                $headers = array(
                    GOOGLE_GCM_URL,
                    'Content-Type: application/json',
                    'Authorization: key=AIzaSyBQDOIo8uwvF9ppdPK-mM-1bVGJKs70jk4'
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
        }

        if ($insert2) {
            $order_date = date('j M Y h:i A', strtotime($order_date));
            $res_token = mysqli_query($connection, "select token,token_status from users where id='$user_id' limit 1");
            $token_value = mysqli_fetch_array($res_token);
            $token_status = $token_value['token_status'];
            if ($token_status > 0) {
                $reg_id = $token_value['token'];
                $msg = 'Thanks uploading your prescription with ' . $listing_name;
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
                $tag = 'text';
                $key_count = '1';
                $title = 'Order Placed';
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name);
            }

            $partner_token = mysqli_query($connection, "select token,token_status from users where id='$listing_id' limit 1");
            $partner_token_value = mysqli_fetch_array($partner_token);
            $partner_token_status = $partner_token_value['token_status'];
            if ($partner_token_status > 0) {
                $reg_id = $partner_token_value['token'];
                $msg = 'You Have Received a New Prescription Order';
                $img_url = 'https://d2c8oti4is0ms3.cloudfront.net/images/1/images/logo.png';
                $tag = 'text';
                $key_count = '2';
                $title = 'New Order';
                send_gcm_notify($title, $reg_id, $msg, $img_url, $tag, $key_count, $order_status, $order_date, $order_id, $invoice_no, $name, $listing_name);
            }
        }
    } else {
        $error_msg = 'Order failed';
        $true_false = 'false';
        array_push($result, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result);
    }
    mysqli_close($connection);
} else {
    $error_msg = 'Please enter all fields!';
    $true_false = 'false';
    array_push($result, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result);
    mysqli_close($connection);
}
?>