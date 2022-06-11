<?php

require_once ("config.php");
if (isset($_POST['user_id']) && isset($_POST['patient_name']) && isset($_POST['address1']) && isset($_POST['address2']) && isset($_POST['mobile'])) {
    $user_id = $_POST['user_id'];
    $patient_name = $_POST['patient_name'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $landmark = $_POST['landmark'];
    $mobile = $_POST['mobile'];
    $city = $_POST['city'];
    $state = $_POST['state'];
    $postcode = $_POST['pincode'];
    $result = array();

    if ($user_id != '' && $address1 != '' && $address2 != '' && $mobile != '') {
        $insert = mysqli_query($connection, "INSERT INTO `oc_address`(`customer_id`, `firstname`, `lastname`, `email`, `telephone`, `company`, `address_1`, `address_2`, `landmark`, `city`,`state`, `postcode`, `country_id`, `zone_id`, `custom_field`) VALUES ('$user_id', '$patient_name', '', '', '$mobile', '', '$address1', '$address2', '$landmark', '$city', '$state', '$postcode', '0', '')");

        $id = mysqli_insert_id($connection);
        $true_false = 'true';
        array_push($result, array('true_false' => $true_false, 'id' => $id));
        echo json_encode($result);
        mysqli_close($connection);
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