<?php

$result = array();
require_once ("../../config.php");
if (isset($_POST['user_id'])) {
    date_default_timezone_set('Asia/Calcutta');
    $date = date('Y-m-d H:i:s');
    $user_id = $_POST['user_id'];
    $post_id = $_POST['post_id'];


    if ($user_id != '') {
        $sql = "SELECT id FROM `timelines` WHERE id='$user_id'";
        $res = mysqli_query($connection, $sql);
        $count = mysqli_num_rows($res);
        if ($count > 0) {
            $sql3 = "SELECT id FROM `sex_education_hide` WHERE post_id='$post_id' AND user_id='$user_id'";
            $res3 = mysqli_query($connection, $sql3);
            $count3 = mysqli_num_rows($res3);
            if ($count3 == 0) {
                $sql2 = "SELECT id FROM `sex_education_question` WHERE id='$post_id'";
                $res2 = mysqli_query($connection, $sql2);
                $count2 = mysqli_num_rows($res2);
                if ($count2 > 0) {
                    $insert = mysqli_query($connection, "INSERT INTO `sex_education_hide`(`user_id`, `post_id`, `is_hide`) VALUES ('$user_id','$post_id','1')");
                    $status = '1';
                    $msg = 'success';

                    $result = array('status' => $status, 'msg' => $msg);
                    echo json_encode($result);
                    mysqli_close($connection);
                } else {
                    $status = '0';
                    $msg = 'Post id not exist';
                    $result = array('status' => $status, 'msg' => $msg);
                    echo json_encode($result);
                    mysqli_close($connection);
                }
            } else {
                $getlist3 = mysqli_fetch_array($res3);
                if ($getlist3['is_hide'] == '1') {
                    $msg = 'success';
                    $update = mysqli_query($connection, "UPDATE `sex_education_hide` SET `is_hide`='0' WHERE post_id='$post_id' AND user_id='$user_id' ");
                } else {
                    $msg = 'success';
                    $update = mysqli_query($connection, "UPDATE `sex_education_hide` SET `is_hide`='1' WHERE post_id='$post_id' AND user_id='$user_id' ");
                }

                $status = '1';

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