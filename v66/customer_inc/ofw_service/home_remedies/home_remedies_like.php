<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id']) && isset($_POST['home_remedies_id'])) {
    $user_id = $_POST['user_id'];
    $home_remedies_id = $_POST['home_remedies_id'];
    if ($user_id != '' && $home_remedies_id != '') {
        $sql2 = "SELECT id FROM `timelines` WHERE id='$user_id'";
        $res2 = mysqli_query($connection, $sql2);
        $count2 = mysqli_num_rows($res2);
        if ($count2 > 0) {
            $sql = "SELECT id FROM `home_remedies_likes` WHERE user_id='$user_id' and home_remedies_id='$home_remedies_id'";
            $res = mysqli_query($connection, $sql);
            $count = mysqli_num_rows($res);
            if ($count > 0) {
                $delete = mysqli_query($connection, "DELETE FROM `home_remedies_likes` WHERE user_id='$user_id' and home_remedies_id='$home_remedies_id'");
                $status = '200';
                $msg = 'success';
                $sql2 = "SELECT * FROM `home_remedies_likes` WHERE home_remedies_id='$home_remedies_id'";
                $res2 = mysqli_query($connection, $sql2);
                $total_count = mysqli_num_rows($res2);
                $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
                echo json_encode($result);
                mysqli_close($connection);
            } else {
                $insert = mysqli_query($connection, "INSERT INTO `home_remedies_likes`(`user_id`, `home_remedies_id`) VALUES ('$user_id','$home_remedies_id')");
                $status = '200';
                $msg = 'success';
                $sql2 = "SELECT * FROM `home_remedies_likes` WHERE home_remedies_id='$home_remedies_id'";
                $res2 = mysqli_query($connection, $sql2);
                $total_count = mysqli_num_rows($res2);
                $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
                echo json_encode($result);
                mysqli_close($connection);
            }
        } else {
            $status = '0';
            $msg = 'User not found';
            $sql2 = "SELECT * FROM `home_remedies_likes` WHERE home_remedies_id='$home_remedies_id'";
            $res2 = mysqli_query($connection, $sql2);
            $total_count = mysqli_num_rows($res2);
            $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
            echo json_encode($result);
            mysqli_close($connection);
        }
    } else {
        $status = '401';
        $msg = 'Post Method Error!';
        $sql2 = "SELECT * FROM `home_remedies_likes` WHERE home_remedies_id='$home_remedies_id'";
        $res2 = mysqli_query($connection, $sql2);
        $total_count = mysqli_num_rows($res2);
        $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
        echo json_encode($result);
        mysqli_close($connection);
    }
} else {
    $status = '401';
    $msg = 'Post Method Error!';
    $sql2 = "SELECT * FROM `home_remedies_likes` WHERE home_remedies_id='$home_remedies_id'";
    $res2 = mysqli_query($connection, $sql2);
    $total_count = mysqli_num_rows($res2);
    $result = array('status' => $status, 'msg' => $msg, 'count' => $total_count);
    echo json_encode($result);
    mysqli_close($connection);
}
?>