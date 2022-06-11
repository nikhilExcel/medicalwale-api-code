<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id']) && isset($_POST['home_remedies_id'])) {
    $user_id = $_POST['user_id'];
    $home_remedies_id = $_POST['home_remedies_id'];
    if ($user_id != '' && $home_remedies_id != '') {
        $sql = "SELECT * FROM `home_remedies_bookmark` WHERE user_id='$user_id' and home_remedies_id='$home_remedies_id'";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $delete = mysqli_query($connection, "DELETE FROM `home_remedies_bookmark` WHERE user_id='$user_id' and home_remedies_id='$home_remedies_id'");
            $status = '0';
            $msg = 'success';
            $result = array('status' => $status, 'msg' => $msg);
            echo json_encode($result);
            mysqli_close($connection);
        } else {
            $insert = mysqli_query($connection, "INSERT INTO `home_remedies_bookmark`(`user_id`, `home_remedies_id`) VALUES ('$user_id','$home_remedies_id')");
            $status = '1';
            $msg = 'success';
            $result = array('status' => $status, 'msg' => $msg);
            echo json_encode($result);
            mysqli_close($connection);
        }
    } else {
        $status = '0';
        $msg = 'Post Method Error!';
        $result = array('status' => $status, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $result = array('status' => $status, 'msg' => $msg);
    echo json_encode($result);
    mysqli_close($connection);
}
?>