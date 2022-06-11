<?php

require_once ("../../config.php");
$result = array();

if (isset($_POST['address_id']) && isset($_POST['user_id'])) {
    $address_id = addslashes($_POST['address_id']);
    $user_id = addslashes($_POST['user_id']);

    $delete = mysqli_query($connection, "DELETE FROM `oc_address` WHERE address_id='$address_id' AND customer_id='$user_id'");

    if ($delete) {
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
    $msg = 'Post Method Error!';
    $result = array('status' => $status, 'msg' => $msg);
    echo json_encode($result);
    mysqli_close($hconn);
}
?>