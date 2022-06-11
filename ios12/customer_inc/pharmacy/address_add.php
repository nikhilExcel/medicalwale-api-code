<?php

$result = array();
require_once ("../../config.php");
if (isset($_POST['user_id']) && isset($_POST['patient_name']) && isset($_POST['address1']) && isset($_POST['mobile'])) {
    $user_id = $_POST['user_id'];
    $patient_name = $_POST['patient_name'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $landmark = $_POST['landmark'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postcode = $_POST['pincode'];
    if ($user_id != '' && $address1 != '' && $mobile != '' && $city != '' && $state != '' && $postcode != '') {
        $sql = "SELECT id FROM `timelines` WHERE id='$user_id' limit 1";
        $res = mysqli_query($hconn, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $insert = mysqli_query($hconn, "INSERT INTO `oc_address`(`customer_id`, `firstname`, `lastname`, `email`, `telephone`, `company`, `address_1`, `address_2`, `landmark`, `city`, `state`, `postcode`, `country_id`, `zone_id`, `custom_field`) VALUES ('$user_id', '$patient_name', '', '', '$mobile', '', '$address1', '$address2', '$landmark', '$city', '$state', '$postcode', '0', '0', '0')");
            if ($insert) {
                $status = '1';
                $msg = 'success';
                $result = array('status' => $status, 'msg' => $msg);
                echo json_encode($result);
                mysqli_close($hconn);
            } else {
                $status = '0';
                $msg = 'failure';
                $result = array('status' => $status, 'msg' => $msg);
                echo json_encode($result);
                mysqli_close($hconn);
            }
        } else {
            $status = '0';
            $msg = 'User not found';
            $result = array('status' => $status, 'msg' => $msg);
            echo json_encode($result);
            mysqli_close($hconn);
        }
    } else {
        $status = '0';
        $msg = 'Values Blank';
        $result = array('status' => $status, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($hconn);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $result = array('status' => $status, 'msg' => $msg);
    echo json_encode($result);
    mysqli_close($hconn);
}
?>