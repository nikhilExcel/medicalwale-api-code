<?php

$result = array();
$ratng = 4.5;
require_once ("../../config.php");
if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    if ($user_id != '') {
        $sql = "SELECT id FROM `insurance_policy_view` WHERE user_id='$user_id' limit 1";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $status = '1';
            $msg = 'success';
            $sql2 = "SELECT id FROM `insurance_policy_view`";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);
            $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count, 'ratng' => $ratng);
            echo json_encode($result);
            mysqli_close($connection);
        } else {
            $insert2 = mysqli_query($connection, "INSERT INTO `insurance_policy_view`(`user_id`) VALUES ('$user_id')");
            $status = '1';
            $msg = 'success';
            $sql2 = "SELECT id FROM `insurance_policy_view`";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);
            $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count, 'ratng' => $ratng);
            echo json_encode($result);
            mysqli_close($connection);
        }
    } else {
        $status = '0';
        $msg = 'Post Method Error!';
        $sql2 = "SELECT id FROM `insurance_policy_view`";
        $res2 = mysqli_query($connection, $sql2);
        $total_count = mysqli_num_rows($res2);
        $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count, 'ratng' => $ratng);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $sql2 = "SELECT id FROM `insurance_policy_view`";
    $res2 = mysqli_query($connection, $sql2);
    $total_count = mysqli_num_rows($res2);
    $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count, 'ratng' => $ratng);
    echo json_encode($result);
    mysqli_close($connection);
}
?>