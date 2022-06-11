<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id']) && isset($_POST['product_id'])) {
    $user_id = $_POST['user_id'];
    $product_id = $_POST['product_id'];
    if ($user_id != '' && $product_id != '') {
        $sql = "SELECT id FROM `optic_store_product_view` WHERE user_id='$user_id' and product_id='$product_id' limit 1";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $status = '1';
            $msg = 'success';
            $sql2 = "SELECT id FROM `optic_store_product_view` where product_id='$product_id'";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);
            $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count);
            echo json_encode($result);
            mysqli_close($connection);
        } else {
            $insert2 = mysqli_query($connection, "INSERT INTO `optic_store_product_view`(`user_id`,`product_id`) VALUES ('$user_id','$product_id')");
            $status = '1';
            $msg = 'success';
            $sql2 = "SELECT id FROM `optic_store_product_view` where product_id='$product_id'";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);

            $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count);
            echo json_encode($result);
            mysqli_close($connection);
        }
    } else {
        $status = '0';
        $msg = 'Post Method Error!';
        $sql2 = "SELECT id FROM `optic_store_product_view` where product_id='$product_id'";
        $res2 = mysqli_query($connection, $sql2);
        $total_count = mysqli_num_rows($res2);
        $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $sql2 = "SELECT id FROM `optic_store_product_view` where product_id='$product_id'";
    $res2 = mysqli_query($connection, $sql2);
    $total_count = mysqli_num_rows($res2);
    $result = array('status' => $status, 'msg' => $msg, 'view' => $total_count);
    echo json_encode($result);
    mysqli_close($connection);
}
?>