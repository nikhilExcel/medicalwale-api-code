<?php

require_once ("config.php");
$result = array();
if (isset($_POST['order_id']) && isset($_POST['user_id']) && isset($_POST['status'])) {
    $order_id = $_POST['order_id'];
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];

    if ($user_id != '' && $order_id != '' && $status != '') {
        $update = '';
        $count_row = '0';
        $update = mysqli_query($connection, "UPDATE `cart_order` SET `customer_status`='$status' where user_id='$user_id' and uni_id='$order_id'");
        $count_row = mysqli_affected_rows($connection);

        if ($count_row > 0) {
            $true_false = 'True';
            $error_msg = 'Updated Successfully!';
            array_push($result, array('true_false' => $true_false, 'error_msg' => $error_msg));
            echo json_encode($result);
            mysqli_close($connection);
        } else {
            $error_msg = 'Updation Failed';
            $true_false = 'false';
            array_push($result, array('true_false' => $true_false, 'error_msg' => $error_msg));
            echo json_encode($result);
            mysqli_close($connection);
        }
    } else {
        $error_msg = 'Please enter all fields!';
        $true_false = 'false';
        array_push($result, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'Please enter all fields!';
    $true_false = 'false';
    array_push($result, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result);
    mysqli_close($connection);
}
?>