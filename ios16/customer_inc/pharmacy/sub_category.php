<?php

require_once("config.php");

if (isset($_POST['category_id'])) {
    $category_id = $_POST['category_id'];

    $sql = "SELECT * FROM `sub_category` WHERE category='$category_id'";
    $res = mysqli_query($connection, $sql);
    $count = mysqli_num_rows($res);
    $result1 = array();
    $result2 = array();
    if ($count > 0) {
        $true_false = 'true';
        while ($row = mysqli_fetch_array($res)) {
            $true_false = 'true';
            $id = $row['id'];
            $sub_category = $row['sub_category'];

            array_push($result2, array('id' => $id, 'sub_category' => $sub_category));
        }
        array_push($result1, array('true_false' => $true_false));
        $arry = array(array('true_false' => $true_false), $result2);
        echo json_encode($arry);
        mysqli_close($connection);
    } else {
        $error_msg = 'No Sub Category List';
        $true_false = 'false';
        array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
        echo json_encode($result1);
        mysqli_close($connection);
    }
} else {
    $error_msg = 'No Sub Category List';
    $true_false = 'false';
    array_push($result1, array('true_false' => $true_false, 'error_msg' => $error_msg));
    echo json_encode($result1);
    mysqli_close($connection);
}
?>