<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $name = $_POST['name'];
    $message = $_POST['message'];
    $mobile = $_POST['mobile'];
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');


    $sql = "SELECT id FROM `timelines` WHERE id='$user_id' limit 1";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);
    if ($count > 0) {
        $insert = mysqli_query($connection, "INSERT INTO `optic_store_contactus`(`user_id`, `name`, `message`, `mobile`, `date`) VALUES ('$user_id', '$name','$message','$mobile', '$date')");

        $status = '1';
        $msg = 'success';
        $result = array('status' => $status, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    } else {
        $status = '0';
        $msg = 'User not found';
        $result = array('status' => $status, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $result = array('status' => $status, 'msg' => $msg);
    echo json_encode($result);
    mysqli_close($connection);
}
?>