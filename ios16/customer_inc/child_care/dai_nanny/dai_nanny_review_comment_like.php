<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id']) && isset($_POST['comment_id'])) {
    $user_id = $_POST['user_id'];
    $comment_id = $_POST['comment_id'];
    if ($user_id != '' && $comment_id != '') {
        $sql = "SELECT id FROM `dai_nanny_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id' limit 1";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $delete = mysqli_query($connection, "DELETE FROM `dai_nanny_review_comment_like` WHERE user_id='$user_id' and comment_id='$comment_id'");
            $status = '1';
            $msg = 'success';
            $sql2 = "SELECT id FROM `dai_nanny_review_comment_like` WHERE comment_id='$comment_id'";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);
            $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
            echo json_encode($result);
            mysqli_close($connection);
        } else {
            $sql1 = "SELECT id FROM `dai_nanny_review_comment` WHERE id='$comment_id'";
            $res1 = mysqli_query($connection, $sql1);
            $count2 = mysqli_num_rows($res1);
            if ($count2 > 0) {
                $insert2 = mysqli_query($connection, "INSERT INTO `dai_nanny_review_comment_like`(`user_id`, `comment_id`) VALUES ('$user_id','$comment_id')");
                $status = '1';
                $msg = 'success';
                $sql2 = "SELECT id FROM `dai_nanny_review_comment_like` WHERE comment_id='$comment_id'";
                $res2 = mysqli_query($connection, $sql2);
                $total_count = mysqli_num_rows($res2);
                $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
                echo json_encode($result);
                mysqli_close($connection);
            } else {
                $status = '0';
                $msg = 'Comment list not found';
                $sql2 = "SELECT id FROM `dai_nanny_review_comment_like` WHERE comment_id='$comment_id'";
                $res2 = mysqli_query($connection, $sql2);
                $total_count = mysqli_num_rows($res2);
                $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
                echo json_encode($result);
                mysqli_close($connection);
            }
        }
    } else {
        $status = '0';
        $msg = 'Values not blank';
        $sql2 = "SELECT id FROM `dai_nanny_review_comment_like` WHERE comment_id='$comment_id'";
        $res2 = mysqli_query($connection, $sql2);
        $total_count = mysqli_num_rows($res2);
        $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '0';
    $msg = 'Post Method Error!';
    $sql2 = "SELECT id FROM `dai_nanny_review_comment_like` WHERE comment_id='$comment_id'";
    $res2 = mysqli_query($connection, $sql2);
    $total_count = mysqli_num_rows($res2);
    $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
    echo json_encode($result);
    mysqli_close($connection);
}
?>