<?php

require_once("config.php");
$result1 = array();
$result2 = array();

if (isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    $sql = "SELECT prescription_order.id AS orderid,prescription_order.user_id,prescription_order.medical_id,prescription_order.address_id,prescription_order.uni_id,prescription_order.date,prescription_order.status,medical_stores.id,medical_stores.medical_name FROM `prescription_order` 
INNER JOIN `medical_stores` ON prescription_order.medical_id=medical_stores.id 
WHERE prescription_order.user_id='$user_id' ";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);

    if ($count > 0) {
        $true_false = 'true';

        while ($row = mysqli_fetch_array($res)) {
            $true_false = 'true';
            $order_id = $row['orderid'];
            $order_no = $row['uni_id'];
            $medical_name = $row['medical_name'];
            $order_status = $row['status'];
            $order_date = $row['date'];

            $status = '1';
            $msg = 'success';

            array_push($result2, array('order_id' => $order_id, 'order_no' => $order_no, 'medical_name' => $medical_name, 'order_status' => $order_status, 'order_date' => $order_date));
        }
        array_push($result1, array('true_false' => $true_false));
        $arry = array(array('true_false' => $true_false), $result2);
        echo json_encode($arry);
        mysqli_close($connection);
    } else {
        $error_msg = 'No Order List';
        $true_false = 'false';
        array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result1);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'No Order List';
    $true_false = 'false';
    array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result1);
    mysqli_close($connection);
}
?>