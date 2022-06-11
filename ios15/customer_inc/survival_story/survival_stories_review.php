<?php

$result = array();
require_once ("../../config.php");
if (isset($_POST['user_id']) && isset($_POST['post_id'])) {
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    $service = $_POST['service'];
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d H:i:s');
    if ($user_id != '' && post_id != '') {
        $sql = "SELECT id FROM `users` WHERE id='$user_id' limit 1";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $insert = mysqli_query($connection, "INSERT INTO `survival_stories_review`(`user_id`, `survival_stories_id`, `review`, `service`, `date`) VALUES ('$user_id', '$post_id', '$comment', '$service', '$date')");


            if ($insert) {
                $status = 200;
                $msg = 'success';
                $result = array('status' => $status, 'msg' => $msg);
                echo json_encode($result);
                mysqli_close($connection);
            } else {
                $status = 4001;
                $msg = 'failure';
                $result = array('status' => $status, 'msg' => $msg);
                echo json_encode($result);
                mysqli_close($connection);
            }
        } else {
            $status = 4002;
            $msg = 'User not found';
            $result = array('status' => $status, 'msg' => $msg);
            echo json_encode($result);
            mysqli_close($connection);
        }
    } else {
        $status = 4003;
        $msg = 'Post Method Error!';
        $result = array('status' => $status, 'msg' => $msg);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = 4004;
    $msg = 'Post Method Error!';
    $result = array('status' => $status, 'msg' => $msg);
    echo json_encode($result);
    mysqli_close($connection);
}
?>