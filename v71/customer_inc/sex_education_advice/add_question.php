<?php

$result = array();
require_once ("../../config.php");
if (isset($_POST['user_id'])) {
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    $user_name = $_POST['user_name'];
    $user_id = $_POST['user_id'];
    $user_image = $_POST['user_image'];
    $question = $_POST['question'];
    $age = $_POST['age'];
    if ($age > 0) {
        $age = $_POST['age'];
    } else {
        $age = '0';
    }
    if ($user_id != '') {
        $sql = "SELECT id FROM `users` WHERE id='$user_id'";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {

            $insert = mysqli_query($connection, "INSERT INTO `sex_education_question` (`age`,`user_image`, `user_name`, `user_id`, `question`, `date`) VALUES ('$age', '$user_image', '$user_name', '$user_id','$question', '$date')");
            if ($insert) {
                $status = '1';
                $msg = 'success';
                $result = array('status' => $status, 'msg' => $msg);
                echo json_encode($result);
                mysqli_close($connection);
            } else {
                $status = '0';
                $msg = 'failure';
                $result = array('status' => $status, 'msg' => $msg);
                echo json_encode($result);
                mysqli_close($connection);
            }
        } else {
            $status = '0';
            $msg = 'User not found';
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