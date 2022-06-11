<?php

require_once("config.php");
if (isset($_POST['service_id']) && isset($_POST['medical_id'])) {
    $medical_id = $_POST['medical_id'];
    $service_id = $_POST['service_id'];
    $sql = "SELECT * FROM `other_services_list` WHERE medical_id='$medical_id' and service_id='$service_id'";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);
    $result1 = array();
    $result2 = array();
    if ($count > 0) {
        $true_false = 'true';
        while ($row = mysqli_fetch_array($res)) {
            $true_false = 'true';
            $service_id = $row['service_id'];
            $column_1 = $row['column_1'];
            $column_2 = $row['column_2'];
            $column_3 = $row['column_3'];
            $column_4 = $row['column_4'];

            array_push($result2, array('service_id' => $service_id, 'column_1' => $column_1, 'column_2' => $column_2, 'column_3' => $column_3, 'column_4' => $column_4));
        }
        array_push($result1, array('true_false' => $true_false));
        $arry = array(array('true_false' => $true_false), $result2);
        echo json_encode($arry);
        mysqli_close($connection);
    } else {
        $error_msg = 'No Service List';
        $true_false = 'false';
        array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result1);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'No Service List';
    $true_false = 'false';
    array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result1);
    mysqli_close($connection);
}
?>