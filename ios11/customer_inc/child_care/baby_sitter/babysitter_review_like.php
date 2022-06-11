<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id']) && isset($_POST['post_id'])) {
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];
    if ($user_id != '' && $post_id != '') {
        $sql = "SELECT id FROM `babysitter_review_likes` WHERE user_id='$user_id' and post_id='$post_id'";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $delete = mysqli_query($connection, "DELETE FROM `babysitter_review_likes` WHERE user_id='$user_id' and post_id='$post_id'");
            $status = '0';
            $msg = 'success';
            $sql2 = "SELECT id FROM `babysitter_review_likes` WHERE post_id='$post_id'";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);
            $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
            echo json_encode($result);
            mysqli_close($connection);
        } else {
            $sql1 = "SELECT id FROM `babysitter_review` WHERE id='$post_id'";
            $res1 = mysqli_query($connection, $sql1);
            $count2 = mysqli_num_rows($res1);
            if ($count2 > 0) {
                $insert2 = mysqli_query($connection, "INSERT INTO `babysitter_review_likes`(`user_id`, `post_id`) VALUES ('$user_id','$post_id')");
                $status = '1';
                $msg = 'success';
                $sql2 = "SELECT id FROM `babysitter_review_likes` WHERE post_id='$post_id'";
                $res2 = mysqli_query($connection, $sql2);
                $total_count = mysqli_num_rows($res2);
                $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
                echo json_encode($result);
                mysqli_close($connection);
            } else {
                $status = '0';
                $msg = 'Post Method Error!';
                $sql2 = "SELECT id FROM `babysitter_review_likes` WHERE post_id='$post_id'";
                $res2 = mysqli_query($connection, $sql2);
                $total_count = mysqli_num_rows($res2);
                $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
                echo json_encode($result);
                mysqli_close($connection);
            }
        }
    } else {
        $status = '0';
        $msg = 'Post Method Error!';
        $sql2 = "SELECT id FROM `babysitter_review_likes` WHERE post_id='$post_id'";
        $res2 = mysqli_query($connection, $sql2);
        $total_count = mysqli_num_rows($res2);
        $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $sql2 = "SELECT id FROM `babysitter_review_likes` WHERE post_id='$post_id'";
    $res2 = mysqli_query($connection, $sql2);
    $total_count = mysqli_num_rows($res2);
    $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
    echo json_encode($result);
    mysqli_close($connection);
}
?>