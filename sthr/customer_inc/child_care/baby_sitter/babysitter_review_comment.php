<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id']) && isset($_POST['post_id']) && isset($_POST['comment'])) {
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    $comment = $_POST['comment'];
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    if ($user_id != '' && $post_id != '' && $comment != '') {
        $sql = "SELECT id FROM `babysitter_review` WHERE id='$post_id' limit 1";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $insert = mysqli_query($connection, "INSERT INTO `babysitter_review_comment`(`user_id`, `post_id`,`comment`,`date`) VALUES ('$user_id','$post_id','$comment','$date')");
            $status = '1';
            $msg = 'success';
            $result = array('status' => $status, 'msg' => $msg);
            echo json_encode($result);
            mysqli_close($connection);
        } else {
            $status = '0';
            $msg = 'Post not found';
            $sql2 = "SELECT id FROM `babysitter_review_comment` WHERE post_id='$post_id'";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);
            $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
            echo json_encode($result);
            mysqli_close($connection);
        }
    } else {
        $status = '0';
        $msg = 'Values Blank';
        $sql2 = "SELECT id FROM `babysitter_review_comment` WHERE post_id='$post_id'";
        $res2 = mysqli_query($connection, $sql2);
        $total_count = mysqli_num_rows($res2);
        $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $sql2 = "SELECT id FROM `babysitter_review_comment` WHERE post_id='$post_id'";
    $res2 = mysqli_query($connection, $sql2);
    $total_count = mysqli_num_rows($res2);
    $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
    echo json_encode($result);
    mysqli_close($connection);
}
?>