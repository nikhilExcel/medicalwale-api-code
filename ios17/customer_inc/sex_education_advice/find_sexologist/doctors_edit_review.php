<?php

$result = array();
require_once ("../../../config.php");
if (isset($_POST['user_id'])) {
      $id = $_POST['id'];
    $user_id = $_POST['user_id'];
    $rating = $_POST['rating'];
    $review = $_POST['review'];
    $service = $_POST['service'];
    date_default_timezone_set('Asia/Kolkata');
    $date = date('Y-m-d H:i:s');
    if ($user_id != '') {
      
            $insert = mysqli_query($connection, "UPDATE find_sexologist_review  SET rating='$rating', review='$review', service='$service' where user_id='$user_id' and id='$id'");
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