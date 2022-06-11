<?php

require_once("config.php");

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $sql = "SELECT * FROM `notification` WHERE user_id='$user_id'";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);
    $result1 = array();
    $result2 = array();
    if ($count > 0) {
        $true_false = 'true';
        while ($row = mysqli_fetch_array($res)) {
            $true_false = 'true';
            $order_id = $row['order_id'];
            $message = $row['message'];
            $order_type = $row['order_type'];
            $date = $row['date'];

            if ($order_type == 'order') {
                $title = 'General Medicines Order';
            }
            if ($order_type == 'prescription') {
                $title = "Prescription Order";
            }

            array_push($result2, array('order_id' => $order_id, 'title' => $title, 'message' => $message, 'order_type' => $order_type, 'date' => $date));
        }
        array_push($result1, array('true_false' => $true_false));
        $arry = array(array('true_false' => $true_false), $result2);
        echo json_encode($arry);
        mysqli_close($connection);
    } else {
        $error_msg = 'No Notification List';
        $true_false = 'false';
        array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result1);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'No Notification List';
    $true_false = 'false';
    array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result1);
    mysqli_close($connection);
}
?>