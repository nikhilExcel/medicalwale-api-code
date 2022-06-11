<?php

$result = array();

require_once ("../../config.php");
if (isset($_POST['user_id'])) {
    $status = '200';
    $msg = 'success';
    $user_id = $_POST['user_id'];
    if ($user_id != '') {
        $sql = "SELECT id FROM `insurance_policy_view` order by id desc";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        $result = array('status' => $status, 'msg' => $msg, 'view' => $count);
        echo json_encode($result);
        mysqli_close($connection);
    } else {
        $status = '201';
        $msg = 'Post Method Error!';
        $sql2 = "SELECT id FROM `insurance_policy_view`";
        $res2 = mysqli_query($connection, $sql2);
        $total_count = mysqli_num_rows($res2);
        $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count, 'ratng' => $ratng);
        echo json_encode($result);
        mysqli_close($connection);
    }
}